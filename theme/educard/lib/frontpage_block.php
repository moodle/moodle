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
 * Transferring educard block settings to mustache
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Front page block 1.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock01($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block01headline'] = format_string($theme->settings->block01headline);
    $templatecontext['block01header'] = format_string($theme->settings->block01header);
    $templatecontext['block01maintitle'] = format_string($theme->settings->block01maintitle);
    $templatecontext['block01caption'] = format_string($theme->settings->block01caption);
    $templatecontext['block01button'] = format_string($theme->settings->block01button);
    $templatecontext['block01buttonlink'] = $theme->settings->block01buttonlink;
    $templatecontext['block01color'] = $theme->settings->block01color;
    $templatecontext['block01parallax'] = $theme->settings->block01parallax;
    $image = $theme->setting_file_url("imgblock01background", "imgblock01background");
    if (empty($image)) {
        if (!empty($theme->settings->frontpageimglink)) {
            $image = $theme->settings->frontpageimglink."block01/d".$dsn."1.jpg";
        } else {
            $image = null;
        }
    }
    $templatecontext['imgblock01background'] = $image;
    return $templatecontext;
}
/**
 * Front page block 2.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock02($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $count = $theme->settings->block02count;
    $exp = explode(',', $theme->settings->block02icon);
    $iconcount = count($exp);
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block02img = "sliderimageblock02img{$i}";
        $block02title = "block02title{$i}";
        $block02caption = "block02caption{$i}";
        $block02button = "block02button{$i}";
        $block02buttonlink = "block02buttonlink{$i}";
        $image = $theme->setting_file_url($block02img, $block02img);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 10));
            }
        }
        $templatecontext['block02'][$j]['block02image'] = format_string($image);
        $templatecontext['block02'][$j]['title'] = format_string($theme->settings->$block02title);
        $templatecontext['block02'][$j]['caption'] = format_text($theme->settings->$block02caption);
        $templatecontext['block02'][$j]['button'] = format_string($theme->settings->$block02button);
        $templatecontext['block02'][$j]['buttonurl'] = format_string($theme->settings->$block02buttonlink);
        $templatecontext['block02'][$j]['randomcolor'] = theme_educard_random_color();
        if ($j > $iconcount ) {
            $templatecontext['block02'][$j]['icon'] = isset($exp[0]) ? $exp[0] : null;
        } else {
            $templatecontext['block02'][$j]['icon'] = isset($exp[$j]) ? $exp[$j] : null;
        }
        $templatecontext['block02'][$j]['counter'] = $i;
    }
    if ($count == 2) {
        $templatecontext['count'] = "col-lg-6";
    } else if ($count == 3) {
        $templatecontext['count'] = "col-lg-4";
    } else {
        $templatecontext['count'] = "col-lg-6 col-xl-3";
    }
    return $templatecontext;
}
/**
 * Front page block 3.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock03($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block03headline'] = format_string($theme->settings->block03headline);
    $templatecontext['block03header'] = format_string($theme->settings->block03header);
    $templatecontext['block03maintitle'] = format_string($theme->settings->block03maintitle);
    $count = 6;
    $exp = explode(',', $theme->settings->block03icon);
    $iconcount = count($exp);
    $sayac = 1;
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block03title = "block03title{$i}";
        $block03caption = "block03caption{$i}";
        $block03link = "block03link{$i}";
        if ($j >= $iconcount ) {
            $templatecontext['block03'][$j]['icon'] = isset($exp[0]) ? $exp[0] : null;
        } else {
            $templatecontext['block03'][$j]['icon'] = isset($exp[$j]) ? $exp[$j] : null;
        }
        if (empty($theme->settings->$block03title) && empty($theme->settings->$block03caption)) {
            $templatecontext['block03'][$j]['empty_content'] = false;
            $sayac = $sayac - 1;
        } else {
            $templatecontext['block03'][$j]['title'] = format_string($theme->settings->$block03title);
            $templatecontext['block03'][$j]['caption'] = format_string($theme->settings->$block03caption);
            $templatecontext['block03'][$j]['link'] = $theme->settings->$block03link;
            $templatecontext['block03'][$j]['count'] = $i;
            $templatecontext['block03'][$j]['empty_content'] = true;
            $sayac++;
        }
    }
    if ($sayac == 6 || $sayac == 5 ) {
        $templatecontext['block03_grid'] = 4;
    } else {
        $templatecontext['block03_grid'] = 6;
    }
    return $templatecontext;
}
/**
 * Front page block 4.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock04($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block04headline'] = format_string($theme->settings->block04headline);
    $templatecontext['block04header'] = format_string($theme->settings->block04header);
    $templatecontext['block04maintitle'] = format_string($theme->settings->block04maintitle);
    $templatecontext['block04button'] = format_string($theme->settings->block04button);
    $templatecontext['block04buttonlink'] = $theme->settings->block04buttonlink;
    $templatecontext['block04imgheight'] = $theme->settings->block04imgheight;
    $count = 8;
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block04img = "sliderimageblock04img{$i}";
        $block04title = "block04title{$i}";
        $block04caption = "block04caption{$i}";
        $block04link = "block04link{$i}";
        $image = $theme->setting_file_url($block04img, $block04img);
        if (empty($image)) {
            $image = null;
        }
        if ($i == 1) {
            $templatecontext['block04'][$j]['active'] = "1";
        } else {
            $templatecontext['block04'][$j]['active'] = "";
        }
        $templatecontext['block04'][$j]['image'] = $image;
        $templatecontext['block04'][$j]['title'] = format_string($theme->settings->$block04title);
        $templatecontext['block04'][$j]['caption'] = format_string($theme->settings->$block04caption);
        $templatecontext['block04'][$j]['link'] = $theme->settings->$block04link;
        $templatecontext['block04'][$j]['counter'] = $i;
    }
    for ($i = 1, $j = 0; $i <= 4; $i++, $j++) {
        $block04img = "sliderimageblock04img{$i}";
        $block04title = "block04title{$i}";
        $block04caption = "block04caption{$i}";
        $block04link = "block04link{$i}";
        $image = $theme->setting_file_url($block04img, $block04img);
        if (empty($image)) {
            $image = null;
        }
        $templatecontext['block04_1'][$j]['image'] = $image;
        $templatecontext['block04_1'][$j]['title'] = format_string($theme->settings->$block04title);
        $templatecontext['block04_1'][$j]['caption'] = format_string($theme->settings->$block04caption);
        $templatecontext['block04_1'][$j]['link'] = $theme->settings->$block04link;
        $templatecontext['block04_1'][$j]['counter'] = $i;
    }
    for ($i = 5, $j = 0; $i <= 8; $i++, $j++) {
        $block04img = "sliderimageblock04img{$i}";
        $block04title = "block04title{$i}";
        $block04caption = "block04caption{$i}";
        $block04link = "block04link{$i}";
        $image = $theme->setting_file_url($block04img, $block04img);
        if (empty($image)) {
            $image = null;
        }
        $templatecontext['block04_2'][$j]['image'] = $image;
        $templatecontext['block04_2'][$j]['title'] = format_string($theme->settings->$block04title);
        $templatecontext['block04_2'][$j]['caption'] = format_string($theme->settings->$block04caption);
        $templatecontext['block04_2'][$j]['link'] = $theme->settings->$block04link;
        $templatecontext['block04_2'][$j]['counter'] = $i;
    }
    return $templatecontext;
}
/**
 * Front page block 5.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock05($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block05enabled'] = $theme->settings->block05enabled;
    $templatecontext['block05headline'] = format_string($theme->settings->block05headline);
    $templatecontext['block05header'] = format_string($theme->settings->block05header);
    $templatecontext['block05maintitle'] = format_string($theme->settings->block05maintitle);
    $templatecontext['block05shape01'] = $OUTPUT->image_url('fp/shapes/abstract-12', 'theme');
    $templatecontext['block05shape02'] = $OUTPUT->image_url('fp/shapes/abstract-13', 'theme');
    $image = $theme->setting_file_url('sliderimageblock05img', 'sliderimageblock05img');
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 10));
        }
    }

    $templatecontext['block05image'] = $image;
    if ($dsn == 4) {
        $count = 6;
    } else if ($dsn == 3) {
        $count = 4;
    } else {
        $count = $theme->settings->block05count;
    }
    if ($dsn == 1) {
        $youtube = "https://www.youtube.com/embed/";
        $vimeo = "https://player.vimeo.com/video/";
        if ($theme->settings->block05video == "1") {
            $templatecontext['block05_youtube'] = $theme->settings->block05video;
            $templatecontext['block05videolink'] = $youtube.format_string($theme->settings->block05videolink);
        } else if ($theme->settings->block05video == "2") {
            $templatecontext['block05_vimeo'] = $theme->settings->block05video;
            $templatecontext['block05videolink'] = $vimeo.format_string($theme->settings->block05videolink);
        } else {
            $templatecontext['block05_custom'] = $theme->settings->block05video;
            $templatecontext['block05videolink'] = format_string($theme->settings->block05videolink);
        }
    }
    $exp = explode(',', $theme->settings->block05icon);
    $iconcount = count($exp);
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block05title = "block05title{$i}";
        $block05caption = "block05caption{$i}";
        $block05link = "block05link{$i}";
        if ($j >= $iconcount ) {
            $templatecontext['block05'][$j]['icon'] = isset($exp[0]) ? $exp[0] : null;
        } else {
            $templatecontext['block05'][$j]['icon'] = isset($exp[$j]) ? $exp[$j] : null;
        }
        $templatecontext['block05'][$j]['title'] = format_string($theme->settings->$block05title);
        $templatecontext['block05'][$j]['caption'] = format_string($theme->settings->$block05caption);
        $templatecontext['block05'][$j]['count'] = $i;
        $templatecontext['block05'][$j]['link'] = $theme->settings->$block05link;
    }
    return $templatecontext;
}
/**
 * Front page block 6.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock06($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block06headline'] = format_string($theme->settings->block06headline);
    $templatecontext['block06header'] = format_string($theme->settings->block06header);
    $templatecontext['block06maintitle'] = format_string($theme->settings->block06maintitle);
    $templatecontext['block06color'] = $theme->settings->block06color;
    $templatecontext['block06caption'] = format_text($theme->settings->block06caption);
    $templatecontext['block06button'] = format_string($theme->settings->block06button);
    $templatecontext['block06buttonlink'] = $theme->settings->block06buttonlink;
    $image = $theme->setting_file_url('sliderimageblock06img', 'sliderimageblock06img');
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 10));
        }
    }
    $templatecontext['block06image'] = $image;
    return $templatecontext;
}

/**
 * Front page block 7.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock07($dsn) {
    GLOBAL  $CFG, $DB, $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block07teacherenabled'] = $theme->settings->block07teacherenabled;
    $templatecontext['block07headline'] = format_string($theme->settings->block07headline);
    $templatecontext['block07header'] = format_string($theme->settings->block07header);
    $templatecontext['block07maintitle'] = format_string($theme->settings->block07maintitle);
    $templatecontext['block07button'] = format_string($theme->settings->block07button);
    $templatecontext['block07buttonlink'] = $theme->settings->block07buttonlink;
    $templatecontext['block07imgenabled'] = $theme->settings->block07imgenabled;
    $templatecontext['block07fullname'] = 0;
    $templatecontext['block07shortname'] = 0;
    if ($theme->settings->block07title == 'shortname') {
        $templatecontext['block07shortname'] = 1;
    } else {
        $templatecontext['block07fullname'] = 1;
    }
    require_once( $CFG->libdir . '/filelib.php' );
    $count = $theme->settings->block07count;
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP ". $count ." c.id, c.fullname, c.shortname, c.summary, c.timemodified, c.category, c.format, c.visible";
    } else {
        $sql = "SELECT c.id, c.fullname, c.shortname, c.summary, c.timemodified, c.category, c.format, c.visible";
    }
    $sql = $sql." FROM {course} c";
    $sql = $sql." WHERE c.visible = 1";
    if (!empty($theme->settings->block07crselect)) {
        $sql = $sql." and id IN (". $theme->settings->block07crselect .")";
    }
    $sql = $sql." ORDER BY c.timemodified DESC";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    $allcourses = [];
    $courses = $DB->get_records_sql($sql);
    foreach ($courses as $id => $course) {
        $category = $DB->get_record('course_categories', ['id' => $course->category]);
        if (!empty($category)) {
            $course->categoryName = $category->name;
            $course->categoryId = $category->id;
            $allcourses[$id] = $course;
        }
    };
    $j = 0;
    $sql = "SELECT  en.id, en.courseid, en.cost, en.currency";
    $sql = $sql." FROM {enrol} en";
    $sql = $sql." WHERE en.courseid = :courseid and en.status = 0 and en.cost != 'NULL'";
    $templatecontext['block07priceshow'] = $theme->settings->block07priceshow;
    foreach ($allcourses as $id => $course) {
        $context = context_course::instance($id);
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php',
        $context->id, 'course', 'summary', false);
        $templatecontext['block07'][$j]['fullname'] = format_string($course->fullname);
        $templatecontext['block07'][$j]['shortname'] = format_string($course->shortname);
        $templatecontext['block07'][$j]['summary'] = format_text($summary);
        $sectiontotal = $DB->count_records('course_sections', ['course' => $id]);
        $templatecontext['block07'][$j]['format'] = $sectiontotal." of ". $course->format;
        $templatecontext['block07'][$j]['update'] = gmdate("M d,Y", $course->timemodified);
        $templatecontext['block07'][$j]['categoryName'] = format_string($course->categoryName);
        $templatecontext['block07'][$j]['courselink'] = "course/view.php?id=".$id;
        $templatecontext['block07'][$j]['categorylink'] = "course/index.php?categoryid=".$course->categoryId;
        if ($theme->settings->block07imgenabled) {
            $templatecontext['block07'][$j]['imgurl'] = educard_get_course_image($id, true);
        }
        $templatecontext['block07'][$j]['counter'] = $j + 1;
        $enrol = $DB->get_records_sql($sql, ['courseid' => $id]);
        if (!empty($theme->settings->block07priceshow)) {
            if (empty($enrol)) {
                $templatecontext['block07'][$j]['currency'] = get_string('block07enrol', 'theme_educard');
            } else {
                foreach ($enrol as $enrols) {
                    $templatecontext['block07'][$j]['cost'] = $enrols->cost;
                    $templatecontext['block07'][$j]['currency'] = $enrols->currency;
                };
            }
        }
        $context = context_course::instance($id);
        $role = $theme->settings->block07studentrole;
        $students = get_role_users($role, $context);
        $templatecontext['block07'][$j]['studentscount'] = count($students);
        $role = $theme->settings->block07teacherrole;
        $teachers = get_role_users($role, $context);
        if (!empty($theme->settings->block07teacherenabled)) {
            foreach ($teachers as $id => $teacher) {
                $templatecontext['block07'][$j]['teachername'] = format_string(fullname($teacher));
                $teacher->imagealt = get_string('defaultcourseteacher', 'moodle');
                $templatecontext['block07'][$j]['userpicture'] =
                    $OUTPUT->user_picture($teacher, ['class' => '', 'size' => '512']);
            }
        }
        $j = $j + 1;
        if ($count == $j ) {
            break;
        }
    };
    return $templatecontext;
}
/**
 * Front page block 8.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock08($dsn) {
    GLOBAL $DB, $OUTPUT, $PAGE, $CFG;
    $theme = theme_config::load('educard');
    $count = $theme->settings->block08count;
    $templatecontext['block08headline'] = format_string($theme->settings->block08headline);
    $templatecontext['block08header'] = format_string($theme->settings->block08header);
    $templatecontext['block08maintitle'] = format_string($theme->settings->block08maintitle);
    $teacherrole = $theme->settings->block08showrole;
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP ". $count ." ra.userid, ra.roleid";
    } else {
        $sql = "SELECT ra.userid, ra.roleid";
    }
    $sql = $sql." FROM {role_assignments} ra";
    $sql = $sql." JOIN {context} ctx on ra.contextid = ctx.id";
    $sql = $sql." WHERE ra.roleid = :roleid";
    $sql = $sql." GROUP by ra.userid, ra.roleid";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    // And ctx.contextlevel = '50'?
    if (!empty($theme->settings->block08total)) {
        $courses = get_courses('all', 'c.timemodified DESC');
    }
    $roleassignments = $DB->get_records_sql($sql, ['roleid' => $teacherrole]);
    if (!empty($roleassignments)) {
        $j = 0;
        $coursecount = 0;
        $studentscount = 0;
        foreach ($roleassignments as $roleassignment) {
            $templatecontext['block08'][$j]['showdescription'] = $theme->settings->block08description;
            $roleassignment->imagealt = "Teacher";
            if ($user = $DB->get_record('user', ['id' => $roleassignment->userid])) {
                $personcontext = context_user::instance($user->id);
                $description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php',
                $personcontext->id, 'user', 'profile', false);
                $templatecontext['block08'][$j]['teachername'] = format_string($user->firstname." ".$user->lastname);
                $templatecontext['block08'][$j]['description'] = format_text($description);
                $templatecontext['block08'][$j]['userpicture'] =
                    $OUTPUT->user_picture($user, ['class' => '']);
                $templatecontext['block08'][$j]['userURL'] =
                    new moodle_url('/user/profile.php', ['id' => $roleassignment->userid]);
                $userpicture = new user_picture($user);
                $userpicture->size = 512;
                $url = $userpicture->get_url($PAGE)->out(false);
                $templatecontext['block08'][$j]['userpictureURL'] = $url;
            }
            $templatecontext['block08total'] = $theme->settings->block08total;
            if (!empty($templatecontext['block08total'])) {
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
            $templatecontext['block08'][$j]['coursecount'] = $coursecount;
            $templatecontext['block08'][$j]['studentscount'] = $studentscount;
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
                        $templatecontext['block08'][$j]['job'] = $otherarea->data;
                    } else if ( $otherarea->shortname == "usermail" ) {
                        $templatecontext['block08'][$j]['usermail'] = $otherarea->data;
                    } else if ( substr($otherarea->shortname, 0, 10) == "usersocial" ) {
                        if (!empty($otherarea->data)) {
                            $exp = explode(',', $otherarea->data);
                            $templatecontext['block08'][$j][$otherarea->shortname."link"] = empty($exp[1]) ? null : $exp[1];
                            $socialmedia = "";
                            if (get_string_manager()->string_exists($exp[0], 'theme_educard')) {
                                $socialmedia = get_string($exp[0], 'theme_educard');
                            }
                            if (substr($socialmedia, 0, 1) != "[" ) {
                                $templatecontext['block08'][$j][$otherarea->shortname] = $socialmedia;
                            } else {
                                $templatecontext['block08'][$j][$otherarea->shortname] = "fa fa-question";
                            }
                        }
                    }
                }
            }
            $j = $j + 1;
            if ($j == $theme->settings->block08count) {
                break;
            }
        }
    }
    return $templatecontext;
}
/**
 * Front page block 9.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock09($dsn) {
    GLOBAL $CFG, $DB;
    require_once($CFG->libdir.'/formslib.php');
    $theme = theme_config::load('educard');
    $count = $theme->settings->block09count;
    $templatecontext['block09headline'] = format_string($theme->settings->block09headline);
    $templatecontext['block09header'] = format_string($theme->settings->block09header);
    $templatecontext['block09maintitle'] = format_string($theme->settings->block09maintitle);
    $templatecontext['block09background'] = $theme->settings->block09background;
    // Select category.
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP ". $count ." ca.id, ca.name, ca.parent, ca.coursecount, ca.visible, ca.depth, ca.path, ca.description";
    } else {
        $sql = "SELECT ca.id, ca.name, ca.parent, ca.coursecount, ca.visible, ca.depth, ca.path, ca.description";
    }
    $sql = $sql." FROM {course_categories} ca";
    $sql = $sql." WHERE ca.coursecount > 0 and ca.visible = 1";
    if (!empty($theme->settings->block09ctselect)) {
        $sql = $sql." and id IN (". $theme->settings->block09ctselect .")";
    }
    $sql = $sql." ORDER BY ca.coursecount DESC";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    $categorys = $DB->get_records_sql($sql, []);
    $exp = explode(',', $theme->settings->block09icon);
    $iconcount = count($exp);
    if (!empty($categorys)) {
        $j = 0;
        foreach ($categorys as $category) {
            $templatecontext['block09'][$j]['catagoryname'] = format_string($category->name);
            $templatecontext['block09'][$j]['coursecount'] = $category->coursecount;
            $templatecontext['block09'][$j]['description'] = format_text($category->description);
            $templatecontext['block09'][$j]['catagoryURL'] = new moodle_url('/course/index.php?categoryid='. $category->id);
            $templatecontext['block09'][$j]['bgcolor'] = "";
            $templatecontext['block09'][$j]['bgnone'] = "";
            if ($j >= $iconcount ) {
                $templatecontext['block09'][$j]['block09icon'] = isset($exp[0]) ? $exp[0] : null;
            } else {
                $templatecontext['block09'][$j]['block09icon'] = isset($exp[$j]) ? $exp[$j] : null;
            }
            if ($theme->settings->block09background == '0') {
                $templatecontext['block09'][$j]['bgnone'] = true;
            } else if ($theme->settings->block09background == '1') {
                $templatecontext['block09'][$j]['bgcolor'] = theme_educard_random_color();
            }
            $templatecontext['block09'][$j]['imgurl'] = "";
            if ($theme->settings->block09background == '2') {
                $courses = get_courses($category->id);
                if (!empty($courses)) {
                    foreach ($courses as $course) {
                        $imgurl = educard_get_course_image($course->id, true);
                        if (!empty($imgurl)) {
                            $templatecontext['block09'][$j]['imgurl'] = $imgurl;
                            break;
                        }
                    }
                }
            }
            $templatecontext['block09'][$j]['count'] = substr($j, 0, 1);
            $templatecontext['block09'][$j]['counter'] = $j + 1;
            $j++;
            if ($j == $theme->settings->block09count) {
                break;
            }
        }
    }
    return $templatecontext;
}
/**
 * Front page block 10.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock10($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block10headline'] = format_string($theme->settings->block10headline);
    $templatecontext['block10header'] = format_string($theme->settings->block10header);
    $templatecontext['block10maintitle'] = format_string($theme->settings->block10maintitle);
    $count = $theme->settings->block10count;
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block10img = "sliderimageblock10img{$i}";
        $block10name = "block10name{$i}";
        $block10job = "block10job{$i}";
        $block10caption = "block10caption{$i}";
        $block10link = "block10link{$i}";
        if ($i == 1) {
            $templatecontext['block10'][$j]['active'] = "1";
        } else {
            $templatecontext['block10'][$j]['active'] = "";
        }
        $image = $theme->setting_file_url($block10img, $block10img);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 25));
            }
        }
        $templatecontext['block10'][$j]['block10count'] = $i;
        $templatecontext['block10'][$j]['block10image'] = $image;
        $templatecontext['block10'][$j]['block10name'] = format_string($theme->settings->$block10name);
        $templatecontext['block10'][$j]['block10job'] = format_string($theme->settings->$block10job);
        $templatecontext['block10'][$j]['block10caption'] = format_string($theme->settings->$block10caption);
        $templatecontext['block10'][$j]['block10linkurl'] = $theme->settings->$block10link;
    }
    return $templatecontext;
}
/**
 * Front page block 11.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock11($dsn) {
    // Site blog frontpage.
    global $OUTPUT, $DB, $CFG;
    $theme = theme_config::load('educard');
    if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL and (!isloggedin() or isguestuser())) {
        $templatecontext['block11enabled'] = "false";
        return $templatecontext;
    }
    $templatecontext['block11headline'] = format_string($theme->settings->block11headline);
    $templatecontext['block11header'] = format_string($theme->settings->block11header);
    $templatecontext['block11maintitle'] = format_string($theme->settings->block11maintitle);
    $count = $theme->settings->block11count;
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP ". $count ." *";
    } else {
        $sql = "SELECT *";
    }
    $sql = $sql." FROM {post} pt";
    if (isloggedin()) {
        $sql = $sql." WHERE pt.publishstate = 'public' or pt.publishstate = 'site'";
    } else {
        $sql = $sql." WHERE pt.publishstate = 'public'";
    }
    $sql = $sql." ORDER BY pt.created DESC";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    $posts = $DB->get_records_sql($sql, []);
    if (!empty($posts)) {
        $j = 0;
        foreach ($posts as $post) {
            $context = context_system::instance();
            $summary = file_rewrite_pluginfile_urls($post->summary, 'pluginfile.php', 
            $context->id, 'blog', 'post', $post->id);
            $templatecontext['block11'][$j]['subject'] = format_string($post->subject);
            $templatecontext['block11'][$j]['summary'] = format_text($summary);
            $templatecontext['block11'][$j]['created'] = gmdate(" D, d M Y", $post->created);
            $templatecontext['block11'][$j]['lastmodified'] = gmdate("d/M/Y", $post->lastmodified);
            $templatecontext['block11'][$j]['postURL'] = new moodle_url('/blog/index.php?entryid='. $post->id);
            $templatecontext['block11'][$j]['imgurl'] = educard_get_blog_post_image($post->id);
            $templatecontext['block11'][$j]['tag'] = $OUTPUT->tag_list(core_tag_tag::get_item_tags('core', 'post', $post->id));
            if ($user = $DB->get_record('user', ['id' => $post->userid])) {
                $user->imagealt = "Author";
                $templatecontext['block11'][$j]['userpicture'] =
                    $OUTPUT->user_picture($user, ['size' => '512']);
                $templatecontext['block11'][$j]['userURL'] =
                    new moodle_url('/user/profile.php', ['id' => $post->userid]);
                $templatecontext['block11'][$j]['username'] = fullname($user);
            }
            if ($j == 0) {
                $templatecontext['block11'][$j]['active'] = "1";
            } else {
                $templatecontext['block11'][$j]['active'] = "";
            }
            $by = new stdClass();
            $by->name = fullname($user);
            $by->date = userdate($post->created);
            $shortdate = "";
            $exp = explode(',', $OUTPUT->container($by->date, 'userdate'));
            if (!empty($exp)) {
                $trimdate = trim(empty($exp[1]) ? null : $exp[1]);
                if (!empty($trimdate)) {
                    $shortdate = substr($trimdate, 0, 2) ." ". substr($trimdate,
                        strcspn($trimdate, " ") + 1 , 3) ." ". substr($trimdate, -4, 4);
                }
            }
            $templatecontext['block11'][$j]['shortdate'] = $shortdate;
            $templatecontext['block11'][$j]['userdate'] = $OUTPUT->container($by->date, 'userdate');
            $j++;
        }
    }
    return $templatecontext;
}
/**
 * Front page block 12.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock12($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block12headline'] = format_string($theme->settings->block12headline);
    $templatecontext['block12header'] = format_string($theme->settings->block12header);
    $templatecontext['block12maintitle'] = format_string($theme->settings->block12maintitle);
    $count = $theme->settings->block12count;
    $youtube = "https://www.youtube.com/embed/";
    $vimeo = "https://player.vimeo.com/video/";
    $exp = explode(',', $theme->settings->block12icon);
    $iconcount = count($exp);
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block12title = "block12title{$i}";
        $block12caption = "block12caption{$i}";
        $block12link = "block12link{$i}";
        $block12video = "block12video{$i}";
        $imgblock12 = "imgblock12{$i}";
        $image = $theme->setting_file_url($imgblock12, $imgblock12);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 2));
            }
        }
        if ($j >= $iconcount ) {
            $templatecontext['block12'][$j]['block12icon'] = isset($exp[0]) ? $exp[0] : null;
        } else {
            $templatecontext['block12'][$j]['block12icon'] = isset($exp[$j]) ? $exp[$j] : null;
        }
        $templatecontext['block12'][$j]['block12title'] = format_string($theme->settings->$block12title);
        $templatecontext['block12'][$j]['block12caption'] = format_string($theme->settings->$block12caption);
        $templatecontext['block12'][$j]['block12count'] = $i;
        $templatecontext['block12'][$j]['block12image'] = $image;
        if ($theme->settings->$block12video == "1") {
            $templatecontext['block12'][$j]['youtube'] = $theme->settings->$block12video;
            $templatecontext['block12'][$j]['link'] = $youtube.format_string($theme->settings->$block12link);
        } else if ($theme->settings->$block12video == "2") {
            $templatecontext['block12'][$j]['vimeo'] = $theme->settings->$block12video;
            $templatecontext['block12'][$j]['link'] = $vimeo.format_string($theme->settings->$block12link);
        } else {
            $templatecontext['block12'][$j]['custom'] = $theme->settings->$block12video;
            $templatecontext['block12'][$j]['link'] = format_string($theme->settings->$block12link);
        }
    }
    return $templatecontext;
}
/**
 * Front page block 13.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock13($dsn) {
    GLOBAL $CFG, $DB;
    $theme = theme_config::load('educard');
    // Categories.
    $templatecontext['block13headline'] = format_string($theme->settings->block13headline);
    $templatecontext['block13header'] = format_string($theme->settings->block13header);
    $templatecontext['block13maintitle'] = format_string($theme->settings->block13maintitle);
    $templatecontext['block13imgenabled'] = $theme->settings->block13imgenabled;
    $sql = "SELECT ca.id, ca.name, ca.parent, ca.coursecount, ca.visible, ca.depth, ca.path, ca.description";
    $sql = $sql." FROM {course_categories} ca";
    $sql = $sql." WHERE ca.coursecount > 0 and ca.visible = 1";
    $sql = $sql." ORDER BY ca.coursecount DESC";
    $categorys = $DB->get_records_sql($sql, []);
    $totalcourse = 0;
    if (!empty($categorys)) {
        $j = 0;
        foreach ($categorys as $category) {
            $templatecontext['block13-ctgry'][$j]['catagoryname'] = format_string($category->name);
            $templatecontext['block13-ctgry'][$j]['coursecount'] = $category->coursecount;
            $templatecontext['block13-ctgry'][$j]['description'] = format_text($category->description);
            $templatecontext['block13-ctgry'][$j]['catagoryURL'] = new moodle_url('/course/index.php?categoryid='. $category->id);
            $templatecontext['block13-ctgry'][$j]['catagoryid'] = $category->id;
            $totalcourse = $totalcourse + $category->coursecount;
            $catagorycounter = $j + 1;
            $j++;
        }
    }
    $templatecontext['block13catagorycounter'] = $catagorycounter;
    $templatecontext['block13totalcourse'] = $totalcourse;
    // Course.
    $templatecontext['block13fullname'] = 0;
    $templatecontext['block13shortname'] = 0;
    $count = $theme->settings->block13count;
    if ($theme->settings->block07title == 'shortname') {
        $templatecontext['block13shortname'] = 1;
    } else {
        $templatecontext['block13fullname'] = 1;
    }
    require_once( $CFG->libdir . '/filelib.php' );
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP ". $count ." *";
    } else {
        $sql = "SELECT *";
    }
    $sql = $sql." FROM {course}";
    $sql = $sql." WHERE visible = 1";
    $sql = $sql." ORDER BY timemodified DESC";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    $courses = $DB->get_records_sql($sql);

    foreach ($courses as $id => $course) {
        $category = $DB->get_record('course_categories', ['id' => $course->category]);
        if (!empty($category)) {
            $course->categoryName = format_string($category->name);
            $course->categoryId = $category->id;
            $allcourses[$id] = $course;
        }
    };
    $j = 0;
    // Course enrol, currency SQL.
    $sql = "SELECT  en.id, en.courseid, en.cost, en.currency";
    $sql = $sql." FROM {enrol} en";
    $sql = $sql." WHERE en.courseid = :courseid and en.status = 0 and en.cost != 'NULL'";
    // Course star SQL.
    $sqla = "SELECT fv.itemid, count(fv.itemid) as countstar";
    $sqla = $sqla." FROM {favourite} fv";
    $sqla = $sqla." WHERE fv.itemid = :courseid and fv.itemtype = 'courses'";
    $sqla = $sqla." GROUP BY fv.itemid";
    $templatecontext['block13priceshow'] = $theme->settings->block07priceshow;
    foreach ($allcourses as $id => $course) {
        $context = context_course::instance($id);
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php',
        $context->id, 'course', 'summary', false);
        $templatecontext['block13'][$j]['fullname'] = format_string($course->fullname);
        $templatecontext['block13'][$j]['shortname'] = format_string($course->shortname);
        $templatecontext['block13'][$j]['summary'] = format_text($summary);
        $sectiontotal = $DB->count_records('course_sections', ['course' => $id]);
        $templatecontext['block13'][$j]['format'] = $sectiontotal." of ". $course->format;
        $templatecontext['block13'][$j]['update'] = gmdate("M d,Y", $course->timemodified);
        $templatecontext['block13'][$j]['categoryName'] = format_string($course->categoryName);
        $templatecontext['block13'][$j]['courselink'] = "course/view.php?id=".$id;
        $templatecontext['block13'][$j]['categorylink'] = "course/index.php?categoryid=".$course->categoryId;
        $templatecontext['block13'][$j]['idcategory'] = $course->categoryId;
        if ($theme->settings->block13imgenabled) {
            $templatecontext['block13'][$j]['imgurl'] = educard_get_course_image($id, true);
        }
        $templatecontext['block13'][$j]['counter'] = $j + 1;
        $enrol = $DB->get_records_sql($sql, ['courseid' => $id]);
        $star = $DB->get_records_sql($sqla, ['courseid' => $id]);
        if (!empty($theme->settings->block07priceshow)) {
            if (empty($enrol)) {
                $templatecontext['block13'][$j]['currency'] = get_string('block07enrol', 'theme_educard');
            } else {
                foreach ($enrol as $enrols) {
                    $templatecontext['block13'][$j]['cost'] = $enrols->cost;
                    $templatecontext['block13'][$j]['currency'] = $enrols->currency;
                };
            }
        }
        if (empty($star)) {
            $templatecontext['block13'][$j]['star'] = false;
        } else {
            foreach ($star as $stars) {
                $templatecontext['block13'][$j]['star'] = $stars->countstar;
            };
        }
        $context = context_course::instance($id);
        $role = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $students = get_role_users($role, $context);
        $templatecontext['block13'][$j]['studentscount'] = count($students);
        $role = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $teachers = get_role_users($role, $context);
        if (!empty($theme->settings->block07teacherenabled)) {
            foreach ($teachers as $id => $teacher) {
                $templatecontext['block13'][$j]['teachername'] = format_string(fullname($teacher));
                $teacher->imagealt = get_string('defaultcourseteacher', 'moodle');
            }
        }
        $j++;
        if ($count == $j ) {
            break;
        }
    };
    return $templatecontext;
}
/**
 * Front page block 14.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock14($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block14headline'] = format_string($theme->settings->block14headline);
    $templatecontext['block14header'] = format_string($theme->settings->block14header);
    $templatecontext['block14maintitle'] = format_string($theme->settings->block14maintitle);
    $count = $theme->settings->block14count;
    for ($i = 1, $j = 0; $i <= $count; $i++, $j++) {
        $block14img = "sliderimageblock14img{$i}";
        $block14eventheader = "block14eventheader{$i}";
        $block14caption = "block14caption{$i}";
        $block14link = "block14link{$i}";
        $block14eventdate = "block14eventdate{$i}";
        $block14detail = "block14detail{$i}";
        $image = $theme->setting_file_url($block14img, $block14img);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 2));
            }
        }
        $templatecontext['block14'][$j]['img'] = $image;
        $templatecontext['block14'][$j]['eventheader'] = format_string($theme->settings->$block14eventheader);
        $templatecontext['block14'][$j]['caption'] = format_string($theme->settings->$block14caption);
        $templatecontext['block14'][$j]['link'] = $theme->settings->$block14link;
        $templatecontext['block14'][$j]['date'] = $theme->settings->$block14eventdate;
        $templatecontext['block14'][$j]['detail'] = $theme->settings->$block14detail;
        $templatecontext['block14'][$j]['count'] = $i;
    }
    return $templatecontext;
}
/**
 * Front page block 15.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock15($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block15title'] = format_string($theme->settings->block15title);
    $templatecontext['block15caption'] = format_text($theme->settings->block15caption, FORMAT_HTML, ['noclean' => true]);
    $templatecontext['block15csslink'] = $theme->settings->block15csslink;
    $templatecontext['block15css'] = $theme->settings->block15css;
    return $templatecontext;
}
/**
 * Front page block 16.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock16($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block16title'] = format_string($theme->settings->block16title);
    $templatecontext['block16caption'] = format_text($theme->settings->block16caption, FORMAT_HTML, ['noclean' => true]);
    $templatecontext['block16csslink'] = $theme->settings->block16csslink;
    $templatecontext['block16css'] = $theme->settings->block16css;
    return $templatecontext;
}
/**
 * Front page block 17.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock17($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block17title'] = format_string($theme->settings->block17title);
    $templatecontext['block17caption'] = format_text($theme->settings->block17caption, FORMAT_HTML, ['noclean' => true]);
    $templatecontext['block17csslink'] = $theme->settings->block17csslink;
    $templatecontext['block17css'] = $theme->settings->block17css;
    return $templatecontext;
}
/**
 * Front page block 18.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock18($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block18title'] = format_string($theme->settings->block18title);
    $templatecontext['block18caption'] = format_text($theme->settings->block18caption, FORMAT_HTML, ['noclean' => true]);
    $templatecontext['block18csslink'] = $theme->settings->block18csslink;
    $templatecontext['block18css'] = $theme->settings->block18css;
    return $templatecontext;
}
/**
 * Front page block 19.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock19($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block19headerenabled'] = $theme->settings->block19headerenabled;
    if (!empty($templatecontext['block19headerenabled'])) {
        $templatecontext['block19headline'] = format_string($theme->settings->block19headline);
        $templatecontext['block19header'] = format_string($theme->settings->block19header);
        $templatecontext['block19maintitle'] = format_string($theme->settings->block19maintitle);
    }
    $count = $theme->settings->block19count;
    $j = 0;
    for ($i = 1; $i <= $count; $i++) {
        $block19img = "sliderimageblock19img{$i}";
        $block19link = "block19link{$i}";
        $image = $theme->setting_file_url($block19img, $block19img);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 2));
            }
        }

        $templatecontext['block19'][$j]['image19'] = $image;
        $templatecontext['block19'][$j]['link'] = format_string($theme->settings->$block19link);
        $templatecontext['block19'][$j]['count'] = $i;
        $j++;

    }
    return $templatecontext;
}
/**
 * Front page block 20.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock20($dsn) {
    $theme = theme_config::load('educard');
    $templatecontext['block20enabled'] = $theme->settings->block20enabled;
    $templatecontext['block20design'.$theme->settings->block20design.''] = true;
    $templatecontext['block20moodle'] = $theme->settings->block20moodle;
    $templatecontext['footerbackcolor'] = $theme->settings->footerbackcolor;
    $templatecontext['block20logo'] = $theme->setting_file_url('block20logo', 'block20logo');
    $templatecontext['block20col1header'] = format_string($theme->settings->block20col1header);
    $templatecontext['block20col1caption'] = format_string($theme->settings->block20col1caption);
    $templatecontext['block20col2header'] = format_string($theme->settings->block20col2header);
    $templatecontext['block20col2links'] = theme_educard_links($theme->settings->block20col2link);
    $templatecontext['block20col3header'] = format_string($theme->settings->block20col3header);
    $templatecontext['block20col3links'] = theme_educard_links($theme->settings->block20col3link);
    $templatecontext['block20col4header'] = format_string($theme->settings->block20col4header);
    $templatecontext['block20col4caption'] = format_text($theme->settings->block20col4caption);
    $templatecontext['block20social'] = $theme->settings->block20social;
    $templatecontext['block20copyright'] = format_text($theme->settings->block20copyright);
    return $templatecontext;
}
/**
 * Front page block 21.
 *
 * @param string $dsn theme block design.
 */
function theme_educard_frontpageblock21($dsn) {
    GLOBAL  $CFG, $DB, $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['block21teacherenabled'] = $theme->settings->block21teacherenabled;
    $templatecontext['block21headline'] = format_string($theme->settings->block21headline);
    $templatecontext['block21header'] = format_string($theme->settings->block21header);
    $templatecontext['block21maintitle'] = format_string($theme->settings->block21maintitle);
    $templatecontext['block21button'] = format_string($theme->settings->block21button);
    $templatecontext['block21buttonlink'] = $theme->settings->block21buttonlink;
    $templatecontext['block21imgenabled'] = $theme->settings->block21imgenabled;
    $templatecontext['block21fullname'] = 0;
    $templatecontext['block21shortname'] = 0;
    if ($theme->settings->block21title == 'shortname') {
        $templatecontext['block21shortname'] = 1;
    } else {
        $templatecontext['block21fullname'] = 1;
    }
    require_once( $CFG->libdir . '/filelib.php' );
    $count = $theme->settings->block21count;
    // Course custom field.
    global $DB;

    $datefieldid = $theme->settings->block21led;
    $livefieldid = $theme->settings->block21plive;

    // SQL Server.
    $sql = "SELECT";
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = " TOP ".$count;
    }
    $sql = $sql." c.id, c.fullname, c.shortname, c.summary, c.timemodified, c.category, c.format, c.visible,";
    $sql = $sql." cflive.value, cfd.intvalue";
    $sql = $sql." FROM {course} c
                    JOIN {customfield_data} cflive ON cflive.instanceid = c.id and cflive.fieldid = ".$datefieldid. "
                    JOIN {customfield_data} cfd ON cfd.instanceid = c.id and cfd.fieldid = ".$livefieldid. "
                    WHERE c.visible = 1 and cfd.intvalue = 1
                    ORDER BY cflive.value ".$theme->settings->block21ledsort;
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql." LIMIT ". $count;
    }
    $allcourses = [];
    $courses = $DB->get_records_sql($sql);
    foreach ($courses as $id => $course) {
        $category = $DB->get_record('course_categories', ['id' => $course->category]);
        if (!empty($category)) {
            $course->categoryName = $category->name;
            $course->categoryId = $category->id;
            $allcourses[$id] = $course;
        }
    };
    $j = 0;
    $sql = "SELECT  en.id, en.courseid, en.cost, en.currency";
    $sql = $sql." FROM {enrol} en";
    $sql = $sql." WHERE en.courseid = :courseid and en.status = 0 and en.cost != 'NULL'";
    $templatecontext['block21priceshow'] = $theme->settings->block21priceshow;
    foreach ($allcourses as $id => $course) {
        $context = context_course::instance($id);
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php',
        $context->id, 'course', 'summary', false);
        $templatecontext['block21'][$j]['fullname'] = format_string($course->fullname);
        $templatecontext['block21'][$j]['shortname'] = format_string($course->shortname);
        $templatecontext['block21'][$j]['summary'] = format_text($summary);
        $templatecontext['block21'][$j]['update'] = userdate($course->value, $theme->settings->block21ledfrmt);
        $sectiontotal = $DB->count_records('course_sections', ['course' => $id]);
        $templatecontext['block21'][$j]['format'] = $sectiontotal." of ". $course->format;
        $templatecontext['block21'][$j]['categoryName'] = format_string($course->categoryName);
        $templatecontext['block21'][$j]['courselink'] = "course/view.php?id=".$id;
        $templatecontext['block21'][$j]['categorylink'] = "course/index.php?categoryid=".$course->categoryId;
        if ($theme->settings->block21imgenabled) {
            $templatecontext['block21'][$j]['imgurl'] = educard_get_course_image($id, true);
        }
        $templatecontext['block21'][$j]['counter'] = $j + 1;
        $enrol = $DB->get_records_sql($sql, ['courseid' => $id]);
        if (!empty($theme->settings->block21priceshow)) {
            if (empty($enrol)) {
                $templatecontext['block21'][$j]['currency'] = get_string('block21enrol', 'theme_educard');
            } else {
                foreach ($enrol as $enrols) {
                    $templatecontext['block21'][$j]['cost'] = $enrols->cost;
                    $templatecontext['block21'][$j]['currency'] = $enrols->currency;
                };
            }
        }
        $context = context_course::instance($id);
        $role = $theme->settings->block21studentrole;
        $students = get_role_users($role, $context);
        $templatecontext['block21'][$j]['studentscount'] = count($students);
        $role = $theme->settings->block21teacherrole;
        $teachers = get_role_users($role, $context);
        if (!empty($theme->settings->block21teacherenabled)) {
            foreach ($teachers as $id => $teacher) {
                $templatecontext['block21'][$j]['teachername'] = format_string(fullname($teacher));
                $teacher->imagealt = get_string('defaultcourseteacher', 'moodle');
                $templatecontext['block21'][$j]['userpicture'] =
                    $OUTPUT->user_picture($teacher, ['class' => '', 'size' => '512']);
            }
        }
        $j = $j + 1;
        if ($count == $j ) {
            break;
        }
    };
    return $templatecontext;
}
/**
 * Front page block footer select.
 *
 */
function theme_educard_footer_select() {
    $theme = theme_config::load('educard');
    if ($theme->settings->footerselect == "1") {
        $templatecontext['footerselect1'] = $theme->settings->footerselect;
    } else if (($theme->settings->footerselect == "2")) {
        $templatecontext['footerselect2'] = $theme->settings->footerselect;
    } else {
        $templatecontext['footerselect3'] = $theme->settings->footerselect;
    }
    return $templatecontext;
}
