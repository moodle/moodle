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
 * Contains class profilefield_social\networks
 *
 * @package    profilefield_social
 * @copyright  2020 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace profilefield_social;

/**
 * helper class for social profile fields.
 *
 * @copyright  2020 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper  {

    /**
     * Get the available social networks
     *
     * @return array list of social networks.
     */
    public static function get_networks(): array {
        return [
            'icq' => get_string('icqnumber', 'profilefield_social'),
            'msn' => get_string('msnid', 'profilefield_social'),
            'aim' => get_string('aimid', 'profilefield_social'),
            'yahoo' => get_string('yahooid', 'profilefield_social'),
            'skype' => get_string('skypeid', 'profilefield_social'),
            'url' => get_string('webpage', 'profilefield_social'),
        ];
    }

    /**
     * Get the translated fieldname string for a network.
     *
     * @param string $fieldname Network short name.
     * @return string network name.
     */
    public static function get_fieldname(string $fieldname): string {
        $networks = self::get_networks();
        return $networks[$fieldname];
    }

    /**
     * Get the available network url formats.
     *
     * @return array list network url strings.
     */
    public static function get_network_urls(): array {
        return [
            'skype' => '<a href="skype:%%ENCODED%%?call">%%PLAIN%%</a>',
            'icq' => '<a href="http://www.icq.com/whitepages/cmd.php?uin=%%ENCODED%%&action=message">%%PLAIN%%</a>',
            'url' => '<a href="%%PLAIN%%">%%PLAIN%%</a>'
        ];
    }
}
