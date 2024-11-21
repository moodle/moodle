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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

require_once('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');

$courseid = optional_param('course_id', 0, PARAM_INT);

require_login();

if(!get_config('local_intelliboard', 'enableattendance')){
    throw new moodle_exception('invalidaccess', 'error');
}

if($courseid) {
    $course =$DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $PAGE->set_course($course);
    $PAGE->set_context(context_course::instance($courseid));
    $PAGE->navigation->find(
        'intelliboard_attendance', navigation_node::TYPE_CUSTOM
    )->make_active();
} else {
    $PAGE->set_context(context_system::instance());
}

$PAGE->set_url(new moodle_url("/local/intelliboard/attendance/index.php"));
$PAGE->set_title(get_string('attendance', 'local_intelliboard'));
$PAGE->set_heading(get_string('attendance', 'local_intelliboard'));
$PAGE->navbar->add(get_string('attendance', 'local_intelliboard'));

$params = array(
    'mode'=> 1
);
$intelliboard = intelliboard($params);

echo $OUTPUT->header();

if(!isset($intelliboard) || !$intelliboard->token) {
    echo '<div class="alert alert-error alert-block fade in " role="alert">' . get_string('intelliboardaccess', 'local_intelliboard') . '</div>';
    echo $OUTPUT->footer();
    exit;
}
$contenturl = sprintf('launch.php?sesskey=%s&course_id=%s', sesskey(), $courseid);

// Request the launch content with an iframe tag.
echo "<iframe id='contentframe' style='min-height: 800px;'  height='800px' width='100%' src='{$contenturl}'
     webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";

// Output script to make the iframe tag be as large as possible.
$resize = '
        <script type="text/javascript">
        //<![CDATA[
            YUI().use("node", "event", function(Y) {
                var doc = Y.one("body");
                var frame = Y.one("#contentframe");
                var padding = 15; //The bottom of the iframe wasn\'t visible on some themes. Probably because of border widths, etc.
                var lastHeight;
                var resize = function(e) {
                    var viewportHeight = doc.get("winHeight");
                    if(lastHeight !== Math.min(doc.get("docHeight"), viewportHeight)){
                        frame.setStyle("height", viewportHeight - frame.getY() - padding + "px");
                        lastHeight = Math.min(doc.get("docHeight"), doc.get("winHeight"));
                    }
                };

                resize();

                Y.on("windowresize", resize);
            });
        //]]
        </script>
';

echo $resize;

echo $OUTPUT->footer();