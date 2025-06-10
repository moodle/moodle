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
 * Class plugin_parentcategory
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_parentcategory extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('parentcategory', 'block_configurable_reports');
        $this->type = 'text';
        $this->form = true;
        $this->reporttypes = ['categories'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $DB;

        $cat = $DB->get_record('course_categories', ['id' => $data->categoryid]);
        if ($cat) {
            return format_string(get_string('category') . ' ' . $cat->name);
        }

        return get_string('category') . ' ' . get_string('top');
    }

    /**
     * Execute
     *
     * @param object $data
     * @return array
     */
    public function execute($data) {
        global $DB, $CFG;
        // Data -> Plugin configuration data.
        require_once($CFG->dirroot . '/course/lib.php');

        if (isset($data->includesubcats)) {
            if ($category = $DB->get_record('course_categories', ['id' => $data->categoryid])) {
                cr_make_categories_list($options, $parents, '', 0, $category);
            } else {
                cr_make_categories_list($options, $parents);
            }
            unset($options[$data->categoryid]);

            return array_keys($options);
        }

        $categories = $DB->get_records('course_categories', ['parent' => $data->categoryid]);
        if ($categories) {
            return array_keys($categories);
        }

        return [];
    }

}
