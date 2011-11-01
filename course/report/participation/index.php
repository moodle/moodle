<?php

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/tablelib.php');

    define('DEFAULT_PAGE_SIZE', 20);
    define('SHOW_ALL_PAGE_SIZE', 5000);

    $id         = required_param('id', PARAM_INT); // course id.
    $roleid     = optional_param('roleid', 0, PARAM_INT); // which role to show
    $instanceid = optional_param('instanceid', 0, PARAM_INT); // instance we're looking at.
    $timefrom   = optional_param('timefrom', 0, PARAM_INT); // how far back to look...
    $action     = optional_param('action', '', PARAM_ALPHA);
    $page       = optional_param('page', 0, PARAM_INT);                     // which page to show
    $perpage    = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page

    $url = new moodle_url('/course/report/participation/index.php', array('id'=>$id));
    if ($roleid !== 0) $url->param('roleid');
    if ($instanceid !== 0) $url->param('instanceid');
    if ($timefrom !== 0) $url->param('timefrom');
    if ($action !== '') $url->param('action');
    if ($page !== 0) $url->param('page');
    if ($perpage !== DEFAULT_PAGE_SIZE) $url->param('perpage');
    $PAGE->set_url($url);
    $PAGE->set_pagelayout('admin');

    if ($action != 'view' and $action != 'post') {
        $action = ''; // default to all (don't restrict)
    }

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourse');
    }

    if ($roleid != 0 and !$role = $DB->get_record('role', array('id'=>$roleid))) {
        print_error('invalidrole');
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/participation:view', $context);

    add_to_log($course->id, "course", "report participation", "report/participation/index.php?id=$course->id", $course->id);

    $strparticipation = get_string('participationreport');
    $strviews         = get_string('views');
    $strposts         = get_string('posts');
    $strview          = get_string('view');
    $strpost          = get_string('post');
    $strallactions    = get_string('allactions');
    $strreports       = get_string('reports');

    $actionoptions = array('' => $strallactions,
                           'view' => $strview,
                           'post' => $strpost,);
    if (!array_key_exists($action, $actionoptions)) {
        $action = '';
    }

    $PAGE->set_title($course->shortname .': '. $strparticipation);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $modinfo = get_fast_modinfo($course);

    $modules = $DB->get_records_select('modules', "visible = 1", null, 'name ASC');

    $instanceoptions = array();
    foreach ($modules as $module) {
        if (empty($modinfo->instances[$module->name])) {
            continue;
        }
        $instances = array();
        foreach ($modinfo->instances[$module->name] as $cm) {
            // Skip modules such as label which do not actually have links;
            // this means there's nothing to participate in
            if (!$cm->has_view()) {
                continue;
            }
            $instances[$cm->id] = format_string($cm->name);
        }
        if (count($instances) == 0) {
            continue;
        }
        $instanceoptions[] = array(get_string('modulenameplural', $module->name)=>$instances);
    }

    $timeoptions = array();
    // get minimum log time for this course
    $minlog = $DB->get_field_sql('SELECT min(time) FROM {log} WHERE course = ?', array($course->id));

    $now = usergetmidnight(time());

    // days
    for ($i = 1; $i < 7; $i++) {
        if (strtotime('-'.$i.' days',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' days',$now)] = get_string('numdays','moodle',$i);
        }
    }
    // weeks
    for ($i = 1; $i < 10; $i++) {
        if (strtotime('-'.$i.' weeks',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' weeks',$now)] = get_string('numweeks','moodle',$i);
        }
    }
    // months
    for ($i = 2; $i < 12; $i++) {
        if (strtotime('-'.$i.' months',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' months',$now)] = get_string('nummonths','moodle',$i);
        }
    }
    // try a year
    if (strtotime('-1 year',$now) >= $minlog) {
        $timeoptions[strtotime('-1 year',$now)] = get_string('lastyear');
    }

    $roleoptions = array();
    // TODO: we need a new list of roles that are visible here
    if ($roles = get_roles_used_in_context($context)) {
        foreach ($roles as $r) {
            $roleoptions[$r->id] = $r->name;
        }
    }
    $guestrole = get_guest_role();
    if (empty($roleoptions[$guestrole->id])) {
            $roleoptions[$guestrole->id] = $guestrole->name;
    }

    $roleoptions = role_fix_names($roleoptions, $context);

    // print first controls.
    echo '<form class="participationselectform" action="index.php" method="get"><div>'."\n".
         '<input type="hidden" name="id" value="'.$course->id.'" />'."\n";
    echo '<label for="menuinstanceid">'.get_string('activitymodule').'</label>'."\n";
    echo html_writer::select($instanceoptions, 'instanceid', $instanceid);
    echo '<label for="menutimefrom">'.get_string('lookback').'</label>'."\n";
    echo html_writer::select($timeoptions,'timefrom',$timefrom);
    echo '<label for="menuroleid">'.get_string('showonly').'</label>'."\n";
    echo html_writer::select($roleoptions,'roleid',$roleid,false);
    echo '<label for="menuaction">'.get_string('showactions').'</label>'."\n";
    echo html_writer::select($actionoptions,'action',$action,false);
    echo '<input type="submit" value="'.get_string('go').'" />'."\n</div></form>\n";

    $baseurl =  $CFG->wwwroot.'/course/report/participation/index.php?id='.$course->id.'&amp;roleid='
        .$roleid.'&amp;instanceid='.$instanceid.'&amp;timefrom='.$timefrom.'&amp;action='.$action.'&amp;perpage='.$perpage;

    if (!empty($instanceid) && !empty($roleid)) {
        // from here assume we have at least the module we're using.
        $cm = $modinfo->cms[$instanceid];
        $modulename = get_string('modulename', $cm->modname);

        include_once($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php');

        $viewfun = $cm->modname.'_get_view_actions';
        $postfun = $cm->modname.'_get_post_actions';

        if (!function_exists($viewfun) || !function_exists($postfun)) {
            print_error('modulemissingcode', 'error', $baseurl, $cm->modname);
        }

        $viewnames = $viewfun();
        $postnames = $postfun();

        $table = new flexible_table('course-participation-'.$course->id.'-'.$cm->id.'-'.$roleid);
        $table->course = $course;

        $table->define_columns(array('fullname','count','select'));
        $table->define_headers(array(get_string('user'),((!empty($action)) ? get_string($action) : get_string('allactions')),get_string('select')));
        $table->define_baseurl($baseurl);

        $table->set_attribute('cellpadding','5');
        $table->set_attribute('class', 'generaltable generalbox reporttable');

        $table->sortable(true,'lastname','ASC');
        $table->no_sorting('select');

        $table->set_control_variables(array(
                                            TABLE_VAR_SORT    => 'ssort',
                                            TABLE_VAR_HIDE    => 'shide',
                                            TABLE_VAR_SHOW    => 'sshow',
                                            TABLE_VAR_IFIRST  => 'sifirst',
                                            TABLE_VAR_ILAST   => 'silast',
                                            TABLE_VAR_PAGE    => 'spage'
                                            ));
        $table->setup();

        switch ($action) {
            case 'view':
                $actions = $viewnames;
                break;
            case 'post':
                $actions = $postnames;
                break;
            default:
                // some modules have stuff we want to hide, ie mail blocked etc so do actually need to limit here.
                $actions = array_merge($viewnames, $postnames);
        }

        list($actionsql, $params) = $DB->get_in_or_equal($actions, SQL_PARAMS_NAMED, 'action');
        $actionsql = "action $actionsql";

        $relatedcontexts = get_related_contexts_string($context);

        $sql = "SELECT ra.userid, u.firstname, u.lastname, u.idnumber, l.actioncount AS count
                FROM (SELECT * FROM {role_assignments} WHERE contextid $relatedcontexts AND roleid = :roleid ) ra
                JOIN {user} u ON u.id = ra.userid
                LEFT JOIN (
                    SELECT userid, COUNT(action) AS actioncount FROM {log} WHERE cmid = :instanceid AND time > :timefrom AND $actionsql GROUP BY userid
                ) l ON (l.userid = ra.userid)";
        $params['roleid'] = $roleid;
        $params['instanceid'] = $instanceid;
        $params['timefrom'] = $timefrom;

        list($twhere, $tparams) = $table->get_sql_where();
        if ($twhere) {
            $sql .= ' WHERE '.$twhere; //initial bar
            $params = array_merge($params, $tparams);
        }

        if ($table->get_sql_sort()) {
            $sql .= ' ORDER BY '.$table->get_sql_sort();
        }

        $countsql = "SELECT COUNT(DISTINCT(ra.userid))
                       FROM {role_assignments} ra
                       JOIN {user} u ON u.id = ra.userid
                      WHERE ra.contextid $relatedcontexts AND ra.roleid = :roleid";

        $totalcount = $DB->count_records_sql($countsql, $params);

        if ($twhere) {
            $matchcount = $DB->count_records_sql($countsql.' AND '.$twhere, $params);
        } else {
            $matchcount = $totalcount;
        }

        echo '<div id="participationreport">' . "\n";
        echo '<p class="modulename">'.$modulename . ' ' . $strviews.': '.implode(', ',$viewnames).'<br />'."\n"
            . $modulename . ' ' . $strposts.': '.implode(', ',$postnames).'</p>'."\n";

        $table->initialbars($totalcount > $perpage);
        $table->pagesize($perpage, $matchcount);

        if (!$users = $DB->get_records_sql($sql, $params, $table->get_page_start(), $table->get_page_size())) {
            $users = array(); // tablelib will handle saying 'Nothing to display' for us.
        }

        $data = array();

        $a->count = $totalcount;
        $a->items = $role->name;

        if ($matchcount != $totalcount) {
            $a->count = $matchcount.'/'.$a->count;
        }

        echo '<h2>'.get_string('counteditems', '', $a).'</h2>'."\n";

        echo '<form action="'.$CFG->wwwroot.'/user/action_redir.php" method="post" id="studentsform">'."\n";
        echo '<div>'."\n";
        echo '<input type="hidden" name="id" value="'.$id.'" />'."\n";
        echo '<input type="hidden" name="returnto" value="'. s($FULLME) .'" />'."\n";
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />'."\n";

        foreach ($users as $u) {
            $data = array('<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&amp;course='.$course->id.'">'.fullname($u,true).'</a>'."\n",
                          ((!empty($u->count)) ? get_string('yes').' ('.$u->count.') ' : get_string('no')),
                          '<input type="checkbox" class="usercheckbox" name="user'.$u->userid.'" value="'.$u->count.'" />'."\n",
                          );
            $table->add_data($data);
        }

        $table->print_html();

        if ($perpage == SHOW_ALL_PAGE_SIZE) {
            echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.DEFAULT_PAGE_SIZE.'">'.get_string('showperpage', '', DEFAULT_PAGE_SIZE).'</a></div>'."\n";
        }
        else if ($matchcount > 0 && $perpage < $matchcount) {
            echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.SHOW_ALL_PAGE_SIZE.'">'.get_string('showall', '', $matchcount).'</a></div>'."\n";
        }

        echo '<div class="selectbuttons">';
        echo '<input type="button" id="checkall" value="'.get_string('selectall').'" /> '."\n";
        echo '<input type="button" id="checknone" value="'.get_string('deselectall').'" /> '."\n";
        if ($perpage >= $matchcount) {
            echo '<input type="button" id="checknos" value="'.get_string('selectnos').'" />'."\n";
        }
        echo '</div>';
        echo '<div>';
        echo '<label for="formaction">'.get_string('withselectedusers').'</label>';
        $displaylist['messageselect.php'] = get_string('messageselectadd');
        echo html_writer::select($displaylist, 'formaction', '', array(''=>'choosedots'), array('id'=>'formactionselect'));
        echo $OUTPUT->help_icon('withselectedusers');
        echo '<input type="submit" value="' . get_string('ok') . '" />'."\n";
        echo '</div>';
        echo '</div>'."\n";
        echo '</form>'."\n";
        echo '</div>'."\n";

        $PAGE->requires->js_init_call('M.coursereport_participation.init');
    }

    echo $OUTPUT->footer();


