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
 * Customcert image element upgrade code.
 *
 * @package    customcertelement_image
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Customcert image element upgrade code.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_customcertelement_image_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2016120501) {
        // Go through each 'image' element and update the file stored information.
        if ($images = $DB->get_records_select('customcert_elements', $DB->sql_compare_text('element') . ' = \'image\'')) {
            // Create a file storage instance we are going to use to create pathname hashes.
            $fs = get_file_storage();
            // Go through and update the details.
            foreach ($images as $image) {
                // Get the current data we have stored for this element.
                $elementinfo = json_decode($image->data);
                if ($file = $fs->get_file_by_hash($elementinfo->pathnamehash)) {
                    $arrtostore = [
                        'contextid' => $file->get_contextid(),
                        'filearea' => $file->get_filearea(),
                        'itemid' => $file->get_itemid(),
                        'filepath' => $file->get_filepath(),
                        'filename' => $file->get_filename(),
                        'width' => (int) $elementinfo->width,
                        'height' => (int) $elementinfo->height,
                    ];
                    $arrtostore = json_encode($arrtostore);
                    $DB->set_field('customcert_elements', 'data', $arrtostore,  ['id' => $image->id]);
                }
            }
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2016120501, 'customcertelement', 'image');
    }

    return true;
}
