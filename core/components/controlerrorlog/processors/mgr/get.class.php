<?php
include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class controlErrorLogGetProcessor extends controlErrorLogProcessor
{
    /** @var string $file */
    protected $file;
    /** @var int $size */
    protected $size;
    /** @var int $count */
    protected $count = 0;
    /** @var bool */
    protected $defExists = false;
    /** @var bool */
    protected $fromCache = false;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view');
    }

    public function process()
    {
        $this->modx->lexicon->load('controlerrorlog:default');
        $includeContent = $this->getProperty('includeContent', true);
        $this->file = $this->getLogPath($this->getProperty('file', 'error.log'));
        $content = '';
        $size = '0Kb';
        $empty = true;
        $tooLarge = false;
        $lastLines = (int)$this->modx->getOption('controlerrorlog.last_lines', null, 15);
        $formatOutput = $this->modx->getOption('controlerrorlog.format_output', null, true);

        if (file_exists($this->file)) {
            $size = $this->getSize(true);
            if ($this->size >= 1048576) {
                $tooLarge = true;
                if ($lastLines > 0) {
                    $content = $this->getLastLines($lastLines);
                }
            } else {
                $content = $formatOutput
                    ? $this->getContent($this->file)
                    : file_get_contents($this->file);
            }
            if ($this->getSize() > 0) {
                $empty = false;
            }

        }
        $connector_url = $this->modx->getOption('controlerrorlog_assets_url', null, $this->modx->getOption('assets_url') . 'components/controlerrorlog/') . 'connector.php';
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
            'messages_count' => $this->count ?: 0,
            'format_output' => (bool)$formatOutput,
            'collapsed' => false,
            'from_cache' => $this->fromCache,
            'tpl' => $this->render([], false),
        ];

        return $this->success('', $response);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getContent($file)
    {
        if ($this->modx->getOption('controlerrorlog.cache_table', null, false) && $content = $this->getFromCache()) {
            return $content;
        }
        $generator = $this->readTheFile($file);

        $messages = [];
        foreach ($generator as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2})\s(\d{2}:\d{2}:\d{2})\]\s+(.+)/s', $line, $match)) {
                if (isset($logMessage) && is_object($logMessage)) {
                    $this->parseLogMessage($logMessage);
                    $messages[] = $logMessage;
                }
                $logMessage = new stdClass;
                $logMessage->date = $match[1];
                $logMessage->time = $match[2];
                $logMessage->_content = $match[3];
            } elseif (isset($logMessage) && is_object($logMessage)) {
                $logMessage->_content .= $line;
            }
        }
        if (isset($logMessage)) {
            // The last message
            $this->parseLogMessage($logMessage);
            $this->count = count($messages) + 1;
            $messages[] = $logMessage;
        }

        return $this->render($messages);
    }

    /**
     * @return string
     */
    protected function getFromCache()
    {
        $content = '';
        if ($data = $this->modx->getCacheManager()->get('errorlog')) {
            $hashFile = md5_file($this->file);
            if ($data['hash'] === $hashFile) {
                $this->fromCache = true;
                $this->count = $data['count'];
                return $data['content'];
            }
        }
        return $content;
    }

    /**
     * @param array $data
     * @param bool $cacheable
     * @return string
     * @throws \SmartyException
     */
    protected function render(array $data, $cacheable = true)
    {
        $templatePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

        $smarty = class_exists(Smarty::class) ? new Smarty : $this->modx->getService('smarty', 'smarty.modSmarty');
        $smarty->setCompileDir($this->modx->getOption(xPDO::OPT_CACHE_PATH) . 'mgr/smarty/controlerrorlog/');
        $tpl = $this->modx->getOption('controlerrorlog.tpl', null, 'error_table.tpl', true);

        $lexicon = [
            'date' => $this->modx->lexicon('errorlog_date'),
            'time' => $this->modx->lexicon('errorlog_time'),
            'type' => $this->modx->lexicon('errorlog_type'),
            'def' => $this->modx->lexicon('errorlog_def'),
            'file' => $this->modx->lexicon('errorlog_file'),
            'line' => $this->modx->lexicon('errorlog_line'),
        ];

        if (file_exists($templatePath . $tpl)) {
            $smarty->assign('messages', $data);
            $smarty->assign('defExists', $this->defExists);
            $smarty->assign('lexicon', $lexicon);
            $smarty->assign('dateFormat', $this->modx->getOption('date_format', null, '%d.%m.%Y', true));
            $smarty->assign('fromCache', $this->fromCache);

            $content = $smarty->fetch($templatePath . $tpl);

            $hash = md5_file($this->file);
            $payload = ['hash' => $hash, 'count' => $this->count, 'content' => $content];

            if ($this->modx->getOption('controlerrorlog.cache_table', null, false) && $cacheable) {
                $this->modx->getCacheManager()->set('errorlog', $payload);
            }

            return $content;
        }
        return '';
    }

    private function parseLogMessage($logMessage)
    {
        if (preg_match('/\(([A-Z]+) (in (.+) )?@ (.*) : (\d+)\)\s+(.+)/s', $logMessage->_content, $match)) {
            $logMessage->type = $match[1];
            $logMessage->def = $match[3];
            $logMessage->file = $match[4];
            $logMessage->line = $match[5];
            $logMessage->message = $match[6];
            if (!empty($match[3])) {
                $this->defExists = true;
            }
        }
        unset($logMessage->_content);
    }

    protected function readTheFile($path) {
        $handle = fopen($path, "rb");

        while(!feof($handle)) {
            yield fgets($handle);
        }

        fclose($handle);
    }

    protected function getLastLines($lastLines)
    {
        $data = [];
        if ($this->file) {
            $handle = fopen($this->file, 'rb');
            $pos = $this->size - 2048;
            fseek($handle, $pos);
            while (($line = fgets($handle)) !== false) {
                $data[] = $line;
            }
            $data = array_slice($data, -$lastLines);

            fclose($handle);
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