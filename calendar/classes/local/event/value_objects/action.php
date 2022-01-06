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
 * Class representing an action a user should take.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\value_objects;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\entities\action_interface;

/**
 * Class representing an action a user should take
 *
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action implements action_interface {
    /**
     * @var string $name The action's name.
     */
    protected $name;

    /**
     * @var \moodle_url $url The action's URL.
     */
    protected $url;

    /**
     * @var int $itemcount How many items there are to action.
     */
    protected $itemcount;

    /**
     * @var bool $actionable Whether or not the event is currently actionable.
     */
    protected $actionable;

    /**
     * Constructor.
     *
     * @param string      $name       The action's name.
     * @param \moodle_url $url        The action's URL.
     * @param int         $itemcount  How many items there are to action.
     * @param bool        $actionable Whether or not the event is currently actionable.
     */
    public function __construct(
        $name,
        \moodle_url $url,
        $itemcount,
        $actionable
    ) {
        $this->name = $name;
        $this->url = $url;
        $this->itemcount = $itemcount;
        $this->actionable = $actionable;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_url() {
        return $this->url;
    }

    public function get_item_count() {
        return $this->itemcount;
    }

    public function is_actionable() {
        return $this->actionable;
    }
}
