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
 * qbank_columnsortorder external functions and service definitions.
 *
 * @package    qbank_columnsortorder
 * @category   webservice
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     2021, Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'qbank_columnsortorder_set_columnbank_order' => [
        'classname' => 'qbank_columnsortorder\external\set_columnbank_order',
        'description' => 'Sets question columns order in database',
        'type' => 'write',
        'ajax' => true,
    ],
    'qbank_columnsortorder_set_hidden_columns' => [
        'classname' => 'qbank_columnsortorder\external\set_hidden_columns',
        'description' => 'Hidden Columns',
        'type' => 'write',
        'ajax' => true,
    ],
    'qbank_columnsortorder_set_column_size' => [
        'classname' => 'qbank_columnsortorder\external\set_column_size',
        'description' => 'Column size',
        'type' => 'write',
        'ajax' => true,
    ],
];
