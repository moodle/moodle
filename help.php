<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays help via AJAX call or in a new page
 *
 * Use {@link core_renderer::help_icon()} or {@link addHelpButton()} to display
 * the help icon.
 *
 * @copyright 2002 onwards Martin Dougiamas
 * @package   core
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);

require_once(dirname(__FILE__) . '/config.php');

$identifier = required_param('identifier', PARAM_STRINGID);
$component  = required_param('component', PARAM_COMPONENT);
$lang       = required_param('lang', PARAM_LANG); // TODO: maybe split into separate scripts
$ajax       = optional_param('ajax', 0, PARAM_BOOL);

if (!$lang) {
    $lang = 'en';
}
$SESSION->lang = $lang; // does not actually modify session because we do not use cookies here

$sm = get_string_manager();

$PAGE->set_url('/help.php');
$PAGE->set_pagelayout('popup');
$PAGE->set_context(context_system::instance());

if ($ajax) {
    @header('Content-Type: text/plain; charset=utf-8');
} else {
    echo $OUTPUT->header();
}

if (!$sm->string_exists($identifier.'_help', $component)) {
    // strings on-diskc cache may be dirty - try to rebuild it and check again
    $sm->load_component_strings($component, current_language(), true);
}

if ($sm->string_exists($identifier.'_help', $component)) {
    $options = new stdClass();
    $options->trusted = false;
    $options->noclean = false;
    $options->smiley = false;
    $options->filter = false;
    $options->para = true;
    $options->newlines = false;
    $options->overflowdiv = !$ajax;

    echo $OUTPUT->heading(format_string(get_string($identifier, $component)), 1, 'helpheading');
    // Should be simple wiki only MDL-21695
    echo format_text(get_string($identifier.'_help', $component), FORMAT_MARKDOWN, $options);

    if ($sm->string_exists($identifier.'_link', $component)) {  // Link to further info in Moodle docs
        $link = get_string($identifier.'_link', $component);
        $linktext = get_string('morehelp');
        echo '<div class="helpdoclink">'.$OUTPUT->doc_link($link, $linktext).'</div>';
    }

} else {
    echo "<p><strong>TODO</strong>: missing help string [{$identifier}_help, $component]</p>";
}

if (!$ajax) {
    echo $OUTPUT->footer();
}
