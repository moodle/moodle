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

namespace core\hook\output;

use moodle_url;

/**
 * Allow augmentation of the moodle URL.
 *
 * The hook carries a mutable moodle_url. Callbacks can add query params.
 *
 * @package    core
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allow augmentation of the moodle URL')]
#[\core\attribute\tags('output')]
final class extend_url {
    /**
     * Create a new instance of the hook.
     *
     * @param moodle_url $url The url of the page
     */
    public function __construct(
        /** @var moodle_url The url of the page */
        private moodle_url $url,
    ) {
    }

    /**
     * Get the URL being modified.
     *
     * @return moodle_url
     */
    public function get_url(): moodle_url {
        return $this->url;
    }

    /**
     * Add a query parameter to the URL.
     *
     * @param string $name The name of the parameter
     * @param string $value The value of the parameter
     */
    public function add_param(
        string $name,
        string $value,
    ): void {
        $this->url->param($name, $value);
    }
}
