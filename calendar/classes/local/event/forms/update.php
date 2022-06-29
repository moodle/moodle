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
 * The mform for updating a calendar event. Based on the old event form.
 *
 * @package    core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_calendar\local\event\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for updating a calendar event.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update extends create {
    /**
     * Add the repeat elements for the form when editing an existing event.
     *
     * @param MoodleQuickForm $mform
     */
    protected function add_event_repeat_elements($mform) {
        $event = $this->_customdata['event'];

        $mform->addElement('hidden', 'repeatid');
        $mform->setType('repeatid', PARAM_INT);

        if (!empty($event->repeatid)) {
            $group = [];
            $group[] = $mform->createElement('radio', 'repeateditall', null, get_string('repeateditall', 'calendar',
                    $event->eventrepeats), 1);
            $group[] = $mform->createElement('radio', 'repeateditall', null, get_string('repeateditthis', 'calendar'), 0);
            $mform->addGroup($group, 'repeatgroup', get_string('repeatedevents', 'calendar'), '<br />', false);

            $mform->setDefault('repeateditall', 1);
            $mform->setAdvanced('repeatgroup');
        }
    }
}
