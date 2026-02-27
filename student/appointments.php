<?php
include '../db.php';

// Simulate logged-in student (Alice Johnson - S101)
$student_id = 'S101';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);

    if (!empty($date) && !empty($time)) {
        $sql = "INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose) VALUES ('$student_id', '$date', '$time', '$purpose')";
        if (mysqli_query($conn, $sql)) {
            $success_msg = "Appointment scheduled successfully! Please arrive 10 minutes early.";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Date and Time are required!";
    }
}

// Fetch existing appointments
$appointments_sql = "SELECT * FROM appointments WHERE student_id = '$student_id' ORDER BY appointment_date, appointment_time";
$appointments_result = mysqli_query($conn, $appointments_sql);

include 'header.php';
?>

<div class="appointment-header" style="text-align: center; margin-bottom: 4rem;">
    <h1>Schedule your Visit</h1>
    <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Select a time slot to skip the long queues at the Finance Office. Your appointment will be prioritized during your selected time.</p>
</div>

<div class="appointment-content" style="display: flex; gap: 4rem; flex-wrap: wrap;">
    <div class="appointment-form-panel" style="flex: 1; min-width: 300px;">
        <div class="form-card" style="margin: 0; max-width: 100%;">
            <h2>Book a Slot</h2>
            <?php if (isset($success_msg)): ?>
                <p class="badge badge-success" style="display: block; margin: 1rem 0; text-align: center;"><?php echo $success_msg; ?></p>
            <?php endif; ?>
            <?php if (isset($error_msg)): ?>
                <p class="badge badge-danger" style="display: block; margin: 1rem 0; text-align: center;"><?php echo $error_msg; ?></p>
            <?php endif; ?>
            
            <form action="appointments.php" method="POST">
                <div class="form-group">
                    <label for="date">Select Date</label>
                    <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="time">Select Time Slot</label>
                    <select id="time" name="time" required>
                        <option value="">-- Select Time --</option>
                        <option value="09:00:00">09:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="12:00:00">12:00 PM</option>
                        <option value="14:00:00">02:00 PM</option>
                        <option value="15:00:00">03:00 PM</option>
                        <option value="16:00:00">04:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose of Visit</label>
                    <select id="purpose" name="purpose" required>
                        <option value="Fee Payment Inquiry">Fee Payment Inquiry</option>
                        <option value="Clearance">Financial Clearance</option>
                        <option value="Installment Plan">Installment Plan Negotiation</option>
                        <option value="Scholarship Inquiry">Scholarship Inquiry</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" class="btn primary-btn" style="width: 100%;">Confirm Appointment</button>
            </form>
        </div>
    </div>
    
    <div class="appointment-list-panel" style="flex: 1; min-width: 300px;">
        <h2>My Scheduled Appointments</h2>
        <div style="margin-top: 1.5rem;">
            <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                <?php while($appt = mysqli_fetch_assoc($appointments_result)): ?>
                    <div class="stat-card" style="text-align: left; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($appt['purpose']); ?></h4>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">
                                📅 <?php echo date('D, M j, Y', strtotime($appt['appointment_date'])); ?> 
                                | 🕒 <?php echo date('h:i A', strtotime($appt['appointment_time'])); ?>
                            </p>
                        </div>
                        <span class="badge <?php 
                            echo $appt['status'] == 'Scheduled' ? 'badge-warning' : 
                                ($appt['status'] == 'Completed' ? 'badge-success' : 'badge-danger'); 
                        ?>">
                            <?php echo $appt['status']; ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 3rem; text-align: center; background: white; border-radius: 1rem;">No appointments booked yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
