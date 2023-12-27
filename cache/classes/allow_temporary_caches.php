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

namespace core_cache;

/**
 * Create and keep an instance of this class to allow temporary caches when caches are disabled.
 *
 * This class works together with code in {@see cache_factory_disabled}.
 *
 * The intention is that temporary cache should be short-lived (not for the entire install process),
 * which avoids two problems: first, that we might run out of memory for the caches, and second,
 * that some code e.g. install.php/upgrade.php files, is entitled to assume that caching is not
 * used and make direct database changes.
 *
 * @package core_cache
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class allow_temporary_caches {
    /** @var int Number of references of this class; if more than 0, temporary caches are allowed */
    protected static $references = 0;

    /**
     * Constructs an instance of this class.
     *
     * Temporary caches will be allowed until this instance goes out of scope. Store this token
     * in a local variable, so that the caches have a limited life; do not save it outside your
     * function.
     *
     * If cache is not disabled then normal (non-temporary) caches will be used, and this class
     * does nothing.
     *
     * If an object of this class already exists then creating (or destroying) another one will
     * have no effect.
     */
    public function __construct() {
        self::$references++;
    }

    /**
     * Destroys an instance of this class.
     *
     * You do not need to call this manually; PHP will call it automatically when your variable
     * goes out of scope. If you do need to remove your token at other times, use unset($token);
     *
     * If there are no other instances of this object, then all temporary caches will be discarded.
     */
    public function __destruct() {
        global $CFG;
        require_once($CFG->dirroot . '/cache/disabledlib.php');

        self::$references--;
        if (self::$references === 0) {
            \cache_factory_disabled::clear_temporary_caches();
        }
    }

    /**
     * Checks if temp caches are currently allowed.
     *
     * @return bool True if allowed
     */
    public static function is_allowed(): bool {
        return self::$references > 0;
    }
}
