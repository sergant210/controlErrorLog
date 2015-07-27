<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';

// handle request
$path = $modx->getOption('core_path') . 'components/controlerrorlog/processors/';
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));