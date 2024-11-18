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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = [

    [
     'eventname' => '\block_iomad_company_admin\event\company_created',
     'callback' => '\local_email\observer::company_created',
     'internal' => false,
    ],

    [
     'eventname' => '\tool_langimport\event\langpack_removed',
     'callback' => '\local_email\observer::langpack_removed',
     'internal' => false,
    ],

    [
     'eventname' => '\tool_langimport\event\langpack_imported',
     'callback' => '\local_email\observer::langpack_imported',
     'internal' => false,
    ],
];
