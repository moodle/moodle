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
 * coursetags_add.php
 *
 * @package    core_tag
 * @category   tag
 * @copyright  2007 j.beedell@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/tag:create', $systemcontext);

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$keyword = optional_param('coursetag_new_tag', '', PARAM_TEXT);
$courseid = optional_param('entryid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$keyword = trim(strip_tags($keyword));
if ($keyword and confirm_sesskey()) {

    require_once($CFG->dirroot.'/tag/coursetagslib.php');

    if ($courseid > 0 and $userid > 0) {
        $myurl = 'tag/search.php';
        $keywords = explode(',', $keyword);
        coursetag_store_keywords($keywords, $courseid, $userid, 'default', $myurl);
    }
}

// send back to originating page, where the new tag will be visible in the block
if ($returnurl) {
    redirect($returnurl);
} else {
    $myurl = $CFG->wwwroot.'/';
}

redirect($myurl);
