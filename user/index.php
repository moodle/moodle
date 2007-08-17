<?PHP // $Id$

//  Lists all the users within a given course

    require_once('../config.php');
    require_once($CFG->libdir.'/tablelib.php');

    define('USER_SMALL_CLASS', 20);   // Below this is considered small
    define('USER_LARGE_CLASS', 200);  // Above this is considered large
    define('DEFAULT_PAGE_SIZE', 20);
    define('SHOW_ALL_PAGE_SIZE', 5000);

    $group        = optional_param('group', -1, PARAM_INT);                   // Group to show
    $page         = optional_param('page', 0, PARAM_INT);                     // which page to show
    $perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page
    $mode         = optional_param('mode', NULL);                             // '0' for less details, '1' for more
    $accesssince  = optional_param('accesssince',0,PARAM_INT);               // filter by last access. -1 = never
    $search       = optional_param('search','',PARAM_CLEAN);
    $roleid       = optional_param('roleid', 0, PARAM_INT);                 // optional roleid

    $contextid    = optional_param('contextid', 0, PARAM_INT);                 // one of this or
    $courseid     = optional_param('id', 0, PARAM_INT);                        // this are required

    if ($contextid) {
        if (! $context = get_context_instance_by_id($contextid)) {
            error("Context ID is incorrect");
        }
        if (! $course = get_record('course', 'id', $context->instanceid)) {
            error("Course ID is incorrect");
        }
    } else {
        if (! $course = get_record('course', 'id', $courseid)) {
            error("Course ID is incorrect");
        }
        if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
            error("Context ID is incorrect");
        }
    }
    // not needed anymore
    unset($contextid);
    unset($courseid);

    require_login($course);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    if (!has_capability('moodle/course:viewparticipants', $context)) {
        print_error('nopermissions');
    }

    $rolenames = array();
    $avoidroles = array();

    if ($roles = get_roles_used_in_context($context, true)) {
        // We should ONLY allow roles with moodle/course:view because otherwise we get little niggly issues
        // like MDL-8093
        // We should further exclude "admin" users (those with "doanything" at site level) because
        // Otherwise they appear in every participant list

        $canviewroles    = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context);
        $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);

        foreach ($roles as $role) {
            if (!isset($canviewroles[$role->id])) {   // Avoid this role (eg course creator)
                $avoidroles[] = $role->id;
                unset($roles[$role->id]);
                continue;
            }
            if (isset($doanythingroles[$role->id])) {   // Avoid this role (ie admin)
                $avoidroles[] = $role->id;
                unset($roles[$role->id]);
                continue;
            }
            $rolenames[$role->id] = strip_tags(role_get_name($role, $context));   // Used in menus etc later on
        }
    }

    // no roles to display yet?
    if (empty($rolenames)) {
        if (has_capability('moodle/user:assign', $context)) {
            redirect($CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id);
        } else {
            error ('No participants found for this course');
        }
    }

    add_to_log($course->id, 'user', 'view all', 'index.php?id='.$course->id, '');

    $bulkoperations = has_capability('moodle/course:bulkmessaging', $context);

    $countries = get_list_of_countries();

    $strnever = get_string('never');

    $datestring->year  = get_string('year');
    $datestring->years = get_string('years');
    $datestring->day   = get_string('day');
    $datestring->days  = get_string('days');
    $datestring->hour  = get_string('hour');
    $datestring->hours = get_string('hours');
    $datestring->min   = get_string('min');
    $datestring->mins  = get_string('mins');
    $datestring->sec   = get_string('sec');
    $datestring->secs  = get_string('secs');

    if ($mode !== NULL) {
        $SESSION->userindexmode = $fullmode = ($mode == 1);
    } else if (isset($SESSION->userindexmode)) {
        $fullmode = $SESSION->userindexmode;
    } else {
        $fullmode = false;
    }

/// Check to see if groups are being used in this forum
/// and if so, set $currentgroup to reflect the current group

    $groupmode    = groupmode($course);   // Groups are being used
    $currentgroup = get_and_set_current_group($course, $groupmode, $group);

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup  = NULL;
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce and
                         !has_capability('moodle/site:accessallgroups', $context));

    if ($isseparategroups and (!$currentgroup) ) {
        $navlinks = array();
        $navlinks[] = array('name' => get_string('participants'), 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$course->shortname: ".get_string('participants'), $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));
        print_heading(get_string("notingroup", "forum"));
        print_footer($course);
        exit;
    }

    // Should use this variable so that we don't break stuff every time a variable is added or changed.
    $baseurl = $CFG->wwwroot.'/user/index.php?contextid='.$context->id.'&amp;roleid='.$roleid.'&amp;id='.$course->id.'&amp;group='.$currentgroup.'&amp;perpage='.$perpage.'&amp;accesssince='.$accesssince.'&amp;search='.s($search);

/// Print headers

    $navlinks = array();
    $navlinks[] = array('name' => get_string('participants'), 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: ".get_string('participants'), $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));

/// setting up tags
    if ($course->id == SITEID) {
        $filtertype = 'site';
    } else if ($course->id && !$currentgroup) {
        $filtertype = 'course';
        $filterselect = $course->id;
    } else {
        $filtertype = 'group';
        $filterselect = $currentgroup;
    }
    $currenttab = 'participants';
    $user = $USER;

    require_once($CFG->dirroot .'/user/tabs.php');


/// Get the hidden field list
    if (has_capability('moodle/course:viewhiddenuserfields', $context)) {
        $hiddenfields = array();  // teachers and admins are allowed to see everything
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }


/// Print settings and things in a table across the top

    echo '<table class="controls" cellspacing="0"><tr>';

/// Print my course menus
    if ($mycourses = get_my_courses($USER->id)) {
        echo '<td class="left">';
        $courselist = array();
        foreach ($mycourses as $mycourse) {
            $courselist[$mycourse->id] = format_string($mycourse->shortname);
        }
        popup_form($CFG->wwwroot.'/user/index.php?roleid='.$roleid.'&amp;sifirst=&amp;silast=&amp;id=',
                   $courselist, 'courseform', $course->id, '', '', '', false, 'self', get_string('mycourses'));
        echo '</td>';
    }

    echo '<td class="left">';
    setup_and_print_groups($course, $groupmode, $baseurl);
    echo '</td>';

    // get minimum lastaccess for this course and display a dropbox to filter by lastaccess going back this far.
    // this might not work anymore because you always going to get yourself as the most recent entry? added $USER!=$user ch
    $minlastaccess = get_field_sql('SELECT min(timeaccess) FROM '.$CFG->prefix.'user_lastaccess WHERE courseid = '.$course->id.' AND timeaccess != 0 AND userid!='.$USER->id);
    $lastaccess0exists = record_exists('user_lastaccess','courseid',$course->id,'timeaccess',0);
    $now = usergetmidnight(time());
    $timeaccess = array();

    // makes sense for this to go first.
    $timeoptions[0] = get_string('selectperiod');

    // days
    for ($i = 1; $i < 7; $i++) {
        if (strtotime('-'.$i.' days',$now) >= $minlastaccess) {
            $timeoptions[strtotime('-'.$i.' days',$now)] = get_string('numdays','moodle',$i);
        }
    }
    // weeks
    for ($i = 1; $i < 10; $i++) {
        if (strtotime('-'.$i.' weeks',$now) >= $minlastaccess) {
            $timeoptions[strtotime('-'.$i.' weeks',$now)] = get_string('numweeks','moodle',$i);
        }
    }
    // months
    for ($i = 2; $i < 12; $i++) {
        if (strtotime('-'.$i.' months',$now) >= $minlastaccess) {
            $timeoptions[strtotime('-'.$i.' months',$now)] = get_string('nummonths','moodle',$i);
        }
    }
    // try a year
    if (strtotime('-1 year',$now) >= $minlastaccess) {
        $timeoptions[strtotime('-1 year',$now)] = get_string('lastyear');
    }

    if (!empty($lastaccess0exists)) {
        $timeoptions[-1] = get_string('never');
    }

    if (count($timeoptions) > 1) {
        echo '<td class="left">';
        $baseurl = preg_replace('/&amp;accesssince='.$accesssince.'/','',$baseurl);
        popup_form($baseurl.'&amp;accesssince=',$timeoptions,'timeoptions',$accesssince, '', '', '', false, 'self', get_string('usersnoaccesssince'));
        echo '</td>';
    }


    echo '<td class="right">';
    $formatmenu = array( '0' => get_string('detailedless'),
                         '1' => get_string('detailedmore'));
    popup_form($baseurl.'&amp;mode=', $formatmenu, 'formatmenu', $fullmode, '', '', '', false, 'self', get_string('userlist'));
    echo '</td></tr></table>';

    if ($currentgroup and (!$isseparategroups or has_capability('moodle/site:accessallgroups', $context))) {    /// Display info about the group
        if ($group = groups_get_group($currentgroup)) { //TODO:
            if (!empty($group->description) or (!empty($group->picture) and empty($group->hidepicture))) {
                echo '<table class="groupinfobox"><tr><td class="left side picture">';
                print_group_picture($group, $course->id, true, false, false);
                echo '</td><td class="content">';
                echo '<h3>'.$group->name;
                if (has_capability('moodle/course:managegroups', $context)) {
                    echo '&nbsp;<a title="'.get_string('editgroupprofile').'" href="'.$CFG->wwwroot.'/group/group.php?id='.$group->id.'&amp;courseid='.$group->courseid.'">';
                    echo '<img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('editgroupprofile').'" />';
                    echo '</a>';
                }
                echo '</h3>';
                echo format_text($group->description);
                echo '</td></tr></table>';
            }
        }
    }


    /// Define a table showing a list of users in the current role selection

    $tablecolumns = array('userpic', 'fullname');
    $tableheaders = array(get_string('userpic'), get_string('fullname'));
    if (!isset($hiddenfields['city'])) {
        $tablecolumns[] = 'city';
        $tableheaders[] = get_string('city');
    }
    if (!isset($hiddenfields['country'])) {
        $tablecolumns[] = 'country';
        $tableheaders[] = get_string('country');
    }
    if (!isset($hiddenfields['lastaccess'])) {
        $tablecolumns[] = 'lastaccess';
        $tableheaders[] = get_string('lastaccess');
    }

    if ($course->enrolperiod) {
        $tablecolumns[] = 'timeend';
        $tableheaders[] = get_string('enrolmentend');
    }

    if ($bulkoperations) {
        $tablecolumns[] = '';
        $tableheaders[] = get_string('select');
    }

    $table = new flexible_table('user-index-participants-'.$course->id);

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl);

    $table->sortable(true, 'lastaccess', SORT_DESC);

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'participants');
    $table->set_attribute('class', 'generaltable generalbox');

    $table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));
    $table->setup();


    // we are looking for all users with this role assigned in this context or higher
    if ($usercontexts = get_parent_contexts($context)) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
    } else {
        $listofcontexts = '('.$sitecontext->id.')'; // must be site
    }
    if ($roleid) {
        $selectrole = " AND r.roleid = $roleid ";
    } else {
        $selectrole = " ";
    }
    $select = 'SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.city, u.country, u.picture, u.lang, u.timezone, u.emailstop, u.maildisplay, ul.timeaccess AS lastaccess, r.hidden '; // s.lastaccess
    $select .= $course->enrolperiod?', r.timeend ':'';

    $from   = "FROM {$CFG->prefix}user u INNER JOIN
    {$CFG->prefix}role_assignments r on u.id=r.userid LEFT OUTER JOIN
    {$CFG->prefix}user_lastaccess ul on (r.userid=ul.userid and ul.courseid = $course->id)";

    $hiddensql = has_capability('moodle/role:viewhiddenassigns', $context)? '':' AND r.hidden = 0 ';

    // exclude users with roles we are avoiding
    if ($avoidroles) {
        $adminroles = 'AND r.roleid NOT IN (';
        $adminroles .= implode(',', $avoidroles);
        $adminroles .= ')';
    } else {
        $adminroles = '';
    }

    // join on 2 conditions
    // otherwise we run into the problem of having records in ul table, but not relevant course
    // and user record is not pulled out
    $where  = "WHERE (r.contextid = $context->id OR r.contextid in $listofcontexts)
        AND u.deleted = 0 $selectrole
        AND (ul.courseid = $course->id OR ul.courseid IS NULL)
        AND u.username != 'guest'
        $adminroles
        $hiddensql ";
        $where .= get_lastaccess_sql($accesssince);

    $wheresearch = '';

    if (!empty($search)) {
        $LIKE = sql_ilike();
        $fullname  = sql_fullname('u.firstname','u.lastname');
        $wheresearch .= ' AND ('. $fullname .' '. $LIKE .'\'%'. $search .'%\' OR email '. $LIKE .'\'%'. $search .'%\' OR idnumber '.$LIKE.' \'%'.$search.'%\') ';

    }

    if ($currentgroup) {    // Displaying a group by choice
        // FIX: TODO: This will not work if $currentgroup == 0, i.e. "those not in a group"
        $from  .= 'LEFT JOIN '.$CFG->prefix.'groups_members gm ON u.id = gm.userid ';
        $where .= ' AND gm.groupid = '.$currentgroup;
    }

    $totalcount = count_records_sql('SELECT COUNT(distinct u.id) '.$from.$where);   // Each user could have > 1 role

    if ($table->get_sql_where()) {
        $where .= ' AND '.$table->get_sql_where();
    }

    if ($table->get_sql_sort()) {
        $sort = ' ORDER BY '.$table->get_sql_sort();
    } else {
        $sort = '';
    }

    $matchcount = count_records_sql('SELECT COUNT(distinct u.id) '.$from.$where.$wheresearch);

    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $matchcount);

    $userlist = get_records_sql($select.$from.$where.$wheresearch.$sort,
            $table->get_page_start(),  $table->get_page_size());

    /// If there are multiple Roles in the course, then show a drop down menu for switching

    if (count($rolenames) > 1) {
        echo '<div class="rolesform">';
        echo get_string('currentrole', 'role').': ';
        $rolenames = array(0 => get_string('all')) + $rolenames;
        popup_form("$CFG->wwwroot/user/index.php?contextid=$context->id&amp;sifirst=&amp;silast=&amp;roleid=", $rolenames,
                   'rolesform', $roleid, '');
        echo '</div>';
    }

    if ($roleid) {
        if (!$currentrole = get_record('role','id',$roleid)) {
            error('That role does not exist');
        }
        $a->number = $totalcount;
        $a->role = $currentrole->name;
        $heading = format_string(get_string('xuserswiththerole', 'role', $a));
        if (user_can_assign($context, $roleid)) {
            $heading .= ' <a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?roleid='.$roleid.'&amp;contextid='.$context->id.'">';
            $heading .= '<img src="'.$CFG->pixpath.'/i/edit.gif" class="icon" alt="" /></a>';
        }
        print_heading($heading, 'center', 3);
    } else {
        if ($matchcount < $totalcount) {
            print_heading(get_string('allparticipants').': '.$matchcount.'/'.$totalcount, '', 3);
        } else {
            print_heading(get_string('allparticipants').': '.$matchcount, '', 3);
        }
    }


    if ($bulkoperations) {
        echo '
        <script type="text/javascript">
        //<![CDATA[
        function checksubmit(form) {
            var destination = form.formaction.options[form.formaction.selectedIndex].value;
            if (destination == "" || !checkchecked(form)) {
                form.formaction.selectedIndex = 0;
                return false;
            } else {
                return true;
            }
        }

        function checkchecked(form) {
            var inputs = document.getElementsByTagName(\'INPUT\');
            var checked = false;
            inputs = filterByParent(inputs, function() {return form;});
            for(var i = 0; i < inputs.length; ++i) {
                if (inputs[i].type == \'checkbox\' && inputs[i].checked) {
                    checked = true;
                }
            }
            return checked;
        }
        //]]>
        </script>
            ';
        echo '<form action="action_redir.php" method="post" id="participantsform" onsubmit="return checksubmit(this);">';
        echo '<div>';
        echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
        echo '<input type="hidden" name="returnto" value="'.s($_SERVER['REQUEST_URI']).'" />';
    }

    if ($CFG->longtimenosee > 0 && $CFG->longtimenosee < 1000 && $totalcount > 0) {
        echo '<p id="longtimenosee">('.get_string('unusedaccounts', '', $CFG->longtimenosee).')</p>';
    }

    if ($fullmode) {    // Print simple listing
        if ($totalcount < 1) {
            print_heading(get_string('nothingtodisplay'));
        } else {
            if ($totalcount > $perpage) {

                $firstinitial = $table->get_initial_first();
                $lastinitial  = $table->get_initial_last();
                $strall = get_string('all');
                $alpha  = explode(',', get_string('alphabet'));

                // Bar of first initials

                echo '<div class="initialbar firstinitial">'.get_string('firstname').' : ';
                if(!empty($firstinitial)) {
                    echo '<a href="'.$baseurl.'&amp;sifirst=">'.$strall.'</a>';
                } else {
                    echo '<strong>'.$strall.'</strong>';
                }
                foreach ($alpha as $letter) {
                    if ($letter == $firstinitial) {
                        echo ' <strong>'.$letter.'</strong>';
                    } else {
                        echo ' <a href="'.$baseurl.'&amp;sifirst='.$letter.'">'.$letter.'</a>';
                    }
                }
                echo '</div>';

                // Bar of last initials

                echo '<div class="initialbar lastinitial">'.get_string('lastname').' : ';
                if(!empty($lastinitial)) {
                    echo '<a href="'.$baseurl.'&amp;silast=">'.$strall.'</a>';
                } else {
                    echo '<strong>'.$strall.'</strong>';
                }
                foreach ($alpha as $letter) {
                    if ($letter == $lastinitial) {
                        echo ' <strong>'.$letter.'</strong>';
                    } else {
                        echo ' <a href="'.$baseurl.'&amp;silast='.$letter.'">'.$letter.'</a>';
                    }
                }
                echo '</div>';

                print_paging_bar($matchcount, intval($table->get_page_start() / $perpage), $perpage, $baseurl.'&amp;', 'spage');
            }

            if ($matchcount > 0) {
                foreach ($userlist as $user) {
                    print_user($user, $course, $bulkoperations);
                }

            } else {
                print_heading(get_string('nothingtodisplay'));
            }
        }

    } else {
        $countrysort = (strpos($sort, 'country') !== false);
        $timeformat = get_string('strftimedate');


        if (!empty($userlist))  {
            foreach ($userlist as $user) {
                if ($user->hidden) {
                // if the assignment is hidden, display icon
                    $hidden = "<img src=\"{$CFG->pixpath}/t/hide.gif\" alt=\"".get_string('hiddenassign')."\" class=\"hide-show-image\"/>";
                } else {
                    $hidden = '';
                }

                if ($user->lastaccess) {
                    $lastaccess = format_time(time() - $user->lastaccess, $datestring);
                } else {
                    $lastaccess = $strnever;
                }

                if (empty($user->country)) {
                    $country = '';

                } else {
                    if($countrysort) {
                        $country = '('.$user->country.') '.$countries[$user->country];
                    }
                    else {
                        $country = $countries[$user->country];
                    }
                }

                $usercontext = get_context_instance(CONTEXT_USER, $user->id);

                if ($piclink = ($USER->id == $user->id || has_capability('moodle/user:viewdetails', $context) ||has_capability('moodle/user:viewdetails', $context))) {
                    $profilelink = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></strong>';
                } else {
                    $profilelink = '<strong>'.fullname($user).'</strong>';
                }

                $data = array (
                        print_user_picture($user->id, $course->id, $user->picture, false, true, $piclink),
                        $profilelink);

                if (!isset($hiddenfields['city'])) {
                    $data[] = $user->city;
                }
                if (!isset($hiddenfields['country'])) {
                    $data[] = $country;
                }
                if (!isset($hiddenfields['lastaccess'])) {
                    $data[] = $lastaccess;
                }
                if ($course->enrolperiod) {
                    if ($user->timeend) {
                        $data[] = userdate($user->timeend, $timeformat);
                    } else {
                        $data[] = get_string('unlimited');
                    }
                }
                if ($bulkoperations) {
                    $data[] = '<input type="checkbox" name="user'.$user->id.'" />';
                }
                $table->add_data($data);

            }
        }

        $table->print_html();

    }

    if ($bulkoperations) {
        echo '<br /><div class="buttons">';
        echo '<input type="button" onclick="checkall()" value="'.get_string('selectall').'" /> ';
        echo '<input type="button" onclick="checknone()" value="'.get_string('deselectall').'" /> ';
        $displaylist = array();
        // fix for MDL-8885, only show this if user has capability
        if (has_capability('moodle/site:readallmessages', $context) && !empty($CFG->messaging)) {
            $displaylist['messageselect.php'] = get_string('messageselectadd');
        }
        if (has_capability('moodle/notes:manage', $context)) {
            $displaylist['addnote.php'] = get_string('addnewnote', 'notes');
            $displaylist['groupaddnote.php'] = get_string('groupaddnewnote', 'notes');
        }
        $displaylist['extendenrol.php'] = get_string('extendenrol');
        $displaylist['groupextendenrol.php'] = get_string('groupextendenrol');

        helpbutton("participantswithselectedusers", get_string("withselectedusers"));
        choose_from_menu ($displaylist, "formaction", "", get_string("withselectedusers"), "if(checksubmit(this.form))this.form.submit();", "");
        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        echo '<input type="submit" value="' . get_string('ok') . '" />';
        echo '</div>';
        echo '</div>';
        echo '</form>';

    }

    if ($bulkoperations && $totalcount > ($perpage*3)) {
        echo '<form action="index.php"><div><input type="hidden" name="id" value="'.$course->id.'" />'.get_string('search').':&nbsp;'."\n";
        echo '<input type="text" name="search" value="'.s($search).'" />&nbsp;<input type="submit" value="'.get_string('search').'" /></div></form>'."\n";
    }

    $perpageurl = preg_replace('/&amp;perpage=\d*/','', $baseurl);
    if ($perpage == SHOW_ALL_PAGE_SIZE) {
        echo '<div id="showall"><a href="'.$perpageurl.'&amp;perpage='.DEFAULT_PAGE_SIZE.'">'.get_string('showperpage', '', DEFAULT_PAGE_SIZE).'</a></div>';

    } else if ($matchcount > 0 && $perpage < $matchcount) {
        echo '<div id="showall"><a href="'.$perpageurl.'&amp;perpage='.SHOW_ALL_PAGE_SIZE.'">'.get_string('showall', '', $matchcount).'</a></div>';
    }

    print_footer($course);




function get_lastaccess_sql($accesssince='') {
    if (empty($accesssince)) {
        return '';
    }
    if ($accesssince == -1) { // never
        return ' AND ul.timeaccess = 0';
    } else {
        return ' AND ul.timeaccess != 0 AND timeaccess < '.$accesssince;
    }
}

?>
