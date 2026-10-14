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

namespace core\task;

use core\api\entity\api_token_entity;
use core\api\repository\api_token_repository;

/**
 * Queues the expiry notices due on personal access tokens, one task per owner.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class personal_access_token_expiry_task extends scheduled_task {
    /**
     * Create the task, with its dependencies injected by the task manager.
     *
     * @param api_token_repository $repository The repository which finds the tokens due a notice.
     */
    public function __construct(
        /** @var api_token_repository The repository which finds the tokens due a notice. */
        protected readonly api_token_repository $repository,
    ) {
    }

    #[\Override]
    public function get_name(): string {
        return get_string('taskpersonalaccesstokenexpiry', 'admin');
    }

    #[\Override]
    public function execute(): void {
        global $DB;

        $tokens = array_merge($this->repository->get_tokens_to_warn(), $this->repository->get_expired_tokens_to_notify());

        if (empty($tokens)) {
            return;
        }

        // Suspended and deleted owners get no task: the task runner would only cancel it.
        $userids = array_unique(array_map(fn(api_token_entity $token) => $token->get_userid(), $tokens));
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $ownerids = $DB->get_fieldset_select('user', 'id', "id {$insql} AND deleted = 0 AND suspended = 0", $params);

        foreach ($ownerids as $ownerid) {
            // Built the way the task manager builds it, so its dependencies are injected.
            $task = \core\di::make(send_personal_access_token_expiry_notices::class);
            $task->set_userid($ownerid);
            manager::queue_adhoc_task($task, true);
        }
    }
}
