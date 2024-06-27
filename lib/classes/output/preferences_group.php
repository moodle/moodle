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

namespace core\output;

/**
 * Represents a group of preferences page link.
 *
 * @package core
 * @category output
 * @copyright 2015 Frédéric Massart - FMCorz.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preferences_group implements renderable {
    /**
     * Title of the group.
     * @var string
     */
    public $title;

    /**
     * Array of navigation_node.
     * @var array
     */
    public $nodes;

    /**
     * Constructor.
     * @param string $title The title.
     * @param array $nodes of navigation_node.
     */
    public function __construct($title, $nodes) {
        $this->title = $title;
        $this->nodes = $nodes;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(preferences_group::class, \preferences_group::class);
