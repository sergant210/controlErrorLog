<?php

$settings = array();

$tmp = array(
	'last_lines' => array(
		'xtype' => 'numberfield',
		'value' => 15,
		'area' => 'controlerrorlog.main',
	),
    'refresh_freq' => array(
        'xtype' => 'numberfield',
        'value' => 60,
        'area' => 'controlerrorlog.main',
    ),
    'auto_refresh' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'controlerrorlog.main',
    ),
    'control_frontend' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'controlerrorlog.main',
    ),
    'admin_email' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'controlerrorlog.main',
    ),
    'allow_copy_deletion' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'controlerrorlog.main',
    ),
    'tpl' => array(
        'xtype' => 'textfield',
        'value' => 'error_table.tpl',
        'area' => 'controlerrorlog.main',
    ),
    'format_output' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'controlerrorlog.main',
    ),
    'date_format' => array(
        'xtype' => 'textfield',
        'value' => '%Y-%m-%d',
        'area' => 'controlerrorlog.main',
    ),
    'css_file' => array(
        'xtype' => 'textfield',
        'value' => '/assets/components/controlerrorlog/css/web/default.css',
        'area' => 'controlerrorlog.main',
    ),
    'js_file' => array(
        'xtype' => 'textfield',
        'value' => '/assets/components/controlerrorlog/js/web/default.js',
        'area' => 'controlerrorlog.main',
    ),
    'enable' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'controlerrorlog.main',
    ),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'controlerrorlog.' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
