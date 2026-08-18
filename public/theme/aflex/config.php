<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->name = 'aflex';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->scss = function($theme) {
    return theme_aflex_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_aflex_get_pre_scss';
$THEME->extrascsscallback = 'theme_aflex_get_extra_scss';
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;

