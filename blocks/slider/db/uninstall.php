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
 * Simple slider block for Moodle
 *
 * @package   block_slider
 * @copyright 2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This is called at the beginning of the uninstallation process to give the module
 * a chance to clean-up its hacks, bits etc. where possible.
 *
 * @return bool true if success
 */
function block_slider_uninstall() {
    global $DB;

    // Delete all images.
    if ($slides = $DB->get_records('slider_slides')) {
        foreach ($slides as $slide) {
            $fs = get_file_storage();
            $context = context_block::instance($slide->sliderid);
            if ($file = $fs->get_file($context->id, 'block_slider', 'slider_slides', $slide->id, '/', $slide->slide_image)) {
                if ($file->delete()) {
                    mtrace("File {$slide->slide_image} deleted");
                }
            }
        }
    }

    return true;
}