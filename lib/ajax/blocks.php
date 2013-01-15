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
$pagetype = required_param('pagetype', PARAM_ALPHAEXT);
$contextid = required_param('contextid', PARAM_INT);
$subpage = optional_param('subpage', '', PARAM_ALPHANUMEXT);
$cmid = optional_param('cmid', null, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
// Params for blocks-move actions
$bui_moveid = optional_param('bui_moveid', 0, PARAM_INT);
$bui_newregion = optional_param('bui_newregion', '', PARAM_ALPHAEXT);
$bui_beforeid = optional_param('bui_beforeid', 0, PARAM_INT);

// Setting pagetype and URL
$PAGE->set_pagetype($pagetype);
$PAGE->set_url('/lib/ajax/blocks.php', array('courseid' => $courseid, 'pagelayout' => $pagelayout, 'pagetype' => $pagetype));

// Verifying login and session
$cm = null;
if (!is_null($cmid)) {
    $cm = get_coursemodule_from_id(null, $cmid, $courseid, false, MUST_EXIST);
}
require_login($courseid, false, $cm);
require_sesskey();

// Set context from ID, so we don't have to guess it from other info.
$PAGE->set_context(context::instance_by_id($contextid));

// Setting layout to replicate blocks configuration for the page we edit
$PAGE->set_pagelayout($pagelayout);
$PAGE->set_subpage($subpage);
$pagetype = explode('-', $pagetype);
switch ($pagetype[0]) {
    case 'my':
        // My Home page needs to have 'content' block region set up.
        $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
        $PAGE->blocks->add_region('content');
        break;
    case 'user':
        if ($pagelayout == 'mydashboard') {
            // User profile pages also need the 'content' block region set up.
            $PAGE->blocks->add_region('content');
            // If it's not the current user's profile, we need a different capability.
            if ($PAGE->context->contextlevel == CONTEXT_USER && $PAGE->context->instanceid != $USER->id) {
                $PAGE->set_blocks_editing_capability('moodle/user:manageblocks');
            } else {
                $PAGE->set_blocks_editing_capability('moodle/user:manageownblocks');
            }
        }
        break;
}

echo $OUTPUT->header(); // send headers

switch ($action) {
    case 'move':
        // Loading blocks and instances for the region
        $PAGE->blocks->load_blocks();
        $instances = $PAGE->blocks->get_blocks_for_region($bui_newregion);

        $bui_newweight = null;
        if ($bui_beforeid == 0) {
            // Moving to very bottom
            $last = end($instances);
            $bui_newweight = $last->instance->weight + 1;
        } else {
            // Moving somewhere
            $lastweight = 0;
            $lastblock = 0;
            $first = reset($instances);
            if ($first) {
                $lastweight = $first->instance->weight - 2;
            }

            foreach ($instances as $instance) {
                if ($instance->instance->id == $bui_beforeid) {
                    // Location found, just calculate weight like in
                    // block_manager->create_block_contents() and quit the loop.
                    if ($lastblock == $bui_moveid) {
                        // same block, same place - nothing to move
                        break;
                    }
                    $bui_newweight = ($lastweight + $instance->instance->weight) / 2;
                    break;
                }
                $lastweight = $instance->instance->weight;
                $lastblock = $instance->instance->id;
            }
        }

        // Move block if we need
        if (isset($bui_newweight)) {
            // Nasty hack
            $_POST['bui_newweight'] = $bui_newweight;
            $PAGE->blocks->process_url_move();
        }
        break;
}
