<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_academi_child';
$plugin->dependencies = [
    'theme_academi' => 2024060503,
    'theme_boost' => 2024042200
];
$plugin->release = '1.0';
$plugin->version = 2024110600;
$plugin->requires = 2024042200;
$plugin->maturity = MATURITY_STABLE;
