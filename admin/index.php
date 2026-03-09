<?php
include 'auth.php';
include 'header.php';
?>

<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-firestore.js"></script>
<script src="../firebase-config.js"></script>

<div class="admin-dashboard-header" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>🔥 Student Database (Firestore)</h1>
        <p style="color: var(--text-muted);">Real-time database of all students with profile details and metadata.</p>
    </div>
    <div style="background: #f0f9ff; padding: 1rem; border-radius: 0.5rem; border: 1px solid #bae6fd;">
        <span id="student-count" style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">0</span>
        <span style="font-size: 0.85rem; color: #0369a1; font-weight: 600; display: block;">Total Students</span>
    </div>
</div>

<div class="table-section">
    <table class="data-table">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="students-tbody">
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem;">
                    <div class="loading-spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid var(--primary); border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 0 auto 1rem;"></div>
                    <p style="color: var(--text-muted);">Loading student database...</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Student Detail Modal -->
<div id="student-modal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close-modal" id="close-modal">&times;</span>
        <div id="modal-body">
            <!-- Content will be injected by JS -->
        </div>
    </div>
</div>

<style>
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .student-photo-small { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
    
    /* Reuse Modal styles from student dashboard */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 2.5rem;
        border-radius: 1rem;
        width: 90%;
        position: relative;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    }
    .close-modal {
        position: absolute;
        right: 1.5rem;
        top: 1rem;
        font-size: 2rem;
        cursor: pointer;
        color: var(--text-muted);
    }
</style>

<script>
    async function fetchStudents() {
        try {
            const snapshot = await db.collection('students').get();
            const tbody = document.getElementById('students-tbody');
            const countEl = document.getElementById('student-count');
            
            tbody.innerHTML = '';
            countEl.textContent = snapshot.size;
            
            if (snapshot.empty) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No students found in Firestore.</td></tr>';
                return;
            }

            snapshot.forEach(doc => {
                const s = doc.data();
                const tr = document.createElement('tr');
                const photo = s.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(s.name)}&background=random`;
                
                tr.innerHTML = `
                    <td><img src="${photo}" class="student-photo-small" alt="${s.name}"></td>
                    <td><strong>${s.student_id}</strong></td>
                    <td>${s.name}</td>
                    <td><span class="badge badge-success">${s.course}</span></td>
                    <td>
                        <button onclick="viewStudentDetails('${doc.id}')" class="btn primary-btn" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">👁️ View Profile</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error("Error fetching students:", error);
            document.getElementById('students-tbody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--danger);">Error loading student data.</td></tr>';
        }
    }

    async function viewStudentDetails(studentDocId) {
        const modal = document.getElementById('student-modal');
        const modalBody = document.getElementById('modal-body');
        modal.style.display = 'block';
        modalBody.innerHTML = '<p>Loading details...</p>';

        try {
            const studentDoc = await db.collection('students').doc(studentDocId).get();
            const s = studentDoc.data();
            const photo = s.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(s.name)}&background=random`;

            // Get some extra data for the "unique features" view
            const msgSnapshot = await db.collection('messages').where('student_id', '==', s.student_id).get();
            const notifSnapshot = await db.collection('notifications').where('student_id', '==', s.student_id).where('status', '==', 'unread').get();

            modalBody.innerHTML = `
                <div style="display: flex; gap: 2rem; align-items: start;">
                    <div style="text-align: center;">
                        <img src="${photo}" style="width: 150px; height: 150px; border-radius: 1rem; object-fit: cover; border: 4px solid var(--primary);">
                        <h2 style="margin-top: 1rem;">${s.name}</h2>
                        <p style="color: var(--text-muted);">${s.student_id}</p>
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Student Metadata</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="stat-card" style="padding: 1rem; text-align: left; background: #f8fafc;">
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Course</p>
                                <p style="font-weight: 700; margin: 0;">${s.course}</p>
                            </div>
                            <div class="stat-card" style="padding: 1rem; text-align: left; background: #f8fafc;">
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Status</p>
                                <p style="font-weight: 700; margin: 0; color: var(--success);">Enrolled</p>
                            </div>
                            <div class="stat-card" style="padding: 1rem; text-align: left; background: #f0fdf4;">
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Unread Notifications</p>
                                <p style="font-weight: 700; margin: 0; color: var(--success);">${notifSnapshot.size}</p>
                            </div>
                            <div class="stat-card" style="padding: 1rem; text-align: left; background: #eff6ff;">
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Total Messages</p>
                                <p style="font-weight: 700; margin: 0; color: var(--primary);">${msgSnapshot.size}</p>
                            </div>
                        </div>
                        
                        <div style="margin-top: 2rem;">
                            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Quick Actions</h3>
                            <div style="display: flex; gap: 1rem;">
                                <a href="send_reminders.php?student_id=${s.student_id}" class="btn primary-btn" style="background: #f59e0b; font-size: 0.85rem;">📧 Send Reminder</a>
                                <button onclick="alert('Support system for staff coming soon!')" class="btn secondary-btn" style="color: var(--primary); border: 1px solid var(--primary); font-size: 0.85rem;">💬 View Messages</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error("Error fetching details:", error);
            modalBody.innerHTML = '<p style="color: var(--danger);">Error loading student details.</p>';
        }
    }

    document.getElementById('close-modal').onclick = () => {
        document.getElementById('student-modal').style.display = 'none';
    };

    window.onclick = (event) => {
        const modal = document.getElementById('student-modal');
        if (event.target == modal) modal.style.display = 'none';
    };

    fetchStudents();
</script>

<?php include 'footer.php'; ?>
