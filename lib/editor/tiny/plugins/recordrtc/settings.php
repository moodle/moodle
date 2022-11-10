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
 * @package    tiny_recordrtc
 * @copyright  2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Needed for constants.
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/recordrtc/classes/plugininfo.php');

$ADMIN->add('editortiny', new admin_category('tiny_recordrtc', new lang_string('pluginname', 'tiny_recordrtc')));

if ($ADMIN->fulltree) {
    $defaulttimelimit = 120;

    $url = parse_url($CFG->wwwroot);
    $hostname = parse_url($CFG->wwwroot, PHP_URL_HOST);
    $isvalid = in_array($hostname, ['localhost', '127.0.0.1', '::1']);
    $isvalid = $isvalid || preg_match("/^.*\.localhost$/", $hostname);

    if (!$isvalid && $url['scheme'] !== 'https') {
        $warning = html_writer::div(get_string('insecurealert', 'tiny_recordrtc'), 'box py-3 generalbox alert alert-danger');
        $setting = new admin_setting_description('tiny_recordrtc/warning', null, $warning);
        $settings->add($setting);
    }

    // Types allowed.
    $options = [
        'both' => new lang_string('audioandvideo', 'tiny_recordrtc'),
        'audio' => new lang_string('onlyaudio', 'tiny_recordrtc'),
        'video' => new lang_string('onlyvideo', 'tiny_recordrtc')
    ];
    $name = get_string('allowedtypes', 'tiny_recordrtc');
    $desc = get_string('allowedtypes_desc', 'tiny_recordrtc');
    $default = 'both';
    $setting = new admin_setting_configselect('tiny_recordrtc/allowedtypes', $name, $desc, $default, $options);
    $settings->add($setting);

    // Audio bitrate.
    $name = get_string('audiobitrate', 'tiny_recordrtc');
    $desc = get_string('audiobitrate_desc', 'tiny_recordrtc');
    $default = '128000';
    $setting = new admin_setting_configtext('tiny_recordrtc/audiobitrate', $name, $desc, $default, PARAM_INT, 8);
    $settings->add($setting);

    // Video bitrate.
    $name = get_string('videobitrate', 'tiny_recordrtc');
    $desc = get_string('videobitrate_desc', 'tiny_recordrtc');
    $default = '2500000';
    $setting = new admin_setting_configtext('tiny_recordrtc/videobitrate', $name, $desc, $default, PARAM_INT, 8);
    $settings->add($setting);

    // Audio recording time limit.
    $name = get_string('audiotimelimit', 'tiny_recordrtc');
    $desc = get_string('audiotimelimit_desc', 'tiny_recordrtc');
    // Validate audiotimelimit greater than 0.
    $setting = new admin_setting_configduration('tiny_recordrtc/audiotimelimit', $name, $desc, $defaulttimelimit);
    $setting->set_validate_function(function(int $value): string {
        if ($value <= 0) {
            return get_string('timelimitwarning', 'tiny_recordrtc');
        }
        return '';
    });
    $settings->add($setting);

    // Video recording time limit.
    $name = get_string('videotimelimit', 'tiny_recordrtc');
    $desc = get_string('videotimelimit_desc', 'tiny_recordrtc');
    // Validate videotimelimit greater than 0.
    $setting = new admin_setting_configduration('tiny_recordrtc/videotimelimit', $name, $desc, $defaulttimelimit);
    $setting->set_validate_function(function(int $value): string {
        if ($value <= 0) {
            return get_string('timelimitwarning', 'tiny_recordrtc');
        }
        return '';
    });
    $settings->add($setting);
}
