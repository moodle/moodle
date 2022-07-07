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
 * Dropbox Rate Limit Encountered.
 *
 * @since       Moodle 3.2
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_dropbox;

defined('MOODLE_INTERNAL') || die();

/**
 * Dropbox Rate Limit Encountered.
 *
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rate_limit_exception extends dropbox_exception {
    /**
     * Constructor for rate_limit_exception.
     */
    public function __construct() {
        parent::__construct('Rate limit hit');
    }
}
