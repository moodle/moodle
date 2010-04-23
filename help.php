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

$PAGE->set_url('/help.php');
$PAGE->set_pagelayout('popup');
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

if ($ajax) {
    @header('Content-Type: text/plain; charset=utf-8');
} else {
    echo $OUTPUT->header();
}

if ($sm->string_exists($identifier.'_help', $component)) {
    $options = new object;
    $options->trusted = false;
    $options->noclean = false;
    $options->smiley = false;
    $options->filter = false;
    $options->para = true;
    $options->newlines = false;

    // Should be simple wiki only MDL-21695
    echo format_text(get_string($identifier.'_help', $component), FORMAT_MOODLE, $options);  

    if ($sm->string_exists($identifier.'_link', $component)) {  // Link to further info in Moodle docs
        $link = get_string($identifier.'_link', $component);
        $linktext = get_string('morehelp');
        echo '<div class="helpdoclink">'.$OUTPUT->doc_link($link, $linktext).'</div>';
    }

} else {
    echo "<p><strong>TODO</strong>: fix help for [{$identifier}_help, $component]</p>";
}

if (!$ajax) {
    echo $OUTPUT->footer();
}
