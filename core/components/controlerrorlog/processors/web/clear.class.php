<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Clear the error log
 *
 * @package modx
 * @subpackage processors.system.errorlog
 */
class controlErrorLogWebClearProcessor extends controlErrorLogProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view') && $this->checkToken();
    }

    public function process()
    {
        $this->setProperty('file', 'error.log');
        $path = dirname(__DIR__) . '/';
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('mgr/clear', $this->getProperties(), ['processors_path' => $path]);

        $response->response['object']['messages_count'] = 0;
        unset($response->response['object']['isDeleted']);

        return $response->response;
    }
}

return 'controlErrorLogWebClearProcessor';
