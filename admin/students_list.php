<?php
include '../db.php';
include 'auth.php';

$page_title = 'Manage Students';

// ── Summary stats ─────────────────────────────────────────────────────────────
$stats_sql = "SELECT
    COUNT(*)                                                    AS total_students,
    SUM(student_status = 'Active')                              AS active_count,
    SUM(student_status = 'Inactive')                            AS inactive_count,
    SUM(student_status = 'Suspended')                           AS suspended_count
    FROM students";
$stats      = mysqli_fetch_assoc(mysqli_query($conn, $stats_sql));

$fee_stats_sql = "SELECT
    COALESCE(SUM(CASE WHEN status='Paid'    THEN amount END),0) AS total_paid,
    COALESCE(SUM(CASE WHEN status='Pending' THEN amount END),0) AS total_pending
    FROM fees";
$fee_stats  = mysqli_fetch_assoc(mysqli_query($conn, $fee_stats_sql));

// ── All students with fee totals & next due date ───────────────────────────────
$sql = "SELECT
    s.*,
    COALESCE(SUM(f.amount), 0)                              AS total_fee,
    COALESCE(SUM(CASE WHEN f.status='Paid'    THEN f.amount END),0) AS paid_amount,
    COALESCE(SUM(CASE WHEN f.status='Pending' THEN f.amount END),0) AS remaining_dues,
    (SELECT MIN(pd.due_date)
        FROM payment_deadlines pd
        INNER JOIN fees pf ON pd.fee_id = pf.id
        WHERE pd.student_id = s.student_id
          AND pf.status = 'Pending') AS next_due_date
FROM students s
LEFT JOIN fees f ON s.student_id = f.student_id
GROUP BY s.student_id
ORDER BY
    FIELD(s.class_name,'Nursery','Primary 1','Primary 2','Primary 3',
                        'Primary 4','Primary 5','Primary 6','Primary 7'),
    s.name LIMIT 50";

$result = mysqli_query($conn, $sql);
$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM payment_deadlines WHERE student_id='$del_id'");
    mysqli_query($conn, "DELETE FROM appointments WHERE student_id='$del_id'");
    $fee_ids = mysqli_query($conn, "SELECT id FROM fees WHERE student_id='$del_id'");
    while ($frow = mysqli_fetch_assoc($fee_ids)) {
        mysqli_query($conn, "DELETE FROM payment_deadlines WHERE fee_id='{$frow['id']}'");
    }
    mysqli_query($conn, "DELETE FROM fees WHERE student_id='$del_id'");
    mysqli_query($conn, "DELETE FROM queue WHERE student_id='$del_id'");
    mysqli_query($conn, "DELETE FROM students WHERE student_id='$del_id'");
    header('Location: students_list.php?deleted=1');
    exit();
}

$class_order = ['Nursery','Primary 1','Primary 2','Primary 3','Primary 4','Primary 5','Primary 6','Primary 7'];

include 'header.php';
?>

<style>
/* ── Page-specific styles ─────────────────────────────────────────── */
.sl-page-header   { margin-bottom: 2rem; }
.sl-page-header h1 { font-size: 1.75rem; margin: 0 0 0.25rem; }
.sl-page-header p  { color: var(--text-muted); margin: 0; }

/* Stats strip */
.sl-stats  { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem; }
.sl-stat   { background: white; border-radius: 0.75rem; padding: 1.25rem 1.5rem;
             box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-left: 4px solid transparent; }
.sl-stat.blue   { border-color: var(--primary); }
.sl-stat.green  { border-color: var(--success); }
.sl-stat.red    { border-color: var(--danger); }
.sl-stat.amber  { border-color: var(--warning); }
.sl-stat .label { font-size: 0.78rem; font-weight: 600; text-transform: uppercase;
                  letter-spacing: 0.05em; color: var(--text-muted); margin: 0 0 0.4rem; }
.sl-stat .num   { font-size: 1.75rem; font-weight: 800; margin: 0; line-height: 1; }

/* Filter bar */
.filter-bar { background: white; border-radius: 0.75rem; padding: 1rem 1.25rem;
              box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 1.5rem;
              display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
.filter-bar input, .filter-bar select {
    padding: 0.55rem 0.85rem; border: 1.5px solid var(--border);
    border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;
    color: var(--text-main); background: #f8fafc; transition: border-color 0.2s; }
.filter-bar input  { flex: 1; min-width: 180px; }
.filter-bar select { min-width: 150px; }
.filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: var(--primary); background: white; }
.filter-bar .spacer { flex: 1; }
.export-btn { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
              padding: 0.55rem 1rem; border-radius: 0.5rem; font-weight: 600;
              font-size: 0.85rem; cursor: pointer; white-space: nowrap; }
.export-btn:hover { background: #dbeafe; }

/* Table wrapper */
.table-wrap { background: white; border-radius: 0.75rem;
              box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
.table-wrap .tbl-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; justify-content: space-between; align-items: center; }
.table-wrap .tbl-header h2 { font-size: 1rem; margin: 0; }
.tbl-count { font-size: 0.85rem; color: var(--text-muted); }
.tbl-scroll { overflow-x: auto; }

/* Table */
.sl-table { width: 100%; border-collapse: collapse; min-width: 1100px; }
.sl-table th { background: #f8fafc; padding: 0.75rem 1rem; text-align: left;
               font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
               letter-spacing: 0.04em; color: var(--text-muted);
               border-bottom: 1px solid var(--border); white-space: nowrap; }
.sl-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9;
               font-size: 0.875rem; vertical-align: middle; }
.sl-table tbody tr:last-child td { border-bottom: none; }
.sl-table tbody tr:hover td { background: #fafbff; }

/* Avatar */
.stu-avatar { width: 38px; height: 38px; border-radius: 50%;
              object-fit: cover; border: 2px solid var(--border); }

/* Name+email cell */
.name-cell .stu-name  { font-weight: 600; margin: 0 0 0.2rem; color: var(--text-main); }
.name-cell .stu-email { font-size: 0.78rem; color: var(--text-muted); margin: 0; }

/* Class badge */
.class-badge { display: inline-block; background: #ede9fe; color: #5b21b6;
               padding: 0.25rem 0.6rem; border-radius: 1rem;
               font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
.class-badge.nursery { background: #fce7f3; color: #9d174d; }

/* Gender badge */
.gender-m { color: #1d4ed8; font-weight: 600; }
.gender-f { color: #be185d; font-weight: 600; }

/* Status badges */
.status-active    { background: #dcfce7; color: #15803d; padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; }
.status-inactive  { background: #f1f5f9; color: #475569; padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; }
.status-suspended { background: #fef3c7; color: #92400e; padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; }

/* Amount cells */
.amt-total { font-weight: 700; color: var(--primary); }
.amt-paid   { font-weight: 700; color: var(--success); }
.amt-due    { font-weight: 700; color: var(--danger); }
.due-date-overdue { color: var(--danger); font-weight: 600; font-size: 0.8rem; }
.due-date-ok      { color: var(--text-muted); font-size: 0.8rem; }

/* Action buttons */
.actions { display: flex; gap: 4px; flex-wrap: nowrap; }
.act-btn { border: none; padding: 5px 8px; border-radius: 5px; cursor: pointer;
           font-size: 0.75rem; font-weight: 600; text-decoration: none;
           display: inline-flex; align-items: center; gap: 3px;
           transition: opacity 0.15s; white-space: nowrap; }
.act-btn:hover { opacity: 0.85; }
.act-view   { background: #eff6ff; color: #1d4ed8; }
.act-remind { background: #fffbeb; color: #92400e; }
.act-delete { background: #fef2f2; color: #dc2626; }
.act-edit   { background: #f0fdf4; color: #15803d; }

/* Progress bar for paid */
.progress-wrap { min-width: 80px; }
.progress-bar  { height: 5px; background: #e2e8f0; border-radius: 9px; overflow: hidden; margin-top: 4px; }
.progress-fill { height: 100%; background: var(--success); border-radius: 9px; }

/* Alert banner */
.alert-banner { background: #f0fdf4; border-left: 4px solid var(--success);
    padding: 0.85rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.5rem;
    font-size: 0.9rem; color: #166534; }
.alert-danger { background: #fef2f2; border-left-color: var(--danger); color: #7f1d1d; }

/* Delete modal */
.del-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
             z-index:2000; align-items:center; justify-content:center; }
.del-modal.open { display:flex; }
.del-modal-card { background:white; border-radius:1rem; padding:2rem; max-width:400px;
                  width:90%; box-shadow:0 20px 40px rgba(0,0,0,0.2); text-align:center; }
.del-modal-card h3 { margin: 0 0 0.75rem; font-size: 1.1rem; }
.del-modal-card p  { color: var(--text-muted); font-size: 0.9rem; margin: 0 0 1.5rem; }
.del-btns { display:flex; gap:0.75rem; justify-content:center; }

@media (max-width: 768px) {
    .sl-stats { grid-template-columns: repeat(2,1fr); }
    .filter-bar { flex-direction: column; }
    .filter-bar input, .filter-bar select { width: 100%; }
}
</style>

<div class="sl-page-header">
    <h1>🏫 School Students Registry</h1>
    <p>Manage all enrolled students — view fees, status, class, gender and payment records.</p>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert-banner">✓ Student record deleted successfully.</div>
<?php endif; ?>

<!-- Stats Strip -->
<div class="sl-stats">
    <div class="sl-stat blue">
        <p class="label">Total Students</p>
        <p class="num" style="color:var(--primary);"><?php echo $stats['total_students']; ?></p>
    </div>
    <div class="sl-stat green">
        <p class="label">Active</p>
        <p class="num" style="color:var(--success);"><?php echo $stats['active_count']; ?></p>
    </div>
    <div class="sl-stat amber">
        <p class="label">Fees Collected</p>
        <p class="num" style="color:var(--warning); font-size:1.35rem;">
            $<?php echo number_format($fee_stats['total_paid'], 0); ?>
        </p>
    </div>
    <div class="sl-stat red">
        <p class="label">Outstanding</p>
        <p class="num" style="color:var(--danger); font-size:1.35rem;">
            $<?php echo number_format($fee_stats['total_pending'], 0); ?>
        </p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <input type="text" id="search-input"   placeholder="🔍  Search name, ID or email…" oninput="filterTable()">
    <select id="class-filter"   onchange="filterTable()">
        <option value="">All Classes</option>
        <?php foreach ($class_order as $cls): ?>
            <option value="<?php echo $cls; ?>"><?php echo $cls; ?></option>
        <?php endforeach; ?>
    </select>
    <select id="gender-filter"  onchange="filterTable()">
        <option value="">All Genders</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
    <select id="status-filter"  onchange="filterTable()">
        <option value="">All Statuses</option>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
        <option value="Suspended">Suspended</option>
    </select>
    <div class="spacer"></div>
    <button class="export-btn" onclick="exportCSV()">📥 Export CSV</button>
</div>

<!-- Table -->
<div class="table-wrap">
    <div class="tbl-header">
        <h2>All Students</h2>
        <span class="tbl-count">Showing <strong id="visible-count"><?php echo count($students); ?></strong> of <?php echo count($students); ?> students</span>
    </div>
    <div class="tbl-scroll">
        <table class="sl-table" id="students-table">
            <thead>
                <tr>
                    <th style="width:48px;">Photo</th>
                    <th>Student ID</th>
                    <th>Name &amp; Email</th>
                    <th>Class</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Acad. Year</th>
                    <th>Duration</th>
                    <th>Total Fee</th>
                    <th>Paid</th>
                    <th>Remaining</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s):
                $total     = (float)$s['total_fee'];
                $paid      = (float)$s['paid_amount'];
                $remaining = (float)$s['remaining_dues'];
                $pct       = $total > 0 ? round(($paid / $total) * 100) : 0;
                $avatar    = 'https://i.pravatar.cc/76?u=' . urlencode($s['student_id'] . $s['name']);
                $due_str   = $s['next_due_date'];
                $is_overdue = $due_str && strtotime($due_str) < strtotime('today');
                $class_css = $s['class_name'] === 'Nursery' ? 'class-badge nursery' : 'class-badge';
                $status_css = 'status-' . strtolower($s['student_status']);
            ?>
                <tr data-name="<?php echo strtolower($s['name']); ?>"
                    data-id="<?php echo strtolower($s['student_id']); ?>"
                    data-email="<?php echo strtolower($s['email']); ?>"
                    data-class="<?php echo htmlspecialchars($s['class_name']); ?>"
                    data-gender="<?php echo htmlspecialchars($s['gender']); ?>"
                    data-status="<?php echo htmlspecialchars($s['student_status']); ?>">

                    <td><img src="<?php echo $avatar; ?>" class="stu-avatar" alt="<?php echo htmlspecialchars($s['name']); ?>"></td>

                    <td><strong style="font-size:0.85rem;"><?php echo htmlspecialchars($s['student_id']); ?></strong></td>

                    <td class="name-cell">
                        <p class="stu-name"><?php echo htmlspecialchars($s['name']); ?></p>
                        <p class="stu-email">📧 <?php echo htmlspecialchars($s['email']); ?></p>
                    </td>

                    <td><span class="<?php echo $class_css; ?>"><?php echo htmlspecialchars($s['class_name']); ?></span></td>

                    <td>
                        <?php if ($s['gender'] === 'Male'): ?>
                            <span class="gender-m">♂ Male</span>
                        <?php else: ?>
                            <span class="gender-f">♀ Female</span>
                        <?php endif; ?>
                    </td>

                    <td><span class="<?php echo $status_css; ?>"><?php echo htmlspecialchars($s['student_status']); ?></span></td>

                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo htmlspecialchars($s['academic_year']); ?></td>

                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo htmlspecialchars($s['duration']); ?></td>

                    <td class="amt-total">$<?php echo number_format($total, 2); ?></td>

                    <td>
                        <span class="amt-paid">$<?php echo number_format($paid, 2); ?></span>
                        <div class="progress-wrap">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:<?php echo $pct; ?>%"></div>
                            </div>
                        </div>
                    </td>

                    <td class="amt-due">
                        <?php if ($remaining > 0): ?>
                            $<?php echo number_format($remaining, 2); ?>
                        <?php else: ?>
                            <span style="color:var(--success); font-weight:700;">✓ Cleared</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($due_str): ?>
                            <span class="<?php echo $is_overdue ? 'due-date-overdue' : 'due-date-ok'; ?>">
                                <?php echo $is_overdue ? '⚠️ ' : '📅 '; ?>
                                <?php echo date('M j, Y', strtotime($due_str)); ?>
                                <?php if ($is_overdue): ?><br><em style="font-size:0.72rem;">(Overdue)</em><?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div class="actions">
                            <a href="../student/dashboard.php?sim_id=<?php echo urlencode($s['student_id']); ?>"
                               class="act-btn act-view" title="View Dashboard" target="_blank">👁️ View</a>

                            <a href="send_reminders.php?student_id=<?php echo urlencode($s['student_id']); ?>"
                               class="act-btn act-remind" title="Send Reminder">📧 Remind</a>

                            <button class="act-btn act-edit" title="Edit (coming soon)"
                                onclick="alert('Edit student coming soon!')">✏️ Edit</button>

                            <button class="act-btn act-delete" title="Delete Student"
                                onclick="confirmDelete('<?php echo htmlspecialchars($s['student_id']); ?>','<?php echo htmlspecialchars(addslashes($s['name'])); ?>')">
                                🗑️ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($students)): ?>
                <tr><td colspan="13" style="text-align:center; padding:3rem; color:var(--text-muted);">No students found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="del-modal" id="del-modal">
    <div class="del-modal-card">
        <div style="font-size:2.5rem; margin-bottom:0.75rem;">🗑️</div>
        <h3>Delete Student?</h3>
        <p id="del-msg">This will permanently remove the student and all associated fee records.</p>
        <div class="del-btns">
            <button class="btn" style="background:#f1f5f9; color:var(--text-main);"
                onclick="document.getElementById('del-modal').classList.remove('open')">Cancel</button>
            <a id="del-confirm-link" href="#" class="btn primary-btn"
               style="background:var(--danger);">Yes, Delete</a>
        </div>
    </div>
</div>

<script>
// ── Filter ────────────────────────────────────────────────────────────────────
function filterTable() {
    const search  = document.getElementById('search-input').value.toLowerCase();
    const cls     = document.getElementById('class-filter').value;
    const gender  = document.getElementById('gender-filter').value;
    const status  = document.getElementById('status-filter').value;
    const rows    = document.querySelectorAll('#students-table tbody tr[data-name]');
    let visible   = 0;

    rows.forEach(row => {
        const matchSearch  = !search || row.dataset.name.includes(search)
                                      || row.dataset.id.includes(search)
                                      || row.dataset.email.includes(search);
        const matchClass   = !cls    || row.dataset.class  === cls;
        const matchGender  = !gender || row.dataset.gender === gender;
        const matchStatus  = !status || row.dataset.status === status;
        const show = matchSearch && matchClass && matchGender && matchStatus;
        row.style.display  = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('visible-count').textContent = visible;
}

// ── Delete modal ──────────────────────────────────────────────────────────────
function confirmDelete(id, name) {
    document.getElementById('del-msg').textContent =
        'Delete "' + name + '" (' + id + ')? This will permanently remove all their fee records.';
    document.getElementById('del-confirm-link').href = 'students_list.php?delete=' + encodeURIComponent(id);
    document.getElementById('del-modal').classList.add('open');
}
window.addEventListener('click', e => {
    const modal = document.getElementById('del-modal');
    if (e.target === modal) modal.classList.remove('open');
});

// ── CSV Export ────────────────────────────────────────────────────────────────
function exportCSV() {
    const rows    = document.querySelectorAll('#students-table tbody tr[data-name]:not([style*="display: none"])');
    const headers = ['Student ID','Name','Email','Class','Gender','Status','Academic Year','Duration','Total Fee','Paid','Remaining'];
    const lines   = [headers.join(',')];

    rows.forEach(row => {
        const tds   = row.querySelectorAll('td');
        const id    = tds[1]?.innerText.trim();
        const name  = tds[2]?.querySelector('.stu-name')?.innerText.trim();
        const email = tds[2]?.querySelector('.stu-email')?.innerText.replace('📧','').trim();
        const cls   = tds[3]?.innerText.trim();
        const gndr  = tds[4]?.innerText.replace(/[♂♀]/g,'').trim();
        const stat  = tds[5]?.innerText.trim();
        const yr    = tds[6]?.innerText.trim();
        const dur   = tds[7]?.innerText.trim();
        const tot   = tds[8]?.innerText.trim();
        const paid  = tds[9]?.querySelector('.amt-paid')?.innerText.trim();
        const rem   = tds[10]?.innerText.trim();
        lines.push([id,name,email,cls,gndr,stat,yr,dur,tot,paid,rem]
            .map(v => '"' + (v||'').replace(/"/g,'""') + '"').join(','));
    });

    const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = Object.assign(document.createElement('a'), { href: url, download: 'students_export.csv' });
    a.click();
    URL.revokeObjectURL(url);
}
</script>

<?php include 'footer.php'; ?>
