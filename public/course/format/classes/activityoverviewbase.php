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

namespace core_courseformat;

use cm_info;
use core\context\module as module_context;
use core_completion\cm_completion_details;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\output\local\overview\activityname;
use core_courseformat\output\local\overview\overviewpage;
use core_courseformat\base as courseformat;
use grade_item;
use grade_grade;

/**
 * Base class for activity overview.
 *
 * Plugins must extend this class on their \mod_PLUGINNAME\courseformat\overview
 * integration to provide overview items about a course module instance.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activityoverviewbase {
    /** @var cm_info The course module. */
    protected module_context $context;

    /** @var \stdClass $course */
    protected \stdClass $course;

    /** @var courseformat $format the course format */
    protected courseformat $format;

    /**
     * Activity Overview Base class constructor.
     *
     * Overview integrations are meant to use dependency injection.
     * Don't create instances of this class directly, use the factory instead.
     *
     * \core_courseformat\local\overview\overviewfactory::create($cm);
     *
     * Plugins can override the constructor adding more dependencies such as:
     *
     *  - protected readonly \moodle_database $db -> To access the database.
     *  - protected readonly \core\clock $clock -> The clock interface to handle time.
     *
     * However, it is important to note all original dependencies must be kept.
     *
     * @param cm_info $cm The course module information (loaded by the factory).
     */
    public function __construct(
        /** @var cm_info The course module. */
        protected readonly cm_info $cm,
    ) {
        $this->context = $cm->context;
        $this->course = $cm->get_course();
        $this->format = courseformat::instance($this->course);
    }

    /**
     * Redirects to the overview page for the activity.
     *
     * @param int $courseid The course id.
     * @param string $modname The module name.
     */
    public static function redirect_to_overview_page(int $courseid, string $modname): void {
        redirect(overviewpage::get_modname_url($courseid, $modname));
    }

    /**
     * Get the plugin specific overview items for the activity.
     *
     * Plugins can override this method to provide their own overview items.
     *
     * The resulting array must be indexed by item shortname.
     *
     * @return overviewitem[] Array of overview items indexed by item shortname.
     */
    public function get_extra_overview_items(): array {
        return [];
    }

    /**
     * Get the name of the activity.
     */
    final public function get_name_overview(): overviewitem {
        return new overviewitem(
            name: get_string('name'),
            value: $this->cm->name,
            content: new activityname($this->cm),
        );
    }

    /**
     * Retrieves the due date overview for the activity.
     *
     * @return overviewitem|null null if module does not have a due date.
     */
    public function get_due_date_overview(): ?overviewitem {
        return null;
    }

    /**
     * Retrieves the actions overview for the activity.
     *
     * @return overviewitem|null null if module does not have a main action item.
     */
    public function get_actions_overview(): ?overviewitem {
        return null;
    }

    /**
     * Retrieves the completion overview for the activity.
     *
     * @return overviewitem|null null if completion is not enabled.
     */
    public function get_completion_overview(): ?overviewitem {
        global $USER;

        $showcompletionconditions = $this->course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;

        $completiondetails = cm_completion_details::get_instance(
            $this->cm,
            $USER->id,
            $showcompletionconditions
        );

        if (!$completiondetails->is_tracked_user()) {
            return null;
        }

        $showcompletioninfo = $completiondetails->has_completion() &&
            ($showcompletionconditions || $completiondetails->show_manual_completion());

        if (!$showcompletioninfo) {
            return new overviewitem(
                name: get_string('completion_status', 'completion'),
                value: null,
                content: '-',
            );
        }

        $status = $completiondetails->get_overall_completion();

        $completionclass = $this->format->get_output_classname('content\\cm\\completion');
        /** @var \core_courseformat\output\local\content\cm\completion $completion */
        $completion = new $completionclass(
            $this->format,
            $this->cm->get_section_info(),
            $this->cm
        );
        $completion->set_smallbutton(false);

        return new overviewitem(
            name: get_string('completion_status', 'completion'),
            value: $status,
            content: $completion,
        );
    }

    /**
     * Retrieves the grades overview items for the activity.
     *
     * Most activities will have none or one grade. However, some activities
     * may have multiple grades, such as workshop or quiz.
     *
     * It is not recommended to override this method unless the plugin
     * has specific requirements. Instead, plugins should override
     * get_grade_item_names to provide the grade item names.
     *
     * @return overviewitem[] Array of overview items representing the grades.
     */
    public function get_grades_overviews(): array {
        global $CFG, $USER;
        // This overview is to see the own grades, users with full gradebook
        // access will see all grades in the gradebook.
        if (has_capability('moodle/grade:viewall', $this->context)) {
            return [];
        }
        if (!plugin_supports('mod', $this->cm->modname, FEATURE_GRADE_HAS_GRADE, false)) {
            return [];
        }
        require_once($CFG->libdir . '/gradelib.php');

        $items = grade_item::fetch_all([
                'itemtype' => 'mod',
                'itemmodule' => $this->cm->modname,
                'iteminstance' => $this->cm->instance,
                'courseid' => $this->course->id,
        ]);
        if (empty($items)) {
            return [];
        }

        $itemnames = $this->get_grade_item_names($items);
        $result = [];
        foreach ($items as $item) {
            // Plugins may decide to hide a specific grade item by not setting a name.
            if (empty($itemnames[$item->id])) {
                continue;
            }

            $gradegrade = grade_grade::fetch(['itemid' => $item->id, 'userid' => $USER->id]);

            if (
                !$gradegrade
                || ($gradegrade->is_hidden() && !has_capability('moodle/grade:viewhidden', $this->context))
            ) {
                $result[] = new overviewitem(
                    name: $itemnames[$item->id],
                    value: '-',
                    content: '-',
                );
                continue;
            }

            $result[] = new overviewitem(
                name: $itemnames[$item->id],
                value: $gradegrade->finalgrade,
                content: grade_format_gradevalue($gradegrade->finalgrade, $item),
            );
        }
        return $result;
    }

    /**
     * Retrieves the grade item names for the activity.
     *
     * By default, the overview will display the grade if the activities
     * has only one grade item. The name of the grade item will be 'Grade'.
     * For plugins with multiple grade items, the plugin must override this method
     * and provide names for each grade item that want to be displayed.
     *
     * @param grade_item[] $items
     * @return array<integer, string> the grade item names indexed by item id.
     */
    protected function get_grade_item_names(array $items): array {
        if (count($items) == 1) {
            return [reset($items)->id => get_string('gradenoun')];
        }
        return [];
    }
}
