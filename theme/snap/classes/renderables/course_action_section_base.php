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
 * Base class for section actions.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;
use moodle_url;
use section_info;

abstract class course_action_section_base implements \renderable, \templatable {

    use trait_exportable;

    /**
     * @var string
     */
    public $title;

    /**
     * @var moodle_url
     */
    public $url;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $ariapressed;

    /**
     * @var string
     */
    public $arialabel;

    /**
     * Variable to know if the action is inside a dropdown menu.
     * @var string
     */
    public $isinmenu;

    abstract public function __construct($course, section_info $section, $onsectionpage = false);

}
