<?php

/**
 * Grab and output the error log
 */
class celSystemErrorLogCopyProcessor extends modProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_copy');
    }

    public function process()
    {
        $file = $this->modx->getOption(xPDO::OPT_CACHE_PATH) . 'logs/error.log';
        $newFile = $this->newName($file);
//return $this->failure("не удалось скопировать $file...\n");
        if (!copy($file, $newFile)) {
            return $this->failure("Error on copying log file.");
        }

        return $this->success('File "' . basename($newFile) . '" is created!');
    }

    public function newName($log)
    {
        $timestamp = date('dmY_His');
        return pathinfo($log, PATHINFO_DIRNAME) . '/' . "error_{$timestamp}.log";
    }
}

return 'celSystemErrorLogCopyProcessor';