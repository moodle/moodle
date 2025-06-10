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
 * Library for core hooks.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Prepare for code checker update. Will be removed on INT-17966.
// @codingStandardsIgnoreLine
defined('MOODLE_INTERNAL') || die();

use tool_ally\file_processor,
    tool_ally\local_file,
    tool_ally\cache,
    tool_ally\local_content,
    tool_ally\componentsupport\interfaces\content_sub_tables,
    tool_ally\local,
    tool_ally\logging\logger;
use tool_ally\files_in_use;

/**
 * Callback for after file deleted.
 * @param stdClass $filerecord
 */
function tool_ally_after_file_deleted($filerecord) {
    $fs = get_file_storage();
    $file = $fs->get_file_instance($filerecord);

    if (!local_file::file_validator()->validate_stored_file($file, null, true)) {
        return; // Ally does not support files outside of a course.
    }

    files_in_use::delete_file_record($filerecord->id);
    local_file::queue_file_for_deletion($file);
}

/**
 * Callback for after file created.
 * @param stdClass $filerecord
 */
function tool_ally_after_file_created($filerecord) {
    $fs = get_file_storage();
    $file = $fs->get_file_instance($filerecord);
    file_processor::push_file_update($file);

    cache::instance()->invalidate_file_keys($file);
}

/**
 * Callback for after file updated.
 * @param stdClass $filerecord
 */
function tool_ally_after_file_updated($filerecord) {
    $fs = get_file_storage();
    $file = $fs->get_file_instance($filerecord);
    file_processor::push_file_update($file);

    cache::instance()->invalidate_file_keys($file);
}

/**
 * Callback for pre-module deletion.
 * @param stdClass $cm (cm record from course_modules table)
 * @throws \moodle_exception Probably the cm_info could not be generated.
 */
function tool_ally_pre_course_module_delete(stdClass $cm) {
    try {
        list ($course, $cm) = get_course_and_cm_from_cmid($cm->id, null, $cm->course);
        $component = local_content::component_instance($cm->modname);
        if (!$component || !$component instanceof content_sub_tables) {
            return;
        }
        // Queue for deletion, all records related to the main record for this course module.
        $component->queue_delete_sub_tables($cm);
    } catch (\moodle_exception $mex) {
        // Probably caught when disabled or erratic modules are in tests.
        if (!local::duringtesting()) {
            // Something is wrong with this module.
            $msg = 'logger:cmiderraticpremoddelete';
            $context['_explanation'] = $msg.'_exp';
            $context['_exception'] = $mex;
            logger::get()->error($msg, $context);
        }
    }
}

/**
 * Serves 3rd party js files.
 * (c) Guy Thomas 2018
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function tool_ally_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $pluginpath = __DIR__.'/';

    if ($filearea === 'vendorjs') {
        // Typically CDN fall backs would go in vendorjs.
        $path = $pluginpath.'vendorjs/'.implode('/', $args);
        send_file($path, basename($path));
        return true;
    } else if ($filearea === 'vue') {
        // Vue components.
        $jsfile = array_pop ($args);
        $compdir = basename($jsfile, '.js');
        $umdfile = $compdir.'.umd.js';
        $args[] = $compdir;
        $args[] = 'dist';
        $args[] = $umdfile;
        $path = $pluginpath.'vue/'.implode('/', $args);
        send_file($path, basename($path));
        return true;
    } else {
        die('unsupported file area');
    }
    die;
}

/**
 * Respond to a change in the exclude unused files setting.
 */
function tool_ally_exclude_setting_changed() {
    global $DB;

    // Clear records when this setting changes.
    $DB->delete_records('tool_ally_file_in_use');
}
