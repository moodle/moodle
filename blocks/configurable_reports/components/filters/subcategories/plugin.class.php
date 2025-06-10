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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_subcategories
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_subcategories extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtersubcategories', 'block_configurable_reports');
        $this->reporttypes = ['categories', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filtersubcategories_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return array|string|string[]
     */
    public function execute($finalelements) {
        $filtersubcategories = optional_param('filter_subcategories', 0, PARAM_INT);
        if (!$filtersubcategories) {
            return $finalelements;
        }

        if ($this->report->type !== 'sql') {
            return [$filtersubcategories];
        } else {
            if (preg_match("/%%FILTER_SUBCATEGORIES:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' LIKE CONCAT( \'%/\', ' . $filtersubcategories . ', \'%\' ) ';

                return str_replace('%%FILTER_SUBCATEGORIES:' . $output[1] . '%%', $replace, $finalelements);
            }
        }

        return $finalelements;
    }

    /**
     * Print filter
     *
     * @param MoodleQuickForm $mform
     * @param bool|object $formdata
     * @return void
     */
    public function print_filter(MoodleQuickForm $mform, $formdata = false): void {

        global $remotedb, $CFG;

        $filtersubcategories = optional_param('filter_subcategories', 0, PARAM_INT);

        $reportclassname = 'report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        $courseoptions = [];
        $courseoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if ($this->report->type !== 'sql') {
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $subcategorieslist = $reportclass->elements_by_conditions($conditions);

            if (!empty($subcategorieslist)) {
                [$usql, $params] = $remotedb->get_in_or_equal($subcategorieslist);
                $subcategories = $remotedb->get_records_select('course_categories', "id $usql", $params);

                foreach ($subcategories as $c) {
                    $courseoptions[$c->id] = format_string($c->name);
                }
            }
        } else {
            if (class_exists('core_course_category')) {
                $subcategorieslist = core_course_category::make_categories_list();
            } else {
                require_once($CFG->libdir . '/coursecatlib.php');
                $subcategorieslist = coursecat::make_categories_list();
            }

            if (!empty($subcategorieslist)) {
                $courseoptions += $subcategorieslist;
            }
        }

        $mform->addElement('select', 'filter_subcategories', get_string('category'), $courseoptions);
        $mform->setType('filter_subcategories', PARAM_INT);
    }

}
