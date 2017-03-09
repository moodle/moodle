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
 * Bulk activity completion manager class
 *
 * @package     core_completion
 * @category    completion
 * @copyright   2017 Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_completion;

use stdClass;
use context_course;

/**
 * Bulk activity completion manager class
 *
 * @package     core_completion
 * @category    completion
 * @copyright   2017 Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    protected $courseid;

    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Gets the data (context) to be used with the bulkactivitycompletion template.
     *
     * @return stdClass data for use with the bulkactivitycompletion template.
     */
    public function get_activities_and_headings() {
        global $OUTPUT;
        $moduleinfo = get_fast_modinfo($this->courseid);
        $sections = $moduleinfo->get_sections();
        $data = new stdClass;
        $data->courseid = $this->courseid;
        $data->sesskey = sesskey();
        $data->helpicon = $OUTPUT->help_icon('temphelp', 'moodle');
        $data->sections = [];
        foreach ($sections as $sectionnumber => $section) {
            $sectioninfo = $moduleinfo->get_section_info($sectionnumber);

            $sectionobject = new stdClass();
            $sectionobject->sectionnumber = $sectionnumber;
            $sectionobject->name = get_section_name($this->courseid, $sectioninfo);
            $sectionobject->activities = [];

            foreach ($section as $cmid) {
                $mod = $moduleinfo->get_cm($cmid);
                $moduleobject = new stdClass();
                $moduleobject->cmid = $cmid;
                $moduleobject->modname = $mod->get_formatted_name();
                $moduleobject->icon = $mod->get_icon_url()->out();
                $moduleobject->url = $mod->url;

                // Get activity completion information.
                $moduleobject->completionstatus = $this->get_completion_detail($mod);

                $sectionobject->activities[] = $moduleobject;
            }
            $data->sections[] = $sectionobject; 
        }
        return $data;
    }

    private function get_completion_detail(\cm_info $mod) {
        global $OUTPUT;
        $strings = [];
        switch ($mod->completion) {
            case 0:
                $strings['string'] = get_string('none');
                break;

            case 1:
                $strings['string'] = get_string('manual');
                $strings['icon'] = $OUTPUT->pix_url('i/completion-manual-enabled')->out();
                break;

            case 2:
                $strings['string'] = get_string('withconditions');

                // Get the descriptions for all the active completion rules for the module.
                if ($ruledescriptions = $mod->get_completion_active_rule_descriptions()) {
                    foreach ($ruledescriptions as $ruledescription) {
                        $strings['string'] .= \html_writer::empty_tag('br') . $ruledescription;
                    }
                }

                $strings['icon'] = $OUTPUT->pix_url('i/completion-auto-enabled')->out();
                break;

            default:
                $strings['string'] = get_string('none');
                break;
        }
        return $strings;
    }

    public function get_activities_and_resources() {
        global $DB, $OUTPUT;
        // Get enabled activities and resources.
        $modules = $DB->get_records('modules', ['visible' => 1], 'name ASC');
        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->sesskey = sesskey();
        $data->helpicon = $OUTPUT->help_icon('temphelp', 'moodle');
        // Add icon information.
        $data->modules = array_values($modules);
        $coursecontext = context_course::instance($this->courseid);
        foreach ($data->modules as $module) {
            $module->icon = $OUTPUT->pix_url('icon', $module->name)->out();
            $module->formatedname = format_string(get_string('pluginname', 'mod_' . $module->name), true, ['context' => $coursecontext]);
        }

        return $data;
    }

}