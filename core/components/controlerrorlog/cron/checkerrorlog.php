<?php

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

if ($email = $modx->getOption('controlerrorlog.admin_email')) {

    $f = $modx->getOption(xPDO::OPT_CACHE_PATH) . 'logs/error.log';
    if (file_exists($f)) {
        $casheHash = $modx->cacheManager->get('error_log');
        $hash = md5_file($f);
        if (!empty($casheHash) && $casheHash !== $hash && filesize($f) > 0) {
            $modx->lexicon->load('controlerrorlog:default');
            /** @var modPHPMailer $mail */
            $mail = $modx->getService('mail', 'mail.modPHPMailer');
            $mail->setHTML(true);

            $mail->set(modMail::MAIL_SUBJECT, $modx->lexicon('errorlog_email_subject'));
            $mail->set(modMail::MAIL_BODY, $modx->lexicon('errorlog_email_body', array('siteName' => $modx->config['site_name'])));
            $mail->set(modMail::MAIL_SENDER, $modx->getOption('emailsender'));
            $mail->set(modMail::MAIL_FROM, $modx->getOption('emailsender'));
            $mail->set(modMail::MAIL_FROM_NAME, $modx->getOption('site_name'));

            $mail->address('to', $email);
            $mail->address('reply-to', $modx->getOption('emailsender'));

            if (!$mail->send()) {
                print ('An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
            }
            $mail->reset();
        }
        if ($casheHash !== $hash) {
            $modx->cacheManager->set('error_log', $hash, 0);
        }
    }
}