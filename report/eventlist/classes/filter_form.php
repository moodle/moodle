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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Event list filter form.
 *
 * @package   report_eventlist
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_eventlist_filter_form extends moodleform {

    /**
     * Form definition method.
     */
    public function definition() {

        $mform = $this->_form;
        $mform->disable_form_change_checker();
        $componentarray = $this->_customdata['components'];
        $edulevelarray = $this->_customdata['edulevel'];
        $crudarray = $this->_customdata['crud'];

        $mform->addElement('header', 'displayinfo', get_string('filter', 'report_eventlist'));

        $mform->addElement('text', 'eventname', get_string('name', 'report_eventlist'));
        $mform->setType('eventname', PARAM_RAW);

        $mform->addElement('selectgroups', 'eventcomponent', get_string('component', 'report_eventlist'),
            self::group_components_by_type($componentarray));
        $mform->addElement('select', 'eventedulevel', get_string('edulevel', 'report_eventlist'), $edulevelarray);
        $mform->addElement('select', 'eventcrud', get_string('crud', 'report_eventlist'), $crudarray);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('button', 'filterbutton', get_string('filter', 'report_eventlist'));
        $buttonarray[] = $mform->createElement('button', 'clearbutton', get_string('clear', 'report_eventlist'));
        $mform->addGroup($buttonarray, 'filterbuttons', '', array(' '), false);
    }

    /**
     * Group list of component names by type for use in grouped select element
     *
     * @param string[] $components
     * @return array[] Component type => [...Components]
     */
    private static function group_components_by_type(array $components): array {
        $pluginmanager = core_plugin_manager::instance();

        $result = [];
        foreach ($components as $component) {
            // Core sub-systems are grouped together and are denoted by a distinct lang string.
            if (strpos($component, 'core') === 0) {
                $componenttype = get_string('core', 'report_eventlist');
                $componentname = get_string('coresubsystem', 'report_eventlist', $component);
            } else {
                [$type] = core_component::normalize_component($component);
                $componenttype = $pluginmanager->plugintype_name_plural($type);
                $componentname = $pluginmanager->plugin_name($component);
            }

            $result[$componenttype][$component] = $componentname;
        }

        // Sort returned components according to their type, followed by name.
        core_collator::ksort($result);
        array_walk($result, function(array &$componenttype) {
            core_collator::asort($componenttype);
        });

        // Prepend "All" option.
        array_unshift($result, [0 => get_string('all', 'report_eventlist')]);
        return $result;
    }
}
