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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/assign/feedback/offline/importgradeslib.php');

/**
 * Import grades form
 *
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_offline_import_grades_form extends moodleform implements renderable {

    /**
     * Create this grade import form
     */
    public function definition() {
        global $USER, $PAGE, $DB;

        $mform = $this->_form;
        $params = $this->_customdata;

        $renderer = $PAGE->get_renderer('assign');

        // Visible elements.
        $assignment = $params['assignment'];
        $csvdata = $params['csvdata'];
        $gradeimporter = $params['gradeimporter'];
        $update = false;

        $ignoremodified = $params['ignoremodified'];
        $draftid = $params['draftid'];

        if (!$gradeimporter) {
            throw new \moodle_exception('invalidarguments');
            return;
        }

        if ($csvdata) {
            $gradeimporter->parsecsv($csvdata);
        }

        $scaleoptions = null;
        if ($assignment->get_instance()->grade < 0) {
            if ($scale = $DB->get_record('scale', array('id'=>-($assignment->get_instance()->grade)))) {
                $scaleoptions = make_menu_from_list($scale->scale);
            }
        }
        if (!$gradeimporter->init()) {
            $thisurl = new moodle_url('/mod/assign/view.php', array('action'=>'viewpluginpage',
                                                                     'pluginsubtype'=>'assignfeedback',
                                                                     'plugin'=>'offline',
                                                                     'pluginaction'=>'uploadgrades',
                                                                     'id'=>$assignment->get_course_module()->id));
            throw new \moodle_exception('invalidgradeimport', 'assignfeedback_offline', $thisurl);
            return;
        }

        $mform->addElement('header', 'importgrades', get_string('importgrades', 'assignfeedback_offline'));

        $updates = array();
        while ($record = $gradeimporter->next()) {
            $user = $record->user;
            $grade = $record->grade;
            $mark = (isset($record->mark) && $record->mark && $record->mark !== '') ? $record->mark : null;
            $modified = $record->modified;
            $userdesc = fullname($user);
            if ($assignment->is_blind_marking()) {
                $userdesc = get_string('hiddenuser', 'assign') . $assignment->get_uniqueid_for_user($user->id);
            }

            $usergrade = $assignment->get_user_grade($user->id, false);
            $usermark = false;
            if ($usergrade) {
                $usermark = $assignment->get_mark($usergrade->id, $USER->id);
            }
            // Note: we lose the seconds when converting to user date format - so must not count seconds in comparision.
            $skip = false;

            $stalemodificationdate = ($usergrade && $usergrade->timemodified > ($modified + 60));

            if (!empty($scaleoptions)) {
                // This is a scale - we need to convert any grades to indexes in the scale.
                $scaleindex = array_search($grade, $scaleoptions);
                $markindex = array_search($mark, $scaleoptions);
                $grade = ($scaleindex !== false) ? $scaleindex : '';
                $mark = ($markindex !== false) ? $markindex : '';
            } else {
                $grade = unformat_float($grade);
                $mark = unformat_float($mark);
            }

            if (
                $usergrade &&
                $usergrade->grade == $grade &&
                $usermark &&
                $usermark->mark == $mark
            ) {
                // Skip - neither grade nor mark modified.
                $skip = true;
            } else if (
                (!isset($grade) || $grade === '' || $grade < 0) &&
                (!isset($mark) || $mark === '' || $mark < 0)
            ) {
                // Skip - grade and mark have no value.
                $skip = true;
            } else if (!$ignoremodified && $stalemodificationdate) {
                // Skip - grade has been modified.
                $skip = true;
            } else if ($assignment->grading_disabled($user->id)) {
                // Skip grade is locked.
                $skip = true;
            } else if (($assignment->get_instance()->grade > -1) &&
                      (($grade < 0) || ($grade > $assignment->get_instance()->grade))) {
                // Out of range.
                $skip = true;
            }

            // Work out which (or both) of the changes to display - grade and/or mark.
            $grademodified = ($grade && $grade !== '' && $grade >= 0);
            if ($usergrade) {
                $grademodified = ($grademodified && $grade != $usergrade->grade);
            }
            $markmodified = ($mark && $mark !== '' && $mark >= 0);
            if ($usermark) {
                $markmodified = ($markmodified && $mark != $usermark->mark);
            }

            if (!$skip) {
                $update = true;
                if (!empty($scaleoptions)) {
                    $formattedgrade = ($grade !== '') ? $scaleoptions[$grade] : $grade;
                    $formattedmark = ($mark !== '') ? $scaleoptions[$mark] : $mark;
                } else {
                    $gradeitem = $assignment->get_grade_item();
                    $formattedgrade = format_float($grade, $gradeitem->get_decimals());
                    $formattedmark = format_float($mark, $gradeitem->get_decimals());
                }

                if ($grademodified) {
                    $updates[] = get_string('gradeupdate', 'assignfeedback_offline', [
                        'grade' => $formattedgrade, 'student' => $userdesc,
                    ]);
                }

                if ($markmodified) {
                    $updates[] = get_string('markupdate', 'assignfeedback_offline', [
                        'mark' => $formattedmark, 'student' => $userdesc,
                    ]);
                }
            }

            if ($ignoremodified || !$stalemodificationdate) {
                foreach ($record->feedback as $feedback) {
                    $markid = null;
                    $ismarkercol = false;
                    $isourmarkercol = false;
                    // Is it a marker column? And is this user one of the markers for the student?
                    if (isset($feedback['markernumber'])) {
                        $ismarkercol = true;
                        // Get the mark record or create it if it doesn't exist.
                        $marker = $assignment->get_marker_number($user->id, $feedback['markernumber'] - 1);
                        if ($marker && $marker->id == $USER->id) {
                            $isourmarkercol = true;
                            if ($usermark) {
                                $markid = $usermark->id;
                            } else {
                                // In this case it's our marker column, but there's no mark record to get an ID.
                                // So we use -1 instead of null, as null will retrieve the overall value from the
                                // feedback plugin, instead of a marker specific one.
                                $markid = -1;
                            }
                        }
                    }
                    $plugin = $feedback['plugin'];
                    $field = $feedback['field'];
                    $newvalue = $feedback['value'];
                    $description = $feedback['description'];
                    $oldvalue = '';
                    if ($ismarkercol && !$isourmarkercol) {
                        continue;
                    }
                    if ($usergrade) {
                        $oldvalue = $plugin->get_editor_text($field, $usergrade->id, $markid);
                    }
                    if ($newvalue != $oldvalue) {
                        $update = true;
                        $updates[] = get_string('feedbackupdate', 'assignfeedback_offline',
                                                    array('text'=>$newvalue, 'field'=>$description, 'student'=>$userdesc));
                    }
                }
            }

        }
        $gradeimporter->close(false);

        if ($update) {
            $mform->addElement('html', $renderer->list_block_contents(array(), $updates));
        } else {
            $mform->addElement('html', get_string('nochanges', 'assignfeedback_offline'));
        }

        $mform->addElement('hidden', 'id', $assignment->get_course_module()->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'viewpluginpage');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'confirm', 'true');
        $mform->setType('confirm', PARAM_BOOL);
        $mform->addElement('hidden', 'plugin', 'offline');
        $mform->setType('plugin', PARAM_PLUGIN);
        $mform->addElement('hidden', 'pluginsubtype', 'assignfeedback');
        $mform->setType('pluginsubtype', PARAM_PLUGIN);
        $mform->addElement('hidden', 'pluginaction', 'uploadgrades');
        $mform->setType('pluginaction', PARAM_ALPHA);
        $mform->addElement('hidden', 'importid', $gradeimporter->importid);
        $mform->setType('importid', PARAM_INT);

        $mform->addElement('hidden', 'encoding', $gradeimporter->get_encoding());
        $mform->setType('encoding', PARAM_ALPHAEXT);
        $mform->addElement('hidden', 'separator', $gradeimporter->get_separator());
        $mform->setType('separator', PARAM_ALPHA);

        $mform->addElement('hidden', 'ignoremodified', $ignoremodified);
        $mform->setType('ignoremodified', PARAM_BOOL);
        $mform->addElement('hidden', 'draftid', $draftid);
        $mform->setType('draftid', PARAM_INT);
        if ($update) {
            $this->add_action_buttons(true, get_string('confirm'));
        } else {
            $mform->addElement('cancel');
            $mform->closeHeaderBefore('cancel');
        }

    }
}

