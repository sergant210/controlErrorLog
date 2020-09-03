<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and download the error log
 *
 * @package modx
 * @subpackage processors.system.errorlog
 */
class controlErrorLogDownloadProcessor extends controlErrorLogProcessor
{

    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view') && $this->modx->context->key === 'mgr';
    }

    public function process()
    {
        $file = $this->getLogPath($this->getProperty('file', 'error.log'));
        if (!file_exists($file)) {
            return $this->failure();
        }
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        ob_get_level() && @ob_end_flush();
        readfile($file);
        die();
    }
}

return 'controlErrorLogDownloadProcessor';