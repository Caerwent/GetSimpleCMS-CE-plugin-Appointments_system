<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

/**
 * Process shortcodes in content
 */
function appointment_process_shortcodes($content) {
    // [appointment-form]
    $content = preg_replace_callback(
        '/\[appointment-form\]/',
        'appointment_form_shortcode',
        $content
    );

    // [appointment-calendar]
    $content = preg_replace_callback(
        '/\[appointment-calendar\]/',
        'appointment_calendar_shortcode',
        $content
    );

    return $content;
}

/**
 * Appointment form shortcode
 */
function appointment_form_shortcode() {
    ob_start();
    include(APPOINTMENT_PATH . 'templates/user-form.php');
    return ob_get_clean();
}

/**
 * Appointment calendar shortcode (simple view)
 */
function appointment_calendar_shortcode() {
    $appointments = AppointmentBooking::getAll(date('Y-m-d'), 10);

    ob_start();
    ?>
    <div class="appointment-calendar-widget">
        <h3><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/UPCOMING_APPOINTMENTS'); ?></h3>
        <?php if (empty($appointments)): ?>
            <p class="text-muted"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/NO_APPOINTMENTS'); ?></p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($appointments as $apt): ?>
                    <?php if ($apt['status'] === 'confirmed'): ?>
                        <li class="list-group-item">
                            <strong><?php echo appointment_format_date($apt['appointment_date']); ?></strong>
                            - <?php echo htmlspecialchars($apt['start_time']); ?>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($apt['client_name']); ?></small>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
