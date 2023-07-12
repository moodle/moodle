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
 * Grade settings form
 *
 * @package    report_trainingsessions
 * @category   report
 * @version    moodle 2.x
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/lib.php');

class TrainingsessionsGradeSettingsForm extends moodleform {

    protected $linkablemodules;

    public function definition() {
        global $COURSE, $OUTPUT;

        $this->linkablemodules = report_trainingsessions_get_linkable_modules($COURSE->id);

        $config = get_config('report_trainingsessions');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'from');
        $mform->setType('from', PARAM_INT);

        $mform->addElement('hidden', 'to');
        $mform->setType('to', PARAM_INT);

        $mform->addElement('header', 'coursegradehead', get_string('coursegrade', 'report_trainingsessions'));

        $formgroup = array();
        $formgroup[] = &$mform->createElement('checkbox', 'coursegrade', get_string('addcoursegrade', 'report_trainingsessions'));
        $formgroup[] = &$mform->createElement('text', 'courselabel', '', array('size' => 60, 'maxlength' => 60));
        $mform->setType('courselabel', PARAM_TEXT);
        $label = get_string('enablecoursescore', 'report_trainingsessions');
        $seps = array(get_string('courselabel', 'report_trainingsessions').' ');
        $mform->addGroup($formgroup, 'coursegroup', $label, $seps, false);

        $mform->addElement('header', 'modulegrades', get_string('modulegrades', 'report_trainingsessions'));

        $mform->addHelpButton('modulegrades', 'modulegrades', 'report_trainingsessions');

        /*
         * The linked modules portion goes here, but is forced in in the 'definition_after_data' function so
         * that we can get any elements added in the form and not overwrite them with what's in the database.
         */

        $mform->addElement('submit', 'addmodule', get_string('addmodulelabel', 'report_trainingsessions'),
                           array('title' => get_string('addmoduletitle', 'report_trainingsessions')));
        $mform->registerNoSubmitButton('addmodule');

        if (report_trainingsessions_supports_feature('calculation/specialgrades')) {
            $mform->addElement('header', 'specialgrades', get_string('specialgrades', 'report_trainingsessions'));

            $mform->addElement('html', $OUTPUT->box_start('trainingsessions-fieldset'));

            $label = get_string('noextragrade', 'report_trainingsessions');
            $mform->addElement('radio', 'specialgrade', '', $label, TR_TIMEGRADE_DISABLED);

            $label = get_string('addtimegrade', 'report_trainingsessions');
            $mform->addElement('radio', 'specialgrade', '', $label, TR_TIMEGRADE_GRADE);

            $options = array(TR_GRADE_MODE_BINARY => get_string('binary', 'report_trainingsessions'),
                             TR_GRADE_MODE_DISCRETE => get_string('discrete', 'report_trainingsessions'),
                             TR_GRADE_MODE_CONTINUOUS => get_string('continuous', 'report_trainingsessions'));
            $mform->addElement('select', 'timegrademode', get_string('timegrademode', 'report_trainingsessions'), $options);
            $mform->setDefault('timegrademode', $config->timegrademodedefault);
            $mform->disabledIf('timegrademode', 'specialgrade', 'neq', TR_TIMEGRADE_GRADE);
            $mform->addHelpButton('timegrademode', 'grademodes', 'report_trainingsessions');

            $label = get_string('addtimebonus', 'report_trainingsessions');
            $mform->addElement('radio', 'specialgrade', '', $label, TR_TIMEGRADE_BONUS);

            $options = array(TR_GRADE_MODE_DISCRETE => get_string('discrete', 'report_trainingsessions'),
                             TR_GRADE_MODE_CONTINUOUS => get_string('continuous', 'report_trainingsessions'));
            $mform->addElement('select', 'bonusgrademode', get_string('bonusgrademode', 'report_trainingsessions'), $options);
            $mform->setDefault('bonusgrademode', $config->bonusgrademodedefault);
            $mform->disabledIf('bonusgrademode', 'specialgrade', 'neq', TR_TIMEGRADE_BONUS);
            $mform->addHelpButton('timegrademode', 'grademodes', 'report_trainingsessions');

            $mform->addElement('html', $OUTPUT->box_end());

            $options = array(TR_GRADE_SOURCE_COURSE => get_string('coursetotaltime', 'report_trainingsessions'),
                             TR_GRADE_SOURCE_COURSE_EXT => get_string('extelapsed', 'report_trainingsessions'),
                             TR_GRADE_SOURCE_ACTIVITIES => get_string('activitytime', 'report_trainingsessions'));
            $mform->addElement('select', 'timegradesource', get_string('timesource', 'report_trainingsessions'), $options);
            $mform->setDefault('timegradesource', $config->timegradesourcedefault);
            $mform->disabledIf('timegradesource', 'specialgrade', 'eq', TR_TIMEGRADE_DISABLED);

            $mform->addElement('modgrade', 'timegrade', get_string('timegrade', 'report_trainingsessions'));

            $attrs = array('size' => 80, 'maxlength' => 254);
            $mform->addElement('text', 'timegraderanges', get_string('timegraderanges', 'report_trainingsessions'), $attrs);
            $mform->addHelpButton('timegraderanges', 'timegraderanges', 'report_trainingsessions');
            $mform->setType('timegraderanges', PARAM_TEXT);
        }

        if (report_trainingsessions_supports_feature('xls/calculated')) {
            // Preliminary implementation. Not finished yet.
            $label = get_string('calculatedcolumns', 'report_trainingsessions');
            $mform->addElement('header', 'calculatedcolumnshead', $label, '');

            $formulastr = get_string('xlsformula', 'report_trainingsessions');
            $formulalabelstr = get_string('formulalabel', 'report_trainingsessions');
            for ($i = 1; $i <= 3; $i++) {
                $attrs = array('cols' => 60, 'rows' => 4, 'maxlength' => 254);
                $mform->addElement('textarea', 'calculated'.$i, $formulastr.' '.$i, $attrs);
                $mform->setType('calculated'.$i, PARAM_RAW);
                $mform->addHelpButton('calculated'.$i, 'calculated', 'report_trainingsessions');
                $attrs = array('size' => 20, 'maxlength' => 254);
                $mform->addElement('text', 'calculated'.$i.'label', $formulalabelstr.' '.$i, $attrs);
                $mform->setType('calculated'.$i.'label', PARAM_RAW);
            }

            $attrs = array('size' => 80, 'maxlength' => 254);
            $label = get_string('lineaggregators', 'report_trainingsessions');
            $mform->addElement('text', 'lineaggregators', $label, $attrs);
            $mform->addHelpButton('lineaggregators', 'lineaggregators', 'report_trainingsessions');
            $mform->setType('lineaggregators', PARAM_TEXT);
        }

        $this->add_action_buttons(true);
    }

    /**
     * Add the added activities portion only after the entire form has been created. That way,
     * we can act on previous added values that haven't been committed to the database.
     * Check for an 'addmodule' button. If the linked activities fields are all full, add an empty one.
     */
    public function definition_after_data() {
        global $COURSE;

        // Start process core datas (conditions, etc.).
        parent::definition_after_data();

        /*
         * This gets called more than once, and there's no way to tell which time this is, so set a
         * variable to make it as called so we only do this processing once.
         */
        if (!empty($this->def_after_data_done)) {
            return;
        }
        $this->def_after_data_done = true;

        $mform    =& $this->_form;
        $fdata = $mform->getSubmitValues();

        /*
         * Get the existing linked activities from the database, unless this form has resubmitted itself, in
         * which case they will be in the form already.
         */
        $moduleids = array();

        if (empty($fdata)) {
            if ($linkedmodules = report_trainingsessions_get_graded_modules($COURSE->id)) {
                foreach ($linkedmodules as $cidx => $cmid) {
                    if ($cmid > 0) {
                        $moduleids[$cidx] = $cmid;
                    }
                }
            }
        } else {
            if (isset($fdata['moduleid'])) {
                foreach ($fdata['moduleid'] as $cidx => $cmid) {
                    if ($cmid > 0) {
                        $moduleids[$cidx] = $cmid;
                    }
                }
            }
        }

        if (isset($fdata['linkablemodules']) && is_array($fdata['linkablemodules'])) {
            foreach ($fdata['linkablemodules'] as $linkablemodule) {
                $moduleids[] = $linkablemodule;
            }
        }
        $moduleids = array_unique($moduleids);
        $ix = 0;
        foreach ($moduleids as $cidx => $modid) {
            $formgroup = array();
            $choices = array(
                '' => get_string('disabled', 'report_trainingsessions'),
                $modid => $this->linkablemodules[$modid]
            );
            $formgroup[] = &$mform->createElement('select', 'moduleid['.$ix.']', '', $choices);
            $mform->setDefault('moduleid['.$ix.']', $modid);
            $formgroup[] = & $mform->createElement('text', 'scorelabel['.$ix.']', '', array('maxlength' => 60));
            $mform->setType('scorelabel['.$ix.']', PARAM_TEXT);
            $label = get_string('modgrade', 'report_trainingsessions', ($ix + 1));
            $padding = array(' '.get_string('columnname', 'report_trainingsessions'));
            $group =& $mform->createElement('group', 'modgrade'.$ix, $label, $formgroup, $padding, false);
            $mform->insertElementBefore($group, 'addmodule');
            $ix++;
        }

        $availablemodules = $this->linkablemodules;
        unset($availablemodules[0]);
        $label = get_string('availableactivities', 'report_trainingsessions');
        $linkablemodules = $mform->createElement('select', 'linkablemodules', $label, $availablemodules, array('size' => 10));
        $linkablemodules->setMultiple(true);

        $formgroup[] = $linkablemodules;
        $mform->insertElementBefore($linkablemodules, 'addmodule');
    }

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        if (($data['specialgrade'] == TR_TIMEGRADE_GRADE) &&
                ($data['timegrademode'] == TR_GRADE_MODE_CONTINUOUS) &&
                        ($data['timegrade'] < 0)) {
            // Scales cannot be used in continuous mode.
            $errors['timegrademode'] = get_string('errorcontinuousscale', 'report_trainingsessions');
            $errors['timegrade'] = get_string('errorcontinuousscale', 'report_trainingsessions');
        }

        if (($data['specialgrade'] == TR_TIMEGRADE_BONUS) &&
                ($data['bonusgrademode'] == TR_GRADE_MODE_CONTINUOUS) &&
                        ($data['timegrade'] < 0)) {
            // Scales cannot be used in continuous mode.
            $errors['bonusgrademode'] = get_string('errorcontinuousscale', 'report_trainingsessions');
            $errors['timegrade'] = get_string('errorcontinuousscale', 'report_trainingsessions');
        }

        if (($data['specialgrade'] == TR_TIMEGRADE_GRADE) &&
                        ($data['timegrademode'] < TR_GRADE_MODE_CONTINUOUS) &&
                                (empty($data['timegraderanges']))) {
            $errors['timegrademode'] = get_string('errordiscretenoranges', 'report_trainingsessions');
            $errors['timegraderanges'] = get_string('errordiscretenoranges', 'report_trainingsessions');
        }

        if (($data['specialgrade'] == TR_TIMEGRADE_BONUS) &&
                        ($data['bonusgrademode'] < TR_GRADE_MODE_CONTINUOUS) &&
                                (empty($data['timegraderanges']))) {
            $errors['bonusgrademode'] = get_string('errordiscretenoranges', 'report_trainingsessions');
            $errors['timegraderanges'] = get_string('errordiscretenoranges', 'report_trainingsessions');
        }

        return $errors;
    }
}