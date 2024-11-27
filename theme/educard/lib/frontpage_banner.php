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
 * Transferring educard banner settings to mustache
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Front page banner 1.
 */
function theme_educard_frontpagebanner01() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['shape1'] = $OUTPUT->image_url('fp/shapes/bubble1', 'theme');
    $templatecontext['shape2'] = $OUTPUT->image_url('fp/shapes/abstract-6', 'theme');
    $templatecontext['shape3'] = $OUTPUT->image_url('fp/shapes/paper_plane', 'theme');
    $templatecontext['shape4'] = $OUTPUT->image_url('fp/shapes/bubble2', 'theme');
    $templatecontext['banner1caption1'] = format_string($theme->settings->banner1caption1);
    $templatecontext['banner1caption2'] = format_string($theme->settings->banner1caption2);
    $templatecontext['banner1caption3'] = format_string($theme->settings->banner1caption3);
    $templatecontext['banner1caption4'] = format_string($theme->settings->banner1caption4);
    $templatecontext['banner1placeholder'] = format_string($theme->settings->banner1placeholder);
    $templatecontext['banner1ctgtext'] = format_string($theme->settings->banner1ctgtext);
    $templatecontext['banner1shape'] = $theme->settings->banner1shape;
    if (!empty($theme->settings->banner1ctgid)) {
        $exp = explode(',', $theme->settings->banner1ctgid);
        $ctgcount = count($exp);
        for ($i = 1, $j = 0; $i <= $ctgcount; $i++, $j++) {
            $templatecontext['banner1ctg'][$j]['ctgid'] = $exp[$j];
            $templatecontext['banner1ctg'][$j]['ctgname'] = theme_educard_ctgname($exp[$j]);
        }
    }
    $image = $theme->setting_file_url("imgbanner1", "imgbanner1");
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(1963);
        } else {
            $image = $theme->settings->frontpageimglink."banners/1.jpg";
        }
    }
    $templatecontext['imgbanner1'] = $image;

    return $templatecontext;
}
/**
 * Front page banner 2.
 */
function theme_educard_frontpagebanner02() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['shape2'] = $OUTPUT->image_url('fp/shapes/free_now', 'theme');
    $templatecontext['shape3'] = $OUTPUT->image_url('fp/shapes/abstract-4', 'theme');
    $templatecontext['banner2caption1'] = format_string($theme->settings->banner2caption1);
    $templatecontext['banner2caption2'] = format_string($theme->settings->banner2caption2);
    $templatecontext['banner2caption3'] = format_string($theme->settings->banner2caption3);
    $templatecontext['banner2caption4'] = format_string($theme->settings->banner2caption4);
    $templatecontext['banner2placeholder'] = format_string($theme->settings->banner2placeholder);
    $templatecontext['banner2ctgtext'] = format_string($theme->settings->banner2ctgtext);
    $templatecontext['banner2shape'] = $theme->settings->banner2shape;
    if (!empty($theme->settings->banner2ctgid)) {
        $exp = explode(',', $theme->settings->banner2ctgid);
        $ctgcount = count($exp);
        for ($i = 1, $j = 0; $i <= $ctgcount; $i++, $j++) {
            $templatecontext['banner2ctg'][$j]['ctgid'] = $exp[$j];
            $templatecontext['banner2ctg'][$j]['ctgname'] = theme_educard_ctgname($exp[$j]);
        }
    }
    $image = $theme->setting_file_url("imgbanner2", "imgbanner2");
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(1963);
        } else {
            $image = $theme->settings->frontpageimglink."banners/2.jpg";
        }
    }
    $templatecontext['imgbanner2'] = $image;
    if (!empty($theme->settings->banner2courseid) && is_numeric($theme->settings->banner2courseid)) {
        $templatecontext['banner2styl'.$theme->settings->banner2coursestyl] = $theme->settings->banner2coursestyl;
        $templatecontext = array_merge($templatecontext, theme_educard_course_single($theme->settings->banner2courseid));
    }
    return $templatecontext;
}
/**
 * Front page banner 3.
 */
function theme_educard_frontpagebanner03() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['shape1'] = $OUTPUT->image_url('fp/shapes/popular_courses', 'theme');
    $templatecontext['shape2'] = $OUTPUT->image_url('fp/shapes/abstract-18', 'theme');
    $templatecontext['shape3'] = $OUTPUT->image_url('fp/shapes/abstract-11', 'theme');
    $templatecontext['shape3drk'] = $OUTPUT->image_url('fp/shapes/abstract-11-dark', 'theme');
    $templatecontext['shape4'] = $OUTPUT->image_url('fp/shapes/bubble2', 'theme');
    $templatecontext['banner3caption1'] = format_string($theme->settings->banner3caption1);
    $templatecontext['banner3caption2'] = format_string($theme->settings->banner3caption2);
    $templatecontext['banner3caption3'] = format_string($theme->settings->banner3caption3);
    $templatecontext['banner3btn'] = format_string($theme->settings->banner3btn);
    $templatecontext['banner3btnlnk'] = $theme->settings->banner3btnlnk;
    $templatecontext['banner3shape'] = $theme->settings->banner3shape;

    $templatecontext['banner3subhd1'] = format_string($theme->settings->banner3subhd1);
    $templatecontext['banner3subhd2'] = format_string($theme->settings->banner3subhd2);
    $templatecontext['banner3icon'] = $theme->settings->banner3icon;
    if (!empty($theme->settings->banner3subhd1)) {
        $exp = explode('&', $theme->settings->banner3subhd1);
        $say = count($exp);
        for ($i = 1, $j = 0; $i <= $say; $i++, $j++) {
            $templatecontext['subheader1-'.$i] = $exp[$j];
        }
    }
    if (!empty($theme->settings->banner3subhd2)) {
        $exp = explode('&', $theme->settings->banner3subhd2);
        $say = count($exp);
        for ($i = 1, $j = 0; $i <= $say; $i++, $j++) {
            $templatecontext['subheader2-'.$i] = $exp[$j];
        }
    }
    if (!empty($theme->settings->banner3icon)) {
        $exp = explode('&', $theme->settings->banner3icon);
        $say = count($exp);
        for ($i = 1, $j = 0; $i <= $say; $i++, $j++) {
            $templatecontext['icon-'.$i] = $exp[$j];
        }
    }

    $image = $theme->setting_file_url("imgbanner3", "imgbanner3");
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(1963);
        } else {
            $image = $theme->settings->frontpageimglink."banners/3.jpg";
        }
    }
    $templatecontext['imgbanner3'] = $image;

    return $templatecontext;
}
/**
 * Front page banner 4.
 */
function theme_educard_frontpagebanner04() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['shape1'] = $OUTPUT->image_url('fp/shapes/abstract-21', 'theme');
    $templatecontext['shape2'] = $OUTPUT->image_url('fp/shapes/abstract-25', 'theme');
    $templatecontext['shape3'] = $OUTPUT->image_url('fp/shapes/abstract-22', 'theme');
    $templatecontext['shape4'] = $OUTPUT->image_url('fp/shapes/abstract-23', 'theme');
    $templatecontext['banner4caption1'] = format_string($theme->settings->banner4caption1);
    $templatecontext['banner4caption2'] = format_string($theme->settings->banner4caption2);
    $templatecontext['banner4caption3'] = format_string($theme->settings->banner4caption3);
    $templatecontext['banner4btn'] = format_string($theme->settings->banner4btn);
    $templatecontext['banner4btnlnk'] = $theme->settings->banner4btnlnk;
    $templatecontext['banner4shape'] = $theme->settings->banner4shape;

    $image = $theme->setting_file_url("imgbanner4", "imgbanner4");
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(1963);
        } else {
            $image = $theme->settings->frontpageimglink."banners/4.jpg";
        }
    }
    $templatecontext['imgbanner4'] = $image;

    return $templatecontext;
}
/**
 * Front page banner 5.
 */
function theme_educard_frontpagebanner05() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $templatecontext['shape1'] = $OUTPUT->image_url('fp/shapes/abstract-26', 'theme');
    $templatecontext['shape2'] = $OUTPUT->image_url('fp/shapes/abstract-27', 'theme');
    $templatecontext['shape4'] = $OUTPUT->image_url('fp/shapes/abstract-28', 'theme');
    $templatecontext['banner5caption1'] = format_string($theme->settings->banner5caption1);
    $templatecontext['banner5caption2'] = format_string($theme->settings->banner5caption2);
    $templatecontext['banner5caption3'] = format_string($theme->settings->banner5caption3);
    $templatecontext['banner5btn'] = format_string($theme->settings->banner5btn);
    $templatecontext['banner5btnlnk'] = $theme->settings->banner5btnlnk;
    $templatecontext['banner5btn1'] = format_string($theme->settings->banner5btn1);
    $templatecontext['banner5btn1lnk'] = $theme->settings->banner5btn1lnk;
    $templatecontext['banner5shape'] = $theme->settings->banner5shape;
    $image = $theme->setting_file_url("imgbanner5", "imgbanner5");
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(1963);
        } else {
            $image = $theme->settings->frontpageimglink."banners/5.jpg";
        }
    }
    $templatecontext['imgbanner5'] = $image;

    return $templatecontext;
}
/**
 * Front page banner 5.
 */
function theme_educard_frontpagebanner06() {
    $theme = theme_config::load('educard');
    $templatecontext['banner6caption1'] = format_string($theme->settings->banner6caption1);
    $templatecontext['banner6caption2'] = format_string( $theme->settings->banner6caption2);
    $templatecontext['banner6btn'] = format_string($theme->settings->banner6btn);
    $templatecontext['banner6btnlnk'] = $theme->settings->banner6btnlnk;
    $templatecontext['banner6vdolnk'] = $theme->settings->banner6vdolnk;
    $templatecontext['banner6mb'] = $theme->settings->banner6mb;
    return $templatecontext;
}
