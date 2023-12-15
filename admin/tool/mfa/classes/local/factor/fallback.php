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

namespace tool_mfa\local\factor;

/**
 * Fallback factor class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fallback extends object_factor_base {

    /**
     * Overridden constructor. Name is hard set to 'fallback'.
     */
    public function __construct() {
        $this->name = 'fallback';
    }

    /**
     * {@inheritDoc}
     */
    public function get_display_name(): string {
        return get_string('fallback', 'tool_mfa');
    }

    /**
     * {@inheritDoc}
     */
    public function get_info(): string {
        return get_string('fallback_info', 'tool_mfa');
    }

    /**
     * {@inheritDoc}
     */
    public function get_state(): string {
        return \tool_mfa\plugininfo\factor::STATE_FAIL;
    }

    /**
     * Sets the state of the factor check into the session.
     * Returns whether storing the var was successful.
     *
     * @param string $state
     * @return bool
     */
    public function set_state(string $state): bool {
        return false;
    }
}
