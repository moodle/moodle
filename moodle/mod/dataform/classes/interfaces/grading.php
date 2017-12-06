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
 * Interface class.
 *
 * @package   mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_dataform\interfaces;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for dataformfield grading support
 *
 * The interface that is implemented by any dataformfield plugin which supports grading.
 * It forces inheriting classes to define methods that are called by the dataform for grading.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface grading {

    /**
     * Returns the value replacement of the pattern for each user with content in the field.
     *
     * @param string $pattern
     * @param array $entryids The ids of entries the field values should be fetched from.
     *      If not provided the method should return values from all applicable entries.
     * @param int $userid   The id of the users whose field values are requested.
     *      If not specified, should return values for all applicable users.
     * @return null|array Array of userid => value pairs.
     */
    public function get_user_values($pattern, array $entryids = null, $userid = 0);

}
