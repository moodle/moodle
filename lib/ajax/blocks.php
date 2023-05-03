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
require_once(__DIR__ . '/../../config.php');

// Initialise ALL common incoming parameters here, up front.
$pagehash = required_param('pagehash', PARAM_RAW);
$action = optional_param('action', '', PARAM_ALPHA);
// Params for blocks-move actions.
$buimoveid = optional_param('bui_moveid', 0, PARAM_INT);
$buinewregion = optional_param('bui_newregion', '', PARAM_ALPHAEXT);
$buibeforeid = optional_param('bui_beforeid', 0, PARAM_INT);

$PAGE->set_url('/lib/ajax/blocks.php', ['pagehash' => $pagehash]);

// Retrieve the edited page from the session hash.
$page = moodle_page::retrieve_edited_page($pagehash, MUST_EXIST);

// Verifying login and session.
$cm = null;
if (!is_null($page->cm)) {
    $cm = get_coursemodule_from_id(null, $page->cm->id, $page->course->id, false, MUST_EXIST);
}
require_login($page->course, false, $cm);
require_sesskey();
$PAGE->set_context($page->context);

if (!$page->user_can_edit_blocks() || !$page->user_is_editing()) {
    throw new moodle_exception('nopermissions', '', $page->url->out(), get_string('editblock'));
}

// Send headers.
echo $OUTPUT->header();

switch ($action) {
    case 'move':
        // Loading blocks and instances for the region.
        $page->blocks->load_blocks();
        $instances = $page->blocks->get_blocks_for_region($buinewregion);

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
            $page->blocks->process_url_move();
        }
        break;
}
