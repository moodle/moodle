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
 * Capabilities for plagiarism_turnitin
 *
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'plagiarism/turnitin:enable' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
         'legacy' => [
         'editingteacher' => CAP_ALLOW,
         'manager' => CAP_ALLOW,
        ],
    ],
    'plagiarism/turnitin:viewfullreport' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
         'legacy' => [
         'editingteacher' => CAP_ALLOW,
         'manager' => CAP_ALLOW,
        ],
    ],
];
