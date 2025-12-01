<?php
defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2024112600;
$plugin->requires  = 2022041900; // Moodle 4.0+
$plugin->component = 'theme_aust';
$plugin->dependencies = [
    'theme_boost' => 2022041900,
];
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.0';
