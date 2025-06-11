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
 * Navigation link.
 * @package   theme_snap
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;

/**
 * Renderable class for navigation link.
 * @package   theme_snap
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_section_navigation_link implements \renderable {
    /**
     * @var int section number
     */
    public $section;

    /**
     * @var string additional classes for link
     */
    public $classes;

    /**
     * @var string section title
     */
    public $title;

    /**
     * course_section_navigation_link constructor.
     * @param int $section section number
     * @param string $classes additional classes for link
     * @param string $title section title
     */
    public function __construct($section, $classes, $title) {
        $this->section = $section;
        $this->classes = $classes;
        $this->title = $title;
    }
}
