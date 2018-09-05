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
 * Excel data format writer
 *
 * @package    dataformat_excel
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformat_excel;

defined('MOODLE_INTERNAL') || die();

/**
 * Excel data format writer
 *
 * @package    dataformat_excel
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends \core\dataformat\spout_base {

    /** @var $mimetype */
    protected $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

    /** @var $extension */
    protected $extension = ".xlsx";

    /** @var $spouttype */
    protected $spouttype = \Box\Spout\Common\Type::XLSX;

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

        // Replace any characters in the name that Excel cannot cope with.
        $title = strtr(trim($title, "'"), '[]*/\?:', '       ');
        // Shorten the title if necessary.
        $title = \core_text::substr($title, 0, 31);
        // After the substr, we might now have a single quote on the end.
        $title = trim($title, "'");

        $this->sheettitle = $title;
    }
}

