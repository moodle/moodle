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
 * Post installation and migration code.
 *
 * This file:
 * - creates a sample image in the database for use in the photo library
 *
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * To be executed on install.
 * See https://docs.moodle.org/dev/Upgrade_API#install.php.
 * @throws dml_exception
 * @throws file_exception
 * @throws stored_file_creation_exception
 */
function xmldb_format_tiles_install() {
    global $CFG;

    // Store the sample photo tile image in the database.
    $fs = get_file_storage();
    $filerecord = format_tiles\tile_photo::file_api_params();
    $filerecord['contextid'] = \context_system::instance()->id;
    $filerecord['itemid'] = 0;
    $filerecord['mimetype'] = 'image/jpeg';
    $filerecord['filename'] = 'sample_image.jpg';
    $path = $CFG->dirroot . '/course/format/tiles/';
    $fs->create_file_from_pathname($filerecord, $path . $filerecord['filename']);
}
