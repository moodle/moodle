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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\tools;

class os_classifier {

    const TYPE_DESKTOP = 'desktop';
    const TYPE_MOBILE = 'mobile';
    const TYPE_OTHER = 'other';

    private static $os = array(
        self::TYPE_DESKTOP => array(
            'Windows 10',
            'Windows 8.1',
            'Windows 8',
            'Windows 7',
            'Windows Vista',
            'Windows 2003',
            'Windows XP',
            'Windows 2000',
            'Windows NT 4.0',
            'Windows NT',
            'Windows 98',
            'Windows 95',
            'Windows Phone',
            'Unknown Windows OS',
            'Mac OS X',
            'Power PC Mac',
            'ppc mac',
            'freebsd',
            'ppc',
            'Macintosh',
            'linux',
            'debian',
            'sunos',
            'Sun Solaris',
            'beos',
            'GNU/Linux',
            'gnu',
            'unix',
            'Unknown Unix OS',
            'netbsd',
            'bsdi',
            'openbsd'
        ),
        self::TYPE_MOBILE => array(
            'Android',
            'IOS',
            'iphone',
            'ipad',
            'ipod',
            'BlackBerry',
            'Symbian OS',
            'symbian'
        )
    );

    public static function getOSType($required) {

        $required = strtolower($required);

        foreach (self::$os as $type => $osses) {

            foreach ($osses as $os) {

                if (strtolower($os) === $required) {
                    return $type;
                }

            }

        }

        return self::TYPE_OTHER;

    }

}