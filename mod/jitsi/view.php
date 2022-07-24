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
 * Prints a particular instance of jitsi
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

global $USER;

$id = optional_param('id', 0, PARAM_INT);
$n  = optional_param('n', 0, PARAM_INT);
$state = optional_param('state', null, PARAM_TEXT);
$deletejitsirecordid = optional_param('deletejitsirecordid', 0, PARAM_INT);
$hidejitsirecordid = optional_param('hidejitsirecordid', 0, PARAM_INT);
$showjitsirecordid = optional_param('showjitsirecordid', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('jitsi', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $jitsi = $DB->get_record('jitsi', array('id' => $cm->instance), '*', MUST_EXIST);
    $sesskey = optional_param('sesskey', null, PARAM_TEXT);
} else if ($n) {
    $jitsi  = $DB->get_record('jitsi', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $jitsi->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('jitsi', $jitsi->id, $course->id, false, MUST_EXIST);
} else if ($state) {
    $paramdecode = base64urldecode($state);
    $parametrosarray = explode("&", $paramdecode);
    $idarray = $parametrosarray[0];
    $deletejitsirecordidarray = $parametrosarray[1];
    $hidejitsirecordidarray = $parametrosarray[2];
    $showjitsirecordidarray = $parametrosarray[3];
    $sesskeyarray = $parametrosarray[4];
    $statesesarray = $parametrosarray[5];
    $ida = explode("=", $idarray);
    $deletejitsirecordida = explode("=", $deletejitsirecordidarray);
    $hidejitsirecordida = explode("=", $hidejitsirecordidarray);
    $showjitsirecordida = explode("=", $showjitsirecordidarray);
    $sesskeya = explode("=", $sesskeyarray);
    $statesesa = explode("=", $statesesarray);
    $id = $ida[1];
    $deletejitsirecordid = $deletejitsirecordida[1];
    $hidejitsirecordid = $hidejitsirecordida[1];
    $showjitsirecordid = $showjitsirecordida[1];
    $sesskey = $sesskeya[1];
    $stateses = $statesesa[1];
    $cm = get_coursemodule_from_id('jitsi', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $jitsi = $DB->get_record('jitsi', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    throw new \moodle_exception('Unable to find jitsi');
}

require_login($course, true, $cm);
$event = \mod_jitsi\event\course_module_viewed::create(array(
  'objectid' => $PAGE->cm->instance,
  'context' => $PAGE->context,
));

$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $jitsi);
$event->trigger();
$PAGE->set_url('/mod/jitsi/view.php', array('id' => $cm->id));

$PAGE->set_title(format_string($jitsi->name));
$PAGE->set_heading(format_string($course->fullname));

if ($deletejitsirecordid && confirm_sesskey($sesskey)) {
    marktodelete($deletejitsirecordid, 1);
    redirect($PAGE->url, get_string('deleted'));
}

if ($hidejitsirecordid && confirm_sesskey($sesskey)) {
    $record = $DB->get_record('jitsi_record', array('id' => $hidejitsirecordid));
    $record->visible = 0;
    $DB->update_record('jitsi_record', $record);
    redirect($PAGE->url, get_string('updated', 'jitsi'));
}

if ($showjitsirecordid && confirm_sesskey($sesskey)) {
    $record = $DB->get_record('jitsi_record', array('id' => $showjitsirecordid));
    $record->visible = 1;
    $DB->update_record('jitsi_record', $record);
    redirect($PAGE->url, get_string('updated', 'jitsi'));
}

$context = context_module::instance($cm->id);

$cm = get_coursemodule_from_id('jitsi', $id);
$cminfo = \cm_info::create($cm);

if (!has_capability('mod/jitsi:view', $context)) {
    notice(get_string('noviewpermission', 'jitsi'));
}
$courseid = $course->id;
$context = context_course::instance($courseid);
$roles = get_user_roles($context, $USER->id);

$rolestr[] = null;
foreach ($roles as $role) {
    $rolestr[] = $role->shortname;
}

$moderation = false;
if (has_capability('mod/jitsi:moderation', $context)) {
    $moderation = true;
}

$nom = null;
switch ($CFG->jitsi_id) {
    case 'username':
        $nom = $USER->username;
        break;
    case 'nameandsurname':
        $nom = $USER->firstname.' '.$USER->lastname;
        break;
    case 'alias':
        break;
}
$sessionoptionsparam = ['$course->shortname', '$jitsi->id', '$jitsi->name'];
$fieldssessionname = $CFG->jitsi_sesionname;

$allowed = explode(',', $fieldssessionname);
$max = count($allowed);

$sesparam = '';
$optionsseparator = ['.', '-', '_', ''];
for ($i = 0; $i < $max; $i++) {
    if ($i != $max - 1) {
        if ($allowed[$i] == 0) {
            $sesparam .= string_sanitize($course->shortname).$optionsseparator[$CFG->jitsi_separator];
        } else if ($allowed[$i] == 1) {
            $sesparam .= $jitsi->id.$optionsseparator[$CFG->jitsi_separator];
        } else if ($allowed[$i] == 2) {
            $sesparam .= string_sanitize($jitsi->name).$optionsseparator[$CFG->jitsi_separator];
        }
    } else {
        if ($allowed[$i] == 0) {
            $sesparam .= string_sanitize($course->shortname);
        } else if ($allowed[$i] == 1) {
            $sesparam .= $jitsi->id;
        } else if ($allowed[$i] == 2) {
            $sesparam .= string_sanitize($jitsi->name);
        }
    }
}

$avatar = $CFG->wwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
$urlparams = array('avatar' => $avatar, 'nom' => $nom, 'ses' => $sesparam,
    'courseid' => $course->id, 'cmid' => $id, 't' => $moderation);

$today = getdate();

if (!$deletejitsirecordid) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($jitsi->name);
}

$cm = get_coursemodule_from_id('jitsi', $id, 0, false, MUST_EXIST);
update_completition($cm);
if ($CFG->branch >= 311) {
    $completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
    $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
    echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);
}

$contextmodule = context_module::instance($cm->id);
$logs = $DB->get_records_select('logstore_standard_log',
    'contextid = ? and timecreated between ? and ? and action = ?',
    array($contextmodule->id, $today[0] - 60, $today[0], 'participating'));

echo " ";
echo "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\"
     class=\"bi bi-person-workspace\" viewBox=\"0 0 16 16\">";
echo "<path d=\"M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H4Zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z\"/>";
echo "<path d=\"M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.373 5.373 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1
     1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2H2Z\"/>";
echo "</svg>";
echo (" ".count($logs)." ".get_string('connectedattendeesnow', 'jitsi'));
echo "<p></p>";
echo get_string('minutesconnected', 'jitsi', getminutes($id, $USER->id));

if ($jitsi->intro) {
    echo $OUTPUT->box(format_module_intro('jitsi', $jitsi, $cm->id), 'generalbox mod_introbox', 'jitsiintro');
}
if ($today[0] < $jitsi->timeclose || $jitsi->timeclose == 0) {
    if ($today[0] > (($jitsi->timeopen)) ||
        has_capability('mod/jitsi:moderation', $context) && $today[0] > (($jitsi->timeopen) - ($jitsi->minpretime * 60))) {
        echo $OUTPUT->box(get_string('instruction', 'jitsi'));
        echo $OUTPUT->single_button(new moodle_url('/mod/jitsi/session.php', $urlparams), get_string('access', 'jitsi'), 'post');
    } else {
        echo $OUTPUT->box(get_string('nostart', 'jitsi', userdate($jitsi->timeopen)));
    }
} else {
    echo $OUTPUT->box(get_string('finish', 'jitsi'));
}
if ($CFG->jitsi_invitebuttons == 1 && has_capability('mod/jitsi:createlink', $PAGE->context) && $jitsi->validitytime != 0) {
    echo " ";
    echo "<button class=\"btn btn-secondary\" type=\"button\" ";
    echo "     data-toggle=\"collapse\" data-target=\"#collapseInvitaciones\"";
    echo "     aria-expanded=\"false\" aria-controls=\"collapseExample\">";
    echo get_string('invitations', 'jitsi');
    echo "</button>";
}

$sql = 'select * from {jitsi_record} where jitsi = '.$jitsi->id.' and deleted = 0 order by id desc';
$records = $DB->get_records_sql($sql);

$sqlusersconnected = 'select distinct userid from {logstore_standard_log} where contextid = '
    .$contextmodule->id.' and action = \'participating\'';

$usersconnected = $DB->get_records_sql($sqlusersconnected);

if ($records && isallvisible($records) || $records && has_capability ('mod/jitsi:record', $PAGE->context)) {
    echo " ";
    echo "<button class=\"btn btn-secondary\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapseExample\"
         aria-expanded=\"false\" aria-controls=\"collapseExample\">";
    echo get_string('records', 'jitsi');
    echo "</button>";
}

if ($usersconnected && has_capability('mod/jitsi:viewusersonsession', $PAGE->context)) {
    echo " ";
    echo "<button class=\"btn btn-secondary\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapseAsistentes\"
    aria-expanded=\"false\" aria-controls=\"collapseExample\">";
    echo get_string('attendeesreport', 'jitsi');
    echo "</button>";
    echo "<div class=\"collapse\" id=\"collapseAsistentes\">";
    echo "<div class=\"card card-body\">";
    $table = new html_table();
    $table->head = array(get_string('name'), get_string('minutes'));
    $table->data = array();
    foreach ($usersconnected as $userconnected) {
        if ($userconnected->userid != 0) {
            $user = $DB->get_record('user', array('id' => $userconnected->userid));
            $table->data[] = array(fullname($user), getminutes($id, $user->id));
        }
    }
    echo html_writer::table($table);
    echo "</div>";
    echo "</div>";
}

if ($CFG->jitsi_invitebuttons == 1 && has_capability('mod/jitsi:createlink', $PAGE->context) && $jitsi->validitytime != 0) {
    echo "<div class=\"collapse\" id=\"collapseInvitaciones\">";
    echo "<div class=\"card card-body\">";
    $urlinvitacion = $CFG->wwwroot.'/mod/jitsi/formuniversal.php?t='.$jitsi->token;
    echo "<div class=\"container\">";
    echo "<div class=\"row\">";
    echo "<div class=\"col-11\">";
    echo get_string('sharetoinvite', 'jitsi');
    echo "</div>";
    echo "</div>";
    echo "<div class=\"row\">";
    echo "<div class=\"col-11\">";

    echo "<input class=\"form-control\" type=\"text\" placeholder=\"".$urlinvitacion."\" ";
    echo "        aria-label=\"Disabled input example\" disabled>";
    echo "</div>";
    echo "<div class=\"col-1\">";
    echo "<button onclick=\"copyurl()\" type=\"button\" class=\"btn btn-secondary\" id=\"copyurl\">";
    echo "Copy";
    echo "</button>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
    echo "</div>";

    echo "<p></p>";
    echo "<script>";
    echo "function copyurl() {\n";
        echo "var time = ".generatecode($jitsi).";\n";
        echo "var copyText = \"".$urlinvitacion."\";\n";
        echo "navigator.clipboard.writeText(copyText);\n";
        echo "alert(\"".get_string('copied', 'jitsi')."\");\n";
        echo "}\n";
    echo "</script>";
}

if ($records) {
    echo "<div class=\"collapse\" id=\"collapseExample\">";
    echo "<div class=\"card card-body\">";

    echo "<div class=\"row\">";
    foreach ($records as $record) {
        // Para borrar grabaciones.
        $deleteurl = new moodle_url('/mod/jitsi/view.php?id='.$cm->id.'&deletejitsirecordid=' .
                 $record->id . '&sesskey=' . sesskey());
        $deleteicon = new pix_icon('t/delete', get_string('delete'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, $deleteicon,
            new confirm_action(get_string('confirmdeleterecordinactivity', 'jitsi')));

        $hideurl = new moodle_url('/mod/jitsi/view.php?id='.$cm->id.'&hidejitsirecordid=' .
                 $record->id . '&sesskey=' . sesskey());
        $showurl = new moodle_url('/mod/jitsi/view.php?id='.$cm->id.'&showjitsirecordid=' .
                 $record->id . '&sesskey=' . sesskey());
        $hideicon = new pix_icon('t/hide', get_string('hide'));
        $showicon = new pix_icon('t/show', get_string('show'));
        $hideaction = $OUTPUT->action_icon($hideurl, $hideicon, new confirm_action('Hide?'));
        $showaction = $OUTPUT->action_icon($showurl, $showicon, new confirm_action('Show?'));

        $sourcerecord = $DB->get_record('jitsi_source_record', array('id' => $record->source));
        if ($record->visible != 0 || (has_capability('mod/jitsi:record', $context) && has_capability('mod/jitsi:hide', $context))) {

            echo "<div class=\"col-sm-6\">";
            echo "<div class=\"card\" >";
            echo "<div class=\"card-body\">";
            if ($record->visible == 0) {
                echo "<h5 class=\"card-title text-muted\">";
            } else {
                echo "<h5 class=\"card-title\">";
            }
            if (has_capability('mod/jitsi:record', $context) && has_capability('mod/jitsi:hide', $context)) {
                $tmpl = new \core\output\inplace_editable('mod_jitsi', 'recordname', $record->id,
                    has_capability('mod/jitsi:record', $context),
                    format_string($record->name), $record->name, get_string('editrecordname', 'jitsi'),
                    get_string('newvaluefor', 'jitsi') . format_string($record->name));
                echo $OUTPUT->render($tmpl);
            } else {
                echo $record->name;
            }
            echo "</h5>";
            if ($sourcerecord) {
                echo "<h6 class=\"card-subtitle mb-2 text-muted\">".userdate($sourcerecord->timecreated)."</h6>";
            } else {
                echo "<h6 class=\"card-subtitle mb-2 text-muted\">".get_string('error')."</h6>";

            }
            $account = $DB->get_record('jitsi_record_account', array('id' => $sourcerecord->account));
            echo "<div class=\"embed-responsive embed-responsive-16by9\">";
            if ($sourcerecord) {
                if ($account->clientaccesstoken != null && $sourcerecord->timecreated != 0) {
                    doembedable($sourcerecord->link);
                }
                echo "<iframe class=\"embed-responsive-item\" src=\"https://youtube.com/embed/".$sourcerecord->link."\"
                    allowfullscreen></iframe>";
            } else {
                echo "<iframe class=\"embed-responsive-item\" src=\"https://youtube.com/embed/\"
                    allowfullscreen></iframe>";
            }

            echo "</div>";
            echo "<div class=\"row\">";
            echo "<div class=\"col-sm\">";
            echo "</div>";
            echo "  <div class=\"col-sm\">";

            if (has_capability('mod/jitsi:record', $context)) {
                echo "<span class=\"align-middle text-right\"><p>".$deleteaction."</span>";
            }
            if (has_capability('mod/jitsi:hide', $context)) {
                if ($record->visible != 0) {
                    echo "<span class=\"align-middle text-right\">".$hideaction."</p></span>";
                } else {
                    echo "<span class=\"align-middle text-right\">".$showaction."</p></span>";
                }
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }
    echo "</div>";

    echo "</div>";
    echo "</div>";
}

echo $CFG->jitsi_help;
echo "<hr>";
echo $OUTPUT->footer();
