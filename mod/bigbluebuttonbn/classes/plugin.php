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

namespace mod_bigbluebuttonbn;

/**
 * Class plugin.
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Darko Miletic  (darko.miletic [at] gmail [dt] com)
 */
abstract class plugin {

    /**
     * Component name.
     */
    const COMPONENT = 'mod_bigbluebuttonbn';

    /**
     * Helper function to convert an html string to plain text.
     *
     * @param string $html
     * @param int $len
     *
     * @return string
     */
    public static function html2text($html, $len = 0) {
        $text = strip_tags($html);
        $text = str_replace('&nbsp;', ' ', $text);
        $textlen = strlen($text);
        $text = mb_substr($text, 0, $len);
        if ($textlen > $len) {
            $text .= '...';
        }
        return $text;
    }

    /**
     * Helper generates a random password.
     *
     * @param int $length
     * @param string $unique
     *
     * @return string
     */
    public static function random_password($length = 8, $unique = "") {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $password = substr(str_shuffle($chars), 0, $length);
        } while ($unique == $password);
        return $password;
    }

    /**
     * Generate random credentials for guest access
     *
     * @return array
     */
    public static function generate_guest_meeting_credentials(): array {
        $password = self::random_password();
        $guestlinkuid = sha1(self::random_password(1024));
        return [$guestlinkuid, $password];
    }
}
