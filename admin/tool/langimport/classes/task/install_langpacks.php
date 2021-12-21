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

namespace tool_langimport\task;

/**
 * Ad hoc task to install one or more language packs.
 *
 * @package     tool_langimport
 * @category    task
 * @copyright   2021 David Mudrák <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class install_langpacks extends \core\task\adhoc_task {

    /**
     * Execute the ad hoc task.
     */
    public function execute(): void {

        $data = $this->get_custom_data();

        if (empty($data->langs)) {
            mtrace('No language packs to install');
        }

        get_string_manager()->reset_caches();

        $controller = new \tool_langimport\controller();

        \core_php_time_limit::raise();

        try {
            $controller->install_languagepacks($data->langs);
            $this->notify_user_success($controller);

        } catch (\Throwable $e) {
            $this->notify_user_error($e->getMessage());

        } finally {
            get_string_manager()->reset_caches();
        }
    }

    /**
     * Notify user that the task finished successfully.
     *
     * @param \tool_langimport\controller $controller
     */
    protected function notify_user_success(\tool_langimport\controller $controller): void {

        $message = new \core\message\message();

        $message->component = 'moodle';
        $message->name = 'notices';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $this->get_userid();
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/admin/tool/langimport/index.php'))->out(false);
        $message->contexturlname = get_string('pluginname', 'tool_langimport');

        $message->subject = get_string('installfinished', 'tool_langimport');
        $message->fullmessage = '* ' . implode(PHP_EOL . '* ', $controller->info);
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = markdown_to_html($message->fullmessage);
        $message->smallmessage = get_string('installfinished', 'tool_langimport');

        message_send($message);
    }

    /**
     * Notify user that the task failed.
     *
     * @param string $error The error text
     */
    protected function notify_user_error(string $error): void {

        $message = new \core\message\message();

        $message->component = 'moodle';
        $message->name = 'notices';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $this->get_userid();
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/admin/tool/langimport/index.php'))->out(false);
        $message->contexturlname = get_string('pluginname', 'tool_langimport');

        $message->subject = get_string('installfailed', 'tool_langimport');
        $message->fullmessage = $error;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = text_to_html($message->fullmessage);
        $message->smallmessage = get_string('installfailed', 'tool_langimport');

        message_send($message);
    }
}
