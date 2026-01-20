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
 * Contains the default activity list from a section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use cm_info;
use context_course;
use core\output\named_templatable;
use core_availability\info_module;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content\cm;
use core_courseformat\output\local\courseformat_named_templatable;
use core_courseformat\output\section_renderer;
use renderable;
use renderer_base;
use section_info;
use stdClass;

/**
 * Base class to render a restricted course module.
 *
 * @package   core_courseformat
 * @copyright 2026 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restricted extends cm {
    /** @var string the activity name output class name */
    protected $restrictedclass;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param array $displayoptions optional extra display options
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod, array $displayoptions = []) {

        // Get the necessary classes.
        $this->restrictedclass = $format->get_output_classname('content\\cm\\restricted');

        parent::__construct($format, $section, $mod, $displayoptions);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {

        $mod = $this->mod;
        $displayoptions = $this->displayoptions;

        $data = (object)[
            'grouping' => $mod->get_grouping_label($displayoptions['textclasses']),
            'textclasses' => $displayoptions['textclasses'],
            'classlist' => [],
            'cmid' => $mod->id,
            'hasurl' => false,
            'hasname' => false,
        ];

        // Add partial data segments.
        $haspartials = [];
        $haspartials['availability'] = $this->add_availability_data($data, $output);
        $haspartials['alternative'] = $this->add_alternative_content_data($data, $output);
        $haspartials['completion'] = $this->add_completion_data($data, $output);
        $haspartials['dates'] = $this->add_dates_data($data, $output);
        $haspartials['groupmode'] = $this->add_groupmode_data($data, $output);
        $haspartials['visibility'] = $this->add_visibility_data($data, $output);
        $this->add_format_data($data, $haspartials, $output);

        return $data;
    }
}
