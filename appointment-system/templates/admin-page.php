<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

// Handle actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add time slot
    if (isset($_POST['add_slot'])) {
        if (AppointmentTimeSlot::create(
            (int)$_POST['day_of_week'],
            $_POST['start_time'],
            $_POST['end_time'],
            (int)$_POST['duration'],
            (int)$_POST['max_appointments']
        )) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_SLOT_ADDED');
            $messageType = 'success';
        } else {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_SLOT_ADD');
            $messageType = 'error';
        }
    }

    // Delete time slot
    if (isset($_POST['delete_slot'])) {
        if (AppointmentTimeSlot::delete((int)$_POST['slot_id'])) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_SLOT_DELETED');
            $messageType = 'success';
        }
    }

    // Add exception
    if (isset($_POST['add_exception'])) {
        if (AppointmentException::create($_POST['exception_date'], $_POST['reason'], (int)$_POST['duration'])) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_EXCEPTION_ADDED');
            $messageType = 'success';
        }
    }

    // Delete exception
    if (isset($_POST['delete_exception'])) {
        if (AppointmentException::delete((int)$_POST['exception_id'])) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_EXCEPTION_DELETED');
            $messageType = 'success';
        }
    }

    // Cancel appointment
    if (isset($_POST['cancel_appointment'])) {
        if (AppointmentBooking::cancel((int)$_POST['appointment_id'])) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_APPOINTMENT_CANCELLED');
            $messageType = 'success';
        }
    }

    // Delete appointment
    if (isset($_POST['delete_appointment'])) {
        if (AppointmentBooking::delete((int)$_POST['appointment_id'])) {
            $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_APPOINTMENT_DELETED');
            $messageType = 'success';
        }
    }

    // Save settings
    if (isset($_POST['save_settings'])) {
        AppointmentSettings::set('admin_email', $_POST['admin_email']);
        AppointmentSettings::set('site_name', $_POST['site_name']);
        AppointmentSettings::set('enable_notifications', isset($_POST['enable_notifications']) ? '1' : '0');
        AppointmentSettings::set('server_name', $_POST['server_name']);
        AppointmentSettings::set('server_port', (int)$_POST['server_port']);
        AppointmentSettings::set('use_ssl', isset($_POST['use_ssl']) ? '1' : '0');
        AppointmentSettings::set('authent_check', isset($_POST['authent_check']) ? '1' : '0');
        AppointmentSettings::set('sender_email', $_POST['sender_email']);
        AppointmentSettings::set('booking_advance_days', (int)$_POST['booking_advance_days']);
        AppointmentSettings::set('use_site_theme', isset($_POST['use_site_theme']) ? '1' : '0');
        AppointmentSettings::set('mail_footer', $_POST['mail_footer']);

        file_put_contents(APPOINTMENT_PATH.'/security/pass',$_POST['password']);
        $message = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_SETTINGS_SAVED');
        $messageType = 'success';
    }
}

// Handle export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    appointment_export_csv();
}

// Get data
$timeSlots = AppointmentTimeSlot::getAll();
$appointments = AppointmentBooking::getAll(date('Y-m-d'));
$exceptions = AppointmentException::getAll();
$stats = AppointmentBooking::getStats();
$settings = AppointmentSettings::getAll();
$daysOfWeek = appointment_get_days_of_week();

$mainLang = current( explode( '_' , (string) get_site_lang() ) ) ;

// Current tab
$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<link rel="stylesheet" href="<?php echo APPOINTMENT_URL_PATH; ?>css/appointment-admin.css">

<div class="appointment-admin-wrapper">
    <h3 class="floated">
        <i class="fa fa-calendar"></i> <?php echo APPOINTMENT_PLUGIN_NAME; ?>
        <span style="font-size: 12px; color: #999; margin-left: 10px;">v<?php echo APPOINTMENT_VERSION; ?></span>
    </h3>

    <div class="edit-nav clearfix">
        <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=dashboard"
           class="<?php echo $currentTab === 'dashboard' ? 'current' : ''; ?>">
            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/TAB_DASHBOARD'); ?>
        </a>
        <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=slots"
           class="<?php echo $currentTab === 'slots' ? 'current' : ''; ?>">
            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/TAB_SLOTS'); ?>
        </a>
        <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=appointments"
           class="<?php echo $currentTab === 'appointments' ? 'current' : ''; ?>">
            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/TAB_APPOINTMENTS'); ?>
        </a>
        <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=exceptions"
           class="<?php echo $currentTab === 'exceptions' ? 'current' : ''; ?>">
            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/TAB_EXCEPTIONS'); ?>
        </a>
        <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=settings"
           class="<?php echo $currentTab === 'settings' ? 'current' : ''; ?>">
            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/TAB_SETTINGS'); ?>
        </a>
    </div>

    <?php if ($message): ?>
        <div class="<?php echo $messageType === 'success' ? 'updated' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="appointment-content">

        <?php if ($currentTab === 'dashboard'): ?>
            <!-- DASHBOARD -->
            <div class="appointment-dashboard">
                <h4><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DASHBOARD_TITLE'); ?></h4>

                <div class="appointment-stats">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STAT_TOTAL'); ?></div>
                    </div>
                    <div class="stat-box stat-today">
                        <div class="stat-number"><?php echo $stats['today']; ?></div>
                        <div class="stat-label"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STAT_TODAY'); ?></div>
                    </div>
                    <div class="stat-box stat-upcoming">
                        <div class="stat-number"><?php echo $stats['upcoming']; ?></div>
                        <div class="stat-label"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STAT_UPCOMING'); ?></div>
                    </div>
                    <div class="stat-box stat-cancelled">
                        <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
                        <div class="stat-label"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/STAT_CANCELLED'); ?></div>
                    </div>
                </div>

                <div class="appointment-quick-info">
                    <h5><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/QUICK_START'); ?></h5>
                    <ol>
                        <li><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/QUICK_STEP1'); ?></li>
                        <li><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/QUICK_STEP2'); ?></li>
                        <li><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/QUICK_STEP3'); ?></li>
                    </ol>

                    <div class="appointment-shortcode-info">
                        <strong><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SHORTCODE_TITLE'); ?>:</strong>
                        <code>[appointment-form]</code>
                        <p class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SHORTCODE_HINT'); ?></p>
                    </div>
                </div>
            </div>

        <?php elseif ($currentTab === 'slots'): ?>
            <!-- TIME SLOTS -->
            <h4><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SLOTS_TITLE'); ?></h4>

            <form method="POST" class="appointment-form">
                <table class="highlight">
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DAY'); ?>:</label></td>
                        <td>
                            <select name="day_of_week" required>
                                <?php foreach ($daysOfWeek as $num => $day): ?>
                                    <option value="<?php echo $num; ?>"><?php echo $day; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_START_TIME'); ?>:</label></td>
                        <td><input type="time" name="start_time" required></td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_END_TIME'); ?>:</label></td>
                        <td><input type="time" name="end_time" required></td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DURATION'); ?>:</label></td>
                        <td><input type="number" name="duration" value="30" min="5" max="480" required> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_MINUTES'); ?></td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_MAX_APPOINTMENTS'); ?>:</label></td>
                        <td><input type="number" name="max_appointments" value="1" min="1" max="10" required></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button type="submit" name="add_slot" class="submit"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_ADD_SLOT'); ?></button></td>
                    </tr>
                </table>
            </form>

            <h5><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIGURED_SLOTS'); ?></h5>

            <?php if (empty($timeSlots)): ?>
                <p class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/NO_SLOTS'); ?></p>
            <?php else: ?>
                <table class="edittable highlight">
                    <thead>
                        <tr>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DAY'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_START_TIME'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_END_TIME'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DURATION'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_MAX_APPOINTMENTS'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_ACTIONS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timeSlots as $slot): ?>
                            <tr>
                                <td><strong><?php echo $daysOfWeek[$slot['day_of_week']]; ?></strong></td>
                                <td><?php echo htmlspecialchars($slot['start_time']); ?></td>
                                <td><?php echo htmlspecialchars($slot['end_time']); ?></td>
                                <td><?php echo $slot['duration']; ?> min</td>
                                <td><?php echo $slot['max_appointments']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                        <button type="submit" name="delete_slot" class="cancel"
                                                onclick="return confirm('<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIRM_DELETE_SLOT'); ?>');">
                                            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_DELETE'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($currentTab === 'appointments'): ?>
            <!-- APPOINTMENTS -->
            <h4><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/APPOINTMENTS_TITLE'); ?></h4>

            <div style="margin-bottom: 15px;">
                <a href="?id=<?php echo APPOINTMENT_PLUGIN_ID; ?>&tab=appointments&export=csv" class="submit">
                    <i class="fa fa-download"></i> <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_EXPORT_CSV'); ?>
                </a>
            </div>

            <?php if (empty($appointments)): ?>
                <p class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/NO_APPOINTMENTS'); ?></p>
            <?php else: ?>
                <table class="edittable highlight">
                    <thead>
                        <tr>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_TIME'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_CLIENT'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_EMAIL'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_PHONE'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_STATUS'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_ACTIONS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                            <?php
                            $isPast = appointment_is_past_date($apt['appointment_date']);
                            $rowClass = $isPast ? 'appointment-past' : '';
                            if ($apt['status'] === 'cancelled') $rowClass .= ' appointment-cancelled';
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo appointment_format_date($apt['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($apt['start_time']) . ' - ' . htmlspecialchars($apt['end_time']); ?></td>
                                <td><strong><?php echo htmlspecialchars($apt['client_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($apt['client_email']); ?></td>
                                <td><?php echo htmlspecialchars($apt['client_phone'] ?? '-'); ?></td>
                                <td><?php echo appointment_get_status_badge($apt['status']); ?></td>
                                <td>
                                    <?php if ($apt['status'] === 'confirmed'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                            <button type="submit" name="cancel_appointment" class="cancel"
                                                    onclick="return confirm('<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIRM_CANCEL_APPOINTMENT'); ?>');">
                                                <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_CANCEL'); ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                        <button type="submit" name="delete_appointment" class="cancel"
                                                onclick="return confirm('<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIRM_DELETE_APPOINTMENT'); ?>');">
                                            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_DELETE'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php if (!empty($apt['notes'])): ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td colspan="7" style="padding-left: 30px; font-size: 0.9em; color: #666;">
                                        <strong><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_NOTES'); ?>:</strong>
                                        <?php echo nl2br(htmlspecialchars($apt['notes'])); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($currentTab === 'exceptions'): ?>
            <!-- EXCEPTIONS -->
            <h4><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/EXCEPTIONS_TITLE'); ?></h4>

            <form method="POST" class="appointment-form">
                <table class="highlight">
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE'); ?>:</label></td>
                        <td><input type="date" name="exception_date" required></td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DURATION'); ?>:</label></td>
                        <td><select name="duration">
                                <option value="0"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_DAY'); ?></option>
                                <option value="1"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_AM'); ?></option>
                                <option value="2"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_PM'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_REASON'); ?>:</label></td>
                        <td><input type="text" name="reason" placeholder="<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/PLACEHOLDER_REASON'); ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button type="submit" name="add_exception" class="submit"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_ADD_EXCEPTION'); ?></button></td>
                    </tr>
                </table>
            </form>

            <h5><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIGURED_EXCEPTIONS'); ?></h5>

            <?php if (empty($exceptions)): ?>
                <p class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/NO_EXCEPTIONS'); ?></p>
            <?php else: ?>
                <table class="edittable highlight">
                    <thead>
                        <tr>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DURATION'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_REASON'); ?></th>
                            <th><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_ACTIONS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exceptions as $exc): ?>
                            <tr>
                                <td><strong><?php echo appointment_format_date($exc['exception_date']); ?></strong></td>
                                <td><?php if($exc['duration']==0) echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_DAY');
                                else if($exc['duration']==1) echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_AM');
                                else if($exc['duration']==2) echo i18n_r(APPOINTMENT_PLUGIN_ID . '/DURATION_PM');?></td>
                                <td><?php echo htmlspecialchars($exc['reason'] ?? '-'); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="exception_id" value="<?php echo $exc['id']; ?>">
                                        <button type="submit" name="delete_exception" class="cancel"
                                                onclick="return confirm('<?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIRM_DELETE_EXCEPTION'); ?>');">
                                            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_DELETE'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($currentTab === 'settings'): ?>
            <!-- SETTINGS -->
            <h4><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTINGS_TITLE'); ?></h4>

            <form method="POST" class="appointment-form">
                <table class="highlight">
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SITE_NAME'); ?></label></td>
                        <td>
                            <input type="text" name="site_name"
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>"
                                   style="width: 100%;"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_ADMIN_EMAIL'); ?></label></td>
                        <td>
                            <input type="email" name="admin_email"
                                   value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>"
                                   style="width: 100%;"/>
                            <span class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_ADMIN_EMAIL_HINT'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_ADVANCE_DAYS'); ?></label></td>
                        <td>
                            <input type="number" name="booking_advance_days"
                                   value="<?php echo htmlspecialchars($settings['booking_advance_days'] ?? '90'); ?>"
                                   min="1" max="365"/>
                            <span class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_ADVANCE_DAYS_HINT'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_NOTIFICATIONS'); ?></label></td>
                        <td>
                            <input type="checkbox" name="enable_notifications"
                                   <?php echo (isset($settings['enable_notifications']) && $settings['enable_notifications'] === '1') ? 'checked' : ''; ?>/>
                            <?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_NOTIFICATIONS_LABEL'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_SERVER_NAME'); ?></label></td>
                        <td>
                            <input type="text" name="server_name"
                                   value="<?php echo htmlspecialchars($settings['server_name'] ?? ''); ?>"
                                   style="width: 100%;"/>
                        </td>
                    </tr>
                     <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_SERVER_PORT'); ?></label></td>
                        <td>
                            <input type="number" name="server_port"
                                   value="<?php echo htmlspecialchars($settings['server_port'] ?? '495'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_USE_SSL'); ?></label></td>
                        <td>
                            <input type="checkbox" name="use_ssl"
                                   <?php echo (isset($settings['use_ssl']) && $settings['use_ssl'] === '1') ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_EMAIL'); ?></label></td>
                        <td>
                            <input type="email" name="sender_email"
                                value="<?php echo htmlspecialchars($settings['sender_email'] ?? ''); ?>"
                                style="width: 100%;"/>
                            <span class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_EMAIL_HINT'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_AUTHENT'); ?></label></td>
                        <td>
                            <input type="checkbox" name="authent_check"
                                   <?php echo (isset($settings['authent_check']) && $settings['authent_check'] === '1') ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                     <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/SETTING_SMTP_PASSWORD'); ?></label></td>
                        <td>
                            <input type="password" name="password"
                                   value="<?php echo base64_decode( @file_get_contents(APPOINTMENT_PATH.'/security/pass'))?? ''; ?>"
                                   style="width: 100%;"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/USE_SITE_THEME'); ?></label></td>
                        <td>
                            <input type="checkbox" name="use_site_theme"
                                   <?php echo (isset($settings['use_site_theme']) && $settings['use_site_theme'] === '1') ? 'checked' : ''; ?>/>
                            <span class="hint"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/USE_SITE_THEME_HINT')?><a href="<?php echo APPOINTMENT_URL_PATH.'css/appointment-frontend.css'; ?>"><?php echo APPOINTMENT_URL_PATH.'css/appointment-frontend.css'; ?></a></span>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/CONFIRM_MAIL_FOOTER')?></label></td>
                        <td><textarea name="mail_footer" id="mailFooter" style="box-sizing:border-box;padding:10px;height:250px;width:100%;border:solid 1px #ddd;"><?php echo $settings['mail_footer'] ?? ''; ?></textarea></td>

                    </tr>

                    <tr>
                        <td></td>
                        <td><button type="submit" name="save_settings" class="submit"><?php echo i18n_r(APPOINTMENT_PLUGIN_ID . '/BTN_SAVE_SETTINGS'); ?></button></td>
                    </tr>
                </table>
            </form>

        <?php endif; ?>

    </div>
</div>
<script type="text/javascript" src="template/js/ckeditor/ckeditor.js?t=3.3.16"></script>

<script type="text/javascript">
    CKEDITOR.timestamp = "3.3.16";
    var editor = CKEDITOR.replace( "mailFooter", {
        skin : "getsimple",
        forcePasteAsPlainText : true,
            language : "<?php echo $mainLang;?>",
            defaultLanguage : "<?php echo $mainLang;?>",
                entities : false,
                height: "200px",
                baseHref : "<?php echo $SITEURL; ?>",
                tabSpaces:10,
                filebrowserBrowseUrl : "filebrowser.php?type=all",
                filebrowserImageBrowseUrl : "filebrowser.php?type=images",
                filebrowserWindowWidth : "730",
                filebrowserWindowHeight : "500"
                ,toolbar: "advanced"
    });

</script>
<script src="<?php echo APPOINTMENT_URL_PATH; ?>js/appointment-admin.js"></script>
