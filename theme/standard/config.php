<?php

$THEME->name = 'standard';
$THEME->parents = array('base');
$THEME->sheets = array(
    'core',     /** Must come first**/
    'admin',
    'blocks',
    'calendar',
    'course',
    'dock',
    'grade',
    'message',
    'modules',
    'question',
    'css3'      /** Sets up CSS 3 + browser specific styles **/
);
$THEME->enable_dock = true;
$THEME->javascripts_footer = array('navigation');