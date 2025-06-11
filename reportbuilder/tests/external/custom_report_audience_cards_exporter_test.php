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

declare(strict_types=1);

namespace core_reportbuilder\external;

use advanced_testcase;
use context_system;

/**
 * Unit tests for custom report audience cards exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_audience_cards_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_report_audience_cards_exporter_test extends advanced_testcase {

    /**
     * Test exported data/structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        $exporter = new custom_report_audience_cards_exporter(null);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertNotEmpty($export->menucards);

        // Test only the site audiences, so tests are unaffected by audiences within components.
        $menucardsite = array_filter($export->menucards, static function(array $menucard): bool {
            return $menucard['name'] === get_string('site');
        });

        $this->assertCount(1, $menucardsite);
        $menucardsite = reset($menucardsite);

        $this->assertNotEmpty($menucardsite['key']);
        $this->assertGreaterThanOrEqual(4, count($menucardsite['items']));

        // Test the structure of the first menu card item.
        $menucarditem = reset($menucardsite['items']);
        $this->assertEquals([
            'name' => 'All users',
            'identifier' => \core_reportbuilder\reportbuilder\audience\allusers::class,
            'title' => 'Add audience \'All users\'',
            'action' => 'add-audience',
            'disabled' => false,
        ], $menucarditem);
    }

    /**
     * Test exported data when user cannot add some audience types
     */
    public function test_export_audience_user_can_add(): void {
        global $DB, $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // This capability controls access to the all/manual users audiences.
        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/user:viewalldetails', CAP_ALLOW, $userrole, context_system::instance());

        $exporter = new custom_report_audience_cards_exporter(null);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertCount(1, $export->menucards);
        $this->assertEquals('Site', $export->menucards[0]['name']);
        $this->assertEquals([
            'All users',
            'Manually added users',
        ], array_column($export->menucards[0]['items'], 'name'));
    }

    /**
     * Test exported data when user can add an audience type, but it isn't available
     */
    public function test_export_audience_is_available(): void {
        global $DB, $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/cohort:view', CAP_ALLOW, $userrole, context_system::instance());

        $exporter = new custom_report_audience_cards_exporter(null);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertCount(1, $export->menucards);
        $this->assertEquals('Site', $export->menucards[0]['name']);

        // Cohort audience should be present, but disabled.
        $this->assertCount(1, $export->menucards[0]['items']);
        $this->assertEquals('Member of cohort', $export->menucards[0]['items'][0]['name']);
        $this->assertTrue($export->menucards[0]['items'][0]['disabled']);
    }
}
