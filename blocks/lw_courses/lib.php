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
 * Plugin file lib for default images.
 *
 * @package    block_lw_courses
 * @copyright  2017 Mathew May <mathewm@hotmail.co.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Function to save the image in the instance of the block
 *
 *
 * @param object $course the course that aint used
 * @param string $birecord unsure
 * @param int $context what context to save the file in
 * @param int $filearea where to save the file
 * @param array $args arguments
 * @param boolean $forcedownload flag
 * @param array $options different options
 */
function block_lw_courses_pluginfile($course, $birecord, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';

    if (!$file = $fs->get_file($context->id, 'block_lw_courses', 'courseimagedefault', 0, $filepath, $filename)) {
        send_file_not_found();
    }

    send_stored_file($file, null, 0, $forcedownload, array());
}