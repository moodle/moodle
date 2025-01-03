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
 * capabilities for local_ai_manager
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
        'local/ai_manager:use' => [
                'captype' => 'read',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_ALLOW,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_ALLOW,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_iomad_manager:manage' => [
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_PREVENT,
                        'companymanager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:manage' => [
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:managetenants' => [
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_PREVENT,
                ],
        ],
        'local/ai_manager:viewstatistics' => [
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:viewuserstatistics' => [
                'captype' => 'read',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:viewusernames' => [
                'captype' => 'read',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:viewusage' => [
                'captype' => 'read',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
        'local/ai_manager:managevertexcache' => [
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => [
                        'user' => CAP_PREVENT,
                        'guest' => CAP_PREVENT,
                        'student' => CAP_PREVENT,
                        'teacher' => CAP_PREVENT,
                        'editingteacher' => CAP_PREVENT,
                        'manager' => CAP_ALLOW,
                ],
        ],
];
