<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_login();
    
    //
    // Create a temporary directory to unzip package and validate imsmanifest
    //

    $reference = clean_param($_GET["reference"], PARAM_PATH);
    $courseid = $_GET["id"];
    $datadir = '';
    $launch = 0;
    if (isset($_GET["datadir"])) {
        $datadir = $_GET["datadir"];
    }
    $result = '';
    if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
        if ($tempdir = scorm_datadir($scormdir, $datadir)) {
            copy ("$CFG->dataroot/$courseid/$reference", $tempdir."/".basename($reference));
            unzip_file($tempdir."/".basename($reference), $tempdir, false);
            $result = scorm_validate($tempdir."/imsmanifest.xml");
        } else {
            $result = "packagedir";
        }
    } else {
        $result = "datadir";
    }
    $errorlogs = '';
    if (($result != "regular") && ($result != "found")) {
    	$result = get_string($result,'scorm');
        if ($CFG->scorm_validate == 'domxml') {
            foreach ($errors as $error) {
                $errorlogs .= get_string($error->type,"scorm",$error->data) . ".\n";
            }
        }
        //
        // Delete files and temporary directory
        //
        if (is_dir($tempdir))
            scorm_delete_files($tempdir);
        } else {
        //
        // Delete package file
        //
        unlink ($tempdir."/".basename($reference));
        if (isset($_GET["instance"])) {
            $fp = fopen($CFG->dataroot.'/'.$reference,"r");
            $fstat = fstat($fp);
            fclose($fp);
            if ($scorm = get_record("scorm","id",$_GET["instance"])) {
            	$launch = $scorm->launch;
            	if ($scorm->timemodified < $fstat["mtime"]) {
                    $launch = 0;
                }
            }
        }
    }
    //
    // Print validation result
    //
    echo $result . "\n";
    echo $launch . "\n";
    $datadir = substr($tempdir,strlen($scormdir));
    echo $datadir . "\n";
    if ($errorlogs != '') {
	echo $errorlogs;
    }
?>
    