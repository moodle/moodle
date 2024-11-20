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
 * Contains the favourite class, each instance being a representation of a DB row for the 'favourite' table.
 *
 * @package   core_favourites
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains the favourite class, each instance being a representation of a DB row for the 'favourite' table.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class favourite {
    /** @var int $id the id of the favourite.*/
    public $id;

    /** @var string $component the frankenstyle name of the component containing the favourited item. E.g. 'core_course'.*/
    public $component;

    /** @var string $itemtype the type of the item being marked as a favourite. E.g. 'course', 'conversation', etc.*/
    public $itemtype;

    /** @var int $itemid the id of the item that is being marked as a favourite. e.g course->id, conversation->id, etc.*/
    public $itemid;

    /** @var int $contextid the id of the context in which this favourite was created.*/
    public $contextid;

    /** @var int $userid the id of user who owns this favourite.*/
    public $userid;

    /** @var int $ordering the ordering of the favourite within it's favourite area.*/
    public $ordering;

    /** @var int $timecreated the time at which the favourite was created.*/
    public $timecreated;

    /** @var int $timemodified the time at which the last modification of the favourite took place.*/
    public $timemodified;

    /** @var string $uniquekey favourite unique key.*/
    public $uniquekey;

    /**
     * Favourite constructor.
     * @param string $component the frankenstyle name of the component containing the favourited item. E.g. 'core_course'.
     * @param string $itemtype the type of the item being marked as a favourite. E.g. 'course', 'conversation', etc.
     * @param int $itemid the id of the item that is being marked as a favourite. e.g course->id, conversation->id, etc.
     * @param int $contextid the id of the context in which this favourite was created.
     * @param int $userid the id of user who owns this favourite.
     */
    public function __construct(string $component, string $itemtype, int $itemid, int $contextid, int $userid) {
        $this->component = $component;
        $this->itemtype = $itemtype;
        $this->itemid = $itemid;
        $this->contextid = $contextid;
        $this->userid = $userid;
    }
}
