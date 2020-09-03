<?php
/** @noinspection PhpIncludeInspection */
if (file_exists(dirname(dirname(dirname(__DIR__))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(__DIR__))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';

// handle request
/** @var modX $modx*/
$path = $modx->getOption('controlerrorlog_core_path', null, $modx->getOption('core_path') . 'components/controlerrorlog/') . 'processors/';
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));