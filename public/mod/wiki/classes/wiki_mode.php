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

namespace mod_wiki;

/**
 * Wiki modes enum.
 *
 * @package    mod_wiki
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum wiki_mode: string {
    case UNDEFINED = '';
    case COLLABORATIVE = 'collaborative';
    case INDIVIDUAL = 'individual';

    /**
     * Returns the user friendly string representation of the wiki mode.
     *
     * @return string user friendly representation.
     */
    public function to_string(): string {
        $stringmanager = \core\di::get(\core_string_manager::class);
        if ($this === self::UNDEFINED) {
            return $stringmanager->get_string('wikimodeundefined', 'mod_wiki');
        }
        return $stringmanager->get_string('wikimode' . $this->value, 'mod_wiki');
    }
}
