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
 * Generate the links to open/download the Safe Exam Browser with correct settings.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Generate the links to open/download the Safe Exam Browser with correct settings.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link_generator {

    /**
     * Get a link to force the download of the file over https or sebs protocols.
     *
     * @param string $cmid Course module ID.
     * @param bool $seb Whether to use a seb:// scheme or fall back to http:// scheme.
     * @param bool $secure Whether to use HTTPS or HTTP protocol.
     * @return string A URL.
     */
    public static function get_link(string $cmid, bool $seb = false, bool $secure = true) : string {
        // Check if course module exists.
        get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);

        $url = new moodle_url('/mod/quiz/accessrule/seb/config.php?cmid=' . $cmid);
        if ($seb) {
            $secure ? $url->set_scheme('sebs') : $url->set_scheme('seb');
        } else {
            $secure ? $url->set_scheme('https') : $url->set_scheme('http');
        }
        return $url->out();
    }
}
