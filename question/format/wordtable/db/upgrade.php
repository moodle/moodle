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
 * Short-answer question type upgrade code.
 *
 * @package    qformat_wordtable
 * @copyright  2014 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the wordtable question format.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qformat_wordtable_upgrade($oldversion) {

    if (get_config('converter_url', 'qformat_wordtable') !== false) {
        unset_config('converter_url', 'qformat_wordtable');
        unset_config('registration_url', 'qformat_wordtable');
        unset_config('username', 'qformat_wordtable');
        unset_config('password', 'qformat_wordtable');
    }

    return true;
}
