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
 * Base class for unit tests for fileconverter_googledrive.
 *
 * @package    fileconverter_googledrive
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace fileconverter_googledrive\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;

/**
 * Unit tests for files/converter/googledrive/classes/privacy/provider.php
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $contextlist = \fileconverter_googledrive\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEmpty($contextlist);
    }
}
