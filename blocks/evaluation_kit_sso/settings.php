<?php
$config = get_config('blocks/evaluation_kit_sso');
$configs = array();
$configs[] = new admin_setting_configtext('EvalKitaccounturl', get_string('EvalKitaccounturl', 'block_evaluation_kit_sso'), '','', PARAM_TEXT);
$configs[] = new admin_setting_configtext('EvalKitconsumerkey', get_string('EvalKitconsumerkey', 'block_evaluation_kit_sso'), '','', PARAM_TEXT);
$configs[] = new admin_setting_configtext('EvalKitsharedsecretkey', get_string('EvalKitsharedsecretkey', 'block_evaluation_kit_sso'), '','', PARAM_TEXT);
foreach ($configs as $config) {
    $config->plugin = 'blocks/evaluation_kit_sso';
    $settings->add($config);
}
?>