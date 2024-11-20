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

namespace mod_bigbluebuttonbn\local\extension;

use mod_bigbluebuttonbn\instance;

/**
 * A class to deal with broker addons in a subplugin
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
abstract class broker_meeting_events_addons {

    /**
     * @var instance $instance BigBlueButton instance if any
     */
    protected $instance;

    /**
     * @var string $data data to be processed
     */
    protected $data;

    /**
     * Constructor
     *
     * @param instance $instance BigBlueButton instance
     * @param string $data data to be processed
     */
    public function __construct(instance $instance, string $data) {
        $this->instance = $instance;
        $this->data = $data;
    }

    /**
     * Data processing action
     */
    abstract public function process_action();
}
