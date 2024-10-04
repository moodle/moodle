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
 * @package   mod_forum
 * @copyright Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

use core_grades\component_gradeitems;

class mod_forum_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $COURSE, $DB;

        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('forumname', 'forum'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 1333), 'maxlength', 1333, 'client');

        $this->standard_intro_elements(get_string('forumintro', 'forum'));

        $forumtypes = forum_get_forum_types();
        core_collator::asort($forumtypes, core_collator::SORT_STRING);
        $mform->addElement('select', 'type', get_string('forumtype', 'forum'), $forumtypes);
        $mform->addHelpButton('type', 'forumtype', 'forum');
        $mform->setDefault('type', 'general');

        $mform->addElement('header', 'availability', get_string('availability', 'forum'));

        $name = get_string('duedate', 'forum');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional' => true));
        $mform->addHelpButton('duedate', 'duedate', 'forum');

        $name = get_string('cutoffdate', 'forum');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional' => true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'forum');

        // Attachments and word count.
        $mform->addElement('header', 'attachmentswordcounthdr', get_string('attachmentswordcount', 'forum'));

        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes, 0, $CFG->forum_maxbytes);
        $choices[1] = get_string('uploadnotallowed');
        $mform->addElement('select', 'maxbytes', get_string('maxattachmentsize', 'forum'), $choices);
        $mform->addHelpButton('maxbytes', 'maxattachmentsize', 'forum');
        $mform->setDefault('maxbytes', $CFG->forum_maxbytes);

        $choices = array(
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 10,
            20 => 20,
            50 => 50,
            100 => 100
        );
        $mform->addElement('select', 'maxattachments', get_string('maxattachments', 'forum'), $choices);
        $mform->addHelpButton('maxattachments', 'maxattachments', 'forum');
        $mform->setDefault('maxattachments', $CFG->forum_maxattachments);

        $mform->addElement('selectyesno', 'displaywordcount', get_string('displaywordcount', 'forum'));
        $mform->addHelpButton('displaywordcount', 'displaywordcount', 'forum');
        $mform->setDefault('displaywordcount', 0);

        // Subscription and tracking.
        $mform->addElement('header', 'subscriptionandtrackinghdr', get_string('subscriptionandtracking', 'forum'));

        $options = forum_get_subscriptionmode_options();
        $mform->addElement('select', 'forcesubscribe', get_string('subscriptionmode', 'forum'), $options);
        $mform->addHelpButton('forcesubscribe', 'subscriptionmode', 'forum');
        if (isset($CFG->forum_subscription)) {
            $defaultforumsubscription = $CFG->forum_subscription;
        } else {
            $defaultforumsubscription = FORUM_CHOOSESUBSCRIBE;
        }
        $mform->setDefault('forcesubscribe', $defaultforumsubscription);

        $options = array();
        $options[FORUM_TRACKING_OPTIONAL] = get_string('trackingoptional', 'forum');
        $options[FORUM_TRACKING_OFF] = get_string('trackingoff', 'forum');
        if ($CFG->forum_allowforcedreadtracking) {
            $options[FORUM_TRACKING_FORCED] = get_string('trackingon', 'forum');
        }
        $mform->addElement('select', 'trackingtype', get_string('trackingtype', 'forum'), $options);
        $mform->addHelpButton('trackingtype', 'trackingtype', 'forum');
        $default = $CFG->forum_trackingtype;
        if ((!$CFG->forum_allowforcedreadtracking) && ($default == FORUM_TRACKING_FORCED)) {
            $default = FORUM_TRACKING_OPTIONAL;
        }
        $mform->setDefault('trackingtype', $default);

        if ($CFG->enablerssfeeds && isset($CFG->forum_enablerssfeeds) && $CFG->forum_enablerssfeeds) {
//-------------------------------------------------------------------------------
            $mform->addElement('header', 'rssheader', get_string('rss'));
            $choices = array();
            $choices[0] = get_string('none');
            $choices[1] = get_string('discussions', 'forum');
            $choices[2] = get_string('posts', 'forum');
            $mform->addElement('select', 'rsstype', get_string('rsstype', 'forum'), $choices);
            $mform->addHelpButton('rsstype', 'rsstype', 'forum');
            if (isset($CFG->forum_rsstype)) {
                $mform->setDefault('rsstype', $CFG->forum_rsstype);
            }

            $choices = array();
            $choices[0] = '0';
            $choices[1] = '1';
            $choices[2] = '2';
            $choices[3] = '3';
            $choices[4] = '4';
            $choices[5] = '5';
            $choices[10] = '10';
            $choices[15] = '15';
            $choices[20] = '20';
            $choices[25] = '25';
            $choices[30] = '30';
            $choices[40] = '40';
            $choices[50] = '50';
            $mform->addElement('select', 'rssarticles', get_string('rssarticles'), $choices);
            $mform->addHelpButton('rssarticles', 'rssarticles', 'forum');
            $mform->hideIf('rssarticles', 'rsstype', 'eq', '0');
            if (isset($CFG->forum_rssarticles)) {
                $mform->setDefault('rssarticles', $CFG->forum_rssarticles);
            }
        }

        $mform->addElement('header', 'discussionlocking', get_string('discussionlockingheader', 'forum'));
        $options = [
            0               => get_string('discussionlockingdisabled', 'forum'),
            1   * DAYSECS   => get_string('numday', 'core', 1),
            1   * WEEKSECS  => get_string('numweek', 'core', 1),
            2   * WEEKSECS  => get_string('numweeks', 'core', 2),
            30  * DAYSECS   => get_string('nummonth', 'core', 1),
            60  * DAYSECS   => get_string('nummonths', 'core', 2),
            90  * DAYSECS   => get_string('nummonths', 'core', 3),
            180 * DAYSECS   => get_string('nummonths', 'core', 6),
            1   * YEARSECS  => get_string('numyear', 'core', 1),
        ];
        $mform->addElement('select', 'lockdiscussionafter', get_string('lockdiscussionafter', 'forum'), $options);
        $mform->addHelpButton('lockdiscussionafter', 'lockdiscussionafter', 'forum');
        $mform->disabledIf('lockdiscussionafter', 'type', 'eq', 'single');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'blockafterheader', get_string('blockafter', 'forum'));
        $options = array();
        $options[0] = get_string('blockperioddisabled','forum');
        $options[60*60*24]   = '1 '.get_string('day');
        $options[60*60*24*2] = '2 '.get_string('days');
        $options[60*60*24*3] = '3 '.get_string('days');
        $options[60*60*24*4] = '4 '.get_string('days');
        $options[60*60*24*5] = '5 '.get_string('days');
        $options[60*60*24*6] = '6 '.get_string('days');
        $options[60*60*24*7] = '1 '.get_string('week');
        $mform->addElement('select', 'blockperiod', get_string('blockperiod', 'forum'), $options);
        $mform->addHelpButton('blockperiod', 'blockperiod', 'forum');

        $mform->addElement('text', 'blockafter', get_string('blockafter', 'forum'));
        $mform->setType('blockafter', PARAM_INT);
        $mform->setDefault('blockafter', '0');
        $mform->addRule('blockafter', null, 'numeric', null, 'client');
        $mform->addHelpButton('blockafter', 'blockafter', 'forum');
        $mform->hideIf('blockafter', 'blockperiod', 'eq', 0);

        $mform->addElement('text', 'warnafter', get_string('warnafter', 'forum'));
        $mform->setType('warnafter', PARAM_INT);
        $mform->setDefault('warnafter', '0');
        $mform->addRule('warnafter', null, 'numeric', null, 'client');
        $mform->addHelpButton('warnafter', 'warnafter', 'forum');
        $mform->hideIf('warnafter', 'blockperiod', 'eq', 0);

//-------------------------------------------------------------------------------

        // Add the whole forum grading options.
        $this->add_forum_grade_settings($mform, 'forum');

        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();
    }

    /**
     * Add the whole forum grade settings to the mform.
     *
     * @param   \mform $mform
     * @param   string $itemname
     */
    private function add_forum_grade_settings($mform, string $itemname) {
        global $COURSE;

        $component = "mod_{$this->_modname}";
        $defaultgradingvalue = 0;

        $itemnumber = component_gradeitems::get_itemnumber_from_itemname($component, $itemname);
        $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
        $gradecatfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradecat');
        $gradepassfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradepass');

        // The advancedgradingmethod is different in that it is suffixed with an area name... which is not the
        // itemnumber.
        $methodfieldname = "advancedgradingmethod_{$itemname}";

        $headername = "{$gradefieldname}_header";
        $mform->addElement('header', $headername, get_string("grade_{$itemname}_header", $component));

        $isupdate = !empty($this->_cm);
        $gradeoptions = [
            'isupdate' => $isupdate,
            'currentgrade' => false,
            'hasgrades' => false,
            'canrescale' => false,
            'useratings' => false,
        ];

        if ($isupdate) {
            $gradeitem = grade_item::fetch([
                'itemtype' => 'mod',
                'itemmodule' => $this->_cm->modname,
                'iteminstance' => $this->_cm->instance,
                'itemnumber' => $itemnumber,
                'courseid' => $COURSE->id,
            ]);
            if ($gradeitem) {
                $gradeoptions['currentgrade'] = $gradeitem->grademax;
                $gradeoptions['currentgradetype'] = $gradeitem->gradetype;
                $gradeoptions['currentscaleid'] = $gradeitem->scaleid;
                $gradeoptions['hasgrades'] = $gradeitem->has_grades();
            }
        }
        $mform->addElement(
            'modgrade',
            $gradefieldname,
            get_string("{$gradefieldname}_title", $component),
            $gradeoptions
        );
        $mform->addHelpButton($gradefieldname, 'modgrade', 'grades');
        $mform->setDefault($gradefieldname, $defaultgradingvalue);

        if (!empty($this->current->_advancedgradingdata['methods']) && !empty($this->current->_advancedgradingdata['areas'])) {
            $areadata = $this->current->_advancedgradingdata['areas'][$itemname];
            $mform->addElement(
                'select',
                $methodfieldname,
                get_string('gradingmethod', 'core_grading'),
                $this->current->_advancedgradingdata['methods']
            );
            $mform->addHelpButton($methodfieldname, 'gradingmethod', 'core_grading');
            $mform->hideIf($methodfieldname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');
        }

        // Grade category.
        $mform->addElement(
            'select',
            $gradecatfieldname,
            get_string('gradecategoryonmodform', 'grades'),
            grade_get_categories_menu($COURSE->id, $this->_outcomesused)
        );
        $mform->addHelpButton($gradecatfieldname, 'gradecategoryonmodform', 'grades');
        $mform->hideIf($gradecatfieldname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');

        // Grade to pass.
        $mform->addElement('text', $gradepassfieldname, get_string('gradepass', 'grades'));
        $mform->addHelpButton($gradepassfieldname, 'gradepass', 'grades');
        $mform->setDefault($gradepassfieldname, '');
        $mform->setType($gradepassfieldname, PARAM_RAW);
        $mform->hideIf($gradepassfieldname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');

        $mform->addElement('selectyesno', 'grade_forum_notify', get_string('sendstudentnotificationsdefault', 'forum'));
        $mform->addHelpButton('grade_forum_notify', 'sendstudentnotificationsdefault', 'forum');
        $mform->hideIf('grade_forum_notify', "{$gradefieldname}[modgrade_type]", 'eq', 'none');
    }

    function definition_after_data() {
        parent::definition_after_data();
        $mform     =& $this->_form;
        $type      =& $mform->getElement('type');
        $typevalue = $mform->getElementValue('type');

        //we don't want to have these appear as possible selections in the form but
        //we want the form to display them if they are set.
        if ($typevalue[0]=='news') {
            $type->addOption(get_string('namenews', 'forum'), 'news');
            $mform->addHelpButton('type', 'namenews', 'forum');
            $type->freeze();
            $type->setPersistantFreeze(true);
        }
        if ($typevalue[0]=='social') {
            $type->addOption(get_string('namesocial', 'forum'), 'social');
            $type->freeze();
            $type->setPersistantFreeze(true);
        }
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['type'] === 'single' && $data['groupmode'] == SEPARATEGROUPS) {
            $errors['type'] = get_string('cannotusesingletopicandseperategroups', 'forum');
            $errors['groupmode'] = get_string('cannotuseseperategroupsandsingletopic', 'forum');
        }

        if ($data['duedate'] && $data['cutoffdate']) {
            if ($data['duedate'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'forum');
            }
        }

        $this->validation_forum_grade($data, $files, $errors);

        return $errors;
    }

    /**
     * Handle definition after data for grade settings.
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     */
    private function validation_forum_grade(array $data, array $files, array $errors) {
        global $COURSE;

        $mform =& $this->_form;

        $component = "mod_forum";
        $itemname = 'forum';
        $itemnumber = component_gradeitems::get_itemnumber_from_itemname($component, $itemname);
        $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
        $gradepassfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');

        $gradeitem = grade_item::fetch([
            'itemtype' => 'mod',
            'itemmodule' => $data['modulename'],
            'iteminstance' => $data['instance'],
            'itemnumber' => $itemnumber,
            'courseid' => $COURSE->id,
        ]);

        if ($mform->elementExists('cmidnumber') && $this->_cm) {
            if (!grade_verify_idnumber($data['cmidnumber'], $COURSE->id, $gradeitem, $this->_cm)) {
                $errors['cmidnumber'] = get_string('idnumbertaken');
            }
        }

        // Check that the grade pass is a valid number.
        $gradepassvalid = false;
        if (isset($data[$gradepassfieldname])) {
            if (unformat_float($data[$gradepassfieldname], true) === false) {
                $errors[$gradepassfieldname] = get_string('err_numeric', 'form');
            } else {
                $gradepassvalid = true;
            }
        }

        // Grade to pass: ensure that the grade to pass is valid for points and scales.
        // If we are working with a scale, convert into a positive number for validation.
        if ($gradepassvalid && isset($data[$gradepassfieldname]) && (!empty($data[$gradefieldname]))) {
            $grade = $data[$gradefieldname];
            if (unformat_float($data[$gradepassfieldname]) > $grade) {
                $errors[$gradepassfieldname] = get_string('gradepassgreaterthangrade', 'grades', $grade);
            }
        }
    }

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        $suffix = $this->get_suffix();
        $completiondiscussionsenabledel = 'completiondiscussionsenabled' . $suffix;
        $completiondiscussionsel = 'completiondiscussions' . $suffix;
        $completionrepliesenabledel = 'completionrepliesenabled' . $suffix;
        $completionrepliesel = 'completionreplies' . $suffix;
        $completionpostsel = 'completionposts' . $suffix;
        $completionpostsenabledel = 'completionpostsenabled' . $suffix;

        // Set up the completion checkboxes which aren't part of standard data.
        // We also make the default value (if you turn on the checkbox) for those
        // numbers to be 1, this will not apply unless checkbox is ticked.
        $defaultvalues[$completiondiscussionsenabledel] = !empty($defaultvalues[$completiondiscussionsel]) ? 1 : 0;
        if (empty($defaultvalues[$completiondiscussionsel])) {
            $defaultvalues[$completiondiscussionsel] = 1;
        }
        $defaultvalues[$completionrepliesenabledel] = !empty($defaultvalues[$completionrepliesel]) ? 1 : 0;
        if (empty($defaultvalues[$completionrepliesel])) {
            $defaultvalues[$completionrepliesel] = 1;
        }
        // Tick by default if Add mode or if completion posts settings is set to 1 or more.
        if (empty($this->_instance) || !empty($defaultvalues[$completionpostsel])) {
            $defaultvalues[$completionpostsenabledel] = 1;
        } else {
            $defaultvalues[$completionpostsenabledel] = 0;
        }
        if (empty($defaultvalues[$completionpostsel])) {
            $defaultvalues[$completionpostsel] = 1;
        }
    }

    /**
     * Add custom completion rules.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $suffix = $this->get_suffix();

        $group = [];
        $completionpostsenabledel = 'completionpostsenabled' . $suffix;
        $group[] =& $mform->createElement('checkbox', $completionpostsenabledel, '', get_string('completionposts', 'forum'));
        $completionpostsel = 'completionposts' . $suffix;
        $group[] =& $mform->createElement('text', $completionpostsel, '', ['size' => 3]);
        $mform->setType($completionpostsel, PARAM_INT);
        $completionpostsgroupel = 'completionpostsgroup' . $suffix;
        $mform->addGroup($group, $completionpostsgroupel, '', ' ', false);
        $mform->hideIf($completionpostsel, $completionpostsenabledel, 'notchecked');

        $group = [];
        $completiondiscussionsenabledel = 'completiondiscussionsenabled' . $suffix;
        $group[] =& $mform->createElement(
            'checkbox',
            $completiondiscussionsenabledel,
            '',
            get_string('completiondiscussions', 'forum')
        );
        $completiondiscussionsel = 'completiondiscussions' . $suffix;
        $group[] =& $mform->createElement('text', $completiondiscussionsel, '', ['size' => 3]);
        $mform->setType($completiondiscussionsel, PARAM_INT);
        $completiondiscussionsgroupel = 'completiondiscussionsgroup' . $suffix;
        $mform->addGroup($group, $completiondiscussionsgroupel, '', ' ', false);
        $mform->hideIf($completiondiscussionsel, $completiondiscussionsenabledel, 'notchecked');

        $group = [];
        $completionrepliesenabledel = 'completionrepliesenabled' . $suffix;
        $group[] =& $mform->createElement('checkbox', $completionrepliesenabledel, '', get_string('completionreplies', 'forum'));
        $completionrepliesel = 'completionreplies' . $suffix;
        $group[] =& $mform->createElement('text', $completionrepliesel, '', ['size' => 3]);
        $mform->setType($completionrepliesel, PARAM_INT);
        $completionrepliesgroupel = 'completionrepliesgroup' . $suffix;
        $mform->addGroup($group, $completionrepliesgroupel, '', ' ', false);
        $mform->hideIf($completionrepliesel, $completionrepliesenabledel, 'notchecked');

        return [$completiondiscussionsgroupel, $completionrepliesgroupel, $completionpostsgroupel];
    }

    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return (!empty($data['completiondiscussionsenabled' . $suffix]) && $data['completiondiscussions' . $suffix] != 0) ||
            (!empty($data['completionrepliesenabled' . $suffix]) && $data['completionreplies' . $suffix] != 0) ||
            (!empty($data['completionpostsenabled' . $suffix]) && $data['completionposts' . $suffix] != 0);
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * Do not override this method, override data_postprocessing() instead.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $itemname = 'forum';
            $component = 'mod_forum';
            $gradepassfieldname = component_gradeitems::get_field_name_for_itemname($component, $itemname, 'gradepass');

            // Convert the grade pass value - we may be using a language which uses commas,
            // rather than decimal points, in numbers. These need to be converted so that
            // they can be added to the DB.
            if (isset($data->{$gradepassfieldname})) {
                $data->{$gradepassfieldname} = unformat_float($data->{$gradepassfieldname});
            }
        }

        return $data;
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $suffix = $this->get_suffix();
            $completion = $data->{'completion' . $suffix};
            $autocompletion = !empty($completion) && $completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->{'completiondiscussionsenabled' . $suffix}) || !$autocompletion) {
                $data->{'completiondiscussions' . $suffix} = 0;
            }
            if (empty($data->{'completionrepliesenabled' . $suffix}) || !$autocompletion) {
                $data->{'completionreplies' . $suffix} = 0;
            }
            if (empty($data->{'completionpostsenabled' . $suffix}) || !$autocompletion) {
                $data->{'completionposts' . $suffix} = 0;
            }
        }
    }
}
