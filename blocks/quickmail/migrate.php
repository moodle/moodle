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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/quickmail/lib.php');

// Authentication.
require_login();

// Must be a site admin to do this!!
if (!is_siteadmin()) {
    throw new moodle_exception('cannotuseadmin', 'error');
}

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/migrate.php'));

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('migrate'));
$PAGE->navbar->add(block_quickmail_string::get('pluginname'));
$PAGE->navbar->add(block_quickmail_string::get('migrate'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('migrate'));

echo $OUTPUT->header();

$taskurl = new moodle_url('/admin/tool/task/scheduledtasks.php', [
    'action' => 'edit',
    'task' => 'block_quickmail\\tasks\\migrate_legacy_data_task'
]);

$settingsurl = new moodle_url('/admin/settings.php', [
    'section' => 'blocksettingquickmail',
]);

// If old tables do not exist, either the process has completed -OR- the user has dropped the old tables.
if (!block_quickmail\migrator\migrator::old_tables_exist()) {
    echo '<h4>Migration Progress: Complete</h4>
        <p>It looks like the migration process is complete,
        or Quickmail\'s old tables no longer exist, so nothing else needs to be done.</p>';

    // Otherwise, there may still work to be done.
} else {

    // Pull the current numbers.
    $totaldraftcount = block_quickmail\migrator\migrator::total_count('drafts');
    $migrateddraftcount = block_quickmail\migrator\migrator::migrated_count('drafts');
    $totallogcount = block_quickmail\migrator\migrator::total_count('log');
    $migratedlogcount = block_quickmail\migrator\migrator::migrated_count('log');
    $taskenabled = block_quickmail\migrator\migrator::is_enabled();

    $status = 'not-begun';

    if ($totaldraftcount + $totallogcount == 0) {
        $status = 'nothing-to-migrate';
    } else if (($totaldraftcount + $totallogcount - $migrateddraftcount - $migratedlogcount) == 0) {
        $status = 'process-complete';
    } else if ($migrateddraftcount || $migratedlogcount) {
        $status = 'needs-more-work';
    }

    if ($status == 'nothing-to-migrate') {
        if ($taskenabled) {
            echo '<h4>Migration Progress: No Data To Migrate</h4>
                <p>This tool allows you to <strong>migrate historical data
                from Quickmail v1 to v2</strong>, but it looks like you have no
                historical data to migrate. However, please note that <strong>
                the scheduled migration task is still running</strong>.
                This will not harm anything, but you can safely disable it
                forever by going to
                <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
                in the admin panel and marking it as disabled.</p> ';
        } else {
            echo '<h4>Migration Progress: No Data To Migrate</h4>
                <p>This tool allows you to <strong>migrate historical data
                from Quickmail v1 to v2</strong>, but it looks like
                you have no historical data to migrate.</p>';
        }
    } else if ($status == 'process-complete') {
        if ($taskenabled) {
            echo '<h4>Migration Progress: Complete</h4>
                <p>It looks like all of your old Quickmail data has been migrated
                over to the new version, congratulations! However, please note that
                <strong> the scheduled migration task is still running</strong>.
                This will not harm anything, but you can safely disable it forever.
                If you want to disable the task, you can go to
                <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
                in the admin panel and mark it as disabled.</p>';
        } else {
            echo '<h4>Migration Progress: Complete</h4>
                <p>It looks like all of your old Quickmail data has been migrated over
                to the new version and there is nothing left to do, congratulations!</p>';
        }

    } else if ($status == 'needs-more-work' && ! $taskenabled) {
        echo '<h4>Migration Progress: Disabled But Incomplete</h4>
            <p>This tool allows you to <strong>migrate historical data from
            Quickmail v1 to v2</strong>. It looks like the process was started,
            but <strong>is now currently disabled</strong>. If you want
            to continue the migration process, please enable the
            <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
            in the admin panel, then come back to this page to see the progress.</p>';
    } else if (!$taskenabled) {
        echo '<h4>Migration Progress: Not Enabled</h4>
            <p>Things have changed in Quickmail. This tool allows you to
            <strong>migrate historical data from Quickmail v1 to v2</strong>.
            If you want to do this, please enable the
            <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
            in the admin panel, then come back to this page to see the progress.</p>';
    } else {
        echo '<h4>Migration Progress: Working</h4>
            <p>This process is currently running. If you want to stop
            this process, please disable the
            <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
            in the admin panel. If you want to speed things up, you
            can try to increase the "Migration Chunk Size" in the
            <a href="' . $settingsurl . '">Quickmail settings</a>.
            <i>Note: If you disable the task right now, any data that has been
            migrated up to this point will be retained.</i></p>';

        // Draft migration status.
        $bar = new progress_bar('drafts_bar', 500, true);
        $bar->update($migrateddraftcount,
            $totaldraftcount,
            'Drafts (' . number_format($migrateddraftcount) . ' / ' . number_format($totaldraftcount) . ')');

        // Log migration status.
        $bar = new progress_bar('log_bar', 500, true);
        $bar->update($migratedlogcount,
            $totallogcount,
            'Logs (' . number_format($migratedlogcount) . ' / ' . number_format($totallogcount) . ')');
    }

    if (in_array($status, ['process-complete', 'nothing-to-migrate'])) {

        echo '<br><br><h4>Delete Old Tables?</h4>';

        if ($status == 'process-complete') {
            echo '<p>Since this process is complete, you can now safely drop the old tables:</p>';
        } else {
            echo '<p>Since there is nothing to migrate, you can now safely drop the old tables:</p>';
        }

        echo '
            <pre>block_quickmail_log</pre>
            <pre>block_quickmail_drafts</pre>
        ';

        if ($taskenabled) {
            echo '<p>If you do wish to drop these tables,
                 please disable this migration task first by going to
                 <a href="' . $taskurl . '">block_quickmail\tasks\migrate_legacy_data_task</a>
                 in the admin panel and marking it as disabled.';
        } else {
            echo '<p>Click the button below to automatically remove them now,
                 or come back to this page at any time to do so.</p>';

            // Display a form to allow for deletion of old tables.
            $mform = new block_quickmail\forms\migration_post_actions_form();

            if ($mform->is_cancelled()) {
                // Should not happen, but don't do anything just in case.
                redirect($PAGE->url);
            } else if ($submission = $mform->get_data()) {
                if (property_exists($submission, 'submitbutton')) {
                    try {
                        block_quickmail\migrator\migrator::drop_old_tables();
                        redirect($PAGE->url, 'Old tables have been successfully removed!', 'success');
                    } catch (\Exception $e) {
                        redirect($PAGE->url, $e->getMessage(), 'error');
                    }
                }
            } else {
                $mform->display();
            }
        }
    }
}

echo $OUTPUT->footer();
