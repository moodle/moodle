<?php
defined('MOODLE_INTERNAL') || die();

function theme_verdinum_page_init(moodle_page $page)
{
    // require_once($CFG->dirroot . '/theme/educard/lib.php');
    theme_educard_page_init($page);
}
