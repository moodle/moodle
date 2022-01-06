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
 * List of grade letters.
 *
 * @package   core_grades
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$contextid = optional_param('id', SYSCONTEXTID, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);
$edit     = optional_param('edit', false, PARAM_BOOL); //are we editing?

$PAGE->set_url('/grade/edit/letter/index.php', array('id' => $contextid));

list($context, $course, $cm) = get_context_info_array($contextid);
$contextid = null;//now we have a context object throw away the $contextid from the params

//if viewing
if (!$edit) {
    if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:manageletters', $context)) {
        print_error('nopermissiontoviewletergrade');
    }
} else {//else we're editing
    require_capability('moodle/grade:manageletters', $context);
}

$returnurl = null;
$editparam = null;
if ($context->contextlevel == CONTEXT_SYSTEM or $context->contextlevel == CONTEXT_COURSECAT) {
    require_once $CFG->libdir.'/adminlib.php';

    admin_externalpage_setup('letters');

    $admin = true;
    $returnurl = "$CFG->wwwroot/grade/edit/letter/index.php";
    $editparam = '?edit=1';
} else if ($context->contextlevel == CONTEXT_COURSE) {

    $PAGE->set_pagelayout('standard');//calling this here to make blocks display

    require_login($context->instanceid, false, $cm);

    $admin = false;
    $returnurl = $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$context->id;
    $editparam = '&edit=1';

    $gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'letter', 'courseid'=>$course->id));
} else {
    print_error('invalidcourselevel');
}

$strgrades = get_string('grades');
$pagename  = get_string('letters', 'grades');

$letters = grade_get_letters($context);
$num = count($letters) + 3;

$override = $DB->record_exists('grade_letters', array('contextid' => $context->id));

//if were viewing the letters
if (!$edit) {

    $data = array();

    $max = 100;
    foreach($letters as $boundary=>$letter) {
        $line = array();
        $line[] = format_float($max,2).' %';
        $line[] = format_float($boundary,2).' %';
        $line[] = format_string($letter);
        $data[] = $line;
        $max = $boundary - 0.01;
    }

    print_grade_page_head($COURSE->id, 'letter', 'view', get_string('gradeletters', 'grades'));

    if (!empty($override)) {
        echo $OUTPUT->notification(get_string('gradeletteroverridden', 'grades'), 'notifymessage');
    }

    $stredit = get_string('editgradeletters', 'grades');
    $editlink = html_writer::nonempty_tag('div', html_writer::link($returnurl.$editparam, $stredit), array('class'=>'mdl-align'));
    echo $editlink;

    $table = new html_table();
    $table->id = 'grade-letters-view';
    $table->head  = array(get_string('max', 'grades'), get_string('min', 'grades'), get_string('letter', 'grades'));
    $table->size  = array('30%', '30%', '40%');
    $table->align = array('left', 'left', 'left');
    $table->width = '30%';
    $table->data  = $data;
    $table->tablealign  = 'center';
    echo html_writer::table($table);

    echo $editlink;
} else { //else we're editing
    require_once('edit_form.php');

    $data = new stdClass();
    $data->id = $context->id;

    $i = 1;
    foreach ($letters as $boundary=>$letter) {
        $gradelettername = 'gradeletter'.$i;
        $gradeboundaryname = 'gradeboundary'.$i;

        $data->$gradelettername   = $letter;
        $data->$gradeboundaryname = $boundary;
        $i++;
    }
    $data->override = $override;

    $mform = new edit_letter_form($returnurl.$editparam, array('num'=>$num, 'admin'=>$admin));
    $mform->set_data($data);

    if ($mform->is_cancelled()) {
        redirect($returnurl);

    } else if ($data = $mform->get_data()) {

        // Make sure we are updating the cache.
        $cache = cache::make('core', 'grade_letters');

        if (!$admin and empty($data->override)) {
            $records = $DB->get_records('grade_letters', array('contextid' => $context->id));
            foreach ($records as $record) {
                $DB->delete_records('grade_letters', array('id' => $record->id));
                // Trigger the letter grade deleted event.
                $event = \core\event\grade_letter_deleted::create(array(
                    'objectid' => $record->id,
                    'context' => $context,
                ));
                $event->trigger();
            }

            // Make sure we clear the cache for this context.
            $cache->delete($context->id);
            redirect($returnurl);
        }

        $letters = array();
        for ($i=1; $i < $num+1; $i++) {
            $gradelettername = 'gradeletter'.$i;
            $gradeboundaryname = 'gradeboundary'.$i;

            if (property_exists($data, $gradeboundaryname) and $data->$gradeboundaryname != -1) {
                $letter = trim($data->$gradelettername);
                if ($letter == '') {
                    continue;
                }

                $boundary = floatval($data->$gradeboundaryname);
                if ($boundary < 0 || $boundary > 100) {
                    continue;    // Skip if out of range.
                }

                // The keys need to be strings so floats are not truncated.
                $letters[number_format($boundary, 5)] = $letter;
            }
        }

        $pool = array();
        if ($records = $DB->get_records('grade_letters', array('contextid' => $context->id), 'lowerboundary ASC')) {
            foreach ($records as $r) {
                // Will re-use the lowerboundary to avoid duplicate during the update process.
                $pool[number_format($r->lowerboundary, 5)] = $r;
            }
        }

        foreach ($letters as $boundary => $letter) {
            $record = new stdClass();
            $record->letter        = $letter;
            $record->lowerboundary = $boundary;
            $record->contextid     = $context->id;

            if (isset($pool[$boundary])) {
                // Re-use the existing boundary to avoid key constraint.
                if ($letter != $pool[$boundary]->letter) {
                    // The letter has been assigned to another boundary, we update it.
                    $record->id = $pool[$boundary]->id;
                    $DB->update_record('grade_letters', $record);
                    // Trigger the letter grade updated event.
                    $event = \core\event\grade_letter_updated::create(array(
                        'objectid' => $record->id,
                        'context' => $context,
                    ));
                    $event->trigger();
                }
                unset($pool[$boundary]);    // Remove the letter from the pool.
            } else if ($candidate = array_pop($pool)) {
                // The boundary is new, we update a random record from the pool.
                $record->id = $candidate->id;
                $DB->update_record('grade_letters', $record);
                // Trigger the letter grade updated event.
                $event = \core\event\grade_letter_updated::create(array(
                    'objectid' => $record->id,
                    'context' => $context,
                ));
                $event->trigger();
            } else {
                // No records were found, this must be a new letter.
                $newid = $DB->insert_record('grade_letters', $record);
                // Trigger the letter grade added event.
                $event = \core\event\grade_letter_created::create(array(
                    'objectid' => $newid,
                    'context' => $context,
                ));
                $event->trigger();
            }
        }

        // Cache the changed letters.
        if (!empty($letters)) {

            // For some reason, the cache saves it in the order in which they were entered
            // but we really want to order them in descending order so we sort it here.
            krsort($letters);
            $cache->set($context->id, $letters);
        }

        // Delete the unused records.
        foreach($pool as $leftover) {
            $DB->delete_records('grade_letters', array('id' => $leftover->id));
            // Trigger the letter grade deleted event.
            $event = \core\event\grade_letter_deleted::create(array(
                'objectid' => $leftover->id,
                'context' => $context,
            ));
            $event->trigger();
        }

        redirect($returnurl);
    }

    print_grade_page_head($COURSE->id, 'letter', 'edit', get_string('editgradeletters', 'grades'));

    $mform->display();
}

echo $OUTPUT->footer();
