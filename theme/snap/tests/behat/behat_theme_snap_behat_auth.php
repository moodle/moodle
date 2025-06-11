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
 * Overrides for behat authentication.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../auth/tests/behat/behat_auth.php');

/**
 * Overrides for behat authentication.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_auth extends behat_auth {

    public function i_log_out() {
        $this->getSession()->visit($this->locate_path('login/logout.php'));
        $this->execute('behat_forms::press_button', get_string('continue'));
    }
}
