<?php // $Id$

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

        print_header_simple("$strediting", "$strediting",
                      "$strediting");

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
    $errorlogs = '';
    if (($result != "regular") && ($result != "found")) {
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
        echo "    <tr><td align=\"right\" nowrap=\"nowrap\"><p><b>$strname:</b></p></td><td><p>$form->name</p></a></td></tr>\n";
        echo "    <tr><td align=\"right\" nowrap=\"nowrap\"><p><b>".get_string("validation","scorm").":</b></p></td><td><p>".get_string($result,"scorm")."</p></a></td></tr>\n";
        if ($errorlogs != '') {
            $lines = round(count($errors)/4);
            if ($lines < 5) {
                $lines = 5;
            }
            echo "    <tr><td align=\"right\" nowrap=\"nowrap\"><p><b>".get_string("errorlogs","scorm").":</b></p></td><td><textarea rows=\"".$lines."\" cols=\"30\" readonly>".$errorlogs."</textarea></a></td></tr>\n";
        }
        if (($form->mode == "update") && ($form->launch == 0) && (get_records("scorm_sco_users","scormid",$form->instance)))
        echo "    <tr><td align=\"center\" colspan=\"2\" nowrap=\"nowrap\"><p><b>".get_string("trackingloose","scorm")."</b></p></td></tr>\n";
        echo "</table>\n";
        if (($result == "regular") || ($result == "found")){
            if (empty($form->auto)) {
        $form->auto = "";
            }
            if (empty($form->maxgrade)) {
        $form->maxgrade = "";
            }
            if (empty($form->grademethod)) {
        $form->grademethod = "0";
            }
        echo "<form name=\"theform\" method=\"post\" action=\"$form->destination\">\n";
        
        //$form->popup = $CFG->scorm_popup;
        $strnewwindow     = get_string("newwindow", "scorm");
            $strnewwindowopen = get_string("newwindowopen", "scorm");
        foreach ($SCORM_WINDOW_OPTIONS as $optionname) {
            $stringname = "str$optionname";
            $$stringname = get_string("new$optionname", "scorm");
            $window->$optionname = "";
            $jsoption[] = "\"$optionname\"";
            }
            $alljsoptions = implode(",", $jsoption);
        
            if ($form->instance) {     // Re-editing
            if ($form->popup == "") {
                    $newwindow = "";   // Disable the new window
                    foreach ($SCORM_WINDOW_OPTIONS as $optionname) {
                        $defaultvalue = "scorm_popup$optionname";
                        $window->$optionname = $CFG->$defaultvalue;
                }
            } else {
                    $newwindow = "checked";
                    $rawoptions = explode(',', $form->popup); 
                    foreach ($rawoptions as $rawoption) {
                    $option = explode('=', trim($rawoption));
                    if (($option[0] != 'location') && ($option[0] != 'menubar') && ($option[0] != 'toolbar')) {
                        $optionname = $option[0];
                        $optionvalue = $option[1];
                        if ($optionname == "height" or $optionname == "width") {
                            $window->$optionname = $optionvalue;
                        } else if ($optionvalue == 1) {
                            $window->$optionname = "checked";
                        }
                    }
                    }
            }
            } else {
                foreach ($SCORM_WINDOW_OPTIONS as $optionname) {
                    $defaultvalue = "scorm_popup$optionname";
                    $window->$optionname = $CFG->$defaultvalue;
            }
            $newwindow = $CFG->scorm_popup;
            }
        
?>
    <table cellpadding="5" align="center">
      <tr valign="top">
            <td align="right"><p><b><?php print_string("grademethod", "scorm") ?>:</b></p></td>
            <td>
              <?php
            $options = array();
            $options[0] = get_string("gradescoes", "scorm");
            $options[1] = get_string("gradehighest", "scorm");
            $options[2] = get_string("gradeaverage", "scorm");
            choose_from_menu($SCORM_GRADE_METHOD, "grademethod", "$form->grademethod", "");
            helpbutton("grademethod", get_string("grademethod","scorm"), "scorm");
              ?>
            </td>
      </tr>
      <tr valign="top">
            <td align="right"><p><b><?php print_string("maximumgrade") ?>:</b></p></td>
            <td>
              <?php
            for ($i=100; $i>=1; $i--) {
                    $grades[$i] = $i;
            }

            choose_from_menu($grades, "maxgrade", "$form->maxgrade", "");
            helpbutton("maxgrade", get_string("maximumgrade"), "scorm");
              ?>
            </td>
      </tr>
      <tr valign="top">
        <td align="right"><p><b><?php print_string("autocontinue","scorm") ?>:</b></p></td>
        <td>
        <?php
            $options = array();
            $options[0]=get_string("no");
            $options[1]=get_string("yes");
            choose_from_menu ($options, "auto", $form->auto);
        ?>
        </td>
      </tr>
      <tr valign="top">
            <td align="right" nowrap="nowrap">
                <p><b><?php p($strnewwindow) ?></b></p>
            </td>
            <td>
                <script>
                    var subitems = [<?php echo $alljsoptions; ?>];
                    
                    function autowindow() {
                        if (document.theform.newwindow.checked) 
                            document.theform.auto.disabled=true;
                        else
                            document.theform.auto.disabled=false;
                    }
                    
                    <?php
                        if ($newwindow == "checked")
                            echo "document.theform.auto.disabled=true;\n";
                    ?>
                </script>
                <input name="setnewwindow" type="hidden" value="1" />
                <input name="newwindow" type="checkbox" value="1" <?php p($newwindow) ?> onclick="autowindow();return lockoptions('theform','newwindow', subitems);" /> 
                <?php p($strnewwindowopen) ?>
                <ul>
                <?php
                    foreach ($window as $name => $value) {
                        if ($name == "height" or $name == "width") {
                            continue;
                        }
                        echo "\t\t<input name=\"h$name\" type=\"hidden\" value=\"0\" />\n";
                        echo "\t\t<input name=\"$name\" type=\"checkbox\" value=\"1\" ".$window->$name." /> ";
                        $stringname = "str$name";
                        echo $$stringname."<br />\n";
                     }
                ?>

                <input name="hwidth" type="hidden" value="0" />
                <input name="width" type="text" size="4" value="<?php p($window->width) ?>" /> <?php p($strwidth) ?><br />
                <input name="hheight" type="hidden" value="0" />
                <input name="height" type="text" size="4" value="<?php p($window->height) ?>" /> <?php p($strheight) ?><br />
                 <?php
                     if (!$newwindow) {
                         echo "<script>\n<!--\n";
                         echo "\tlockoptions('theform','newwindow', subitems);";
                         echo "\n-->\n</script>";
                     }
                 ?>
                 </ul>
                 </p>
             </td>
           </tr>
        </table>
        <input type="hidden" name="reference"   value="<?php p($form->reference) ?>" />
        <input type="hidden" name="datadir" value="<?php p(substr($tempdir,strlen($scormdir))) ?>" />
        <input type="hidden" name="summary" value="<?php p($form->summary) ?>" />
        <input type="hidden" name="name"    value="<?php p($form->name) ?>" />
    <input type="hidden" name="launch"  value="<?php p($form->launch) ?>" />
        <input type="hidden" name="course"  value="<?php p($form->course) ?>" />
        <input type="hidden" name="coursemodule"    value="<?php p($form->coursemodule) ?>" />
        <input type="hidden" name="section" value="<?php p($form->section) ?>" />
        <input type="hidden" name="module"  value="<?php p($form->module) ?>" />
        <input type="hidden" name="modulename"  value="<?php p($form->modulename) ?>" />
        <input type="hidden" name="instance"    value="<?php p($form->instance) ?>" />
        <input type="hidden" name="mode"    value="<?php p($form->mode) ?>" />
    <div align="center">
        <input type="submit" value="<?php print_string("savechanges") ?>" />
        <input type="submit" name="cancel" value="<?php print_string("cancel") ?>" />
    </div>
        </form>
<?php
        } else {
?>
    <center>
           <input type="button" value="<?php print_string("continue") ?>" onClick="document.location='<?php echo $CFG->wwwroot ?>/course/view.php?id=<?php echo $course->id ?>';" />
        </center>
<?php
    }
    print_simple_box_end();
        print_footer($course);
    } else {
           error("This script was called incorrectly");
    }
?>
