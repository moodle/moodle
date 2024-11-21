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

namespace block_xp\task;

use block_xp\di;

/**
 * Admin notices.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_notices extends \core\task\scheduled_task {

    /**
     * Get name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskadminnotices', 'block_xp');
    }

    /**
     * Execute.
     */
    public function execute() {
        $config = di::get('config');

        if (!$config->get('adminnotices')) {
            mtrace('Admin notices are disabled, disabling task...');
            static::set_enabled(false);
            return;
        }

        // No add-on, nothing to do so far.
        $addon = di::get('addon');
        if (!$addon->is_activated()) {
            return;
        }

        $pluginman = \core_plugin_manager::instance();
        $blockxp = $pluginman->get_plugin_info('block_xp');
        $localxp = $pluginman->get_plugin_info('local_xp');

        // That's odd, bail!
        if (!$blockxp || !$localxp) {
            mtrace('Plugins were not located, abandoning!');
            return;
        }

        $this->execute_out_of_sync_notices($blockxp, $localxp);
    }

    /**
     * Execute out of sync notices.
     *
     * @param \core\plugininfo\base $blockxp The info.
     * @param \core\plugininfo\base $localxp The info.
     */
    protected function execute_out_of_sync_notices($blockxp, $localxp) {
        $config = di::get('config');
        $addon = di::get('addon');

        if (!$addon->is_out_of_sync()) {
            return;
        }

        // Only send once per major version pair.
        $xpversion = (string) floor((int) $blockxp->versiondb / 100);
        $xppversion = (string) floor((int) $localxp->versiondb / 100);
        $key = "{$xpversion}:{$xppversion}";
        if ($config->get('lastoutofsyncnoticekey') === $key) {
            return;
        }

        $contenthtml = markdown_to_html(get_string('adminnoticeoutofsyncmessage', 'block_xp', [
            'blockxpversion' => $blockxp->release . ' (' . $blockxp->versiondb . ')',
            'localxpversion' => $localxp->release . ' (' . $localxp->versiondb . ')',
            'localxpversionexpected' => $addon->get_expected_release(),
        ]));
        $contentplain = html_to_text($contenthtml);
        $userfrom = \core_user::get_noreply_user();

        $users = get_admins();
        foreach ($users as $user) {
            try {
                $message = new \core\message\message();
                $message->component = 'block_xp';
                $message->name = 'adminnotice';
                $message->userfrom = $userfrom;
                $message->userto = $user;
                $message->subject = get_string('adminnoticeoutofsyncsubject', 'block_xp');
                $message->fullmessage = $contentplain;
                $message->fullmessageformat = FORMAT_PLAIN;
                $message->fullmessagehtml = $contenthtml;
                $message->notification = 1;
                message_send($message);
            } catch (\Throwable $e) {
                mtrace("Failed to send notice to {$user->username}: " . $e->getMessage());
            }
        }

        $config->set('lastoutofsyncnoticekey', $key);
    }

    /**
     * Enable or disable the task.
     *
     * @param bool $enabled Whether to enable the task.
     */
    public static function set_enabled($enabled) {
        $task = \core\task\manager::get_scheduled_task('\\' . static::class);
        if (!$task) {
            return;
        }
        $task->set_disabled(!$enabled);
        try {
            \core\task\manager::configure_scheduled_task($task);
        } catch (\moodle_exception $e) {
            return;
        }
    }

}
