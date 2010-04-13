<?php
/**
 * help.php - Displays help page.
 *
 * Prints a very simple page and includes
 * page content or a string from elsewhere.
 * Usually this will appear in a popup
 * See {@link helpbutton()} in {@link lib/moodlelib.php}
 *
 * @author Martin Dougiamas
 * @package moodlecore
 */

define('NO_MOODLE_COOKIES', true);

require_once('config.php');

$identifier = required_param('identifier', PARAM_SAFEDIR);
$component  = required_param('component', PARAM_SAFEDIR);
$lang       = required_param('component', PARAM_LANG); // TODO: maybe split into separate scripts
$ajax       = optional_param('ajax', 0, PARAM_BOOL);

if (!$lang) {
    $lang = 'en';
}

$SESSION->lang = $lang; // does not actually modify session because we do not use cookies here

$sm = get_string_manager();

//TODO: this is a minimalistic help page, needs a lot more love 

$PAGE->set_url('/help.php');
$PAGE->set_pagelayout('popup'); // not really a popup because this page gets dispalyed directly only when JS disabled

if ($ajax) {
    @header('Content-Type: text/plain; charset=utf-8');
} else {
    echo $OUTPUT->header();
}

if ($sm->string_exists($identifier.'_hlp', $component)) {
    echo get_string($identifier.'_hlp', $component);
} else {
    echo "<p><strong>TODO</strong>: fix help for [{$identifier}_hlp, $component]</p>";
}

if (!$ajax) {
    echo $OUTPUT->footer();
}
