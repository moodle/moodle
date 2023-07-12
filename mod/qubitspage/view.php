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
 * Qubits Page module version information
 *
 * @package mod_qubitspage
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/mod/qubitspage/lib.php');
require_once($CFG->dirroot . '/mod/qubitspage/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // Page instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$page = $DB->get_record('qubitspage', array('id' => $p))) {
        throw new \moodle_exception('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('qubitspage', $page->id, $page->course, false, MUST_EXIST);
} else {
    if (!$cm = get_coursemodule_from_id('qubitspage', $id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    $page = $DB->get_record('qubitspage', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/qubitspage:view', $context);

// Completion and trigger events.
qubitspage_view($page, $course, $cm, $context);

$PAGE->set_url('/mod/qubitspage/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? [] : (array) unserialize_array($page->displayoptions);

$activityheader = ['hidecompletion' => false];
if (empty($options['printintro']) || !trim(strip_tags($page->intro))) {
    $activityheader['description'] = '';
}

if ($inpopup and $page->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->add_body_class('limitedwidth');
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($page);
    if (!$PAGE->activityheader->is_title_allowed()) {
        $activityheader['title'] = "";
    }
}
$PAGE->activityheader->set_attrs($activityheader);
$PAGE->set_pagelayout('executablebook');
echo $OUTPUT->header();
$pageslug = trim(strip_tags($page->intro));
$qmurl = $CFG->wwwroot . '/mod/qubitspage';
?>

<div id="__next"></div>
<script id="__NEXT_DATA__" type="application/json">
    {
        "props": {
            "pageProps": {}
        },
        "page": "<?php echo $pageslug; ?>",

        "query": {},
        "buildId": "KBNmaMY8_hUwQkFmcUMeO",
        "nextExport": true,
        "autoExport": true,
        "isFallback": false,
        "scriptLoader": []
    }
</script>
<?php
echo $OUTPUT->footer();
?>