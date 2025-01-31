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

namespace qbank_managecategories\privacy;

use advanced_testcase;
use core_privacy\local\request\writer;
use qbank_managecategories\privacy\provider;

/**
 * Unit tests for qbank_managecategories privacy provider.
 *
 * @package    qbank_managecategories
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     2021, Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_managecategories\privacy\provider
 */
final class provider_test extends advanced_testcase {
    /**
     * Test to check export_user_preferences.
     *
     * @covers ::export_user_preferences
     */
    public function test_export_user_preferences(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference('qbank_managecategories_showdescriptions', 1, $user);
        set_user_preference('qbank_managecategories_includesubcategories_filter_default', 1, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $prefs = $writer->get_user_preferences('qbank_managecategories');
        $this->assertEquals(1, $prefs->showdescr->value);
        $this->assertEquals(1, $prefs->includesubcategories->value);
        $this->assertEquals(get_string('displaydescription', 'qbank_managecategories'), $prefs->showdescr->description);
        $this->assertEquals(get_string('questionsubcategoriesdisplayed',
            'qbank_managecategories'), $prefs->includesubcategories->description);
    }
}
