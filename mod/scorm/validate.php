<?php // $Id$

require_once("../../config.php");
require_once("lib.php");

require_login();

if (confirm_sesskey()) {
    $reference = clean_param($_GET["reference"], PARAM_PATH);
    $courseid = $_GET["id"];
    $datadir = '';
    if (isset($_GET["datadir"])) {
        $datadir = $_GET["datadir"];
    }
    
    $scormid = 0;
    $launch = 0;
    $result = '';
    $errorlogs = '';
    if (isset($_GET["instance"])) {
	$scormid = $_GET["instance"];
	$launch = 1;
    	$fp = fopen($CFG->dataroot.'/'.$courseid.'/'.$reference,"r");
    	$fstat = fstat($fp);
    	fclose($fp);
    	if ($scorm = get_record("scorm","id",$scormid)) {
    	    if ((($scorm->timemodified < $fstat["mtime"]) && ($scorm->reference == $reference)) || ($scorm->reference != $reference)) {
    	    	// This is a new package
		$launch = 0;
    	    } else {
    	    	// Old package already validated
		$result = 'found';
    	    }
    	}
    }
    if ($launch == 0) {
    	//
    	// Package must be validated
    	//
	
        // Create a temporary directory to unzip package and validate imsmanifest
        $tempdir = '';
        $scormdir = '';
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
	if (($result != "regular") && ($result != "found")) {
	    // Generate error log string
	    $result = get_string($result,'scorm');
	    if ($CFG->scorm_validate == 'domxml') {
	        foreach ($errors as $error) {
		    $errorlogs .= get_string($error->type,"scorm",$error->data) . ".\n";
 		}
	    }
            if (is_dir($tempdir)) {
                // Delete files and temporary directory
                scorm_delete_files($tempdir);
            } else {
                // Delete package file
                unlink ($tempdir."/".basename($reference));
            }
        } else {
            $datadir = substr($tempdir,strlen($scormdir));
        }
    }
    //
    // Print validation result
    //
    echo $result . "\n";
    echo $launch . "\n";
    echo $datadir . "\n";
    if ($errorlogs != '') {
	echo $errorlogs;
    }
} else {
   print_string('badrequest','scorm');
}
?>
    