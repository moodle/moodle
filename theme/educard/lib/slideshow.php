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
 * Transferring educard main slide block settings to mustache
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Front page sliders.
 *
 * @param int $dsn design id.
 */
function theme_educard_slideshow($dsn) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    if ($dsn == 0) {
        $dsn = $theme->settings->sliderdesing;
    }
    $templatecontext['slideropacity'] = $theme->settings->slideropacity;
    $templatecontext['slider' . $dsn] = $dsn;
    $templatecontext['slidershowheight'] = $theme->settings->slidershowheight;
    $templatecontext['kenburns'] = $theme->settings->sliderimgkenburns;
    $templatecontext['textposition'] = $theme->settings->textposition;
    $templatecontext['autoplay'] = $theme->settings->autoplay;
    $templatecontext['nextprev'] = $theme->settings->nextprev;
    $templatecontext['pagination'] = $theme->settings->pagination;
    $templatecontext['textanimation'] = $theme->settings->textanimation;
    $templatecontext['slidershapes'] = $theme->settings->slidershapes;
    $slidercount = $theme->settings->slidercount;
    for ($i = 1, $j = 0; $i <= $slidercount; $i++, $j++) {
        $sliderimage = "sliderimage{$i}";
        $slidertitle = "slidertitle{$i}";
        $slidercap = "slidercap{$i}";
        $sliderbutton = "sliderbutton{$i}";
        $sliderurl = "sliderurl{$i}";
        $sliderurlblank = "sliderurlblank{$i}";
        $sliderimageenable = "sliderimageenable{$i}";
        $templatecontext['slides'][$j]['key'] = $j;
        $templatecontext['slides'][$j]['active'] = false;
        if ($theme->settings->$sliderimageenable) {
            $image = $theme->setting_file_url($sliderimage, $sliderimage);
            if (empty($image)) {
                if (empty($theme->settings->frontpageimglink)) {
                    $image = $OUTPUT->get_generated_image_for_id(rand(10, 100));
                } else {
                    $image = $theme->settings->frontpageimglink . "slider/d".$dsn . "/".$i.".jpg";
                }
            }
        } else {
            $image = null;
        }
        $templatecontext['slides'][$j]['image'] = $image;
        $templatecontext['slides'][$j]['count'] = $i;
        $templatecontext['slides'][$j]['title'] = format_string($theme->settings->$slidertitle);
        $templatecontext['slides'][$j]['caption'] = format_string($theme->settings->$slidercap);
        $templatecontext['slides'][$j]['button'] = format_string($theme->settings->$sliderbutton);
        $templatecontext['slides'][$j]['buttonurl'] = $theme->settings->$sliderurl;
        $templatecontext['slides'][$j]['imageenable'] = $theme->settings->$sliderimageenable;
        if ($theme->settings->$sliderurlblank && $theme->settings->$sliderurl) {
            $templatecontext['slides'][$j]['buttonurlblank'] = true;
        }
        if ($i === 1) {
            $templatecontext['slides'][$j]['active'] = true;
        }
    }
    return $templatecontext;
}

/**
 * Front page sliders.
 *
 */
function theme_educard_announcements() {
    global $CFG, $DB, $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['announcementscount'] = $theme->settings->announcementscount;
    $count = $theme->settings->announcementscount;
    // SQL Server.
    if ($CFG->dbtype === 'sqlsrv') {
        $sql = "SELECT TOP " . $count . "fd.id as discid, ";
    } else {
        $sql = "SELECT fd.id as discid, ";
    }
    $sql = $sql . "fp.id as postid, fd.course as disccourse, fd.forum as discforum, fd.timestart as timestart, ";
    $sql = $sql . "fd.timeend as timeend, ";
    $sql = $sql . "fd.name as discname, fd.firstpost as discfirstpost, ";
    $sql = $sql . "fd.userid as discuserid, fp.modified as postmodified, fp.subject as postsubject, fp.message as postmessage  ";
    $sql = $sql . "FROM {forum_discussions} fd ";
    $sql = $sql . "JOIN {forum_posts} fp ON fp.id = fd.firstpost ";
    $sql = $sql . "WHERE fd.course = 1 and fd.timestart <= ".time();
    $sql = $sql . " and ( fd.timeend >= ".time()." or fd.timeend = 0 )";
    $sql = $sql . " ORDER BY fp.modified DESC";
    if ($CFG->dbtype != 'sqlsrv') {
        $sql = $sql . " LIMIT " . $count;
    }
    $records = $DB->get_records_sql($sql);
    if (empty($records)) {
        $templatecontext['announcementsbar'] = false;
        return $templatecontext;
    }
    $j = 0;
    foreach ($records as $record) {
        $templatecontext['duyuru'][$j]['name'] = $record->discname;
        $templatecontext['duyuru'][$j]['postmodified'] = userdate($record->postmodified, "%b %d,%Y");
        $templatecontext['duyuru'][$j]['subject'] = format_string($record->postsubject);
        $templatecontext['duyuru'][$j]['message'] = format_text($record->postmessage);
        $templatecontext['duyuru'][$j]['fullname'] = format_string(fullname($record));
        // User name and picture.
        if ($user = $DB->get_record('user', ['id' => $record->discuserid])) {
            $templatecontext['duyuru'][$j]['fullname'] = format_string($user->firstname." ".$user->lastname);
            $templatecontext['duyuru'][$j]['userpicture'] =
                $OUTPUT->user_picture($user, ['size' => '45', 'class' => 'rounded']);
        }
        $templatecontext['duyuru'][$j]['say'] = $j + 1;
        if ($j === 0) {
            $templatecontext['duyuru'][$j]['active'] = true;
        }
        $templatecontext['duyuru'][$j]['url'] = "mod/forum/discuss.php?d=".$record->discid;
        $j = $j + 1;
        $templatecontext['duyurucount'] = $j;
    }

    return $templatecontext;
}
