<?php
if (!defined('IN_GS')) { die('You cannot load this page directly.'); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
.container { max-width: 600px; margin: 0 auto; padding: 20px; }
.header { background: #007bff; color: white; padding: 20px; text-align: center; }
.content { background: #f8f9fa; padding: 20px; margin: 20px 0; }
.footer { text-align: center; color: #666; font-size: 12px; padding: 20px; }
.button { display: inline-block; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
.info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
</style>
</head>
<body>
<div class="container">
<div class="header">
<h1><?php echo htmlspecialchars($siteName); ?></h1>
</div>

<div class="content">
<h2><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/EMAIL_GREETING', 'Hello'); ?> <?php echo htmlspecialchars($name); ?>,</h2>

<p><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/EMAIL_CONFIRMATION_TEXT', 'Your appointment has been confirmed'); ?></p>

<div class="info-box">
<strong><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/LABEL_DATE', 'Date'); ?>:</strong>
<?php echo $dayName . ' ' . $dateFormatted; ?><br>

<strong><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/LABEL_TIME', 'Time'); ?>:</strong>
<?php echo htmlspecialchars($startTime) . ' - ' . htmlspecialchars($endTime); ?>
</div>

<p><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/EMAIL_REMINDER_TEXT', 'We look forward to seeing you'); ?></p>
</div>

<div class="footer">
<?php echo html_entity_decode($mailFooter);?>
<p><?php echo i18n_r(APPOINTMENT_PLUGIN_ID.'/EMAIL_FOOTER_TEXT', 'Automated email'); ?></p>
<p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?></p>
</div>
</div>
</body>
</html>
