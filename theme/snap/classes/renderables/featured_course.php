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

namespace theme_snap\renderables;
use moodle_url;

/**
 * Featured course renderable
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featured_course implements \renderable, \templatable {

    use trait_exportable;

    /**
     * @var \moodle_url
     */
    public $url;

    /**
     * @var \moodle_url | null
     */
    public $coverimageurl;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int card number in list
     */
    public $number;

    /**
     * featured_course constructor.
     * @param \moodle_url $url
     * @param string $coverimageurl
     * @param string $title
     * @param int $number
     */
    public function __construct(\moodle_url $url, $coverimageurl, $title, $number) {
        $this->url = $url;
        $this->coverimageurl = $coverimageurl;
        $this->title = $title;
        $this->number = $number;
    }

}
