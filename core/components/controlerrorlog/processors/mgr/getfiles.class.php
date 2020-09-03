<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class celSystemErrorLogGetFilesProcessor extends controlErrorLogProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view');
    }

    public function process()
    {
        if (!file_exists($this->getLogPath('error.log'))) {
            $this->modx->getCacheManager();
            $this->modx->cacheManager->writeFile($this->getLogPath('error.log'), '');
        }
        $files = [['id' => 'error.log', 'name' => 'error.log']];
        foreach (new DirectoryIterator($this->getLogPath()) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'log' && $fileInfo->getFilename() !== 'error.log') {
                $files[] = ['id' => $fileInfo->getFilename(), 'name' => $fileInfo->getFilename()];
            }
        }
        return $this->outputArray($files);
    }
}

return 'celSystemErrorLogGetFilesProcessor';