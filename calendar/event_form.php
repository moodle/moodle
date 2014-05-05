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
 * The mform for creating and editing a calendar event
 *
 * @copyright 2009 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

 /**
  * Always include formslib
  */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating and editing a calendar
 *
 * @copyright 2009 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_form extends moodleform {
    /**
     * The form definition
     */
    function definition () {
        global $CFG, $USER, $OUTPUT;
        $mform = $this->_form;
        $newevent = (empty($this->_customdata->event) || empty($this->_customdata->event->id));
        $repeatedevents = (!empty($this->_customdata->event->eventrepeats) && $this->_customdata->event->eventrepeats>0);
        $hasduration = (!empty($this->_customdata->hasduration) && $this->_customdata->hasduration);
        $mform->addElement('header', 'general', get_string('general'));

        if ($newevent) {
            $eventtypes = $this->_customdata->eventtypes;
            $options = array();
            if (!empty($eventtypes->user)) {
                $options['user'] = get_string('user');
            }
            if (!empty($eventtypes->groups) && is_array($eventtypes->groups)) {
                $options['group'] = get_string('group');
            }
            if (!empty($eventtypes->courses)) {
                $options['course'] = get_string('course');
            }
            if (!empty($eventtypes->site)) {
                $options['site'] = get_string('site');
            }

            $mform->addElement('select', 'eventtype', get_string('eventkind', 'calendar'), $options);
            $mform->addRule('eventtype', get_string('required'), 'required');

            if (!empty($eventtypes->groups) && is_array($eventtypes->groups)) {
                $groupoptions = array();
                foreach ($eventtypes->groups as $group) {
                    $groupoptions[$group->id] = $group->name;
                }
                $mform->addElement('select', 'groupid', get_string('typegroup', 'calendar'), $groupoptions);
                $mform->disabledIf('groupid', 'eventtype', 'noteq', 'group');
            }
        }

        // Add some hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        $mform->addElement('hidden', 'modulename');
        $mform->setType('modulename', PARAM_INT);
        $mform->setDefault('modulename', '');

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', 0);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_INT);

        // Normal fields
        $mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'description', get_string('eventdescription','calendar'), null, $this->_customdata->event->editoroptions);
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('date_time_selector', 'timestart', get_string('date'));
        $mform->addRule('timestart', get_string('required'), 'required');

        $mform->addElement('header', 'durationdetails', get_string('eventduration', 'calendar'));

        $group = array();
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationnone', 'calendar'), 0);
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
        $group[] =& $mform->createElement('date_time_selector', 'timedurationuntil', '');
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationminutes', 'calendar'), 2);
        $group[] =& $mform->createElement('text', 'timedurationminutes', null);

        $mform->addGroup($group, 'durationgroup', '', '<br />', false);

        $mform->disabledIf('timedurationuntil',         'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[day]',    'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[month]',  'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[year]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[hour]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[minute]', 'duration', 'noteq', 1);

        $mform->setType('timedurationminutes', PARAM_INT);
        $mform->disabledIf('timedurationminutes','duration','noteq', 2);

        $mform->setDefault('duration', ($hasduration)?1:0);

        if ($newevent) {

            $mform->addElement('header', 'repeatevents', get_string('repeatedevents', 'calendar'));
            $mform->addElement('checkbox', 'repeat', get_string('repeatevent', 'calendar'), null);
            $mform->addElement('text', 'repeats', get_string('repeatweeksl', 'calendar'), 'maxlength="10" size="10"');
            $mform->setType('repeats', PARAM_INT);
            $mform->setDefault('repeats', 1);
            $mform->disabledIf('repeats','repeat','notchecked');

        } else if ($repeatedevents) {

            $mform->addElement('hidden', 'repeatid');
            $mform->setType('repeatid', PARAM_INT);

            $mform->addElement('header', 'repeatedevents', get_string('repeatedevents', 'calendar'));
            $mform->addElement('radio', 'repeateditall', null, get_string('repeateditall', 'calendar', $this->_customdata->event->eventrepeats), 1);
            $mform->addElement('radio', 'repeateditall', null, get_string('repeateditthis', 'calendar'), 0);

            $mform->setDefault('repeateditall', 1);

        }

        $this->add_action_buttons(false, get_string('savechanges'));
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     * @return array
     */
    function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        if ($data['courseid'] > 0) {
            if ($course = $DB->get_record('course', array('id'=>$data['courseid']))) {
                if ($data['timestart'] < $course->startdate) {
                    $errors['timestart'] = get_string('errorbeforecoursestart', 'calendar');
                }
            } else {
                $errors['courseid'] = get_string('invalidcourse', 'error');
            }

        }

        if ($data['duration'] == 1 && $data['timestart'] > $data['timedurationuntil']) {
            $errors['timedurationuntil'] = get_string('invalidtimedurationuntil', 'calendar');
        } else if ($data['duration'] == 2 && (trim($data['timedurationminutes']) == '' || $data['timedurationminutes'] < 1)) {
            $errors['timedurationminutes'] = get_string('invalidtimedurationminutes', 'calendar');
        }

        return $errors;
    }

}
