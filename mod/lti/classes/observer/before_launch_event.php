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
 * Event for ltisource plugins.
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */

namespace mod_lti\observer;

/**
 * This event occurs prior to an LTI launch.
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
class before_launch_event {
    /**
     * LTI activity instance
     *
     * @var \stdClass
     */
    public $instance;

    /**
     * Launch URL
     *
     * @var string
     */
    public $endpoint;

    /**
     * Launch request parameters
     *
     * @var array
     */
    public $params;

    /**
     * Constructor
     *
     * @param \stdClass $instance
     * @param string $endpoint
     * @param array $params
     */
    public function __construct($instance, $endpoint, array $params) {
        $this->instance = $instance;
        $this->endpoint = $endpoint;
        $this->params   = $params;
    }
}