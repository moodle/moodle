<?php  // $Id$
    require_once('../../config.php');
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);     // sco ID

    $delayseconds = 2;  // Delay time before sco launch, used to give time to browser to define API

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            error('Course Module ID was incorrect');
        }
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
        if (! $scorm = get_record('scorm', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record('scorm', 'id', $a)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $scorm->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);
    if (!empty($scoid)) {
    //
    // Direct SCO request
    //
        if ($sco = scorm_get_sco($scoid)) {
            if ($sco->launch == '') {
                // Search for the next launchable sco
                if ($scoes = get_records_select('scorm_scoes','scorm='.$scorm->id." AND launch<>'' AND id>".$sco->id,'id ASC')) {
                    $sco = current($scoes);
                }
            }
        }
    }
    //
    // If no sco was found get the first of SCORM package
    //
    if (!isset($sco)) {
        $scoes = get_records_select('scorm_scoes','scorm='.$scorm->id." AND launch<>''",'id ASC');
        $sco = current($scoes);
    }

    if ($sco->scormtype == 'asset') {
       $attempt = scorm_get_last_attempt($scorm->id,$USER->id);
       $element = $scorm->version == 'scorm_13'?'cmi.completion_status':'cmi.core.lesson_status';
       $value = 'completed';
       $result = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
    }
    
    //
    // Forge SCO URL
    //
    $connector = '';
    $version = substr($scorm->version,0,4);
    if ((isset($sco->parameters) && (!empty($sco->parameters))) || ($version == 'AICC')) {
        if (stripos($sco->launch,'?') !== false) {
            $connector = '&';
        } else {
            $connector = '?';
        }
        if ((isset($sco->parameters) && (!empty($sco->parameters))) && ($sco->parameters[0] == '?')) {
            $sco->parameters = substr($sco->parameters,1);
        }
    }
    
    if ($version == 'AICC') {
        if (isset($sco->parameters) && (!empty($sco->parameters))) {
            $sco->parameters = '&'. $sco->parameters;
        }
        $launcher = $sco->launch.$connector.'aicc_sid='.sesskey().'&aicc_url='.$CFG->wwwroot.'/mod/scorm/aicc.php'.$sco->parameters;
    } else {
        if (isset($sco->parameters) && (!empty($sco->parameters))) {
            $launcher = $sco->launch.$connector.$sco->parameters;
        } else {
            $launcher = $sco->launch;
        }
    }
    
    if (scorm_external_link($sco->launch)) {
        // Remote learning activity
        $result = $launcher;
    } else if ($scorm->reference[0] == '#') {
        // Repository
        require_once($repositoryconfigfile);
        $result = $CFG->repositorywebroot.substr($scorm->reference,1).'/'.$sco->launch;
    } else {
        if ((basename($scorm->reference) == 'imsmanifest.xml') && scorm_external_link($scorm->reference)) {
            // Remote manifest
            $result = dirname($scorm->reference).'/'.$launcher;
        } else {
            // Moodle internal package/manifest or remote (auto-imported) package
            if (basename($scorm->reference) == 'imsmanifest.xml') {
                $basedir = dirname($scorm->reference);
            } else {
                $basedir = $CFG->moddata.'/scorm/'.$scorm->id;
            }
            require_once($CFG->libdir.'/filelib.php');
            $result = get_file_url($scorm->course .'/'. $basedir .'/'. $launcher);
        }
    }
?>
<html>
    <head>
        <title>LoadSCO</title>
        <script type="text/javascript">
        //<![CDATA[
            setTimeout('document.location = "<?php echo $result ?>";',<?php echo $delayseconds ?>000);
        //]]>
        </script>
        <noscript>
            <meta http-equiv="refresh" content="<?php echo $delayseconds ?>;url=<?php echo $result ?>" />
        </noscript> 
    </head>
    <body>
        &nbsp;
    </body>
</html>
