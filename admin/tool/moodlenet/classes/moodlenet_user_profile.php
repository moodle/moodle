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
 * Moodle net user profile class.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlenet;

/**
 * A class to represent the moodlenet profile.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_user_profile {

    /** @var string $profile The full profile name. */
    protected $profile;

    /** @var int $userid The user ID that this profile belongs to. */
    protected $userid;

    /** @var string $username The username from $userprofile */
    protected $username;

    /** @var string $domain The domain from $domain */
    protected $domain;

    /**
     * Constructor method.
     *
     * @param string $userprofile The moodle net user profile string.
     * @param int $userid The user ID that this profile belongs to.
     */
    public function __construct(string $userprofile, int $userid) {
        $this->profile = $userprofile;
        $this->userid = $userid;

        $explodedprofile = explode('@', $this->profile);
        if (count($explodedprofile) === 2) {
            // It'll either be an email or WebFinger entry.
            $this->username = $explodedprofile[0];
            $this->domain = $explodedprofile[1];
        } else if (count($explodedprofile) === 3) {
            // We may have a profile link as MoodleNet gives to the user.
            $this->username = $explodedprofile[1];
            $this->domain = $explodedprofile[2];
        } else {
            throw new \moodle_exception('invalidmoodlenetprofile', 'tool_moodlenet');
        }
    }

    /**
     * Get the full moodle net profile.
     *
     * @return string The moodle net profile.
     */
    public function get_profile_name(): string {
        return $this->profile;
    }

    /**
     * Get the user ID that this profile belongs to.
     *
     * @return int The user ID.
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Get the username for this profile.
     *
     * @return string The username.
     */
    public function get_username(): string {
        return $this->username;
    }

    /**
     * Get the domain for this profile.
     *
     * @return string The domain.
     */
    public function get_domain(): string {
        return $this->domain;
    }
}
