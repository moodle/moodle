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
 * Course toc section
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;

class course_toc_chapter implements \renderable {

    /**
     * @var bool
     */
    public $outputlink;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $classes;

    /**
     * @var bool
     */
    public $iscurrent;

    /**
     * @var bool
     */
    public $isweeksformat;

    /**
     * @var string
     */
    public $availabilityclass;

    /**
     * @var string
     */
    public $availabilitystatus;

    /**
     * @var course_toc_progress
     */
    public $progress;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $section;

    /**
     * @var int
     */
    public $sectionid;

}
