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
 * A value object encapsulating user preferences to set up the report table.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\user_preferences;

use stdClass;

final class user_preferences {

    public const PER_PAGE_OPTIONS = [5, 10, 15, 20, 25, 50];

    public const PER_PAGE_DEFAULT = 15;

    public const SHOW_INITIALS_BAR_DEFAULT = 1;

    public const PERSISTENT_FILTER_DEFAULT = 0;

    /**
     * @var int $perpage
     */
    private $perpage;

    /**
     * @var int $showinitialsbar Represents a boolean value, but defined as an int, as it's stored as an int.
     */
    private $showinitialsbar;

    /**
     * @var int $persistentfilter Same as for $showinitialsbar above.
     */
    private $persistentfilter;

    /**
     * @var filter_user_preferences|null $filter
     */
    private $filter;

    private function __construct(
        int $perpage,
        int $showinitialsbar,
        int $persistentfilter,
        ?filter_user_preferences $filter
    ) {
        $this->perpage = in_array($perpage, self::PER_PAGE_OPTIONS) ? $perpage : self::PER_PAGE_DEFAULT;

        $this->showinitialsbar = in_array($showinitialsbar, [0, 1])
            ? $showinitialsbar
            : self::SHOW_INITIALS_BAR_DEFAULT;

        $this->persistentfilter = in_array($persistentfilter, [0, 1])
            ? $persistentfilter
            : self::PERSISTENT_FILTER_DEFAULT;

        $this->filter = $filter;
    }

    public function rows_per_page(): int {
        return $this->perpage;
    }

    public function show_initials_bar(): bool {
        return (bool) $this->showinitialsbar;
    }

    public function persistent_filter(): bool {
        return (bool) $this->persistentfilter;
    }

    public function filter(): ?filter_user_preferences {
        return $this->filter;
    }

    public function has_filter_preference(): bool {
        return $this->filter !== null;
    }

    public function with_filter_preference(filter_user_preferences $preference): self {
        return new self($this->perpage, $this->showinitialsbar, $this->persistentfilter, $preference);
    }

    public function without_filter_preference(): self {
        return new self($this->perpage, $this->showinitialsbar, $this->persistentfilter, null);
    }

    public function as_array(): array {
        $return = ['perpage' => $this->perpage, 'showinitialsbar' => $this->showinitialsbar,
            'persistentfilter' => $this->persistentfilter];
        $return['filter'] = ($this->filter === null) ? null : $this->filter->as_array();

        return $return;
    }

    public static function from_array(array $prefs): self {
        $filter = array_key_exists('filter', $prefs) ? $prefs['filter'] : null;

        return new self(
            array_key_exists('perpage', $prefs) ? $prefs['perpage'] : self::PER_PAGE_DEFAULT,
            array_key_exists('showinitialsbar', $prefs) ? $prefs['showinitialsbar'] : self::SHOW_INITIALS_BAR_DEFAULT,
            array_key_exists('persistentfilter', $prefs) ? $prefs['persistentfilter'] : self::PERSISTENT_FILTER_DEFAULT,
            ($filter === null) ? null : filter_user_preferences::from_array($filter)
        );
    }

    public static function from_plain_object(stdClass $object): self {
        return self::from_array((array) $object);
    }

    public static function defaults(): self {
        return new self(self::PER_PAGE_DEFAULT, self::SHOW_INITIALS_BAR_DEFAULT, self::PERSISTENT_FILTER_DEFAULT, null);
    }
}
