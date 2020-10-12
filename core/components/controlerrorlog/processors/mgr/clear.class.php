<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Clear the error log
 *
 * @package modx
 * @subpackage processors.system.errorlog
 */
class controlErrorLogClearProcessor extends controlErrorLogProcessor
{

    public function checkPermissions($permission = 'error_log_erase')
    {
        if ($this->getProperty('file', 'error.log') !== 'error.log') {
            $permission = 'error_copy_erase';
        }
        return $this->modx->hasPermission($permission);
    }

    public function process()
    {
        $file = $this->getLogPath($this->getProperty('file', 'error.log'));
        $content = '';
        $isDeleted = false;

        if (file_exists($file)) {
            if ($this->modx->getOption('controlerrorlog.allow_copy_deletion', null, true) && basename($file) !== 'error.log') {
                $success = unlink($file);
                $isDeleted = true;
            } else {
                $success = file_put_contents($file, $content);
                if ($this->modx->getOption('controlerrorlog.cache_table', null, false)) {
                    $this->modx->getCacheManager()->delete('errorlog');
                }
            }
            if ($success === false) {
                return $this->failure('Error on deleting/clearing the file.');
            }
        }

        $response = [
            'name' => basename($file),
            'log' => $content,
            'tooLarge' => false,
            'empty' => true,
            'isDeleted' => $isDeleted,
            'size' => '0Kb',
        ];
        return $this->success('', $response);
    }
}

return 'controlErrorLogClearProcessor';
