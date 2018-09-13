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
 * mod/hotpot/tools/clear_cache.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot.'/mod/hotpot/lib.php');

require_login(SITEID);
if (class_exists('context_system')) {
    $context = context_system::instance();
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
}
require_capability('moodle/site:config', $context);

// $SCRIPT is set by initialise_fullme() in "lib/setuplib.php"
// it is the path below $CFG->wwwroot of this script
$PAGE->set_url($CFG->wwwroot.$SCRIPT);

$title = get_string('clearcache', 'mod_hotpot');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

if ($confirm = optional_param('confirm', 0, PARAM_INT)) {
    $DB->delete_records('hotpot_cache');
    $count_cache = 0;
} else {
    $count_cache = $DB->count_records('hotpot_cache');
}
$count_quizzes = $DB->count_records('hotpot');

echo $OUTPUT->box_start();

echo '<table style="margin:auto"><tbody>'."\n";
echo '<tr><th style="text-align:right;">'.get_string('quizzes', 'mod_hotpot').':</th><td>'.$count_quizzes.'</td></tr>'."\n";
echo '<tr><th style="text-align:right;">'.get_string('cacherecords', 'mod_hotpot').':</th><td>'.$count_cache.'</td></tr>'."\n";
if ($count_cache) {
    echo '<tr><td colspan="2" style="text-align:center;">';
    echo '<form action="'.$CFG->wwwroot.$SCRIPT.'" method="post">';
    echo '<fieldset>';
    echo '<input type="hidden" value="1" name="confirm" />';
    echo '<input type="submit" value="'.get_string('confirm').'" />';
    echo '</fieldset>';
    echo '</td></tr>'."\n";
} else {
    echo '<tr><td colspan="2" style="text-align:center;">'.get_string('clearedcache', 'mod_hotpot').'</td></tr>'."\n";
}
echo '</tbody></table>'."\n";

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
