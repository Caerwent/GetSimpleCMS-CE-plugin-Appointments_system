<?php
/**
 * Installation script
 * Ce fichier peut être appelé lors de l'activation du plugin
 */

if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

function appointment_install() {
    // Créer la base de données
    require_once(APPOINTMENT_PATH . 'inc/database.php');
    AppointmentDatabase::getInstance();

    // Définir les paramètres par défaut
    AppointmentSettings::set('site_name', get_site_name());
    AppointmentSettings::set('admin_email', '');
    AppointmentSettings::set('enable_notifications', '1');
    AppointmentSettings::set('booking_advance_days', '90');

    return true;
}

function appointment_uninstall() {
    // Optionnel : supprimer la base de données
    // Attention : cela supprimera toutes les données !
    /*
    if (file_exists(APPOINTMENT_DB)) {
        unlink(APPOINTMENT_DB);
    }
    */

    return true;
}
