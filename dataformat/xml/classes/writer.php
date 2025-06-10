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

namespace dataformat_xml;

use stdClass;
use core_text;
use core\dataformat\base;

/**
 * XML data format writer
 *
 * @package    dataformat_xml
 * @copyright  2021 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends base {

    /** @var string $mimetype */
    public $mimetype = "text/xml";

    /** @var string $extension */
    public $extension = ".xml";

    /** @var string[] $columns */
    protected $columns = [];

    /**
     * Start XML output
     */
    public function start_output(): void {
        echo '<?xml version="1.0" encoding="UTF-8"?>
            <records>';
    }

    /**
     * After output has begun, store normalized column names for use later
     *
     * @param string[] $columns
     */
    public function start_sheet($columns): void {
        $this->columns = array_map(static function(string $column): string {
            return core_text::strtolower(clean_param($column, PARAM_ALPHANUMEXT));
        }, $columns);
    }

    /**
     * Ensure we produce correctly formed XML content by encoding characters appropriately
     *
     * @param string $string
     * @return string
     */
    private static function xml_special_chars(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES | ENT_XML1);
    }

    /**
     * Write a single record
     *
     * @param array|stdClass $record
     * @param int $rownum
     */
    public function write_record($record, $rownum): void {
        $record = array_combine($this->columns, (array) $record);

        echo "<record rowNum=\"{$rownum}\">";

        foreach ($this->columns as $column) {
            $columndata = self::xml_special_chars((string) $record[$column]);

            echo "\t<{$column}>{$columndata}</{$column}>\n";
        }

        echo "</record>\n";
    }

    /**
     * Finish XML output
     */
    public function close_output(): void {
        echo '</records>';
    }
}
