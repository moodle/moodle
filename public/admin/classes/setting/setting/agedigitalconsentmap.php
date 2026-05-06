<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Used to validate the content and format of the age of digital consent map and ensuring it is parsable.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class agedigitalconsentmap extends \core_admin\setting\setting\configtextarea {
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param string $cols
     * @param string $rows
     */
    public function __construct(
        $name,
        $visiblename,
        $description,
        $defaultsetting,
        $paramtype = PARAM_RAW,
        $cols = '60',
        $rows = '8'
    ) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $cols, $rows);
        // Pre-set force LTR to false.
        $this->set_force_ltr(false);
    }

    /**
     * Validate the content and format of the age of digital consent map to ensure it is parsable.
     *
     * @param string $data The age of digital consent map from text field.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        try {
            \core_auth\digital_consent::parse_age_digital_consent_map($data);
        } catch (\moodle_exception $e) {
            return get_string('invalidagedigitalconsent', 'admin', $e->getMessage());
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(agedigitalconsentmap::class, \admin_setting_agedigitalconsentmap::class);
