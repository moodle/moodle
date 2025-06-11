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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\persistents\signature;

class block_quickmail_signature_persistent_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_sets_default_when_no_user_signature_exists() {
        $this->resetAfterTest(true);

        $signature = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'default_flag' => 0,
            'signature' => '<p>This is my signature!</p>',
        ]);

        $this->assertEquals(1, $signature->get('default_flag'));
        $this->assertTrue($signature->is_default());
    }

    public function test_changes_default_when_new_signature_is_created_as_default() {
        $this->resetAfterTest(true);

        // Create (default) signature.
        $signature1 = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'default_flag' => 0,
            'signature' => '<p>This is my signature!</p>',
        ]);

        $this->assertTrue($signature1->is_default());

        // Create new (default) signature.
        $signature2 = signature::create_new([
            'user_id' => 1,
            'title' => 'second',
            'default_flag' => 1,
            'signature' => '<p>This is another signature!</p>',
        ]);

        // Refresh the first signature.
        $signature1->read();

        $this->assertTrue($signature2->is_default());
        $this->assertFalse($signature1->is_default());
    }

    public function test_makes_another_signature_default_when_default_is_deleted() {
        $this->resetAfterTest(true);

        // Create (default) signature.
        $signature1 = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'default_flag' => 0,
            'signature' => '<p>This is my signature!</p>',
        ]);

        $this->assertTrue($signature1->is_default());

        // Create new (non-default) signature.
        $signature2 = signature::create_new([
            'user_id' => 1,
            'title' => 'second',
            'default_flag' => 0,
            'signature' => '<p>This is another signature!</p>',
        ]);

        $this->assertFalse($signature2->is_default());

        // Delete the current default.
        $signature1->delete();

        // Refresh the second signature.
        $signature2->read();

        $this->assertFalse($signature1->is_default());
        $this->assertTrue($signature2->is_default());
    }

    public function test_finds_default_signature_for_user() {
        $this->resetAfterTest(true);

        $signature1 = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'signature' => '<p>This is my signature!</p>',
        ]);

        $signature2 = signature::create_new([
            'user_id' => 1,
            'title' => 'second',
            'signature' => '<p>This is another signature!</p>',
        ]);

        $signature3 = signature::create_new([
            'user_id' => 1,
            'title' => 'third',
            'signature' => '<p>This is yet another signature!</p>',
        ]);

        $default = signature::get_default_signature_for_user(1);

        $this->assertInstanceOf(signature::class, $default);
        $this->assertEquals($default->get('id'), $signature1->get('id'));
    }

    public function test_gets_signature_scoped_to_user() {
        $this->resetAfterTest(true);

        $signature11 = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'signature' => '<p>This is my signature!</p>',
        ]);

        $signature12 = signature::create_new([
            'user_id' => 1,
            'title' => 'second',
            'signature' => '<p>This is another signature!</p>',
        ]);

        $signature21 = signature::create_new([
            'user_id' => 2,
            'title' => 'first',
            'signature' => '<p>This is my signature!</p>',
        ]);

        // Get a signature belonging to the user.
        $signature = signature::find_user_signature_or_null($signature11->get('id'), 1);

        $this->assertInstanceOf(signature::class, $signature);
        $this->assertEquals($signature->get('id'), $signature11->get('id'));

        // Attempt to get a signature that belongs to a different user.
        $signature = signature::find_user_signature_or_null($signature11->get('id'), 2);

        $this->assertNull($signature);
    }

    public function test_gets_user_signatures_as_array() {
        $this->resetAfterTest(true);

        $signature1 = signature::create_new([
            'user_id' => 1,
            'title' => 'first',
            'signature' => '<p>This is my signature!</p>',
        ]);

        $signature2 = signature::create_new([
            'user_id' => 1,
            'title' => 'second',
            'signature' => '<p>This is another signature!</p>',
        ]);

        $signature3 = signature::create_new([
            'user_id' => 1,
            'title' => 'third',
            'signature' => '<p>This is yet another signature!</p>',
        ]);

        $signatures = signature::get_flat_array_for_user(1);

        $this->assertIsArray($signatures);
        $this->assertCount(3, $signatures);
    }

}
