<?php  // $Id$
/// This page prints all instances of attendance in a given course

    require_once("../../config.php");
    require_once("lib.php" );
/// @include_once("$CFG->dirroot/mod/attendance/lib.php"); 
/// error_reporting(E_ALL);

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // attendance ID

/// populate the appropriate objects
    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course is misconfigured");
        }
        if (! $attendances = get_records("attendance", "course", $id, "day ASC ")) {
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
        if (! $attendances = get_records("attendance", "course", $cm->course)) {
            error("Course module is incorrect");
        }
    }

    require_login($course->id);

    add_to_log($course->id, "attendance", "viewall", "viewall.php?id=$course->id");

 
/// Print the main part of the page
if ($attendances) {
   if ( isstudent($course->id) && !isteacher($course->id))  {
		 attendance_print_header();
     notice(get_string("noviews", "attendance"));
     print_footer($course); exit;
   }


/// create an array of all the attendance objects for the entire course
   $numatt=0;
   $numhours=0;
   if ($attendances) foreach ($attendances as $attendance){
     // store the raw attendance object
     $cm = get_coursemodule_from_instance("attendance", $attendance->id, $course->id);
     $attendance->cmid = $cm->id;
     $atts[$numatt]->attendance=$attendance;
     // tally the hours for possible paging of the report
     $numhours=$numhours+$attendance->hours;
     // get the list of attendance records for all hours of the given day and 
     // put it in the array for use in the attendance table
     if (isstudent($course->id) && !isteacher($course->id)) { 
       $rolls = get_records("attendance_roll", "dayid", $form->id, "userid", $USER->id);
     } else { // must be a teacher
    	 $rolls = get_records("attendance_roll", "dayid", $attendance->id);
     }
     if ($rolls) {
	     foreach ($rolls as $roll) {
	       $atts[$numatt]->sroll[$roll->userid][$roll->hour]->status=$roll->status;
	       $atts[$numatt]->sroll[$roll->userid][$roll->hour]->notes=$roll->notes;
	     }
     }
   $numatt++;
   }


//
//
//  IF REQUESTING A DATA FILE DOWNLOAD, GENERATE IT INSTEAD OF THE HTML PAGE, SEND IT, AND EXIT
//
//

if ($download == "xls") {
    require_once("$CFG->libdir/excel/Worksheet.php");
    require_once("$CFG->libdir/excel/Workbook.php");
  // HTTP headers
  attendance_HeaderingExcel($course->shortname."_Attendance.xls");
  // Creating a workbook
  $workbook = new Workbook("-");
  // Creating the first worksheet
  $myxls =& $workbook->add_worksheet('Attendance');

    // print the date headings at the top of the table
    // for each day of attendance
    $myxls->write_string(3,0,get_string("lastname"));
    $myxls->write_string(3,1,get_string("firstname"));
    $myxls->write_string(3,2,get_string("idnumber"));
    $pos=3;
if ($dlsub== "all") {
    for($k=0;$k<$numatt;$k++)  {
    // put notes for the date in the date heading
	    $myxls->write_string(1,$pos,userdate($atts[$k]->attendance->day,"%m/%0d"));
	    $myxls->set_column($pos,$pos,5);
	    $myxls->write_string(2,$pos,$atts[$k]->attendance->notes);
			for ($i=1;$i<=$atts[$k]->attendance->hours;$i++) {
				$myxls->write_number(3,$pos,$i);
	    $myxls->set_column($pos,$pos,1);
				$pos++;
			}
    }
}  // if dlsub==all
		$myxls->write_string(3,$pos,get_string("total"));
	    $myxls->set_column($pos,$pos,5);
		
/// generate the attendance rolls for the body of the spreadsheet
  if (isstudent($course->id) && !isteacher($course->id)) { 
  	$students[0] = get_user_info_from_db("id", $USER->id);
  } else { // must be a teacher
    $students = attendance_get_course_students($attendance->course, "u.lastname ASC");
  }
  $i=0;
  $A = get_string("absentshort","attendance");
  $T = get_string("tardyshort","attendance");
  $P = get_string("presentshort","attendance");  
  $row=4;
  if ($students) foreach ($students as $student) {
    $myxls->write_string($row,0,$student->lastname);
    $myxls->write_string($row,1,$student->firstname);
    $studentid=(($student->idnumber != "") ? $student->idnumber : " ");
    $myxls->write_string($row,2,$studentid);
    $pos=3;
    if ($dlsub== "all") {
	    for($k=0;$k<$numatt;$k++)  { // for each day of attendance for the student
	  	  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
	        // set the attendance defaults for each student
	  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {$status=$T;}
		      elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {$status=$A;}
	 	      else {$status=$P;}
	        $myxls->write_string($row,$pos,$status);
	        $pos++;
		    } /// for loop
	    }
    }
		$abs=$tar=0;
    for($k=0;$k<$numatt;$k++)  {  // for eacj day of attendance for the student
  	  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
	      // set the attendance defaults for each student
	  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {;$tar++;}
		    elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {;$abs++;}
		  } /// for loop
	  } // outer for for each day of attendance
    $tot=attendance_tally_overall_absences_decimal($abs,$tar);
    $myxls->write_number($row,$pos,$tot);
		$row++;
  }
  $workbook->close();

  exit;
}


if ($download == "txt") {

        header("Content-Type: application/download\n"); 
        header("Content-Disposition: attachment; filename=\"".$course->shortname."_Attendance.txt\"");

/// Print names of all the fields

        echo get_string("firstname")."\t".get_string("lastname") . "\t". get_string("idnumber");

if ($dlsub== "all") {
    for($k=0;$k<$numatt;$k++)  {
    // put notes for the date in the date heading
	    echo "\t" . userdate($atts[$k]->attendance->day,"%m/%0d");
	    echo (($atts[$k]->attendance->notes != "")?" ".$atts[$k]->attendance->notes:"");
			for ($i=2;$i<=$atts[$k]->attendance->hours;$i++) { echo "\t$i"; }
    }
}  // if dlsub==all
		echo "\t". get_string("total") . "\n";
		
/// generate the attendance rolls for the body of the spreadsheet
  if (isstudent($course->id) && !isteacher($course->id)) { 
  	$students[0] = get_user_info_from_db("id", $USER->id);
  } else { // must be a teacher
    $students = attendance_get_course_students($attendance->course, "u.lastname ASC");
  }
  $i=0;
  $A = get_string("absentshort","attendance");
  $T = get_string("tardyshort","attendance");
  $P = get_string("presentshort","attendance");  
  $row=3;
  if ($students) foreach ($students as $student) {
    echo $student->lastname;
    echo "\t".$student->firstname;
    $studentid=(($student->idnumber != "") ? $student->idnumber : " ");
    echo "\t". $studentid;
    if ($dlsub== "all") {
	    for($k=0;$k<$numatt;$k++)  { // for each day of attendance for the student
	  	  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
	        // set the attendance defaults for each student
	  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {$status=$T;}
		      elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {$status=$A;}
	 	      else {$status=$P;}
	        echo "\t".$status;
		    } /// for loop
	    }
    }
		$abs=$tar=0;
    for($k=0;$k<$numatt;$k++)  {  // for eacj day of attendance for the student
  	  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
	      // set the attendance defaults for each student
	  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {;$tar++;}
		    elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {;$abs++;}
		  } /// for loop
	  } // outer for for each day of attendance
    $tot=attendance_tally_overall_absences_decimal($abs,$tar);
    echo "\t".$tot."\n";
		$row++;
  }
  exit;
}



//
//
//  FIGURE OUT THE PAGING LAYOUT FOR THE DATA BASED ON STATUS, PAGE NUMBER REQUESTED, ETC
//
//

// A LOOP FOR CREATING SINGLE-USER VERSION OF THE REPORT OR A ONE-PAGE REPORT
   if (isstudent($course->id) && !isteacher($course->id)) {
     $onepage=true;
     $multipage=false; 
   } else if (!(isset($onepage))){
     $onepage=false;
     $multipage=true;
   } else if ($onepage) {
     $multipage=false;
   } else {  // if onepage is set to false
   	 $multilpage=true;
   }

// adjust the width for the report for students 

   if (($onetable) || ($CFG->attendance_hours_in_full_report == 0)) {
      $hoursinreport = 100+$numhours;
   } else if (isstudent($course->id) && !isteacher($course->id)) {
      $hoursinreport = $CFG->attendance_hours_in_full_report + 15;
   } else { 
      $hoursinreport = $CFG->attendance_hours_in_full_report;
   }   	
while (($multipage || $onepage) && (!$endonepage)) {
   // this makes for a one iteration loop for multipage
	 if ($multipage) {$onepage = false; $multipage = false; $endonepage=false;}
	 
	 
   if ($numhours>=$hoursinreport) {
	 if (!isset($pagereport)) {
		// $pagereport is used to determine whether the report needs to be paged at all
	 	$pagereport=true;
		$endatt=0;
		$page=1;
	 } 
	 // find the last hour to have on this page of the report
	    // go to the next (or first) page
//		$endatt++;
//		$startatt=$endatt;
		$curpage=1;
		$endatt=0;
  for($curpage=1;true;$curpage++) { // the for loop is broken from the inside
		$pagehours=$atts[$endatt]->attendance->hours;
		$startatt=$endatt;
		while(($pagehours<=$hoursinreport)) {
			if ($endatt>=$numatt) { break 2; } // end the page number calculations and trigger the end of a multi-page report!
			$endatt++;
			$pagehours=$pagehours+$atts[$endatt]->attendance->hours;
		}
		// if this is the page we're on, save the info
		if ($curpage == $page) {$endatt_target = $endatt; $startatt_target = $startatt; }
      } // hopefully at this point, startatt and endatt are set correctly for the current page
		if ($curpage == $page) {$endatt_target = $endatt; $startatt_target = $startatt; } else {
		   $endatt=$endatt_target; $startatt=$startatt_target; }
		$maxpages = $curpage;
   } else {$pagereport=false;}

  $minatt=($pagereport ? $startatt : 0);
  $maxatt=($pagereport ? $endatt : $numatt);     
 
  if ((!$pagereport) || ($page == $maxpages)) {$endonepage = true;}  // end a one page display






//
//
//  ALL PRELIMINARY STUFF DONE - MAKE THE MEAT OF THE PAGE
//
//
    attendance_print_header();

// print other links at top of page
  	$strviewone = get_string("viewone", "attendance");
  	$strviewtable = get_string("viewtable", "attendance");
  	$strviewmulti = get_string("viewmulti", "attendance");
  	$strviewweek = get_string("viewweek", "attendance");	
    if ($onepage) {  // one page for all tables
      echo "<p align=\"right\"><a href=\"viewall.php?id=".$course->id."\">";
      echo "$strviewmulti</a><br />";
      echo "<a href=\"viewall.php?id=".$course->id."&onetable=1\">";
      echo "$strviewtable</a><br />";
      echo "<a href=\"viewweek.php?scope=week&id=".$atts[$minatt]->attendance->id."\">";
      echo "$strviewweek</a></p>";
    } else if ($onetable) { // one table for all
      echo "<p align=\"right\"><a href=\"viewall.php?id=".$course->id."\">";
      echo "$strviewmulti</a><br />";
      echo "<a href=\"viewall.php?id=".$course->id."&onepage=1\">";
      echo "$strviewone</a><br />";
      echo "<a href=\"viewweek.php?scope=week&id=".$atts[$minatt]->attendance->id."\">";
      echo "$strviewweek</a></p>";
    } else { // multiple pages
      echo "<p align=\"right\"><a href=\"viewall.php?id=".$course->id."&onepage=1\">";
      echo "$strviewone</a><br />";
      echo "<a href=\"viewall.php?id=".$course->id."&onetable=1\">";
      echo "$strviewtable</a><br />";
      echo "<a href=\"viewweek.php?scope=week&id=".$atts[$minatt]->attendance->id."\">";
      echo "$strviewweek</a></p>";

    }

  if (!$onepage) {

  attendance_print_pagenav(); 
  } 
   // build the table for attendance roll
   // this is the wrapper table
   echo "<table align=\"center\" width=\"80\" class=\"generalbox\"".
         "border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>".
         "<td bgcolor=\"#ffffff\" class=\"generalboxcontent\">";
   // this is the main table
   echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ".
         "cellpadding=\"5\" cellspacing=\"1\" class=\"generaltable\">";
   if (isteacher($course->id)) {
   echo "<tr><th valign=\"top\" align=\"right\" colspan=\"3\" nowrap class=\"generaltableheader\">".
	       "&nbsp;</th>\n";
   } 
//      $minpage=0;$maxpage=$numatt;
    // print the date headings at the top of the table
    // for each day of attendance
    for($k=$minatt;$k<$maxatt;$k++)  {
    // put notes for the date in the date heading
      $notes = ($atts[$k]->attendance->notes != "") ? ":<br />".$atts[$k]->attendance->notes : "";
      $auto = ($atts[$k]->attendance->autoattend == 1) ? "(".get_string("auto","attendance").")" : "";
 	    echo "<th valign=\"top\" align=\"left\" colspan=\"" .$atts[$k]->attendance->hours. "\" nowrap class=\"generaltableheader\">".
	       "<a href=\"view.php?id=".$atts[$k]->attendance->cmid."\">".userdate($atts[$k]->attendance->day,"%m/%0d")."</a>".$auto.
	       $notes."</th>\n";
    }
    // if we're at the end of the report
    if ($maxatt==$numatt || !$pagereport) {
      echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">&nbsp;</th>\n";
    }
    echo "</tr>\n";
    // print the second level headings with name and possibly hour numbers
  if (isteacher($course->id)) {
  	echo "<tr><th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">Last Name</th>\n";
	  echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">First Name</th>\n";
	  echo "<th valign=\"top\" align=\"left\" nowrap class=\"generaltableheader\">ID</th>\n";
  }
    // generate the headers for the attendance hours
    for($k=$minatt;$k<$maxatt;$k++)  {
      if ($atts[$k]->attendance->hours > 1) {
  	    for($i=1;$i<=$atts[$k]->attendance->hours;$i++) {
    	    echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$i."</th>\n";
  	    }
 	  } else { echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">&nbsp;</th>\n"; }
    }
    // if we're at the end of the report
    if ($maxatt==$numatt || !$pagereport) {
      echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">total</th>";
    }
	echo "</tr>\n";

   // get the list of students along with student ID field
   // get back array of stdclass objects in sorted order, with members:
   // id, username,firstname,lastname,maildisplay,mailformat,email,city,country,
   //   lastaccess,lastlogin,picture (picture is null, 0, or 1), idnumber


  if (isstudent($course->id) && !isteacher($course->id)) { 
  	$students[0] = get_user_info_from_db("id", $USER->id);
  } else { // must be a teacher
    $students = attendance_get_course_students($attendance->course, "u.lastname ASC");
  }
  $i=0;
  $A = get_string("absentshort","attendance");
  $T = get_string("tardyshort","attendance");
  $P = get_string("presentshort","attendance");  
  if ($students) foreach ($students as $student) {
    if (isteacher($course->id)) {
      echo "<tr><td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$student->lastname."</td>\n";
      echo "<td align=\"left\" nowrap class=\"generaltablecell\"  style=\"border-top: 1px solid;\">".$student->firstname."</td>\n";
      $studentid=(($student->idnumber != "") ? $student->idnumber : "&nbsp");
      echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-top: 1px solid;\">".$studentid."</td>\n";
    }
    for($k=$minatt;$k<$maxatt;$k++)  {  // for eacj day of attendance for the student
	  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
      // set the attendance defaults for each student
  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {$status=$T;}
	    elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {$status=$A;}
 	    else {$status=$P;}
        echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$status."</td>\n";
	  } /// for loop
    }
    if ($maxatt==$numatt || !$pagereport) {
	    // tally total attendances for the students
		$abs=$tar=0;
	    for($k=0;$k<$numatt;$k++)  {  // for eacj day of attendance for the student
		  for($j=1;$j<=$atts[$k]->attendance->hours;$j++) {
	      // set the attendance defaults for each student
	  	    if ($atts[$k]->sroll[$student->id][$j]->status == 1) {;$tar++;}
		    elseif ($atts[$k]->sroll[$student->id][$j]->status == 2) {;$abs++;}
		  } /// for loop
	    } // outer for for each day of attendance
      $tot=attendance_tally_overall_absences_fraction($abs,$tar);
      echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$tot."</td></tr>\n";
    }
  }  // foreach
  /// doing the table manually now
  ///  print_table($table);
  /// ending for the table
  echo "</table></td></tr></table>\n";

if ($onepage) {$page++; echo "<br /> <br />\n"; }
}  // while loop for multipage/one page printing

  if (!$onepage) { attendance_print_pagenav(); }


  echo "<center><TABLE BORDER=0 ALIGN=CENTER><TR>";
  echo "<TD>";
  if (($numhours-4) > 255) {
  	echo "<form><input type=\"button\" value=\"".get_string("downloadexcelfull", "attendance").
  	"\" onclick=\"alert('Sorry, you have more than 251 days on this report.  This will not fit into an Excel Spreadsheet. ".
  	" Please try downloading the report week by week instead.')\"></form>";
  } else {
    $options["id"] = "$course->id";
    $options["download"] = "xls";
    $options["dlsub"] = "all";  
    print_single_button("viewall.php", $options, get_string("downloadexcelfull", "attendance"));
  }
  echo "</td><TD>";
  $options["id"] = "$course->id";
  $options["download"] = "xls";
  $options["dlsub"] = "totals";  
  print_single_button("viewall.php", $options, get_string("downloadexceltotals", "attendance"));
  echo "</td><TD>";
  $options["download"] = "txt";
  $options["dlsub"] = "all";  
  print_single_button("viewall.php", $options, get_string("downloadtextfull", "attendance"));
  echo "</td><TD>";
  $options["dlsub"] = "totals";  
  print_single_button("viewall.php", $options, get_string("downloadtexttotals", "attendance"));
  echo "</td></TABLE></center>";

  
} else { error("There are no attendance rolls in this course.");} // for no attendance rolls  
/// Finish the page


    print_footer($course);

function attendance_print_header() {
    global $course, $cm;

	/// Print the page header
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strattendances = get_string("modulenameplural", "attendance");
    $strattendance  = get_string("modulename", "attendance");
    $strallattendance  = get_string("allmodulename", "attendance");
    print_header("$course->shortname: $strallattendance", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strattendances</A> -> $strallattendance", 
                  "", "", true, "&nbsp;", 
                  navmenu($course, $cm));
}

function attendance_print_pagenav() {
    global $pagereport, $minatt, $maxatt, $course, $page, $numatt, $maxpages;
	  if ($pagereport) {
  	$of = get_string('of','attendance');
  	$pg = get_string('page');

		echo "<center><table align=\"center\" width=\"80\" class=\"generalbox\"".
         "border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>".
         "<td bgcolor=\"#ffffff\" class=\"generalboxcontent\">";
    // this is the main table
    echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ".
         "cellpadding=\"5\" cellspacing=\"1\" class=\"generaltable\">";
    echo "<tr>";
  	if ($minatt!=0) {
    echo "<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	       "<a href=\"viewall.php?id=".$course->id ."&pagereport=1&page=1\">&lt;&lt;</a>&nbsp;\n".
	       "<a href=\"viewall.php?id=".$course->id ."&pagereport=1&page=".($page-1)."\">&lt;</a></th>\n";
  	} else {
    echo "<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">&lt;&lt;&nbsp;&lt;</th>\n";
  	}
    echo "<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
	       "$pg $page $of $maxpages</th>\n";
  	if ($maxatt!=$numatt) {
      echo "<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">".
      "<a href=\"viewall.php?id=".$course->id ."&pagereport=1&page=". ($page+1)."\">&gt;</a>&nbsp;".
      "<a href=\"viewall.php?id=".$course->id ."&pagereport=1&page=$maxpages\">&gt;&gt;</a></th>";
  	} else {
    echo "<th valign=\"top\" align=\"right\" nowrap class=\"generaltableheader\">&gt;&nbsp;&gt;&gt;</th>\n";
  	}
		echo "</tr></table></td></tr></table></center>\n";
  }
}

function attendance_HeaderingExcel($filename) {
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=$filename" );
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
  header("Pragma: public");
}

?>

http://www.alpine-usa.com/company_info/press_release/010804_ipad.html
