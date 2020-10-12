<?php

include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class controlErrorLogWebGetProcessor extends controlErrorLogProcessor
{
    public function checkPermissions()
    {
        return $this->checkToken() && $this->modx->hasPermission('error_log_view');
    }

    public function process()
    {
        $path = dirname(__DIR__) . '/';
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('mgr/get', $this->getProperties(), ['processors_path' => $path]);
        $object = [];
        foreach (['tooLarge', 'size', 'empty', 'log', 'messages_count'] as $key) {
            $object[$key] = $response->response['object'][$key];
        }
        $object['config'] = [
            'format_output' => $response->response['object']['format_output'],
            'last' => $response->response['object']['last'],
            'from_cache' => $response->response['object']['from_cache'],
            'tpl' => $response->response['object']['tpl'],
        ];
        $response->response['object'] = $object;
        if ($object['tooLarge']) {
            $response->response['message'] = $this->modx->lexicon('errorlog_web_too_large');
            $response->response['message'] .= $this->modx->lexicon('errorlog_web_last_lines', ['last' => $object['last']]);
        }

        return $response->response;
    }
}

return 'controlErrorLogWebGetProcessor';