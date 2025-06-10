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
 * This file contains functions used by the admin pages
 *
 * @since Moodle 2.1
 * @package admin
 * @copyright 2011 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function admin_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $array = array(
        'admin-*' => get_string('page-admin-x', 'pagetype'),
        $pagetype => get_string('page-admin-current', 'pagetype')
    );
    return $array;
}

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function core_admin_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;

    if (in_array($filearea, ['logo', 'logocompact', 'favicon'])) {
        $size = array_shift($args); // The path hides the size.
        $itemid = clean_param(array_shift($args), PARAM_INT);
        $filename = clean_param(array_shift($args), PARAM_FILE);
        $themerev = theme_get_revision();
        if ($themerev <= 0) {
            // Normalise to 0 as -1 doesn't place well with paths.
             $themerev = 0;
        }

        // Extract the requested width and height.
        $maxwidth = 0;
        $maxheight = 0;
        if (preg_match('/^\d+x\d+$/', $size)) {
            list($maxwidth, $maxheight) = explode('x', $size);
            $maxwidth = clean_param($maxwidth, PARAM_INT);
            $maxheight = clean_param($maxheight, PARAM_INT);
        }

        $lifetime = 0;
        if ($itemid > 0 && $themerev == $itemid) {
            // The itemid is $CFG->themerev, when 0 or less no caching. Also no caching when they don't match.
            $lifetime = DAYSECS * 60;
        }

        // Anyone, including guests and non-logged in users, can view the logos.
        $options = ['cacheability' => 'public'];

        // Check if we've got a cached file to return. When lifetime is 0 then we don't want to cached one.
        $candidate = $CFG->localcachedir . "/core_admin/$themerev/$filearea/{$maxwidth}x{$maxheight}/$filename";
        if (file_exists($candidate) && $lifetime > 0) {
            send_file($candidate, $filename, $lifetime, 0, false, false, '', false, $options);
        }

        // Find the original file.
        $fs = get_file_storage();
        $filepath = "/{$context->id}/core_admin/{$filearea}/0/{$filename}";
        if (!$file = $fs->get_file_by_hash(sha1($filepath))) {
            send_file_not_found();
        }

        // Check whether width/height are specified, and we can resize the image (some types such as ICO cannot be resized).
        if (($maxwidth === 0 && $maxheight === 0) ||
                !$filedata = $file->resize_image($maxwidth, $maxheight)) {

            if ($lifetime) {
                file_safe_save_content($file->get_content(), $candidate);
            }
            send_stored_file($file, $lifetime, 0, false, $options);
        }

        // If we don't want to cached the file, serve now and quit.
        if (!$lifetime) {
            send_content_uncached($filedata, $filename);
        }

        // Save, serve and quit.
        file_safe_save_content($filedata, $candidate);
        send_file($candidate, $filename, $lifetime, 0, false, false, '', false, $options);
    }

    send_file_not_found();
}
