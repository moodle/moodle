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
 * Transferring educard pages settings to mustache
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Front pages page 1.
 *
 */
function theme_educard_frontpagepage01() {
    $theme = theme_config::load('educard');
    $templatecontext['page01enabled'] = $theme->settings->page01enabled;
    $templatecontext['block20enabled'] = $theme->settings->block20enabled;
    if ($theme->settings->page01enabled == true) {
        $templatecontext['page01desing'] = $theme->settings->page01desing;
        $templatecontext['page01desing-'.$theme->settings->page01desing.''] = true;
        $image = $theme->setting_file_url("imgpage01", "imgpage01");
        if (empty($image)) {
            if (!empty($theme->settings->frontpageimglink)) {
                $image = $theme->settings->frontpageimglink."page01/d".$theme->settings->page01desing."/1.jpg";
            } else {
                $image = null;
            }
        }
        $image1 = $theme->setting_file_url("imgpage02", "imgpage02");
        if (empty($image1)) {
            if (!empty($theme->settings->frontpageimglink)) {
                $image1 = $theme->settings->frontpageimglink."page01/d".$theme->settings->page01desing."/2.jpg";
            } else {
                $image1 = null;
            }
        }
        $templatecontext['imgpage01'] = $image;
        $templatecontext['imgpage02'] = $image1;
        $templatecontext['page01header'] = format_string($theme->settings->page01header);
        $templatecontext['page01caption'] = format_string($theme->settings->page01caption);
        $templatecontext['page01address'] = format_string($theme->settings->page01address);
        $templatecontext['page01phone'] = $theme->settings->page01phone;
        $templatecontext['page01email'] = $theme->settings->page01email;
        $templatecontext['page01opening'] = $theme->settings->page01opening;

        $exp = explode('|', format_string($theme->settings->page01opening));
        if (isset($exp[0]) && isset($exp[1]) && isset($exp[2]) && isset($exp[3])) {
            $templatecontext['page01opening1'] = $exp[0];
            $templatecontext['page01opening2'] = $exp[1];
            $templatecontext['page01opening3'] = $exp[2];
            $templatecontext['page01opening4'] = $exp[3];
        }
        $expset = explode(",", format_string($theme->settings->page01geolocation));
        $say = 0;
        foreach ($expset as $x => $val) {
            if (isset($val)) {
                $say++;
                $templatecontext['page01geolocation_'.$say] = floatval($val);
            }
        }
    } else {
        $templatecontext['block20enabled'] = false;
    }
    return $templatecontext;
}
/**
 * Front pages page 2.
 *
 */
function theme_educard_frontpagepage02() {
    GLOBAL $DB, $OUTPUT, $PAGE, $CFG;
    $theme = theme_config::load('educard');
    $templatecontext['page02enabled'] = $theme->settings->page02enabled;
    if ($theme->settings->page02enabled == true) {
        $templatecontext['page02desing'] = $theme->settings->page02desing;
        $templatecontext['page02explanation'] = format_text($theme->settings->page02explanation);
        $templatecontext['page02desing-'.$theme->settings->page02desing.''] = true;
        if (!empty($theme->settings->page02count)) {
            $count = $theme->settings->page02count;
        } else {
            $count = 8;
        }
        $teacherrole = $theme->settings->page02showrole;
        $userids = $theme->settings->page02id;
        $image = $theme->setting_file_url("imgpage02img", "imgpage02img");
        if (empty($image)) {
            if (!empty($theme->settings->frontpageimglink)) {
                $image = $theme->settings->frontpageimglink."page02/d".$theme->settings->page02desing."/1.jpg";
            } else {
                $image = null;
            }
        }
        $templatecontext['imgpage02img'] = $image;
        // SQL Server.
        if ($CFG->dbtype === 'sqlsrv') {
            $sql = "SELECT TOP ". $count ." ra.userid, ra.roleid";
        } else {
            $sql = "SELECT ra.userid, ra.roleid";
        }
        $sql = $sql." FROM {role_assignments} ra";
        $sql = $sql." JOIN {context} ctx on ra.contextid = ctx.id";
        if ($userids) {
            $sql = $sql." WHERE ra.roleid = :roleid and ra.userid IN (".$userids.")";
        } else {
            $sql = $sql." WHERE ra.roleid = :roleid";
        }

        $sql = $sql." GROUP by ra.userid, ra.roleid";
        if ($CFG->dbtype != 'sqlsrv') {
            $sql = $sql." LIMIT ". $count;
        }
        // And ctx.contextlevel = '50'?
        if (!empty($theme->settings->page02total)) {
            $courses = get_courses('all', 'c.timemodified DESC');
        }
        $roleassignments = $DB->get_records_sql($sql, ['roleid' => $teacherrole]);
        if (!empty($roleassignments)) {
            $j = 0;
            $coursecount = 0;
            $studentscount = 0;
            foreach ($roleassignments as $roleassignment) {
                $templatecontext['page02'][$j]['showdescription'] = $theme->settings->page02description;
                $roleassignment->imagealt = "Teacher";
                if ($user = $DB->get_record('user', ['id' => $roleassignment->userid])) {
                    $personcontext = context_user::instance($user->id);
                    $description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php',
                    $personcontext->id, 'user', 'profile', false);
                    $templatecontext['page02'][$j]['teachername'] = format_string($user->firstname." ".$user->lastname);
                    $templatecontext['page02'][$j]['description'] = format_text($description);
                    $templatecontext['page02'][$j]['userpicture'] =
                        $OUTPUT->user_picture($user, ['class' => '']);
                    $templatecontext['page02'][$j]['userURL'] =
                        new moodle_url('/user/profile.php', ['id' => $roleassignment->userid]);
                    $userpicture = new user_picture($user);
                    $userpicture->size = 512;
                    $url = $userpicture->get_url($PAGE)->out(false);
                    $templatecontext['page02'][$j]['userpictureURL'] = $url;
                }
                $templatecontext['page02total'] = $theme->settings->page02total;
                if (!empty($templatecontext['page02total'])) {
                    foreach ($courses as $id => $course) {
                        $context = context_course::instance($id);
                        $teachers = get_role_users($teacherrole, $context);
                        foreach ($teachers as $id => $teacher) {
                            if ($teacher->username == $user->username) {
                                $coursecount++;
                                $role = $DB->get_field('role', 'id', ['shortname' => 'student']);
                                $students = get_role_users($role, $context);
                                $studentscount = $studentscount + count($students);
                            }
                        }
                    }
                }
                $templatecontext['page02'][$j]['coursecount'] = $coursecount;
                $templatecontext['page02'][$j]['studentscount'] = $studentscount;
                $coursecount = 0;
                $studentscount = 0;

                $sql = "SELECT  usdata.*, usfield.shortname";
                $sql = $sql." FROM {user_info_data} usdata";
                $sql = $sql." JOIN {user_info_field} usfield ON usdata.fieldid = usfield.id";
                $sql = $sql." WHERE usdata.userid = ". $roleassignment->userid;
                $otherareas = $DB->get_records_sql($sql);
                if (!empty($otherareas)) {
                    $k = 1;
                    foreach ($otherareas as $otherarea) {
                        if ($otherarea->shortname == "userjob" ) {
                            $templatecontext['page02'][$j]['job'] = $otherarea->data;
                        } else if ( $otherarea->shortname == "usermail" ) {
                            $templatecontext['page02'][$j]['usermail'] = $otherarea->data;
                        } else if ( substr($otherarea->shortname, 0, 10) == "usersocial" ) {
                            if (!empty($otherarea->data)) {
                                $exp = explode(',', $otherarea->data);
                                $templatecontext['page02'][$j][$otherarea->shortname."link"] = empty($exp[1]) ? null : $exp[1];
                                $socialmedia = "";
                                if (get_string_manager()->string_exists($exp[0], 'theme_educard')) {
                                    $socialmedia = get_string($exp[0], 'theme_educard');
                                }
                                if (substr($socialmedia, 0, 1) != "[" ) {
                                    $templatecontext['page02'][$j][$otherarea->shortname] = $socialmedia;
                                } else {
                                    $templatecontext['page02'][$j][$otherarea->shortname] = "fa fa-question";
                                }
                            }
                        }
                    }
                }
                $j = $j + 1;
                if ($j == $theme->settings->page02count) {
                    break;
                }
            }
        }
    }
    return $templatecontext;
}
