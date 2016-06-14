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
 * General exception thrown by the {@link \core\update\checker} class
 */
class checker_exception extends \moodle_exception {

    /**
     * @param string $errorcode exception description identifier
     * @param mixed $debuginfo debugging data to display
     */
    public function __construct($errorcode, $debuginfo=null) {
        parent::__construct($errorcode, 'core_plugin', '', null, print_r($debuginfo, true));
    }
}
