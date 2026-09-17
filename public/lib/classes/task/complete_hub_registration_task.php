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
 * Adhoc task that retries sending the full site registration payload to the hub.
 *
 * @package    core
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

use core\hub\api;
use core\hub\registration;

/**
 * Retries the post-confirmation hub_update_site_info call.
 *
 * Queued by {@see registration::confirm_registration()} when the initial attempt to send
 * the full site info to the hub fails, so a partial registration does not persist until the
 * next weekly {@see registration_cron_task}. Adhoc task failure handling automatically
 * reschedules this task with an increasing back-off if it keeps failing.
 *
 * @package    core
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class complete_hub_registration_task extends adhoc_task {
    /**
     * Send the full site info to the hub.
     *
     * @throws \moodle_exception if the hub call fails, so the task manager retries it.
     */
    public function execute() {
        if (!registration::is_registered()) {
            // Site is no longer registered (for example, it was unregistered in the meantime).
            return;
        }

        $this->send_full_registration();
    }

    /**
     * Send the full site info to the hub.
     *
     * Kept as a separate method so tests can mock the call to the hub without hitting the network.
     *
     * @throws \moodle_exception if the hub call fails, so the task manager retries it.
     */
    protected function send_full_registration(): void {
        api::update_registration(registration::get_site_info());
    }
}
