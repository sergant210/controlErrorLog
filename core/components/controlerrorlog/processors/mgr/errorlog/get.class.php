<?php

/**
 * Grab and output the error log
 */
class celSystemErrorLogGetProcessor extends modProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view');
    }

    public function process()
    {
        $includeContent = $this->getProperty('includeContent',true);
        $f = $this->modx->getOption(xPDO::OPT_CACHE_PATH) . 'logs/error.log';
        $content = '';
        $tooLarge = false;
        $size = 0;
        $empty = true;
        $lastLines = (int) $this->modx->getOption('controlerrorlog.last_lines', null, 15);
        if (file_exists($f)) {
            $size = round(@filesize($f) / 1000 / 1000, 2);
            if ($size > 1) {
                $tooLarge = true;
                if ($lastLines > 0) {
                    $content = @file_get_contents($f);
                    $lines = preg_split('/\\r\\n?|\\n/', $content);
                    $content = end($lines);
                    for ($i = 1; $i < $lastLines; $i++) {
                        /*while (trim($content) == false) {
                            $content = prev($lines);
                        }*/
                        $content = prev($lines) . "\n" . $content;
                    }
                    unset($lines);
                }
            } else {
                $content = @file_get_contents($f);
            }
            if (@filesize($f) > 1) {
                $empty = false;
            }
        }
        $connector_url = $this->modx->getOption('assets_url') . 'components/controlerrorlog/connector.php';
        $la = array(
            'name' => $f,
            'tooLarge' => $tooLarge,
            'size' => $size,
            'empty' => $empty,
            'last' => $lastLines,
            'auto_refresh' => (bool) $this->modx->getOption('controlerrorlog.auto_refresh',null,true),
            'refresh_freq' => $this->modx->getOption('controlerrorlog.refresh_freq', null, 60) * 1000,
            'connector_url' => $connector_url
        );
        if ($includeContent) $la['log'] = $content;
        return $this->success('', $la);
    }
}

return 'celSystemErrorLogGetProcessor';