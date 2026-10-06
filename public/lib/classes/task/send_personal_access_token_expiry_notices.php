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
use core\api\token_manager;
use core\url;

/**
 * Sends one owner the expiry notices due on their personal access tokens.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_personal_access_token_expiry_notices extends adhoc_task {
    /**
     * Create the task, with its dependencies injected by the task manager.
     *
     * @param api_token_repository $repository The repository which finds and marks the owner's tokens.
     */
    public function __construct(
        /** @var api_token_repository The repository which finds and marks the owner's tokens. */
        protected readonly api_token_repository $repository,
    ) {
    }

    #[\Override]
    public function execute(): void {
        global $USER;

        // The task runs as the owner, so the notices come out in their language and time zone.
        $this->send_notices(
            $this->repository->get_tokens_to_warn($USER->id),
            'pat_expiringnotice',
            fn(int $tokenid) => $this->repository->mark_expiry_warned($tokenid),
        );
        $this->send_notices(
            $this->repository->get_expired_tokens_to_notify($USER->id),
            'pat_expirednotice',
            fn(int $tokenid) => $this->repository->mark_expired_notified($tokenid),
        );
    }

    /**
     * Send the owner one notice per token, marking each token once its notice is sent.
     *
     * @param api_token_entity[] $tokens The owner's tokens to send a notice about.
     * @param string $stringprefix The prefix of the notice's subject and body strings.
     * @param callable $mark Records that a token's notice has been dealt with, given its id.
     * @return void
     */
    protected function send_notices(array $tokens, string $stringprefix, callable $mark): void {
        global $SITE, $USER;

        // The message provider requires the capability too, so an owner who has lost it is
        // skipped here rather than refused by message_send() with a debugging notice. Their
        // tokens stay unmarked, so they are still told if they regain it in time.
        if (!has_capability('moodle/api:createtoken', \context_system::instance())) {
            return;
        }

        $tokensurl = new url('/user/personalaccesstokens.php');

        foreach ($tokens as $token) {
            $message = new \core\message\message();
            $message->courseid = SITEID;
            $message->component = 'moodle';
            $message->name = 'personalaccesstokenexpiry';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $USER;
            $message->notification = 1;
            $message->subject = get_string("{$stringprefix}subject", 'moodle', $token->get_name());
            $message->fullmessageformat = FORMAT_HTML;
            $message->fullmessagehtml = get_string("{$stringprefix}body", 'moodle', (object) [
                'name' => s($token->get_name()),
                'expiry' => token_manager::format_datetime($token->get_expirytime()),
                'sitename' => format_string($SITE->fullname),
                'url' => $tokensurl->out(),
            ]);
            $message->fullmessage = html_to_text($message->fullmessagehtml);
            $message->smallmessage = $message->subject;
            $message->contexturl = $tokensurl->out(false);
            $message->contexturlname = get_string('personalaccesstokens');

            // Marked only once sent, and after sending, so a failed run repeats a notice rather
            // than losing one.
            if (message_send($message)) {
                $mark($token->get_id());
            }
        }
    }
}
