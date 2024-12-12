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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/format_theunittest.php');

/**
 * Fixture for fake course format testing course format API.
 *
 * @package    core_courseformat
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_theunittestdelete extends format_theunittest {

    /**
     * Definitions of the additional options that format uses
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = parent::course_format_options(true);
            $courseformatoptionsadditional = [
                'can_delete_sections' => [
                    'default' => false,
                    'type' => PARAM_BOOL,
                ],
            ];
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsadditional);
        }
        return $courseformatoptions;
    }

    /**
     * Whether this format allows to delete sections
     *
     * Here for test purpose we just can delete one section every two sections
     *
     * Do not call this function directly, instead use course_can_delete_section()
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return $this->get_format_options()['can_delete_sections'] ?? false;
    }
}
