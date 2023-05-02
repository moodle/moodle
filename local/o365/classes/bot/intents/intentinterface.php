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
 * Interface intentinterface defines constants default for all intents and  methods that all intents must implement.
 *
 * @package local_o365
 * @author Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface intentinterface defines constants default for all intents and  methods that all intents must implement.
 */
interface intentinterface {
    /**
     * Default list items maximum limit number
     */
    const DEFAULT_LIMIT_NUMBER = 10;

    /**
     * Get bot ready message.
     *
     * @param string $language - message language code
     * @param mixed $entities - intent entities (optional)
     * @return array - structured message with all details for bot
     */
    public static function get_message($language, $entities = null);
}
