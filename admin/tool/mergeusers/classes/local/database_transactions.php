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
 * Determines whether the current database engine supports transactions.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use ReflectionException;
use ReflectionMethod;
use stdClass;

/**
 * Class that abstracts how to determine whether the current database engine supports transactions.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class database_transactions {
    /** @var bool true when database transactions are supported; false otherwise. */
    private static bool $supported = false;
    /** @var bool false by default to tell that the support for transactiosn is not checked yet. */
    private static bool $initialized = false;

    /**
     * Informs whether the current database engine supports transactions.
     *
     * @return bool true when database transactions are supported; false otherwise.
     * @throws ReflectionException
     */
    public static function are_supported(): bool {
        if (!self::$initialized) {
            global $DB;
            // Tricky way of getting real transactions support, without re-programming it.
            // May be in the future, as phpdoc shows, this method will be publicly accessible.
            $method = new ReflectionMethod($DB, 'transactions_supported');
            // From PHP 8.1 there is no more need to make it accessible.
            self::$supported = $method->invoke($DB);
            self::$initialized = true;
        }
        return self::$supported;
    }
}
