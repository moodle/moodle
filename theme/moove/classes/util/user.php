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
 * User class utility class
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\util;

use stdClass;
use user_picture;

/**
 * User class utility class
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
    /**
     * @var \stdClass $user The user object.
     */
    protected $user;

    /**
     * Class constructor
     *
     * @param stdClass $user
     *
     */
    public function __construct($user = null) {
        global $USER, $DB;

        if (!is_object($user)) {
            $user = $DB->get_record('user', ['id' => $user], '*', MUST_EXIST);
        }

        if (!$user) {
            $user = $USER;
        }

        $this->user = $user;
    }

    /**
     * Returns the user picture
     *
     * @param int $imgsize
     *
     * @return \moodle_url
     * @throws \coding_exception
     */
    public function get_user_picture($imgsize = 100) {
        global $PAGE;

        $userimg = new user_picture($this->user);

        $userimg->size = $imgsize;

        return $userimg->get_url($PAGE)->out();
    }
}
