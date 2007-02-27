<?php // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $strparticipation = get_string('participationreport');
    $strviews         = get_string('views');
    $strposts         = get_string('posts');
    $strview          = get_string('view');
    $strpost          = get_string('post');
    $strallactions    = get_string('allactions');



    $allowedmodules = array('assignment','book','chat','choice','exercise','forum','glossary','hotpot',
                            'journal','lesson','questionnaire','quiz','resource','scorm',
                            'survey','wiki','workshop'); // some don't make sense here - eg 'label'

    if (!$modules = get_records_sql('SELECT DISTINCT module,name FROM '.$CFG->prefix.'course_modules cm JOIN '.
                                    $CFG->prefix.'modules m ON cm.module = m.id WHERE course = '.$course->id)) {
        print_error('noparticipatorycms','', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }


    $modoptions = array();
    foreach ($modules as $m) {
        if (in_array($m->name,$allowedmodules)) {
            $modoptions[$m->module] = get_string('modulename',$m->name);
        }
    }

    $timeoptions = array();
    // get minimum log time for this course
    $minlog = get_field_sql('SELECT min(time) FROM '.$CFG->prefix.'log WHERE course = '.$course->id);

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

    $useroptions = array();
    if ($roles = get_roles_on_exact_context(get_context_instance(CONTEXT_COURSE,$course->id))) {
        foreach ($roles as $role) {
            $useroptions[$role->id] = $role->name;
        }
    }
    $guestrole = get_guest_role();
    if (empty($useroptions[$guestrole->id])) { 
            $useroptions[$guestrole->id] = $guestrole->name;
    }
    $actionoptions = array('' => $strallactions,
                           'view' => $strview,
                           'post' => $strpost,
                           );
    
    
    // print first controls.
    echo '<form class="participationselectform" action="'.$CFG->wwwroot.'/course/report/participation/index.php" method="get"><div>'."\n".
         '<input type="hidden" name="id" value="'.$course->id.'" />'."\n";
    echo '<label for="menumoduleid">'.get_string('activitymodule').'</label>';
    choose_from_menu($modoptions,'moduleid',0);
    echo '<label for="menutimefrom">'.get_string('lookback').'</label>';
    choose_from_menu($timeoptions,'timefrom',0);
    echo '<label for="menuroleid">'.get_string('showonly').'</label>';
    choose_from_menu($useroptions,'roleid',0,'');
    echo '<label for="menuaction">'.get_string('showactions').'</label>';
    choose_from_menu($actionoptions,'action',0,'');
    helpbutton('participationreport',get_string('participationreport'));
    echo '<input type="submit" value="'.get_string('go').'" />'."\n</div></form>\n";


?>
