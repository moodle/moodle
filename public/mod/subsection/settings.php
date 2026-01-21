<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     mod_subsection
 * @category    admin
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('mod_subsection_settings', new lang_string('pluginname', 'mod_subsection'));

    if ($ADMIN->fulltree) {
        // Add description cleanup and migration links.
        $count = $DB->count_records_select(
            table: 'course_sections',
            select: 'component = :component AND summary != :empty',
            params: ['component' => 'mod_subsection', 'empty' => ''],
        );
        $task = \core\task\manager::get_queued_adhoc_task_record(new \mod_subsection\task\migrate_subsection_descriptions_task());
        if ($task) {
            // There is a pending migration task, show notification and pending count.
            $notification = $OUTPUT->notification(
                get_string('descriptionsmigratedsuccess', 'mod_subsection'),
                \core\output\notification::NOTIFY_SUCCESS,
            );
            $settings->add(new admin_setting_heading(
                'migratedescriptionsnotification',
                '',
                $notification,
            ));
            $settings->add(new admin_setting_heading(
                'pendingcleandescriptions',
                '',
                new lang_string('descriptionsmigratedpending', 'mod_subsection', $count),
            ));
        } else if ($count > 0) {
            // Show migration and deletion links.
            $migrateaction = new \confirm_action(
                message: get_string('migrateconfirmtext', 'mod_subsection', $count),
                continuelabel: get_string('migrateconfirmbutton', 'mod_subsection'),
                title: get_string('migrateconfirmtitle', 'mod_subsection'),
            );
            $migrateurl = new moodle_url(
                '/mod/subsection/cleandescriptions.php',
                ['action' => 'migrate', 'count' => $count, 'sesskey' => sesskey()],
            );
            $migratelink = $OUTPUT->action_link(
                url: $migrateurl,
                text: get_string('migratelinktext', 'mod_subsection'),
                action: $migrateaction,
                attributes: ['class' => 'btn btn-secondary'],
            );

            $deleteaction = new \confirm_action(
                message: get_string('deleteconfirmtext', 'mod_subsection', $count),
                continuelabel: get_string('deleteconfirmbutton', 'mod_subsection'),
                title: get_string('deleteconfirmtitle', 'mod_subsection'),
                dialogtype: 'delete',
            );
            $deleteurl = new moodle_url(
                '/mod/subsection/cleandescriptions.php',
                ['action' => 'delete', 'count' => $count, 'sesskey' => sesskey()],
            );
            $deletelink = $OUTPUT->action_link(
                url: $deleteurl,
                text: get_string('deletelinktext', 'mod_subsection'),
                action: $deleteaction,
                attributes: ['class' => 'btn btn-secondary'],
            );

            $settings->add(new admin_setting_heading(
                'cleandescriptions',
                '',
                new lang_string(
                    'cleandescriptionsdetail',
                    'mod_subsection',
                    [
                        'count' => $count,
                        'migratelink' => $migratelink,
                        'deletelink' => $deletelink,
                    ],
                ),
            ));
        }
    }
}
