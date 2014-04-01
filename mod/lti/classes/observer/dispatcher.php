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
 * Dispatches events to ltisource plugin listeners
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */

namespace mod_lti\observer;

/**
 * Event dispatcher
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
class dispatcher {
    /**
     * @var listener_interface[]
     */
    protected $listeners = array();

    /**
     * Add a listener
     *
     * @param listener_interface $listener
     */
    public function add_listener(listener_interface $listener) {
        $this->listeners[] = $listener;
    }

    /**
     * Set a list of listeners
     *
     * @param listener_interface[] $listeners
     */
    public function set_listeners($listeners) {
        $this->listeners = array();
        foreach ($listeners as $listener) {
            $this->add_listener($listener);
        }
    }

    /**
     * Dispatches an event.
     *
     * Very trivial right now.
     *
     * @param string $name Event name
     * @param mixed $event The event object
     * @throws \coding_exception
     */
    public function dispatch($name, $event) {
        foreach ($this->listeners as $listener) {
            $subscribed = $listener->get_subscribed_events();

            if (!array_key_exists($name, $subscribed)) {
                continue;
            }
            $callable = array($listener, $subscribed[$name]);
            if (!is_callable($callable)) {
                throw new \coding_exception(
                    sprintf('The method %s is not callable on %s class', $subscribed[$name], get_class($listener))
                );
            }
            call_user_func($callable, $event);
        }
    }
}