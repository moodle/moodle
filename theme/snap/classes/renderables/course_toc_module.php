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
 * Course toc module search
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;

class course_toc_module implements \renderable {

    /**
     * @var string module name
     */
    public $modname;

    /**
     * @var boolean is this module visible to the current user?
     */
    public $uservisible;

    /**
     * @var \moodle_url url to module icon
     */
    public $iconurl;

    /**
     * @var string formatted name of module
     */
    public $formattedname;

    /**
     * @var string - any screen reader info to display
     */
    public $srinfo;

    /**
     * @var string - hash bang #section-x&module-x
     */
    public $url;

    /**
     * @var int - course module id
     */
    public $cmid;

}
