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

namespace factor_nosetup;

use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * No setup factor class.
 *
 * @package     factor_nosetup
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * No Setup Factor implementation.
     * Factor is a singleton, can only be one instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * No Setup Factor implementation.
     * Factor does not have input.
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        return false;
    }

    /**
     * No Setup Factor implementation.
     * State check is performed here, as there is no form to do it in.
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        // Check if user has any other input or setup factors active.
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        foreach ($factors as $factor) {
            if ($factor->has_input() || $factor->has_setup()) {
                return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
            }
        }

        return \tool_mfa\plugininfo\factor::STATE_PASS;
    }

    /**
     * No setup implementation.
     * Copy of get_state, but can take other user..
     *
     * @param stdClass $user
     * @return void
     */
    public function possible_states(stdClass $user): array {
        // We return Neutral here because to support optional rollouts
        // it needs to report neutral or the menu to setup will not display.
        return [\tool_mfa\plugininfo\factor::STATE_NEUTRAL];
    }

    /**
     * No Setup Factor implementation.
     * The state can never be set. Always return true.
     *
     * @param string $state the state constant to set
     * @return bool
     */
    public function set_state(string $state): bool {
        return true;
    }

    /**
     * No Setup Factor implementation.
     * nosetup should not be a valid combination with another factor.
     *
     * @param array $combination array of factors that make up the combination
     * @return bool
     */
    public function check_combination(array $combination): bool {
        // If this combination has more than 1 factor that has setup or input, not valid.
        foreach ($combination as $factor) {
            if ($factor->has_setup() || $factor->has_input()) {
                return false;
            }
        }
        return true;
    }
}
