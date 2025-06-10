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
 * Class plugin_coursecategories
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_coursecategories extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtercoursecategories', 'block_configurable_reports');
        $this->reporttypes = ['courses'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('filtercoursecategories_summary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @return mixed
     */
    public function execute($finalelements) {
        global $remotedb, $CFG;
        require_once($CFG->dirroot . "/course/lib.php");

        $category = optional_param('filter_coursecategories', 0, PARAM_INT);
        if (!$category) {
            return $finalelements;
        }

        $displaylist = [];
        $parents = [];
        cr_make_categories_list($displaylist, $parents);

        $coursecache = [];
        foreach ($finalelements as $key => $course) {
            if (empty($coursecache[$course])) {
                $coursecache[$course] = $remotedb->get_record('course', ['id' => $course]);
            }
            $course = $coursecache[$course];
            if ($category != $course->category && (empty($parents[$course->id]) || !in_array($category, $parents[$course->id]))) {
                unset($finalelements[$key]);
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

        global $CFG;
        require_once($CFG->dirroot . "/course/lib.php");

        $filtercategories = optional_param('filter_coursecategories', 0, PARAM_INT);

        $displaylist = [];
        $notused = [];
        cr_make_categories_list($displaylist, $notused);

        $displaylist[0] = get_string("all");
        $mform->addElement('select', 'filter_coursecategories', get_string('category'), $displaylist, $filtercategories);
        $mform->setDefault('filter_coursecategories', 0);
        $mform->setType('filter_coursecategories', PARAM_INT);
    }

}
