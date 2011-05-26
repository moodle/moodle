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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    workshopform
 * @subpackage comments
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/workshop/form/comments/db/upgradelib.php');

/**
 * Conversion handler for the comments grading strategy data
 */
class moodle1_workshopform_comments_handler extends moodle1_workshopform_handler {

    /**
     * Converts <ELEMENT> into <workshopform_comments_dimension>
     */
    public function process_legacy_element($data, $raw) {
        // prepare a fake record and re-use the upgrade logic
        $fakerecord = (object)$data;
        $converted = (array)workshopform_comments_upgrade_element($fakerecord, 12345678);
        unset($converted['workshopid']);

        $converted['id'] = $data['id'];
        $this->write_xml('workshopform_comments_dimension', $converted, array('/workshopform_comments_dimension/id'));

        return $converted;
    }
}
