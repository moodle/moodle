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

require_once '../../../config.php';
require_once 'lib.php';
require_once $CFG->libdir.'/filelib.php';

$gradesurl = required_param('url', PARAM_URL); // only real urls here
$id        = required_param('id', PARAM_INT); // course id
$feedback  = optional_param('feedback', 0, PARAM_BOOL);

$url = new moodle_url('/grade/import/xml/import.php', array('id' => $id,'url' => $gradesurl));
if ($feedback !== 0) {
    $url->param('feedback', $feedback);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourseid');
}

require_login($course);
$context = context_course::instance($id);

// Only reachable as the body of fetch.php (key login); reject direct access. MDL-84545.
if (!defined('USER_KEY_LOGIN')) {
    throw new \moodle_exception('invalidaccess', 'error');
}

require_capability('moodle/grade:import', $context);
require_capability('gradeimport/xml:view', $context);

if (gradeimport_xml_fetch_and_commit($course, $gradesurl, $feedback, false)) {
    echo 'ok';
    die;
} else {
    throw new \moodle_exception('cannotimportgrade');
}
