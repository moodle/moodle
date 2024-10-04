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

declare(strict_types=1);

namespace core_user\hook;

use core\attribute\{label, tags};
use core\lang_string;
use core\url;

/**
 * Hook to allow callbacks to extend the default homepage options
 *
 * @package     core_user
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[label('Allows callbacks to extend the default homepage options')]
#[tags('user')]
final class extend_default_homepage {

    /** @var array $options */
    private array $options = [];

    /**
     * Hook constructor
     *
     * @param bool $userpreference Whether this is being selected as a user preference
     */
    public function __construct(
        /** @var bool $userpreference Whether this is being selected as a user preference */
        public readonly bool $userpreference = false,
    ) {
    }

    /**
     * To be called by callback to add an option
     *
     * @param url $url URL that can be used as a site homepage. Must be a local URL.
     * @param lang_string|string $title
     */
    public function add_option(url $url, lang_string|string $title): void {
        $this->options[$url->out_as_local_url()] = $title;
    }

    /**
     * Returns all added options
     *
     * @return array
     */
    public function get_options(): array {
        return $this->options;
    }
}
