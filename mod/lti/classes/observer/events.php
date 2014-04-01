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
 * Defines internal events that ltisource
 * plugins can subscribe to.
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */

namespace mod_lti\observer;

/**
 * These are all of the event names
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
final class events {
    /**
     * This is thrown before an LTI launch
     *
     * The event listener will recevie an instance
     * of \mod_lti\observer\before_launch_event
     */
    const BEFORE_LAUNCH = 'before.launch';
}