<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

// Get result from session
$result = isset($_SESSION['appointment_result']) ? $_SESSION['appointment_result'] : null;
unset($_SESSION['appointment_result']);


global $SITEURL;
?>

<div class="appointment-booking-form">


<?php if ($result): ?>
<div class="appointment-alert <?php echo $result['success'] ? 'success' : 'error'; ?>">
<?php echo htmlspecialchars($result['message']); ?>
</div>
<?php else: ?>

<form method="POST" id="appointmentBookingForm">
<?php  // TODO manage nonce in session ?>
<input type="hidden" name="appointment_nonce" value="<?php //echo appointment_generate_nonce(); ?>">
<input type="hidden" name="appointment_time" id="appointmentTime">

<!-- Étape 1: Sélection de la date -->
<div class="appointment-step" id="step1">
<h4><i class="fa fa-calendar"></i> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STEP1_TITLE', 'Choose a date'); ?></h4>
<div class="appointment-form-group">
<label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_SELECT_DATE', 'Select a date'); ?> *</label>
<input type="date"
name="appointment_date"
id="appointmentDate"
min="<?php echo date('Y-m-d'); ?>"
max="<?php echo date('Y-m-d', strtotime('+' . (AppointmentSettings::get('booking_advance_days', 90)) . ' days')); ?>"
required>
</div>
</div>

<!-- Étape 2: Sélection du créneau -->
<div class="appointment-step step-hidden" id="step2">
<h4><i class="fa fa-clock-o"></i> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STEP2_TITLE', 'Choose a time slot'); ?></h4>
<div class="loading-spinner" id="loadingSlots">
<p><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LOADING_SLOTS', 'Loading slots'); ?>...</p>
</div>
<div id="timeSlotsContainer"></div>
</div>

<!-- Étape 3: Informations personnelles -->
<div class="appointment-step step-hidden" id="step3">
<h4><i class="fa fa-user"></i> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STEP3_TITLE', 'Your information'); ?></h4>

<div class="appointment-summary" id="appointmentSummary">
<strong><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SUMMARY_TITLE', 'Summary'); ?>:</strong><br>
<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE', 'Date'); ?>: <span id="summaryDate">-</span><br>
<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_TIME', 'Time'); ?>: <span id="summaryTime">-</span>
</div>

<div class="appointment-form-group">
<label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_NAME', 'Full Name'); ?> *</label>
<input type="text" name="client_name" required>
</div>

<div class="appointment-form-group">
<label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/LABEL_EMAIL', 'Email'); ?> *</label>
<input type="email" name="client_email" required>
</div>

<div class="appointment-form-group">
<label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/LABEL_PHONE', 'Phone'); ?></label>
<input type="tel" name="client_phone" placeholder="06 12 34 56 78">
</div>

<div class="appointment-form-group">
<label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/LABEL_NOTES', 'Notes'); ?></label>
<textarea name="notes" rows="4" placeholder="<?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/PLACEHOLDER_NOTES', 'Additional information...'); ?>"></textarea>
</div>

<button type="submit" name="appointment_book" class="appointment-btn">
<i class="fa fa-check"></i> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/BTN_CONFIRM_BOOKING', 'Confirm Appointment'); ?>
</button>
</div>
</form>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    let selectedDate = '';
    let selectedTime = '';

    const dateInput = document.getElementById('appointmentDate');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const loadingSlots = document.getElementById('loadingSlots');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const appointmentTimeInput = document.getElementById('appointmentTime');

    // Gestion de la sélection de date
    dateInput.addEventListener('change', function() {
        selectedDate = this.value;
        if (selectedDate) {
            loadTimeSlots(selectedDate);
            step2.classList.remove('step-hidden');
            step3.classList.add('step-hidden');
        }
    });

    // Chargement des créneaux disponibles
    function loadTimeSlots(date) {
        loadingSlots.style.display = 'block';
        timeSlotsContainer.innerHTML = '';

        const url = '<?php echo APPOINTMENT_URL; ?>?appointment_ajax=get_slots&date=' + date;

        fetch(url)
        .then(response => response.json())
        .then(slots => {
            loadingSlots.style.display = 'none';

            if (slots.length === 0) {
                timeSlotsContainer.innerHTML = '<p style="color: #999;"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/NO_SLOTS_AVAILABLE', 'No slots available'); ?></p>';
            return;
            }

            slots.forEach(slot => {
                const slotDiv = document.createElement('div');
                slotDiv.className = 'time-slot';
                slotDiv.dataset.time = slot.start + '|' + slot.end;
                slotDiv.innerHTML = '<strong>' + slot.start + '</strong> - ' + slot.end +
            '<br><small>' + slot.available + ' <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/AVAILABLE', 'available'); ?></small>';

                slotDiv.addEventListener('click', function() {
                    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedTime = this.dataset.time;
                    appointmentTimeInput.value = selectedTime;

                    const [start, end] = selectedTime.split('|');
                    document.getElementById('summaryTime').textContent = start + ' - ' + end;

                    step3.classList.remove('step-hidden');
                    step3.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });

                timeSlotsContainer.appendChild(slotDiv);
                });
            })
            .catch(error => {
                loadingSlots.style.display = 'none';
                timeSlotsContainer.innerHTML = '<p style="color: red;"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_LOADING_SLOTS', 'Error loading slots'); ?></p>';
            console.error('Error:', error);
            });
    }

    // Mise à jour du résumé de la date
    if(dateInput)
        dateInput.addEventListener('change', function() {
            if (this.value) {
                const dateObj = new Date(this.value + 'T00:00:00');
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('summaryDate').textContent = dateObj.toLocaleDateString('<?php echo substr(i18n_r(APPOINTMENT_PLUGIN_ID . '/LANG_CODE', 'fr-FR'), 0, 5); ?>', options);
            }
        });

    // Validation du formulaire
    document.getElementById('appointmentBookingForm').addEventListener('submit', function(e) {
        if (!selectedDate || !selectedTime) {
            e.preventDefault();
            alert('<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_SELECT_DATETIME', 'Please select date and time'); ?>');
            return false;
            }
    });
})();
</script>
