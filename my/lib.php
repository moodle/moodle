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
 * My Moodle -- a user's personal dashboard
 *
 * This file contains common functions for the dashboard and profile pages.
 *
 * @package    moodlecore
 * @subpackage my
 * @copyright  2010 Remote-Learner.net
 * @author     Hubert Chathi <hubert@remote-learner.net>
 * @author     Olav Jordan <olav.jordan@remote-learner.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('MY_PAGE_PUBLIC', 0);
define('MY_PAGE_PRIVATE', 1);

require_once("$CFG->libdir/blocklib.php");

/*
 * For a given user, this returns the $page information for their My Moodle page
 *
 */
function my_get_page($userid, $private=MY_PAGE_PRIVATE) {
    global $DB, $CFG;

    if (empty($CFG->forcedefaultmymoodle) && $userid) {  // Ignore custom My Moodle pages if admin has forced them
        // Does the user have their own page defined?  If so, return it.
        if ($customised = $DB->get_record('my_pages', array('userid' => $userid, 'private' => $private))) {
            return $customised;
        }
    }

    // Otherwise return the system default page
    return $DB->get_record('my_pages', array('userid' => null, 'name' => '__default', 'private' => $private));
}


/*
 * This copies a system default page to the current user
 *
 */
function my_copy_page($userid, $private=MY_PAGE_PRIVATE, $pagetype='my-index') {
    global $DB;

    if ($customised = $DB->get_record('my_pages', array('userid' => $userid, 'private' => $private))) {
        return $customised;  // We're done!
    }

    // Get the system default page
    if (!$systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => $private))) {
        return false;  // error
    }

    // Clone the basic system page record
    $page = clone($systempage);
    unset($page->id);
    $page->userid = $userid;
    $page->id = $DB->insert_record('my_pages', $page);

    // Clone ALL the associated blocks as well
    $systemcontext = context_system::instance();
    $usercontext = context_user::instance($userid);

    $blockinstances = $DB->get_records('block_instances', array('parentcontextid' => $systemcontext->id,
                                                                'pagetypepattern' => $pagetype,
                                                                'subpagepattern' => $systempage->id));
    foreach ($blockinstances as $instance) {
        $originalid = $instance->id;
        unset($instance->id);
        $instance->parentcontextid = $usercontext->id;
        $instance->subpagepattern = $page->id;
        $instance->id = $DB->insert_record('block_instances', $instance);
        $blockcontext = context_block::instance($instance->id);  // Just creates the context record
        $block = block_instance($instance->blockname, $instance);
        if (!$block->instance_copy($originalid)) {
            debugging("Unable to copy block-specific data for original block instance: $originalid
                to new block instance: $instance->id", DEBUG_DEVELOPER);
        }
    }

    // FIXME: block position overrides should be merged in with block instance
    //$blockpositions = $DB->get_records('block_positions', array('subpage' => $page->name));
    //foreach($blockpositions as $positions) {
    //    $positions->subpage = $page->name;
    //    $DB->insert_record('block_positions', $tc);
    //}

    return $page;
}

/*
 * For a given user, this deletes their My Moodle page and returns them to the system default.
 *
 * @param int $userid the id of the user whose page should be reset
 * @param int $private either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC
 * @param string $pagetype either my-index or user-profile
 * @return mixed system page, or false on error
 */
function my_reset_page($userid, $private=MY_PAGE_PRIVATE, $pagetype='my-index') {
    global $DB, $CFG;

    $page = my_get_page($userid, $private);
    if ($page->userid == $userid) {
        $context = context_user::instance($userid);
        if ($blocks = $DB->get_records('block_instances', array('parentcontextid' => $context->id,
                'pagetypepattern' => $pagetype))) {
            foreach ($blocks as $block) {
                if (is_null($block->subpagepattern) || $block->subpagepattern == $page->id) {
                    blocks_delete_instance($block);
                }
            }
        }
        $DB->delete_records('my_pages', array('id' => $page->id));
    }

    // Get the system default page
    if (!$systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => $private))) {
        return false; // error
    }
    return $systempage;
}

/**
 * Resets the page customisations for all users.
 *
 * @param int $private Either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC.
 * @param string $pagetype Either my-index or user-profile.
 * @return void
 */
function my_reset_page_for_all_users($private = MY_PAGE_PRIVATE, $pagetype = 'my-index') {
    global $DB;

    // Find all the user pages.
    $where = 'userid IS NOT NULL AND private = :private';
    $params = array('private' => $private);
    $pages = $DB->get_recordset_select('my_pages', $where, $params, 'id, userid');
    $pageids = array();
    $blockids = array();

    foreach ($pages as $page) {
        $pageids[] = $page->id;
        $usercontext = context_user::instance($page->userid);

        // Find all block instances in that page.
        $blocks = $DB->get_recordset('block_instances', array('parentcontextid' => $usercontext->id,
            'pagetypepattern' => $pagetype), '', 'id, subpagepattern');
        foreach ($blocks as $block) {
            if (is_null($block->subpagepattern) || $block->subpagepattern == $page->id) {
                $blockids[] = $block->id;
            }
        }
        $blocks->close();
    }
    $pages->close();

    // Wrap the SQL queries in a transaction.
    $transaction = $DB->start_delegated_transaction();

    // Delete the block instances.
    if (!empty($blockids)) {
        blocks_delete_instances($blockids);
    }

    // Finally delete the pages.
    if (!empty($pageids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($pageids);
        $DB->delete_records_select('my_pages', "id $insql", $pageids);
    }

    // We should be good to go now.
    $transaction->allow_commit();
}

class my_syspage_block_manager extends block_manager {
    // HACK WARNING!
    // TODO: figure out a better way to do this
    /**
     * Load blocks using the system context, rather than the user's context.
     *
     * This is needed because the My Moodle pages set the page context to the
     * user's context for access control, etc.  But the blocks for the system
     * pages are stored in the system context.
     */
    public function load_blocks($includeinvisible = null) {
        $origcontext = $this->page->context;
        $this->page->context = context_system::instance();
        parent::load_blocks($includeinvisible);
        $this->page->context = $origcontext;
    }
}
