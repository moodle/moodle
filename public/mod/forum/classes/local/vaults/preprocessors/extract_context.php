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
 * Extract context vault preprocessor.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults\preprocessors;

defined('MOODLE_INTERNAL') || die();

use context;
use context_helper;

/**
 * Extract context vault preprocessor.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extract_context {
    /**
     * Extract the contexts from a list of records.
     *
     * @param stdClass[] $records The list of records which have context properties
     * @return context[] List of contexts matching the records.
     */
    public function execute(array $records): array {
        return array_map(function($record) {
            $contextid = $record->ctxid;
            context_helper::preload_from_record($record);
            $context = context::instance_by_id($contextid);
            return $context;
        }, $records);
    }
}
