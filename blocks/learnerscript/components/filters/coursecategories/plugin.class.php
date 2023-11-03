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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;
use block_learnerscript\local\permissionslib;

class plugin_coursecategories extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filtercoursecategories', 'block_learnerscript');
        $this->reporttypes = array('courses');
        $this->filtertype = 'custom';
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'coursecategories') {
                    $this->filtertype = 'basic';
                }
            }
        }
    }

    public function summary($data) {
        return get_string('filtercoursecategories_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/course/lib.php");
        $category = optional_param('filter_coursecategories', 0, PARAM_INT);
        if (!$category) {
            return $finalelements;
        }

        $displaylist = array();
        $parents = array();
        (new ls)->cr_make_categories_list($displaylist, $parents);

        $coursecache = array();
        foreach ($finalelements as $key => $course) {
            if (empty($coursecache[$course])) {
                $coursecache[$course] = $DB->get_record('course', array('id' => $course));
            }
            $course = $coursecache[$course];
            if ($category != $course->category and ! in_array($category, $parents[$course->id])) {
                unset($finalelements[$key]);
            }
        }

        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;
        require_once($CFG->dirroot . "/course/lib.php");

        $filtercategories = optional_param('filter_coursecategories', 0, PARAM_INT);

        $displaylist = array();
        $notused = array();
        if($selectoption){
            $displaylist[0] = get_string('filter_category', 'block_learnerscript');
        }
        if(!is_siteadmin($this->reportclass->userid) && !(new ls)->is_manager($this->reportclass->userid, $this->reportclass->contextlevel, $this->reportclass->role)){
            if(!empty($this->reportclass->rolewisecourses)){
                $courses = $this->reportclass->rolewisecourses;
                $categories = $DB->get_records_sql_menu("SELECT DISTINCT(cat.id), cat.name FROM {course_categories} AS cat JOIN {course} AS c ON cat.id = c.category
                    WHERE c.id IN ($courses)");
                foreach ($categories as $key => $value) {
                    $displaylist[$key] = $value;
                }
            }
        }else{
            (new ls)->cr_make_categories_list($displaylist, $notused);
        }
        return $displaylist;
    }
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $displaylist = $this->filter_data();

        if ($this->filtertype == 'basic') {
           unset($displaylist[0]); 
        }
        $select = $mform->addElement('select', 'filter_coursecategories', get_string('category'), $displaylist, array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setDefault('filter_coursecategories', 0);
        $mform->setType('filter_coursecategories', PARAM_INT);
    }

}
