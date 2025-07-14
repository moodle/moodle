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
 * A base communication provider.
 *
 * This interface should be used to declare support for the instantiation method for communication providers.
 *
 * Every communication provider must, as a minimum, implement this provider.
 *
 * @package    core_communication
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface communication_provider {
    /**
     * A base communication provider.
     *
     * @param processor $communication The communication object
     */
    public static function load_for_instance(processor $communication): self;

    /**
     * Check if the provider is configured or not.
     *
     * This method is intended to check if the plugin have got any settings and if all the settings are set properly.
     * This checking helps to reduce errors in future when a communication instance is added for the provider and not configured.
     */
    public static function is_configured(): bool;
}
