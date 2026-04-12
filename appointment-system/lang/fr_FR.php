<?php
$i18n = array(
    'LANG_CODE' => 'fr-FR',
    // General
    'SETTINGS' => 'Paramètres',

    // Tabs
    'TAB_DASHBOARD' => 'Tableau de bord',
    'TAB_SLOTS' => 'Créneaux horaires',
    'TAB_APPOINTMENTS' => 'Rendez-vous',
    'TAB_EXCEPTIONS' => 'Jours fermés',
    'TAB_SETTINGS' => 'Paramètres',

    // Dashboard
    'DASHBOARD_TITLE' => 'Tableau de bord',
    'STAT_TOTAL' => 'Total',
    'STAT_TODAY' => "Aujourd'hui",
    'STAT_UPCOMING' => 'À venir',
    'STAT_CANCELLED' => 'Annulés',
    'QUICK_START' => 'Démarrage rapide',
    'QUICK_STEP1' => 'Configurez vos créneaux horaires récurrents',
    'QUICK_STEP2' => 'Ajoutez le shortcode [appointment-form] dans une page',
    'QUICK_STEP3' => 'Gérez vos rendez-vous depuis cet espace',
    'SHORTCODE_TITLE' => 'Shortcode à utiliser',
    'SHORTCODE_HINT' => 'Insérez ce code dans le contenu de votre page pour afficher le formulaire de réservation',

    // Time Slots
    'SLOTS_TITLE' => 'Gestion des créneaux horaires',
    'CONFIGURED_SLOTS' => 'Créneaux configurés',
    'NO_SLOTS' => 'Aucun créneau configuré. Ajoutez-en un pour commencer.',

    // Appointments
    'APPOINTMENTS_TITLE' => 'Gestion des rendez-vous',
    'NO_APPOINTMENTS' => 'Aucun rendez-vous enregistré',
    'UPCOMING_APPOINTMENTS' => 'Rendez-vous à venir',

    // Exceptions
    'EXCEPTIONS_TITLE' => 'Jours de fermeture',
    'CONFIGURED_EXCEPTIONS' => 'Jours de fermeture configurés',
    'NO_EXCEPTIONS' => 'Aucun jour de fermeture configuré',

    // Settings
    'SETTINGS_TITLE' => 'Paramètres du plugin',
    'SETTING_SITE_NAME' => 'Nom du site',
    'SETTING_ADMIN_EMAIL' => 'Email administrateur',
    'SETTING_ADMIN_EMAIL_HINT' => 'Email pour recevoir les notifications de nouveaux rendez-vous',
    'SETTING_ADVANCE_DAYS' => 'Jours de réservation à l\'avance',
    'SETTING_ADVANCE_DAYS_HINT' => 'Nombre de jours maximum à l\'avance pour réserver',
    'SETTING_NOTIFICATIONS' => 'Notifications',
    'SETTING_NOTIFICATIONS_LABEL' => 'Activer les notifications par email',
    'SETTING_SMTP_SERVER_NAME' => 'Adresse du serveur sortant SMTP',
    'SETTING_SMTP_SERVER_PORT'=>'Port du serveur sortant SMTP',
    'SETTING_SMTP_USE_SSL'=>'Utiliser SSL ?',
    'SETTING_SMTP_AUTHENT' => 'Utiliser l\'authentification ?',
    'SETTING_SMTP_PASSWORD' => 'Mot de passe du compte SMTP expéditeur',
    'USE_SITE_THEME' => 'Utiliser le thème du site pour le formulaire',
    'USE_SITE_THEME_HINT' => 'Vous devrez ajouter les styles suivants dans votre thème ',

    // Labels
    'LABEL_DAY' => 'Jour',
    'LABEL_START_TIME' => 'Heure de début',
    'LABEL_END_TIME' => 'Heure de fin',
    'LABEL_DURATION' => 'Durée',
    'LABEL_MINUTES' => 'minutes',
    'LABEL_MAX_APPOINTMENTS' => 'Rendez-vous max',
    'LABEL_ACTIONS' => 'Actions',
    'LABEL_DATE' => 'Date',
    'LABEL_TIME' => 'Heure',
    'LABEL_CLIENT' => 'Client',
    'LABEL_NAME' => 'Nom complet',
    'LABEL_EMAIL' => 'Email',
    'LABEL_PHONE' => 'Téléphone',
    'LABEL_STATUS' => 'Statut',
    'LABEL_NOTES' => 'Notes',
    'LABEL_REASON' => 'Raison',
    'LABEL_SELECT_DATE' => 'Sélectionnez une date',

    // Days of week
    'DAY_MONDAY' => 'Lundi',
    'DAY_TUESDAY' => 'Mardi',
    'DAY_WEDNESDAY' => 'Mercredi',
    'DAY_THURSDAY' => 'Jeudi',
    'DAY_FRIDAY' => 'Vendredi',
    'DAY_SATURDAY' => 'Samedi',
    'DAY_SUNDAY' => 'Dimanche',

    // Status
    'STATUS_CONFIRMED' => 'Confirmé',
    'STATUS_CANCELLED' => 'Annulé',
    'STATUS_COMPLETED' => 'Terminé',
    'STATUS_PENDING' => 'En attente',

    // Buttons
    'BTN_ADD_SLOT' => 'Ajouter le créneau',
    'BTN_DELETE' => 'Supprimer',
    'BTN_CANCEL' => 'Annuler',
    'BTN_ADD_EXCEPTION' => 'Ajouter',
    'BTN_SAVE_SETTINGS' => 'Enregistrer les paramètres',
    'BTN_EXPORT_CSV' => 'Exporter en CSV',
    'BTN_CONFIRM_BOOKING' => 'Confirmer le rendez-vous',

    // Confirmations
    'CONFIRM_DELETE_SLOT' => 'Êtes-vous sûr de vouloir supprimer ce créneau ?',
    'CONFIRM_DELETE_EXCEPTION' => 'Êtes-vous sûr de vouloir supprimer cette exception ?',
    'CONFIRM_CANCEL_APPOINTMENT' => 'Êtes-vous sûr de vouloir annuler ce rendez-vous ?',
    'CONFIRM_DELETE_APPOINTMENT' => 'Êtes-vous sûr de vouloir supprimer définitivement ce rendez-vous ?',

    // Success messages
    'SUCCESS_SLOT_ADDED' => 'Créneau ajouté avec succès',
    'SUCCESS_SLOT_DELETED' => 'Créneau supprimé avec succès',
    'SUCCESS_EXCEPTION_ADDED' => 'Jour de fermeture ajouté avec succès',
    'SUCCESS_EXCEPTION_DELETED' => 'Jour de fermeture supprimé avec succès',
    'SUCCESS_APPOINTMENT_CANCELLED' => 'Rendez-vous annulé avec succès',
    'SUCCESS_APPOINTMENT_DELETED' => 'Rendez-vous supprimé avec succès',
    'SUCCESS_SETTINGS_SAVED' => 'Paramètres enregistrés avec succès',
    'SUCCESS_BOOKING' => 'Votre rendez-vous a été confirmé ! Vous allez recevoir un email de confirmation.',
    'SUCCESS_BOOKING_NO_MAIL' => 'Votre rendez-vous a été confirmé !',

    // Error messages
    'ERROR_SLOT_ADD' => 'Erreur lors de l\'ajout du créneau',
    'ERROR_REQUIRED_FIELDS' => 'Veuillez remplir tous les champs obligatoires',
    'ERROR_INVALID_EMAIL' => 'Adresse email invalide',
    'ERROR_SLOT_UNAVAILABLE' => 'Désolé, ce créneau n\'est plus disponible',
    'ERROR_BOOKING_FAILED' => 'Une erreur est survenue lors de la réservation',
    'ERROR_INVALID_REQUEST' => 'Requête invalide',
    'ERROR_SELECT_DATETIME' => 'Veuillez sélectionner une date et un créneau horaire',
    'ERROR_LOADING_SLOTS' => 'Erreur lors du chargement des créneaux',

    // Frontend
    'STEP1_TITLE' => 'Choisissez une date',
    'STEP2_TITLE' => 'Choisissez un créneau horaire',
    'STEP3_TITLE' => 'Vos informations',
    'SUMMARY_TITLE' => 'Récapitulatif',
    'LOADING_SLOTS' => 'Chargement des créneaux disponibles',
    'NO_SLOTS_AVAILABLE' => 'Aucun créneau disponible pour cette date',
    'AVAILABLE' => 'disponible(s)',
    'PLACEHOLDER_NOTES' => 'Informations complémentaires...',
    'PLACEHOLDER_REASON' => 'Ex: Jour férié, congés...',

    // Email
    'EMAIL_SUBJECT_CONFIRMATION' => 'Confirmation de rendez-vous - %s',
    'EMAIL_SUBJECT_ADMIN' => 'Nouveau rendez-vous - %s',
    'EMAIL_GREETING' => 'Bonjour',
    'EMAIL_CONFIRMATION_TEXT' => 'Votre rendez-vous a bien été enregistré. Voici les détails :',
    'EMAIL_REMINDER_TEXT' => 'Nous vous attendons à la date et l\'heure indiquées. En cas d\'empêchement, merci de nous prévenir.',
    'EMAIL_FOOTER_TEXT' => 'Cet email a été envoyé automatiquement, merci de ne pas y répondre.',
    'EMAIL_ADMIN_TITLE' => 'Nouveau rendez-vous enregistré',
);
