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

namespace tool_policy;

use tool_policy\privacy\local\sitepolicy\handler;
use tool_policy\test\helper;

/**
 * Unit tests for the {@link \tool_policy\privacy\local\sitepolicy\handler} class.
 *
 * @package     tool_policy
 * @category    test
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sitepolicy_handler_test extends \advanced_testcase {

    /**
     * Test behaviour of the {@link \tool_policy\privacy\local\sitepolicy\handler::get_redirect_url()} method.
     */
    public function test_get_redirect_url() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // No redirect for guests.
        $this->assertNull(handler::get_redirect_url(true));

        // No redirect if there is no policy.
        $this->assertNull(handler::get_redirect_url());

        // No redirect if no policy for logged in users.
        $policy1 = helper::add_policy(['audience' => policy_version::AUDIENCE_GUESTS])->to_record();
        api::make_current($policy1->id);
        $this->assertNull(handler::get_redirect_url());

        // URL only when there is actually some policy to show.
        $policy2 = helper::add_policy(['audience' => policy_version::AUDIENCE_LOGGEDIN])->to_record();
        api::make_current($policy2->id);
        $this->assertInstanceOf('moodle_url', handler::get_redirect_url());
    }

    /**
     * Test behaviour of the {@link \tool_policy\privacy\local\sitepolicy\handler::get_embed_url()} method.
     */
    public function test_get_embed_url() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // No embed if there is no policy.
        $this->assertNull(handler::get_embed_url());
        $this->assertNull(handler::get_embed_url(true));

        $policy1 = helper::add_policy(['audience' => policy_version::AUDIENCE_GUESTS])->to_record();
        api::make_current($policy1->id);

        // Policy exists for guests only.
        $this->assertNull(handler::get_embed_url());
        $this->assertInstanceOf('moodle_url', handler::get_embed_url(true));

        $policy2 = helper::add_policy(['audience' => policy_version::AUDIENCE_LOGGEDIN])->to_record();
        api::make_current($policy2->id);

        // Some policy exists for all users.
        $this->assertInstanceOf('moodle_url', handler::get_embed_url());
        $this->assertInstanceOf('moodle_url', handler::get_embed_url(true));
    }

    /**
     * Test behaviour of the {@link \tool_policy\privacy\local\sitepolicy\handler::accept()} method.
     */
    public function test_accept() {
        global $DB, $USER;
        $this->resetAfterTest();

        // False if not logged in.
        $this->setUser(0);
        $this->assertFalse(handler::accept());

        // Guests accept policies implicitly by continuing to use the site.
        $this->setGuestUser();
        $this->assertTrue(handler::accept());

        // Create one compulsory and one optional policy.
        $this->setAdminUser();
        $policy1 = helper::add_policy(['optional' => policy_version::AGREEMENT_COMPULSORY])->to_record();
        api::make_current($policy1->id);
        $policy2 = helper::add_policy(['optional' => policy_version::AGREEMENT_OPTIONAL])->to_record();
        api::make_current($policy2->id);

        $user1 = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
        $this->assertEmpty($DB->get_records('tool_policy_acceptances', ['userid' => $user1->id]));

        $this->setUser($user1->id);
        $this->assertEquals(0, $USER->policyagreed);

        // Only the compulsory policy is marked as accepted when accepting via the handler.
        $this->assertTrue(handler::accept());
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $user1->id]));
        $this->assertEquals(1, $USER->policyagreed);
        $this->assertEquals(1, $DB->count_records('tool_policy_acceptances', ['userid' => $user1->id]));
        $this->assertTrue($DB->record_exists('tool_policy_acceptances', ['userid' => $user1->id,
            'policyversionid' => $policy1->id]));
    }

    /**
     * Test presence of the {@link \tool_policy\privacy\local\sitepolicy\handler::signup_form()} method.
     */
    public function test_signup_form() {
        $this->assertTrue(method_exists('\tool_policy\privacy\local\sitepolicy\handler', 'signup_form'));
    }
}
