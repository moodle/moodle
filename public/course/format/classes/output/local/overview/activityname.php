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

namespace core_courseformat\output\local\overview;

use cm_info;
use core\output\externable;
use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core_courseformat\base as course_format;
use core_courseformat\external\activityname_exporter;
use stdClass;

/**
 * Class activityname
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activityname implements externable, named_templatable, renderable {
    /**
     * Constructor.
     *
     * @param cm_info $cm The course module.
     */
    public function __construct(
        /** @var cm_info The course module. */
        protected cm_info $cm,
        /** @var bool Should show no groups error */
        protected bool $nogroupserror = false,
    ) {
    }

    /**
     * nogroupserror property setter
     *
     * @param bool $nogroupserror New value fpr nogroupserror property
     * @return $this
     */
    public function set_nogroupserror(bool $nogroupserror): self {
        $this->nogroupserror = $nogroupserror;
        return $this;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $cm = $this->cm;
        $section = $this->cm->get_section_info();
        $course = $this->cm->get_course();
        $format = course_format::instance($course);

        $result = (object) [
            'activityname' => \core_external\util::format_string($cm->name, $cm->context, true),
            'activityurl' => $cm->url,
            'hidden' => empty($cm->visible),
            'stealth' => $cm->is_stealth(),
            'nogroupserror' => $this->nogroupserror,
            'available' => overviewtable::is_cm_available($cm),
        ];
        if ($format->uses_sections() && $section->uservisible) {
            $result->sectiontitle = $format->get_section_name($section);
        }
        return $result;
    }

    /**
     * Get the error messages for this activity.
     *
     * @return array
     */
    public function get_error_messages(): array {
        $messages = [];
        if ($this->nogroupserror) {
            $messages[] = get_string('overview_nogroups_error', 'course');
        }
        return $messages;
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): activityname_exporter {
        $context = $context ?? $this->cm->context;
        return new activityname_exporter($this, ['context' => $context]);
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return activityname_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return activityname_exporter::read_properties_definition();
    }

    /**
     * Get the template name.
     *
     * @param renderer_base $renderer Renderer base.
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'core_courseformat/local/overview/activityname';
    }
}
