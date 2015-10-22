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

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

$id    = optional_param('id', '', PARAM_INT);    // Course Module ID, or
$a     = optional_param('a', '', PARAM_INT);     // scorm ID
$scoid = required_param('scoid', PARAM_INT);     // sco ID.

$delayseconds = 2;  // Delay time before sco launch, used to give time to browser to define API.

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('scorm', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $scorm = $DB->get_record('scorm', array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else if (!empty($a)) {
    if (! $scorm = $DB->get_record('scorm', array('id' => $a))) {
        print_error('coursemisconf');
    }
    if (! $course = $DB->get_record('course', array('id' => $scorm->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

$PAGE->set_url('/mod/scorm/loadSCO.php', array('scoid' => $scoid, 'id' => $cm->id));

if (!isloggedin()) { // Prevent login page from being shown in iframe.
    // Using simple html instead of exceptions here as shown inside iframe/object.
    echo html_writer::start_tag('html');
    echo html_writer::tag('head', '');
    echo html_writer::tag('body', get_string('loggedinnot'));
    echo html_writer::end_tag('html');
    exit;
}

require_login($course, false, $cm, false); // Call require_login anyway to set up globals correctly.

// Check if SCORM is available.
scorm_require_available($scorm);

$context = context_module::instance($cm->id);

if (!empty($scoid)) {
    // Direct SCO request.
    if ($sco = scorm_get_sco($scoid)) {
        if ($sco->launch == '') {
            // Search for the next launchable sco.
            if ($scoes = $DB->get_records_select(
                    'scorm_scoes',
                    'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true).' AND id > ?',
                    array($scorm->id, $sco->id),
                    'sortorder, id')) {
                $sco = current($scoes);
            }
        }
    }
}

// If no sco was found get the first of SCORM package.
if (!isset($sco)) {
    $scoes = $DB->get_records_select(
        'scorm_scoes',
        'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true),
        array($scorm->id),
        'sortorder, id'
    );
    $sco = current($scoes);
}

if ($sco->scormtype == 'asset') {
    $attempt = scorm_get_last_attempt($scorm->id, $USER->id);
    $element = (scorm_version_check($scorm->version, SCORM_13)) ? 'cmi.completion_status' : 'cmi.core.lesson_status';
    $value = 'completed';
    $result = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
}

// Forge SCO URL.
$connector = '';
$version = substr($scorm->version, 0, 4);
if ((isset($sco->parameters) && (!empty($sco->parameters))) || ($version == 'AICC')) {
    if (stripos($sco->launch, '?') !== false) {
        $connector = '&';
    } else {
        $connector = '?';
    }
    if ((isset($sco->parameters) && (!empty($sco->parameters))) && ($sco->parameters[0] == '?')) {
        $sco->parameters = substr($sco->parameters, 1);
    }
}

if ($version == 'AICC') {
    require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
    $aiccsid = scorm_aicc_get_hacp_session($scorm->id);
    if (empty($aiccsid)) {
        $aiccsid = sesskey();
    }
    $scoparams = '';
    if (isset($sco->parameters) && (!empty($sco->parameters))) {
        $scoparams = '&'. $sco->parameters;
    }
    $launcher = $sco->launch.$connector.'aicc_sid='.$aiccsid.'&aicc_url='.$CFG->wwwroot.'/mod/scorm/aicc.php'.$scoparams;
} else {
    if (isset($sco->parameters) && (!empty($sco->parameters))) {
        $launcher = $sco->launch.$connector.$sco->parameters;
    } else {
        $launcher = $sco->launch;
    }
}

if (scorm_external_link($sco->launch)) {
    // TODO: does this happen?
    $result = $launcher;
} else if ($scorm->scormtype === SCORM_TYPE_EXTERNAL) {
    // Remote learning activity.
    $result = dirname($scorm->reference).'/'.$launcher;
} else if ($scorm->scormtype === SCORM_TYPE_LOCAL && strtolower($scorm->reference) == 'imsmanifest.xml') {
    // This SCORM content sits in a repository that allows relative links.
    $result = "$CFG->wwwroot/pluginfile.php/$context->id/mod_scorm/imsmanifest/$scorm->revision/$launcher";
} else if ($scorm->scormtype === SCORM_TYPE_LOCAL or $scorm->scormtype === SCORM_TYPE_LOCALSYNC) {
    // Note: do not convert this to use get_file_url() or moodle_url()
    // SCORM does not work without slasharguments and moodle_url() encodes querystring vars.
    $result = "$CFG->wwwroot/pluginfile.php/$context->id/mod_scorm/content/$scorm->revision/$launcher";
}

// Trigger a Sco launched event.
$event = \mod_scorm\event\sco_launched::create(array(
    'objectid' => $sco->id,
    'context' => $context,
    'other' => array('instanceid' => $scorm->id, 'loadedcontent' => $result)
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('scorm', $scorm);
$event->add_record_snapshot('scorm_scoes', $sco);
$event->trigger();

header('Content-Type: text/html; charset=UTF-8');

if ($sco->scormtype == 'asset') {
    // HTTP 302 Found => Moved Temporarily.
    header('Location: ' . $result);
    // Provide a short feedback in case of slow network connection.
    echo html_writer::start_tag('html');
    echo html_writer::tag('body', html_writer::tag('p', get_string('activitypleasewait', 'scorm')));
    echo html_writer::end_tag('html');
    exit;
}

// We expect a SCO: select which API are we looking for.
$lmsapi = (scorm_version_check($scorm->version, SCORM_12) || empty($scorm->version)) ? 'API' : 'API_1484_11';

echo html_writer::start_tag('html');
echo html_writer::start_tag('head');
echo html_writer::tag('title', 'LoadSCO');
?>
    <script type="text/javascript">
    //<![CDATA[
    var myApiHandle = null;
    var myFindAPITries = 0;

    function myGetAPIHandle() {
       myFindAPITries = 0;
       if (myApiHandle == null) {
          myApiHandle = myGetAPI();
       }
       return myApiHandle;
    }

    function myFindAPI(win) {
       while ((win.<?php echo $lmsapi; ?> == null) && (win.parent != null) && (win.parent != win)) {
          myFindAPITries++;
          // Note: 7 is an arbitrary number, but should be more than sufficient
          if (myFindAPITries > 7) {
             return null;
          }
          win = win.parent;
       }
       return win.<?php echo $lmsapi; ?>;
    }

    // hun for the API - needs to be loaded before we can launch the package
    function myGetAPI() {
       var theAPI = myFindAPI(window);
       if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined")) {
          theAPI = myFindAPI(window.opener);
       }
       if (theAPI == null) {
          return null;
       }
       return theAPI;
    }

   function doredirect() {
        if (myGetAPIHandle() != null) {
            location = "<?php echo $result ?>";
        }
        else {
            document.body.innerHTML = "<p><?php echo get_string('activityloading', 'scorm');?>" +
                                        "<span id='countdown'><?php echo $delayseconds ?></span> " +
                                        "<?php echo get_string('numseconds', 'moodle', '');?>. &nbsp; " +
                                        "<img src='<?php echo $OUTPUT->pix_url('wait', 'scorm') ?>'></p>";
            var e = document.getElementById("countdown");
            var cSeconds = parseInt(e.innerHTML);
            var timer = setInterval(function() {
                                            if( cSeconds && myGetAPIHandle() == null ) {
                                                e.innerHTML = --cSeconds;
                                            } else {
                                                clearInterval(timer);
                                                document.body.innerHTML = "<p><?php echo get_string('activitypleasewait', 'scorm');?></p>";
                                                location = "<?php echo $result ?>";
                                            }
                                        }, 1000);
        }
    }
    //]]>
    </script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=<?php echo $result ?>" />
    </noscript>
<?php
echo html_writer::end_tag('head');
echo html_writer::tag('body', html_writer::tag('p', get_string('activitypleasewait', 'scorm')), array('onload' => "doredirect();"));
echo html_writer::end_tag('html');
