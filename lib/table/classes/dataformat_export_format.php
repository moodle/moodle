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

use core\exception\coding_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("{$CFG->libdir}/tablelib.php");

use core\dataformat;

/**
 * Dataformat exporter
 *
 * @package    core_table
 * @subpackage tablelib
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformat_export_format extends base_export_format {
    /** @var \core\dataformat\base $dataformat */
    protected $dataformat;

    /** @var int $rownum */
    protected $rownum = 0;

    /** @var array $columns */
    protected $columns;

    /**
     * Constructor
     *
     * @param string $table An sql table
     * @param string $dataformat type of dataformat for export
     */
    public function __construct(&$table, $dataformat) {
        parent::__construct($table);

        if (ob_get_length()) {
            throw new coding_exception("Output can not be buffered before instantiating table_dataformat_export_format");
        }

        $this->dataformat = dataformat::get_format_instance($dataformat);

        // The dataformat export time to first byte could take a while to generate...
        set_time_limit(0);

        // Close the session so that the users other tabs in the same session are not blocked.
        \core\session\manager::write_close();
    }

    /**
     * Whether the current dataformat supports export of HTML
     *
     * @return bool
     */
    public function supports_html(): bool {
        return $this->dataformat->supports_html();
    }

    /**
     * Start document
     *
     * @param string $filename
     * @param string $sheettitle
     */
    public function start_document($filename, $sheettitle) {
        $this->documentstarted = true;
        $this->dataformat->set_filename($filename);
        $this->dataformat->send_http_headers();
        $this->dataformat->set_sheettitle($sheettitle);
        $this->dataformat->start_output();
    }

    /**
     * Start export
     *
     * @param string $sheettitle optional spreadsheet worksheet title
     */
    public function start_table($sheettitle) {
        $this->dataformat->set_sheettitle($sheettitle);
    }

    /**
     * Output headers
     *
     * @param array $headers
     */
    public function output_headers($headers) {
        $this->columns = $this->format_data($headers);
        if (method_exists($this->dataformat, 'write_header')) {
            error_log('The function write_header() does not support multiple sheets. In order to support multiple sheets you ' .
                'must implement start_output() and start_sheet() and remove write_header() in your dataformat.');
            $this->dataformat->write_header($this->columns);
        } else {
            $this->dataformat->start_sheet($this->columns);
        }
    }

    /**
     * Add a row of data
     *
     * @param array $row One record of data
     */
    public function add_data($row) {
        if (!$this->supports_html()) {
            $row = $this->format_data($row);
        }

        $this->dataformat->write_record($row, $this->rownum++);
        return true;
    }

    /**
     * Finish export
     */
    public function finish_table() {
        if (method_exists($this->dataformat, 'write_footer')) {
            error_log('The function write_footer() does not support multiple sheets. In order to support multiple sheets you ' .
                'must implement close_sheet() and close_output() and remove write_footer() in your dataformat.');
            $this->dataformat->write_footer($this->columns);
        } else {
            $this->dataformat->close_sheet($this->columns);
        }
    }

    /**
     * Finish download
     */
    public function finish_document() {
        $this->dataformat->close_output();
        exit();
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(dataformat_export_format::class, \table_dataformat_export_format::class);
