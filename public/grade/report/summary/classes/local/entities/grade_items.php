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

namespace gradereport_summary\local\entities;

use core_reportbuilder\local\filters\select;
use grade_item;
use grade_plugin_return;
use grade_report_summary;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/grade/report/summary/lib.php');
require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Grade summary entity class implementation
 *
 * @package    gradereport_summary
 * @copyright  2022 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_items extends base {

    /** @var stdClass Course */
    public $course;

    /** @var grade_report_summary Grade report. */
    public $report;

    /** @var array Ungraded grade items counts with sql info. */
    public $ungradedcounts;

    /**
     * Entity constructor
     *
     * @param stdClass $course
     */
    public function __construct(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'grade_items',
        ];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('gradeitem', 'grades');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $context = \context_course::instance($this->course->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'summary',
                'course' => $this->course,
            ]
        );

        $this->report = new grade_report_summary($this->course->id, $gpr, $context);
        $showonlyactiveenrol = $this->report->show_only_active();
        $this->ungradedcounts = $this->report->ungraded_counts(false, false, $showonlyactiveenrol);

        return parent::initialise();
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {

        $tablealias = $this->get_table_alias('grade_items');
        $selectsql = "$tablealias.id, $tablealias.itemname, $tablealias.iteminstance, $tablealias.calculation,
         $tablealias.itemnumber, $tablealias.itemmodule, $tablealias.hidden, $tablealias.courseid";

        // Grade item name column.
        $columns[] = (new column(
            'name',
            null,
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields($selectsql)
            ->add_callback(static function($value, $row): string {
                $gradeitem = grade_item::fetch(['id' => $row->id, 'courseid' => $row->courseid]);
                $element = ['type' => 'item', 'object' => $gradeitem, 'modinfo' => get_fast_modinfo($row->courseid)];
                $fullname = \grade_helper::get_element_header($element, true, false, true, true, true);

                $icon = \grade_helper::get_element_icon($element);
                $elementtype = \grade_helper::get_element_type_string($element);
                $itemtype = \html_writer::span($elementtype, 'd-block text-uppercase small dimmed_text',
                    ['title' => $elementtype]);
                $content = \html_writer::div($itemtype . $fullname);
                $dimmed = '';
                if ($row->hidden) {
                    $dimmed = ' dimmed_text';
                }
                return \html_writer::div($icon . $content, "item d-flex align-items-center" . $dimmed);
            });

        $report = [
            'report' => $this->report,
            'ungradedcounts' => $this->ungradedcounts
        ];

        // Average column.
        $columns[] = (new column(
            'average',
            new lang_string('average', 'grades'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("$tablealias.id")
            ->add_callback(static function($value) use ($report): string {

                $gradeitem = grade_item::fetch(['id' => $value]);
                if (!empty($gradeitem->avg)) {
                    $averageformatted = '-';
                }

                if ($gradeitem->needsupdate) {
                    $averageformatted = get_string('error');
                }

                if (empty($averageformatted)) {
                    $ungradedcounts = $report['ungradedcounts'];
                    $aggr = $report['report']->calculate_average($gradeitem, $ungradedcounts);

                    if (empty($aggr['average'])) {
                        $averageformatted = '-';
                    } else {
                        $averagesdisplaytype = $ungradedcounts['report']['averagesdisplaytype'];
                        $averagesdecimalpoints = $ungradedcounts['report']['averagesdecimalpoints'];
                        $shownumberofgrades = $ungradedcounts['report']['shownumberofgrades'];

                        // Determine which display type to use for this average.
                        // No ==0 here, please resave the report and user preferences.
                        if ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) {
                            $displaytype = $gradeitem->get_displaytype();
                        } else {
                            $displaytype = $averagesdisplaytype;
                        }

                        // Override grade_item setting if a display preference (not inherit) was set for the averages.
                        if ($averagesdecimalpoints == GRADE_REPORT_PREFERENCE_INHERIT) {
                            $decimalpoints = $gradeitem->get_decimals();
                        } else {
                            $decimalpoints = $averagesdecimalpoints;
                        }

                        $gradehtml = grade_format_gradevalue($aggr['average'],
                            $gradeitem, true, $displaytype, $decimalpoints);

                        if ($shownumberofgrades) {
                            $numberofgrades = $aggr['meancount'];
                            $gradehtml .= " (" . $numberofgrades . ")";
                        }
                        $averageformatted = $gradehtml;

                    }
                }
                return $averageformatted;
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $tablealias = $this->get_table_alias('grade_items');

        // Activity type filter (for performance only load options on demand).
        $filters[] = (new filter(
            select::class,
            'name',
            new lang_string('activitytype', 'format_singleactivity'),
            $this->get_entity_name(),
            "coalesce({$tablealias}.itemmodule,{$tablealias}.itemtype)"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback([$this->report, 'item_types']);

        return $filters;
    }
}
