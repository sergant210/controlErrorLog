<?php
/**
 * Grab and output the error log
 */
class celSystemErrorLogGetProcessor extends modProcessor {
	public function checkPermissions() {
		return $this->modx->hasPermission('error_log_view');
	}
	public function process() {
		$f = $this->modx->getOption(xPDO::OPT_CACHE_PATH).'logs/error.log';
		$content = '';
		$tooLarge = false;
		$size = 0;
		$empty = true;

		if (file_exists($f)) {
			$size = round(@filesize($f) / 1000 / 1000,2);
			if ($size > 1) {
				$tooLarge = true;
			} else {
				$content = @file_get_contents($f);
			}
		}
		if (mb_strlen(trim($content)) > 0 || $tooLarge) $empty = false;
		$connector_url = $this->modx->getOption('assets_url').'components/controlerrorlog/connector.php';
		$la = array(
			'name' => $f,
			'log' => $content,
			'tooLarge' => $tooLarge,
			'size' => $size,
			'empty' => $empty,
			'connector_url' => $connector_url
		);
		return $this->success('',$la);
	}
}
return 'celSystemErrorLogGetProcessor';