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

define('NO_MOODLE_COOKIES', true); // session not used here
require_once '../../../config.php';

$id = required_param('id', PARAM_INT); // course id
if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('nocourseid');
}

require_user_key_login('grade/export', $id); // we want different keys for each course

if (empty($CFG->gradepublishing)) {
    print_error('gradepubdisable');
}

$context = get_context_instance(CONTEXT_COURSE, $id);
require_capability('gradeexport/ods:publish', $context);

// use the same page parameters as export.php and append &key=sdhakjsahdksahdkjsahksadjksahdkjsadhksa
require 'export.php';


