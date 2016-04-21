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
 * ODS data format writer
 *
 * @package    dataformat_ods
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformat_ods;

require_once("$CFG->libdir/spout/src/Spout/Autoloader/autoload.php");

defined('MOODLE_INTERNAL') || die();

/**
 * ODS data format writer
 *
 * @package    dataformat_ods
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends \core\dataformat\spout_base {

    /** @var $mimetype */
    protected $mimetype = "application/vnd.oasis.opendocument.spreadsheet";

    /** @var $extension */
    protected $extension = ".ods";

    /** @var $spouttype */
    protected $spouttype = \Box\Spout\Common\Type::ODS;

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

        // Replace any characters in the name that ODS cannot cope with.
        $title = strtr(trim($title, "'"), '[]*/\?:', '       ');
        // Shorten the title if necessary.
        $title = \core_text::substr($title, 0, 31);
        // After the substr, we might now have a single quote on the end.
        $title = trim($title, "'");

        $this->sheettitle = $title;
    }
}

