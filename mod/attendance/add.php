<?php  // $Id$
/// This page prints all instances of attendance in a given course
    error_reporting(E_ALL);
    require("../../config.php");
    require("lib.php");

if (isset($_POST["course"]))  {

echo "<html><body><pre>";
echo "submitted form with these values:\n";

    var_dump($_POST);

echo "</pre></body></html>";
/*

    require_login();
/// -----------------------------------------------------------------------------------
/// --------------------SECTION FOR PROCESSING THE FORM ON POST -----------------------
/// -----------------------------------------------------------------------------------
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $mod = $SESSION->modform;
        unset($SESSION->modform);
    } else {
        $mod = (object)$_POST;
    }


    if (isset($cancel)) {  
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
    }


    if (isset($_POST["course"])) {    // add or update form submitted

        if (!isteacheredit($mod->course)) {
            error("You can't modify this course!");
        }

        $modlib = "../mod/$mod->modulename/lib.php";
        if (file_exists($modlib)) {
            include_once($modlib);
        } else {
            error("This module is missing important code! ($modlib)");
        }
        $addinstancefunction    = $mod->modulename."_add_instance";
        $updateinstancefunction = $mod->modulename."_update_instance";
        $deleteinstancefunction = $mod->modulename."_delete_instance";

        switch ($mod->mode) {
            case "update":
                if (! $updateinstancefunction($mod)) {
                    error("Could not update the $mod->modulename");
                }
                add_to_log($mod->course, "course", "update mod", 
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule", 
                           "$mod->modulename $mod->instance"); 
                break;

            case "add":
                if (! $mod->instance = $addinstancefunction($mod)) {
                    error("Could not add a new instance of $mod->modulename");
                }
                // course_modules and course_sections each contain a reference 
                // to each other, so we have to update one of them twice.

                if (! $mod->coursemodule = add_course_module($mod) ) {
                    error("Could not add a new course module");
                }
                if (! $sectionid = add_mod_to_section($mod) ) {
                    error("Could not add the new course module to that section");
                }
                if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                    error("Could not update the course module with the correct section");
                }   
                add_to_log($mod->course, "course", "add mod", 
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule", 
                           "$mod->modulename $mod->instance"); 
                break;
            case "delete":
                if (! $deleteinstancefunction($mod->instance)) {
                    notify("Could not delete the $mod->modulename (instance)");
                }
                if (! delete_course_module($mod->coursemodule)) {
                    notify("Could not delete the $mod->modulename (coursemodule)");
                }
                if (! delete_mod_from_section($mod->coursemodule, "$mod->section")) {
                    notify("Could not delete the $mod->modulename from that section");
                }
                add_to_log($mod->course, "course", "delete mod", 
                           "view.php?id=$mod->course", 
                           "$mod->modulename $mod->instance"); 
                break;
            default:
                error("No mode defined");

        }

        rebuild_course_cache($mod->course);

        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
        exit;
    }
*/


} else {
/// -----------------------------------------------------------------------------------
/// ------------------ SECTION FOR MAKING THE FORM TO BE POSTED -----------------------
/// -----------------------------------------------------------------------------------

/// @include_once("$CFG->dirroot/mod/attendance/lib.php"); 
/// error_reporting(E_ALL);

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // attendance ID

/// populate the appropriate objects
    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course is misconfigured");
        }
        if (! $attendance = get_record("attendance", "course", $id)) {
            error("Course module is incorrect");
        }
/*        if (! $cm = get_coursemodule_from_instance("attendance", $attendance->id, $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $attendances = get_records("attendance", "course", $cm->course)) {
            error("Course module is incorrect");
        }
*/
    } else {
        if (! $attendance = get_record("attendance", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $attendance->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("attendance", $attendance->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        if (! $attendances = get_records("attendance", "course", $cm->course)) {
            error("Course module is incorrect");
        }
    }

    require_login($course->id);

    add_to_log($course->id, "attendance", "add", "add.php?id=$course->id");

/// Print the page header
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strattendances = get_string("modulenameplural", "attendance");
    $strattendance  = get_string("modulename", "attendance");
    $straddmultiple  = get_string("addmultiple", "attendance");
    $strallattendance = get_string("allmodulename", "attendance");
    
    print_header("$course->shortname: $strallattendance", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strattendances</A> -> $straddmultiple", 
                  "", "", true, "&nbsp;", 
                  navmenu($course));
 
/// Print the main part of the page

   // adaptation of mod code to view code needs this:
   $form = $attendance;

   if (!isteacher($course->id)) {
     notice(get_string("cantadd", "attendance"));
     print_footer($course); exit;
   }
//choose the start date
//choose the end date
//OR 
//for the whole course
//choose the days of the week you want attendance 
  @include_once("$CFG->dirroot/mod/attendance/lib.php");
    //require_once("lib.php")
?>
<FORM name="form" method="post" action="<?=$ME ?>">
<CENTER>
<INPUT type="submit" value="<? print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<? print_string("cancel") ?>">
<TABLE cellpadding=5>

<!-- <? $options[0] = get_string("no"); $options[1] = get_string("yes"); ?> -->
<!-- <TR valign=top> -->
<!--     <TD align=right><P><B><? print_string("takeroll", "attendance") ?>:</B></P></TD> -->
<!--     <TD align=left><? choose_from_menu($options, "roll", $form->roll, "") ?></td> -->
<!-- </tr> -->

<TR valign=top>
    <TD align=right><P><B><?php print_string("dayofroll", "attendance") ?>:</B></P></TD>
    <TD colspan="3"><?php print_date_selector("theday", "themonth", "theyear", $form->day) ?></TD>
</TR>
<tr valign=top>
    <TD align="right"><P><B><?php print_string("dynamicsection", "attendance") ?>:</B></P></TD>
    <TD align="left">
      <input type="checkbox" name="dynsection" <?php echo !empty($form->dynsection) ? 'checked' : '' ?> >
    </TD>
</tr>
<?php // starting with 2 to allow for the nothing value in choose_from_menu to be the default of 1
for ($i=2;$i<=24;$i++){ $opt[$i] = $i; } ?>
<TR valign=top>
    <TD align=right><P><B><?php print_string("hoursinclass", "attendance") ?>:</B></P></TD>
    <TD  colspan="3" align="left"><?php choose_from_menu($opt, "hours", $form->hours, "1","","1") ?></td>
</tr>
<tr valign=top>
    <td align=right><p><b><?php print_string("notes", "attendance") ?>:</b></p></td>
    <td colspan="3">
        <input type="text" name="notes" size=60 value="<?php p($form->notes) ?>">
    </td>
</tr>
</TABLE>
<!-- These hidden variables are always the same -->
<INPUT type="hidden" name=course        value="<?php p($form->course) ?>">
<INPUT type="hidden" name=coursemodule  value="<?php p($form->coursemodule) ?>">
<INPUT type="hidden" name=section       value="<?php p($form->section) ?>">
<INPUT type="hidden" name=module        value="<?php p($form->module) ?>">
<INPUT type="hidden" name=modulename    value="<?php p($form->modulename) ?>">
<INPUT type="hidden" name=instance      value="<?php p($form->instance) ?>">
<INPUT type="hidden" name=mode          value="<?php p($form->mode) ?>">
<BR />
<INPUT type="submit" value="<?php print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<?php print_string("cancel") ?>">
</CENTER>
</FORM>

<?php
/// Finish the page
    print_footer($course);
    }

?>