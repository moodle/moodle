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
echo $OUTPUT->header();
$content = file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $context->id, 'mod_qubitspage', 'content', $page->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $page->contentformat, $formatoptions);
$templatecontext = array(
    "PYODIDE_INDEX_URL" => "https://cdn.jsdelivr.net/pyodide/v0.21.3/full/",
    "tickimgurl" => new moodle_url("/mod/qubitspage/pix/chapter-tick.png"),
    "staticbasic" => new moodle_url("/mod/qubitspage/static/")
);
//echo $OUTPUT->box($content, "generalbox center clearfix", "qubitspage");
$pageslug = trim(strip_tags($page->intro));
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/gh/qubits-platform/sqlite-wasm@master/sqlite3.js"></script>
<script src="https://cdn.jsdelivr.net/pyodide/v0.21.3/full/pyodide.js"></script>
<link rel="preload" href="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/css/764a282bd97c50ec.css" as="style" />
<link rel="stylesheet" href="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/css/764a282bd97c50ec.css" data-n-g="" />
<noscript data-n-css=""></noscript>
<script defer="" nomodule="" src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/polyfills-c67a75d1b6f99dc8.js"></script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/webpack-b6a0d4afbf757f34.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/framework-2c79e2a64abdb08b.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/main-c3c47d789570afbb.js" defer=""></script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/pages/_app-36098dacfc4cdfc8.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/pages/index-6bad1b3ea3f5e85f.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/yo32rx2DlR4M--pvQTybe/_buildManifest.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/yo32rx2DlR4M--pvQTybe/_ssgManifest.js" defer="">

</script>


<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/pages/Chapter2-1a347203221c73c2.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/pages/Chapter3-c56d845d803f27df.js" defer="">
</script>

<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/8a28b14e.76beef9b9875f983.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/163.6a4972002b5c8c3d.js" defer="">
</script>
<script src="<?php echo $CFG->wwwroot; ?>/mod/qubitspage/_next/static/chunks/873.3a28d5c74883a08b.js" defer="">
</script>



<script id="__NEXT_DATA__" type="application/json">
    {
        "props": {
            "pageProps": {}
        },
        "page": "<?php echo $pageslug; ?>",
        "query": {},
        "buildId": "yo32rx2DlR4M--pvQTybe",
        "nextExport": true,
        "autoExport": true,
        "isFallback": false,
        "scriptLoader": []
    }
</script>



<div id="__next"></div>


<?php

if (!isset($options['printlastmodified']) || !empty($options['printlastmodified'])) {
    $strlastmodified = get_string("lastmodified");
    echo html_writer::div("$strlastmodified: " . userdate($page->timemodified), 'modified');
}

echo $OUTPUT->footer();
