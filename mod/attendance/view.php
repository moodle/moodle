<?php   // $Id$
/// This page prints a particular instance of attendance
    require_once("../../config.php");
    require_once("lib.php" );
/// @include_once("$CFG->dirroot/mod/attendance/lib.php"); teachered
// error_reporting(E_ALL);
    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // attendance ID
    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }   
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $attendance = get_record("attendance", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
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
    }

    require_login($course->id);

    add_to_log($course->id, "attendance", "view", "view.php?id=$cm->id", $attendance->id, $cm->id);

/// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strattendances = get_string("modulenameplural", "attendance");
    $strattendance  = get_string("modulename", "attendance");
    $strteacheredit = get_string("teacheredit", "attendance");

    print_header("$course->shortname: $attendance->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strattendances</A> -> $attendance->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strattendance), 
                  navmenu($course, $cm));

/// Print the main part of the page

   // adaptation of mod code to view code needs this:
   $form = $attendance;

   if (isteacher($course->id)) {
     $rolls = get_records("attendance_roll", "dayid", $form->id);
   } else if (!$cm->visible) {
     notice(get_string("activityiscurrentlyhidden"));
     print_footer($course); exit;
   } else if (isstudent($course->id)) {  // visible and a student
     $rolls = get_records("attendance_roll", "dayid", $form->id, "userid", $USER->id);
   } else {
     notice(get_string("noviews", "attendance"));
     print_footer($course); exit;
   }
   if ($rolls) {
     foreach ($rolls as $roll) {
       $sroll[$roll->userid][$roll->hour]->status=$roll->status;
       $sroll[$roll->userid][$roll->hour]->notes=$roll->notes;
     }
   }

   // get the list of attendance records for all hours of the given day and 
   // put it in the array for use in the attendance table
	$strviewall = get_string("viewall", "attendance");
 	$strviewweek = get_string("viewweek", "attendance");
	echo "<p align=\"right\">";
  if (isteacher($course->id)) {
    echo "<a href=\"teacheredit.php?update=".$cm->id."&return=true\">$strteacheredit</a><br/>";
  }
  echo "<a href=\"viewall.php?id=".$course->id."\">$strviewall</a><br/>";
  echo "<a href=\"viewweek.php?scope=week&id=".$attendance->id."\">$strviewweek</a><br/></p>";

  // this is the wrapper table
  echo "<table align=\"center\" width=\"80\" class=\"generalbox\"".
         "border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<tr><td bgcolor=\"#ffffff\" class=\"generalboxcontent\">";
  // this is the main table
  echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ".
         "cellpadding=\"5\" cellspacing=\"1\" class=\"generaltable\">";
// print the date headings at the top of the table
	echo "<tr><th valign=\"top\" align=\"right\" colspan=\"3\" nowrap class=\"generaltableheader\">".
	       "&nbsp;</th>\n";
    // put notes for the date in the date heading
    $notes = ($form->notes != "") ? ":<br />".$form->notes : "";
	echo "<th valign=\"top\" align=\"left\" colspan=\"" .$form->hours. "\" nowrap class=\"generaltableheader\">".
	       userdate($form->day,get_string("strftimedateshort")).$notes."</th>\n";
    echo (($form->hours > 1)  ? "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">&nbsp;</th>\n" : "");
    echo "</tr>\n";
// print the second level headings with name and possibly hour numbers
	echo "<tr><th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">Last Name</th>\n";
	echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">First Name</th>\n";
	echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">ID</th>\n";
    // generate the headers for the attendance hours
    if ($form->hours > 1) {
  	  for($i=1;$i<=$form->hours;$i++) {
  	    echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$i."</th>\n";
	  }
		echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">total</td>";
	} else { echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">&nbsp;</th>\n"; }
	echo "</tr>\n";
  // get the list of students along with student ID field
  // get back array of stdclass objects in sorted order, with members:
  // id, username,firstname,lastname,maildisplay,mailformat,email,city,country,
  //   lastaccess,lastlogin,picture (picture is null, 0, or 1), idnumber
  if (isteacher($course->id)){
    $students = attendance_get_course_students($form->course, "u.lastname ASC");
  } else { // must be a student
  	$students[0] = get_user_info_from_db("id", $USER->id);
  }
  $i=0;
  $A = get_string("absentshort","attendance");
  $T = get_string("tardyshort","attendance");
  $P = get_string("presentshort","attendance");  
  

  if ($students) foreach ($students as $student) {
    echo "<tr><td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$student->lastname."</td>\n";
    echo "<td align=\"left\" nowrap class=\"generaltablecell\"  style=\"border-top: 1px solid;\">".$student->firstname."</td>\n";
    $studentid=(($student->idnumber != "") ? $student->idnumber : "&nbsp");
    echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$studentid."</td>\n";
	  $abs=$tar=0;
	  for($j=1;$j<=$form->hours;$j++) {
      // set the attendance defaults for each student
  	      if ($sroll[$student->id][$j]->status == 1) {$status=$T;$tar++;}
	      elseif ($sroll[$student->id][$j]->status == 2) {$status=$A;$abs++;}
	    else {$status=$P;}
      echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$status."</td>\n";
	  } /// for loop
    if ($form->hours > 1) {
	    $tot=attendance_tally_overall_absences_fraction($abs,$tar);
      echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$tot."</td></tr>\n";
    }
  }
  /// ending for the table
  echo "</table></td></tr></table>\n";
  
  
  /// print the miscellaneous settings information before the attendance roll
  echo "<center><table align=\"center\" width=\"80\" class=\"generalbox\"".
    "border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>".
    "<td bgcolor=\"#ffffff\" class=\"generalboxcontent\">";
// this is the main table
  echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ".
         "cellpadding=\"5\" cellspacing=\"1\" class=\"generaltable\">";
  echo"<tr><th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  get_string("dynsectionshort","attendance").":</th>\n";
  echo"<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  (($form->dynsection=="1")?"Yes":"No")."</th></tr>\n";
  echo"<tr><th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  get_string("autoattendshort","attendance").":</th>\n";
  echo"<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  (($form->autoattend=="1")?"Yes":"No")."</th></tr>\n";
  echo"<tr><th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  get_string("gradeshort","attendance").":</th>\n";
  echo"<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  (($form->grade=="1")?"Yes":"No")."</th></tr>\n";
  if ($form->grade == "1") {
    echo"<tr><th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  get_string("maxgradeshort","attendance").":</th>\n";
    echo"<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	  $form->maxgrade."</th></tr>\n";
  }
  echo "</table></td></table>\n";

/// Finish the page
    print_footer($course);

?>
