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
 * @package   block_use_stats
 * @category  blocks
 * @copyright 2006 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$publishes = array(
    'use_stats' => array(
        'servicename' => 'use_stats',
        'description' => get_string('use_stats_rpc_service_name', 'block_use_stats'),
        'apiversion' => 1,
        'classname'  => '',
        'filename'   => 'rpclib.php',
        'methods'    => array(
            'use_stats_rpc_get_stats',
            'use_stats_rpc_get_scores'
        ),
    ),
);

$subscribes = array(
    'use_stats' => array(
        'use_stats_rpc_get_stats' => 'blocks/use_stats/rpclib.php/use_stats_rpc_get_stats',
        'use_stats_rpc_get_scores' => 'blocks/use_stats/rpclib.php/use_stats_rpc_get_scores',
    ),
);
