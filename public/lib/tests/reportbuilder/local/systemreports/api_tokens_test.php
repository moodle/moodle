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

namespace core\reportbuilder\local\systemreports;

use core\api\repository\api_token_repository;
use core\context\system;
use core_reportbuilder\system_report_factory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for {@see api_tokens}.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(api_tokens::class)]
#[CoversClass(\core\reportbuilder\local\entities\api_token::class)]
final class api_tokens_test extends \advanced_testcase {
    /**
     * Create a token for the current user and render the report as they would see it.
     *
     * @param int $expirytime The expiry to give the token.
     * @param string $name The name to give it.
     * @return string The rendered report.
     */
    private function render_report_with_token(int $expirytime, string $name = 'Gradebook sync'): string {
        global $PAGE, $USER;

        // Through the repository rather than by hand, so the row is what the write path stores.
        (new api_token_repository())->create_token(
            $name,
            'irrelevant-secret',
            (int) $USER->id,
            'core_grades:grade:read',
            null,
            $expirytime,
        );

        $PAGE->set_url('/user/personalaccesstokens.php');

        return system_report_factory::create(api_tokens::class, system::instance())->output();
    }

    /**
     * An active token offers revoking, through the modal that warns about losing access.
     */
    public function test_active_token_offers_revoke(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $html = $this->render_report_with_token(time() + DAYSECS);

        $this->assertStringContainsString('data-modal-yes-button="' . get_string('pat_revoke') . '"', $html);
        $this->assertStringNotContainsString('data-modal-yes-button="' . get_string('pat_delete') . '"', $html);
        // Revoking cuts off working access, so it gets the destructive modal.
        $this->assertStringContainsString('data-modal-type="delete"', $html);
    }

    /**
     * An expired token offers deleting instead: there is no access left to cut off.
     */
    public function test_expired_token_offers_delete(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $html = $this->render_report_with_token(time() - DAYSECS);

        $this->assertStringContainsString('data-modal-yes-button="' . get_string('pat_delete') . '"', $html);
        $this->assertStringNotContainsString('data-modal-yes-button="' . get_string('pat_revoke') . '"', $html);
        // Nothing is cut off by removing a token that already lapsed.
        $this->assertStringNotContainsString('data-modal-type="delete"', $html);
    }

    /**
     * The row posts its removal, names the token it would remove, and offers no URL that removes.
     */
    public function test_row_action_posts_and_is_named(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $html = $this->render_report_with_token(time() + DAYSECS);
        $id = $DB->get_field('rest_api_tokens', 'id', ['userid' => $USER->id], MUST_EXIST);

        // A posted form carrying what the page needs, rather than a link that carries it.
        $this->assertStringContainsString('method="post"', $html);
        $this->assertStringContainsString('name="confirm" value="1"', $html);
        $this->assertStringContainsString('name="sesskey" value="' . sesskey() . '"', $html);
        $this->assertStringContainsString('name="id" value="' . $id . '"', $html);
        $this->assertStringContainsString(s(get_string('pat_revokeconfirm', '', 'Gradebook sync')), $html);

        // No destination, so the modal resubmits the button instead of navigating: nothing in the
        // table is a URL that removes a token if it is followed.
        $this->assertStringNotContainsString('data-modal-destination', $html);
        $this->assertStringNotContainsString('confirm=1', $html);
        $this->assertStringNotContainsString('sesskey=' . sesskey(), $html);
    }

    /**
     * The dates a token carries are all shown, and the created date explains whose clock it is on.
     */
    public function test_created_column_explains_the_time_zone(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $html = $this->render_report_with_token(time() + DAYSECS);

        $this->assertStringContainsString(get_string('pat_timecreated'), $html);
        $this->assertStringContainsString(get_string('pat_lastaccess'), $html);

        // Both date columns explain themselves, and each help icon carries the viewer's own
        // zone rather than the server's.
        $timezone = \core_date::get_user_timezone();
        $this->assertStringContainsString(s(get_string('pat_timecreated_help', 'core', $timezone)), $html);
        $this->assertStringContainsString(s(get_string('pat_validuntil_help', 'core', $timezone)), $html);
    }
}
