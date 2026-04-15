<?php
/**
 * Plugin Name: Appointment System
 * Description: Système complet de gestion de rendez-vous
 * Version: 1.0.0
 * Author: Votre Nom
 */

# Prevent direct access
if (!defined('IN_GS')) {
    die('You cannot load this page directly.');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


# Plugin configuration
$thisfile=basename(__FILE__, ".php");
define('APPOINTMENT_PLUGIN_ID', $thisfile);
define('APPOINTMENT_PLUGIN_NAME', 'Appointment system');
define('APPOINTMENT_VERSION', '1.0.0');
define('APPOINTMENT_PATH', GSPLUGINPATH . $thisfile.'/');
define('APPOINTMENT_LANG_PATH', APPOINTMENT_PATH . 'lang/');

// URL du plugin
global $SITEURL;
$appointment_url = $SITEURL .basename(__FILE__).'/';
define('APPOINTMENT_URL', $appointment_url);
define('APPOINTMENT_URL_PATH',$SITEURL . 'plugins/'.$thisfile.'/');
define('APPOINTMENT_DB', GSDATAOTHERPATH . 'appointments.db');



# add in this plugin's language file
i18n_merge(APPOINTMENT_PLUGIN_ID) || i18n_merge(APPOINTMENT_PLUGIN_ID, 'en_US');


# Load dependencies
require_once(APPOINTMENT_PATH . 'inc/database.php');
require_once(APPOINTMENT_PATH . 'inc/functions.php');
require_once(APPOINTMENT_PATH . 'inc/shortcodes.php');

# Register plugin
register_plugin(
    APPOINTMENT_PLUGIN_ID,
    APPOINTMENT_PLUGIN_NAME,
    APPOINTMENT_VERSION,
    'Caerwent',
    'https://github.com/Caerwent/GetSimpleCMS-CE-plugin-Appointments_system',
    'Appointments booking system with periodic slots and exceptions',
    'plugins',
    'appointment_admin_page'
);

# Add admin menu
add_action('plugins-sidebar', 'createSideMenu', array(
    APPOINTMENT_PLUGIN_ID,
    APPOINTMENT_PLUGIN_NAME.' 📅',
    "appointment_admin_page"
));

# Register shortcode
add_filter('content', 'appointment_process_shortcodes');

# Initialize database
add_action('index-pretemplate', 'appointment_init_db');


# Hook to intercept API requests
appointment_api_router();
/**
 * Initialize database
 */
function appointment_init_db() {
    try {
        AppointmentDatabase::getInstance();
    } catch (Exception $e) {
        error_log('Appointment System DB Error: ' . $e->getMessage());
    }
}



function appointment_api_router() {
    /**
    * AJAX handler for getting available slots
    */
    try {
        if (isset($_GET['appointment_ajax']) && $_GET['appointment_ajax'] === 'get_slots') {
            header('Content-Type: application/json');

            if (isset($_GET['date'])) {
                    $slots = AppointmentTimeSlot::getAvailableSlots($_GET['date']);
                    echo json_encode($slots);
                    http_response_code(200);

            } else {
                echo json_encode([]);
                http_response_code(204);
            }

            exit;
        }
     } catch(Exception $e) {

            error_log('appointment_api_router Error: ' . $e);
            echo json_encode([]);
            http_response_code(500);
            exit;
        }
    if (isset($_POST['appointment_book'])) {
        /**
        * Handle appointment booking from frontend
        */
        appointment_handle_booking();
    }

}

/**
 * Main admin page
 */
function appointment_admin_page() {
    include(APPOINTMENT_PATH . 'templates/admin-page.php');
}


/**
 * Handle appointment booking
 */
function appointment_handle_booking() {

    $result = [
        'success' => false,
        'message' => ''
    ];


    // Validate CSRF token
    if (!isset($_POST['appointment_nonce']) ||
         !appointment_verify_nonce($_POST['appointment_nonce'])) {
        $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_INVALID_REQUEST', 'Invalid request');
        $_SESSION['appointment_result'] = $result;
        return;
    }

    // Sanitize inputs
    $date = appointment_sanitize_input($_POST['appointment_date'] ?? '');
    $time = appointment_sanitize_input($_POST['appointment_time'] ?? '');
    $name = appointment_sanitize_input($_POST['client_name'] ?? '');
    $email = appointment_sanitize_input($_POST['client_email'] ?? '');
    $phone = appointment_sanitize_input($_POST['client_phone'] ?? '');
    $notes = appointment_sanitize_input($_POST['notes'] ?? '');

    // Validate
    if (empty($name) || empty($email) || empty($date) || empty($time)) {
        $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_REQUIRED_FIELDS', 'Please fill all required fields');
        $_SESSION['appointment_result'] = $result;
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_INVALID_EMAIL', 'Invalid email');
        $_SESSION['appointment_result'] = $result;
        return;
    }

    // Extract start and end time
    $timeParts = explode('|', $time);
    if (count($timeParts) !== 2) {
        $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_INVALID_REQUEST', 'Invalid request');
        $_SESSION['appointment_result'] = $result;
        return;
    }

    list($startTime, $endTime) = $timeParts;

    // Check availability
    try {
        $availableSlots = AppointmentTimeSlot::getAvailableSlots($date);
        $slotAvailable = false;

        foreach ($availableSlots as $slot) {
            if ($slot['start'] === $startTime && $slot['available'] > 0) {
                $slotAvailable = true;
                break;
            }
        }

        if (!$slotAvailable) {
            $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_SLOT_UNAVAILABLE', 'Slot unavailable');
            $_SESSION['appointment_result'] = $result;
            return;
        }

        // Create appointment
        if (AppointmentBooking::create($date, $startTime, $endTime, $name, $email, $phone, $notes)) {
            $result['success'] = true;
            if(AppointmentSettings::get('enable_notifications', '0')==='1') {
                $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_BOOKING', 'Your appointment has been confirmed! You will receive a confirmation email.');
                // Send confirmation email
                appointment_send_confirmation_email($email, $name, $date, $startTime, $endTime);

                // Send admin notification
                appointment_send_admin_notification($name, $email, $date, $startTime);
            } else {
                $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/SUCCESS_BOOKING_NO_MAIL', 'Booking confirmed!');
            }



        } else {
            $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_BOOKING_FAILED', 'Booking failed');
        }
    } catch (Exception $e) {
        $result['message'] = i18n_r(APPOINTMENT_PLUGIN_ID . '/ERROR_BOOKING_FAILED', 'Booking failed');
        error_log('Appointment booking error: ' . $e->getMessage());
    }

    $_SESSION['appointment_result'] = $result;
}

/**
 * Sanitize input
 */
function appointment_sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate nonce for CSRF protection
 */
function appointment_generate_nonce() {
    $secret = md5(APPOINTMENT_URL);
    // 1 hour validity duration
    return hash_hmac('sha256', 'appointment_' . date('YmdH'), $secret);
}
function appointment_verify_nonce($token) {
    if (!defined('IN_GS')) return false;
     $secret = md5(APPOINTMENT_URL);
    // 1 hour validity duration : accept current hour or previous hour
    $current = hash_hmac('sha256', 'appointment_' . date('YmdH'), $secret);
    $prev    = hash_hmac('sha256', 'appointment_' . date('YmdH', strtotime('-1 hour')), $secret);
    return hash_equals($current, (string)$token)
        || hash_equals($prev,    (string)$token);
}

if(AppointmentSettings::get('use_site_theme', '0')==='0') {
    register_style('appointmentstyle', APPOINTMENT_URL_PATH.'css/appointment-frontend.css', GSVERSION.'-rev1', 'screen');
    queue_style('appointmentstyle',GSBOTH);
}


