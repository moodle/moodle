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
 *
 * @package    local_intellidata
 * @category   task
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\task;


use local_intellidata\helpers\DBManagerHelper;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\repositories\config_repository;
use local_intellidata\services\datatypes_service;

/**
 * Task to create index in table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_index_adhoc_task extends \core\task\adhoc_task {

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        if (TrackingHelper::enabled()) {

            DebugHelper::enable_moodle_debug();

            $data = $this->get_custom_data();

            if (!empty($data->datatype)) {

                $configrepository = new config_repository();
                $config = $configrepository->get_record(['datatype' => $data->datatype]);
                $config = $config->to_record();

                $datatypes = datatypes_service::get_all_datatypes();

                if (!empty($config->tableindex) && !empty($datatypes[$data->datatype]['table'])) {

                    $table = $datatypes[$data->datatype]['table'];
                    $index = $config->tableindex;

                    mtrace('IntelliData: Creating DB index "' . $index . '" in table "' . $table . '"');

                    DBManagerHelper::create_index($table, $index);
                }

            }
        }
    }
}
