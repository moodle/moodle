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
 * Base class for unit tests for core_rss.
 *
 * @package    core_rss
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \core_rss\privacy\provider;
use \core_privacy\local\request\writer;

/**
 * Unit tests for rss\classes\privacy\provider.php
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_rss_testcase extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $key = get_user_key('rss', $user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'core_rss');
        $data = $writer->get_related_data([get_string('rss', 'rss'), $user->id], 'user_private_key-' . $key->id);
        $this->assertEquals($key->value, $data->value);
    }
}
