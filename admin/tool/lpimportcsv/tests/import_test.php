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

namespace tool_lpimportcsv;

use core_competency\api;

/**
 * External learning plans webservice API tests.
 *
 * @package tool_lpimportcsv
 * @copyright 2015 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_test extends \advanced_testcase {

    public function test_import_framework(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $importer = new framework_importer(
            file_get_contents(self::get_fixture_path(__NAMESPACE__, 'example.csv')),
        );

        $this->assertEquals('', $importer->get_error());

        $framework = $importer->import();
        $this->assertEmpty('', $importer->get_error());

        $this->assertGreaterThan(0, $framework->get('id'));

        $filters = [
            'competencyframeworkid' => $framework->get('id')
        ];
        $count = api::count_competencies($filters);
        $this->assertEquals(64, $count);

        // We can't test the exporter because it sends force-download headers.
    }


}
