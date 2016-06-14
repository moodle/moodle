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
 * Common Spout class for dataformat.
 *
 * @package    core
 * @subpackage dataformat
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\dataformat;

/**
 * Common Spout class for dataformat.
 *
 * @package    core
 * @subpackage dataformat
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class spout_base extends \core\dataformat\base {

    /** @var $spouttype */
    protected $spouttype = '';

    /** @var $writer */
    protected $writer;

    /** @var $sheettitle */
    protected $sheettitle;

    /**
     * Output file headers to initialise the download of the file.
     */
    public function send_http_headers() {
        $this->writer = \Box\Spout\Writer\WriterFactory::create($this->spouttype);
        $filename = $this->filename . $this->get_extension();
        $this->writer->openToBrowser($filename);
        if ($this->sheettitle && $this->writer instanceof \Box\Spout\Writer\AbstractMultiSheetsWriter) {
            $sheet = $this->writer->getCurrentSheet();
            $sheet->setName($this->sheettitle);
        }
    }

    /**
     * Set the title of the worksheet inside a spreadsheet
     *
     * For some formats this will be ignored.
     *
     * @param string $title
     */
    public function set_sheettitle($title) {
        if (!$title) {
            return;
        }
        $this->sheettitle = $title;
    }

    /**
     * Write the start of the format
     *
     * @param array $columns
     */
    public function write_header($columns) {
        $this->writer->addRow(array_values((array)$columns));
    }

    /**
     * Write a single record
     *
     * @param object $record
     * @param int $rownum
     */
    public function write_record($record, $rownum) {
        $this->writer->addRow(array_values((array)$record));
    }

    /**
     * Write the end of the format
     *
     * @param array $columns
     */
    public function write_footer($columns) {
        $this->writer->close();
        $this->writer = null;
    }

}
