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
 * Essential is a clean and customizable theme.
 * Unit tests for the implementation of the privacy API.
 *
 * @package    theme_essential
 * @copyright  &copy; 2018-onwards G J Barnard based upon code originally written by Andrew Nicols.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \theme_essential\privacy\provider;

/**
 * Privacy unit tests for the Essential theme.
 * @group theme_essential
 */
class theme_essential_privacy_testcase extends \core_privacy\tests\provider_testcase {

    protected function set_up() {
        $this->resetAfterTest(true);

        set_config('theme', 'essential');
    }

    /**
     * Ensure that get_metadata exports valid content.
     */
    public function test_get_metadata() {
        $items = new collection('theme_essential');
        $result = provider::get_metadata($items);
        $this->assertSame($items, $result);
        $this->assertInstanceOf(collection::class, $result);
    }

    /**
     * Ensure that export_user_preferences returns no data if the user has not set the course search user preference.
     */
    public function test_export_user_preferences_no_pref() {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns request data.
     */
    public function test_export_user_preferences() {
        $this->set_up();
        $this->setAdminUser();

        set_user_preference('theme_essential_courseitemsearchtype', 1);

        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = $writer->get_user_preferences('theme_essential');

        $this->assertCount(1, (array)$prefs);

        $this->assertEquals(1, (((array)$prefs)['theme_essential_courseitemsearchtype'])->value);

        $description = get_string('privacy:request:preference:courseitemsearchtype', 'theme_essential', (object) [
            'name' => 'theme_essential_courseitemsearchtype',
            'value' => (((array)$prefs)['theme_essential_courseitemsearchtype'])->value
        ]);
        $this->assertEquals($description, (((array)$prefs)['theme_essential_courseitemsearchtype'])->description);

        // And for another user with a different value.
        $user = $this->getDataGenerator()->create_user();
        set_user_preference('theme_essential_courseitemsearchtype', 0, $user->id);
        provider::export_user_preferences($user->id);

        $prefs = $writer->get_user_preferences('theme_essential');

        $this->assertCount(1, (array)$prefs);

        $this->assertEquals(0, (((array)$prefs)['theme_essential_courseitemsearchtype'])->value);
        $description = get_string('privacy:request:preference:courseitemsearchtype', 'theme_essential', (object) [
            'name' => 'theme_essential_courseitemsearchtype',
            'value' => (((array)$prefs)['theme_essential_courseitemsearchtype'])->value
        ]);
        $this->assertEquals($description, (((array)$prefs)['theme_essential_courseitemsearchtype'])->description);
    }

}
