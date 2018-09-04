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
 * The main hotpot configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * mod_hotpot_mod_form
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_mod_form extends moodleform_mod {

    /**
     * Detects if we are adding a new HotPot activity
     * as opposed to updating an existing one
     *
     * Note: we could use any of the following to detect add:
     *   - empty($this->_instance | _cm)
     *   - empty($this->current->add | id | coursemodule | instance)
     *
     * @return bool True if we are adding an new activity instance, false otherwise
     */
    public function is_add() {
        if (empty($this->current->instance)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Detects if we are updating a new HotPot activity
     * as opposed to adding an new one
     *
     * @return bool True if we are adding an new activity instance, false otherwise
     */
    public function is_update() {
        if (empty($this->current->instance)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Detects if the current activity instance has a grade item in the Moodle gradebook
     *
     * Note: could make this general purpose, if we use $this->_cm->modname for 'itemmodule'
     *
     * @return bool True if the current activity has a grade item, false otherwise
     */
    protected function has_grade_item() {
        global $DB;
        if ($this->is_add()) {
            return false;
        } else {
            $params = array('itemtype' => 'mod',
                            'itemmodule' => 'hotpot',
                            'iteminstance' => $this->current->instance);
            return $DB->record_exists('grade_items', $params);
        }
    }

    /**
     * return a field value from the original record
     * this function is useful to see if a value has changed
     *
     * @return bool the field value if it exists, false otherwise
     */
    public function get_original_value($fieldname, $default) {
        if (isset($this->current) && isset($this->current->$fieldname)) {
            return $this->current->$fieldname;
        } else {
            return $default;
        }
    }

    /**
     * Defines the hotpot instance configuration form
     *
     * @return void
     */
    function definition() {
        global $CFG, $PAGE;

        $hotpotconfig = get_config('hotpot');
        $mform = $this->_form;

        $plugin = 'mod_hotpot';

        $textoptions = array('size' => '40');

        // General --------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        //-----------------------------------------------------------------------------

        // Hotpot name
        $name = 'name';
        $label = get_string('name');
        if ($this->is_add()) {
            $elements = array(
                $mform->createElement('select', 'namesource', '', hotpot::available_namesources_list()),
                $mform->createElement('text', $name, '', $textoptions)
            );
            $mform->addGroup($elements, $name.'_elements', $label, array(' '), false);
            $mform->disabledIf($name.'_elements', 'namesource', 'ne', hotpot::TEXTSOURCE_SPECIFIC);
            $mform->setDefault('namesource', get_user_preferences('hotpot_namesource', hotpot::TEXTSOURCE_FILE));
            $mform->addHelpButton($name.'_elements', 'nameadd', $plugin);
        } else {
            $mform->addElement('text', $name, $label, $textoptions);
            $mform->addElement('hidden', 'namesource', hotpot::TEXTSOURCE_SPECIFIC);
            $mform->addHelpButton($name, 'nameedit', $plugin);
            $mform->addRule($name, null, 'required', null, 'client');
            $mform->addRule($name, get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        }
        $mform->setType('namesource', PARAM_INT);
        if (empty($CFG->formatstringstriptags)) {
            $mform->setType($name, PARAM_CLEAN);
        } else {
            $mform->setType($name, PARAM_TEXT);
        }

        // Reference
        // $mform->addElement('filepicker', 'sourceitemid', get_string('sourcefile', $plugin));
        // $mform->addRule('sourceitemid', null, 'required', null, 'client');
        // $mform->addHelpButton('sourceitemid', 'sourcefile', $plugin);
        $options = array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => -1, 'mainfile' => true, 'accepted_types' => '*');
        $mform->addElement('filemanager', 'sourceitemid', get_string('sourcefile', $plugin), null, $options);
        $mform->addRule('sourceitemid', null, 'required', null, 'client');
        $mform->addHelpButton('sourceitemid', 'sourcefile', $plugin);

        // legacy field from Moodle 1.9 - it will probably be removed someday
        $mform->addElement('hidden', 'sourcelocation', isset($this->current->sourcelocation) ? $this->current->sourcelocation : 0);
        $mform->setType('sourcelocation', PARAM_INT);

        // Add quiz chain (this setting is not implemented in Moodle 2.0)
        $mform->addElement('hidden', 'quizchain', 0);
        $mform->setType('quizchain', PARAM_INT);
        //if ($this->is_add()) {
        //    $mform->addElement('selectyesno', 'quizchain', get_string('addquizchain', $plugin));
        //    $mform->setDefault('quizchain', get_user_preferences('hotpot_add_quizchain', 0));
        //    $mform->addHelpButton('quizchain', 'addquizchain', $plugin);
        //    $mform->setAdvanced('quizchain');
        //} else {
        //    $mform->addElement('hidden', 'quizchain', 0);
        //}

        // Entry page -----------------------------------------------------------------
        $mform->addElement('header', 'entrypagehdr', get_string('entrypagehdr', $plugin));
        //-----------------------------------------------------------------------------

        // Entry page text editor
        $this->add_hotpot_text_editor('entry');

        // Entry page options
        $name = 'entryoptions';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('checkbox', 'entry_title', '', get_string('title', $plugin)),
            $mform->createElement('checkbox', 'entry_grading', '', get_string('entry_grading', $plugin)),
            $mform->createElement('checkbox', 'entry_dates', '', get_string('entry_dates', $plugin)),
            $mform->createElement('checkbox', 'entry_attempts', '', get_string('entry_attempts', $plugin))
        );
        $mform->addGroup($elements, $name.'_elements', $label, html_writer::empty_tag('br'), false);
        $mform->setAdvanced($name.'_elements');
        $mform->addHelpButton($name.'_elements', 'entryoptions', $plugin);
        $mform->disabledIf($name.'_elements', 'entrypage', 'ne', 1);

        // Exit page ------------------------------------------------------------------
        $mform->addElement('header', 'exitpagehdr', get_string('exitpagehdr', $plugin));
        //-----------------------------------------------------------------------------

        // Exit page text editor
        $this->add_hotpot_text_editor('exit');

        // Exit page options (feedback)
        $name = 'exit_feedback';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('checkbox', 'exit_title', '', get_string('title', $plugin)),
            $mform->createElement('checkbox', 'exit_encouragement', '', get_string('exit_encouragement', $plugin)),
            $mform->createElement('checkbox', 'exit_attemptscore', '', get_string('exit_attemptscore', $plugin, '...')),
            $mform->createElement('checkbox', 'exit_hotpotgrade', '', get_string('exit_hotpotgrade', $plugin, '...'))
        );
        $mform->addGroup($elements, $name, $label, html_writer::empty_tag('br'), false);
        $mform->setAdvanced($name);
        $mform->disabledIf($name, 'exitpage', 'ne', 1);
        $mform->addHelpButton($name, $name, $plugin);

        // Exit page options (links)
        $name = 'exit_links';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('checkbox', 'exit_retry', '', get_string('exit_retry', $plugin).': '.get_string('exit_retry_text', $plugin)),
            $mform->createElement('checkbox', 'exit_index', '', get_string('exit_index', $plugin).': '.get_string('exit_index_text', $plugin)),
            $mform->createElement('checkbox', 'exit_course', '', get_string('exit_course', $plugin).': '.get_string('exit_course_text', $plugin)),
            $mform->createElement('checkbox', 'exit_grades', '', get_string('exit_grades', $plugin).': '.get_string('exit_grades_text', $plugin)),
        );
        $mform->addGroup($elements, $name, $label, html_writer::empty_tag('br'), false);
        $mform->setAdvanced($name);
        $mform->disabledIf($name, 'exitpage', 'ne', 1);
        $mform->addHelpButton($name, $name, $plugin);

        // Next activity
        $this->add_activity_list('exit');

        // Display --------------------------------------------------------------------
        $mform->addElement('header', 'displayhdr', get_string('display', 'form'));
        //-----------------------------------------------------------------------------

        // Output format
        if (empty($this->current->sourcetype)) {
            $sourcetype = ''; // add
        } else {
            $sourcetype = $this->current->sourcetype;
        }
        $name = 'outputformat';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_outputformats_list($sourcetype));
        $mform->setDefault($name, get_user_preferences('hotpot_outputformat', ''));
        $mform->addHelpButton($name, $name, $plugin);

        // Navigation
        $name = 'navigation';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_navigations_list());
        $mform->setDefault($name, get_user_preferences('hotpot_navigation', hotpot::NAVIGATION_MOODLE));
        $mform->addHelpButton($name, $name, $plugin);

        // Title
        $name = 'title';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_titles_list());
        $mform->setDefault($name, get_user_preferences('hotpot_title', hotpot::TEXTSOURCE_SPECIFIC));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Show stop button
        $name = 'stopbutton';
        $label = get_string($name, $plugin);
        $options = array(
            'hotpot_giveup' => get_string('giveup', $plugin),
            'specific' => get_string('stopbutton_specific', $plugin)
        );
        $elements = array(
            $mform->createElement('selectyesno', 'stopbutton_yesno', ''),
            $mform->createElement('select', 'stopbutton_type', '', $options),
            $mform->createElement('text', 'stopbutton_text', '', array('size' => '20'))
        );
        $mform->addGroup($elements, $name.'_elements', $label, ' ', false);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');

        $mform->setType('stopbutton_yesno', PARAM_INT);
        $mform->setType('stopbutton_type', PARAM_ALPHAEXT);
        $mform->setType('stopbutton_text', PARAM_TEXT);

        $mform->disabledIf($name.'_elements', 'stopbutton_yesno', 'ne', '1');
        $mform->disabledIf('stopbutton_text', 'stopbutton_type', 'ne', 'specific');

        // Allow paste
        $name = 'allowpaste';
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $name, $label);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, get_user_preferences('hotpot_quiz_allowpaste', 1));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Use filters
        $name = 'usefilters';
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $name, $label);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, get_user_preferences('hotpot_quiz_usefilters', 1));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Use glossary
        $name = 'useglossary';
        $label = get_string($name, $plugin);
        $mform->addElement('selectyesno', $name, $label);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, get_user_preferences('hotpot_quiz_useglossary', 1));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Use media filters
        $name = 'usemediafilter';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_mediafilters_list());
        $mform->setType($name, PARAM_SAFEDIR); // [a-zA-Z0-9_-]
        $mform->setDefault($name, get_user_preferences('hotpot_quiz_usemediafilter', 'moodle'));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Student feedback
        $name = 'studentfeedback';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('select', $name, '', hotpot::available_feedbacks_list()),
            $mform->createElement('text', 'studentfeedbackurl', '', $textoptions)
        );
        $mform->addGroup($elements, $name.'_elements', $label, array(' '), false);
        $mform->disabledIf($name.'_elements', $name, 'eq', hotpot::FEEDBACK_NONE);
        $mform->disabledIf($name.'_elements', $name, 'eq', hotpot::FEEDBACK_MOODLEFORUM);
        $mform->disabledIf($name.'_elements', $name, 'eq', hotpot::FEEDBACK_MOODLEMESSAGING);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');
        $mform->setType($name, PARAM_INT);
        $mform->setType('studentfeedbackurl', PARAM_URL);

        // Access control -------------------------------------------------------------
        $mform->addElement('header', 'accesscontrolhdr', get_string('accesscontrol', 'lesson'));
        //-----------------------------------------------------------------------------

        // Previous activity
        $this->add_activity_list('entry');

        // Open time
        $name = 'timeopen';
        $label = get_string($name, $plugin);
        $mform->addElement('date_time_selector', $name, $label, array('optional' => true));
        $mform->addHelpButton($name, 'timeopenclose', $plugin);
        $mform->setAdvanced($name);

        // Close time
        $name = 'timeclose';
        $label = get_string($name, $plugin);
        $mform->addElement('date_time_selector', $name, $label, array('optional' => true));
        $mform->addHelpButton($name, 'timeopenclose', $plugin);
        $mform->setAdvanced($name);

        // Time limit
        $name = 'timelimit';
        $label = get_string($name, $plugin);
        $options = array(
            hotpot::TIME_SPECIFIC => get_string('timelimitspecific', $plugin),
            hotpot::TIME_TEMPLATE => get_string('timelimittemplate', $plugin),
            hotpot::TIME_DISABLE  => get_string('disable')
        );
        $elements = array(
            $mform->createElement('static', '', '', get_string('timelimitsummary', $plugin)),
            $mform->createElement('static', '', '', html_writer::empty_tag('br')),
            $mform->createElement('select', $name, '', $options),
            $mform->createElement('duration', $name.'specific', '', array('optional'=>0, 'defaultunit'=>1))
        );
        $mform->addGroup($elements, $name.'_elements', $label, '', false);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');
        $mform->setType($name, PARAM_INT);
        $mform->disabledIf($name.'specific[number]', $name, 'ne', hotpot::TIME_SPECIFIC);
        $mform->disabledIf($name.'specific[timeunit]', $name, 'ne', hotpot::TIME_SPECIFIC);

        // Delay 1
        $name = 'delay1';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('static', '', '', get_string('delay1summary', $plugin)),
            $mform->createElement('static', '', '', html_writer::empty_tag('br')),
            $mform->createElement('duration', $name, '', array('optional'=>1, 'defaultunit'=>1))
        );
        $mform->addGroup($elements, $name.'_elements', $label, '', false);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');
        // the standard disabledIf for the "enable" checkbox doesn't work because we are in group, so ...
        $mform->disabledIf($name.'[number]', $name.'[enabled]', 'notchecked', '');
        $mform->disabledIf($name.'[timeunit]', $name.'[enabled]', 'notchecked', '');

        // Delay 2
        $name = 'delay2';
        $label = get_string($name, $plugin);
        $elements = array(
            $mform->createElement('static', '', '', get_string('delay2summary', $plugin)),
            $mform->createElement('static', '', '', html_writer::empty_tag('br')),
            $mform->createElement('duration', $name, '', array('optional'=>1, 'defaultunit'=>1))
        );
        $mform->addGroup($elements, $name.'_elements', $label, '', false);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');
        // the standard disabledIf for the "enable" checkbox doesn't work because we are in group, so ...
        $mform->disabledIf($name.'[number]', $name.'[enabled]', 'notchecked', '');
        $mform->disabledIf($name.'[timeunit]', $name.'[enabled]', 'notchecked', '');

        // Delay 3
        $name = 'delay3';
        $label = get_string($name, $plugin);
        $options = array(
            hotpot::TIME_SPECIFIC => get_string('delay3specific', $plugin),
            hotpot::TIME_TEMPLATE => get_string('delay3template', $plugin),
            hotpot::TIME_AFTEROK  => get_string('delay3afterok', $plugin),
            hotpot::TIME_DISABLE  => get_string('delay3disable', $plugin)
        );
        $elements = array(
            $mform->createElement('static', '', '', get_string('delay3summary', $plugin)),
            $mform->createElement('static', '', '', html_writer::empty_tag('br')),
            $mform->createElement('select', $name, '', $options),
            $mform->createElement('duration', 'delay3specific', '', array('optional'=>0, 'defaultunit'=>1))
        );
        $mform->addGroup($elements, $name.'_elements', $label, '', false);
        $mform->addHelpButton($name.'_elements', $name, $plugin);
        $mform->setAdvanced($name.'_elements');
        $mform->setType($name, PARAM_INT);
        $mform->disabledIf($name.'specific[number]', $name, 'ne', hotpot::TIME_SPECIFIC);
        $mform->disabledIf($name.'specific[timeunit]', $name, 'ne', hotpot::TIME_SPECIFIC);

        // Allow review?
        //$mform->addElement('selectyesno', 'allowreview', get_string('allowreview', $plugin));
        //$mform->setDefault('allowreview', get_user_preferences('hotpot_review', 1));
        //$mform->addHelpButton('allowreview', 'allowreview', $plugin);
        //$mform->setAdvanced('allowreview');

        // Review options -------------------------------------------------------------
        $mform->addElement('header', 'reviewoptionshdr', get_string('reviewoptions', $plugin));
        //-----------------------------------------------------------------------------

        list($times, $items) = hotpot::reviewoptions_times_items();
        foreach ($times as $timename => $timevalue) {
            $elements = array();
            foreach ($items as $itemname => $itemvalue) {
                $name = $timename.$itemname; // e.g. duringattemptresponses
                $elements[] = &$mform->createElement('checkbox', $name, '', get_string($itemname, 'mod_quiz'));
                $mform->setType($name, PARAM_INT);
            }

            // js_amd_inline is available in Moodle >= 3.3
            $js_amd_inline = method_exists($PAGE->requires, 'js_amd_inline');

            $text = '';
            $id = 'fgroup_id_'.$timename.'_elements';

            if ($js_amd_inline) {
                $text .= html_writer::tag('a', get_string('all'), array('href' => '#', 'id' => $name.'selectall'));
                $text .= ' / ';
                $text .= html_writer::tag('a', get_string('none'), array('href' => '#', 'id' => $name.'selectnone'));
            } else {
                $text .= html_writer::tag('a', get_string('all'), array('onclick' => 'select_all_in("DIV", "fitem", "'.$id.'")'));
                $text .= ' / ';
                $text .= html_writer::tag('a', get_string('none'), array('onclick' => 'deselect_all_in("DIV", "fitem", "'.$id.'")'));
            }
            $elements[] = &$mform->createElement('static', '', '', html_writer::tag('span', $text));

            $mform->addGroup($elements, $timename.'_elements', get_string('review'.$timename, $plugin), null, false);
            if ($timename=='afterclose') {
                $mform->disabledIf('afterclose_elements', 'timeclose[off]', 'checked');
            }

            if ($js_amd_inline) {
                $PAGE->requires->js_amd_inline("
                require(['jquery'], function($) {
                    $('#{$name}selectall').click(function(e) {
                        $('#{$id}').find('input:checkbox').prop('checked', true);
                        e.preventDefault();
                    });
                    $('#{$name}selectnone').click(function(e) {
                        $('#{$id}').find('input:checkbox').prop('checked', false);
                        e.preventDefault();
                    });
                });");
            }
        }

        // Security -------------------------------------------------------------------
        $mform->addElement('header', 'securityhdr', get_string('extraattemptrestrictions', 'mod_quiz'));
        //-----------------------------------------------------------------------------

        // Maximum number of attempts
        $name = 'attemptlimit';
        $label = get_string('attemptsallowed', 'mod_quiz');
        $mform->addElement('select', $name, $label, hotpot::available_attemptlimits_list());
        $mform->setDefault($name, get_user_preferences('hotpot_attempts', 0)); // 0=unlimited
        $mform->setAdvanced($name);
        $mform->addHelpButton($name, $name, $plugin);

        // Password
        $name = 'password';
        $label = get_string('requirepassword', 'mod_quiz');
        $mform->addElement('text', $name, $label);
        $mform->setType($name, PARAM_TEXT);
        $mform->addHelpButton($name, 'requirepassword', 'mod_quiz');
        $mform->setAdvanced($name);

        // IP address.
        $name = 'subnet';
        $label = get_string('requiresubnet', 'mod_quiz');
        $mform->addElement('text', $name, $label);
        $mform->setType($name, PARAM_TEXT);
        $mform->addHelpButton($name, 'requiresubnet', 'mod_quiz');
        $mform->setAdvanced($name);

        // Grades ---------------------------------------------------------------------
        $mform->addElement('header', 'gradeshdr', get_string('grades', 'grades'));
        //-----------------------------------------------------------------------------

        // Grading method
        $name = 'grademethod';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_grademethods_list());
        $mform->setDefault($name, get_user_preferences('hotpot_grademethod', hotpot::GRADEMETHOD_HIGHEST));
        $mform->addHelpButton($name, $name, $plugin);
        // $mform->setAdvanced($name);

        // Grade weighting
        $name = 'gradeweighting';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, hotpot::available_gradeweightings_list());
        $mform->setDefault($name, get_user_preferences('hotpot_gradeweighting', 100));
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setAdvanced($name);

        // Note: the min pass grade field must be called "gradepass" so that
        // it is recognized by edit_module_post_actions() in "course/modlib.php"
        // Also, FEATURE_GRADE_HAS_GRADE must be enabled in "mod/hotpot/lib.php"
        $name = 'gradepass';
        $label = get_string($name, 'grades');
        $mform->addElement('text', $name, $label, array('size' => '10'));
        $mform->addHelpButton($name, $name, 'grades');
        $mform->setType($name, PARAM_INT);
        $mform->setAdvanced($name);
        $mform->disabledIf($name, 'gradeweighting', 'eq', '0');

        // Note: the grade category field must be called "gradecat" so that
        // it is recognized by edit_module_post_actions() in "course/modlib.php"
        // Also, FEATURE_GRADE_HAS_GRADE must be enabled in "mod/hotpot/lib.php"
        $name = 'gradecat';
        $label = get_string('gradecategoryonmodform', 'grades');
        $options = grade_get_categories_menu($PAGE->course->id);
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, 'gradecategoryonmodform', 'grades');
        $mform->setType($name, PARAM_INT);
        $mform->setAdvanced($name);
        $mform->disabledIf($name, 'gradeweighting', 'eq', '0');

        // Standard settings (groups etc), common to all modules ----------------------
        $this->standard_coursemodule_elements();

        // Standard buttons, common to all modules ------------------------------------
        $this->add_action_buttons();
    }

    /**
     * add_hotpot_text_editor
     *
     * @param xxx $type
     */
    function add_hotpot_text_editor($type)  {

        $mform = $this->_form;
        $plugin = 'mod_hotpot';

        if ($this->is_add()) {
            $options = array(
                hotpot::TEXTSOURCE_FILE => get_string('textsourcefile', $plugin),
                hotpot::TEXTSOURCE_SPECIFIC => get_string('textsourcespecific', $plugin)
            );
            $elements = array(
                $mform->createElement('selectyesno', $type.'page'),
                $mform->createElement('select', $type.'textsource', '', $options)
            );
            $mform->addGroup($elements, $type.'page_elements', get_string($type.'page', $plugin), array(' '), false);
            $mform->setDefault($type.'page', get_user_preferences('hotpot_'.$type.'page', 0));
            $mform->setAdvanced($type.'page_elements');
            $mform->addHelpButton($type.'page_elements', $type.'page', $plugin);
            $mform->disabledIf($type.'page_elements', $type.'page', 'ne', 1);
        } else {
            $mform->addElement('selectyesno', $type.'page', get_string($type.'page', $plugin));
            $mform->setType($type.'page', PARAM_INT);
            $mform->addHelpButton($type.'page', $type.'page', $plugin);
            $mform->addElement('hidden', $type.'textsource', hotpot::TEXTSOURCE_SPECIFIC);
        }
        $mform->setType($type.'page', PARAM_INT);
        $mform->setType($type.'textsource', PARAM_INT);

        $options = hotpot::text_editors_options($this->context);
        $mform->addElement('editor', $type.'editor', get_string($type.'text', $plugin), null, $options);
        $mform->setType($type.'editor', PARAM_RAW); // no XSS prevention here, users must be trusted
        $mform->setAdvanced($type.'editor');

        $mform->disabledIf($type.'editor[text]', $type.'page', 'ne', 1);
        $mform->disabledIf($type.'editor[format]', $type.'page', 'ne', 1);

        if ($this->is_add()) {
            $mform->disabledIf($type.'editor[text]', $type.'textsource', 'ne', hotpot::TEXTSOURCE_SPECIFIC);
            $mform->disabledIf($type.'editor[format]', $type.'textsource', 'ne', hotpot::TEXTSOURCE_SPECIFIC);
        }
    }

    /**
     * add_activity_list
     *
     * @param xxx $type
     */
    function add_activity_list($type)  {
        global $PAGE;
        $plugin = 'mod_hotpot';

        // if activity name is longer than $namelength, it will be truncated
        // to first $headlength chars + " ... " + last $taillength chars
        $namelength = 40;
        $headlength = 16;
        $taillength = 16;

        $mform = $this->_form;

        $optgroups = array(
            get_string('none') => array(
                hotpot::ACTIVITY_NONE => get_string('none')
            ),
            get_string($type=='entry' ? 'previous' : 'next') => array(
                hotpot::ACTIVITY_COURSE_ANY     => get_string($type.'cmcourse', $plugin),
                hotpot::ACTIVITY_SECTION_ANY    => get_string($type.'cmsection', $plugin),
                hotpot::ACTIVITY_COURSE_HOTPOT  => get_string($type.'hotpotcourse', $plugin),
                hotpot::ACTIVITY_SECTION_HOTPOT => get_string($type.'hotpotsection', $plugin)
            )
        );

        if ($modinfo = get_fast_modinfo($PAGE->course)) {

            switch ($PAGE->course->format) {
                case 'weeks': $strsection = get_string('strftimedateshort'); break;
                case 'topics': $strsection = get_string('topic'); break;
                default: $strsection = get_string('section');
            }
            $sectionnum = -1;
            foreach ($modinfo->cms as $cmid=>$mod) {
                if ($mod->modname=='label') {
                    continue; // ignore labels
                }
                if ($type=='entry' && $mod->modname=='resource') {
                    continue; // ignore resources as entry activities
                }
                if (isset($form->update) && $form->update==$cmid) {
                    continue; // ignore this hotpot
                }
                if ($sectionnum==$mod->sectionnum) {
                    // do nothing (same section)
                } else {
                    // start new optgroup for this course section
                    $sectionnum = $mod->sectionnum;
                    if ($sectionnum==0) {
                        $optgroup = get_string('activities');
                    } else if ($PAGE->course->format=='weeks') {
                        $date = $PAGE->course->startdate + 7200 + ($sectionnum * 604800);
                        $optgroup = userdate($date, $strsection).' - '.userdate($date + 518400, $strsection);
                    } else {
                        $optgroup = $strsection.': '.$sectionnum;
                    }
                    if (empty($options[$optgroup])) {
                        $options[$optgroup] = array();
                    }
                }

                $name = format_string($mod->name);
                $strlen = hotpot_textlib('strlen', $name);
                if ($strlen > $namelength) {
                    $head = hotpot_textlib('substr', $name, 0, $headlength);
                    $tail = hotpot_textlib('substr', $name, $strlen - $taillength, $taillength);
                    $name = $head.' ... '.$tail;
                }
                $optgroups[$optgroup][$cmid] = $name;
            }
        }

        $options = array();
        for ($i=100; $i>=0; $i--) {
            $options[$i] = $i.'%';
        }
        $elements = array(
            $mform->createElement('selectgroups', $type.'cm', '', $optgroups),
            $mform->createElement('select', $type.'grade', '', $options)
        );
        $mform->addGroup($elements, $type.'cm_elements', get_string($type.'cm', $plugin), array(' '), false);
        $mform->addHelpButton($type.'cm_elements', $type.'cm', $plugin);
        if ($type=='entry') {
            $defaultcm = hotpot::ACTIVITY_NONE;
            $defaultgrade = 100;
        } else {
            $defaultcm = hotpot::ACTIVITY_SECTION_HOTPOT;
            $defaultgrade = 0;
        }
        $mform->setDefault($type.'cm', get_user_preferences('hotpot_'.$type.'cm', $defaultcm));
        $mform->setDefault($type.'grade', get_user_preferences('hotpot_'.$type.'grade', $defaultgrade));
        $mform->disabledIf($type.'cm_elements', $type.'cm', 'eq', 0);
        if ($type=='entry') {
            $mform->setAdvanced($type.'cm_elements');
        }

        // add module icons, if possible
        if ($modinfo) {
            // in some browsers (e.g. Chrome) the icons only display
            // when the "size" attribute is set for the SELECT tag
            // but this also means that you MUST scroll to see all
            // item in the SELECT list, so perhaps the code below
            // could be removed.
            $element = reset($mform->getElement($type.'cm_elements')->getElements());
            if (method_exists($PAGE->theme, 'image_url')) {
                $image_url = 'image_url'; // Moodle >= 3.3
            } else {
                $image_url = 'pix_url'; // Moodle <= 3.2
            }
            for ($i=0; $i<count($element->_optGroups); $i++) {
                $optgroup = &$element->_optGroups[$i];
                for ($ii=0; $ii<count($optgroup['options']); $ii++) {
                    $option = &$optgroup['options'][$ii];
                    if (isset($option['attr']['value']) && $option['attr']['value']>0) {
                        $cmid = $option['attr']['value'];
                        $modname = $modinfo->cms[$cmid]->modname;
                        $url = $PAGE->theme->$image_url('icon', $modname)->out();
                        $option['attr']['style'] = 'background-image: url('.$url.'); '.
                                                   'background-repeat: no-repeat; '.
                                                   'background-position: 1px 2px; '.
                                                   'min-height: 20px; '.
                                                   'padding-left: 12px;';
                    }
                }
            }
        }
    }

    /**
     * Prepares the form before data are set
     *
     * Additional wysiwyg editors are prepared here
     * along with the stopbutton switch, type and text
     *
     * @param array $data to be set
     * @return void
     */
    function data_preprocessing(&$data) {
        $plugin = 'mod_hotpot';

        // Note: if you call "file_prepare_draft_area()" without setting itemid
        // (the first argument), then it will be assigned automatically, and the files
        // for this context will be transferred automatically, which is what we want
        $data['sourceitemid'] = 0;
        if ($this->is_add()) {
            $contextid = null;
        } else {
            $contextid = $this->context->id;
        }
        $options = hotpot::sourcefile_options(); // array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => -1);
        file_prepare_draft_area($data['sourceitemid'], $contextid, $plugin, 'sourcefile', 0, $options);

        if ($this->is_add()) {
            // set fields from user preferences, where possible
            foreach (hotpot::user_preferences_fieldnames() as $fieldname) {
                if (! isset($data[$fieldname])) {
                    $data[$fieldname] = get_user_preferences('hotpot_'.$fieldname, '');
                }
            }
        }

        // set entry/exit page settings
        foreach (hotpot::text_page_types() as $type) {

            // extract boolean switches for page options
            foreach (hotpot::text_page_options($type) as $name => $mask) {
                if (array_key_exists($type.'options', $data)) {
                    $data[$type.'_'.$name] = $data[$type.'options'] & $mask;
                } else {
                    $data[$type.'_'.$name] = 0;
                }
            }

            // setup custom wysiwyg editor
            $draftitemid = 0;
            if ($this->is_add()) {
                // adding a new hotpot instance
                $data[$type.'editor'] = array(
                    'text'   => file_prepare_draft_area($draftitemid, $contextid, $plugin, $type, 0),
                    'format' => editors_get_preferred_format(),
                    'itemid' => file_get_submitted_draft_itemid($type)
                );
            } else {
                // editing an existing hotpot
                $data[$type.'editor'] = array(
                    'text'   => file_prepare_draft_area($draftitemid, $contextid, $plugin, $type, 0, $options, $data[$type.'text']),
                    'format' => $data[$type.'format'],
                    'itemid' => file_get_submitted_draft_itemid($type)
                );
            }
        }

        // timelimit
        if ($data['timelimit']>0) {
            $data['timelimitspecific'] = $data['timelimit'];
            $data['timelimit'] = hotpot::TIME_SPECIFIC;
        } else {
            $data['timelimitspecific'] = 0;
        }

        // delay3
        if ($data['delay3']>0) {
            $data['delay3specific'] = $data['delay3'];
            $data['delay3'] = hotpot::TIME_SPECIFIC;
        } else {
            $data['delay3specific'] = 0;
        }

        // set stopbutton options
        switch ($data['stopbutton']) {
            case hotpot::STOPBUTTON_SPECIFIC:
                $data['stopbutton_yesno'] = 1;
                $data['stopbutton_type'] = 'specific';
                $data['stopbutton_text'] = $data['stoptext'];
                break;
            case hotpot::STOPBUTTON_LANGPACK:
                $data['stopbutton_yesno'] = 1;
                $data['stopbutton_type'] = $data['stoptext'];
                $data['stopbutton_text'] = '';
                break;
            case hotpot::STOPBUTTON_NONE:
            default:
                $data['stopbutton_yesno'] = 0;
                $data['stopbutton_type'] = '';
                $data['stopbutton_text'] = '';
        }

        // set review options
        if (empty($data['reviewoptions'])) {
            $default = 0;
        } else {
            $default = $data['reviewoptions'];
        }
        list($times, $items) = hotpot::reviewoptions_times_items();
        foreach ($times as $timename => $timevalue) {
            foreach ($items as $itemname => $itemvalue) {
                $data[$timename.$itemname] = min(1, $default & $timevalue & $itemvalue);
            }
        }
    }

    /**
     * validation
     *
     * @param xxx $data
     * @return xxx
     */
    function validation($data, $files)  {
        global $USER;

        // http://docs.moodle.org/en/Development:lib/formslib.php_Validation
        // Note: see "lang/en/error.php" for a list of common messages
        $errors = array();

        // get the $files specified in the form
        $usercontext = hotpot_get_context(CONTEXT_USER, $USER->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['sourceitemid'], 'sortorder, id', 0); // files only, no dirs

        // check we have at least one file
        // (and set mainfile marker, if necessary)
        if (empty($files)) {
            $errors['sourceitemid'] = get_string('required');
            // $errors['sourceitemid'] = get_string('nofile', 'error');
        } else {
            $mainfile = false;
            foreach ($files as $file) {
                if ($file->get_sortorder()==1) {
                    $mainfile = true;
                    break;
                }
            }
            if (! $mainfile) {
                $file = reset($files); // first file in the list
                file_set_sortorder($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), 1);
            }
        }

        // studentfeedbackurl
        if ($data['studentfeedback']==hotpot::FEEDBACK_WEBPAGE || $data['studentfeedback']==hotpot::FEEDBACK_FORMMAIL) {
            if (empty($data['studentfeedbackurl']) || ! preg_match('/^https?:\/\/.+/', $data['studentfeedbackurl'])) {
                // empty or invalid url
                $errors['studentfeedback_elements']= get_string('invalidurl', 'error');
            }
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
        $plugin = 'mod_hotpot';

        // array of elements names to be returned by this method
        $names = array();

        // these fields will be disabled if gradelimit x gradeweighting = 0
        $disablednames = array('completionusegrade');

        // add "minimum grade" completion condition
        $name = 'completionmingrade';
        $label = get_string($name, $plugin);
        if (empty($this->current->$name)) {
            $value = 0.0;
        } else {
            $value = floatval($this->current->$name);
        }
        $group = array();
        $group[] = &$mform->createElement('checkbox', $name.'enabled', '', $label);
        $group[] = &$mform->createElement('static', $name.'space', '', ' &nbsp; ');
        $group[] = &$mform->createElement('text', $name, '', array('size' => 3));
        $mform->addGroup($group, $name.'group', '', '', false);
        $mform->setType($name, PARAM_FLOAT);
        $mform->setDefault($name, 0.00);
        $mform->setType($name.'enabled', PARAM_INT);
        $mform->setDefault($name.'enabled', empty($value) ? 0 : 1);
        $mform->disabledIf($name, $name.'enabled', 'notchecked');
        $names[] = $name.'group';
        $disablednames[] = $name.'group';

        // add "grade passed" completion condition
        $name = 'completionpass';
        $label = get_string($name, $plugin);
        $mform->addElement('checkbox', $name, '', $label);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, 0);
        $names[] = $name;
        $disablednames[] = $name;

        // add "status completed" completion condition
        $name = 'completioncompleted';
        $label = get_string($name, $plugin);
        $mform->addElement('checkbox', $name, '', $label);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, 0);
        $names[] = $name;
        // no need to disable this field :-)

        // disable grade conditions, if necessary
        foreach ($disablednames as $name) {
            if ($mform->elementExists($name)) {
                $mform->disabledIf($name, 'gradeweighting', 'eq', 0);
            }
        }

        return $names;
    }

    /**
     * Called during validation. Indicates whether a module-specific completion rule is selected.
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        if (empty($data['completionmingradeenabled']) || empty($data['completionmingrade'])) {
            if (empty($data['completionpass']) && empty($data['completioncompleted'])) {
                return false;
            }
        }
        return true; // at least one of the module-specific completion conditions is set
    }

    /**
     * Return submitted data if properly submitted
     * or returns NULL if there is no submitted data or validation fails.
     *
     * note: $slashed param removed
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data($slashed = true) {
        if ($data = parent::get_data($slashed)) {
            // Remove completionmingrade, if it is not enabled and greater than 0.0
            if (empty($data->completionmingradeenabled) || empty($data->completionmingrade) || floatval($data->completionmingrade)==0.0) {
                $data->completionmingradeenabled = 0;
                $data->completionmingrade = 0.0;
            }
        }
        return $data;
    }
}
