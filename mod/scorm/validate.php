<?php // $Id$

    require_once("../../config.php");
    require_once('locallib.php');

    $courseid = required_param('id', PARAM_INT);                  // Course Module ID
    $reference = required_param('reference', PARAM_PATH);         // Package path
    $scormid = optional_param('instance', '', PARAM_INT);         // scorm ID
    $confirmed = optional_param('confirmed', false, PARAM_BOOL);  // This package is changed and some tracks could be lost.
                                                                  // Has the editor confirmed to continue?

    require_login($courseid, false);

if (confirm_sesskey() && !empty($courseid)) {
    $launch = 0;
    $validation = new stdClass();
    $referencefield = $reference;
    if (empty($reference)) {
        $launch = -1;
        $validation->result = "packagefile";
    } else if ($reference[0] == '#') {
        require_once($repositoryconfigfile);
        if ($CFG->repositoryactivate) {
            $referencefield = $reference.'/imsmanifest.xml';
            $reference = $CFG->repository.substr($reference,1).'/imsmanifest.xml';
        } else {
            $launch = -1;
            $validation->result = "packagefile";
        }
    } else if (substr($reference,0,7) != 'http://') {
        $reference = $CFG->dataroot.'/'.$courseid.'/'.$reference;
    }

    if (!empty($scormid)) {  
        //
        // SCORM Update
        //
        if (($launch != -1) && ($fp = fopen($reference,"r"))) {
            //$fp = fopen($reference,"r");
            $fstat = fstat($fp);
            fclose($fp);
            if ($scorm = get_record("scorm","id",$scormid)) {
                if ($scorm->reference[0] == '#') {
                    require_once($repositoryconfigfile);
                    if ($CFG->repositoryactivate) {
                        $oldreference = $CFG->repository.substr($scorm->reference,1).'/imsmanifest.xml';
                    } else {
                        $oldreference = $scorm->reference;
                    }
                } else if (substr($reference,0,7) != 'http://') {
                    $oldreference = $CFG->dataroot.'/'.$courseid.'/'.$scorm->reference;
                }
                $launch = $scorm->launch;
                if ((($scorm->timemodified < $fstat["mtime"]) && ($oldreference == $reference)) || ($oldreference != $reference)) {
                    // This is a new or a modified package
                    if (!$confirmed) {
                        if ($tracks = get_records('scorm_scoes_track','scormid',$scormid)) {
                            $validation->result='confirm';
                            $launch = -1;
                        } else {
                            $launch = 0;
                        }
                    } else {
                        $launch = 0;
                    }
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

        $ext = strtolower(substr(basename($reference),strrpos(basename($reference),'.')));
        switch ($ext) {
            case '.pif':
            case '.zip':
                // Create a temporary directory to unzip package and validate package
                $tempdir = '';
                $scormdir = '';
                if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
                    if ($tempdir = scorm_datadir($scormdir)) {
                        copy ("$reference", $tempdir."/".basename($reference));
                        unzip_file($tempdir."/".basename($reference), $tempdir, false);
                        unlink ($tempdir."/".basename($reference));
                        $validation = scorm_validate($tempdir);
                    } else {
                        $validation->result = "packagedir";
                    }
                } else {
                    $validation->result = "datadir";
                }
            break;
            case '.xml':
                if (basename($reference) == 'imsmanifest.xml') {
                    $validation = scorm_validate(dirname($reference));
                } else {
                    $validation->result = "manifestfile";
                }
            break;
            default: 
                $validation->result = "packagefile";
            break;
        }
        if (($validation->result != "regular") && ($validation->result != "found")) {
            $validation->result = get_string($validation->result,'scorm');
            if (is_dir($tempdir)) {
                // Delete files and temporary directory
                scorm_delete_files($tempdir);
            }
        } else {
            if ($ext == '.xml') {
                $datadir = dirname($referencefield);
            } else {
                $datadir = substr($tempdir,strlen($scormdir));
            }
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
