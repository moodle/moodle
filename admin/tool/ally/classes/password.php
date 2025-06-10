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
 * Password generator.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Password generator.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class password {

    /**
     * @var string
     */
    private $password = '';

    public function __construct() {
        global $CFG;
        $originalminpasswordlength = $CFG->minpasswordlength;
        if ($CFG->minpasswordlength < 40) {
            // To get a min 40 char password for the web service user we have to set this config variable as the
            // core generate_password function does not accommodate a min length argument.
            $CFG->minpasswordlength = 40;
        }
        $maxlength = 254; // The password should never be used so it can be as big as we like.
        $this->password = generate_password($maxlength);
        if ($CFG->maxconsecutiveidentchars > 0) {
            $c = 0;
            while (!check_consecutive_identical_characters($this->password, $CFG->maxconsecutiveidentchars)) {
                $c ++;
                $this->password = generate_password($maxlength);
                if ($c > 100) {
                    $msg = 'Failed to create a password satisfying the maximum consecutive characters site policy ';
                    $msg .= '(' . $CFG->maxconsecutiveidentchars .') characters';
                    throw new \moodle_exception($msg);
                }
            }
        }
        $CFG->minpasswordlength = $originalminpasswordlength;
    }

    public function __toString() {
        return $this->password;
    }
}
