<?php  // $Id$
/// This page prints all instances of attendance in a given course
    error_reporting(E_ALL);
    require("../../config.php");
    require("lib.php");

// if form is being submitted from generated form
if (isset($_POST["course"]))  {
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

  if (!isteacheredit($mod->course)) {
      error("You can't modify this course!");
  }

  $modlib = "lib.php";
  if (file_exists($modlib)) {
      include_once($modlib);
  } else {
      error("This module is missing important code! ($modlib)");
  }

/* // set the information for the new instances
     $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;
     $attendance->day = make_timestamp($attendance->theyear, 
			$attendance->themonth, $attendance->theday); 
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
	 if ($attendance->notes) { 
	 	$attendance->name = $attendance->name . " - " . $attendance->notes;
	 }
*/
  $curdate = make_timestamp($mod->startyear, $mod->startmonth, $mod->startday);
  $stopdate = make_timestamp($mod->endyear, $mod->endmonth, $mod->endday);
  $enddate = $curdate + $mod->numsections * 604800;  
  if ($curdate > $stopdate) {
  	error(get_string("endbeforestart", "attendance"));
	}
  if ($enddate < $curdate) {
  	error(get_string("startafterend", "attendance"));
	}
  if ($stopdate > $enddate) {
      // if stop date is after end of course, just move it to end of course
			$stopdate = $enddate;
	}
  while ($curdate <= $stopdate) {
    $mod->day = $curdate;
    $mod->name=userdate($mod->day, get_string("strftimedate"));
	  if (isset($mod->notes)) {$mod->name = $mod->name . " - " . $mod->notes;}
    switch(userdate($curdate, "%u")) {
      case 1: if (!empty($mod->mon)) {attendance_add_module($mod);}break;
      case 2: if (!empty($mod->tue)) {attendance_add_module($mod);}break;
      case 3: if (!empty($mod->wed)) {attendance_add_module($mod);}break;
      case 4: if (!empty($mod->thu)) {attendance_add_module($mod);}break;
      case 5: if (!empty($mod->fri)) {attendance_add_module($mod);}break;
      case 6: if (!empty($mod->sat)) {attendance_add_module($mod);}break;
      case 7: if (!empty($mod->sun)) {attendance_add_module($mod);}break;
    } // switch
    $curdate = $curdate + 86400; // add one day to the date
  } // while for days
      
  if (!empty($SESSION->returnpage)) {
      $return = $SESSION->returnpage;
      unset($SESSION->returnpage);
      redirect($return);
  } else {
      redirect("index.php?id=$mod->course");
  }
  exit;
  
} else {
/// -----------------------------------------------------------------------------------
/// ------------------ SECTION FOR MAKING THE FORM TO BE POSTED -----------------------
/// -----------------------------------------------------------------------------------

/// @include_once("$CFG->dirroot/mod/attendance/lib.php"); 
/// error_reporting(E_ALL);

        require_variable($id);
        require_variable($section);

        if (! $course = get_record("course", "id", $id)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", "attendance")) {
            error("This module type doesn't exist");
        }

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = "";
        $form->coursemodule = "";
        $form->mode       = "add";

        $sectionname    = get_string("name$course->format");
        $fullmodulename = strtolower(get_string("modulename", $module->name));

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->to   = "$sectionname $form->section";
            $pageheading = get_string("addingmultiple", "attendance");
        } else {
            $pageheading = get_string("addingmultiple", "attendance");
        }

    if (!isteacheredit($course->id)) {
        error("You can't modify this course!");
    }

    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($course->category) {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</A> -> 
                      $streditinga", "form.name", "", false);
    } else {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "$streditinga", "form.name", "", false);
    }

    unset($SESSION->modform); // Clear any old ones that may be hanging around.


        $icon = "<img align=absmiddle height=16 width=16 src=\"$CFG->modpixpath/$module->name/icon.gif\">&nbsp;";

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        print_simple_box_start("center", "", "$THEME->cellheading");

 
/// Print the main part of the page

   // adaptation of mod code to view code needs this:
  @include_once("$CFG->dirroot/mod/attendance/lib.php");
    //require_once("lib.php")
// determine the end date for the course based on the number of sections and the start date
$course->enddate = $course->startdate + $course->numsections * 604800;

if (isset($CFG->attendance_dynsection) && ($CFG->attendance_dynsection == "1")) { $form->dynsection = 1; }
if (isset($CFG->attendance_autoattend) && ($CFG->attendance_autoattend == "1")) { $form->autoattend = 1; }
if (isset($CFG->attendance_grade) && ($CFG->attendance_grade == "1")) { $form->grade = 1; }
$form->maxgrade = isset($CFG->attendance_maxgrade)?$CFG->attendance_maxgrade:0;
$form->hours = isset($CFG->attendance_default_hours)?$CFG->attendance_default_hours:1;

?>
<FORM name="form" method="post" action="<?php echo $ME ?>">
<CENTER>
<INPUT type="submit" value="<?php  print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<?php  print_string("cancel") ?>">
<TABLE cellpadding=5>

<TR valign=top>
    <TD align=right><P><B><?php print_string("startmulti", "attendance") ?>:</B></P></TD>
    <TD colspan="3"><?php print_date_selector("startday", "startmonth", "startyear",$course->startdate) ?></TD>
</TR>
<TR valign=top>
    <TD align=right><P><B><?php print_string("endmulti", "attendance") ?>:</B></P></TD>
    <TD colspan="3"><?php print_date_selector("endday", "endmonth", "endyear",$course->enddate) ?></TD>
</TR>

<TR valign=top>
    <TD align=right><P><B><?php print_string("choosedays", "attendance") ?>:</B></P></TD>
    <TD colspan="3">
    <?php print_string("sunday","attendance"); echo ":"; ?>
    <input type="checkbox" name="sun" >
    <?php print_string("monday","attendance"); echo ":"; ?>
    <input type="checkbox" name="mon" "checked">
    <?php print_string("tuesday","attendance"); echo ":"; ?>
    <input type="checkbox" name="tue" "checked">
    <?php print_string("wednesday","attendance"); echo ":"; ?>
    <input type="checkbox" name="wed" "checked">
    <?php print_string("thursday","attendance"); echo ":"; ?>
    <input type="checkbox" name="thu" "checked">
    <?php print_string("friday","attendance"); echo ":"; ?>
    <input type="checkbox" name="fri" "checked">
    <?php print_string("saturday","attendance"); echo ":"; ?>
    <input type="checkbox" name="sat" >
<?php helpbutton("choosedays", get_string("choosedays","attendance"), "attendance");?>
    </TD>
</TR>

<tr valign=top>
    <TD align="right"><P><B><?php print_string("dynamicsectionmulti", "attendance") ?>:</B></P></TD>
    <TD align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "dynsection", "", "");
        helpbutton("dynsection", get_string("dynamicsectionmulti","attendance"), "attendance");
?>
<!--      <input type="checkbox" name="dynsection" <?php echo !empty($form->dynsection) ? 'checked' : '' ?> > -->
</TD>
</tr>
<tr valign=top>
    <TD align="right"><P><B><?php print_string("autoattendmulti", "attendance") ?>:</B></P></TD>
    <TD align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "autoattend", "", "");
        helpbutton("autoattendmulti", get_string("autoattend","attendance"), "attendance");
?>


<!--      <input type="checkbox" name="autoattend" <?php echo !empty($form->autoattend) ? 'checked' : '' ?> > -->
    </TD>
</tr>
<?php // starting with 2 to allow for the nothing value in choose_from_menu to be the default of 1
for ($i=2;$i<=24;$i++){ $opt[$i] = $i; } ?>
<TR valign=top>
    <TD align=right><P><B><?php print_string("hoursineachclass", "attendance") ?>:</B></P></TD>
    <TD  colspan="3" align="left"><?php choose_from_menu($opt, "hours", $form->hours, "1","","1") ?>
<?php helpbutton("hours", get_string("hoursinclass","attendance"), "attendance"); ?>
</td>
</tr>

<tr valign=top>
    <TD align="right"><P><B><?php print_string("gradevaluemulti", "attendance") ?>:</B></P></TD>
    <TD align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "grade", "", "");
        helpbutton("grade", get_string("gradevalue","attendance"), "attendance");
?>

<!--      <input type="checkbox" name="grade" <?php echo !empty($form->grade) ? 'checked' : '' ?> > -->
    </TD>
</tr>
<?php // starting with 2 to allow for the nothing value in choose_from_menu to be the default of 1
for ($i=0;$i<=100;$i++){ $opt2[$i] = $i; } ?>
<TR valign=top>
    <TD align=right><P><B><?php print_string("maxgradevalue", "attendance") ?>:</B></P></TD>
    <TD  colspan="3" align="left"><?php choose_from_menu($opt2, "maxgrade", $form->maxgrade, "0","","0"); 
   helpbutton("maxgrade", get_string("maxgradevalue","attendance"), "attendance");
?></td>
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
<INPUT type="hidden" name=numsections   value="<?php p($course->numsections) ?>">
<BR />
<INPUT type="submit" value="<?php print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<?php print_string("cancel") ?>">
</CENTER>
</FORM>

<?php
    print_simple_box_end();
/// Finish the page
    print_footer($course);
    }

?>
