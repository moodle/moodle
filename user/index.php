<?PHP // $Id$

//  Lists all the users within a given course

    require_once("../config.php");
    require_once($CFG->libdir.'/tablelib.php');

    define('USER_SMALL_CLASS', 20);   // Below this is considered small
    define('USER_LARGE_CLASS', 200);  // Above this is considered large

    require_variable($id);   //course
    optional_variable($sort, "lastaccess");  //how to sort students
    optional_variable($dir,"desc");          //how to sort students
    optional_variable($page, "0");           // which page to show
    optional_variable($lastinitial, "");     // only show students with this last initial
    optional_variable($firstinitial, "");    // only show students with this first initial
    optional_variable($perpage, "20");       // how many per page
    optional_variable($group, "-1");         // Group to show
    $mode = optional_param('mode', NULL);    // '0' for less details, '1' for more

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    if (!$course->category) {
        if (!$CFG->showsiteparticipantslist and !isteacher(SITEID)) {
            notice(get_string('sitepartlist0'));
        }
        if ($CFG->showsiteparticipantslist < 2 and !isteacherinanycourse()) {
            notice(get_string('sitepartlist1'));
        }
    }

    add_to_log($course->id, "user", "view all", "index.php?id=$course->id", "");

    $isteacher = isteacher($course->id);
    $showteachers = ($page == 0 and $sort == "lastaccess" and $dir == "desc");

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

    if ($showteachers) {
        $participantslink = get_string("participants");
    } else {
        $participantslink = "<a href=\"index.php?id=$course->id\">".get_string("participants")."</a>";
    }

    if ($mode !== NULL) {
        $SESSION->userindexmode = $fullmode = ($mode == 1);
    } else if (isset($SESSION->userindexmode)) {
        $fullmode = $SESSION->userindexmode;
    } else {
        $fullmode = false;
    }



/// Check to see if groups are being used in this forum
/// and if so, set $currentgroup to reflect the current group

    $changegroup  = isset($_GET['group']) ? $_GET['group'] : -1;  // Group change requested?
    $groupmode    = groupmode($course);   // Groups are being used
    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);


    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce and
                         !isteacheredit($course->id));

    if ($isseparategroups and (!$currentgroup) ) {  //XXX
        print_heading(get_string("notingroup", "forum"));
        print_footer($course);
        exit;
    }

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup  = NULL;
    }


/// Print headers

    if ($course->category) {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> -> ".
                     "$participantslink", "", "", true, "&nbsp;", navmenu($course));
    } else {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "$participantslink", "", "", true, "&nbsp;", navmenu($course));
    }

/// Print settings and things in a table across the top

    echo '<table width="100%" border="0" cellpadding="3" cellspacing="0"><tr valign="top">';

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<td class="left">';
            print_group_menu($groups, $groupmode, $currentgroup, "index.php?id=$course->id");
            echo '</td>';
        }
    }

    echo '<td class="right" align="right">';
    echo get_string('userlist').': ';
    $formatmenu = array( '0' => get_string('detailedless'),
                         '1' => get_string('detailedmore'));
    echo popup_form("index.php?id=$id&amp;sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;lastinitial=$lastinitial&amp;mode=", $formatmenu, 'formatmenu', $fullmode, '', '', '', true);
    echo '</td></tr></table>';

    if ($currentgroup and (!$isseparategroups or isteacheredit($course->id))) {    /// Display info about the group
        if ($group = get_record('groups', 'id', $currentgroup)) {              
            if (!empty($group->description) or (!empty($group->picture) and empty($group->hidepicture))) { 
                echo '<table class="groupinfobox"><tr><td class="left side picture">';
                print_group_picture($group, $course->id, true, false, false);
                echo '</td><td class="content">';
                echo '<h3>'.$group->name;
                if (isteacheredit($course->id)) {
                    echo '&nbsp;<a title="'.get_string('editgroupprofile').'" href="../course/groups.php?id='.$course->id.'&amp;group='.$group->id.'">';
                    echo '<img src="'.$CFG->pixpath.'/t/edit.gif" alt="" border="0">';
                    echo '</a>';
                }
                echo '</h3>';
                echo format_text($group->description);
                echo '</td></tr></table>';
            }
        }
    }


    $exceptions = ''; // This will be a list of userids that are shown as teachers and thus
                      // do not have to be shown as users as well. Only relevant on site course.

    if($showteachers) {

echo '<style type="text/css"> body#user-index table#teachers { margin: auto; width: 80%; } body#user-index table#teachers td, body#user-index table#teachers th {vertical-align: middle; padding: 4px;}</style>';

        $tablecolumns = array('picture', 'fullname', 'city', 'country', 'lastaccess');
        $tableheaders = array('', get_string('fullname'), get_string('city'), get_string('country'), get_string('lastaccess'));

        $table = new flexible_table('user-index-teacher');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/user/index.php?id='.$course->id);

        $table->sortable(true);

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'teachers');
        $table->set_attribute('class', 'generaltable generalbox');

        $table->setup();

        if($whereclause = $table->get_sql_where()) {
            $whereclause .= ' AND ';
        }

        $teachersql = "SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.maildigest,
                                   u.email, u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
                                   u.emailstop, t.authority,t.role,t.editall,t.timeaccess as lastaccess, m.groupid
                            FROM {$CFG->prefix}user u
                       LEFT JOIN {$CFG->prefix}user_teachers t ON t.userid = u.id 
                       LEFT JOIN {$CFG->prefix}groups_members m ON m.userid = u.id ";

        if($isseparategroups) {
            $whereclause .= '(t.editall OR groupid = '.$currentgroup.') AND ';
        }
        else if ($currentgroup) {    // Displaying a group by choice
            $whereclause .= 'groupid = '.$currentgroup.' AND ';
        }

        $teachersql .= 'WHERE '.$whereclause.' t.course = '.$course->id.' AND u.deleted = 0 AND u.confirmed = 1 AND t.authority > 0';

        if($sortclause = $table->get_sql_sort()) {
            $teachersql .= ' ORDER BY '.$sortclause;
        }

        $teachers = get_records_sql($teachersql);

        if(!empty($teachers)) {

            echo "<h2 align=\"center\">$course->teachers";
            if (isadmin() or ($course->category and (iscreator() or (isteacheredit($course->id) and !empty($CFG->teacherassignteachers))))) {
                echo ' <a href="../course/teacher.php?id='.$course->id.'">';
                echo '<img src="'.$CFG->pixpath.'/i/edit.gif" height="16" width="16" alt="" /></a>';
            }
            echo '</h2>';

            $exceptions .= implode(',', array_keys($teachers));

            if ($fullmode) {
                foreach ($teachers as $key => $teacher) {
                    print_user($teacher, $course);
                }
            } else {
                foreach ($teachers as $teacher) {
        
                    if ($teacher->lastaccess) {
                        $lastaccess = format_time(time() - $teacher->lastaccess, $datestring);
                    } else {
                        $lastaccess = $strnever;
                    }
        
                    $table->add_data(array (
                                    //'<input type="checkbox" name="userid[]" value="'.$teacher->id.'" />',
                                    print_user_picture($teacher->id, $course->id, $teacher->picture, false, true),
                                    '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$teacher->id.'&amp;course='.$course->id.'">'.fullname($teacher, $isteacher).'</a></strong>',
                                    $teacher->city,
                                    $teacher->country ? $countries[$teacher->country] : '',
                                    $lastaccess));
                }
            }
            
            $table->print_html();
        }
    }

    $guest = get_guest();
    $exceptions .= $guest->id;

    if ($course->id == SITEID) { // Show all site users (even unconfirmed)
        $students = get_users(true, '', true, $exceptions, $sort.' '.$dir, 
                              $firstinitial, $lastinitial, $page*$perpage, $perpage);
        $totalcount = get_users(false, '', true, '', '', '', '') - 1; // -1 to not count guest user
        if ($firstinitial or $lastinitial) {
            $matchcount = get_users(false, '', true, '', '', $firstinitial, $lastinitial) - 1;
        } else {
            $matchcount = $totalcount;
        }
    } else {
        if ($sort == "lastaccess") {
            $dsort = "s.timeaccess";
        } else {
            $dsort = "u.$sort";
        }
        $students = get_course_students($course->id, $dsort, $dir, $page*$perpage, $perpage, 
                                        $firstinitial, $lastinitial, $currentgroup);
        $totalcount = count_course_students($course, "", "", "", $currentgroup);
        if ($firstinitial or $lastinitial) {
            $matchcount = count_course_students($course, "", $firstinitial, $lastinitial, $currentgroup);
        } else {
            $matchcount = $totalcount;
        }
    }

    $a->count = $totalcount;
    $a->items = $course->students;
    echo '<h2 align="center">'.get_string('counteditems', '', $a);
    if (isteacheredit($course->id)) {
        echo ' <a href="../course/student.php?id='.$course->id.'">';
        echo '<img src="'.$CFG->pixpath.'/i/edit.gif" height="16" width="16" alt="" /></a>';
    }
    echo '</h2>';

    if (($CFG->longtimenosee > 0) and ($CFG->longtimenosee < 1000) and (!$page) and ($sort == "lastaccess")) {
        echo '<p id="longtimenosee">('.get_string('unusedaccounts', '', $CFG->longtimenosee).')</p>';
    }

    /// Print paging bars if necessary

    if ($totalcount > $perpage) {
        $alphabet = explode(',', get_string('alphabet'));
        $strall = get_string("all");


        /// Bar of first initials

        echo "<center><p align=\"center\">";
        echo get_string("firstname")." : ";
        if ($firstinitial) {
            echo " <a href=\"index.php?id=$course->id&amp;sort=firstname&amp;dir=ASC&amp;group=$currentgroup&amp;".
                   "perpage=$perpage&amp;lastinitial=$lastinitial\">$strall</a> ";
        } else {
            echo " <b>$strall</b> ";
        }
        foreach ($alphabet as $letter) {
            if ($letter == $firstinitial) {
                echo " <b>$letter</b> ";
            } else {
                echo " <a href=\"index.php?id=$course->id&amp;sort=firstname&amp;dir=ASC&amp;group=$currentgroup&amp;".
                       "perpage=$perpage&amp;lastinitial=$lastinitial&amp;firstinitial=$letter\">$letter</a> ";
            }
        }
        echo "<br />";

        /// Bar of last initials

        echo get_string("lastname")." : ";
        if ($lastinitial) {
            echo " <a href=\"index.php?id=$course->id&amp;sort=lastname&amp;dir=ASC&amp;group=$currentgroup&amp;".
                   "perpage=$perpage&amp;firstinitial=$firstinitial\">$strall</a> ";
        } else {
            echo " <b>$strall</b> ";
        }
        foreach ($alphabet as $letter) {
            if ($letter == $lastinitial) {
                echo " <b>$letter</b> ";
            } else {
                echo " <a href=\"index.php?id=$course->id&amp;sort=lastname&amp;dir=ASC&amp;group=$currentgroup&amp;".
                       "perpage=$perpage&amp;firstinitial=$firstinitial&amp;lastinitial=$letter\">$letter</a> ";
            }
        }
        echo "</p>";
        echo "</center>";

        print_paging_bar($matchcount, $page, $perpage,
                         "index.php?id=$course->id&amp;sort=$sort&amp;dir=$dir&amp;group=$currentgroup&amp;perpage=$perpage&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial&amp;");

    }

    if ($matchcount < 1) {
        print_heading(get_string("nostudentsfound", "", $course->students));

    } else if ($fullmode) {    // Print simple listing
        foreach ($students as $student) {
            print_user($student, $course);
        }

    } else if ($matchcount > 0) {
        print_user_table($students, $isteacher);

        print_paging_bar($matchcount, $page, $perpage,
                         "index.php?id=$course->id&amp;sort=$sort&amp;dir=$dir&amp;group=$currentgroup&amp;perpage=$perpage&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial&amp;");

        if ($perpage < $totalcount) {
            echo "<center><p>";
            echo "<a href=\"index.php?id=$course->id&amp;sort=$sort&amp;dir=$dir&amp;group=$currentgroup&amp;perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        }
    }

    print_footer($course);
    exit;




function print_user_table($users, $isteacher) {
        // Print one big table with abbreviated info
        global $mode, $sort, $course, $dir, $CFG;
        if (isset($_GET['group'])) {
             $group_param = "&amp;group=".$_GET['group'];
        } else {
             $group_param = "";
        }
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
                $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" alt=\"\"/>";
            }
            $$column = "<a href=\"index.php?id=$course->id&amp;sort=$column&amp;dir=$columndir$group_param\">".$colname["$column"]."</a>$columnicon";
        }

        foreach ($users as $key => $user) {
            $users[$key]->country = ($user->country) ? $countries[$user->country] : '';
        }
        if ($sort == "country") {  // Need to re-sort by full country name, not code
            foreach ($users as $user) {
                $sstudents[$user->id] = $user->country;
            }
            asort($sstudents);
            foreach ($sstudents as $key => $value) {
                $nstudents[] = $users[$key];
            }
            $users = $nstudents;
        }


        $table->head = array ("&nbsp;", "$firstname / $lastname", $city, $country, $lastaccess);
        $table->align = array ("left", "left", "left", "left", "left");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->cellpadding = 4;
        $table->cellspacing = 0;

        foreach ($users as $user) {

            if ($user->lastaccess) {
                $lastaccess = format_time(time() - $user->lastaccess, $datestring);
            } else {
                $lastaccess = $strnever;
            }

            $picture = print_user_picture($user->id, $course->id, $user->picture, false, true);

            $fullname = fullname($user, $isteacher);

            $table->data[] = array ($picture,
                "<b><a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a></b>",
                $user->city,
                $user->country,
                $lastaccess);
        }
        print_table($table);
}

?>
