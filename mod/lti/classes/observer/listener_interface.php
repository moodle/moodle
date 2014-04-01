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
 * Listener interface for ltisource plugins
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */

namespace mod_lti\observer;

/**
 * This interface allows a class to define the ltisource
 * plugin events that the class would like to subscribe
 * to
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
interface listener_interface {
    /**
     * Register for events
     *
     * Example return:
     *      array('eventname' => 'methodToCall');
     *
     * @return array
     */
    public function get_subscribed_events();
}