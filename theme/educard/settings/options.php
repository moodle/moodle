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
 * Educard theme general settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Educard section.
 *
 */
function theme_educard_section() {
    $theme = theme_config::load('educard');
    $options = [];
    for ($i = 1; $i <= 21; $i++) {
        if ($i < 10 ) {
            $j = "0".$i;
        }
        if ($i > 9 ) {
            $j = $i;
        }
        if (($i == 21 && empty($theme->settings->customblockkey)) ||
            $i == 21 && md5($theme->settings->customblockkey) != educard_getfile_cb()) {
            // Continue.
            false;
        } else {
            $desingcount = get_string('block'.$j.'desingcount', 'theme_educard');
            $comboboxtitle = get_string('block'.$j.'info', 'theme_educard');
            for ($k = 1; $k <= $desingcount; $k++) {
                if ($i < 10 ) {
                    $key = "0".strval($i)."-".strval($k);
                } else {
                    $key = strval($i)."-".strval($k);
                }
                $options[$key] = substr($comboboxtitle, 1, strlen($comboboxtitle) - 1 )."-". $k;
            }
        }
    }
    $options[0] = 'None';
    return $options;
}
/**
 * Front page category.
 *
 */
function theme_educard_all_category() {
    GLOBAL  $DB;
    $options = [];
    $categorys = $DB->get_records('course_categories', ['visible' => 1], 'name ASC');
    foreach ($categorys as $category) {
        $options[$category->id] = $category->name;
    }
    return $options;
}
/**
 * Front page course select.
 *
 */
function theme_educard_all_course() {
    GLOBAL  $DB;
    $options = [];
    $courses = $DB->get_records('course', ['visible' => 1], 'fullname ASC');
    foreach ($courses as $course) {
        if ($course->id != 1) {
            $options[$course->id] = $course->fullname."(". $course->category . ")";
        }
    }
    return $options;
}
/**
 * Front page live date course select.
 *
 */
function theme_educard_customfield_id() {
    GLOBAL  $DB;
    $options = [];
    $options[0] = "None";
    $cffs = $DB->get_records('customfield_field', [], 'id ASC');
    foreach ($cffs as $cff) {
        $options[$cff->id] = $cff->shortname." (". $cff->name . ")";
    }
    return $options;
}
/**
 * Custom block.
 *
 */
function educard_control_cb() {
    $theme = theme_config::load('educard');
    $code = "blocks/block21.php";
    $fs = get_file_storage();
    $file = $fs->get_file(1, 'theme_educard', 'cb', 0, '/', 'cb.txt');
    if ($file) {
        $customblockkey = $file->get_content();
        if (md5($theme->settings->customblockkey) === $customblockkey) {
            return $code;
        } else {
            $file->delete();
            false;
        }
    } else {
        $fileinfo = [
            'contextid' => 1,
            'component' => 'theme_educard',
            'filearea' => 'cb',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'cb.txt', ];
        $fs->create_file_from_url($fileinfo, 'https://themesalmond.com/cb.txt');
        $file = $fs->get_file(1, 'theme_educard', 'cb', 0, '/', 'cb.txt');
        if ($file) {
            $customblockkey = $file->get_content();
            if (md5($theme->settings->customblockkey) === $customblockkey) {
                return $code;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
/**
 * Custom block.
 *
 */
function educard_getfile_cb() {
    $fileinfo = [
        'contextid' => 1,
        'component' => 'theme_educard',
        'filearea' => 'cb',
        'itemid' => 0,
        'filepath' => '/',
        'filename' => 'cb.txt', ];
    $fs = get_file_storage();
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
            $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
    if ($file) {
        $contents = $file->get_content();
        return $contents;
    } else {
        return "";
    }
}
