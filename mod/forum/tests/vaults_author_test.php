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

namespace mod_forum;

/**
 * The author vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vaults_author_test extends \advanced_testcase {
    /**
     * Test get_from_id.
     */
    public function test_get_from_id(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $authorvault = $vaultfactory->get_author_vault();

        $author = $authorvault->get_from_id($user->id);

        $this->assertEquals($user->id, $author->get_id());
    }

    /**
     * Test get_context_ids_for_author_ids.
     */
    public function test_get_context_ids_for_author_ids(): void {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $userid1 = $user1->id;
        $userid2 = $user2->id;
        $userid3 = $user3->id;
        $fakeuserid = $user3->id + 1000;
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $authorvault = $vaultfactory->get_author_vault();
        $user1context = \context_user::instance($user1->id);
        $user2context = \context_user::instance($user2->id);
        $user3context = \context_user::instance($user3->id);
        $user1contextid = $user1context->id;
        $user2contextid = $user2context->id;
        $user3contextid = $user3context->id;
        $fakeusercontextid = null;
        $userids = [$userid1, $userid2, $userid3, $fakeuserid];

        $expected = [
            $userid1 => $user1contextid,
            $userid2 => $user2contextid,
            $userid3 => $user3contextid,
            $fakeuserid => $fakeusercontextid
        ];

        $this->assertEquals($expected, $authorvault->get_context_ids_for_author_ids($userids));
    }
}
