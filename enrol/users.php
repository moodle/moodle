<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main course enrolment management UI, this is not compatible with frontpage course.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/enrol/users_forms.php");
require_once("$CFG->dirroot/group/lib.php");

$id      = required_param('id', PARAM_INT); // course id
$action  = optional_param('action', '', PARAM_ACTION);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$ifilter = optional_param('ifilter', 0, PARAM_INT); // only one instance
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$sort    = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir     = optional_param('dir', 'ASC', PARAM_ALPHA);


$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/course:enrolreview', $context);

if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

$managegroups = has_capability('moodle/course:managegroups', $context);
$instances = enrol_get_instances($course->id, true);
$plugins   = enrol_get_plugins(true);
$inames    = array();
foreach ($instances as $k=>$i) {
    if (!isset($plugins[$i->enrol])) {
        // weird, some broken stuff in plugin
        unset($instances[$k]);
        continue;
    }
    $inames[$k] = $plugins[$i->enrol]->get_instance_name($i);
}

// validate paging params
if ($ifilter != 0 and !isset($instances[$ifilter])) {
    $ifilter = 0;
}
if ($perpage < 3) {
    $perpage = 3;
}
if ($page < 0) {
    $page = 0;
}
if (!in_array($dir, array('ASC', 'DESC'))) {
    $dir = 'ASC';
}
if (!in_array($sort, array('firstname', 'lastname', 'email', 'lastseen'))) {
    $dir = 'lastname';
}

$PAGE->set_url('/enrol/users.php', array('id'=>$course->id, 'page'=>$page, 'sort'=>$sort, 'dir'=>$dir, 'perpage'=>$perpage, 'ifilter'=>$ifilter));
$PAGE->set_pagelayout('admin');

//lalala- nav hack
navigation_node::override_active_url(new moodle_url('/enrol/users.php', array('id'=>$course->id)));


$allroles   = get_all_roles();
$allroles   = role_fix_names($allroles, $context);
$assignable = get_assignable_roles($context, ROLENAME_ALIAS, false); // verifies unassign access control too
$allgroups  = groups_get_all_groups($course->id);
foreach ($allgroups as $gid=>$group) {
    $allgroups[$gid]->name = format_string($group->name);
}

if ($action) {
    switch ($action) {
        case 'unenrol':
            $ue = required_param('ue', PARAM_INT);
            if (!$ue = $DB->get_record('user_enrolments', array('id'=>$ue))) {
                break;
            }
            $user = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
            if (!isset($instances[$ue->enrolid])) {
                break;
            }
            $instance = $instances[$ue->enrolid];
            $plugin = $plugins[$instance->enrol];
            if (!$plugin->allow_unenrol($instance) or !has_capability("enrol/$instance->enrol:unenrol", $context)) {
                break;
            }

            if ($confirm and confirm_sesskey()) {
                $plugin->unenrol_user($instance, $ue->userid);
                redirect($PAGE->url);

            } else {
                $yesurl = new moodle_url($PAGE->url, array('action'=>'unenrol', 'ue'=>$ue->id, 'confirm'=>1, 'sesskey'=>sesskey()));
                $message = get_string('unenrolconfirm', 'enrol', array('user'=>fullname($user, true), 'course'=>format_string($course->fullname)));
                $PAGE->set_title(get_string('unenrol', 'enrol'));
                echo $OUTPUT->header();
                echo $OUTPUT->heading(get_string('unenrol', 'enrol'));
                echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                echo $OUTPUT->footer();
                die;
            }
            break;

        case 'unassign':
            $role = required_param('role', PARAM_INT);
            $user = required_param('user', PARAM_INT);
            if (!isset($assignable[$role])) {
                break;
            }
            $role = $allroles[$role];
            $user = $DB->get_record('user', array('id'=>$user), '*', MUST_EXIST);

            if ($confirm and confirm_sesskey()) {
                role_unassign($role->id, $user->id, $context->id, '', NULL);
                redirect($PAGE->url);

            } else {
                $yesurl = new moodle_url($PAGE->url, array('action'=>'unassign', 'role'=>$role->id, 'user'=>$user->id, 'confirm'=>1, 'sesskey'=>sesskey()));
                $message = get_string('unassignconfirm', 'role', array('user'=>fullname($user, true), 'role'=>$role->localname));
                $PAGE->set_title(get_string('unassignarole', 'role', $role->localname));
                echo $OUTPUT->header();
                echo $OUTPUT->heading(get_string('unassignarole', 'role', $role->localname));
                echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                echo $OUTPUT->footer();
                die;
            }
            break;

        case 'assign':
            $user = required_param('user', PARAM_INT);
            $user = $DB->get_record('user', array('id'=>$user), '*', MUST_EXIST);

            if (!is_enrolled($context, $user)) {
                break; // no roles without enrolments here in this script
            }

            $mform = new enrol_users_assign_form(NULL, array('user'=>$user, 'course'=>$course, 'assignable'=>$assignable));

            if ($mform->is_cancelled()) {
                redirect($PAGE->url);

            } else if ($data = $mform->get_data()) {
                if ($data->roleid) {
                    role_assign($data->roleid, $user->id, $context->id, '', NULL);
                }
                redirect($PAGE->url);
            }

            $PAGE->set_title(get_string('assignroles', 'role'));
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('assignroles', 'role'));
            $mform->display();
            echo $OUTPUT->footer();
            die;

        case 'removemember':
            $group = required_param('group', PARAM_INT);
            $user  = required_param('user', PARAM_INT);
            if (!$managegroups) {
                break;
            }
            if (!isset($allgroups[$group])) {
                break;
            }
            $group = $allgroups[$group];
            $user  = $DB->get_record('user', array('id'=>$user), '*', MUST_EXIST);

            if ($confirm and confirm_sesskey()) {
                groups_remove_member($group, $user);
                redirect($PAGE->url);

            } else {
                $yesurl = new moodle_url($PAGE->url, array('action'=>'removemember', 'group'=>$group->id, 'user'=>$user->id, 'confirm'=>1, 'sesskey'=>sesskey()));
                $message = get_string('removefromgroupconfirm', 'group', array('user'=>fullname($user, true), 'group'=>$group->name));
                $PAGE->set_title(get_string('removefromgroup', 'group', $group->name));
                echo $OUTPUT->header();
                echo $OUTPUT->heading(get_string('removefromgroup', 'group', $group->name));
                echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                echo $OUTPUT->footer();
                die;
            }
            break;

        case 'addmember':
            $user = required_param('user', PARAM_INT);
            $user = $DB->get_record('user', array('id'=>$user), '*', MUST_EXIST);

            if (!$managegroups) {
                break;
            }
            if (!is_enrolled($context, $user)) {
                break; // no roles without enrolments here in this script
            }

            $mform = new enrol_users_addmember_form(NULL, array('user'=>$user, 'course'=>$course, 'allgroups'=>$allgroups));

            if ($mform->is_cancelled()) {
                redirect($PAGE->url);

            } else if ($data = $mform->get_data()) {
                if ($data->groupid) {
                    groups_add_member($data->groupid, $user->id);
                }
                redirect($PAGE->url);
            }

            $PAGE->set_title(get_string('addgroup', 'group'));
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('addgroup', 'group'));
            $mform->display();
            echo $OUTPUT->footer();
            die;

        case 'edit':
            $ue = required_param('ue', PARAM_INT);
            if (!$ue = $DB->get_record('user_enrolments', array('id'=>$ue))) {
                break;
            }
            $user = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
            if (!isset($instances[$ue->enrolid])) {
                break;
            }
            $instance = $instances[$ue->enrolid];
            $plugin = $plugins[$instance->enrol];
            if (!$plugin->allow_unenrol($instance) or !has_capability("enrol/$instance->enrol:unenrol", $context)) {
                break;
            }

            $mform = new enrol_users_edit_form(NULL, array('user'=>$user, 'course'=>$course, 'ue'=>$ue));

            if ($mform->is_cancelled()) {
                redirect($PAGE->url);

            } else if ($data = $mform->get_data()) {
                if (!isset($data->status)) {
                    $status = $ue->status;
                }
                $plugin->update_user_enrol($instance, $ue->userid, $data->status, $data->timestart, $data->timeend);
                redirect($PAGE->url);
            }

            $PAGE->set_title(fullname($user));
            echo $OUTPUT->header();
            echo $OUTPUT->heading(fullname($user));
            $mform->display();
            echo $OUTPUT->footer();
            die;
    }
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('enrolledusers', 'enrol'));
$PAGE->set_title(get_string('enrolledusers', 'enrol'));

notify('NOTICE TO TESTERS: This interface will shortly be replaced with a new one.');   // TODO FIXME REMOVE THIS

if ($ifilter) {
    $instancessql = " = :ifilter";
    $params = array('ifilter'=>$ifilter);
} else {
    if ($instances) {
        list($instancessql, $params) = $DB->get_in_or_equal(array_keys($instances), SQL_PARAMS_NAMED);
    } else {
        // no enabled instances, oops, we should probably say something
        $instancessql = "= :never";
        $params = array('never'=>-1);
    }
}

$sqltotal = "SELECT COUNT(DISTINCT u.id)
               FROM {user} u
               JOIN {user_enrolments} ue ON (ue.userid = u.id  AND ue.enrolid $instancessql)
               JOIN {enrol} e ON (e.id = ue.enrolid)";
$totalusers = $DB->count_records_sql($sqltotal, $params);

$ufields = user_picture::fields('u');
$sql = "SELECT DISTINCT $ufields, ul.timeaccess AS lastseen
          FROM {user} u
          JOIN {user_enrolments} ue ON (ue.userid = u.id  AND ue.enrolid $instancessql)
          JOIN {enrol} e ON (e.id = ue.enrolid)
     LEFT JOIN {user_lastaccess} ul ON (ul.courseid = e.courseid AND ul.userid = u.id)";
if ($sort === 'firstname') {
    $sql .= " ORDER BY u.firstname $dir, u.lastname $dir";
} else if ($sort === 'lastname') {
    $sql .= " ORDER BY u.lastname $dir, u.firstname $dir";
} else if ($sort === 'email') {
    $sql .= " ORDER BY u.email $dir, u.lastname $dir, u.firstname $dir";
} else if ($sort === 'lastseen') {
    $sql .= " ORDER BY ul.timeaccess $dir, u.lastname $dir, u.firstname $dir";
}

$pagingbar = new paging_bar($totalusers, $page, $perpage, $PAGE->url, 'page');

$users = $DB->get_records_sql($sql, $params, $page*$perpage, $perpage);

$strfirstname = get_string('firstname');
$strlastname  = get_string('lastname');
$stremail     = get_string('email');
$strlastseen  = get_string('lastaccess');

if ($dir === 'ASC') {
    $diricon = html_writer::empty_tag('img', array('alt'=>'', 'src'=>$OUTPUT->pix_url('t/down')));
    $newdir = 'DESC';
} else {
    $diricon  = html_writer::empty_tag('img', array('alt'=>'', 'src'=>$OUTPUT->pix_url('t/up')));
    $newdir = 'ASC';
}

$table = new html_table();
$table->head = array();
if ($sort === 'firstname') {
    $h = html_writer::link(new moodle_url($PAGE->url, array('dir'=>$newdir)), $strfirstname);
    $h .= " $diricon / ";
    $h .= html_writer::link(new moodle_url($PAGE->url, array('sort'=>'lastname')), $strlastname);
} else if ($sort === 'lastname') {
    $newdir = ($dir === 'ASC') ? 'DESC' : 'ASC';
    $h = html_writer::link(new moodle_url($PAGE->url, array('sort'=>'firstname')), $strfirstname);
    $h .= " / ";
    $h .= html_writer::link(new moodle_url($PAGE->url, array('dir'=>$newdir)), $strlastname);
    $h .= " $diricon";
} else {
    $h = html_writer::link(new moodle_url($PAGE->url, array('sort'=>'firstname')), $strfirstname);
    $h .= " / ";
    $h .= html_writer::link(new moodle_url($PAGE->url, array('sort'=>'lastname')), $strlastname);
}
$table->head[] = $h;
if ($sort === 'email') {
    $h = html_writer::link(new moodle_url($PAGE->url, array('dir'=>$newdir)), $stremail);
    $h .= " $diricon";
} else {
    $h = html_writer::link(new moodle_url($PAGE->url, array('sort'=>'email')), $stremail);
}
$table->head[] = $h;
if ($sort === 'lastseen') {
    $h = html_writer::link(new moodle_url($PAGE->url, array('dir'=>$newdir)), $strlastseen);
    $h .= " $diricon";
} else {
    $h = html_writer::link(new moodle_url($PAGE->url, array('sort'=>'lastseen')), $strlastseen);
}
$table->head[] = $h;
$table->head[] = get_string('roles', 'role');
$table->head[] = get_string('groups', 'group');
$table->head[] = get_string('enrolmentinstances', 'enrol');

$table->align = array('left', 'left', 'left', 'left', 'left', 'left');
$table->width = "95%";
foreach ($users as $user) {
    $picture = $OUTPUT->user_picture($user, array('courseid'=>$course->id));

    if ($user->lastseen) {
        $strlastaccess = userdate($user->lastseen);
    } else {
        $strlastaccess = get_string('never');
    }
    $fullname = fullname($user, true);

    // get list of roles
    $roles = array();
    $ras = get_user_roles($context, $user->id, true, 'c.contextlevel DESC, r.sortorder ASC');
    foreach ($ras as $ra) {
        if ($ra->contextid != $context->id) {
            if (!isset($roles[$ra->roleid])) {
                $roles[$ra->roleid] = null;
            }
            // higher ras, course always takes precedence
            continue;
        }
        if (isset($roles[$ra->roleid]) and $roles[$ra->roleid] === false) {
            continue;
        }
        $roles[$ra->roleid] = ($ra->itemid == 0 and $ra->component === '');
    }
    foreach ($roles as $rid=>$unassignable) {
        if ($unassignable and isset($assignable[$rid])) {
            $icon = html_writer::empty_tag('img', array('alt'=>get_string('unassignarole', 'role', $allroles[$rid]->localname), 'src'=>$OUTPUT->pix_url('t/delete')));
            $url = new moodle_url($PAGE->url, array('action'=>'unassign', 'role'=>$rid, 'user'=>$user->id));
            $roles[$rid] = $allroles[$rid]->localname . html_writer::link($url, $icon);
        } else {
            $roles[$rid] = $allroles[$rid]->localname;
        }
    }
    $addrole = '';
    if ($assignable) {
        foreach ($assignable as $rid=>$unused) {
            if (!isset($roles[$rid])) {
                //candidate for role assignment
                $icon = html_writer::empty_tag('img', array('alt'=>get_string('assignroles', 'role'), 'src'=>$OUTPUT->pix_url('t/add')));
                $url = new moodle_url($PAGE->url, array('action'=>'assign', 'user'=>$user->id));
                $addrole .= html_writer::link($url, $icon);
                break;
            }
        }
    }
    $roles = implode(', ', $roles);
    if ($addrole) {
        $roles = $roles . '<div>'.$addrole.'</div>';
    }

    $groups = array();
    $usergroups = groups_get_all_groups($course->id, $user->id, 0, 'g.id');
    foreach($usergroups as $gid=>$unused) {
        $group = $allgroups[$gid];
        if ($managegroups) {
            $icon = html_writer::empty_tag('img', array('alt'=>get_string('removefromgroup', 'group', $group->name), 'src'=>$OUTPUT->pix_url('t/delete')));
            $url = new moodle_url($PAGE->url, array('action'=>'removemember', 'group'=>$gid, 'user'=>$user->id));
            $groups[] = $group->name . html_writer::link($url, $icon);
        } else {
            $groups[] = $group->name;
        }
    }
    $groups = implode(', ', $groups);
    if ($managegroups and (count($usergroups) < count($allgroups))) {
        $icon = html_writer::empty_tag('img', array('alt'=>get_string('addgroup', 'group'), 'src'=>$OUTPUT->pix_url('t/add')));
        $url = new moodle_url($PAGE->url, array('action'=>'addmember', 'user'=>$user->id));
        $groups .= '<div>'.html_writer::link($url, $icon).'</div>';
    }


    // get list of enrol instances
    $now = time();
    $edits = array();
    $params['userid'] = $user->id;
    $ues = $DB->get_records_select('user_enrolments', "enrolid $instancessql AND userid = :userid", $params);
    foreach ($ues as $ue) {
        $instance = $instances[$ue->enrolid];
        $plugin   = $plugins[$instance->enrol];

        $edit = $inames[$instance->id];

        $dimmed = false;
        if ($ue->timestart and $ue->timeend) {
            $edit .= '&nbsp;('.get_string('periodstartend', 'enrol', array('start'=>userdate($ue->timestart), 'end'=>userdate($ue->timeend))).')';
            $dimmed = ($now < $ue->timestart and $now > $ue->timeend);
        } else if ($ue->timestart) {
            $edit .= '&nbsp;('.get_string('periodstart', 'enrol', userdate($ue->timestart)).')';
            $dimmed = ($now < $ue->timestart);
        } else if ($ue->timeend) {
            $edit .= '&nbsp;('.get_string('periodend', 'enrol', userdate($ue->timeend)).')';
            $dimmed = ($now > $ue->timeend);
        }

        if ($dimmed or $ue->status != ENROL_USER_ACTIVE) {
            $edit = html_writer::tag('span', $edit, array('class'=>'dimmed_text'));
        }

        if ($plugin->allow_unenrol($instance) and has_capability("enrol/$instance->enrol:unenrol", $context)) {
            $icon = html_writer::empty_tag('img', array('alt'=>get_string('unenrol', 'enrol'), 'src'=>$OUTPUT->pix_url('t/delete')));
            $url = new moodle_url($PAGE->url, array('action'=>'unenrol', 'ue'=>$ue->id));
            $edit .= html_writer::link($url, $icon);
        }

        if ($plugin->allow_manage($instance) and has_capability("enrol/$instance->enrol:manage", $context)) {
            $icon = html_writer::empty_tag('img', array('alt'=>get_string('edit'), 'src'=>$OUTPUT->pix_url('t/edit')));
            $url = new moodle_url($PAGE->url, array('action'=>'edit', 'ue'=>$ue->id));
            $edit .= html_writer::link($url, $icon);
        }

        $edits[] = $edit;
    }
    $edits = implode('<br />', $edits);

    $table->data[] = array("$picture <a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a>", $user->email, $strlastaccess, $roles, $groups, $edits);
}

$ifilters = new single_select($PAGE->url, 'ifilter', array(0=>get_string('all')) + $inames, $ifilter, array());
$ifilters->set_label(get_string('enrolmentinstances', 'enrol'));

echo $OUTPUT->render($ifilters);
echo $OUTPUT->render($pagingbar);
echo html_writer::table($table);
echo $OUTPUT->render($pagingbar);

// print enrol link or selection
$links = array();
foreach($instances as $instance) {
    $plugin = $plugins[$instance->enrol];
    if ($link = $plugin->get_manual_enrol_link($instance)) {
        $links[$instance->id] = $link;
    }
}
if (count($links) == 1) {
    $link = reset($links);
    echo $OUTPUT->single_button($link, get_string('enrolusers', 'enrol_manual'), 'get');

} else if (count($links) > 1) {
    $options = array();
    foreach ($links as $i=>$link) {
        $options[$link->out(false)] = $inames[$i];
    }
    echo $OUTPUT->url_select($options, '', array(''=>get_string('enrolusers', 'enrol_manual').'...'));
}

echo $OUTPUT->footer();


