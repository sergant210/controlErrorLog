<?php
switch ($modx->event->name) {
	case 'OnManagerPageBeforeRender':
		$modx->controller->addLexiconTopic('controlerrorlog:default');
		$modx->controller->addCss($modx->getOption('assets_url').'components/controlerrorlog/css/mgr/main.css');
		$modx->controller->addJavascript($modx->getOption('assets_url').'components/controlerrorlog/js/mgr/cel.default.js');
		//
		$response = $modx->runProcessor('mgr/errorlog/get', array(), array('processors_path' => $modx->getOption('core_path') . 'components/controlerrorlog/processors/'));
		$resObj = $response->getObject();

		$_html = "<script>	var config = " . $modx->toJSON($resObj) . "; </script>";
		$modx->controller->addHtml($_html);
		break;
}