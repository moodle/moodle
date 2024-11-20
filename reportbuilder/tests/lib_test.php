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

namespace core_reportbuilder;

use advanced_testcase;
use core_reportbuilder_generator;
use core_tag_tag;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/lib.php");

/**
 * Unit tests for the component callbacks
 *
 * @package     core_reportbuilder
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends advanced_testcase {

    /**
     * Test getting tagged reports
     *
     * @covers ::core_reportbuilder_get_tagged_reports
     */
    public function test_core_reportbuilder_get_tagged_reports(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create three tagged reports.
        $reportone = $generator->create_report(['name' => 'Report 1', 'source' => users::class, 'tags' => ['cat']]);
        $reporttwo = $generator->create_report(['name' => 'Report 2', 'source' => users::class, 'tags' => ['dog']]);
        $reportthree = $generator->create_report(['name' => 'Report 3', 'source' => users::class, 'tags' => ['cat']]);

        // Add all users audience to report one and two.
        $generator->create_audience(['reportid' => $reportone->get('id'), 'configdata' => []]);
        $generator->create_audience(['reportid' => $reporttwo->get('id'), 'configdata' => []]);

        $tag = core_tag_tag::get_by_name(0, 'cat');

        // Current user can only access report one with "cat" tag.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $tagindex = core_reportbuilder_get_tagged_reports($tag);
        $this->assertStringContainsString($reportone->get_formatted_name(), $tagindex->content);
        $this->assertStringNotContainsString($reporttwo->get_formatted_name(), $tagindex->content);
        $this->assertStringNotContainsString($reportthree->get_formatted_name(), $tagindex->content);

        // Admin can access both reports with "cat" tag.
        $this->setAdminUser();
        $tagindex = core_reportbuilder_get_tagged_reports($tag);
        $this->assertStringContainsString($reportone->get_formatted_name(), $tagindex->content);
        $this->assertStringNotContainsString($reporttwo->get_formatted_name(), $tagindex->content);
        $this->assertStringContainsString($reportthree->get_formatted_name(), $tagindex->content);
    }
}
