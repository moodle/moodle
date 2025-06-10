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
 * @package    enrol_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
    **********************************************************
    * This is only a test file and will not be used anywhere *
    **********************************************************
*/

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');

global $CFG;

require_once("$CFG->libdir/clilib.php");

// Require the magicness.
require_once('../classes/d1.php');

$links = linky::get_course_sections();
$labels = linky::get_course_labels();
$forum_posts = linky::get_forum_messages();

class linky {

  public static function get_course_sections() {
    global $DB;
    $sql = 'SELECT id, course, section, summary
            FROM mdl_course_sections
            WHERE summary LIKE "%reg.outreach.lsu.edu%"';

    $datas = $DB->get_records_sql($sql);

    foreach($datas as $data) {

        preg_match_all('/"(http\S+reg.outreach.lsu.edu\S+)"/', $data->summary, $linksarray);

        $links = array_pop($linksarray);

        $link = html_entity_decode($links[0]);
        mtrace("COURSE SECTION, https://cemoodle.online.lsu.edu/course/view.php?id=$data->course#section-$data->section, $link");
    }
  }

  public static function get_course_labels() {
    global $DB;
    $sql = 'SELECT id, course, intro
            FROM mdl_label
            WHERE intro LIKE "%reg.outreach.lsu.edu%"';

    $datas = $DB->get_records_sql($sql);

    foreach($datas as $data) {

        preg_match_all('/"(http\S+reg.outreach.lsu.edu\S+)"/', $data->intro, $linksarray);

        $links = array_pop($linksarray);

        $link = html_entity_decode($links[0]);
        mtrace("LABEL, https://cemoodle.online.lsu.edu/course/view.php?id=$data->course, $link");
    }
  }

  public static function get_forum_messages() {
    global $DB;
    $sql = 'SELECT fp.id,
                   f.course,
                   f.name AS forum_name,
                   CONCAT(u.firstname, " ", u.lastname) AS user_fullname,
                   fd.name AS discussion_name,
                   fp.subject,
                   fp.message
            FROM mdl_forum f
            INNER JOIN mdl_forum_discussions fd ON fd.forum = f.id
            INNER JOIN mdl_forum_posts fp ON fp.discussion = fd.id
            INNER JOIN mdl_user u ON u.id = fp.userid
            WHERE fp.message LIKE "%reg.outreach.lsu.edu%"';

    $datas = $DB->get_records_sql($sql);

    foreach($datas as $data) {

        preg_match_all('/"(http\S+reg.outreach.lsu.edu\S+)"/', $data->message, $linksarray);

        $links = array_pop($linksarray);

        $link = html_entity_decode($links[0]);
        mtrace('FORUM, https://cemoodle.online.lsu.edu/course/view.php?id=' . $data->course . ', ' . $link . ', "' . $data->forum_name . '", "' . $data->user_fullname . '", "' . $data->discussion_name . '", "' . $data->subject . '"');
    }
  }

}

?>
