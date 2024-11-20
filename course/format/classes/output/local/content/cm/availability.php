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
 * Contains the default activity availability information.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use core_courseformat\output\local\content\section\availability as section_avalability;
use cm_info;
use core_courseformat\base as course_format;
use section_info;
use stdClass;
use core_availability\info_module;
use core_availability\info;

/**
 * Base class to render a course module availability inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability extends section_avalability {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    protected $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var bool the has availability attribute name */
    protected $hasavailabilityname;

    /** @var stdClass|null the instance export data */
    protected $data = null;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param array $displayoptions optional extra display options
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod, array $displayoptions = []) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
        $this->displayoptions = $displayoptions;
        $this->hasavailabilityname = 'hasmodavailability';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): ?stdClass {
        if (!$this->format->show_activity_editor_options($this->mod)) {
            return null;
        }
        return parent::export_for_template($output);
    }

    /**
     * Get the availability data to be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array the availability data.
     */
    protected function get_info(\renderer_base $output): array {
        if (!$this->mod->is_visible_on_course_page()) {
            // Nothing to be displayed to the user.
            return [];
        }

        if (!$this->mod->uservisible) {
            return ['info' => $this->user_availability_info($output)];
        }

        $editurl = new \moodle_url(
            '/course/modedit.php',
            ['update' => $this->mod->id, 'showonly' => 'availabilityconditionsheader']
        );
        return ['editurl' => $editurl->out(false), 'info' => $this->conditional_availability_info($output)];
    }

    /**
     * Get the current user availability data.
     *
     * This is a student who is not allowed to see the module but might be allowed
     * to see availability info (i.e. "Available from ...").
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array the availability data.
     */
    protected function user_availability_info(\renderer_base $output): array {
        if (empty($this->mod->availableinfo)) {
            return [];
        }

        $info = [];
        $info[] = $this->get_availability_data($output, $this->mod->availableinfo, 'isrestricted');
        return $info;
    }

    /**
     * Get the activity availability data to display.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array the availability data.
     */
    protected function conditional_availability_info(\renderer_base $output): array {
        global $CFG;

        // This is a teacher who is allowed to see module but still should see the
        // information that module is not available to all/some students.
        $mod  = $this->mod;
        $modcontext = $mod->context;
        $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
        if (!$canviewhidden || empty($CFG->enableavailability)) {
            return [];
        }

        // Display information about conditional availability.
        // Don't add availability information if user is not editing and activity is hidden.
        if (!$mod->visible && !$this->format->show_editor()) {
            return [];
        }

        $ci = new info_module($mod);
        $fullinfo = $ci->get_full_information();
        if (!$fullinfo) {
            return [];
        }

        $info = [];
        $hidinfoclass = 'isrestricted isfullinfo';
        if (!$mod->visible) {
            $hidinfoclass .= ' hide';
        }
        $info[] = $this->get_availability_data($output, $fullinfo, $hidinfoclass);

        return $info;
    }
}
