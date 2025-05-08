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

use core\output\externable;
use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\plugin_manager;
use core_courseformat\activityoverviewbase;
use core_courseformat\external\overviewtable_exporter;
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
class overviewtable implements externable, named_templatable, renderable {
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
        $activities = $this->load_all_overviews_from_each_activity();
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
                'haserror' => $activity['haserror'],
                'overviews' => $items,
            ];
        }
        return $result;
    }

    /**
     * Loads all overviews from activities for the given course and module name.
     *
     * @return array An array of overviews.
     */
    private function load_all_overviews_from_each_activity(): array {
        $result = [];
        foreach ($this->get_related_course_modules() as $cm) {
            if (!self::is_cm_displayable($cm)) {
                continue;
            }
            $overview = overviewfactory::create($cm);
            $result[] = [
                'cmid' => $cm->id,
                'cm' => $cm,
                'haserror' => $overview->has_error(),
                'overviews' => $this->load_overview_items_from_activity($overview),
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
            $result = $this->get_all_resource_intances($modinfo);
        } else {
            $result = $modinfo->get_instances_of($this->modname);
        }
        $modinfo->sort_cm_array($result);
        return $result;
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
     * @param cm_info $cm The course module info
     * @return bool Whether the course module is displayable in the overview table or not.
     */
    public static function is_cm_displayable(cm_info $cm): bool {
        // Exclude activities that aren't displayed in the course page (except for stealth),
        // activities that are not available but availability is hidden
        // or activities that have no view link (e.g. label).
        // Folder is an exception because it has settings to be displayed in the course
        // page without having a view link.
        return (
            \course_modinfo::is_mod_type_visible_on_course($cm->modname)
            && (
                has_capability('moodle/course:viewhiddenactivities', $cm->context)
                || (($cm->is_visible_on_course_page() || $cm->is_stealth()
            )
            && ($cm->available || !empty($cm->availableinfo))))
            && ($cm->has_view() || strcmp($cm->modname, 'folder') === 0)
        );
    }

    /**
     * Check if the given course module is available (so linkable) in the overview table.
     *
     * @param cm_info $cm The course module info
     * @return bool Whether the course module is available or not.
     */
    public static function is_cm_available(cm_info $cm): bool {
        return $cm->uservisible || $cm->available;
    }

    /**
     * Loads overview items from a given activity.
     *
     * @param activityoverviewbase $overview
     * @return array An associative array containing the overview items for the activity.
     */
    private function load_overview_items_from_activity(activityoverviewbase $overview): array {
        $row = $this->get_activity_columns($overview);
        $row = array_filter($row, function ($item) {
            return $item !== null;
        });

        $this->register_columns($row);
        $result = [];
        foreach ($row as $key => $item) {
            $item->set_key($key);
            $result[$key] = $item;
        }
        return $result;
    }

    /**
     * Get the columns for the activity overview.
     *
     * This method retrieves the columns that can be displayed in the overview table
     * for a specific activity. However, column with null values may be filtered if
     * all the activities do not have any content for that column.
     *
     * @param activityoverviewbase $overview The activity overview instance.
     * @return array An associative array of column data.
     */
    private function get_activity_columns(activityoverviewbase $overview): array {
        // It is highly improbable that an activity has an error (usually because of an erroneous group
        // configuration). For those cases, we only use the activity name and prevent the plugin from
        // doing any more calculations.
        if ($overview->has_error()) {
            return ['name' => $overview->get_name_overview()];
        }

        if (!self::is_cm_available($overview->cm)) {
            return [
                'name' => $overview->get_name_overview(),
                'duedate' => $overview->get_due_date_overview(),
            ];
        }

        $row = [
            'name' => $overview->get_name_overview(),
            'duedate' => $overview->get_due_date_overview(),
            'completion' => $overview->get_completion_overview(),
        ];

        $row = array_merge($row, $overview->get_extra_overview_items());

        $gradeitems = $overview->get_grades_overviews();
        if (!empty($gradeitems)) {
            foreach ($gradeitems as $gradeitem) {
                $row[$gradeitem->get_name()] = $gradeitem;
            }
        }

        // Actions are always the last column, if any.
        $row['actions'] = $overview->get_actions_overview();

        return $row;
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
                    'align' => $item->get_text_align()->value,
                ];
            }
            $this->columnhascontent[$key] = $this->columnhascontent[$key] || $item->get_value() !== null;
        }
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): overviewtable_exporter {
        $context = $context ?? \core\context\course::instance($this->course->id);
        return new overviewtable_exporter(
            $this,
            ['context' => $context],
        );
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return overviewtable_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return overviewtable_exporter::read_properties_definition();
    }

    /**
     * Exports overview table data for external use.
     *
     * This method gathers all activity overviews, headers, course information,
     * integration status, and formats them for external consumption.
     *
     * @return stdClass An object containing all the output related data.
     */
    public function export_for_external(): stdClass {
        $activities = $this->load_all_overviews_from_each_activity();
        return (object) [
            'headers' => $this->export_headers(),
            'course' => $this->course,
            'hasintegration' => overviewfactory::activity_has_overview_integration($this->modname),
            'activities' => $this->export_activities_for_external($activities),
        ];
    }

    /**
     * Exports the activities for external use.
     *
     * @param array $activities An array of activities, each containing a 'cm' and 'overviews'.
     * @return array An array of activities ready for external export.
     */
    private function export_activities_for_external(
        array $activities,
    ): array {
        $result = [];
        foreach ($activities as $activity) {
            $columnitems = array_filter(
                $activity['overviews'],
                fn($key): bool => $this->columnhascontent[$key],
                ARRAY_FILTER_USE_KEY,
            );
            $result[] = (object) [
                'cm' => $activity['cm'],
                'haserror' => $activity['haserror'],
                'items' => array_values($columnitems),
            ];
        }
        return $result;
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
