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
 * Contains class core_tag\output\course_module_name
 *
 * @package   core_course
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\output;

use cm_info;
use context_module;
use core_external\external_api;
use lang_string;

/**
 * Class to prepare a course module name for display and in-place editing
 *
 * @deprecated since Moodle 4.0 MDL-72656 - please do not use this class any more.
 * @package   core_course
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_name extends \core\output\inplace_editable {

    /** @var cm_info */
    protected $cm;

    /** @var array */
    protected $displayoptions;

    /**
     * Constructor.
     *
     * @param cm_info $cm
     * @param bool $editable
     * @param array $displayoptions
     */
    public function __construct(cm_info $cm, $editable, $displayoptions = array()) {
        debugging(
            'course_section_cm_list is deprecated. Use core_courseformat\\output\\local\\cm\\cmname instead',
            DEBUG_DEVELOPER
        );
        $this->cm = $cm;
        $this->displayoptions = $displayoptions;
        $value = $cm->name;
        $edithint = new lang_string('edittitle');
        $editlabel = new lang_string('newactivityname', '', $cm->get_formatted_name());
        $editable = $editable && has_capability('moodle/course:manageactivities',
                    context_module::instance($cm->id));
        parent::__construct(
            'core_course', 'activityname', $cm->id, $editable, $value, $value, $edithint, $editlabel);
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        $courserenderer = $PAGE->get_renderer('core', 'course');
        $this->displayvalue = $courserenderer->course_section_cm_name_title($this->cm, $this->displayoptions);
        if (strval($this->displayvalue) === '') {
            $this->editable = false;
        }
        return parent::export_for_template($output);
    }

    /**
     * Updates course module name
     *
     * @param int $itemid course module id
     * @param string $newvalue new name
     * @return static
     */
    public static function update($itemid, $newvalue) {
        $context = context_module::instance($itemid);
        // Check access.
        external_api::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        // Trim module name and Update value.
        set_coursemodule_name($itemid, trim($newvalue));
        $coursemodulerecord = get_coursemodule_from_id('', $itemid, 0, false, MUST_EXIST);
        // Return instance.
        $cm = get_fast_modinfo($coursemodulerecord->course)->get_cm($itemid);
        return new static($cm, true);
    }
}
