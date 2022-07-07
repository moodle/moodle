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
 * External admin page class that allows a callback to be provided to determine whether page can be accessed
 *
 * @package     core_admin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\local\externalpage;

use admin_externalpage;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/adminlib.php");

/**
 * Admin externalpage class
 *
 * @package     core_admin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accesscallback extends admin_externalpage {

    /** @var callable $accesscheckcallback */
    protected $accesscheckcallback;

    /**
     * Class constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $url
     * @param callable $accesscheckcallback The callback method that will be executed to check whether user has access to
     *     this page. The setting instance ($this) is passed as an argument to the callback. Should return boolean value
     * @param bool $hidden
     */
    public function __construct(string $name, string $visiblename, string $url, callable $accesscheckcallback,
            bool $hidden = false) {

        $this->accesscheckcallback = $accesscheckcallback;

        parent::__construct($name, $visiblename, $url, [], $hidden);
    }

    /**
     * Determines if the current user has access to this external page based on access callback
     *
     * @return bool
     */
    public function check_access() {
        return ($this->accesscheckcallback)($this);
    }
}
