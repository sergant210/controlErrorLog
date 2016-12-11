<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        if ($modx->hasPermission('error_log_view')) {
            $modx->controller->addLexiconTopic('controlerrorlog:default');
            $modx->controller->addCss($modx->getOption('assets_url').'components/controlerrorlog/css/mgr/main.css');
            $modx->controller->addJavascript($modx->getOption('assets_url').'components/controlerrorlog/js/mgr/cel.default.js');

            $response = $modx->runProcessor('mgr/errorlog/get', array('includeContent'=>false), array('processors_path' => $modx->getOption('core_path') . 'components/controlerrorlog/processors/'));
            $resObj = $response->getObject();
            $_html = "<script>	var cel_config = " . $modx->toJSON($resObj) . "; </script>";
            $modx->controller->addHtml($_html);
        }
        break;
    case 'OnHandleRequest':
        $email = $modx->getOption('controlerrorlog.admin_email');
        if ($modx->context->get('key') == 'mgr' || empty($email) || !$modx->getOption('controlerrorlog.control_frontend')) return;

        $f = $modx->getOption(xPDO::OPT_CACHE_PATH) . 'logs/error.log';
        if (file_exists($f)) {
            $casheHash = $modx->cacheManager->get('error_log');
            $hash = md5_file($f);
            if (filesize($f) > 0 && !empty($casheHash)  &&  $casheHash != $hash) {
                $modx->lexicon->load('controlerrorlog:default');
                /** @var modPHPMailer $mail */
                $mail = $modx->getService('mail', 'mail.modPHPMailer');
                $mail->setHTML(true);

                $mail->set(modMail::MAIL_SUBJECT, $modx->lexicon('error_log_email_subject'));
                $mail->set(modMail::MAIL_BODY, $modx->lexicon('error_log_email_body', array('siteName' => $modx->config['site_name'])));
                $mail->set(modMail::MAIL_SENDER, $modx->getOption('emailsender'));
                $mail->set(modMail::MAIL_FROM, $modx->getOption('emailsender'));
                $mail->set(modMail::MAIL_FROM_NAME, $modx->getOption('site_name'));

                $mail->address('to', $email);
                $mail->address('reply-to', $modx->getOption('emailsender'));

                if (!$mail->send()) {
                    print ('An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                }
                $mail->reset();
            }
            if ($casheHash != $hash) {
                $modx->cacheManager->set('error_log', $hash, 0);
            }
        }
        break;
}
