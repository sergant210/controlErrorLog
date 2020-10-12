<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        if ($modx->getOption('controlerrorlog.enable', null, true) && $modx->hasPermission('error_log_view')) {
            $modx->controller->addLexiconTopic('controlerrorlog:default');
            $assetsUrl = $modx->getOption('controlerrorlog_assets_url', null, $modx->getOption('assets_url') . 'components/controlerrorlog/') ;
            $modx->controller->addCss($assetsUrl . 'css/mgr/main.css');
            $modx->controller->addJavascript($assetsUrl . 'js/mgr/cel.default.js');


            $path = $modx->getOption('controlerrorlog_core_path', null, $modx->getOption('core_path') . 'components/controlerrorlog/') . 'processors/';
            $response = $modx->runProcessor('mgr/get', ['includeContent' => false], ['processors_path' => $path]);
            $resObj = $response->getObject();
            $_html = "<script>	controlErrorLog.config = " . $modx->toJSON($resObj) . ";</script>";
            $modx->controller->addHtml($_html);
        }
        break;
    case 'OnBeforeRegisterClientScripts':
        if ($modx->getOption('controlerrorlog.control_frontend', null, true) && $modx->hasPermission('error_log_view')) {
            $modx->lexicon->load('controlerrorlog:default');
            $modx->regClientHTMLBlock($modx->getChunk('errorLogPanel.tpl'));

            $assetsUrl = $modx->getOption('controlerrorlog_assets_url', null, $modx->getOption('assets_url') . 'components/controlerrorlog/') ;
            if ($css = $modx->getOption('controlerrorlog.css_file', null, $assetsUrl . 'css/web/default.css')) {
                $modx->regClientCSS($css);
            }
            if ($js = $modx->getOption('controlerrorlog.js_file', null, $assetsUrl . 'js/web/default.js')) {
                $modx->regClientScript($js);
            }

            if (!isset($_SESSION['controlerrorlog']['token']) && $modx->user->isAuthenticated($modx->context->key)) {
                $_SESSION['controlerrorlog']['token'] = md5(MODX_HTTP_HOST . time() . mt_rand(1, 1000));
            }
            $path = $modx->getOption('controlerrorlog_core_path', null, $modx->getOption('core_path') . 'components/controlerrorlog/') . 'processors/';
            $response = $modx->runProcessor('web/get', ['includeContent' => false, 'token' => $_SESSION['controlerrorlog']['token']], ['processors_path' => $path]);
            $rObject = $response->getObject();
            $config = json_encode($rObject['config']);
            $connectorUrl = $assetsUrl . 'api.php';
            $rObject["tooLarge"] = $rObject["tooLarge"] ? 'true' : 'false';
            $rObject["empty"] = $rObject["empty"] ? 'true' : 'false';
            $resObj = "{
                token: '{$_SESSION['controlerrorlog']['token']}',
                config: {$config},
                collapsed: false,
                connectorUrl: '{$connectorUrl}',
                tooLarge: {$rObject["tooLarge"]},
                size: '{$rObject["size"]}',
                empty: {$rObject["empty"]},
                log: '{$rObject["log"]}',
                messages_count: {$rObject["messages_count"]},
            }";
            $_html = "<script>\r\n\tlet controlErrorLog = " . $resObj . ";\r\n</script>";
            $modx->regClientStartupHTMLBlock($_html);
        }
        break;
}