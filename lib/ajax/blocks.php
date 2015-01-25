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
 * Provide interface for blocks AJAX actions
 *
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */

define('AJAX_SCRIPT', true);
require_once(dirname(__FILE__) . '/../../config.php');

// Initialise ALL common incoming parameters here, up front.
$courseid = required_param('courseid', PARAM_INT);
$pagelayout = required_param('pagelayout', PARAM_ALPHAEXT);
$pagetype = required_param('pagetype', PARAM_ALPHANUMEXT);
$contextid = required_param('contextid', PARAM_INT);
$subpage = optional_param('subpage', '', PARAM_ALPHANUMEXT);
$cmid = optional_param('cmid', null, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
// Params for blocks-move actions.
$buimoveid = optional_param('bui_moveid', 0, PARAM_INT);
$buinewregion = optional_param('bui_newregion', '', PARAM_ALPHAEXT);
$buibeforeid = optional_param('bui_beforeid', 0, PARAM_INT);

// Setting pagetype and URL.
$PAGE->set_pagetype($pagetype);
$PAGE->set_url('/lib/ajax/blocks.php', array('courseid' => $courseid, 'pagelayout' => $pagelayout, 'pagetype' => $pagetype));

// Verifying login and session.
$cm = null;
if (!is_null($cmid)) {
    $cm = get_coursemodule_from_id(null, $cmid, $courseid, false, MUST_EXIST);
}
require_login($courseid, false, $cm);
require_sesskey();

// Set context from ID, so we don't have to guess it from other info.
$PAGE->set_context(context::instance_by_id($contextid));

// Setting layout to replicate blocks configuration for the page we edit.
$PAGE->set_pagelayout($pagelayout);
$PAGE->set_subpage($subpage);
$PAGE->blocks->add_custom_regions_for_pagetype($pagetype);
$pagetype = explode('-', $pagetype);
switch ($pagetype[0]) {
    case 'my':
        $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
        break;
    case 'user':
        if ($pagelayout == 'mydashboard') {
            // If it's not the current user's profile, we need a different capability.
            if ($PAGE->context->contextlevel == CONTEXT_USER && $PAGE->context->instanceid != $USER->id) {
                $PAGE->set_blocks_editing_capability('moodle/user:manageblocks');
            } else {
                $PAGE->set_blocks_editing_capability('moodle/user:manageownblocks');
            }
        }
        break;
}

// Send headers.
echo $OUTPUT->header();

switch ($action) {
    case 'move':
        // Loading blocks and instances for the region.
        $PAGE->blocks->load_blocks();
        $instances = $PAGE->blocks->get_blocks_for_region($buinewregion);

        $buinewweight = null;
        if ($buibeforeid == 0) {
            if (count($instances) === 0) {
                // Moving the block into an empty region. Give it the default weight.
                $buinewweight = 0;
            } else {
                // Moving to very bottom.
                $last = end($instances);
                $buinewweight = $last->instance->weight + 1;
            }
        } else {
            // Moving somewhere.
            $lastweight = 0;
            $lastblock = 0;
            $first = reset($instances);
            if ($first) {
                $lastweight = $first->instance->weight - 2;
            }

            foreach ($instances as $instance) {
                if ($instance->instance->id == $buibeforeid) {
                    // Location found, just calculate weight like in block_manager->create_block_contents() and quit the loop.
                    if ($lastblock == $buimoveid) {
                        // Same block, same place - nothing to move.
                        break;
                    }
                    $buinewweight = ($lastweight + $instance->instance->weight) / 2;
                    break;
                }
                $lastweight = $instance->instance->weight;
                $lastblock = $instance->instance->id;
            }
        }

        // Move block if we need.
        if (isset($buinewweight)) {
            // Nasty hack.
            $_POST['bui_newweight'] = $buinewweight;
            $PAGE->blocks->process_url_move();
        }
        break;
}
