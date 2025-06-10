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
 * CSV import of ProctorU coupon codes and GUILD mappings.
 *
 * @package   block_pu
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2021 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

require_once($CFG->dirroot . '/blocks/pu/classes/importhelpers.php');

/**
 * Building the class for the task to be run during scheduled tasks.
 */
class pu {
    /**
     * Master function for importing GUILD mapping data.
     *
     * @return boolean
     */
    public function run_import_guildmaps() {
        // Do the nasty.
        pu_import_helper::block_pu_guildimporter();
        return true;
    }

    /**
     * Master function for emailing minimum threshold exceptions.
     *
     * @return @book
     */
    public function run_pu_codeslow() {
        // Do the nasty.
        pu_import_helper::block_pu_codeslow();
        return true;
    }

    /**
     * Master function for importing ProctorU coupon codes.
     *
     * @return boolean
     */
    public function run_import_pucodes() {
        // Do the nasty.
        pu_import_helper::block_pu_codeimport();
        return true;
    }

    /**
     * Master function for cleaning up orphaned codes and mappings.
     *
     * @return boolean
     */
    public function run_import_unmap() {
        // Do the nasty.
        pu_import_helper::block_pu_code_unmap();
        return true;
    }
}
