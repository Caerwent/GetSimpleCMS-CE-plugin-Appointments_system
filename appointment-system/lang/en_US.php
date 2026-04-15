<?php
$i18n = array(
    'LANG_CODE' => 'en-US',
    // General
    'SETTINGS' => 'Settings',

    // Tabs
    'TAB_DASHBOARD' => 'Dashboard',
    'TAB_SLOTS' => 'Time Slots',
    'TAB_APPOINTMENTS' => 'Appointments',
    'TAB_EXCEPTIONS' => 'Closed Days',
    'TAB_SETTINGS' => 'Settings',

    // Dashboard
    'DASHBOARD_TITLE' => 'Dashboard',
    'STAT_TOTAL' => 'Total',
    'STAT_TODAY' => 'Today',
    'STAT_UPCOMING' => 'Upcoming',
    'STAT_CANCELLED' => 'Cancelled',
    'QUICK_START' => 'Quick Start',
    'QUICK_STEP1' => 'Configure your recurring time slots',
    'QUICK_STEP2' => 'Add the shortcode [appointment-form] to a page',
    'QUICK_STEP3' => 'Manage your appointments from this area',
    'SHORTCODE_TITLE' => 'Shortcode to use',
    'SHORTCODE_HINT' => 'Insert this code in your page content to display the booking form',

    // Time Slots
    'SLOTS_TITLE' => 'Time Slots Management',
    'CONFIGURED_SLOTS' => 'Configured Slots',
    'NO_SLOTS' => 'No time slots configured. Add one to get started.',

    // Appointments
    'APPOINTMENTS_TITLE' => 'Appointments Management',
    'NO_APPOINTMENTS' => 'No appointments recorded',
    'UPCOMING_APPOINTMENTS' => 'Upcoming Appointments',

    // Exceptions
    'EXCEPTIONS_TITLE' => 'Closed Days',
    'CONFIGURED_EXCEPTIONS' => 'Configured Closed Days',
    'NO_EXCEPTIONS' => 'No closed days configured',

    // Settings
    'SETTINGS_TITLE' => 'Plugin Settings',
    'SETTING_SITE_NAME' => 'Site Name',
    'SETTING_ADMIN_EMAIL' => 'Admin Email',
    'SETTING_ADMIN_EMAIL_HINT' => 'Email to receive new appointment notifications',
    'SETTING_ADVANCE_DAYS' => 'Advance Booking Days',
    'SETTING_ADVANCE_DAYS_HINT' => 'Maximum number of days in advance for booking',
    'SETTING_NOTIFICATIONS' => 'Notifications',
    'SETTING_NOTIFICATIONS_LABEL' => 'Enable email notifications',
    'SETTING_SMTP_SERVER_NAME' => 'SMTP Server Address',
    'SETTING_SMTP_SERVER_PORT'=>'SMTP Port',
    'SETTING_SMTP_USE_SSL'=>'Uses SSL ?',
    'SETTING_SMTP_AUTHENT' => 'Requires Authorization ?',
    'SETTING_SMTP_PASSWORD' => 'SMTP User Password',
    'USE_SITE_THEME' => 'Use the site\'s theme for the form',
    'USE_SITE_THEME_HINT' => 'You will need to add the following styles to your theme: ',

    // Labels
    'LABEL_DAY' => 'Day',
    'LABEL_START_TIME' => 'Start Time',
    'LABEL_END_TIME' => 'End Time',
    'LABEL_DURATION' => 'Duration',
    'LABEL_MINUTES' => 'minutes',
    'LABEL_MAX_APPOINTMENTS' => 'Max Appointments',
    'LABEL_ACTIONS' => 'Actions',
    'LABEL_DATE' => 'Date',
    'LABEL_TIME' => 'Time',
    'LABEL_CLIENT' => 'Client',
    'LABEL_NAME' => 'Full Name',
    'LABEL_EMAIL' => 'Email',
    'LABEL_PHONE' => 'Phone',
    'LABEL_STATUS' => 'Status',
    'LABEL_NOTES' => 'Notes',
    'LABEL_REASON' => 'Reason',
    'LABEL_DURATION' => 'Duration',
    'LABEL_SELECT_DATE' => 'Select a date',

    'DURATION_DAY' => 'Day',
    'DURATION_AM' => 'AM',
    'DURATION_PM' => 'PM',

    // Days of week
    'DAY_MONDAY' => 'Monday',
    'DAY_TUESDAY' => 'Tuesday',
    'DAY_WEDNESDAY' => 'Wednesday',
    'DAY_THURSDAY' => 'Thursday',
    'DAY_FRIDAY' => 'Friday',
    'DAY_SATURDAY' => 'Saturday',
    'DAY_SUNDAY' => 'Sunday',

    // Status
    'STATUS_CONFIRMED' => 'Confirmed',
    'STATUS_CANCELLED' => 'Cancelled',
    'STATUS_COMPLETED' => 'Completed',
    'STATUS_PENDING' => 'Pending',

    // Buttons
    'BTN_ADD_SLOT' => 'Add Slot',
    'BTN_DELETE' => 'Delete',
    'BTN_CANCEL' => 'Cancel',
    'BTN_ADD_EXCEPTION' => 'Add',
    'BTN_SAVE_SETTINGS' => 'Save Settings',
    'BTN_EXPORT_CSV' => 'Export to CSV',
    'BTN_CONFIRM_BOOKING' => 'Confirm Appointment',

    // Confirmations
    'CONFIRM_DELETE_SLOT' => 'Are you sure you want to delete this time slot?',
    'CONFIRM_DELETE_EXCEPTION' => 'Are you sure you want to delete this exception?',
    'CONFIRM_CANCEL_APPOINTMENT' => 'Are you sure you want to cancel this appointment?',
    'CONFIRM_DELETE_APPOINTMENT' => 'Are you sure you want to permanently delete this appointment?',

    // Success messages
    'SUCCESS_SLOT_ADDED' => 'Time slot added successfully',
    'SUCCESS_SLOT_DELETED' => 'Time slot deleted successfully',
    'SUCCESS_EXCEPTION_ADDED' => 'Closed day added successfully',
    'SUCCESS_EXCEPTION_DELETED' => 'Closed day deleted successfully',
    'SUCCESS_APPOINTMENT_CANCELLED' => 'Appointment cancelled successfully',
    'SUCCESS_APPOINTMENT_DELETED' => 'Appointment deleted successfully',
    'SUCCESS_SETTINGS_SAVED' => 'Settings saved successfully',
    'SUCCESS_BOOKING' => 'Your appointment has been confirmed! You will receive a confirmation email.',
    'SUCCESS_BOOKING_NO_MAIL' => 'Your appointment has been confirmed!',

    // Error messages
    'ERROR_SLOT_ADD' => 'Error adding time slot',
    'ERROR_REQUIRED_FIELDS' => 'Please fill in all required fields',
    'ERROR_INVALID_EMAIL' => 'Invalid email address',
    'ERROR_SLOT_UNAVAILABLE' => 'Sorry, this time slot is no longer available',
    'ERROR_BOOKING_FAILED' => 'An error occurred during booking',
    'ERROR_INVALID_REQUEST' => 'Invalid request',
    'ERROR_SELECT_DATETIME' => 'Please select a date and time slot',
    'ERROR_LOADING_SLOTS' => 'Error loading time slots',

    // Frontend
    'STEP1_TITLE' => 'Choose a date',
    'STEP2_TITLE' => 'Choose a time slot',
    'STEP3_TITLE' => 'Your information',
    'SUMMARY_TITLE' => 'Summary',
    'LOADING_SLOTS' => 'Loading available slots',
    'NO_SLOTS_AVAILABLE' => 'No slots available for this date',
    'AVAILABLE' => 'available',
    'PLACEHOLDER_NOTES' => 'Additional information...',
    'PLACEHOLDER_REASON' => 'E.g.: Holiday, vacation...',

    // Email
    'EMAIL_SUBJECT_CONFIRMATION' => 'Appointment Confirmation - %s',
    'EMAIL_SUBJECT_ADMIN' => 'New Appointment - %s',
    'EMAIL_GREETING' => 'Hello',
    'EMAIL_CONFIRMATION_TEXT' => 'Your appointment has been registered. Here are the details:',
    'EMAIL_REMINDER_TEXT' => 'We look forward to seeing you at the indicated date and time. If you cannot make it, please let us know.',
    'EMAIL_FOOTER_TEXT' => 'This email was sent automatically, please do not reply.',
    'EMAIL_ADMIN_TITLE' => 'New appointment registered',
);
