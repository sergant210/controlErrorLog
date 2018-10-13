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
