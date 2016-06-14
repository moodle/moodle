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
 * JSON data format writer
 *
 * @package    dataformat_json
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformat_json;

defined('MOODLE_INTERNAL') || die();

/**
 * JSON data format writer
 *
 * @package    dataformat_json
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends \core\dataformat\base {

    /** @var $mimetype */
    public $mimetype = "application/json";

    /** @var $extension */
    public $extension = ".json";

    /**
     * Write the start of the format
     *
     * @param array $columns
     */
    public function write_header($columns) {
        echo "[";
    }

    /**
     * Write a single record
     *
     * @param array $record
     * @param int $rownum
     */
    public function write_record($record, $rownum) {
        if ($rownum) {
            echo ",";
        }
        echo json_encode($record);
    }

    /**
     * Write the end of the format
     *
     * @param array $columns
     */
    public function write_footer($columns) {
        echo "]";
    }

}
