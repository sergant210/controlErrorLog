<?php

/**
 * Base controlErrorLog processor
 */
abstract class controlErrorLogProcessor extends modProcessor
{
    protected function checkToken()
    {
        $token = $this->getProperty('token');

        return isset($_SESSION['controlerrorlog']['token']) && hash_equals($token, $_SESSION['controlerrorlog']['token']);
    }

    protected function getLogPath($filename = '')
    {
        return $this->modx->getOption(xPDO::OPT_CACHE_PATH) . xPDOCacheManager::LOG_DIR . basename($filename);
    }
}

return 'controlErrorLogProcessor';