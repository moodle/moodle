<?php

//  Lists all the users within a given course

    require_once('../config.php');
    require_once($CFG->libdir.'/tablelib.php');

    define('USER_SMALL_CLASS', 20);   // Below this is considered small
    define('USER_LARGE_CLASS', 200);  // Above this is considered large
    define('DEFAULT_PAGE_SIZE', 20);
    define('SHOW_ALL_PAGE_SIZE', 5000);
    define('MODE_BRIEF', 0);
    define('MODE_USERDETAILS', 1);
    define('MODE_ENROLDETAILS', 2);

    $page         = optional_param('page', 0, PARAM_INT);                     // which page to show
    $perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page
    $mode         = optional_param('mode', NULL);                             // use the MODE_ constants
    $accesssince  = optional_param('accesssince',0,PARAM_INT);                // filter by last access. -1 = never
    $search       = optional_param('search','',PARAM_CLEAN);
    $roleid       = optional_param('roleid', 0, PARAM_INT);                   // optional roleid, -1 means all site users on frontpage

    $contextid    = optional_param('contextid', 0, PARAM_INT);                // one of this or
    $courseid     = optional_param('id', 0, PARAM_INT);                       // this are required

    $PAGE->set_url('/user/index.php', array(
            'page' => $page,
            'perpage' => $perpage,
            'mode' => $mode,
            'accesssince' => $accesssince,
            'search' => $search,
            'roleid' => $roleid,
            'contextid' => $contextid,
            'courseid' => $courseid));

    if ($contextid) {
        if (! $context = get_context_instance_by_id($contextid)) {
            print_error('invalidcontext');
        }
        if (! $course = $DB->get_record('course', array('id'=>$context->instanceid))) {
            print_error('invalidcourseid');
        }
    } else {
        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourseid');
        }
        if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
            print_error('invalidcontext');
        }
    }
    // not needed anymore
    unset($contextid);
    unset($courseid);

    require_login($course);

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $frontpagectx = get_context_instance(CONTEXT_COURSE, SITEID);

    if ($context->id != $frontpagectx->id) {
        require_capability('moodle/course:viewparticipants', $context);
    } else {
        require_capability('moodle/site:viewparticipants', $systemcontext);
        // override the default on frontpage
        $roleid = optional_param('roleid', -1, PARAM_INT);
    }

    /// front page course is different
    $rolenames = array();
    $avoidroles = array();

    $rolenamesurl = new moodle_url("$CFG->wwwroot/user/index.php?contextid=$context->id&sifirst=&silast=");

    if ($roles = get_roles_used_in_context($context, true)) {
        // We should ONLY allow roles with moodle/course:view because otherwise we get little niggly issues
        // like MDL-8093
        // We should further exclude "admin" users (those with "doanything" at site level) because
        // Otherwise they appear in every participant list

        $canviewroles    = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context);
        $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $systemcontext);

        if ($context->id == $frontpagectx->id) {
            //we want admins listed on frontpage too
            foreach ($doanythingroles as $dar) {
                $canviewroles[$dar->id] = $dar;
            }
            $doanythingroles = array();
        }

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

    if ($context->id == $frontpagectx->id and $CFG->defaultfrontpageroleid) {
        // default frontpage role is assigned to all site users
        unset($rolenames[$CFG->defaultfrontpageroleid]);
    }

    // no roles to display yet?
    // frontpage course is an exception, on the front page course we should display all users
    if (empty($rolenames) && $context->id != $frontpagectx->id) {
        if (has_capability('moodle/role:assign', $context)) {
            redirect($CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id);
        } else {
            print_error('noparticipants');
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
        $mode = (int)$mode;
        $SESSION->userindexmode = $mode;
    } else if (isset($SESSION->userindexmode)) {
        $mode = (int)$SESSION->userindexmode;
    } else {
        $mode = MODE_BRIEF;
    }

/// Check to see if groups are being used in this course
/// and if so, set $currentgroup to reflect the current group

    $groupmode    = groups_get_course_groupmode($course);   // Groups are being used
    $currentgroup = groups_get_course_group($course, true);

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup  = NULL;
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context));

    if ($course->id===SITEID) {
        $PAGE->navbar->ignore_active();
    }

    $PAGE->navbar->add(get_string('participants'));
    $PAGE->set_title("$course->shortname: ".get_string('participants'));
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    if ($isseparategroups and (!$currentgroup) ) {
        // The user is not in the group so show message and exit
        echo $OUTPUT->heading(get_string("notingroup"));
        echo $OUTPUT->footer();
        exit;
    }

    // Should use this variable so that we don't break stuff every time a variable is added or changed.
    $baseurl = new moodle_url('/user/index.php', array(
            'contextid' => $context->id,
            'roleid' => $roleid,
            'id' => $course->id,
            'perpage' => $perpage,
            'accesssince' => $accesssince,
            'search' => s($search)));

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
    $userindexpage = true;

    require_once($CFG->dirroot .'/user/tabs.php');

/// Get the hidden field list
    if (has_capability('moodle/course:viewhiddenuserfields', $context)) {
        $hiddenfields = array();  // teachers and admins are allowed to see everything
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    if (isset($hiddenfields['lastaccess'])) {
        // do not allow access since filtering
        $accesssince = 0;
    }

/// Print settings and things in a table across the top
    $controlstable = new html_table();
    $controlstable->add_class('controls');
    $controlstable->cellspacing = 0;
    $controlstable->data[] = new html_table_row();

/// Print my course menus
    if ($mycourses = get_my_courses($USER->id)) {
        $courselist = array();
        $popupurl = new moodle_url('/user/index.php?roleid='.$roleid.'&sifirst=&silast=');
        foreach ($mycourses as $mycourse) {
            $courselist[$mycourse->id] = format_string($mycourse->shortname);
        }
        if (has_capability('moodle/site:viewparticipants', $systemcontext)) {
            unset($courselist[SITEID]);
            $courselist = array(SITEID => format_string($SITE->shortname)) + $courselist;
        }
        $select = new single_select($popupurl, 'id', $courselist, $course->id, array(''=>'choosedots'), 'courseform');
        $select->set_label(get_string('mycourses'));
        $controlstable->data[0]->cells[] = $OUTPUT->render($select);
    }

    $controlstable->data[0]->cells[] = groups_print_course_menu($course, $baseurl->out());

    if (!isset($hiddenfields['lastaccess'])) {
        // get minimum lastaccess for this course and display a dropbox to filter by lastaccess going back this far.
        // we need to make it diferently for normal courses and site course
        if ($context->id != $frontpagectx->id) {
            $minlastaccess = $DB->get_field_sql('SELECT min(timeaccess)
                                                   FROM {user_lastaccess}
                                                  WHERE courseid = ?
                                                        AND timeaccess != 0', array($course->id));
            $lastaccess0exists = $DB->record_exists('user_lastaccess', array('courseid'=>$course->id, 'timeaccess'=>0));
        } else {
            $minlastaccess = $DB->get_field_sql('SELECT min(lastaccess)
                                                   FROM {user}
                                                  WHERE lastaccess != 0');
            $lastaccess0exists = $DB->record_exists('user', array('lastaccess'=>0));
        }

        $now = usergetmidnight(time());
        $timeaccess = array();
        $baseurl->remove_params('accesssince');

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
            $select = new single_select($baseurl, 'accesssince', $timeoptions, $accesssince, null, 'timeoptions');
            $select->set_label(get_string('usersnoaccesssince'));
            $controlstable->data[0]->cells[] = $OUTPUT->render($select);
        }
    }

    // Decide wheteher we will fetch extra enrolment/groups data.
    //
    // MODE_ENROLDETAILS is expensive, and only suitable where the listing is small
    // (at or below DEFAULT_PAGE_SIZE) and $USER can enrol/unenrol
    // (will take 1 extra DB query - 2 on Oracle)
    //
    if ($course->id != SITEID && $perpage <= DEFAULT_PAGE_SIZE
        && has_capability('moodle/role:assign',$context)) {
        $allowenroldetails=true;
    } else {
        $allowenroldetails=false;
    }
    if ($mode === MODE_ENROLDETAILS && !($allowenroldetails)) {
        // conditions haven't been met - reset
        $mode = MODE_BRIEF;
    }

    $formatmenu = array( '0' => get_string('brief'),
                         '1' => get_string('userdetails'));
    if ($allowenroldetails) {
        $formatmenu['2']= get_string('enroldetails');
    }
    $select = new single_select($baseurl, 'mode', $formatmenu, $mode, null, 'formatmenu');
    $select->set_label(get_string('userlist'));
    $userlistcell = new html_table_cell();
    $userlistcell->add_class('right');
    $userlistcell->text = $OUTPUT->render($select);
    $controlstable->data[0]->cells[] = $userlistcell;

    echo $OUTPUT->table($controlstable);

    if ($currentgroup and (!$isseparategroups or has_capability('moodle/site:accessallgroups', $context))) {    /// Display info about the group
        if ($group = groups_get_group($currentgroup)) {
            if (!empty($group->description) or (!empty($group->picture) and empty($group->hidepicture))) {
                $groupinfotable = new html_table();
                $groupinfotable->add_class('groupinfobox');
                $picturecell = new html_table_cell();
                $picturecell->add_classes(array('left', 'side', 'picture'));
                $picturecell->text = print_group_picture($group, $course->id, true, false, false);

                $contentcell = new html_table_cell();
                $contentcell->add_class('content');
                $contentcell->text = print_group_picture($group, $course->id, true, false, false);

                $contentheading = $group->name;
                if (has_capability('moodle/course:managegroups', $context)) {
                    $aurl = new moodle_url('/group/group.php', array('id' => $group->id, 'courseid' => $group->courseid));
                    $contentheading .= '&nbsp;' . $OUTPUT->action_icon($aurl, new pix_icon('t/edit', get_string('editgroupprofile')));
                }

                $group->description = file_rewrite_pluginfile_urls($group->description, 'pluginfile.php', $context->id, 'course_group_description', $group->id);
                if (!isset($group->descriptionformat)) {
                    $group->descriptionformat = FORMAT_MOODLE;
                }
                $contentcell->text = $OUTPUT->heading($contentheading, 3) . format_text($group->description, $group->descriptionformat);
                $groupinfotable->data[] = html_table_row::make(array($picturecell, $contentcell));
                echo $OUTPUT->table($groupinfotable);
            }
        }
    }

    /// Define a table showing a list of users in the current role selection

    $tablecolumns = array('userpic', 'fullname');
    $tableheaders = array(get_string('userpic'), get_string('fullnameuser'));
    if ($mode === MODE_BRIEF && !isset($hiddenfields['city'])) {
        $tablecolumns[] = 'city';
        $tableheaders[] = get_string('city');
    }
    if ($mode === MODE_BRIEF && !isset($hiddenfields['country'])) {
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

    if ($mode === MODE_ENROLDETAILS) {
        $tablecolumns[] = 'roles';
        $tableheaders[] = get_string('roles');
        if ($groupmode != 0) {
            $tablecolumns[] = 'groups';
            $tableheaders[] = get_string('groups');
            if (!empty($CFG->enablegroupings)) {
                $tablecolumns[] = 'groupings';
                $tableheaders[] = get_string('groupings', 'group');
            }
        }
    }

    if ($bulkoperations) {
        $tablecolumns[] = 'select';
        $tableheaders[] = get_string('select');
    }

    $table = new flexible_table('user-index-participants-'.$course->id);

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl->out());

    if (!isset($hiddenfields['lastaccess'])) {
        $table->sortable(true, 'lastaccess', SORT_DESC);
    }

    $table->no_sorting('roles');
    $table->no_sorting('groups');
    $table->no_sorting('groupings');
    $table->no_sorting('select');

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

    $params = array();
    // we are looking for all users with this role assigned in this context or higher
    if ($usercontexts = get_parent_contexts($context)) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
    } else {
        $listofcontexts = '('.$systemcontext->id.')'; // must be site
    }
    if ($roleid > 0) {
        $selectrole = " AND r.roleid = :roleid ";
        $params['roleid'] = $roleid;
    } else {
        $selectrole = " ";
    }

    if ($context->id != $frontpagectx->id) {
        $select = 'SELECT DISTINCT u.id, u.username, u.firstname, u.lastname,
                      u.email, u.city, u.country, u.picture,
                      u.lang, u.timezone, u.emailstop, u.maildisplay, u.imagealt,
                      COALESCE(ul.timeaccess, 0) AS lastaccess,
                      r.hidden,
                      ctx.id AS ctxid, ctx.path AS ctxpath,
                      ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel ';
        $select .= $course->enrolperiod?', r.timeend ':'';
    } else {
        if ($roleid >= 0) {
            $select = 'SELECT u.id, u.username, u.firstname, u.lastname,
                          u.email, u.city, u.country, u.picture,
                          u.lang, u.timezone, u.emailstop, u.maildisplay, u.imagealt,
                          u.lastaccess, r.hidden,
                          ctx.id AS ctxid, ctx.path AS ctxpath,
                          ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel ';
        } else {
            $select = 'SELECT u.id, u.username, u.firstname, u.lastname,
                          u.email, u.city, u.country, u.picture,
                          u.lang, u.timezone, u.emailstop, u.maildisplay, u.imagealt,
                          u.lastaccess,
                          ctx.id AS ctxid, ctx.path AS ctxpath,
                          ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel ';
        }
    }

    if ($context->id != $frontpagectx->id or $roleid >= 0) {
        $from   = "FROM {user} u
                   LEFT OUTER JOIN {context} ctx
                        ON (u.id=ctx.instanceid AND ctx.contextlevel = ".CONTEXT_USER.")
                   JOIN {role_assignments} r
                        ON u.id=r.userid
                   LEFT OUTER JOIN {user_lastaccess} ul
                        ON (r.userid=ul.userid and ul.courseid = :courseid) ";
        $params['courseid'] = $course->id;
    } else {
        // on frontpage and we want all registered users
        $from = "FROM {user} u
                 LEFT OUTER JOIN {context} ctx
                      ON (u.id=ctx.instanceid AND ctx.contextlevel = ".CONTEXT_USER.") ";
    }

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

    if ($context->id != $frontpagectx->id) {
        $where  = "WHERE (r.contextid = $context->id OR r.contextid in $listofcontexts)
                         AND u.deleted = 0 $selectrole
                         AND (ul.courseid = $course->id OR ul.courseid IS NULL)
                         AND u.username != 'guest'
                         $adminroles
                         $hiddensql ";
            $where .= get_course_lastaccess_sql($accesssince);
    } else {
        if ($roleid >= 0) {
            $where = "WHERE (r.contextid = $context->id OR r.contextid in $listofcontexts)
                      AND u.deleted = 0 $selectrole
                      AND u.username != 'guest'";
            $where .= get_user_lastaccess_sql($accesssince);
        } else {
            $where = "WHERE u.deleted = 0
                AND u.username != 'guest'";
                $where .= get_user_lastaccess_sql($accesssince);
        }
    }
    $wheresearch = '';

    if (!empty($search)) {
        $LIKE = $DB->sql_ilike();
        $fullname = $DB->sql_fullname('u.firstname','u.lastname');
        $wheresearch .= " AND ($fullname $LIKE :search1 OR email $LIKE :search2 OR idnumber $LIKE :search3) ";
        $params['search1'] = "%$search%";
        $params['search2'] = "%$search%";
        $params['search3'] = "%$search%";
    }

    if ($currentgroup) {    // Displaying a group by choice
        // FIX: TODO: This will not work if $currentgroup == 0, i.e. "those not in a group"
        $from  .= 'LEFT JOIN {groups_members} gm ON u.id = gm.userid ';
        $where .= ' AND gm.groupid = :currentgroup';
        $params['currentgroup'] = $currentgroup;
    }

    $totalcount = $DB->count_records_sql("SELECT COUNT(distinct u.id) $from $where", $params);   // Each user could have > 1 role

    if ($table->get_sql_where()) {
        $where .= ' AND '.$table->get_sql_where();
    }

    /// Always add r.hidden to sort in order to guarantee hiddens to "win"
    /// in the resolution of duplicates later - MDL-13935
    /// Only exception is frontpage that doesn't have such r.hidden info
    /// because it retrieves ALL users (without role checking) - MDL-14034
    if ($table->get_sql_sort()) {
        $sort = ' ORDER BY '.$table->get_sql_sort();
        if ($context->id != $frontpagectx->id or $roleid >= 0) {
            $sort .= ', r.hidden DESC';
        }
    } else {
        $sort = '';
        if ($context->id != $frontpagectx->id or $roleid >= 0) {
            $sort .= ' ORDER BY r.hidden DESC';
        }
    }

    $matchcount = $DB->count_records_sql("SELECT COUNT(distinct u.id) $from $where $wheresearch", $params);

    $table->initialbars(true);
    $table->pagesize($perpage, $matchcount);

    $userlist = $DB->get_recordset_sql("$select $from $where $wheresearch $sort", $params,
            $table->get_page_start(),  $table->get_page_size());

    //
    // The SELECT behind get_participants_extra() is cheaper if we pass an array
    // if IDs. We could pass the SELECT we did before (with the limit bits - tricky!)
    // but this is much cheaper. And in any case, it is only doable with limited numbers
    // of rows anyway. On a large course it will explode badly...
    //
    if ($mode===MODE_ENROLDETAILS) {
        if ($context->id != $frontpagectx->id) {
            $userids = $DB->get_fieldset_sql("SELECT DISTINCT u.id $from $where $wheresearch", $params,
                                             $table->get_page_start(),  $table->get_page_size());
        } else {
            $userids = $DB->get_fieldset_sql("SELECT u.id $from $where $wheresearch", $params,
                                             $table->get_page_start(),  $table->get_page_size());
        }
        $userlist_extra = get_participants_extra($userids, $avoidroles, $course, $context);
    }

    if ($context->id == $frontpagectx->id) {
        $strallsiteusers = get_string('allsiteusers', 'role');
        if ($CFG->defaultfrontpageroleid) {
            if ($fprole = $DB->get_record('role', array('id'=>$CFG->defaultfrontpageroleid))) {
                $fprole = role_get_name($fprole, $frontpagectx);
                $strallsiteusers = "$strallsiteusers ($fprole)";
            }
        }
        $rolenames = array(-1 => $strallsiteusers) + $rolenames;
    }

    /// If there are multiple Roles in the course, then show a drop down menu for switching
    if (count($rolenames) > 1) {
        echo '<div class="rolesform">';
        echo '<label for="rolesform_jump">'.get_string('currentrole', 'role').'&nbsp;</label>';
        if ($context->id != $frontpagectx->id) {
            $rolenames = array('0' => get_string('all')) + $rolenames;
        } else {
            if (!$CFG->defaultfrontpageroleid) {
                // we do not want "All users with role" - we already have all users in defualt frontpage role option
                $rolenames = array('0' => get_string('userswithrole', 'role')) + $rolenames;
            }
        }
        echo $OUTPUT->single_select($rolenamesurl, 'roleid', $rolenames, $roleid, null, 'rolesform');
        echo '</div>';

    } else if (count($rolenames) == 1) {
        // when all users with the same role - print its name
        echo '<div class="rolesform">';
        echo get_string('role').': ';
        $rolename = reset($rolenames);
        echo $rolename;
        echo '</div>';
    }

    if ($roleid > 0) {
        if (!$currentrole = $DB->get_record('role', array('id'=>$roleid))) {
            print_error('invalidroleid');
        }
        $a->number = $totalcount;
        // MDL-12217, use course specific rolename
        if (isset($rolenames[$currentrole->id])){
            $a->role = $rolenames[$currentrole->id];
        }else{
            $a->role = $currentrole->name;//safety net
        }
        $heading = format_string(get_string('xuserswiththerole', 'role', $a));

        if ($currentgroup and $group) {
            $a->group = $group->name;
            $heading .= ' ' . format_string(get_string('ingroup', 'role', $a));
        }

        if ($accesssince) {
            $a->timeperiod = $timeoptions[$accesssince];
            $heading .= ' ' . format_string(get_string('inactiveformorethan', 'role', $a));
        }

        $heading .= ": $a->number";
        if (user_can_assign($context, $roleid)) {
            $heading .= ' <a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?roleid='.$roleid.'&amp;contextid='.$context->id.'">';
            $heading .= '<img src="'.$OUTPUT->pix_url('i/edit') . '" class="icon" alt="" /></a>';
        }
        echo $OUTPUT->heading($heading, 3);
    } else {
        if ($course->id != SITEID && has_capability('moodle/role:assign', $context)) {
            $editlink  = ' <a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id.'">';
            $editlink .= '<img src="'.$OUTPUT->pix_url('i/edit') . '" class="icon" alt="" /></a>';
        } else {
            $editlink = '';
        }
        if ($course->id == SITEID and $roleid < 0) {
            $strallparticipants = get_string('allsiteusers', 'role');
        } else {
            $strallparticipants = get_string('allparticipants');
        }
        if ($matchcount < $totalcount) {
            echo $OUTPUT->heading($strallparticipants.': '.$matchcount.'/'.$totalcount . $editlink, 3);
        } else {
            echo $OUTPUT->heading($strallparticipants.': '.$matchcount . $editlink, 3);
        }
    }


    if ($bulkoperations) {
        echo '<form action="action_redir.php" method="post" id="participantsform">';
        echo '<div>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="returnto" value="'.s(me()).'" />';
    }

    if ($CFG->longtimenosee > 0 && $CFG->longtimenosee < 1000 && $totalcount > 0) {
        echo '<p id="longtimenosee">('.get_string('unusedaccounts', '', $CFG->longtimenosee).')</p>';
    }

    if ($mode===MODE_USERDETAILS) {    // Print simple listing
        if ($totalcount < 1) {
            echo $OUTPUT->heading(get_string('nothingtodisplay'));
        } else {
            if ($totalcount > $perpage) {

                $firstinitial = $table->get_initial_first();
                $lastinitial  = $table->get_initial_last();
                $strall = get_string('all');
                $alpha  = explode(',', get_string('alphabet'));

                // Bar of first initials

                echo '<div class="initialbar firstinitial">'.get_string('firstname').' : ';
                if(!empty($firstinitial)) {
                    echo '<a href="'.$baseurl->out().'&amp;sifirst=">'.$strall.'</a>';
                } else {
                    echo '<strong>'.$strall.'</strong>';
                }
                foreach ($alpha as $letter) {
                    if ($letter == $firstinitial) {
                        echo ' <strong>'.$letter.'</strong>';
                    } else {
                        echo ' <a href="'.$baseurl->out().'&amp;sifirst='.$letter.'">'.$letter.'</a>';
                    }
                }
                echo '</div>';

                // Bar of last initials

                echo '<div class="initialbar lastinitial">'.get_string('lastname').' : ';
                if(!empty($lastinitial)) {
                    echo '<a href="'.$baseurl->out().'&amp;silast=">'.$strall.'</a>';
                } else {
                    echo '<strong>'.$strall.'</strong>';
                }
                foreach ($alpha as $letter) {
                    if ($letter == $lastinitial) {
                        echo ' <strong>'.$letter.'</strong>';
                    } else {
                        echo ' <a href="'.$baseurl->out().'&amp;silast='.$letter.'">'.$letter.'</a>';
                    }
                }
                echo '</div>';

                $pagingbar = new paging_bar($matchcount, intval($table->get_page_start() / $perpage), $perpage, $baseurl);
                $pagingbar->pagevar = 'spage';
                echo $OUTPUT->new($pagingbar);
            }

            if ($matchcount > 0) {
                $usersprinted = array();
                foreach ($userlist as $user) {
                    if (in_array($user->id, $usersprinted)) { /// Prevent duplicates by r.hidden - MDL-13935
                        continue;
                    }
                    $usersprinted[] = $user->id; /// Add new user to the array of users printed

                    $user = make_context_subobj($user);

                    $context = get_context_instance(CONTEXT_COURSE, $course->id);
                    if (isset($user->context->id)) {
                        $usercontext = $user->context;
                    } else {
                        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
                    }

                    $countries = get_list_of_countries();

                    /// Get the hidden field list
                    if (has_capability('moodle/course:viewhiddenuserfields', $context)) {
                        $hiddenfields = array();
                    } else {
                        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
                    }
                    $table = new html_table();
                    $table->add_class('userinfobox');

                    $row = new html_table_row();
                    $row->cells[0] = new html_table_cell();
                    $row->cells[0]->add_class('left side');

                    $row->cells[0]->text = $OUTPUT->user_picture($user, array('courseid'=>$course->id));
                    $row->cells[1] = new html_table_cell();
                    $row->cells[1]->add_class('content');

                    $row->cells[1]->text = $OUTPUT->container(fullname($user, has_capability('moodle/site:viewfullnames', $context)), 'username');
                    $row->cells[1]->text .= $OUTPUT->container_start('info');

                    if (!empty($user->role)) {
                        $row->cells[1]->text .= get_string('role') .': '. $user->role .'<br />';
                    }
                    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and ($course->id != SITEID) and !isguestuser()) or
                                has_capability('moodle/course:viewhiddenuserfields', $context)) {
                        $row->cells[1]->text .= get_string('email') .': ' . html_writer::link("mailto:$user->email", $user->email) . '<br />';
                    }
                    if (($user->city or $user->country) and (!isset($hiddenfields['city']) or !isset($hiddenfields['country']))) {
                        $row->cells[1]->text .= get_string('city') .': ';
                        if ($user->city && !isset($hiddenfields['city'])) {
                            $row->cells[1]->text .= $user->city;
                        }
                        if (!empty($countries[$user->country]) && !isset($hiddenfields['country'])) {
                            if ($user->city && !isset($hiddenfields['city'])) {
                                $row->cells[1]->text .= ', ';
                            }
                            $row->cells[1]->text .= $countries[$user->country];
                        }
                        $row->cells[1]->text .= '<br />';
                    }

                    if (!isset($hiddenfields['lastaccess'])) {
                        if ($user->lastaccess) {
                            $row->cells[1]->text .= get_string('lastaccess') .': '. userdate($user->lastaccess);
                            $row->cells[1]->text .= '&nbsp; ('. format_time(time() - $user->lastaccess, $datestring) .')';
                        } else {
                            $row->cells[1]->text .= get_string('lastaccess') .': '. get_string('never');
                        }
                    }

                    $row->cells[1]->text .= $OUTPUT->container_end();

                    $row->cells[2] = new html_table_cell();
                    $row->cells[2]->add_class('links');
                    $row->cells[2]->text = '';

                    $links = array();

                    if ($CFG->bloglevel > 0) {
                        $links[] = html_writer::link(new moodle_url('/blog/index.php?userid='.$user->id), get_string('blogs','blog'));
                    }

                    if (!empty($CFG->enablenotes) and (has_capability('moodle/notes:manage', $context) || has_capability('moodle/notes:view', $context))) {
                        $links[] = html_writer::link(new moodle_url('/notes/index.php?course=' . $course->id. '&user='.$user->id), get_string('notes','notes'));
                    }

                    if (has_capability('moodle/site:viewreports', $context) or has_capability('moodle/user:viewuseractivitiesreport', $usercontext)) {
                        $links[] = html_writer::link(new moodle_url('/course/user.php?id='. $course->id .'&user='. $user->id), get_string('activity'));
                    }

                    if (has_capability('moodle/role:assign', $context) and get_user_roles($context, $user->id, false)) {  // I can unassign and user has some role
                        $links[] = html_writer::link(new moodle_url('/course/unenrol.php?id='. $course->id .'&user='. $user->id), get_string('unenrol'));
                    }

                    if ($USER->id != $user->id && !session_is_loggedinas() && has_capability('moodle/user:loginas', $context) &&
                                                 ! has_capability('moodle/site:doanything', $context, $user->id, false)) {
                        $links[] = html_writer::link(new moodle_url('/course/loginas.php?id='. $course->id .'&user='. $user->id .'&sesskey='. sesskey()), get_string('loginas'));
                    }

                    $links[] = html_writer::link(new moodle_url('/user/view.php?id='. $user->id .'&course='. $course->id), get_string('fullprofile') . '...');

                    $row->cells[2]->text .= implode('', $links);

                    if (!empty($messageselect)) {
                        $row->cells[2]->text .= '<br /><input type="checkbox" name="user'.$user->id.'" /> ';
                    }
                    $table->data = array($row);
                    echo $OUTPUT->table($table);
                }

            } else {
                echo $OUTPUT->heading(get_string('nothingtodisplay'));
            }
        }

    } else {
        $countrysort = (strpos($sort, 'country') !== false);
        $timeformat = get_string('strftimedate');


        if ($userlist)  {

            // only show the plugin if multiple enrolment plugins
            // are enabled...
            if (strpos($CFG->enrol_plugins_enabled, ',')=== false) {
                $showenrolplugin = true;
            } else {
                $showenrolplugin = false;
            }

            $usersprinted = array();
            foreach ($userlist as $user) {
                if (in_array($user->id, $usersprinted)) { /// Prevent duplicates by r.hidden - MDL-13935
                    continue;
                }
                $usersprinted[] = $user->id; /// Add new user to the array of users printed

                $user = make_context_subobj($user);
                if ( !empty($user->hidden) ) {
                // if the assignment is hidden, display icon
                    $hidden = " <img src=\"" . $OUTPUT->pix_url('t/show') . "\" title=\"".get_string('userhashiddenassignments', 'role')."\" alt=\"".get_string('hiddenassign')."\" class=\"hide-show-image\"/>";
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

                if (!isset($user->context)) {
                    $usercontext = get_context_instance(CONTEXT_USER, $user->id);
                } else {
                    $usercontext = $user->context;
                }

                if ($piclink = ($USER->id == $user->id || has_capability('moodle/user:viewdetails', $context) || has_capability('moodle/user:viewdetails', $usercontext))) {
                    $profilelink = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></strong>';
                } else {
                    $profilelink = '<strong>'.fullname($user).'</strong>';
                }

                $data = array ($OUTPUT->user_picture($user, array('courseid'=>$course->id)), $profilelink . $hidden);

                if ($mode === MODE_BRIEF && !isset($hiddenfields['city'])) {
                    $data[] = $user->city;
                }
                if ($mode === MODE_BRIEF && !isset($hiddenfields['country'])) {
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

                if (isset($userlist_extra) && isset($userlist_extra[$user->id])) {
                    $ras = $userlist_extra[$user->id]['ra'];
                    $rastring = '';
                    foreach ($ras AS $key=>$ra) {
                        $rolename = $rolenames [ $ra['roleid'] ] ;
                        if ($ra['ctxlevel'] == CONTEXT_COURSECAT) {
                            $rastring .= $rolename. ' @ ' . '<a href="'.$CFG->wwwroot.'/course/category.php?id='.$ra['ctxinstanceid'].'">'.s($ra['ccname']).'</a>';
                        } elseif ($ra['ctxlevel'] == CONTEXT_SYSTEM) {
                            $rastring .= $rolename. ' - ' . get_string('globalrole','role');
                        } else {
                            $rastring .= $rolename;
                        }
                        if ($showenrolplugin) {
                            $rastring .= '<br />';
                        } else {
                            $rastring .= ' ('. $ra['enrolplugin'] .')<br />';
                        }
                    }
                    $data[] = $rastring;
                    if ($groupmode != 0) {
                        // htmlescape with s() and implode the array
                        $data[] = implode(', ', array_map('s',$userlist_extra[$user->id]['group']));
                        if (!empty($CFG->enablegroupings)) {
                            $data[] = implode(', ', array_map('s', $userlist_extra[$user->id]['gping']));
                        }
                    }
                }

                if ($bulkoperations) {
                    $data[] = '<input type="checkbox" class="usercheckbox" name="user'.$user->id.'" />';
                }
                $table->add_data($data);

            }
        }

        $table->print_html();

    }

    if ($bulkoperations) {
        echo '<br /><div class="buttons">';
        echo '<input type="button" id="checkall" value="'.get_string('selectall').'" /> ';
        echo '<input type="button" id="checknone" value="'.get_string('deselectall').'" /> ';
        $displaylist = array();
        $displaylist['messageselect.php'] = get_string('messageselectadd');
        if (!empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context) && $context->id != $frontpagectx->id) {
            $displaylist['addnote.php'] = get_string('addnewnote', 'notes');
            $displaylist['groupaddnote.php'] = get_string('groupaddnewnote', 'notes');
        }

        if ($context->id != $frontpagectx->id) {
            $displaylist['extendenrol.php'] = get_string('extendenrol');
            $displaylist['groupextendenrol.php'] = get_string('groupextendenrol');
        }

        echo $OUTPUT->help_icon("participantswithselectedusers", get_string("withselectedusers"));
        echo html_writer::tag('label', array('for'=>'formactionid'), get_string("withselectedusers"));
        echo html_writer::select($displaylist, 'formaction', '', array(''=>'choosedots'), array('id'=>'formactionid'));

        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        echo '<noscript style="display:inline">';
        echo '<input type="submit" value="'.get_string('ok').'" />';
        echo '</noscript>';
        echo '</div></div>';
        echo '</form>';

        $module = array('name'=>'core_user', 'fullpath'=>'/user/module.js');
        $PAGE->requires->js_init_call('M.core_user.init_participation', null, false, $module);
    }

    if (has_capability('moodle/site:viewparticipants', $context) && $totalcount > ($perpage*3)) {
        echo '<form action="index.php" class="searchform"><div><input type="hidden" name="id" value="'.$course->id.'" />'.get_string('search').':&nbsp;'."\n";
        echo '<input type="text" name="search" value="'.s($search).'" />&nbsp;<input type="submit" value="'.get_string('search').'" /></div></form>'."\n";
    }

    $perpageurl = clone($baseurl);
    $perpageurl->remove_params('perpage');
    if ($perpage == SHOW_ALL_PAGE_SIZE) {
        $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
        echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');

    } else if ($matchcount > 0 && $perpage < $matchcount) {
        $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
        echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showall', '', $matchcount)), array(), 'showall');
    }

    echo $OUTPUT->footer();

    if ($userlist) {
        $userlist->close();
    }


function get_course_lastaccess_sql($accesssince='') {
    if (empty($accesssince)) {
        return '';
    }
    if ($accesssince == -1) { // never
        return ' AND ul.timeaccess = 0';
    } else {
        return ' AND ul.timeaccess != 0 AND ul.timeaccess < '.$accesssince;
    }
}

function get_user_lastaccess_sql($accesssince='') {
    if (empty($accesssince)) {
        return '';
    }
    if ($accesssince == -1) { // never
        return ' AND u.lastaccess = 0';
    } else {
        return ' AND u.lastaccess != 0 AND u.lastaccess < '.$accesssince;
    }
}

function get_participants_extra ($userids, $avoidroles, $course, $context) {
    global $CFG, $DB;

    if (count($userids) === 0 || count($avoidroles) === 0) {
        return array();
    }

    $params = array();

    $userids = implode(',', $userids);

    // turn the path into a list of context ids
    $contextids = substr($context->path, 1); // kill leading slash
    $contextids = str_replace('/', ',', $contextids);;

    if (count($avoidroles) > 0) {
        $avoidroles = implode(',', $avoidroles);
        $avoidrolescond = " AND ra.roleid NOT IN ($avoidroles) ";
    } else {
        $avoidrolescond = '';
    }

    if (!empty($CFG->enablegroupings)) {
        $gpjoin = "LEFT OUTER JOIN {groupings_groups} gpg
                        ON gpg.groupid=g.id
                   LEFT OUTER JOIN {groupings} gp
                        ON (gp.courseid={$course->id} AND gp.id=gpg.groupingid)";
        $gpselect = ',gp.id AS gpid, gp.name AS gpname ';
    } else {
        $gpjoin   = '';
        $gpselect = '';
    }

    // Note: this returns strange redundant rows, perhaps
    // due to the multiple OUTER JOINs. If we can tweak the
    // JOINs to avoid it ot
    $sql = "SELECT DISTINCT ra.userid,
                   ctx.id AS ctxid, ctx.path AS ctxpath, ctx.depth AS ctxdepth,
                   ctx.contextlevel AS ctxlevel, ctx.instanceid AS ctxinstanceid,
                   cc.name  AS ccname,
                   ra.roleid AS roleid,
                   ra.enrol AS enrolplugin,
                   g.id     AS gid, g.name AS gname
                   $gpselect
              FROM {role_assignments} ra
              JOIN {context} ctx
                   ON (ra.contextid=ctx.id)
              LEFT JOIN {course_categories} cc
                   ON (ctx.contextlevel=40 AND ctx.instanceid=cc.id)

            /* only if groups active */
              LEFT JOIN {groups_members} gm
                   ON (ra.userid=gm.userid)
              LEFT JOIN {groups} g
                   ON (gm.groupid=g.id AND g.courseid={$course->id})
            /* and if groupings is enabled... */
            $gpjoin

             WHERE ra.userid IN ( $userids )
                   AND ra.contextid in ( $contextids )
                   $avoidrolescond

          ORDER BY ra.userid, ctx.depth DESC";

    $rs = $DB->get_recordset_sql($sql, $params);
    $extra = array();

    // Data structure -
    // $extra [ $userid ] [ 'group' ] [ $groupid => 'group name']
    //                    [ 'gping' ] [ $gpingid => 'gping name']
    //                    [ 'ra' ] [  [ "$ctxid:$roleid" => [ctxid => $ctxid
    //                                                       ctxdepth =>  $ctxdepth,
    //                                                       ctxpath => $ctxpath,
    //                                                       ctxname => 'name' (categories only)
    //                                                       ctxinstid =>
    //                                                       roleid => $roleid
    //                                                       enrol => $pluginname
    //
    // Might be interesting to add to RA timestart, timeend, timemodified,
    // and modifierid (with an outer join to mdl_user!
    //

    foreach ($rs as $rec) {
        $userid = $rec->userid;

        // Prime an initial user rec...
        if (!isset($extra[$userid])) {
            $extra[$userid] = array( 'group' => array(),
                                     'gping' => array(),
                                     'ra'    => array() );
        }

        if (!empty($rec->gid)) {
            $extra[$userid]['group'][$rec->gid]= $rec->gname;
        }
        if (!empty($rec->gpid)) {
            $extra[$userid]['gping'][$rec->gpid]= $rec->gpname;
        }
        $rakey = $rec->ctxid . ':' . $rec->roleid;
        if (!isset($extra[$userid]['ra'][$rakey])) {
            $extra[$userid]['ra'][$rakey] = array('ctxid'         => $rec->ctxid,
                                                  'ctxlevel'       => $rec->ctxlevel,
                                                  'ctxinstanceid' => $rec->ctxinstanceid,
                                                  'ccname'        => $rec->ccname,
                                                  'roleid'        => $rec->roleid,
                                                  'enrolplugin'   => $rec->enrolplugin);

        }
    }
    $rs->close();
    return $extra;

}


