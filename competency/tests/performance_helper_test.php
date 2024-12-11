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

namespace core_competency;

use core_competency\external\performance_helper;

/**
 * Performance helper testcase.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class performance_helper_test extends \advanced_testcase {

    public function test_get_context_from_competency(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $competency2 = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        $context = $competency->get_context();
        $helper = new performance_helper();
        $initdbqueries = $DB->perf_get_queries();

        // Confirm that subsequent calls return a cached object.
        // Note that here we check that the framework is not loaded more than once.
        // The context objects are already cached in the context layer.
        $firstruncontext = $helper->get_context_from_competency($competency);
        $dbqueries = $DB->perf_get_queries();
        $this->assertSame($context, $firstruncontext);
        $this->assertNotEquals($initdbqueries, $dbqueries);

        $secondruncontext = $helper->get_context_from_competency($competency);
        $this->assertSame($context, $secondruncontext);
        $this->assertSame($firstruncontext, $secondruncontext);
        $this->assertEquals($DB->perf_get_queries(), $dbqueries);

        $thirdruncontext = $helper->get_context_from_competency($competency2);
        $this->assertSame($context, $thirdruncontext);
        $this->assertSame($secondruncontext, $thirdruncontext);
        $this->assertEquals($DB->perf_get_queries(), $dbqueries);
    }

    public function test_get_framework_from_competency(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $framework1 = $lpg->create_framework();
        $comp1a = $lpg->create_competency(['competencyframeworkid' => $framework1->get('id')]);
        $comp1b = $lpg->create_competency(['competencyframeworkid' => $framework1->get('id')]);
        $framework2 = $lpg->create_framework();
        $comp2a = $lpg->create_competency(['competencyframeworkid' => $framework2->get('id')]);

        $helper = new performance_helper();
        $initdbqueries = $DB->perf_get_queries();

        // Confirm that we get the right framework, and that subsequent calls
        // do not trigger DB queries, even for other competencies.
        $firstrunframework = $helper->get_framework_from_competency($comp1a);
        $firstrundbqueries = $DB->perf_get_queries();
        $this->assertNotEquals($initdbqueries, $firstrundbqueries);
        $this->assertEquals($framework1, $firstrunframework);
        $this->assertNotSame($framework1, $firstrunframework);

        $secondrunframework = $helper->get_framework_from_competency($comp1b);
        $this->assertEquals($firstrundbqueries, $DB->perf_get_queries());
        $this->assertEquals($framework1, $secondrunframework);
        $this->assertSame($firstrunframework, $secondrunframework);

        $thirdrunframework = $helper->get_framework_from_competency($comp1a);
        $this->assertEquals($firstrundbqueries, $DB->perf_get_queries());
        $this->assertEquals($framework1, $thirdrunframework);
        $this->assertSame($firstrunframework, $thirdrunframework);

        // Fetch another framework.
        $fourthrunframework = $helper->get_framework_from_competency($comp2a);
        $fourthrundbqueries = $DB->perf_get_queries();
        $this->assertNotEquals($firstrundbqueries, $fourthrundbqueries);
        $this->assertEquals($framework2, $fourthrunframework);
        $this->assertNotSame($framework2, $fourthrunframework);

        $fifthrunframework = $helper->get_framework_from_competency($comp2a);
        $this->assertEquals($fourthrundbqueries, $DB->perf_get_queries());
        $this->assertEquals($framework2, $fifthrunframework);
        $this->assertSame($fourthrunframework, $fifthrunframework);
    }

    public function test_get_scale_from_competency(): void {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $scale1 = $dg->create_scale();
        $scale2 = $dg->create_scale();
        $cat1 = $dg->create_category();

        $framework1 = $lpg->create_framework(['scaleid' => $scale1->id]);
        $comp1 = $lpg->create_competency(['competencyframeworkid' => $framework1->get('id')]);
        $comp2 = $lpg->create_competency(['competencyframeworkid' => $framework1->get('id'), 'scaleid' => $scale2->id]);
        $comp3 = $lpg->create_competency(['competencyframeworkid' => $framework1->get('id')]);

        $helper = new performance_helper();
        $initdbqueries = $DB->perf_get_queries();

        // Get the first scale.
        $firstrunscale = $helper->get_scale_from_competency($comp1);
        $firstrundbqueries = $DB->perf_get_queries();
        $this->assertNotEquals($initdbqueries, $firstrundbqueries);
        $this->assertEquals($scale1, $firstrunscale->get_record_data());

        $secondrunscale = $helper->get_scale_from_competency($comp3);
        $this->assertEquals($firstrundbqueries, $DB->perf_get_queries());
        $this->assertSame($firstrunscale, $secondrunscale);

        // Another scale, and its subsequent calls.
        $thirdrunscale = $helper->get_scale_from_competency($comp2);
        $thirddbqueries = $DB->perf_get_queries();
        $this->assertNotEquals($firstrundbqueries, $thirddbqueries);
        $this->assertEquals($scale2, $thirdrunscale->get_record_data());
        $this->assertSame($thirdrunscale, $helper->get_scale_from_competency($comp2));
        $this->assertEquals($thirddbqueries, $DB->perf_get_queries());
    }
}
