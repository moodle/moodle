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
 * Version information
 *
 * @package   tool_mergeusers
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author    Mike Holzer
 * @author    Forrest Gaston
 * @author    Juan Pablo Torres Herrera
 * @author    Jordi Pujol-Ahulló, Sred, Universitat Rovira i Virgili
 * @author    John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @copyright Universitat Rovira i Virgili
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\selected_users_to_merge;
use tool_mergeusers\local\user_merger;
use tool_mergeusers\local\user_searcher;
use tool_mergeusers\output\merge_user_form;
use tool_mergeusers\output\user_select_table;

require('../../../config.php');

global $CFG, $PAGE;

// Report all PHP errors.
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/weblib.php');

require_login();
require_capability('tool/mergeusers:mergeusers', context_system::instance());

admin_externalpage_setup('tool_mergeusers_merge');

// Get possible posted params.
$option = optional_param('option', null, PARAM_TEXT);
if (!$option) {
    if (optional_param('clearselection', false, PARAM_TEXT)) {
        $option = 'clearselection';
    } else if (optional_param('mergeusers', false, PARAM_TEXT)) {
        $option = 'mergeusers';
    }
}

// Define the form.
$mergeuserform = new merge_user_form();
// phpcs:disable
/** @var tool_mergeusers\output\renderer $renderer */
$renderer = $PAGE->get_renderer('tool_mergeusers');
// phpcs:enable

$data = $mergeuserform->get_data();

// May abort execution if database not supported, for security.
$usermerger = new user_merger();
// Search tool for searching for users and verifying them.
$usersearcher = new user_searcher();
// Session-stored selection of users to merge.
$currentuserselection = selected_users_to_merge::instance();

// If there was a custom option submitted (by custom form) then use that option instead of main form's data.
if (!empty($option)) {
    switch ($option) {
        // One or two users are selected: save them into session.
        case 'saveselection':
            // Get and verify the userids from the selection form usig the verify_user function (second field is column).
            [$olduser, $oumessage] = $usersearcher->verify_user(
                optional_param('olduser', null, PARAM_INT),
                'id',
            );
            [$newuser, $numessage] = $usersearcher->verify_user(
                optional_param('newuser', null, PARAM_INT),
                'id',
            );

            if ($olduser === null && $newuser === null) {
                $renderer->mu_error(get_string('no_saveselection', 'tool_mergeusers'));
                exit(); // Forces end of execution for error.
            }

            // Store saved selection for displaying them on the index page.
            $currentuserselection->set_from_user($olduser);
            $currentuserselection->set_to_user($newuser);

            $step = $currentuserselection->both_are_selected() ?
                    $renderer::INDEX_PAGE_CONFIRMATION_STEP :
                    $renderer::INDEX_PAGE_SEARCH_STEP;

            echo $renderer->index_page($mergeuserform, $step);
            break;

        // Remove any of the selected users to merge, and search for them again.
        case 'clearselection':
            $currentuserselection->clear_users_selection();

            // Redirect back to index/search page for new selections or review selections.
            $redirecturl = new moodle_url('/admin/tool/mergeusers/index.php');
            redirect($redirecturl, null, 0);
            break;

        // Proceed with the merging and show results.
        case 'mergeusers':
            // Verify users once more just to be sure.  Both users should already be verified, but just an extra layer of security.
            [$fromuser, $oumessage] = $usersearcher->verify_user($currentuserselection->from_user()->id ?? null, 'id');
            [$touser, $numessage] = $usersearcher->verify_user($currentuserselection->to_user()->id ?? null, 'id');
            if ($fromuser === null || $touser === null) {
                $renderer->mu_error($oumessage . '<br />' . $numessage);
                break; // Break execution for error.
            }

            // Merge the users.
            $log = [];
            $success = true;
            [$success, $log, $logid] = $usermerger->merge($touser->id, $fromuser->id);

            // Reset mut session to let the user choose another pair of users to merge.
            $currentuserselection->clear_users_selection();

            // Render results page.
            echo $renderer->results_page($touser, $fromuser, $success, $log, $logid);
            break;

        // We have both users to merge selected, but we want to change any of them.
        case 'searchusers':
            echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_SEARCH_STEP);
            break;

        // We have both users to merge selected, and in the search step.
        // We want to proceed with the merging of the currently selected users.
        case 'continueselection':
            echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_CONFIRMATION_STEP);
            break;

        // Ops! Other option is not expected.
        default:
            $renderer->mu_error(get_string('invalid_option', 'tool_mergeusers'));
            break;
    }
    // Any submitted data?
} else if ($data) {
    // If there is a search argument use this instead of advanced form.
    if (!empty($data->searchgroup['searcharg'])) {
        $searchedusers = $usersearcher->search_users($data->searchgroup['searcharg'], $data->searchgroup['searchfield']);
        $userselecttable = new user_select_table($searchedusers, $renderer);

        echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_SEARCH_AND_SELECT_STEP, $userselecttable);

        // Only run this step if there are both a new and old userids.
    } else if (!empty($data->oldusergroup['olduserid']) && !empty($data->newusergroup['newuserid'])) {
        // Get and verify the userids from the selection form usig the verify_user function (second field is column).
        [$olduser, $oumessage] = $usersearcher->verify_user($data->oldusergroup['olduserid'], $data->oldusergroup['olduseridtype']);
        [$newuser, $numessage] = $usersearcher->verify_user($data->newusergroup['newuserid'], $data->newusergroup['newuseridtype']);

        if ($olduser === null || $newuser === null) {
            $renderer->mu_error($oumessage . '<br />' . $numessage);
            exit(); // Forces end of execution for error.
        }

        // Add users to session for review step.
        $currentuserselection->set_from_user($olduser);
        $currentuserselection->set_to_user($newuser);

        echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_SEARCH_AND_SELECT_STEP);
    } else {
        // Simply show search form.
        echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_SEARCH_STEP);
    }
} else {
    // No submitted data from form.
    echo $renderer->index_page($mergeuserform, $renderer::INDEX_PAGE_SEARCH_STEP);
}
