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
 * Extract user vault preprocessor.
 *
 * @package    mod_forum
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults\preprocessors;

defined('MOODLE_INTERNAL') || die();

use user_picture;

/**
 * Extract user vault preprocessor.
 *
 * Used to separate out the user record
 * from a list of DB records that have been joined on the user table.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extract_user {
    /** @var string $idalias The alias for the id property of the user */
    private $idalias;
    /** @var string $alias The prefix used for each of the user properties */
    private $alias;

    /**
     * Constructor.
     *
     * @param string $idalias The alias for the id property of the user
     * @param string $alias The prefix used for each of the user properties
     */
    public function __construct(string $idalias, string $alias) {
        $this->idalias = $idalias;
        $this->alias = $alias;
    }

    /**
     * Extract the user records from a list of DB records.
     *
     * @param stdClass[] $records The DB records
     * @return stdClass[] The list of extracted users
     */
    public function execute(array $records) : array {
        $idalias = $this->idalias;
        $alias = $this->alias;

        return array_map(function($record) use ($idalias, $alias) {
            return user_picture::unalias($record, ['deleted'], $idalias, $alias);
        }, $records);
    }
}