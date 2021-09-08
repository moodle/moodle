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
 * H5P Content manager class
 *
 * @package    contenttype_h5p
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace contenttype_h5p;

/**
 * H5P Content manager class
 *
 * @package    contenttype_h5p
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends \core_contentbank\content {

    /**
     * Returns user has access permission for the content itself.
     * If the H5P content-type library is disabled, the user won't have access to it.
     *
     * @return bool     True if content could be accessed. False otherwise.
     */
    public function is_view_allowed(): bool {
        // Force H5P content to be deployed.
        $fileurl = $this->get_file_url();
        if (empty($fileurl)) {
            // This should never happen because H5P contents should have always a file. However, this extra-checked has been added
            // to avoid the contentbank stop working if, for any unkonwn/weird reason, the file doesn't exist.
            return false;
        }

        // Skip capability check when creating the H5P content (because it has been created by trusted users).
        $h5pplayer = new \core_h5p\player($fileurl, new \stdClass(), true, '', true);
        // Flush error messages.
        $h5pplayer->get_messages();

        // Check if the H5P entry has been created and if the main library is enabled.
        $file = $this->get_file();
        if (!empty($file)) {
            $h5p = \core_h5p\api::get_content_from_pathnamehash($file->get_pathnamehash());
            if (empty($h5p)) {
                // If there is no H5P entry for this content, it won't be displayed unless the user has the manageanycontent
                // capability. Reasons for contents without a proper H5P entry in DB:
                // - Invalid H5P package (it won't be never deployed).
                // - Disabled content-type library (it can't be deployed so there is no way to know the mainlibraryid).
                $context = \context::instance_by_id($this->content->contextid);
                if (!has_capability('moodle/contentbank:manageanycontent', $context)) {
                    return false;
                }
            } else if (!\core_h5p\api::is_library_enabled((object) ['id' => $h5p->mainlibraryid])) {
                // If the main library is disabled, it won't be displayed.
                return false;
            }
        }

        return parent::is_view_allowed();
    }

    /**
     * Import a file as a valid content.
     * Before importing the file, this method will check if the file is a valid H5P package. If it's not valid, it will thrown
     * an exception.
     *
     * @throws \file_exception If file operations fail
     * @param \stored_file $file File to store in the content file area.
     * @return \stored_file|null the stored content file or null if the file is discarted.
     */
    public function import_file(\stored_file $file): ?\stored_file {
        // The H5P content can be only deployed if the author of the .h5p file can update libraries or if all the
        // content-type libraries exist, to avoid users without the h5p:updatelibraries capability upload malicious content.
        $onlyupdatelibs = !\core_h5p\helper::can_update_library($file);

        if (!\core_h5p\api::is_valid_package($file, $onlyupdatelibs)) {
            throw new \file_exception('invalidpackage');
        }
        return parent::import_file($file);
    }
}
