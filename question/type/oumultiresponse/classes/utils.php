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
 * Utility functions for the oumultiresponse question type.
 *
 * @package qtype_oumultiresponse
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_oumultiresponse;

/**
 * Class that holds utility functions used by the oumultiresponse question type.
 *
 * @package qtype_oumultiresponse
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /** @var string - Less than operator */
    const OP_LT = "<";
    /** @var string - equal operator */
    const OP_E = "=";
    /** @var string - greater than operator */
    const OP_GT = ">";

    /**
     * Conveniently compare the current moodle version to a provided version in branch format. This function will
     * inflate version numbers to a three digit number before comparing them. This way moodle minor versions greater
     * than 9 can be correctly and easily compared.
     *
     * Examples:
     *   utils::moodle_version_is("<", "39");
     *   utils::moodle_version_is("<=", "310");
     *   utils::moodle_version_is(">", "39");
     *   utils::moodle_version_is(">=", "38");
     *   utils::moodle_version_is("=", "41");
     *
     * CFG reference:
     * $CFG->branch = "311", "310", "39", "38", ...
     * $CFG->release = "3.11+ (Build: 20210604)", ...
     * $CFG->version = "2021051700.04", ...
     *
     * @param string $operator for the comparison
     * @param string $version to compare to
     * @return boolean
     */
    public static function moodle_version_is(string $operator, string $version): bool {
        global $CFG;

        if (strlen($version) == 2) {
            $version = $version[0]."0".$version[1];
        }

        $current = $CFG->branch;
        if (strlen($current) == 2) {
            $current = $current[0]."0".$current[1];
        }

        $from = intval($current);
        $to = intval($version);
        $ops = str_split($operator);

        foreach ($ops as $op) {
            switch ($op) {
                case self::OP_LT:
                    if ($from < $to) {
                        return true;
                    }
                    break;
                case self::OP_E:
                    if ($from == $to) {
                        return true;
                    }
                    break;
                case self::OP_GT:
                    if ($from > $to) {
                        return true;
                    }
                    break;
                default:
                    throw new \coding_exception('invalid operator '.$op);
            }
        }

        return false;
    }
}
