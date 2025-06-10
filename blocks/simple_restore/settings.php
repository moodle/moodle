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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Restore general settings.
if ($ADMIN->fulltree) {

    // Course Settings for restore.
    $generalsettings = array(
        'enrol_migratetomanual' => 0,
        'users' => 0,
        'user_files' => 0,
        'role_assignments' => 0,
        'activities' => 1,
        'blocks' => 1,
        'filters' => 1,
        'comments' => 0,
        'userscompletion' => 0,
        'logs' => 0,
        'grade_histories' => 0
    );

    $highlevelsettings = array(
        'keep_roles_and_enrolments' => 0,
        'keep_groups_and_groupings' => 0,
        'overwrite_conf' => 1
    );

    $modules = $DB->get_records_menu('modules', null, 'id, name') +
            array(0 => 'section');

    $producer = function ($type, $default) {
        return function ($module) use ($type, $default) {
            return array("{$module}_{$type}" => $default);
        };
    };

    $flatmap = function ($in, $module) use ($producer) {
        $included = $producer('included', 1);
        $userinfo = $producer('userinfo', 0);
        return $in + $included($module) + $userinfo($module);
    };

    // Flat mapped the producer defaults with original modules.
    $coursesettings = array_reduce($modules, $flatmap, array());

    // Appropriate keys.
    $srk = function ($key) {
        return "simple_restore/{$key}";
    };

    $srs = function ($k, $a=null) {
        return get_string($k, 'block_simple_restore', $a);
    };

    $itersettings = function ($chosensettings) use ($settings, $srk, $srs) {
        foreach ($chosensettings as $name => $default) {
            $str = $srs($name);
            $settings->add(
                new admin_setting_configcheckbox($srk($name), $str, $str, $default)
            );
        }
    };

    // Archive server mode toggle.
    $settings->add(
            new admin_setting_configcheckbox(
                    $srk('is_archive_server'),
                    $srs('is_archive_server'),
                    $srs('is_archive_server_desc'),
                    0,
                    1,
                    0)
            );

    // Start building the Admin screen.
    $settings->add(
        new admin_setting_heading(
            $srk('general'), $srs('general'), $srs('general_desc')
        )
    );

    $itersettings($generalsettings);

    $settings->add(
        new admin_setting_heading(
            $srk('course'), $srs('course'), $srs('course_desc')
        )
    );


    $itersettings($highlevelsettings);

    $settings->add(
        new admin_setting_heading(
            $srk('module'), $srs('module'), $srs('module_desc')
        )
    );

    foreach ($coursesettings as $name => $default) {
        $data = explode('_', $name);
        $type = array_pop($data);
        $module = implode('_', $data);
        if ($module == 'section') {
            $modulename = $srs('section');
        } else {
            $modulename = get_string('pluginname', 'mod_'.$module);
        }
        $str = $srs('restore_'.$type, $modulename);
        $settings->add(
            new admin_setting_configcheckbox($srk($name), $str, $str, $default)
        );
    }

    // ----------------------------------------------------------------
    // Async Settings Title.
    $settings->add(
        new admin_setting_heading(
            'block_simple_restore_async_title',
            get_string('async_title', 'block_simple_restore'),
            ''
        )
    );

    // Remote student role id.
    $settings->add(
        new admin_setting_configcheckbox(
            'simple_restore/async_toggle',
            get_string('async_toggle_title', 'block_simple_restore'),
            get_string('async_toggle_desc', 'block_simple_restore'),
            0 // Default.
        )
    );
}
