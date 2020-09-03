<?php

if (empty($_POST['action']) || empty($_POST['token'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.core.php')) {
    header("HTTP/1.1 500 Internal Server Error");
    exit('Server initialization error!');
}
define('MODX_API_MODE', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');
$modx->lexicon->load('core:default');

$path = $modx->getOption('controlerrorlog_core_path', null, $modx->getOption('core_path') . 'components/controlerrorlog/') . 'processors/';

switch ($_POST['action']) {
    case 'web/get':
        /** @var modProcessorResponse $result */
        $result = $modx->runProcessor('web/get', $_POST, ['processors_path' => $path]);
        break;
    case 'web/clear':
        /** @var modProcessorResponse $result */
        $result = $modx->runProcessor('web/clear', $_POST, ['processors_path' => $path]);
        break;
    default:
        include MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
        $result = new modProcessorResponse($modx, ['success' => false, 'message' => 'Request error!']);
}

$response = $result->response;

if ($result->isError()) {
    header("HTTP/1.1 403 Forbidden");
}
unset($response['total'], $response['errors']);

@session_write_close();
exit(json_encode($response));