<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course module

    $format = optional_param('format', CHOICE_PUBLISH_NAMES, PARAM_INT);

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course module is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    $strresponses = get_string("responses", "choice");

    add_to_log($course->id, "choice", "report", "report.php?id=$cm->id", "$choice->id",$cm->id);


/// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if ($currentgroup) {
        $users = get_group_users($currentgroup, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname') + get_admins();
    } else {
        $users = get_course_users($course->id, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname') + get_admins();
    }

    if (!$users) {
        print_heading(get_string("nousersyet"));
        print_footer($course);
        exit;
    }

    if ($allresponses = get_records("choice_answers", "choiceid", $choice->id)) {
        foreach ($allresponses as $aa) {
            $answers[$aa->userid] = $aa;
        }
    } else {
        $answers = array () ;
    }

    $timenow = time();

    foreach ($choice->option as $optionid => $text) {
        $useranswer[$optionid] = array();
    }
    foreach ($users as $user) {
        if (!empty($user->id) and !empty($answers[$user->id])) {
            $answer = $answers[$user->id];
            $useranswer[(int)$answer->optionid][] = $user;
        } else {
            $useranswer[0][] = $user;
        }
    }
    foreach ($choice->option as $optionid => $text) {
        if (!$choice->option[$optionid]) {
            unset($useranswer[$optionid]);     // Throw away any data that doesn't apply
        }
    }
    ksort($useranswer);
    
    //print spreadsheet if one is asked for:
    if ($download == "xls") {

        require_once("$CFG->libdir/excel/Worksheet.php");
        require_once("$CFG->libdir/excel/Workbook.php");
  
      // HTTP headers
      $filename = $course->shortname."_".$choice->name.".xls";
  
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
  
      // Creating a workbook
      $workbook = new Workbook("-");
      // Creating the first worksheet
      $myxls =& $workbook->add_worksheet('Responses');

        $myxls->write_string(0,0,get_string("lastname"));
        $myxls->write_string(0,1,get_string("firstname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("choice","choice"));
              
        
    /// generate the data for the body of the spreadsheet
      $i=0;  
      $row=1;
      if ($users) foreach ($users as $user) {
          if (!($answers[$user->id]->optionid==0 && isadmin($user->id)) && 
              (!($answers[$user->id]->optionid==0 && isteacher($course->id, $user->id) && !(isteacheredit($course->id, $user->id)) ) ) &&  
              !($choice->showunanswered==0 && $answers[$user->id]->optionid==0)  ) { //make sure admins and hidden teachers are not shown in not answered yet column, and not answered only shown if set in config page.

                  $myxls->write_string($row,0,$user->lastname);
                  $myxls->write_string($row,1,$user->firstname);
                  $studentid=(($user->idnumber != "") ? $user->idnumber : " ");
                  $myxls->write_string($row,2,$studentid);
                  $useroption = choice_get_option_text($choice, $answers[$user->id]->optionid);
                  if (isset($useroption)) {
                      $myxls->write_string($row,3,$useroption);
                  }                 
                  $row++;
          }
         $pos=4;
      }        
  
      $workbook->close();
      exit;
    } 
    // print text file     
    if ($download == "txt") {
    $filename = $course->shortname."_".$choice->name.".txt";
        header("Content-Type: application/download\n"); 
        header("Content-Disposition: attachment; filename=\"".$filename."\"");

    /// Print names of all the fields

        echo get_string("firstname")."\t".get_string("lastname") . "\t". get_string("idnumber") . "\t";
        echo get_string("choice","choice"). "\n";        
        
    /// generate the data for the body of the spreadsheet
      $i=0;  
      $row=1;
      if ($users) foreach ($users as $user) {
          if (!($answers[$user->id]->optionid==0 && isadmin($user->id)) && 
              (!($answers[$user->id]->optionid==0 && isteacher($course->id, $user->id) && !(isteacheredit($course->id, $user->id)) ) ) &&  
              !($choice->showunanswered==0 && $answers[$user->id]->optionid==0)  ) { //make sure admins and hidden teachers are not shown in not answered yet column, and not answered only shown if set in config page.

              echo $user->lastname;
              echo "\t".$user->firstname;
              $studentid=(($user->idnumber != "") ? $user->idnumber : " ");
              echo "\t". $studentid."\t";
              echo choice_get_option_text($choice, $answers[$user->id]->optionid). "\n";
          }
      $row++;
      }      
  exit;
}
    
    print_header_simple(format_string($choice->name).": $strresponses", "",
                 "<a href=\"index.php?id=$course->id\">$strchoices</a> ->
                  <a href=\"view.php?id=$cm->id\">".format_string($choice->name,true)."</a> -> $strresponses", "");

    switch ($format) {
        case CHOICE_PUBLISH_NAMES:

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\">";
            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th class=\"col$optionid\" width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th class=\"col$optionid\" width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo format_string(choice_get_option_text($choice, $optionid));
                echo "</th>";
            }
            echo "</tr><tr>";

            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<td class=\"col$optionid\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else if ($choice->showunanswered) {
                    echo "<td class=\"col$optionid\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else {
                    continue;
                }

                echo "<table width=\"100%\">";
                foreach ($userlist as $user) {
                    if (!($optionid==0 && isadmin($user->id)) && !($optionid==0 && isteacher($course->id, $user->id) && !(isteacheredit($course->id, $user->id)) )  ) { //make sure admins and hidden teachers are not shown in not answered yet column.
                        echo "<tr><td width=\"10\" nowrap=\"nowrap\">";
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo "</td><td width=\"100%\" nowrap=\"nowrap\">";
                        echo "<p>".fullname($user, true)."</p>";
                        echo "</td></tr>";
                    }
                }
                echo "</table>";

                echo "</td>";
            }
            echo "</tr></table>";
            break;


        case CHOICE_PUBLISH_ANONYMOUS:
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\">";
            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo choice_get_option_text($choice, $optionid);
                echo "</th>";
            }
            echo "</tr>";

            $maxcolumn = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $column[$optionid] = count($userlist);
                if ($column[$optionid] > $maxcolumn) {
                    $maxcolumn = $column[$optionid];
                }
            }

            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $height = 0;
                if ($maxcolumn) {
                    $height = $COLUMN_HEIGHT * ((float)$column[$optionid] / (float)$maxcolumn);
                }
                echo "<td valign=\"bottom\" align=\"center\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\" alt=\"\" />";
                echo "</td>";
            }
            echo "</tr>";

            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                echo "<td align=\"center\">".$column[$optionid]."</td>";
            }
            echo "</tr></table>";

            break;
    }
    
    echo "<br />\n";
    echo "<table border=\"0\" align=\"center\"><tr>\n";
    echo "<td>";
    unset($options);
    $options["id"] = "$cm->id";   
    $options["download"] = "xls";
    print_single_button("report.php", $options, get_string("downloadexcel"));
    echo "</td><td>";
    $options["download"] = "txt";    
    print_single_button("report.php", $options, get_string("downloadtext"));

    echo "</td></tr></table>";
print_footer($course);


?>
