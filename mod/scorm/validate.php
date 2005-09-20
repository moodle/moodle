<?php // $Id$

require_once("../../config.php");
require_once("lib.php");

require_login();

$reference = required_param('reference', '', PARAM_PATH);
$courseid = required_param('id', '', PARAM_INT);
$scormid = optional_param('instance','', PARAM_INT);

if (confirm_sesskey() && isteacher($courseid)) {
    $launch = 0;
    if (!empty($scormid)) {
        if (is_file($CFG->dataroot.'/'.$courseid.'/'.$reference)) {
            $fp = fopen($CFG->dataroot.'/'.$courseid.'/'.$reference,"r");
            $fstat = fstat($fp);
            fclose($fp);
            if ($scorm = get_record("scorm","id",$scormid)) {
                $launch = $scorm->launch;
                if ((($scorm->timemodified < $fstat["mtime"]) && ($scorm->reference == $reference)) || ($scorm->reference != $reference)) {
                    // This is a new package
                    $launch = 0;
                } else {
                    // Old package already validated
                    $validation->result = 'found';
                    if (strpos($scorm->version,'AICC') !== false) {
                        $validation->pkgtype = 'AICC';
                    } else {
                        $validation->pkgtype = 'SCORM';
                    }
                }
            } else {
                $validation->result = 'badinstance';
                $launch = -1;
            }
        } else {
            $validation->result = 'badreference';
            $launch = -1;
        }
    }
    //$launch = 0;
    if ($launch == 0) {
        //
        // Package must be validated
        //

        // Create a temporary directory to unzip package and validate package
        $tempdir = '';
        $scormdir = '';
        if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
            if ($tempdir = scorm_datadir($scormdir)) {
                copy ("$CFG->dataroot/$courseid/$reference", $tempdir."/".basename($reference));
                $ext = strtolower(substr(basename($reference),strrpos(basename($reference),'.')));
                if (($ext == '.zip') || ($ext == '.pif')) {
                    unzip_file($tempdir."/".basename($reference), $tempdir, false);
                    unlink ($tempdir."/".basename($reference));
                    $validation = scorm_validate($tempdir);
                } else {
                    $validation->result = "packagefile";
                }
            } else {
                $validation->result = "packagedir";
            }
        } else {
            $validation->result = "datadir";
        }
        if (($validation->result != "regular") && ($validation->result != "found")) {
            $validation->result = get_string($validation->result,'scorm');
            if (is_dir($tempdir)) {
                // Delete files and temporary directory
                scorm_delete_files($tempdir);
            }
        } else {
            $datadir = substr($tempdir,strlen($scormdir));
        }
    }
    //
    // Print validation result
    //
    echo 'result=' . $validation->result . "\n";
    echo 'launch=' . $launch . "\n";
    if (isset($validation->pkgtype)) {
        echo 'pkgtype=' . $validation->pkgtype . "\n";
    }
    if (isset($datadir)) {
        echo 'datadir=' . $datadir . "\n";
    }
    if (isset($validation->errors[1])) {
        echo 'errorlogs='."\n";
        foreach($validation->errors as $error) {
            echo get_string($error->type,"scorm",$error->data) . "\n";
        }
    }
} else {
    echo 'result=' . get_string('badrequest','scorm') . "\n";
}
?>
