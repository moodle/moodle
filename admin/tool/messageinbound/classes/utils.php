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

namespace tool_messageinbound;

/**
 * The Mail Pickup Utils.
 *
 * @package    tool_messageinbound
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /** @var int Encoding type: 7 bit SMTP semantic data. */
    const ENC7BIT = 0;
    /** @var int Encoding type: 8 bit SMTP semantic data. */
    const ENC8BIT = 1;
    /** @var int Encoding type: 8 bit binary data. */
    const ENCBINARY = 2;
    /** @var int Encoding type: BASE64 encoded data. */
    const ENCBASE64 = 3;
    /** @var int Encoding type: Human-readable 8-as-7 bit data. */
    const ENCQUOTEDPRINTABLE = 4;
    /** @var int Encoding type: Unknown. */
    const ENCOTHER = 5;

    /**
     * Get body content encoding.
     *
     * @return string[] List of body content encoding.
     */
    public static function get_body_encoding(): array {
        return [
            self::ENC7BIT => '7BIT',
            self::ENC8BIT => '8BIT',
            self::ENCBINARY => 'BINARY',
            self::ENCBASE64 => 'BASE64',
            self::ENCQUOTEDPRINTABLE => 'QUOTED-PRINTABLE',
            self::ENCOTHER => 'X-UNKNOWN',
        ];
    }
}
