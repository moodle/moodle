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
 * Form to define a new instance of lesson or edit an instance.
 * It is used from /course/modedit.php.
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

class mod_lesson_mod_form extends moodleform_mod {

    protected $course = null;

    public function mod_lesson_mod_form($current, $section, $cm, $course) {
        $this->course = $course;
        parent::moodleform_mod($current, $section, $cm, $course);
    }

    function definition() {
        global $CFG, $COURSE, $DB;

        $mform    = $this->_form;

        $config = get_config('lesson');

        $mform->addElement('header', 'general', get_string('general', 'form'));

        /** Legacy slideshow width element to maintain backwards compatibility */
        $mform->addElement('hidden', 'width');
        $mform->setType('width', PARAM_INT);
        $mform->setDefault('width', $CFG->lesson_slideshowwidth);

        /** Legacy slideshow height element to maintain backwards compatibility */
        $mform->addElement('hidden', 'height');
        $mform->setType('height', PARAM_INT);
        $mform->setDefault('height', $CFG->lesson_slideshowheight);

        /** Legacy slideshow background color element to maintain backwards compatibility */
        $mform->addElement('hidden', 'bgcolor');
        $mform->setType('bgcolor', PARAM_TEXT);
        $mform->setDefault('bgcolor', $CFG->lesson_slideshowbgcolor);

        /** Legacy media popup width element to maintain backwards compatibility */
        $mform->addElement('hidden', 'mediawidth');
        $mform->setType('mediawidth', PARAM_INT);
        $mform->setDefault('mediawidth', $CFG->lesson_mediawidth);

        /** Legacy media popup height element to maintain backwards compatibility */
        $mform->addElement('hidden', 'mediaheight');
        $mform->setType('mediaheight', PARAM_INT);
        $mform->setDefault('mediaheight', $CFG->lesson_mediaheight);

        /** Legacy media popup close button element to maintain backwards compatibility */
        $mform->addElement('hidden', 'mediaclose');
        $mform->setType('mediaclose', PARAM_BOOL);
        $mform->setDefault('mediaclose', $CFG->lesson_mediaclose);

        /** Legacy maximum highscores element to maintain backwards compatibility */
        $mform->addElement('hidden', 'maxhighscores');
        $mform->setType('maxhighscores', PARAM_INT);
        $mform->setDefault('maxhighscores', $CFG->lesson_maxhighscores);

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();

        // Appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        $filemanageroptions = array();
        $filemanageroptions['filetypes'] = '*';
        $filemanageroptions['maxbytes'] = $this->course->maxbytes;
        $filemanageroptions['subdirs'] = 0;
        $filemanageroptions['maxfiles'] = 1;

        $mform->addElement('filemanager', 'mediafile', get_string('mediafile', 'lesson'), null, $filemanageroptions);
        $mform->addHelpButton('mediafile', 'mediafile', 'lesson');

        $mform->addElement('selectyesno', 'progressbar', get_string('progressbar', 'lesson'));
        $mform->addHelpButton('progressbar', 'progressbar', 'lesson');
        $mform->setDefault('progressbar', 0);

        $mform->addElement('selectyesno', 'ongoing', get_string('ongoing', 'lesson'));
        $mform->addHelpButton('ongoing', 'ongoing', 'lesson');
        $mform->setDefault('ongoing', 0);

        $mform->addElement('selectyesno', 'displayleft', get_string('displayleftmenu', 'lesson'));
        $mform->addHelpButton('displayleft', 'displayleftmenu', 'lesson');
        $mform->setDefault('displayleft', 0);

        $options = array();
        for($i = 100; $i >= 0; $i--) {
            $options[$i] = $i.'%';
        }
        $mform->addElement('select', 'displayleftif', get_string('displayleftif', 'lesson'), $options);
        $mform->addHelpButton('displayleftif', 'displayleftif', 'lesson');
        $mform->setDefault('displayleftif', 0);

        $mform->addElement('selectyesno', 'slideshow', get_string('slideshow', 'lesson'));
        $mform->addHelpButton('slideshow', 'slideshow', 'lesson');
        $mform->setDefault('slideshow', 0);

        $numbers = array();
        for ($i = 20; $i > 1; $i--) {
            $numbers[$i] = $i;
        }

        $mform->addElement('select', 'maxanswers', get_string('maximumnumberofanswersbranches', 'lesson'), $numbers);
        $mform->setDefault('maxanswers', $CFG->lesson_maxanswers);
        $mform->setType('maxanswers', PARAM_INT);
        $mform->addHelpButton('maxanswers', 'maximumnumberofanswersbranches', 'lesson');

        $mform->addElement('selectyesno', 'feedback', get_string('displaydefaultfeedback', 'lesson'));
        $mform->addHelpButton('feedback', 'displaydefaultfeedback', 'lesson');
        $mform->setDefault('feedback', 0);

        // Get the modules.
        if ($mods = get_course_mods($COURSE->id)) {
            $modinstances = array();
            foreach ($mods as $mod) {
                // Get the module name and then store it in a new array.
                if ($module = get_coursemodule_from_instance($mod->modname, $mod->instance, $COURSE->id)) {
                    // Exclude this lesson, if it's already been saved.
                    if (!isset($this->_cm->id) || $this->_cm->id != $mod->id) {
                        $modinstances[$mod->id] = $mod->modname.' - '.$module->name;
                    }
                }
            }
            asort($modinstances); // Sort by module name.
            $modinstances=array(0=>get_string('none'))+$modinstances;

            $mform->addElement('select', 'activitylink', get_string('activitylink', 'lesson'), $modinstances);
            $mform->addHelpButton('activitylink', 'activitylink', 'lesson');
            $mform->setDefault('activitylink', 0);
        }

        // Availability.
        $mform->addElement('header', 'availabilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'available', get_string('available', 'lesson'), array('optional'=>true));
        $mform->setDefault('available', 0);

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'lesson'), array('optional'=>true));
        $mform->setDefault('deadline', 0);

        // Time limit.
        $mform->addElement('duration', 'timelimit', get_string('timelimit', 'lesson'),
                array('optional' => true));
        $mform->addHelpButton('timelimit', 'timelimit', 'lesson');

        $mform->addElement('selectyesno', 'usepassword', get_string('usepassword', 'lesson'));
        $mform->addHelpButton('usepassword', 'usepassword', 'lesson');
        $mform->setDefault('usepassword', 0);

        $mform->addElement('passwordunmask', 'password', get_string('password', 'lesson'));
        $mform->setDefault('password', '');
        $mform->setType('password', PARAM_RAW);
        $mform->disabledIf('password', 'usepassword', 'eq', 0);
        $mform->disabledIf('passwordunmask', 'usepassword', 'eq', 0);

        // Dependent on.
        if ($this->current && isset($this->current->dependency) && $this->current->dependency) {
            $mform->addElement('header', 'dependencyon', get_string('prerequisitelesson', 'lesson'));
            $mform->addElement('static', 'warningobsolete',
                get_string('warning', 'lesson'),
                get_string('prerequisiteisobsolete', 'lesson'));
            $options = array(0 => get_string('none'));
            if ($lessons = get_all_instances_in_course('lesson', $COURSE)) {
                foreach ($lessons as $lesson) {
                    if ($lesson->id != $this->_instance) {
                        $options[$lesson->id] = format_string($lesson->name, true);
                    }

                }
            }
            $mform->addElement('select', 'dependency', get_string('dependencyon', 'lesson'), $options);
            $mform->addHelpButton('dependency', 'dependencyon', 'lesson');
            $mform->setDefault('dependency', 0);

            $mform->addElement('text', 'timespent', get_string('timespentminutes', 'lesson'));
            $mform->setDefault('timespent', 0);
            $mform->setType('timespent', PARAM_INT);
            $mform->disabledIf('timespent', 'dependency', 'eq', 0);

            $mform->addElement('checkbox', 'completed', get_string('completed', 'lesson'));
            $mform->setDefault('completed', 0);
            $mform->disabledIf('completed', 'dependency', 'eq', 0);

            $mform->addElement('text', 'gradebetterthan', get_string('gradebetterthan', 'lesson'));
            $mform->setDefault('gradebetterthan', 0);
            $mform->setType('gradebetterthan', PARAM_INT);
            $mform->disabledIf('gradebetterthan', 'dependency', 'eq', 0);
        } else {
            $mform->addElement('hidden', 'dependency', 0);
            $mform->setType('dependency', PARAM_INT);
            $mform->addElement('hidden', 'timespent', 0);
            $mform->setType('timespent', PARAM_INT);
            $mform->addElement('hidden', 'completed', 0);
            $mform->setType('completed', PARAM_INT);
            $mform->addElement('hidden', 'gradebetterthan', 0);
            $mform->setType('gradebetterthan', PARAM_INT);
            $mform->setConstants(array('dependency' => 0, 'timespent' => 0,
                    'completed' => 0, 'gradebetterthan' => 0));
        }

        // Flow control.
        $mform->addElement('header', 'flowcontrol', get_string('flowcontrol', 'lesson'));

        $mform->addElement('selectyesno', 'modattempts', get_string('modattempts', 'lesson'));
        $mform->addHelpButton('modattempts', 'modattempts', 'lesson');
        $mform->setDefault('modattempts', 0);

        $mform->addElement('selectyesno', 'review', get_string('displayreview', 'lesson'));
        $mform->addHelpButton('review', 'displayreview', 'lesson');
        $mform->setDefault('review', 0);

        $numbers = array();
        for ($i = 10; $i > 0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'maxattempts', get_string('maximumnumberofattempts', 'lesson'), $numbers);
        $mform->addHelpButton('maxattempts', 'maximumnumberofattempts', 'lesson');
        $mform->setDefault('maxattempts', 1);

        $defaultnextpages = array();
        $defaultnextpages[0] = get_string('normal', 'lesson');
        $defaultnextpages[LESSON_UNSEENPAGE] = get_string('showanunseenpage', 'lesson');
        $defaultnextpages[LESSON_UNANSWEREDPAGE] = get_string('showanunansweredpage', 'lesson');
        $mform->addElement('select', 'nextpagedefault', get_string('actionaftercorrectanswer', 'lesson'), $defaultnextpages);
        $mform->addHelpButton('nextpagedefault', 'actionaftercorrectanswer', 'lesson');
        $mform->setDefault('nextpagedefault', $CFG->lesson_defaultnextpage);

        $numbers = array();
        for ($i = 100; $i >= 0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'maxpages', get_string('numberofpagestoshow', 'lesson'), $numbers);
        $mform->addHelpButton('maxpages', 'numberofpagestoshow', 'lesson');
        $mform->setDefault('maxpages', 0);

        // Grade.
        $this->standard_grading_coursemodule_elements();

        // No header here, so that the following settings are displayed in the grade section.

        $mform->addElement('selectyesno', 'practice', get_string('practice', 'lesson'));
        $mform->addHelpButton('practice', 'practice', 'lesson');
        $mform->setDefault('practice', 0);

        $mform->addElement('selectyesno', 'custom', get_string('customscoring', 'lesson'));
        $mform->addHelpButton('custom', 'customscoring', 'lesson');
        $mform->setDefault('custom', 1);

        $mform->addElement('selectyesno', 'retake', get_string('retakesallowed', 'lesson'));
        $mform->addHelpButton('retake', 'retakesallowed', 'lesson');
        $mform->setDefault('retake', 0);

        $options = array();
        $options[0] = get_string('usemean', 'lesson');
        $options[1] = get_string('usemaximum', 'lesson');
        $mform->addElement('select', 'usemaxgrade', get_string('handlingofretakes', 'lesson'), $options);
        $mform->addHelpButton('usemaxgrade', 'handlingofretakes', 'lesson');
        $mform->setDefault('usemaxgrade', 0);
        $mform->disabledIf('usemaxgrade', 'retake', 'eq', '0');

        $numbers = array();
        for ($i = 100; $i >= 0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'minquestions', get_string('minimumnumberofquestions', 'lesson'), $numbers);
        $mform->addHelpButton('minquestions', 'minimumnumberofquestions', 'lesson');
        $mform->setDefault('minquestions', 0);

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();
    }

    /**
     * Enforce defaults here
     *
     * @param array $defaultvalues Form defaults
     * @return void
     **/
    public function data_preprocessing(&$defaultvalues) {
        if (isset($defaultvalues['conditions'])) {
            $conditions = unserialize($defaultvalues['conditions']);
            $defaultvalues['timespent'] = $conditions->timespent;
            $defaultvalues['completed'] = $conditions->completed;
            $defaultvalues['gradebetterthan'] = $conditions->gradebetterthan;
        }

        // Set up the completion checkbox which is not part of standard data.
        $defaultvalues['completiontimespentenabled'] =
            !empty($defaultvalues['completiontimespent']) ? 1 : 0;

        if ($this->current->instance) {
            // Editing existing instance - copy existing files into draft area.
            $draftitemid = file_get_submitted_draft_itemid('mediafile');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_lesson', 'mediafile', 0, array('subdirs'=>0, 'maxbytes' => $this->course->maxbytes, 'maxfiles' => 1));
            $defaultvalues['mediafile'] = $draftitemid;
        }
    }

    /**
     * Enforce validation rules here
     *
     * @param object $data Post data to validate
     * @return array
     **/
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if ($data['available'] != 0 && $data['deadline'] != 0 &&
                $data['deadline'] < $data['available']) {
            $errors['deadline'] = get_string('closebeforeopen', 'lesson');
        }

        if (!empty($data['usepassword']) && empty($data['password'])) {
            $errors['password'] = get_string('emptypassword', 'lesson');
        }

        return $errors;
    }

    /**
     * Display module-specific activity completion rules.
     * Part of the API defined by moodleform_mod
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $mform->addElement('checkbox', 'completionendreached', get_string('completionendreached', 'lesson'),
                get_string('completionendreached_desc', 'lesson'));

        $group = array();
        $group[] =& $mform->createElement('checkbox', 'completiontimespentenabled', '',
                get_string('completiontimespent', 'lesson'));
        $group[] =& $mform->createElement('duration', 'completiontimespent', array('optional' => true));
        $mform->addGroup($group, 'completiontimespentgroup', get_string('completiontimespentgroup', 'lesson'), array(' '), false);
        $mform->disabledIf('completiontimespent[number]', 'completiontimespentenabled', 'notchecked');
        $mform->disabledIf('completiontimespent[timeunit]', 'completiontimespentenabled', 'notchecked');

        return array('completionendreached', 'completiontimespentgroup');
    }

    /**
     * Called during validation. Indicates whether a module-specific completion rule is selected.
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionendreached']) || $data['completiontimespent'] > 0;
    }

    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Turn off completion setting if the checkbox is not ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completiontimespentenabled) || !$autocompletion) {
                $data->completiontimespent = 0;
            }
        }
        return $data;
    }
}

