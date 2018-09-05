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
 * The trait for adding eventtype fields to a form.
 *
 * @package     core_calendar
 * @copyright   2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_calendar\local\event\forms;

defined('MOODLE_INTERNAL') || die();

/**
 * The trait for adding eventtype fields to a form.
 *
 * @copyright   2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait eventtype {

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
        global $CFG, $DB;
        $options = [];

        if (!empty($eventtypes['user'])) {
            $options['user'] = get_string('user');
        }
        if (!empty($eventtypes['group'])) {
            $options['group'] = get_string('group');
        }
        if (!empty($eventtypes['course'])) {
            $options['course'] = get_string('course');
        }
        if (!empty($eventtypes['category'])) {
            $options['category'] = get_string('category');
        }
        if (!empty($eventtypes['site'])) {
            $options['site'] = get_string('site');
        }

        // If we only have one event type and it's 'user' event then don't bother
        // rendering the select boxes because there is no choice for the user to
        // make.
        if (!empty($eventtypes['user']) && count($options) == 1) {
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

        if (!empty($eventtypes['category'])) {
            $categoryoptions = [];
            foreach (\coursecat::make_categories_list('moodle/category:manage') as $id => $category) {
                $categoryoptions[$id] = $category;
            }

            $mform->addElement('select', 'categoryid', get_string('category'), $categoryoptions);
            $mform->hideIf('categoryid', 'eventtype', 'noteq', 'category');
        }

        $showall = $CFG->calendar_adminseesall && !has_capability('moodle/calendar:manageentries', \context_system::instance());
        if (!empty($eventtypes['course'])) {
            $mform->addElement('course', 'courseid', get_string('course'), ['limittoenrolled' => !$showall]);
            $mform->hideIf('courseid', 'eventtype', 'noteq', 'course');
        }

        if (!empty($eventtypes['group'])) {
            $groups = !(empty($this->_customdata['groups'])) ? $this->_customdata['groups'] : null;
            // Get the list of courses without groups to filter on the course selector.
            $sql = "SELECT c.id
                      FROM {course} c
                     WHERE c.id NOT IN (
                            SELECT DISTINCT courseid FROM {groups}
                           )";
            $coursesnogroup = $DB->get_records_sql($sql);
            $mform->addElement('course', 'groupcourseid', get_string('course'),  ['limittoenrolled' => !$showall,
                    'exclude' => array_keys($coursesnogroup)]);
            $mform->hideIf('groupcourseid', 'eventtype', 'noteq', 'group');

            $mform->addElement('select', 'groupid', get_string('group'), $groups);
            $mform->hideIf('groupid', 'eventtype', 'noteq', 'group');
            // We handle the group select hide/show actions on the event_form module.
        }
    }
}
