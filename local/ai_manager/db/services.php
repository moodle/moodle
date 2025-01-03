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
 * External service definitions for local_ai_manager.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
        'local_ai_manager_post_query' => [
                'classname' => 'local_ai_manager\external\submit_query',
                'description' => 'Send a query to a LLM.',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => 'local/ai_manager:use',
        ],
        'local_ai_manager_get_ai_config' => [
                'classname' => 'local_ai_manager\external\get_ai_config',
                'description' => 'Get all information about the current ai configuration for the current user',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => 'local/ai_manager:use',
        ],
        'local_ai_manager_get_purpose_options' => [
                'classname' => 'local_ai_manager\external\get_purpose_options',
                'description' => 'Retrieve available options for a given purpose',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => 'local/ai_manager:use',
        ],
        'local_ai_manager_get_user_quota' => [
                'classname' => 'local_ai_manager\external\get_user_quota',
                'description' => 'Retrieve quota information for the current user',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => 'local/ai_manager:use',
        ],
        'local_ai_manager_vertex_cache_status' => [
                'classname' => 'local_ai_manager\external\vertex_cache_status',
                'description' => 'Fetch and update the Google Vertex AI caching status',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => '',
        ],

];
