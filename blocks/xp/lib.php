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
 * Block XP lib.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_xp\di;

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $bi Block instance record.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function block_xp_pluginfile($course, $bi, $context, $filearea, $args, $forcedownload, array $options = []) {
    $fs = di::get('file_server');
    if ($fs instanceof \block_xp\local\file\block_file_server) {
        $fs->serve_block_file($course, $bi, $context, $filearea, $args, $forcedownload, $options);
    }
}

/**
 * Navbar injection.
 *
 * @param \renderer_base $output The global renderer.
 */
function block_xp_render_navbar_output($output) {
    global $USER, $COURSE, $PAGE;

    // Never applies if not logged in.
    if (!$USER->id || isguestuser()) {
        return '';
    }

    $config = di::get('config');
    if (!$config->get('navbardisplay')) {
        return '';
    }

    // If we display per course, we require to be in a course, but not the frontpage.
    $sitewide = $config->get('context') == CONTEXT_SYSTEM;
    if (!$sitewide && (!$PAGE->context->get_course_context(false) || $COURSE->id == SITEID)) {
        return '';
    }

    // Check if enabled.
    $world = di::get('course_world_factory')->get_world($COURSE->id);
    if (!$world->get_config()->get('enabled')) {
        return;
    }

    // Check that the user can see the content.
    $accessperms = $world->get_access_permissions();
    if (!$accessperms->can_access()) {
        return '';
    }

    $renderer = di::get('renderer');
    return $renderer->navbar_widget($world, $world->get_store()->get_state($USER->id));
}

/**
 * Get user preferences.
 *
 * @return array
 */
function block_xp_user_preferences() {
    return [
        'block_xp_notices' => [
            'type' => PARAM_BOOL,
            'permissioncallback' => function($user) {
                global $USER;
                return $user->id == $USER->id;
            },
        ],
        'block_xp_notice_quest' => [
            'type' => PARAM_BOOL,
            'permissioncallback' => function($user) {
                global $USER;
                return $user->id == $USER->id;
            },
        ],
    ];
}
