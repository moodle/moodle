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

namespace core_communication;

/**
 * Interface form_provider to manage communication provider form options from provider plugins.
 *
 * Every provider plugin should implement this class to return the implemented form elements for custom data.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface form_provider {
    /**
     * Set the form data to the instance if any data is available.
     *
     * @param \stdClass $instance The actual instance to set the data
     */
    public function save_form_data(\stdClass $instance): void;

    /**
     * Set the form data to the instance if any data is available.
     *
     * @param \stdClass $instance The actual instance to set the data
     */
    public function set_form_data(\stdClass $instance): void;

    /**
     * Set the form definitions.
     *
     * @param \MoodleQuickForm $mform The form object
     */
    public static function set_form_definition(\MoodleQuickForm $mform): void;
}
