<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_login();
    if ($form = data_submitted($destination)) { 

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        $strediting = get_string("validateascorm", "scorm");
        $strname = get_string("name");

        print_header("$course->shortname: $strediting", "$course->shortname: $strediting",
                      "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> $strediting");

        if (!$form->name or !$form->reference or !$form->summary) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }
	
	//
	// Create a temporary directory to unzip package and validate imsmanifest
	//

	$coursedir = "$CFG->dataroot/$course->id";

	if ($scormdir = make_upload_directory("$course->id/$CFG->moddata/scorm")) {
            if ($tempdir = scorm_datadir($scormdir, $form->datadir)) {
                copy ("$coursedir/$form->reference", $tempdir."/".basename($form->reference));
                if (empty($CFG->unzip)) {    // Use built-in php-based unzip function
                    include_once($CFG->dirroot.'/lib/pclzip/pclzip.lib.php');
                    $archive = new PclZip($tempdir."/".basename($form->reference));
                    if (!$list = $archive->extract($tempdir)) {
                        error($archive->errorInfo(true));
                    }
                } else {
                    $command = "cd $tempdir; $CFG->unzip -o ".basename($form->reference)." 2>&1";
                    exec($command);
                }
                $result = scorm_validate($tempdir."/imsmanifest.xml");
            } else {
                $result = "packagedir";
            }
	} else {
	    $result = "datadir";
	}
	
	if ($result != "regular") {
	    //
	    // Delete files and temporary directory
	    //
	    if (is_dir($tempdir))
        	scorm_delete_files($tempdir);
	} else {
    	    //
	    // Delete package file
	    //
    	    unlink ($tempdir."/".basename($form->reference));
    	    if ($form->mode == "update") {
	    	$fp = fopen($coursedir."/".$form->reference,"r");
		$fstat = fstat($fp);
		fclose($fp);
		if (get_field("scorm","timemodified","id",$form->instance) < $fstat["mtime"])
		    $form->launch = 0;
	    }
    	}
    	//
    	// Print validation result
    	//
    	print_simple_box_start("center", "", "$THEME->cellheading");
    	echo "<table cellpadding=\"5\" align=\"center\">\n";
    	echo "    <tr><td align=\"right\" nowrap><p><b>$strname:</b></p></td><td><p>$form->name</p></a></td></tr>\n";
    	echo "    <tr><td align=\"right\" nowrap><p><b>".get_string("validation","scorm").":</b></p></td><td><p>".get_string($result,"scorm")."</p></a></td></tr>\n";
    	if (($form->mode == "update") && ($form->launch == 0) && (get_records("scorm_sco_user","scormid",$form->instance)))
	    echo "    <tr><td align=\"center\" colspan=\"2\" nowrap><p><b>".get_string("trackingloose","scorm")."</b></p></td></tr>\n";
    	echo "</table>\n";
    	if ($result == "regular") {
	    echo "<form name=\"theform\" method=\"post\" $onsubmit action=\"$form->destination\">\n";
?>
        <input type="hidden" name="reference"	value="<?php p($form->reference) ?>">
        <input type="hidden" name="datadir"	value="<?php p(substr($tempdir,strlen($scormdir))) ?>">
        <input type="hidden" name="summary"	value="<?php p($form->summary) ?>">
        <input type="hidden" name="auto"	value="<?php p($form->auto) ?>">
        <input type="hidden" name="name"	value="<?php p($form->name) ?>">
	<input type="hidden" name="launch"	value="<?php p($form->launch) ?>">
        <input type="hidden" name="course"	value="<?php p($form->course) ?>">
        <input type="hidden" name="coursemodule"	value="<?php p($form->coursemodule) ?>">
        <input type="hidden" name="section"	value="<?php p($form->section) ?>">
        <input type="hidden" name="module"	value="<?php p($form->module) ?>">
        <input type="hidden" name="modulename"	value="<?php p($form->modulename) ?>">
        <input type="hidden" name="instance"	value="<?php p($form->instance) ?>">
        <input type="hidden" name="mode"	value="<?php p($form->mode) ?>">
	<center>
	    <input type="submit" value="<?php print_string("savechanges") ?>">
	    <input type="submit" name=cancel value="<?php print_string("cancel") ?>">
	</center>
        </form>
<?php
    	} else {
?>
	<center>
           <input type="button" value="<?php print_string("continue") ?>" onClick="document.location='<?php echo $CFG->wwwroot ?>/course/view.php?id=<?php echo $course->id ?>';">
        </center>
<?php
	}
	print_simple_box_end();
        print_footer($course);
    } else {
           error("This script was called incorrectly");
    }
?>
