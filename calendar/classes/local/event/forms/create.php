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
 * The mform for creating a calendar event. Based on the old event form.
 *
 * @package    core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_calendar\local\event\forms;

use context_system;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating a calendar event.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create extends \moodleform {

    /**
     * Build the editor options using the given context.
     *
     * @param \context $context A Moodle context
     * @return array
     */
    public static function build_editor_options(\context $context) {
        global $CFG;

        return [
            'context' => $context,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $CFG->maxbytes,
            'noclean' => true,
            'autosave' => false
        ];
    }

    /**
     * The form definition
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $starttime = isset($this->_customdata['starttime']) ? $this->_customdata['starttime'] : 0;
        $editoroptions = !(empty($this->_customdata['editoroptions'])) ? $this->_customdata['editoroptions'] : null;
        $eventtypes = calendar_get_all_allowed_types();

        if (empty($eventtypes)) {
            print_error('nopermissiontoupdatecalendar');
        }

        $mform->setDisableShortforms();
        $mform->disable_form_change_checker();

        // Empty string so that the element doesn't get rendered.
        $mform->addElement('header', 'general', '');

        $this->add_default_hidden_elements($mform);

        // Event name field.
        $mform->addElement('text', 'name', get_string('eventname', 'calendar'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        // Event time start field.
        $mform->addElement('date_time_selector', 'timestart', get_string('date'), ['defaulttime' => $starttime]);

        // Add the select elements for the available event types.
        $this->add_event_type_elements($mform, $eventtypes);

        // Start of advanced elements.
        // Advanced elements are not visible to the user by default.
        // They are displayed through the user of a show more / less button.
        $mform->addElement('editor', 'description', get_string('eventdescription', 'calendar'), ['rows' => 3], $editoroptions);
        $mform->setType('description', PARAM_RAW);
        $mform->setAdvanced('description');

        // Add the variety of elements allowed for selecting event duration.
        $this->add_event_duration_elements($mform);

        // Add the form elements for repeating events.
        $this->add_event_repeat_elements($mform);

        // Add the javascript required to enhance this mform.
        $PAGE->requires->js_call_amd('core_calendar/event_form', 'init', [$mform->getAttribute('id')]);
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     * @return array
     */
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        $coursekey = isset($data['groupcourseid']) ? 'groupcourseid' : 'courseid';
        $eventtypes = calendar_get_all_allowed_types();
        $eventtype = isset($data['eventtype']) ? $data['eventtype'] : null;

        if (empty($eventtype) || !isset($eventtypes[$eventtype])) {
            $errors['eventtype'] = get_string('invalideventtype', 'calendar');
        }

        if (isset($data[$coursekey]) && $data[$coursekey] > 0) {
            if ($course = $DB->get_record('course', ['id' => $data[$coursekey]])) {
                if ($data['timestart'] < $course->startdate) {
                    $errors['timestart'] = get_string('errorbeforecoursestart', 'calendar');
                }
            } else {
                $errors[$coursekey] = get_string('invalidcourse', 'error');
            }
        }

        if ($eventtype == 'course' && empty($data['courseid'])) {
            $errors['courseid'] = get_string('selectacourse');
        }

        if ($eventtype == 'group' && empty($data['groupcourseid'])) {
            $errors['groupcourseid'] = get_string('selectacourse');
        }

        if ($data['duration'] == 1 && $data['timestart'] > $data['timedurationuntil']) {
            $errors['durationgroup'] = get_string('invalidtimedurationuntil', 'calendar');
        } else if ($data['duration'] == 2 && (trim($data['timedurationminutes']) == '' || $data['timedurationminutes'] < 1)) {
            $errors['durationgroup'] = get_string('invalidtimedurationminutes', 'calendar');
        }

        return $errors;
    }

    /**
     * Add the list of hidden elements that should appear in this form each
     * time. These elements will never be visible to the user.
     *
     * @param MoodleQuickForm $mform
     */
    protected function add_default_hidden_elements($mform) {
        global $USER;

        // Add some hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        $mform->addElement('hidden', 'modulename');
        $mform->setType('modulename', PARAM_INT);
        $mform->setDefault('modulename', '');

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', 0);

        $mform->addElement('hidden', 'visible');
        $mform->setType('visible', PARAM_INT);
        $mform->setDefault('visible', 1);
    }

    /**
     * Add the appropriate elements for the available event types.
     *
     * If the only event type available is 'user' then we add a hidden
     * element because there is nothing for the user to choose.
     *
     * If more than one type is available then we add the elements as
     * follows:
     *      - Always add the event type selector
     *      - Elements per type:
     *          - course: add an additional select element with each
     *                    course as an option.
     *          - group: add a select element for the course (different
     *                   from the above course select) and a select
     *                   element for the group.
     *
     * @param MoodleQuickForm $mform
     * @param array $eventtypes The available event types for the user
     */
    protected function add_event_type_elements($mform, $eventtypes) {
        $options = [];

        if (isset($eventtypes['user'])) {
            $options['user'] = get_string('user');
        }
        if (isset($eventtypes['group'])) {
            $options['group'] = get_string('group');
        }
        if (isset($eventtypes['course'])) {
            $options['course'] = get_string('course');
        }
        if (isset($eventtypes['category'])) {
            $options['category'] = get_string('category');
        }
        if (isset($eventtypes['site'])) {
            $options['site'] = get_string('site');
        }

        // If we only have one event type and it's 'user' event then don't bother
        // rendering the select boxes because there is no choice for the user to
        // make.
        if (count(array_keys($eventtypes)) == 1 && isset($eventtypes['user'])) {
            $mform->addElement('hidden', 'eventtype');
            $mform->setType('eventtype', PARAM_TEXT);
            $mform->setDefault('eventtype', 'user');

            // Render a static element to tell the user what type of event will
            // be created.
            $mform->addElement('static', 'staticeventtype', get_string('eventkind', 'calendar'), $options['user']);
            return;
        } else {
            $mform->addElement('select', 'eventtype', get_string('eventkind', 'calendar'), $options);
        }

        if (isset($eventtypes['category'])) {
            $categoryoptions = [];
            foreach ($eventtypes['category'] as $id => $category) {
                $categoryoptions[$id] = $category;
            }

            $mform->addElement('select', 'categoryid', get_string('category'), $categoryoptions);
            $mform->hideIf('categoryid', 'eventtype', 'noteq', 'category');
        }

        if (isset($eventtypes['course'])) {
            $limit = !has_capability('moodle/calendar:manageentries', context_system::instance());
            $mform->addElement('course', 'courseid', get_string('course'), ['limittoenrolled' => $limit]);
            $mform->hideIf('courseid', 'eventtype', 'noteq', 'course');
        }

        if (isset($eventtypes['group'])) {
            $options = ['limittoenrolled' => true];
            // Exclude courses without group.
            if (isset($eventtypes['course']) && isset($eventtypes['groupcourses'])) {
                $options['exclude'] = array_diff(array_keys($eventtypes['course']),
                    array_keys($eventtypes['groupcourses']));
            }

            $mform->addElement('course', 'groupcourseid', get_string('course'), $options);
            $mform->hideIf('groupcourseid', 'eventtype', 'noteq', 'group');

            $groupoptions = [];
            foreach ($eventtypes['group'] as $group) {
                // We are formatting it this way in order to provide the javascript both
                // the course and group ids so that it can enhance the form for the user.
                $index = "{$group->courseid}-{$group->id}";
                $groupoptions[$index] = format_string($group->name, true,
                    ['context' => \context_course::instance($group->courseid)]);
            }

            $mform->addElement('select', 'groupid', get_string('group'), $groupoptions);
            $mform->hideIf('groupid', 'eventtype', 'noteq', 'group');
            // We handle the group select hide/show actions on the event_form module.
        }
    }

    /**
     * Add the various elements to express the duration options available
     * for an event.
     *
     * @param MoodleQuickForm $mform
     */
    protected function add_event_duration_elements($mform) {
        $group = [];
        $group[] = $mform->createElement('radio', 'duration', null, get_string('durationnone', 'calendar'), 0);
        $group[] = $mform->createElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
        $group[] = $mform->createElement('date_time_selector', 'timedurationuntil', '');
        $group[] = $mform->createElement('radio', 'duration', null, get_string('durationminutes', 'calendar'), 2);
        $group[] = $mform->createElement('text', 'timedurationminutes', get_string('durationminutes', 'calendar'));

        $mform->addGroup($group, 'durationgroup', get_string('eventduration', 'calendar'), '<br />', false);
        $mform->setAdvanced('durationgroup');

        $mform->disabledIf('timedurationuntil',         'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[day]',    'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[month]',  'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[year]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[hour]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[minute]', 'duration', 'noteq', 1);

        $mform->setType('timedurationminutes', PARAM_INT);
        $mform->disabledIf('timedurationminutes', 'duration', 'noteq', 2);

        $mform->setDefault('duration', 0);
    }

    /**
     * Add the repeat elements for the form when creating a new event.
     *
     * @param MoodleQuickForm $mform
     */
    protected function add_event_repeat_elements($mform) {
        $mform->addElement('checkbox', 'repeat', get_string('repeatevent', 'calendar'), null);
        $mform->addElement('text', 'repeats', get_string('repeatweeksl', 'calendar'), 'maxlength="10" size="10"');
        $mform->setType('repeats', PARAM_INT);
        $mform->setDefault('repeats', 1);
        $mform->disabledIf('repeats', 'repeat', 'notchecked');
        $mform->setAdvanced('repeat');
        $mform->setAdvanced('repeats');
    }
}
