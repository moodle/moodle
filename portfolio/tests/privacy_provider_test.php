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
 * Privacy provider tests.
 *
 * @package    core_portfolio
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_privacy_provider_test extends \core_privacy\tests\provider_testcase {

    protected function create_portfolio_data($plugin, $name, $user, $preference, $value) {
        global $DB;
        $portfolioinstance = (object) [
            'plugin' => $plugin,
            'name' => $name,
            'visible' => 1
        ];
        $portfolioinstance->id = $DB->insert_record('portfolio_instance', $portfolioinstance);
        $userinstance = (object) [
            'instance' => $portfolioinstance->id,
            'userid' => $user->id,
            'name' => $preference,
            'value' => $value
        ];
        $DB->insert_record('portfolio_instance_user', $userinstance);
    }

    /**
     *  Verify that a collection of metadata is returned for this component and that it just returns the righ types for 'portfolio'.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('core_portfolio');
        $collection = \core_portfolio\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
        $items = $collection->get_collection();
        $this->assertEquals(2, count($items));
        $this->assertInstanceOf(\core_privacy\local\metadata\types\database_table::class, $items[0]);
        $this->assertInstanceOf(\core_privacy\local\metadata\types\plugintype_link::class, $items[1]);
    }

    /**
     * Test that the export for a user id returns a user context.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        $this->create_portfolio_data('googledocs', 'Google Docs', $user, 'visible', 1);
        $contextlist = \core_portfolio\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that exporting user data works as expected.
     */
    public function test_export_user_data() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        $this->create_portfolio_data('googledocs', 'Google Docs', $user, 'visible', 1);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user, 'core_portfolio', [$context->id]);
        \core_portfolio\privacy\provider::export_user_data($contextlist);
        $writer = \core_privacy\local\request\writer::with_context($context);
        $portfoliodata = $writer->get_data([get_string('privacy:path', 'portfolio')]);
        $this->assertEquals('Google Docs', $portfoliodata->{'Google Docs'}->name);
    }

    /**
     * Test that deleting only results in the one context being removed.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_portfolio_data('googledocs', 'Google Docs', $user1, 'visible', 1);
        $this->create_portfolio_data('onedrive', 'Microsoft onedrive', $user2, 'visible', 1);
        // Check a system context sent through.
        $systemcontext = context_system::instance();
        \core_portfolio\privacy\provider::delete_data_for_all_users_in_context($systemcontext);
        $records = $DB->get_records('portfolio_instance_user');
        $this->assertCount(2, $records);
        $context = context_user::instance($user1->id);
        \core_portfolio\privacy\provider::delete_data_for_all_users_in_context($context);
        $records = $DB->get_records('portfolio_instance_user');
        // Only one entry should remain for user 2.
        $this->assertCount(1, $records);
        $data = array_shift($records);
        $this->assertEquals($user2->id, $data->userid);
    }

    /**
     * Test that deleting only results in one user's data being removed.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_portfolio_data('googledocs', 'Google Docs', $user1, 'visible', 1);
        $this->create_portfolio_data('onedrive', 'Microsoft onedrive', $user2, 'visible', 1);

        $records = $DB->get_records('portfolio_instance_user');
        $this->assertCount(2, $records);

        $context = context_user::instance($user1->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_portfolio', [$context->id]);
        \core_portfolio\privacy\provider::delete_data_for_user($contextlist);
        $records = $DB->get_records('portfolio_instance_user');
        // Only one entry should remain for user 2.
        $this->assertCount(1, $records);
        $data = array_shift($records);
        $this->assertEquals($user2->id, $data->userid);
    }
}
