<?php
include_once dirname(dirname(__FILE__)) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class controlErrorLogGetProcessor extends controlErrorLogProcessor
{
    /** @var string $file */
    protected $file;
    /** @var int $size */
    protected $size;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view');
    }

    public function process()
    {
        $includeContent = $this->getProperty('includeContent', true);
        $this->file = $this->getLogPath($this->getProperty('file', 'error.log'));
        $content = '';
        $size = '0Kb';
        $empty = true;
        $tooLarge = false;
        $lastLines = (int)$this->modx->getOption('controlerrorlog.last_lines', null, 15);

        if (file_exists($this->file)) {
            $size = $this->getSize(true);
            if ($this->size >= 1048576) {
                $tooLarge = true;
                if ($lastLines > 0) {
                    $content = $this->getLastLines($lastLines);
                }
            } else {
                $content = @file_get_contents($this->file);
            }
            if ($this->getSize() > 0) {
                $empty = false;
            }
        }
        $connector_url = $this->modx->getOption('assets_url') . 'components/controlerrorlog/connector.php';
        $response = [
            'name' => basename($this->file),
            'tooLarge' => $tooLarge,
            'size' => $size,
            'empty' => $empty,
            'last' => $lastLines,
            'auto_refresh' => (bool)$this->modx->getOption('controlerrorlog.auto_refresh', null, true),
            'refresh_freq' => $this->modx->getOption('controlerrorlog.refresh_freq', null, 60) * 1000,
            'connector_url' => $connector_url,
            'log' => $includeContent ? $content : '',
            'allow_copy_deletion' => (bool)$this->modx->getOption('controlerrorlog.allow_copy_deletion', null, true),
        ];

        return $this->success('', $response);
    }

    protected function getLastLines($lastLines)
    {
        $data = [];
        if ($this->file) {
            $file = fopen($this->file, 'r');
            $pos = $this->size - 2048;
            fseek($file, $pos);
            while (($line = fgets($file)) !== false) {
                $data[] = $line;
            }
            $data = array_slice($data, -$lastLines);

            fclose($file);
        }
        return implode('', $data);
    }

    protected function getSize($convert = false)
    {
        $this->size = $size = isset($this->size) ? $this->size : @filesize($this->file);
        if ($convert) {
            if ($this->size >= 1048576) {
                $size = round($this->size / 1024 / 1024, 2) . 'Mb';
            } else {
                $size = round($this->size / 1024, 2) . 'Kb';
            }
        }
        return $size;
    }
}

return 'controlErrorLogGetProcessor';