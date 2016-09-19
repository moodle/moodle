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
 * Admin setting for SCSS variables.
 *
 * @package   theme_boost
 * @copyright 2016 Frédéric Massart - FMCorz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting for SCSS variables class.
 *
 * @package   theme_boost
 * @copyright 2016 Frédéric Massart - FMCorz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_boost_admin_setting_scss_variables extends admin_setting_configtextarea {

    /**
     * Validate data before storage.
     *
     * @param string $data The data.
     * @return mixed True if validated, else an error string.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        try {
            theme_boost_parse_scss_variables($data, false);
        } catch (moodle_exception $e) {
            return $e->getMessage();
        }

        return true;
    }

}

