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

namespace core_analytics;

/**
 * Unit tests for the calculation info cache.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class calculation_info_test extends \advanced_testcase {
    /**
     * test_calculation_info description
     *
     * @dataProvider provider_test_calculation_info_add_pull
     * @param mixed $info1
     * @param mixed $info2
     * @param mixed $info3
     * @param mixed $info4
     * @return null
     */
    public function test_calculation_info_add_pull($info1, $info2, $info3, $info4): void {
        require_once(__DIR__ . '/fixtures/test_indicator_max.php');
        require_once(__DIR__ . '/fixtures/test_indicator_min.php');
        $this->resetAfterTest();

        $atimesplitting = new \core\analytics\time_splitting\quarters();

        $indicator1 = new \test_indicator_min();
        $indicator2 = new \test_indicator_max();

        $calculationinfo = new \core_analytics\calculation_info();
        $calculationinfo->add_shared(111, [111 => $info1]);
        $calculationinfo->add_shared(222, [222 => 'should-get-overwritten-in-next-line']);
        $calculationinfo->add_shared(222, [222 => $info2]);
        $calculationinfo->save($indicator1, $atimesplitting, 0);

        // We also check that the eheheh does not overwrite the value previously stored in the cache
        // during the previous save call.
        $calculationinfo->add_shared(222, [222 => 'eheheh']);
        $calculationinfo->save($indicator1, $atimesplitting, 0);

        // The method save() should clear the internal attrs in \core_analytics\calculation_info
        // so it is fine to reuse the same calculation_info instance.
        $calculationinfo->add_shared(111, [111 => $info3]);
        $calculationinfo->add_shared(333, [333 => $info4]);
        $calculationinfo->save($indicator2, $atimesplitting, 0);

        // We pull data in rangeindex '0' for samples 111, 222 and 333.
        $predictionrecords = [
            '111-0' => (object)['sampleid' => '111'],
            '222-0' => (object)['sampleid' => '222'],
            '333-0' => (object)['sampleid' => '333'],
        ];
        $info = \core_analytics\calculation_info::pull_info($predictionrecords);

        $this->assertCount(3, $info);
        $this->assertCount(2, $info[111]);
        $this->assertCount(1, $info[222]);
        $this->assertCount(1, $info[333]);
        $this->assertEquals($info1, $info[111]['test_indicator_min:extradata'][111]);
        $this->assertEquals($info2, $info[222]['test_indicator_min:extradata'][222]);
        $this->assertEquals($info3, $info[111]['test_indicator_max:extradata'][111]);
        $this->assertEquals($info4, $info[333]['test_indicator_max:extradata'][333]);

        // The calculationinfo cache gets emptied.
        $this->assertFalse(\core_analytics\calculation_info::pull_info($predictionrecords));
    }

    /**
     * provider_test_calculation_info_add_pull
     *
     * @return mixed[]
     */
    public static function provider_test_calculation_info_add_pull(): array {
        return [
            'mixed-types' => ['asd', true, [123, 123, 123], (object)['asd' => 'fgfg']],
        ];
    }
}
