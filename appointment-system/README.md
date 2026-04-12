# Appointment System Plugin for GetSimple CMS

Version: 1.0.0

## Description

Plugin complet de gestion de rendez-vous pour GetSimple CMS avec :
- Interface d'administration complète
- Formulaire de réservation pour les visiteurs
- Créneaux horaires récurrents
- Gestion des jours de fermeture
- Notifications par email
- Export CSV

## Installation

1. **Télécharger le plugin**
   - Téléchargez le dossier `appointment-system`

2. **Installation**
   - Placez le dossier dans `/plugins/`
   - Activez le plugin depuis l'administration GetSimple

3. **Configuration initiale**
   - Allez dans "Plugins" > "Appointment System"
   - Configurez vos paramètres dans l'onglet "Paramètres"
   - Ajoutez vos créneaux horaires dans l'onglet "Créneaux horaires"

## Utilisation

### Configuration des créneaux

1. Accédez à l'onglet "Créneaux horaires"
2. Sélectionnez un jour de la semaine
3. Définissez les heures de début et de fin
4. Indiquez la durée de chaque rendez-vous
5. Définissez le nombre maximum de rendez-vous simultanés

### Ajout du formulaire sur une page

Créez ou éditez une page et ajoutez le shortcode :

[appointment-form]


Le formulaire de réservation s'affichera automatiquement.

### Gestion des rendez-vous

- Visualisez tous les rendez-vous dans l'onglet "Rendez-vous"
- Annulez ou supprimez des rendez-vous
- Exportez la liste en CSV

### Jours de fermeture

Ajoutez des exceptions (jours fériés, congés) dans l'onglet "Jours fermés".

## Shortcodes disponibles

### [appointment-form]
Affiche le formulaire complet de réservation avec :
- Sélection de date
- Choix du créneau horaire
- Formulaire d'informations client

### [appointment-calendar]
Affiche un widget simple avec les prochains rendez-vous.

## Personnalisation

### Modifier les couleurs

Éditez le fichier `css/appointment-frontend.css` pour personnaliser l'apparence du formulaire.

### Modifier les emails

Éditez le fichier `templates/email-template.php` pour personnaliser les emails de confirmation.

### Traductions

Les fichiers de langue se trouvent dans `lang/` :
- `fr_FR.php` : Français
- `en_US.php` : Anglais

Pour ajouter une langue, dupliquez un fichier et traduisez les chaînes.

## Configuration avancée

### Paramètres disponibles

- **Nom du site** : Utilisé dans les emails
- **Email administrateur** : Reçoit les notifications
- **Jours de réservation à l'avance** : Limite la période de réservation
- **Notifications** : Active/désactive les emails

### Base de données

Le plugin utilise SQLite3 et crée automatiquement :
- `data/other/appointments.db`

**Sauvegarde** : Pensez à sauvegarder régulièrement ce fichier.

## Fonctionnalités

✅ Créneaux horaires récurrents par jour de la semaine
✅ Gestion des rendez-vous (création, annulation, suppression)
✅ Jours de fermeture exceptionnels
✅ Notifications par email (client et admin)
✅ Export CSV des rendez-vous
✅ Interface responsive (mobile-friendly)
✅ Multilingue (FR/EN)
✅ Protection CSRF
✅ Validation des données

## Compatibilité

- GetSimple CMS 3.3+
- PHP 8.0+
- Extension SQLite3 requise

## Support

Pour toute question ou problème :
1. Vérifiez que l'extension SQLite3 est activée
2. Vérifiez les permissions du dossier `data/other/`
3. Consultez les logs d'erreur PHP

## Changelog

### Version 1.0.0
- Version initiale
- Gestion complète des rendez-vous
- Interface d'administration
- Formulaire utilisateur
- Notifications email
- Export CSV

## Licence

Ce plugin est fourni "tel quel" sans garantie.

## Crédits

Développé pour GetSimple CMS
