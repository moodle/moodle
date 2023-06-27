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
 * This file defines an item of metadata which encapsulates a user's preferences.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\metadata\types;

defined('MOODLE_INTERNAL') || die();

/**
 * The user_preference type.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_preference implements type {

    /**
     * @var The name of this user preference.
     */
    protected $name;

    /**
     * @var A description of what this user preference means.
     */
    protected $summary;

    /**
     * Constructor to create a new user_preference types.
     *
     * @param   string  $name The name of the user preference.
     * @param   string  $summary A description of what the preference is used for.
     */
    public function __construct($name, $summary = '') {
        if (debugging('', DEBUG_DEVELOPER)) {
            $teststring = clean_param($summary, PARAM_STRINGID);
            if ($teststring !== $summary) {
                debugging("Summary information for use of the '{$name}' subsystem " .
                    " has an invalid langstring identifier: '{$summary}'",
                    DEBUG_DEVELOPER);
            }
        }

        $this->name = $name;
        $this->summary = $summary;
    }

    /**
     * The name of the user preference.
     *
     * @return  string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * A user preference encapsulates a single field and has no sub-fields.
     *
     * @return  array
     */
    public function get_privacy_fields() {
        return null;
    }

    /**
     * A summary of what this user preference is used for.
     *
     * @return  string
     */
    public function get_summary() {
        return $this->summary;
    }
}
