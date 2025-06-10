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
 * @copyright  2024 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\task;

use local_intellidata\helpers\DebugHelper;

/**
 * Task to reassign lti roles for users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2024 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_lti_role_adhoc_task extends \core\task\adhoc_task {

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        mtrace("Start reassign users roles!");

        DebugHelper::enable_moodle_debug();

        $data = $this->get_custom_data();
        $ids = !empty($data->ids) ? $data->ids : [];
        $roles = !empty($data->roles) ? $data->roles : [];

        (new \local_intellidata\services\lti_service())->set_lti_role($ids, $roles);

        mtrace("End reassign users roles!");
    }
}
