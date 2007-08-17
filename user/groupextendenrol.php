<?php  // $Id$
require_once("../config.php");

$id    = required_param('id', PARAM_INT);              // course id
$users = optional_param('userid', array(), PARAM_INT); // array of user id

if (! $course = get_record('course', 'id', $id)) {
    error("Course ID is incorrect");
}

$context = get_context_instance(CONTEXT_COURSE, $id);
require_login($course->id);

// to extend enrolments current user needs to be able to do role assignments
require_capability('moodle/role:assign', $context);
$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

if ((count($users) > 0) and ($form = data_submitted()) and confirm_sesskey()) {

    foreach ($form->userid as $k => $v) {
        // find all roles this student have in this course
        if ($students = get_records_sql("SELECT ra.id, ra.roleid, ra.timestart, ra.timeend
                                       FROM {$CFG->prefix}role_assignments ra
                                       WHERE userid = $v
                                       AND contextid = $context->id")) {
            // enrol these users again, with time extension
            // not that this is not necessarily a student role
            foreach ($students as $student) {
                // only extend if the user can make role assignments on this role
                if (user_can_assign($context, $student->roleid)) {
                    switch($form->extendperiod) {
                        case 0: // No change (currently this option is not available in dropdown list, but it might be ...)
                            break;
                        case -1: // unlimited
                            $student->timeend = 0;
                            break;
                        default: // extend
                            switch($form->extendbase) {
                                case 0: // course start date
                                    $student->timeend = $course->startdate + $form->extendperiod;
                                    break;
                                case 1: // student enrolment start date
                                    // we check for student enrolment date because Moodle versions before 1.9 did not set this for
                                    // unlimited enrolment courses, so it might be 0
                                    if($student->timestart > 0) {
                                        $student->timeend = $student->timestart + $form->extendperiod;
                                    }
                                    break;
                                case 2: // student enrolment start date
                                    // enrolment end equals 0 means Unlimited, so adding some time to that will still yield Unlimited
                                    if($student->timeend > 0) {
                                        $student->timeend = $student->timeend + $form->extendperiod;
                                    }
                                    break;
                                case 3: // current date
                                    $student->timeend = $today + $form->extendperiod;
                                    break;
                                case 4: // course enrolment start date
                                    if($course->enrolstartdate > 0) {
                                        $student->timeend = $course->enrolstartdate + $form->extendperiod;
                                    }
                                    break;
                                case 5: // course enrolment end date
                                    if($course->enrolenddate > 0) {
                                        $student->timeend = $course->enrolenddate + $form->extendperiod;
                                    }
                                    break;
                            }
                    }
                    role_assign($student->roleid, $v, 0, $context->id, $student->timestart, $student->timeend, 0);
                }
            }
        }
    }

    redirect("$CFG->wwwroot/user/index.php?id=$id", get_string('changessaved'));
}

/// Print headers

$navlinks = array();
$navlinks[] = array('name' => get_string('extendenrol'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));

$timeformat = get_string('strftimedate');
$unlimited = get_string('unlimited');
$periodmenu[-1] = $unlimited;
for ($i=1; $i<=365; $i++) {
    $seconds = $i * 86400;
    $periodmenu[$seconds] = get_string('numdays', '', $i);
}

$basemenu[0] = get_string('startdate') . ' (' . userdate($course->startdate, $timeformat) . ')';
$basemenu[1] = get_string('enrolmentstart');
$basemenu[2] = get_string('enrolmentend');
if($course->enrollable != 2 || ($course->enrolstartdate == 0 || $course->enrolstartdate <= $today) && ($course->enrolenddate == 0 || $course->enrolenddate > $today)) {
    $basemenu[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;
}
if($course->enrollable == 2) {
    if($course->enrolstartdate > 0) {
        $basemenu[4] = get_string('courseenrolstartdate') . ' (' . userdate($course->enrolstartdate, $timeformat) . ')';
    }
    if($course->enrolenddate > 0) {
        $basemenu[5] = get_string('courseenrolenddate') . ' (' . userdate($course->enrolenddate, $timeformat) . ')';
    }
}

$title = get_string('groupextendenrol');
print_heading($title . helpbutton('groupextendenrol', $title, 'moodle', true, false, '', true));
echo '<form method="post" action="groupextendenrol.php">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
$table->head  = array (get_string('fullname'), get_string('enrolmentstart'), get_string('enrolmentend'));
$table->align = array ('left', 'center', 'center', 'center');
$table->width = "600";
$nochange = get_string('nochange');
$notavailable = get_string('notavailable');

foreach ($_POST as $k => $v) {
    if (preg_match('/^user(\d+)$/',$k,$m)) {

        if (!($user = get_record_sql("SELECT * FROM {$CFG->prefix}user u
                                    INNER JOIN {$CFG->prefix}role_assignments ra ON u.id=ra.userid
                                    WHERE u.id={$m[1]} AND ra.contextid = $context->id"))) {
            continue;
        }
        if ($user->timestart) {
            $timestart = userdate($user->timestart, $timeformat);
        } else {
            $timestart = $notavailable;
        }
        if ($user->timeend) {
            $timeend = userdate($user->timeend, $timeformat);
        } else {
            $timeend = $unlimited;
        }
        $table->data[] = array(
        fullname($user, true),
        $timestart,
        $timeend . '<input type="hidden" name="userid['.$m[1].']" value="'.$m[1].'" />'
        );
    }
}
print_table($table);
echo '<div style="width:100%;text-align:center;"><strong>';
echo get_string('extendperiod') . ' ';
choose_from_menu($periodmenu, "extendperiod");
echo ' ' . get_string('startingfrom') . ' ';
choose_from_menu($basemenu, "extendbase", "2", "");
echo '</strong><br />';
echo '<input type="submit" value="'.get_string('savechanges').'" />';
echo '</div></form>';

print_footer($course);
?>
