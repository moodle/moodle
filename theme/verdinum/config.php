<?php
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/lib.php');
$THEME->name = 'verdinum';
$THEME->parents = ['educard', 'boost'];
$THEME->settings = theme_config::load('educard')->settings;
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->editor_scss = ['editor'];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
// $THEME->requiredblocks = '';
// $THEME->scss = function ($theme) {
//     return theme_educard_get_main_scss_content($theme);
// };


// if (!defined('BLOCK_ADDBLOCK_POSITION_FLATNAV')) {
//     define('BLOCK_ADDBLOCK_POSITION_FLATNAV', 1);
// }



// $THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->activityheaderconfig = [
    'notitle' => true,
];
