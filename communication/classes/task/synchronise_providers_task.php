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

namespace core_communication\task;

use core\task\scheduled_task;
use core_communication\api;
use core_communication\processor;

/**
 * Class synchronise_providers to add a task to synchronise the providers and execute the task to action the synchronisation.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class synchronise_providers_task extends scheduled_task {

    public function get_name() {
        return get_string('synchroniseproviders', 'core_communication');
    }

    public function execute() {
        // Communication is not enabled? nothing to do.
        if (!api::is_available()) {
            return;
        }

        global $DB;
        $communicationinstances = $DB->get_records(
            table: 'communication',
            conditions: ['active' => processor::PROVIDER_ACTIVE],
        );

        foreach ($communicationinstances as $communicationinstance) {
            $communication = \core_communication\api::load_by_instance(
                context: \context::instance_by_id($communicationinstance->contextid),
                component: $communicationinstance->component,
                instancetype: $communicationinstance->instancetype,
                instanceid: $communicationinstance->instanceid,
            );
            $processor = $communication->get_processor();
            if ($processor->supports_sync_provider_features()) {
                $communication->sync_provider();
            }
        }
    }
}
