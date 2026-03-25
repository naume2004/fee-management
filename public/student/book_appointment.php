<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$summary_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending
    FROM fees WHERE student_id = '$student_id'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);

$appointments_sql = "SELECT * FROM appointments WHERE student_id = '$student_id' AND appointment_date >= CURDATE() ORDER BY appointment_date ASC, appointment_time ASC";
$appointments_result = mysqli_query($conn, $appointments_sql);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    $check_sql = "SELECT * FROM appointments WHERE appointment_date = '$appointment_date' AND appointment_time = '$appointment_time' AND status = 'Scheduled'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) >= 5) {
        $message = 'This time slot is full. Please select another time.';
        $message_type = 'error';
    } else {
        $insert_sql = "INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose, notes, status, created_at) 
                       VALUES ('$student_id', '$appointment_date', '$appointment_time', '$purpose', '$notes', 'Scheduled', NOW())";
        
        if (mysqli_query($conn, $insert_sql)) {
            $message = 'Appointment booked successfully! We look forward to seeing you.';
            $message_type = 'success';
        } else {
            $message = 'Error booking appointment. Please try again.';
            $message_type = 'error';
        }
    }
}

$min_date = date('Y-m-d', strtotime('+1 day'));
$max_date = date('Y-m-d', strtotime('+30 days'));

include 'header.php';
?>

<style>
    .booking-hero {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        padding: 3rem;
        border-radius: 1rem;
        color: white;
        margin-bottom: 2rem;
    }
    .time-slot {
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .time-slot:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .time-slot.selected {
        border-color: #3b82f6;
        background: #3b82f6;
        color: white;
    }
    .time-slot.unavailable {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .appointment-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #3b82f6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .appointment-card.confirmed {
        border-left-color: #10b981;
    }
    .appointment-card.past {
        border-left-color: #9ca3af;
        opacity: 0.7;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .calendar-day {
        padding: 0.75rem;
        text-align: center;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .calendar-day:hover:not(.disabled) {
        background: #3b82f6;
        color: white;
    }
    .calendar-day.selected {
        background: #3b82f6;
        color: white;
    }
    .calendar-day.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .calendar-day.today {
        border: 2px solid #3b82f6;
    }
</style>

<div class="booking-hero">
    <h1>📅 Book an Appointment</h1>
    <p style="opacity: 0.9; max-width: 600px;">Skip the line! Book a specific time slot to visit the finance office. No more waiting in queues.</p>
    
    <div style="display: flex; gap: 1.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
        <div class="stat-pill" style="background: rgba(255,255,255,0.2);">
            <span>⏱️</span> 15 min appointments
        </div>
        <div class="stat-pill" style="background: rgba(255,255,255,0.2);">
            <span>✅</span> Instant confirmation
        </div>
        <div class="stat-pill" style="background: rgba(255,255,255,0.2);">
            <span>📱</span> SMS reminders
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div style="background: <?php echo $message_type === 'success' ? '#f0fdf4' : '#fee2e2'; ?>; border-left: 4px solid <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        <?php echo $message_type === 'success' ? '✅' : '❌'; ?> <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;">
    <div>
        <?php if (($summary['total_pending'] ?? 0) > 0): ?>
        <div style="background: #fef3c7; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem; border-left: 4px solid #f59e0b;">
            <h3 style="margin: 0 0 0.5rem; color: #92400e;">💰 Outstanding Balance</h3>
            <p style="margin: 0; color: #78350f; font-size: 1.5rem; font-weight: 700;">UGX <?php echo number_format(($summary['total_pending'] ?? 0) * 3800, 0); ?></p>
            <p style="margin: 0.5rem 0 0; color: #92400e; font-size: 0.9rem;">Book an appointment to pay in person, or</p>
            <a href="skip_pay.php?sim_id=<?php echo $student_id; ?>" style="color: #059669; font-weight: 600;">pay online now →</a>
        </div>
        <?php endif; ?>
        
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0;">Your Upcoming Appointments</h3>
            
            <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                <?php while($apt = mysqli_fetch_assoc($appointments_result)): ?>
                    <div class="appointment-card <?php echo $apt['status']; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <div style="font-weight: 600;">
                                    📅 <?php echo date('Mon, M d, Y', strtotime($apt['appointment_date'])); ?>
                                </div>
                                <div style="color: var(--text-muted); font-size: 0.9rem;">
                                    ⏰ <?php echo date('h:i A', strtotime($apt['appointment_time'])); ?>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 0.5rem;">
                                    <?php echo htmlspecialchars($apt['purpose']); ?>
                                </div>
                            </div>
                            <span class="badge badge-<?php echo $apt['status'] === 'Scheduled' ? 'success' : 'warning'; ?>">
                                <?php echo $apt['status']; ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center; padding: 2rem;">No upcoming appointments</p>
            <?php endif; ?>
        </div>
        
        <div style="background: #f0f9ff; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; border-left: 4px solid #0ea5e9;">
            <h4 style="margin: 0 0 1rem; color: #0369a1;">📋 Appointment Tips</h4>
            <ul style="margin: 0; padding-left: 1.25rem; color: #0369a1; font-size: 0.9rem;">
                <li>Arrive 5 minutes before your slot</li>
                <li>Bring your student ID and payment method</li>
                <li>One appointment = one student only</li>
                <li>Cancellations allowed up to 2 hours before</li>
            </ul>
        </div>
    </div>
    
    <div style="background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Book New Appointment</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>Select Date</label>
                <input type="date" name="appointment_date" id="appointment_date" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
            </div>
            
            <div class="form-group">
                <label>Select Time Slot</label>
                <div id="time-slots" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 0.5rem;">
                    <?php 
                    $times = ['08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00', '12:00:00', '13:00:00', '13:30:00', '14:00:00', '14:30:00', '15:00:00'];
                    foreach($times as $time):
                        $time_display = date('h:i A', strtotime($time));
                    ?>
                        <div class="time-slot" onclick="selectTime('<?php echo $time; ?>', this)">
                            <?php echo $time_display; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="appointment_time" id="selected_time" required>
            </div>
            
            <div class="form-group">
                <label>Purpose of Visit</label>
                <select name="purpose" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    <option value="">Select Purpose</option>
                    <option value="Pay School Fees">Pay School Fees</option>
                    <option value="Fee Balance Inquiry">Fee Balance Inquiry</option>
                    <option value="Payment Plan Discussion">Payment Plan Discussion</option>
                    <option value="Collect Receipt">Collect Receipt</option>
                    <option value="Scholarship Inquiry">Scholarship Inquiry</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Additional Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="Any additional information..." style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;"></textarea>
            </div>
            
            <button type="submit" class="btn primary-btn" style="width: 100%; background: #3b82f6; font-size: 1.1rem; padding: 1rem;">
                📅 Book Appointment
            </button>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <h4 style="margin: 0 0 1rem;">Available Time Slots</h4>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Morning: 8:00 AM - 12:00 PM</p>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Afternoon: 1:00 PM - 3:00 PM</p>
            <p style="font-size: 0.85rem; color: #9ca3af; margin-top: 1rem;">* Each slot is 30 minutes. School operates Monday-Friday.</p>
        </div>
    </div>
</div>

<script>
    function selectTime(time, element) {
        document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('selected_time').value = time;
    }
</script>

<?php include 'footer.php'; ?>
