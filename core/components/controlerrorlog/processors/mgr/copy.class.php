<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class controlErrorLogCopyProcessor extends controlErrorLogProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_copy');
    }

    public function process()
    {
        $file = $this->getLogPath('error.log');
        $newFile = $this->getLogPath($this->newName());

        if (!copy($file, $newFile)) {
            return $this->failure("Error when copying the log file.");
        }

        return $this->success('File "' . basename($newFile) . '" is created!', ['file' => basename($newFile)]);
    }

    public function newName()
    {
        $timestamp = date('dmY_His');
        return "error_{$timestamp}.log";
    }
}

return 'controlErrorLogCopyProcessor';