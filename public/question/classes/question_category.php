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

namespace core_question;

use core\context;
use JsonSerializable;
use stdClass;

/**
 * A simple value object representing a question category.
 *
 * When serialised to JSON for output via routes, this the name and intro will be formatted.
 *
 * @package   core_question
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category implements JsonSerializable {
    /**
     * Set properties and format strings.
     *
     * @param int $id The category ID
     * @param string $name The raw category name, this will be formatted.
     * @param int $contextid The module context ID the category belongs to.
     * @param string $info The category's info, this with be formatted according to $infoformat.
     * @param string $infoformat The format for $info.
     * @param string $stamp Generated identifier for this category.
     * @param int $parent Parent category ID.
     * @param int $sortorder Category sort order within its parent.
     * @param int $idnumber ID number.
     */
    public function __construct(
        /** @var int $id The category ID */
        public int $id,
        /** @var string $name The raw category name, this will be formatted. */
        public string $name,
        /** @var int $contextid The module context ID the category belongs to. */
        public int $contextid,
        /** @var string $info The category's info, this with be formatted according to $infoformat. */
        public string $info,
        /** @var string $infoformat The format for $info. */
        public string $infoformat,
        /** @var string $stamp Generated identifier for this category. */
        public string $stamp,
        /** @var int $parent Parent category ID. */
        public int $parent,
        /** @var int $sortorder Category sort order within its parent. */
        public int $sortorder,
        /** @var int $idnumber ID number. */
        public int $idnumber,
    ) {
    }

    /**
     * Returns the properties with formatted 'name' and 'info'
     *
     * This is used when encoding the object for output, for example via web service routes.
     *
     * @return stdClass
     */
    public function jsonSerialize(): stdClass {
        $context = context::instance_by_id($this->contextid);
        return (object) [
            'id' => $this->id,
            'name' => format_string($this->name, ['context' => $context]),
            'contextid' => $this->contextid,
            'info' => format_text($this->info, $this->infoformat, ['context' => $context]),
            'stamp' => $this->stamp,
            'parent' => $this->parent,
            'sortorder' => $this->sortorder,
            'idnumber' => $this->idnumber,
        ];
    }
}
