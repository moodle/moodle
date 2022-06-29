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
 * Settings that allow turning on and off recordrtc features
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Needed for constants.
require_once($CFG->dirroot . '/lib/editor/atto/plugins/recordrtc/lib.php');

$ADMIN->add('editoratto', new admin_category('atto_recordrtc', new lang_string('pluginname', 'atto_recordrtc')));

if ($ADMIN->fulltree) {
    // Types allowed.
    $options = array(
        'both' => new lang_string('audioandvideo', 'atto_recordrtc'),
        'audio' => new lang_string('onlyaudio', 'atto_recordrtc'),
        'video' => new lang_string('onlyvideo', 'atto_recordrtc')
    );
    $name = get_string('allowedtypes', 'atto_recordrtc');
    $desc = get_string('allowedtypes_desc', 'atto_recordrtc');
    $default = 'both';
    $setting = new admin_setting_configselect('atto_recordrtc/allowedtypes', $name, $desc, $default, $options);
    $settings->add($setting);

    // Audio bitrate.
    $name = get_string('audiobitrate', 'atto_recordrtc');
    $desc = get_string('audiobitrate_desc', 'atto_recordrtc');
    $default = '128000';
    $setting = new admin_setting_configtext('atto_recordrtc/audiobitrate', $name, $desc, $default, PARAM_INT, 8);
    $settings->add($setting);

    // Video bitrate.
    $name = get_string('videobitrate', 'atto_recordrtc');
    $desc = get_string('videobitrate_desc', 'atto_recordrtc');
    $default = '2500000';
    $setting = new admin_setting_configtext('atto_recordrtc/videobitrate', $name, $desc, $default, PARAM_INT, 8);
    $settings->add($setting);

    // Audio recording time limit.
    $name = get_string('audiotimelimit', 'atto_recordrtc');
    $desc = get_string('audiotimelimit_desc', 'atto_recordrtc');
    // Validate audiotimelimit greater than 0.
    $setting = new admin_setting_configduration('atto_recordrtc/audiotimelimit', $name, $desc, DEFAULT_TIME_LIMIT);
    $setting->set_validate_function(function(int $value): string {
        if ($value <= 0) {
            return get_string('timelimitwarning', 'atto_recordrtc');
        }
        return '';
    });
    $settings->add($setting);

    // Video recording time limit.
    $name = get_string('videotimelimit', 'atto_recordrtc');
    $desc = get_string('videotimelimit_desc', 'atto_recordrtc');
    // Validate videotimelimit greater than 0.
    $setting = new admin_setting_configduration('atto_recordrtc/videotimelimit', $name, $desc, DEFAULT_TIME_LIMIT);
    $setting->set_validate_function(function(int $value): string {
        if ($value <= 0) {
            return get_string('timelimitwarning', 'atto_recordrtc');
        }
        return '';
    });
    $settings->add($setting);
}
