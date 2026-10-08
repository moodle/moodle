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

namespace core\check;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the check API's results table, in particular the streamed detail page.
 *
 * @package    core
 * @category   check
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2026 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(table::class)]
final class check_table_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        require_once(__DIR__ . '/../fixtures/check/fixture_check.php');
    }

    /**
     * Builds a table instance focused on the detail page for a single check, without going via the
     * check manager registry.
     *
     * @param check $check The check to show on the detail page.
     * @return table
     */
    private function create_detail_table(check $check): table {
        $table = (new \ReflectionClass(table::class))->newInstanceWithoutConstructor();

        $urlproperty = new \ReflectionProperty(table::class, 'url');
        $urlproperty->setAccessible(true);
        $urlproperty->setValue($table, new \moodle_url('/report/status/index.php'));

        $typeproperty = new \ReflectionProperty(table::class, 'type');
        $typeproperty->setAccessible(true);
        $typeproperty->setValue($table, 'status');

        $table->checks = [$check->get_ref() => $check];
        $table->detail = $check;

        return $table;
    }

    /**
     * Runs the checks and captures the streamed html output.
     *
     * @param table $table
     * @return string
     */
    private function run_and_capture(table $table): string {
        global $PAGE;

        $output = $PAGE->get_renderer('core');

        ob_start();
        $table->run_checks($output);
        return ob_get_clean();
    }

    /**
     * A single OK result with details should still have those details streamed to the detail page.
     *
     * This is a regression test: the detail rendering used to only collect details from non-OK
     * results, so an OK result's details were silently dropped.
     */
    public function test_detail_shown_for_single_ok_result(): void {
        $this->resetAfterTest();

        $check = new fixture_check([
            new result(result::OK, 'all good', 'OK-DETAILS-XYZ'),
        ]);
        $check->set_component('core');

        $html = $this->run_and_capture($this->create_detail_table($check));

        $this->assertStringContainsString('OK-DETAILS-XYZ', $html);
        $this->assertStringNotContainsString("querySelector('#checkdetailscontainer')", $html);
    }

    /**
     * Details from an OK result should be combined with details from a failing result, both
     * appearing in the rendered list.
     */
    public function test_details_shown_for_mixed_statuses(): void {
        $this->resetAfterTest();

        $check = new fixture_check([
            new result(result::OK, 'ok summary', 'OK-DETAILS'),
            new result(result::ERROR, 'error summary', 'ERROR-DETAILS'),
        ]);
        $check->set_component('core');

        $html = $this->run_and_capture($this->create_detail_table($check));

        $this->assertStringContainsString('OK-DETAILS', $html);
        $this->assertStringContainsString('ERROR-DETAILS', $html);
        // With more than one result supplying details, they are combined into a list (js string escaped).
        $this->assertStringContainsString('<ul><li>OK-DETAILS<\/li><li>ERROR-DETAILS<\/li><\/ul>', $html);
    }

    /**
     * When no results (of any status) supply details, the details container is removed entirely.
     */
    public function test_details_container_removed_when_no_details(): void {
        $this->resetAfterTest();

        $check = new fixture_check([
            new result(result::OK, 'all good'),
        ]);
        $check->set_component('core');

        $html = $this->run_and_capture($this->create_detail_table($check));

        $this->assertStringContainsString("querySelector('#checkdetailscontainer').outerHTML = '';", $html);
    }
}
