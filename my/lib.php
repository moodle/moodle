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
define('MY_PAGE_DEFAULT', '__default');
define('MY_PAGE_COURSES', '__courses');

require_once("$CFG->libdir/blocklib.php");

/**
 * For a given user, this returns the $page information for their My Moodle page
 *
 * @param int|null $userid the id of the user whose page should be retrieved
 * @param int|null $private either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC
 * @param string|null $pagename Differentiate between standard /my or /courses pages.
 */
function my_get_page(?int $userid, int $private = MY_PAGE_PRIVATE, string $pagename = MY_PAGE_DEFAULT) {
    global $DB, $CFG;

    if (empty($CFG->forcedefaultmymoodle) && $userid) {  // Ignore custom My Moodle pages if admin has forced them
        // Does the user have their own page defined?  If so, return it.
        if ($customised = $DB->get_record(
            'my_pages',
            array('userid' => $userid, 'private' => $private, 'name' => $pagename),
            '*',
            IGNORE_MULTIPLE
        )) {
            return $customised;
        }
    }

    // Otherwise return the system default page
    return $DB->get_record('my_pages', array('userid' => null, 'name' => $pagename, 'private' => $private), '*', IGNORE_MULTIPLE);
}


/**
 * This copies a system default page to the current user
 *
 * @param int $userid the id of the user whose page should be reset
 * @param int $private either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC
 * @param string $pagetype either my-index or user-profile
 * @param string $pagename Differentiate between standard /my or /courses pages.
 */
function my_copy_page(
    int $userid,
    int $private = MY_PAGE_PRIVATE,
    string $pagetype = 'my-index',
    string $pagename = MY_PAGE_DEFAULT
) {
    global $DB;

    if ($customised = $DB->get_record(
        'my_pages',
        array('userid' => $userid, 'name' => $pagename, 'private' => $private),
        '*',
        IGNORE_MULTIPLE
    )) {
        return $customised;  // We're done!
    }

    // Get the system default page
    if (!$systempage = $DB->get_record(
        'my_pages',
        array('userid' => null, 'name' => $pagename, 'private' => $private),
        '*',
        IGNORE_MULTIPLE
    )) {
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
    $roles = get_all_roles();
    $newblockinstanceids = [];
    foreach ($blockinstances as $instance) {
        $originalid = $instance->id;
        $originalcontext = context_block::instance($originalid);
        unset($instance->id);
        $instance->parentcontextid = $usercontext->id;
        $instance->subpagepattern = $page->id;
        $instance->timecreated = time();
        $instance->timemodified = $instance->timecreated;
        $instance->id = $DB->insert_record('block_instances', $instance);
        $newblockinstanceids[$originalid] = $instance->id;
        $blockcontext = context_block::instance($instance->id);  // Just creates the context record
        $block = block_instance($instance->blockname, $instance);
        if (empty($block) || !$block->instance_copy($originalid)) {
            debugging("Unable to copy block-specific data for original block
                instance: $originalid to new block instance: $instance->id for
                block: $instance->blockname", DEBUG_DEVELOPER);
        }
        // Check if there are any overrides on this block instance.
        // We check against all roles, not just roles assigned to the user.
        // This is so any overrides that are applied to the system default page
        // will be applied to the user's page as well, even if their role assignment changes in the future.
        foreach ($roles as $role) {
            $rolecapabilities = get_capabilities_from_role_on_context($role, $originalcontext);
            // If there are overrides, then apply them to the new block instance.
            foreach ($rolecapabilities as $rolecapability) {
                role_change_permission(
                    $rolecapability->roleid,
                    $blockcontext,
                    $rolecapability->capability,
                    $rolecapability->permission
                );
            }
        }
    }

    // Clone block position overrides.
    if ($blockpositions = $DB->get_records('block_positions',
            ['subpage' => $systempage->id, 'pagetype' => $pagetype, 'contextid' => $systemcontext->id])) {
        foreach ($blockpositions as &$positions) {
            $positions->subpage = $page->id;
            $positions->contextid = $usercontext->id;
            if (array_key_exists($positions->blockinstanceid, $newblockinstanceids)) {
                // For block instances that were defined on the default dashboard and copied to the user dashboard
                // use the new blockinstanceid.
                $positions->blockinstanceid = $newblockinstanceids[$positions->blockinstanceid];
            }
            unset($positions->id);
        }
        $DB->insert_records('block_positions', $blockpositions);
    }

    return $page;
}

/**
 * For a given user, this deletes their My Moodle page and returns them to the system default.
 *
 * @param int $userid the id of the user whose page should be reset
 * @param int $private either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC
 * @param string $pagetype either my-index or user-profile
 * @param string $pagename Differentiate between standard /my or /courses pages.
 * @return mixed system page, or false on error
 */
function my_reset_page(
    int $userid,
    int $private = MY_PAGE_PRIVATE,
    string $pagetype='my-index',
    string $pagename = MY_PAGE_DEFAULT
) {
    global $DB, $CFG;

    $page = my_get_page($userid, $private, $pagename);
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
        $DB->delete_records('block_positions', ['subpage' => $page->id, 'pagetype' => $pagetype, 'contextid' => $context->id]);
        $DB->delete_records('my_pages', array('id' => $page->id, 'name' => $pagename));
    }

    // Get the system default page
    if (!$systempage = $DB->get_record(
        'my_pages',
        array('userid' => null, 'name' => $pagename, 'private' => $private),
        '*',
        IGNORE_MULTIPLE
    )) {
        return false; // error
    }

    // Trigger dashboard has been reset event.
    $eventparams = array(
        'context' => context_user::instance($userid),
        'other' => array(
            'private' => $private,
            'pagetype' => $pagetype,
        ),
    );
    $event = \core\event\dashboard_reset::create($eventparams);
    $event->trigger();
    return $systempage;
}

/**
 * Resets the page customisations for all users.
 *
 * @param int $private Either MY_PAGE_PRIVATE or MY_PAGE_PUBLIC.
 * @param string $pagetype Either my-index or user-profile.
 * @param progress_bar|null $progressbar A progress bar to update.
 * @param string $pagename Differentiate between standard /my or /courses pages.
 * @return void
 */
function my_reset_page_for_all_users(
    int $private = MY_PAGE_PRIVATE,
    string $pagetype = 'my-index',
    ?progress_bar $progressbar = null,
    string $pagename = MY_PAGE_DEFAULT
) {
    global $DB;

    // This may take a while. Raise the execution time limit.
    core_php_time_limit::raise();

    $users = $DB->get_fieldset_select(
        'my_pages',
        'DISTINCT(userid)',
        'userid IS NOT NULL AND private = :private',
        ['private' => $private]
    );
    $chunks = array_chunk($users, 20);

    if (!empty($progressbar) && count($chunks) > 0) {
        $count = count($chunks);
        $message = get_string('inprogress');
        $progressbar->update(0, $count, $message);
    }

    foreach ($chunks as $key => $userchunk) {
        list($infragment, $inparams) = $DB->get_in_or_equal($userchunk,  SQL_PARAMS_NAMED);
        // Find all the user pages and all block instances in them.
        $sql = "SELECT bi.id
                  FROM {my_pages} p
                  JOIN {context} ctx ON ctx.instanceid = p.userid AND ctx.contextlevel = :usercontextlevel
                  JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                   AND bi.pagetypepattern = :pagetypepattern
                   AND (bi.subpagepattern IS NULL OR bi.subpagepattern = " . $DB->sql_cast_to_char('p.id') . ")
                 WHERE p.private = :private
                   AND p.name = :name
                   AND p.userid $infragment";

        $params = array_merge([
            'private' => $private,
            'usercontextlevel' => CONTEXT_USER,
            'pagetypepattern' => $pagetype,
            'name' => $pagename
        ], $inparams);
        $blockids = $DB->get_fieldset_sql($sql, $params);

        // Wrap the SQL queries in a transaction.
        $transaction = $DB->start_delegated_transaction();

        // Delete the block instances.
        if (!empty($blockids)) {
            blocks_delete_instances($blockids);
        }

        // Finally delete the pages.
        $DB->delete_records_select(
            'my_pages',
            "userid $infragment AND private = :private",
            array_merge(['private' => $private], $inparams)
        );

        // We should be good to go now.
        $transaction->allow_commit();

        if (!empty($progressbar)) {
            $progressbar->update(((int) $key + 1), $count, $message);
        }
    }

    // Trigger dashboard has been reset event.
    $eventparams = array(
        'context' => context_system::instance(),
        'other' => array(
            'private' => $private,
            'pagetype' => $pagetype,
        ),
    );
    $event = \core\event\dashboards_reset::create($eventparams);
    $event->trigger();

    if (!empty($progressbar)) {
        $progressbar->update(1, 1, get_string('completed'));
    }
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
