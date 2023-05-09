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
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['grade_items' => 'gi'];
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
        $this->ungradedcounts = $this->report->ungraded_counts();

        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {

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
                global $PAGE, $CFG;

                $renderer = new \core_renderer($PAGE, RENDERER_TARGET_GENERAL);
                if ($row->itemmodule) {
                    $modinfo = get_fast_modinfo($row->courseid);
                    $instances = $modinfo->get_instances();
                    $cm = $instances[$row->itemmodule][$row->iteminstance];

                    if (file_exists($CFG->dirroot . '/mod/' . $row->itemmodule . '/grade.php')) {
                        $args = ['id' => $cm->id, 'itemnumber' => $row->itemnumber];
                        $url = new \moodle_url('/mod/' . $row->itemmodule . '/grade.php', $args);
                    } else {
                        $url = new \moodle_url('/mod/' . $row->itemmodule . '/view.php', ['id' => $cm->id]);
                    }

                    $imagedata = $renderer->pix_icon('monologo', '', $row->itemmodule, ['class' => 'activityicon']);
                    $purposeclass = plugin_supports('mod', $row->itemmodule, FEATURE_MOD_PURPOSE);
                    $purposeclass .= ' activityiconcontainer';
                    $purposeclass .= ' modicon_' . $row->itemmodule;
                    $imagedata = \html_writer::tag('div', $imagedata, ['class' => $purposeclass]);

                    $dimmed = '';
                    if ($row->hidden) {
                        $dimmed = ' dimmed_text';
                    }
                    $html = \html_writer::start_div('page-context-header' . $dimmed);
                    // Image data.
                    $html .= \html_writer::div($imagedata, 'page-header-image mr-2');
                    $prefix = \html_writer::div(get_string('pluginname', "mod_{$row->itemmodule}"),
                        'text-muted text-uppercase small line-height-3');
                    $name = $prefix . \html_writer::link($url, format_string($cm->name, true));
                    $html .= \html_writer::tag('div', $name, ['class' => 'page-header-headings']);
                } else {
                    // Manual grade item.
                    $gradeitem = grade_item::fetch(['id' => $row->id, 'courseid' => $row->courseid]);
                    if ($row->calculation) {
                        $imagedata = $renderer->pix_icon('i/agg_sum', '');
                    } else {
                        $imagedata = $renderer->pix_icon('i/manual_item', '');
                    }
                    $imagedata = \html_writer::tag('div', $imagedata);

                    $html = \html_writer::start_div('page-context-header');
                    // Image data.
                    $html .= \html_writer::div($imagedata, 'page-header-image mr-2');
                    $html .= \html_writer::tag('div', $gradeitem->get_name(), ['class' => 'page-header-headings']);
                }
                return $html;

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
    protected function get_all_filters(): array {
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
