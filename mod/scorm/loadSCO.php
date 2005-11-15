<?php
    require_once("../../config.php");
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', '', PARAM_INT); // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode

    if (!empty($id)) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);
    if (!empty($scoid)) {
    //
    // Direct sco request
    //
        if ($sco = get_record("scorm_scoes","id",$scoid)) {
            if ($sco->launch == '') {
                // Search for the next launchable sco
                if ($scoes = get_records_select("scorm_scoes","scorm=".$scorm->id." AND launch<>'' AND id>".$sco->id,"id ASC")) {
                    $sco = current($scoes);
                }
            }
        }
    } else {
    //
    // Search for first incomplete sco
    //
        if ( $scoes_track = get_records_select("scorm_scoes_track","userid=".$USER->id." AND element='cmi.core.lesson_status' AND scormid=".$scorm->id,"scoid ASC") ) {
            $sco_track = current($scoes_track);
            while ((($sco_track->value == "completed") || ($sco_track->value == "passed") || ($sco_track->value == "failed")) && ($mode == "normal")) {
                $sco_track = next($scoes_track);
            }
            $sco = get_record("scorm_scoes","id",$sco_track->scoid);
        }
    }
    //
    // If no sco was found get the first of SCORM package
    //
    if (!isset($sco)) {
        $scoes = get_records_select("scorm_scoes","scorm=".$scorm->id." AND launch<>''","id ASC");
        $sco = current($scoes);
    }

    //
    // Forge SCO URL
    //
    $connector = '';
    $version = substr($scorm->version,0,4);
    if (!empty($sco->parameters) || ($version == 'AICC')) {
        if (stripos($sco->launch,'?') !== false) {
            $connector = '&';
        } else {
            $connector = '?';
        }
        if (!empty($sco->parameters) && ($sco->parameters[0] == '?')) {
            $sco->parameters = substr($sco->parameters,1);
        }
    }
    
    if ($version == 'AICC') {
        if (!empty($sco->parameters)) {
            $sco->parameters = '&'. $sco->parameters;
        }
        $launcher = $sco->launch.$connector.'aicc_sid='.sesskey().'&aicc_url='.$CFG->wwwroot.'/mod/scorm/aicc.php'.$sco->parameters;
    } else {
        $launcher = $sco->launch.$connector.$sco->parameters;
    }
    
    if (scorm_external_link($sco->launch)) {
        $result = $launcher;
    } else {
        if (basename($scorm->reference) == 'imsmanifest.xml') {
            $basedir = dirname($scorm->reference);
        } else {
            $basedir = 'moddata/scorm/'.$scorm->id;
        }
        if ($CFG->slasharguments) {
            $result = $CFG->wwwroot.'/file.php/'.$scorm->course.'/'.$basedir.'/'.$launcher;
        } else {
            $result = $CFG->wwwroot.'/file.php?file=/'.$scorm->course.'/'.$basedir.'/'.$launcher;
        }
    }
?>
<html>
    <head>
        <title>LoadSCO</title>
        <script language="javascript" type="text/javascript">
        <!--
            setTimeout('document.location = "<?php echo $result ?>";',1000);
        -->
        </script>
        <noscript>
            <meta http-equiv="refresh" content="1;url=<?php echo $result ?>" />
        </noscript> 
    </head>
    <body>
        &nbsp;
    </body>
</html>
