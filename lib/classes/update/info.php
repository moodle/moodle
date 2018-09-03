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
 * Defines classes used for updates.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\update;

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the structure of objects returned by {@link \core\update\checker::get_update_info()}
 */
class info {

    /** @var string frankenstyle component name */
    public $component;
    /** @var int the available version of the component */
    public $version;
    /** @var string|null optional release name */
    public $release = null;
    /** @var int|null optional maturity info, eg {@link MATURITY_STABLE} */
    public $maturity = null;
    /** @var string|null optional URL of a page with more info about the update */
    public $url = null;
    /** @var string|null optional URL of a ZIP package that can be downloaded and installed */
    public $download = null;
    /** @var string|null of self::download is set, then this must be the MD5 hash of the ZIP */
    public $downloadmd5 = null;

    /**
     * Creates new instance of the class
     *
     * The $info array must provide at least the 'version' value and optionally all other
     * values to populate the object's properties.
     *
     * @param string $name the frankenstyle component name
     * @param array $info associative array with other properties
     */
    public function __construct($name, array $info) {
        $this->component = $name;
        foreach ($info as $k => $v) {
            if (property_exists('\core\update\info', $k) and $k != 'component') {
                $this->$k = $v;
            }
        }
    }
}
