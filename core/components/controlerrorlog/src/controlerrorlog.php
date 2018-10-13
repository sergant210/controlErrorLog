<?php

/**
 * Base controlErrorLog processor
 */
abstract class controlErrorLogProcessor extends modProcessor
{
    protected function getLogPath($filename = '')
    {
        return $this->modx->getOption(xPDO::OPT_CACHE_PATH) . xPDOCacheManager::LOG_DIR . basename($filename);
    }
}

return 'controlErrorLogProcessor';