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
 * Provider Row.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_oembed\db;

defined('MOODLE_INTERNAL') || die();

class providerrow extends abstract_dbrow{
    /**
     * @var int id
     */
    public $id;

    /**
     * @var str provider name
     */
    public $providername;

    /**
     * @var str provider url
     */
    public $providerurl;

    /**
     * @var str end points
     */
    public $endpoints;

    /**
     * @var str source
     */
    public $source;

    /**
     * @var bool enabled status
     */
    public $enabled;

    /**
     * @var int time created
     */
    public $timecreated;

    /**
     * @var int time modified
     */
    public $timemodified;

}
