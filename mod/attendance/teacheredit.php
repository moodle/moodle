<!-- This page defines the form to create or edit an instance of the attendance module -->
<!-- It is used from /course/mod.php.  The whole instance is available as $form. -->

<!-- RJJ I'm using inline CSS styles for some stuff in this page because I want to centralize -->
<!-- the logic and styles in a single directory -->
<?php   require_once("../../config.php");
        require_once("lib.php" );


        @include_once("$CFG->dirroot/course/lib.php");
    //require_once("lib.php")


// this is code from course/mod.php

        if (! $cm = get_record("course_modules", "id", $_GET['update'])) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        if (!isteacher($course->id)) {
            error("You can't modify this record!");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $form = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }
        
        if (! $cw = get_record("course_sections", "id", $cm->section)) {
            error("This course section doesn't exist");
        }

        if (isset($return)) {  
            $SESSION->returnpage = "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id";
        }

        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;     // The section ID
        $form->course       = $course->id;
        $form->module       = $module->id;
        $form->modulename   = $module->name;
        $form->instance     = $cm->instance;
        $form->mode         = "update";

        $sectionname    = get_string("name$course->format");
        $fullmodulename = strtolower(get_string("modulename", $module->name));

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("updatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("updatinga", "moodle", $fullmodulename);
        }

        


    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($module->name == "label") {
        $focuscursor = "";
    } else {
        $focuscursor = "form.name";
    }

    if ($course->category) {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</A> -> 
                      $streditinga", $focuscursor, "", false);
    } else {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "$streditinga", $focuscursor, "", false);
    }

        $icon = "<img align=absmiddle height=16 width=16 src=\"$CFG->modpixpath/$module->name/icon.gif\">&nbsp;";

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        print_simple_box_start("center", "", "$THEME->cellheading");



// this information is included below instead
//        include_once($modform);






// error_reporting(E_ALL);
 // if we're adding a new instance
if (empty($form->id)) {
 	if (isset($CFG->attendance_dynsection) && ($CFG->attendance_dynsection == "1")) { $form->dynsection = 1; }
 	if (isset($CFG->attendance_autoattend) && ($CFG->attendance_autoattend == "1")) { $form->autoattend = 1; }
 	if (isset($CFG->attendance_grade) && ($CFG->attendance_grade == "1")) { $form->grade = 1; }
 	$form->maxgrade = isset($CFG->attendance_maxgrade)?$CFG->attendance_maxgrade:0;
 	$form->hours = isset($CFG->attendance_default_hours)?$CFG->attendance_default_hours:1;
 	$form->day = time();
 	$form->notes = "";
}
    ?>
<FORM name="form" method="post" action="<?php echo "$CFG->wwwroot/course/mod.php"; ?>">
<CENTER>
<INPUT type="submit" value="<?php  print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<?php  print_string("cancel") ?>">

<?php  // if we're modifying an existing instance of attendance instead 
    //   of creating a new one
 if (isset($form->id)) {
   // get the list of attendance records for all hours of the given day and 
   // put it in the array for use in the attendance table
   $rolls = get_records("attendance_roll", "dayid", $form->id);
   if ($rolls) {
     foreach ($rolls as $roll) {
       $sroll[$roll->userid][$roll->hour]->status=$roll->status;
       $sroll[$roll->userid][$roll->hour]->notes=$roll->notes;
	   }
   }
   // get the list of students along with student ID field
// get back array of stdclass objects in sorted order, with members:
// id, username,firstname,lastname,maildisplay,mailformat,email,city,country,
//   lastaccess,lastlogin,picture (picture is null, 0, or 1), idnumber
  // build the table for attendance roll
  // this is the wrapper table
  echo "<table align=\"center\" width=\"80\" class=\"generalbox\"".
         "border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>".
         "<td bgcolor=\"#ffffff\" class=\"generalboxcontent\">";
  // this is the main table
  echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ".
         "cellpadding=\"5\" cellspacing=\"1\" class=\"generaltable\">";
if ($form->hours >1) {
	echo "<tr><th valign=\"top\" align=\"right\" colspan=\"3\" nowrap class=\"generaltableheader\">".
	       "Hours:</th>\n";
	for($i=1;$i<=$form->hours;$i++) {
		echo "<th valign=\"top\" align=\"center\" colspan=\"3\" nowrap class=\"generaltableheader\">".
	       "$i</th>\n";
	}
	echo "</tr>\n";
} // if more than one hour for each day
	echo "<tr><th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">Last Name</th>\n";
	echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">First Name</th>\n";
	echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">ID</th>\n";
    $P=get_string("presentshort","attendance");
    $T=get_string("tardyshort","attendance");
    $A=get_string("absentshort","attendance");
    // generate the headers for the attendance hours
	for($i=1;$i<=$form->hours;$i++) {
  	  echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$P."</th>\n";
	  echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$T."</th>\n";
	  echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$A."</th>\n";
	}
	echo "</tr>\n";
  $table->head = array("Last Name","First Name","ID",
    get_string("presentlong","attendance"),
    get_string("tardylong","attendance"),
    get_string("absentlong","attendance"));
  $table->align = array("left", "left", "left", "center","center","center");
  $table->wrap = array("nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap");
  $table->width = "80";

  $students = attendance_get_course_students($form->course, "u.lastname ASC");
  $i=0;
  if ($students) foreach ($students as $student) {
    echo "<tr><td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$student->lastname."</td>\n";
    echo "<td align=\"left\" nowrap class=\"generaltablecell\"  style=\"border-top: 1px solid;\">".$student->firstname."</td>\n";
    echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$student->idnumber."</td>\n";
	for($j=1;$j<=$form->hours;$j++) {
      // set the attendance defaults for each student
	  $r1c=$r2c=$r3c=" ";
    $rollstatus = (($form->edited==0)?$CFG->attendance_default_student_status:
      ((isset($sroll[$student->id][$j]->status)?$sroll[$student->id][$j]->status:0)));
    if ($rollstatus==1) {$r2c="checked";}
    elseif ($rollstatus==2) {$r3c="checked";}
    else {$r1c="checked";}
    $radio1="<input type=\"radio\" name=\"student_".$student->id."_".$j."\" value=\"0\" ".$r1c.">";
	  $radio2="<input type=\"radio\" name=\"student_".$student->id."_".$j."\" value=\"1\" ".$r2c.">";
    $radio3="<input type=\"radio\" name=\"student_".$student->id."_".$j."\" value=\"2\" ".$r3c.">";  	
    echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$radio1."</td>\n";
    echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$radio2."</td>\n";
    echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$radio3."</td>\n";
	} // for loop
    echo "</tr>\n";
//      $radio1="<input type=\"radio\" name=\"student_".$student->id."\" value=\"0\" checked>";
//	    $radio2="<input type=\"radio\" name=\"student_".$student->id."\" value=\"1\">";
//      $radio3="<input type=\"radio\" name=\"student_".$student->id."\" value=\"2\">";
//      $table->data[$i]=array($student->lastname, $student->firstname,
//	      $student->idnumber, $radio1,$radio2,$radio3);
//      $i++;
  }
  // doing the table manually now
//  print_table($table);
  // ending for both the tables
  echo "</table></td></tr></table>\n";
} // if ($form->id)
?>
<!-- These hidden variables are always the same -->
<INPUT type="hidden" name=course        value="<?php p($form->course) ?>">
<INPUT type="hidden" name=coursemodule  value="<?php p($form->coursemodule) ?>">
<INPUT type="hidden" name=section       value="<?php p($form->section) ?>">
<INPUT type="hidden" name=module        value="<?php p($form->module) ?>">
<INPUT type="hidden" name=modulename    value="<?php p($form->modulename) ?>">
<INPUT type="hidden" name=instance      value="<?php p($form->instance) ?>">
<INPUT type="hidden" name=mode          value="<?php p($form->mode) ?>">
<BR />
<?php
  echo "<a href=\"../mod/attendance/add.php?id=".$form->course . "&section=".$form->section ."\">Add multiple rolls</a><br /><br />";
?>
<INPUT type="submit" value="<?php print_string("savechanges") ?>">
<INPUT type="submit" name="cancel" value="<?php print_string("cancel") ?>">
</CENTER>
</FORM>




<?php

        print_simple_box_end();


    print_footer($course);

?>