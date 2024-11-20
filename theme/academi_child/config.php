<?php
defined('MOODLE_INTERNAL') || die();

$THEME->name = 'academi_child';
$THEME->parents = ['academi', 'boost'];
$THEME->sheets = ['custom'];
$THEME->editor_sheets = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
// $THEME->csspostprocess = 'theme_academi_process_css';
