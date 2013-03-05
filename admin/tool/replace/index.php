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
 * Search and replace strings throughout all texts in the whole database
 *
 * @package    tool
 * @subpackage replace
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toolreplace');

$search  = optional_param('search', '', PARAM_RAW);
$replace = optional_param('replace', '', PARAM_RAW);
$sure    = optional_param('sure', 0, PARAM_BOOL);

###################################################################
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_replace'));

if ($DB->get_dbfamily() !== 'mysql' and $DB->get_dbfamily() !== 'postgres') {
    //TODO: add $DB->text_replace() to DML drivers
    echo $OUTPUT->notification(get_string('notimplemented', 'tool_replace'));
    echo $OUTPUT->footer();
    die;
}

if (!data_submitted() or !$search or !$replace or !confirm_sesskey() or !$sure) {   /// Print a form
    echo $OUTPUT->notification(get_string('notsupported', 'tool_replace'));
    echo $OUTPUT->notification(get_string('excludedtables', 'tool_replace'));

    echo $OUTPUT->box_start();
    echo '<div class="mdl-align">';
    echo '<form action="index.php" method="post"><div>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<div><label for="search">'.get_string('searchwholedb', 'tool_replace').
            ' </label><input id="search" type="text" name="search" size="40" /> ('.
            get_string('searchwholedbhelp', 'tool_replace').')</div>';
    echo '<div><label for="replace">'.get_string('replacewith', 'tool_replace').
            ' </label><input type="text" id="replace" name="replace" size="40" /> ('.
            get_string('replacewithhelp', 'tool_replace').')</div>';
    echo '<div><label for="sure">'.get_string('disclaimer', 'tool_replace').' </label><input type="checkbox" id="sure" name="sure" value="1" /></div>';
    echo '<div class="buttons"><input type="submit" class="singlebutton" value="Yes, do it now" /></div>';
    echo '</div></form>';
    echo '</div>';
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->box_start();
db_replace($search, $replace);
echo $OUTPUT->box_end();

/// Rebuild course cache which might be incorrect now
echo $OUTPUT->notification(get_string('notifyrebuilding', 'tool_replace'), 'notifysuccess');
rebuild_course_cache();
echo $OUTPUT->notification(get_string('notifyfinished', 'tool_replace'), 'notifysuccess');

echo $OUTPUT->continue_button(new moodle_url('/admin/index.php'));

echo $OUTPUT->footer();


