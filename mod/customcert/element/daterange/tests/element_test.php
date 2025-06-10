<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Test datarange element.
 *
 * @package    customcertelement_daterange
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_daterange;

use stdClass;
use advanced_testcase;
use fake_datarange_element;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/customcert/element/daterange/tests/fixtures/fake_datarange_element.php');

/**
 * Test datarange element.
 *
 * @package    customcertelement_daterange
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element_test extends advanced_testcase {

    /**
     * Helper function to build element data.
     *
     * @param stdClass $data Element data.
     *
     * @return object
     */
    protected function build_element_data(stdClass $data) {
        return (object) [
            'id' => 1,
            'pageid' => 1,
            'name' => 'Test',
            'data' => json_encode($data),
            'font' => 'Font',
            'fontsize' => 1,
            'colour' => '#EEE',
            'posx' => 0,
            'posy' => 0,
            'width' => 100,
            'refpoint' => 1,
        ];
    }

    /**
     * Helper function to build datarange data.
     *
     * @param array $dataranges A list of dataranges.
     * @param string $fallbackstring Fall back string.
     *
     * @return object
     */
    protected function build_datarange_data(array $dataranges, $fallbackstring = '') {
        return (object) [
            'dateitem' => 1,
            'fallbackstring' => $fallbackstring,
            'numranges' => count($dataranges),
            'dateranges' => $dataranges,
        ];
    }

    /**
     * A helper function to get datarange element for testing.
     *
     * @param array $dataranges A list of dataranges.
     * @param string $fallbackstring Fall back strin
     *
     * @return fake_datarange_element
     */
    protected function get_datarange_element(array $dataranges, $fallbackstring = '') {
        $datarangedata = $this->build_datarange_data($dataranges, $fallbackstring);
        $elementdata = $this->build_element_data($datarangedata);

        return new fake_datarange_element($elementdata);
    }

    /**
     * Data provider for test_get_daterange_string_for_recurring_ranges.
     * @return array
     */
    public static function get_test_get_daterange_string_for_recurring_ranges_data_provider(): array {
        return [
            ['1.11.2016', 'WS 2016/2017'],
            ['1.11.2017', 'WS 2017/2018'],
            ['1.11.2018', 'WS 2018/2019'],
            ['1.11.2019', 'WS 2019/2020'],
            ['1.02.2017', 'WS 2016/2017'],
            ['1.02.2018', 'WS 2017/2018'],
            ['1.02.2019', 'WS 2018/2019'],
            ['1.02.2020', 'WS 2019/2020'],
            ['1.05.2016', 'SS 2016'],
            ['1.05.2017', 'SS 2017'],
            ['1.05.2018', 'SS 2018'],
            ['1.05.2019', 'SS 2019'],
        ];
    }

    /**
     * Test get correct strings for recurring ranges.
     *
     * @dataProvider get_test_get_daterange_string_for_recurring_ranges_data_provider
     * @covers \element::get_daterange_string
     *
     * @param string $date Date to test.
     * @param string $expected Expected result.
     */
    public function test_get_daterange_string_for_recurring_ranges($date, $expected) {
        $dateranges = [
            (object)[
                'startdate' => strtotime('01.04.2017'),
                'enddate' => strtotime('30.09.2017'),
                'datestring' => 'SS {{date_year}}',
                'recurring' => true,
            ],
            (object)[
                'startdate' => strtotime('01.10.2017'),
                'enddate' => strtotime('31.03.2018'),
                'datestring' => 'WS {{recurring_range_first_year}}/{{recurring_range_last_year}}',
                'recurring' => true,
            ],
        ];

        $element = $this->get_datarange_element($dateranges);
        $date = strtotime($date);
        $this->assertEquals($expected, $element->get_daterange_string($date));
    }

    /**
     * Test that first found element matched.
     *
     * @covers \element::get_daterange_string
     */
    public function test_that_first_matched_range_applied_first() {
        $dateranges = [
            (object)[
                'startdate' => strtotime('01.04.2017'),
                'enddate' => strtotime('30.09.2017'),
                'datestring' => 'First range',
                'recurring' => false,
            ],
            (object)[
                'startdate' => strtotime('01.05.2017'),
                'enddate' => strtotime('01.07.2018'),
                'datestring' => 'Second range',
                'recurring' => false,
            ],
        ];

        $element = $this->get_datarange_element($dateranges);
        $date = strtotime('1.06.2017');
        $this->assertEquals('First range', $element->get_daterange_string($date));
    }

    /**
     * Test that placeholders correctly applied to matched range and fall back string.
     *
     * @covers \element::get_daterange_string
     */
    public function test_placeholders_and_fall_back_string() {
        $dateranges = [
            (object)[
                'startdate' => strtotime('01.04.2017'),
                'enddate' => strtotime('30.09.2018'),
                'datestring' => '{{current_year}} - {{range_first_year}} - {{range_last_year}} - {{date_year}}',
                'recurring' => false,
            ],
        ];

        $fallbackstring = '{{current_year}} - {{range_first_year}} - {{range_last_year}} - {{date_year}}';
        $element = $this->get_datarange_element($dateranges, $fallbackstring);

        $date = strtotime('1.01.2000');
        $expected = date('Y', time()) . ' - {{range_first_year}} - {{range_last_year}} - 2000';
        $this->assertEquals($expected, $element->get_daterange_string($date));

        $date = strtotime('1.07.2017');
        $expected = date('Y', time()) . ' - 2017 - 2018 - 2017';
        $this->assertEquals($expected, $element->get_daterange_string($date));
    }

    /**
     * Test that nothing will be displayed if not matched and empty fall back string.
     *
     * @covers \element::get_daterange_string
     */
    public function test_nothing_will_be_displayed_if_empty_fallback_string() {
        $dateranges = [
            (object)[
                'startdate' => strtotime('01.04.2017'),
                'enddate' => strtotime('30.09.2018'),
                'datestring' => '{{current_year}} - {{range_first_year}} - {{range_last_year}} - {{date_year}}',
                'recurring' => false,
            ],
        ];

        $fallbackstring = '';
        $element = $this->get_datarange_element($dateranges, $fallbackstring);

        $date = strtotime('1.07.2011');
        $this->assertEquals($fallbackstring, $element->get_daterange_string($date));
    }

    /**
     * Test that display recurring_range_first_year and recurring_range_last_year placeholders.
     *
     * @covers \element::get_daterange_string
     */
    public function test_recurring_range_first_year_and_recurring_range_last_year_placeholders() {
        $datestring = '{{range_first_year}}-{{range_last_year}}-{{recurring_range_first_year}}-{{recurring_range_last_year}}';
        $dateranges = [
            (object) [
                'startdate' => strtotime('01.04.2017'),
                'enddate' => strtotime('30.09.2017'),
                'datestring' => $datestring,
                'recurring' => true,
            ],
            (object)[
                'startdate' => strtotime('01.10.2017'),
                'enddate' => strtotime('31.03.2018'),
                'datestring' => $datestring,
                'recurring' => true,
            ],
        ];

        $element = $this->get_datarange_element($dateranges);

        $date = strtotime('1.05.2020');
        $this->assertEquals('2017-2017-2020-2020', $element->get_daterange_string($date));

        $date = strtotime('1.05.2024');
        $this->assertEquals('2017-2017-2024-2024', $element->get_daterange_string($date));

        $date = strtotime('1.02.2020');
        $this->assertEquals('2017-2018-2019-2020', $element->get_daterange_string($date));

        $date = strtotime('1.02.2024');
        $this->assertEquals('2017-2018-2023-2024', $element->get_daterange_string($date));
    }

}
