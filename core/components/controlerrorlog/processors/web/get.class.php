<?php

include_once dirname(__DIR__) . '/controlerrorlog.php';

/**
 * Grab and output the error log
 */
class controlErrorLogWebGetProcessor extends controlErrorLogProcessor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('error_log_view') && $this->checkToken();
    }

    public function process()
    {
        $path = dirname(__DIR__) . '/';
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('mgr/get', $this->getProperties(), ['processors_path' => $path]);
        $object = [];
        foreach (['tooLarge', 'size', 'empty', 'last', 'log', 'messages_count', 'format_output'] as $key) {
            $object[$key] = $response->response['object'][$key];
        }
        $response->response['object'] = $object;
        if ($object['tooLarge']) {
            $response->response['message'] = $this->modx->lexicon('errorlog_web_too_large');
            $response->response['message'] .= $this->modx->lexicon('errorlog_web_last_lines', ['last' => $object['last']]);
        }

        return $response->response;
    }
}

return 'controlErrorLogWebGetProcessor';