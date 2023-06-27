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
 * Document icon class.
 *
 * @package    core_search
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a document icon.
 *
 * @package    core_search
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document_icon {
    /**
     * Icon file name.
     * @var string
     */
    protected $name;

    /** Icon file component.
     * @var string
     */
    protected $component;

    /**
     * Constructor.
     *
     * @param string $name Icon name.
     * @param string $component Icon component.
     */
    public function __construct($name, $component = 'moodle') {
        $this->name = $name;
        $this->component = $component;
    }

    /**
     * Returns name of the icon file.
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Returns the component of the icon file.
     *
     * @return string
     */
    public function get_component() {
        return $this->component;
    }

}
