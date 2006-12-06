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

if ((count($users) > 0) and ($form = data_submitted()) and confirm_sesskey()) {
    if (count($form->userid) != count($form->extendperiod)) {
        error('Parameters malformation', $CFG->wwwroot.'/user/index.php?id='.$id);
    }

    foreach ($form->userid as $k => $v) {
        // find all roles this student have in this course  
        if ($students = get_records_sql("SELECT ra.timestart, ra.timeend 
                                       FROM {$CFG->prefix}role_assignments ra
                                       WHERE userid = $v
                                       AND contextid = $context->id")) {
            // enrol these users again, with time extension
            // not that this is not necessarily a student role
            foreach ($students as $student) {
                enrol_student($v, $id, $student->timestart, $student->timeend + $form->extendperiod[$k]);
            }
        }
    }

    redirect("$CFG->wwwroot/user/index.php?id=$id", get_string('changessaved'));
}

/// Print headers

if ($course->category) {
    print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname,
    "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> -> ".
    get_string('extendenrol'), "", "", true, "&nbsp;", navmenu($course));
} else {
    print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname,
    get_string('extendenrol'), "", "", true, "&nbsp;", navmenu($course));
}

for ($i=1; $i<=365; $i++) {
    $seconds = $i * 86400;
    $periodmenu[$seconds] = get_string('numdays', '', $i);
}

print_heading(get_string('extendenrol'));
echo "<form method=\"post\" action=\"extendenrol.php\" name=\"form\">\n";
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
$table->head  = array (get_string('fullname'), get_string('enrolmentstart'), get_string('enrolmentend'), get_string('extendperiod'));
$table->align = array ('left', 'center', 'center', 'center');
$table->width = "600";
$timeformat = get_string('strftimedate');
$nochange = get_string('nochange');
$notavailable = get_string('notavailable');
$unlimited = get_string('unlimited');
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
            $checkbox = choose_from_menu($periodmenu, "extendperiod[{$m[1]}]", "0", $nochange, '', '0', true);
        } else {
            $timeend = $unlimited;
            $checkbox = '<input type="hidden" name="extendperiod['.$m[1].']" value="0" />'.$nochange;
        }
        $table->data[] = array(
        fullname($user, true),
        $timestart,
        $timeend,
        '<input type="hidden" name="userid['.$m[1].']" value="'.$m[1].'" />'.$checkbox
        );
    }
}
print_table($table);
echo "\n<div style=\"width:100%;text-align:center;\"><input type=\"submit\" value=\"".get_string('savechanges')."\" /></div>\n</form>\n";

print_footer($course);
?>