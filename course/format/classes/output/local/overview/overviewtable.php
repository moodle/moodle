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

use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\plugin_manager;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\local\overview\overviewfactory;
use cm_info;
use stdClass;

/**
 * Class overviewtable
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewtable implements renderable, named_templatable {
    /** @var array $header the table headers */
    private array $headers = [];

    /** @var array $columnhascontent indicate which columns has content */
    private array $columnhascontent = [];

    /**
     * Constructor.
     *
     * @param stdClass $course the course object.
     * @param string $modname the module name (or "resources" for generic resources overview).
     */
    public function __construct(
        /** @var stdClass the course object  */
        protected stdClass $course,
        /** @var string the module name (or "resources" for generic resources overview) */
        protected string $modname,
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output): stdClass {
        $activities = $this->load_all_overviews_from_each_activity($output);
        $headers = $this->export_headers();
        $result = (object) [
            'caption' => $this->get_table_caption(),
            'headers' => $headers,
            'activities' => $this->export_filtered_overviews($output, $activities, $headers),
        ];
        return $result;
    }

    /**
     * Export the filtered list of headers.
     *
     * This method will remove any header that does not have any content.
     *
     * @return array
     */
    private function export_headers(): array {
        return array_values(
            array_filter($this->headers, function ($header) {
                return $this->columnhascontent[$header->key];
            })
        );
    }

    /**
     * Exports filtered overviews for the given activities and headers.
     *
     * @param renderer_base $output
     * @param array $activities An array of activities, each containing an 'overviews' array and a 'cmid'.
     * @param array $headers An array of header objects, each containing a 'key' property.
     * @return array An array of filtered overviews, each containing 'cmid' and 'overviews' with rendered content.
     */
    private function export_filtered_overviews(
        renderer_base $output,
        array $activities,
        array $headers,
    ): array {
        $result = [];
        foreach ($activities as $activity) {
            $items = [];
            foreach ($headers as $header) {
                if (!isset($activity['overviews'][$header->key])) {
                    $items[] = (object) ['content' => '', 'overview' => $header->key];
                    continue;
                }
                /** @var overviewitem $item */
                $item = $activity['overviews'][$header->key];
                $itemsdata = (object) [
                    'content' => $item->get_rendered_content($output),
                    'overview' => $header->key,
                    'value' => $item->get_value(),
                    'textalign' => $item->get_text_align()->classes(),
                ];

                $alertcount = $item->get_alert_count();
                if ($alertcount > 0) {
                    $itemsdata->alertcount = $alertcount;
                    $itemsdata->alertlabel = $item->get_alert_label();
                }

                $items[] = $itemsdata;
            }
            $result[] = [
                'cmid' => $activity['cmid'],
                'overviews' => $items,
            ];
        }
        return $result;
    }

    /**
     * Loads all overviews from activities for the given course and module name.
     *
     * @param renderer_base $output
     * @return array An array of overviews.
     */
    private function load_all_overviews_from_each_activity(renderer_base $output): array {
        $result = [];
        foreach ($this->get_related_course_modules() as $cm) {
            if (!$this->is_cm_displayable($cm)) {
                continue;
            }
            $result[] = [
                'cmid' => $cm->id,
                'overviews' => $this->load_overview_items_from_activity($output, $cm),
            ];
        }
        return $result;
    }

    /**
     * Get all course modules related to the modname.
     *
     * @return cm_info[]
     */
    private function get_related_course_modules(): array {
        $modinfo = get_fast_modinfo($this->course->id);
        if ($this->modname == 'resource') {
            return $this->get_all_resource_intances($modinfo);
        }
        return $modinfo->get_instances_of($this->modname);
    }

    /**
     * Get the table caption.
     *
     * @return string
     */
    private function get_table_caption(): string {
        if ($this->modname === 'resource') {
            $name = get_string('resource');
        } else {
            $pluginman = plugin_manager::instance();
            $name = $pluginman->plugin_name($this->modname);
        }
        return get_string('overview_table_caption', 'core_course', $name);
    }

    /**
     * Get all resource instances.
     *
     * This is a very specific case where the same overview table shows
     * several plugins at once. This is not a common use case and only
     * happens for MOD_ARCHETYPE_RESOURCE plugins.
     *
     * @param mixed $modinfo
     * @return array
     */
    private function get_all_resource_intances($modinfo): array {
        $resources = [];
        $archetypes = [];
        foreach ($modinfo->cms as $cm) {
            if (!array_key_exists($cm->modname, $archetypes)) {
                $archetypes[$cm->modname] = plugin_supports(
                    type: 'mod',
                    name: $cm->modname,
                    feature: FEATURE_MOD_ARCHETYPE,
                    default: MOD_ARCHETYPE_OTHER
                );
            }
            if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
                $resources[] = $cm;
            }
        }
        return $resources;
    }

    /**
     * Check if the course module is displayable in the overview table.
     *
     * @param cm_info $cm
     * @return bool
     */
    private function is_cm_displayable(cm_info $cm): bool {
        // Folder is an exception because it has settings to be displayed in the course
        // page without having a view link.
        return $cm->uservisible && ($cm->has_view() || strcmp($cm->modname, 'folder') === 0);
    }

    /**
     * Loads overview items from a given activity.
     *
     * @param renderer_base $output
     * @param cm_info $cm
     * @return array An associative array containing the overview items for the activity.
     */
    private function load_overview_items_from_activity(renderer_base $output, cm_info $cm): array {
        global $PAGE;
        $overview = overviewfactory::create($cm);

        $row = [
            'name' => $overview->get_name_overview($output),
            'duedate' => $overview->get_due_date_overview($output),
            'completion' => $overview->get_completion_overview($output),
        ];

        $row = array_merge($row, $overview->get_extra_overview_items($output));

        $gradeitems = $overview->get_grades_overviews();
        if (!empty($gradeitems)) {
            foreach ($gradeitems as $gradeitem) {
                $row[$gradeitem->get_name()] = $gradeitem;
            }
        }

        // Actions are always the last column, if any.
        $row['actions'] = $overview->get_actions_overview($output);

        $row = array_filter($row, function ($item) {
            return $item !== null;
        });

        $this->register_columns($row);

        $result = [];
        foreach ($row as $key => $item) {
            $result[$key] = $item;
        }
        return $result;
    }

    /**
     * Register a new row into the table.
     *
     * @param overviewitem[] $row
     * @return void
     */
    private function register_columns(array $row): void {
        foreach ($row as $key => $item) {
            if (!isset($this->columnhascontent[$key])) {
                $this->columnhascontent[$key] = false;
                $this->headers[] = (object) [
                    'name' => $item->get_name(),
                    'key' => $key,
                    'textalign' => $item->get_text_align()->classes(),
                ];
            }
            $this->columnhascontent[$key] = $this->columnhascontent[$key] || $item->get_value() !== null;
        }
    }

    /**
     * Get the template name.
     *
     * @param renderer_base $renderer Renderer base.
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'core_courseformat/local/overview/overviewtable';
    }
}
