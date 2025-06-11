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

namespace core_table;

use flexible_table;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("{$CFG->libdir}/tablelib.php");

/**
 * The table base export format.
 *
 * @package   core_table
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_export_format {
    /**
     * @var flexible_table or child class reference pointing to table class object from which to export data.
     */
    public $table;

    /**
     * @var bool output started. Keeps track of whether any output has been started yet.
     */
    public $documentstarted = false;

    /**
     * Constructor.
     *
     * @param flexible_table $table
     */
    public function __construct(&$table) {
        $this->table =& $table;
    }

    public function set_table(&$table) {
        $this->table =& $table;
    }

    public function add_data($row) {
        return false;
    }

    public function add_seperator() {
        return false;
    }

    public function document_started() {
        return $this->documentstarted;
    }

    /**
     * Format the text.
     *
     * Given text in a variety of format codings, this function returns
     * the text as safe HTML or as plain text dependent on what is appropriate
     * for the download format. The default removes all tags.
     *
     * @param string $text
     * @param int $format
     * @param null|array $options
     * @param null|int $courseid
     */
    public function format_text($text, $format = FORMAT_MOODLE, $options = null, $courseid = null) {
        // Use some whitespace to indicate where there was some line spacing.
        $text = str_replace(['</p>', "\n", "\r"], '   ', $text);
        return html_entity_decode(strip_tags($text), ENT_COMPAT);
    }

    /**
     * Format a row of data, removing HTML tags and entities from each of the cells
     *
     * @param array $row
     * @return array
     */
    public function format_data(array $row): array {
        return array_map([$this, 'format_text'], $row);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(base_export_format::class, \table_default_export_format_parent::class);
