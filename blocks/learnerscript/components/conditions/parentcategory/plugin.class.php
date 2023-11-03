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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;

class plugin_parentcategory extends pluginbase {

    function init() {
        $this->fullname = get_string('parentcategory', 'block_learnerscript');
        $this->type = 'text';
        $this->form = true;
        $this->allowedops = false;
        $this->reporttypes = array('categories');
    }

    function summary($data) {
        global $DB;

        $cat = $DB->get_record('course_categories', array('id' => $data->categoryid));
        if ($cat)
            return format_string(get_string('category') . ' ' . $cat->name);
        else
            return get_string('category') . ' ' . get_string('top');
    }

    // data -> Plugin configuration data
    function execute($data, $user, $courseid) {
        global $DB;
        $categories = $DB->get_records('course_categories', array('parent' => $data->field));
        if ($categories)
            return array_keys($categories);
        return array();
    }

    function columns(){
        $options = array(get_string('top'));
        $parents = array();
        (new ls)->cr_make_categories_list($options, $parents);
        return $options;
    }

}
