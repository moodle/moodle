<?PHP // $Id$

//  Lists all the users within a given course

    require_once("../config.php");

    define('USER_SMALL_CLASS', 20);   // Below this is considered small
    define('USER_LARGE_CLASS', 200);  // Above this is considered large

    require_variable($id);   //course
    optional_variable($sort, "lastaccess");  //how to sort students
    optional_variable($dir,"desc");          //how to sort students
    optional_variable($page, "0");           // which page to show
    optional_variable($lastinitial, "");     // only show students with this last initial
    optional_variable($firstinitial, "");    // only show students with this first initial
    optional_variable($perpage, "20");       // how many per page


    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "user", "view all", "index.php?id=$course->id", "");

    $isteacher = isteacher($course->id);
    $showteachers = ($page == 0 and $sort == "lastaccess" and $dir == "desc");

    if ($showteachers) {
        $participantslink = get_string("participants");
    } else {
        $participantslink = "<a href=\"index.php?id=$course->id\">".get_string("participants")."</a>";
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce and 
                         !isteacheredit($course->id));

    $currentgroup = $isseparategroups ? get_current_group($course->id) : NULL;

    if ($course->category) {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "<A HREF=../course/view.php?id=$course->id>$course->shortname</A> -> ".
                     "$participantslink", "", "", true, "&nbsp;", navmenu($course));
    } else {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname", 
                     "$participantslink", "", "", true, "&nbsp;", navmenu($course));
    }

    if ($showteachers) {
        if ($teachers = get_course_teachers($course->id)) {
            echo "<h2 align=\"center\">$course->teachers</h2>";
            foreach ($teachers as $teacher) {
                if ($isseparategroups) {
                    if ($teacher->editall or ismember($currentgroup, $teacher->id)) {
                        print_user($teacher, $course);
                    }
                } else if ($teacher->authority > 0) {    // Don't print teachers with no authority
                    print_user($teacher, $course);
                }
            }
        }
    }

    if ($sort == "lastaccess") {
        $dsort = "s.timeaccess";
    } else {
        $dsort = "u.$sort";
    }

    $students = get_course_students($course->id, $dsort, $dir, $page*$perpage, 
                                    $perpage, $firstinitial, $lastinitial, $currentgroup);

    $totalcount = count_course_students($course, "", "", "", $currentgroup);

    if ($firstinitial or $lastinitial) {
        $matchcount = count_course_students($course, "", $firstinitial, $lastinitial, $currentgroup);
    } else {
        $matchcount = $totalcount;
    }


    echo "<h2 align=center>$totalcount $course->students</h2>";

    if (($CFG->longtimenosee < 500) and (!$page) and ($sort == "lastaccess")) {
        echo "<center><p><font size=1>(";
        print_string("unusedaccounts","",$CFG->longtimenosee);
        echo ")</font></p></center>";
    }

    /// Print paging bars if necessary

    if ($totalcount > $perpage) {
        $alphabet = explode(',', get_string('alphabet'));
        $strall = get_string("all");


        /// Bar of first initials

        echo "<center><p align=\"center\">";
        echo get_string("firstname")." : ";
        if ($firstinitial) {
            echo " <a href=\"index.php?id=$course->id&sort=firstname&dir=ASC&".
                   "perpage=$perpage&lastinitial=$lastinitial\">$strall</a> ";
        } else {
            echo " <b>$strall</b> ";
        }
        foreach ($alphabet as $letter) {
            if ($letter == $firstinitial) {
                echo " <b>$letter</b> ";
            } else {
                echo " <a href=\"index.php?id=$course->id&sort=firstname&dir=ASC&".
                       "perpage=$perpage&lastinitial=$lastinitial&firstinitial=$letter\">$letter</a> ";
            }
        }
        echo "<br />";

        /// Bar of last initials

        echo get_string("lastname")." : ";
        if ($lastinitial) {
            echo " <a href=\"index.php?id=$course->id&sort=lastname&dir=ASC&".
                   "perpage=$perpage&firstinitial=$firstinitial\">$strall</a> ";
        } else {
            echo " <b>$strall</b> ";
        }
        foreach ($alphabet as $letter) {
            if ($letter == $lastinitial) {
                echo " <b>$letter</b> ";
            } else {
                echo " <a href=\"index.php?id=$course->id&sort=lastname&dir=ASC&".
                       "perpage=$perpage&firstinitial=$firstinitial&lastinitial=$letter\">$letter</a> ";
            }
        }
        echo "</p>";
        echo "</center>";

        print_paging_bar($matchcount, $page, $perpage, 
                         "index.php?id=$course->id&sort=$sort&dir=$dir&perpage=$perpage&firstinitial=$firstinitial&lastinitial=$lastinitial&");

    }

    if ($matchcount == 0) {
        print_heading(get_string("nostudentsfound", "", $course->students));

    } if (0 < $matchcount and $matchcount < USER_SMALL_CLASS) {    // Print simple listing
        foreach ($students as $student) {
            print_user($student, $course);
        }

    } else if ($matchcount > 0) {

        // Print one big table with abbreviated info
        $columns = array("firstname", "lastname", "city", "country", "lastaccess");

        $countries = get_list_of_countries();

        $strnever = get_string("never");

        $datestring->day   = get_string("day");
        $datestring->days  = get_string("days");
        $datestring->hour  = get_string("hour");
        $datestring->hours = get_string("hours");
        $datestring->min   = get_string("min");
        $datestring->mins  = get_string("mins");
        $datestring->sec   = get_string("sec");
        $datestring->secs  = get_string("secs");

        foreach ($columns as $column) {
            $colname[$column] = get_string($column);
            if ($sort != $column) {
                $columnicon = "";
                if ($column == "lastaccess") {
                    $columndir = "desc";
                } else {
                    $columndir = "asc";
                }
            } else {
                $columndir = $dir == "asc" ? "desc":"asc";
                if ($column == "lastaccess") {
                    $columnicon = $dir == "asc" ? "up":"down";
                } else {
                    $columnicon = $dir == "asc" ? "down":"up";
                }
                $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" />";
            }
            $$column = "<a href=\"index.php?id=$course->id&sort=$column&dir=$columndir\">".$colname["$column"]."</a>$columnicon";
        }

        foreach ($students as $key => $student) {
            $students[$key]->country = $countries[$student->country];
        }
        if ($sort == "country") {  // Need to re-sort by full country name, not code
            foreach ($students as $student) {
                $sstudents[$student->id] = $student->country;
            }
            asort($sstudents);
            foreach ($sstudents as $key => $value) {
                $nstudents[] = $students[$key];
            }
            $students = $nstudents;
        }


        $table->head = array ("&nbsp;", "$firstname / $lastname", $city, $country, $lastaccess);
        $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT", "LEFT");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->cellpadding = 4;
        $table->cellspacing = 0;
        
        foreach ($students as $student) {

            if ($student->lastaccess) {
                $lastaccess = format_time(time() - $student->lastaccess, $datestring);
            } else {
                $lastaccess = $strnever;
            }

            $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);

            $fullname = fullname($student, $isteacher);

            $table->data[] = array ($picture,
                "<b><a href=\"$CFG->wwwroot/user/view.php?id=$student->id&course=$course->id\">$fullname</a></b>",
                "<font size=2>$student->city</font>", 
                "<font size=2>$student->country</font>",
                "<font size=2>$lastaccess</font>");
        }
        print_table($table);

        print_paging_bar($matchcount, $page, $perpage, 
                         "index.php?id=$course->id&sort=$sort&dir=$dir&perpage=$perpage&firstinitial=$firstinitial&lastinitial=$lastinitial&");

        if ($perpage != 99999) {
            echo "<center><p>";
            echo "<a href=\"index.php?id=$course->id&sort=$sort&dir=$dir&perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        }
    }

    print_footer($course);

?>
