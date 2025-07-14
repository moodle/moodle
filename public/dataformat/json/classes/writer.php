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

use core_text;

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

    /** @var $sheetstarted */
    public $sheetstarted = false;

    /** @var $sheetdatadded */
    public $sheetdatadded = false;

    /** @var string[] $columns */
    protected $columns = [];

    /**
     * Write the start of the file.
     */
    public function start_output() {
        echo "[";
    }

    /**
     * Write the start of the sheet we will be adding data to.
     *
     * @param array $columns
     */
    public function start_sheet($columns) {
        $this->columns = array_map(function($column) {
            return core_text::strtolower(clean_param($column, PARAM_ALPHANUMEXT));
        }, $columns);

        if ($this->sheetstarted) {
            echo ",";
        } else {
            $this->sheetstarted = true;
        }
        $this->sheetdatadded = false;
        echo "[";
    }

    /**
     * Write a single record
     *
     * @param array $record
     * @param int $rownum
     */
    public function write_record($record, $rownum) {
        if ($this->sheetdatadded) {
            echo ",";
        }

        // Ensure our record is keyed by column names, rather than numerically.
        $record = array_combine($this->columns, (array) $record);
        echo json_encode($this->format_record($record), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $this->sheetdatadded = true;
    }

    /**
     * Write the end of the sheet containing the data.
     *
     * @param array $columns
     */
    public function close_sheet($columns) {
        echo "]";
    }

    /**
     * Write the end of the file.
     */
    public function close_output() {
        echo "]";
    }
}
