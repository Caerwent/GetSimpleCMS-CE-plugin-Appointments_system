<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

/**
 * Send confirmation email to client
 */
function appointment_send_confirmation_email($email, $name, $date, $startTime, $endTime) {
    try {
        $settings = AppointmentSettings::getAll();

        // check if notifications activated
        if (isset($settings['enable_notifications']) && $settings['enable_notifications'] !== '1') {
            return false;
        }

        if (!class_exists('PHPMailer')) {
            require_once(APPOINTMENT_PATH."PHPMailer/src/PHPMailer.php");
            require_once(APPOINTMENT_PATH."PHPMailer/src/SMTP.php");
            require_once(APPOINTMENT_PATH."PHPMailer/src/Exception.php");
        }

        global $SITENAME, $SITEURL;
        $siteName = isset($settings['site_name']) && !empty($settings['site_name'])
        ? $settings['site_name']
        : (isset($SITENAME) ? $SITENAME : 'Mon Site');

        $senderEmail = isset($settings['sender_email']) && !empty($settings['sender_email'])
        ? $settings['sender_email']
        : "";

        if (empty($senderEmail) || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            error_log('appointment_send_confirmation_email Error (invalid sender mail): ' . $senderEmail);
            return false;
        }

        $servername = isset($settings['server_name']) && !empty($settings['server_name'])
        ? $settings['server_name']
        : "";

        $portname = isset($settings['server_port']) && !empty($settings['server_port'])
        ? $settings['server_port']
        : "";

        $ssl = isset($settings['use_ssl']) && !empty($settings['use_ssl'])
        ? $settings['use_ssl']
        : '0';

        $authcheck = isset($settings['authent_check']) && !empty($settings['authent_check'])
        ? $settings['authent_check']
        : '0';

        $mailFooter = isset($settings['mail_footer']) && !empty($settings['mail_footer'])
        ? $settings['mail_footer']
        : "";

        $passwordfile = @file_get_contents(APPOINTMENT_PATH.'/security/pass');

        $subject = sprintf(i18n_r(APPOINTMENT_PLUGIN_ID . '/EMAIL_SUBJECT_CONFIRMATION', 'Appointment Confirmation - %s'), $siteName);

        $dateFormatted = date('d/m/Y', strtotime($date));
        $dayName = appointment_get_day_name($date);

        ob_start();
        include(APPOINTMENT_PATH . 'templates/email-template.php');
        $message = ob_get_clean();


        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        $mail->SMTPDebug  = 0;
        $mail->IsSMTP();
        $mail->CharSet="UTF-8";
        $mail->Host = $servername;
        $mail->Port = $portname;

        if($ssl ==='1'){
        //$mail->SMTPSecure = 'ssl';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        };


        if($authcheck  ==='1'){
            $mail->SMTPAuth = true;
        }

        $mail->IsHTML(true);
        $mail->Username = $senderEmail;
        $mail->Password =  base64_decode($passwordfile) ;
        $mail->setFrom($senderEmail, $siteName);

        $mail->Subject = $subject;
        $mail->Body = html_entity_decode($message);
        if($email !== '' ){
            $mail->addAddress($email);
            //$success = $mail->Send();

            if(!$mail->Send()){
                $sended = false;
                error_log('appointment_send_confirmation_email Error info: '. $mail->ErrorInfo);

            } else {
                $sended = true;
            }

            $mail->clearAllRecipients();
        }
        return $sended ;
    } catch (Exception $e) {
        error_log('appointment_send_confirmation_email Error: '. $e->getMessage());
         return false;
    }
}

/**
 * Send notification to admin
 */
function appointment_send_admin_notification($clientName, $clientEmail, $date, $time) {
    try {
        $settings = AppointmentSettings::getAll();

        // check if notifications activated
        if (isset($settings['enable_notifications']) && $settings['enable_notifications'] !== '1') {
            return false;
        }

        $adminEmail = isset($settings['admin_email']) && !empty($settings['admin_email'])
        ? $settings['admin_email']
        : appointment_get_site_email();

        if (empty($adminEmail) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            error_log('appointment_send_confirmation_email Error (invalid admin mail): '. $adminEmail);
            return false;
        }

        $senderEmail = isset($settings['sender_email']) && !empty($settings['sender_email'])
        ? $settings['sender_email']
        : "";

        if (empty($senderEmail) || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            error_log('appointment_send_confirmation_email Error (invalid sender mail): '. $senderEmail);
            return false;
        }

        if (!class_exists('PHPMailer')) {
            require_once(APPOINTMENT_PATH."PHPMailer/src/PHPMailer.php");
            require_once(APPOINTMENT_PATH."PHPMailer/src/SMTP.php");
            require_once(APPOINTMENT_PATH."PHPMailer/src/Exception.php");
        }
        global $SITENAME;
        $siteName = isset($settings['site_name']) && !empty($settings['site_name'])
        ? $settings['site_name']
        : (isset($SITENAME) ? $SITENAME : 'Mon Site');

        $servername = isset($settings['server_name']) && !empty($settings['server_name'])
        ? $settings['server_name']
        : "";

        $portname = isset($settings['server_port']) && !empty($settings['server_port'])
        ? $settings['server_port']
        : "";

        $ssl = isset($settings['use_ssl']) && !empty($settings['use_ssl'])
        ? $settings['use_ssl']
        : '0';

        $authcheck = isset($settings['authent_check']) && !empty($settings['authent_check'])
        ? $settings['authent_check']
        : '0';

        $passwordfile = @file_get_contents(APPOINTMENT_PATH.'/security/pass');


        $subject = sprintf(i18n_r(APPOINTMENT_PLUGIN_ID . '/EMAIL_SUBJECT_ADMIN', 'New Appointment - %s'), $siteName);

        $dateFormatted = date('d/m/Y', strtotime($date));

        $message = "<html><body>";
        $message .= "<h2>" . i18n_r(APPOINTMENT_PLUGIN_ID . '/EMAIL_ADMIN_TITLE', 'New appointment') . "</h2>";
        $message .= "<p><strong>" . i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_CLIENT', 'Client') . ":</strong> " . htmlspecialchars($clientName) . "</p>";
        $message .= "<p><strong>" . i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_EMAIL', 'Email') . ":</strong> " . htmlspecialchars($clientEmail) . "</p>";
        $message .= "<p><strong>" . i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE', 'Date') . ":</strong> $dateFormatted</p>";
        $message .= "<p><strong>" . i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_TIME', 'Time') . ":</strong> " . htmlspecialchars($time) . "</p>";
        $message .= "</body></html>";


        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->IsSMTP();
        $mail->CharSet="UTF-8";
        $mail->Host = $servername;
        $mail->SMTPDebug = 0; // 2 to debug
        $mail->Port = $portname;

         if($ssl ==='1'){
        //$mail->SMTPSecure = 'ssl';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        };


        if($authcheck  ==='1'){
            $mail->SMTPAuth = true;
        }

        $mail->IsHTML(true);
        $mail->Username = $senderEmail;
        $mail->Password =  base64_decode($passwordfile) ;
        $mail->setFrom($senderEmail, $siteName);


        $mail->Subject = $subject;
        $mail->Body = html_entity_decode($message);

        if($adminEmail !== '' ){
            $mail->addAddress("$adminEmail");

            if(!$mail->Send()){
                $sended = false;
                error_log('appointment_send_confirmation_email Error info: ' . $mail->ErrorInfo);
            } else {
                $sended = true;
            }

            $mail->clearAllRecipients();
        }
        return $sended ;

    } catch (Exception $e) {
        error_log('appointment_send_admin_notification Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get day name
 */
function appointment_get_day_name($date) {
    $days = [
        'Monday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_MONDAY', 'Monday'),
        'Tuesday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_TUESDAY', 'Tuesday'),
        'Wednesday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_WEDNESDAY', 'Wednesday'),
        'Thursday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_THURSDAY', 'Thursday'),
        'Friday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_FRIDAY', 'Friday'),
        'Saturday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_SATURDAY', 'Saturday'),
        'Sunday' => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_SUNDAY', 'Sunday')
    ];

    $dayEn = date('l', strtotime($date));
    return isset($days[$dayEn]) ? $days[$dayEn] : $dayEn;
}

/**
 * Get site email (compatible CE)
 */
function appointment_get_site_email() {
    global $SITEEMAIL;

    if (isset($SITEEMAIL) && !empty($SITEEMAIL)) {
        return $SITEEMAIL;
    }

    // Fallback : get site mail from site configuration file
    if (defined('GSDATAOTHERPATH')) {
        $config_file = GSDATAOTHERPATH . 'website.xml';
        if (file_exists($config_file)) {
            $xml = @simplexml_load_file($config_file);
            if ($xml && isset($xml->EMAIL)) {
                return (string)$xml->EMAIL;
            }
        }
    }

    // Dernier recours
    return 'noreply@example.com';
}

/**
 * Export appointments to CSV
 */
function appointment_export_csv() {
    try {
        $appointments = AppointmentBooking::getAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=appointments_' . date('Y-m-d') . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_DATE', 'Date'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_TIME', 'Time'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_CLIENT', 'Client'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_EMAIL', 'Email'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_PHONE', 'Phone'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_STATUS', 'Status'),
                i18n_r(APPOINTMENT_PLUGIN_ID . '/LABEL_NOTES', 'Notes')
        ], ';');

        // Data
        foreach ($appointments as $apt) {
            fputcsv($output, [
                date('d/m/Y', strtotime($apt['appointment_date'])),
                    $apt['start_time'] . ' - ' . $apt['end_time'],
                    $apt['client_name'],
                    $apt['client_email'],
                    $apt['client_phone'] ?? '',
                    $apt['status'],
                    $apt['notes'] ?? ''
            ], ';');
        }

        fclose($output);
        exit;
    } catch (Exception $e) {
        error_log('appointment_export_csv Error: ' . $e->getMessage());
        die('Export error');
    }
}

/**
 * Get days of week array
 */
function appointment_get_days_of_week() {
    return [
        1 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_MONDAY', 'Monday'),
        2 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_TUESDAY', 'Tuesday'),
        3 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_WEDNESDAY', 'Wednesday'),
        4 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_THURSDAY', 'Thursday'),
        5 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_FRIDAY', 'Friday'),
        6 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_SATURDAY', 'Saturday'),
        7 => i18n_r(APPOINTMENT_PLUGIN_ID . '/DAY_SUNDAY', 'Sunday')
    ];
}

/**
 * Format date for display
 */
function appointment_format_date($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Check if date is in the past
 */
function appointment_is_past_date($date) {
    return strtotime($date) < strtotime('today');
}

/**
 * Get status badge HTML
 */
function appointment_get_status_badge($status) {
    $badges = [
        'confirmed' => '<span class="badge bg-success">' . i18n_r(APPOINTMENT_PLUGIN_ID . '/STATUS_CONFIRMED', 'Confirmed') . '</span>',
        'cancelled' => '<span class="badge bg-danger">' . i18n_r(APPOINTMENT_PLUGIN_ID . '/STATUS_CANCELLED', 'Cancelled') . '</span>',
        'completed' => '<span class="badge bg-secondary">' . i18n_r(APPOINTMENT_PLUGIN_ID . '/STATUS_COMPLETED', 'Completed') . '</span>',
        'pending' => '<span class="badge bg-warning">' . i18n_r(APPOINTMENT_PLUGIN_ID . '/STATUS_PENDING', 'Pending') . '</span>'
    ];

    return isset($badges[$status]) ? $badges[$status] : htmlspecialchars($status);
}
