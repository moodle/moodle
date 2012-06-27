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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

require_login();

$source = required_param('source', PARAM_INT);
$target = required_param('target', PARAM_INT);

profile_load_custom_fields($USER);

//get current sortorder
$sortorder = array();
if (isset($USER->profile['myorder'])) {
    $mysortorder = explode(',', $USER->profile['myorder']);
}

$courses_sorted = block_course_overview_get_sorted_courses();
$sortorder = array_keys($courses_sorted);

//now resort based on new weight for chosen course
$neworder = array();
reset($sortorder);
foreach ($sortorder as $key => $value) {
    if ($value == $source) {
        unset($sortorder[$key]);
        break;
    }
}
for ($i = 0; $i <= count($sortorder) + 1; $i++) {
    if ($i == $target) {
        $neworder[] = $source;
    }
    if (isset($sortorder[$i])) {
        $neworder[] = $sortorder[$i];
    }
}
$neworder = implode(',', $neworder);
block_course_overview_update_myorder($neworder);

redirect(new moodle_url('/my/index.php'));
