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
 * Data provider tests.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_competency\api;
use core_competency\privacy\provider;

/**
 * Data provider testcase class.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_competency\privacy\provider
 */
class provider_test extends provider_testcase {

    public function setUp(): void {
        global $PAGE;
        parent::setUp();
        $this->resetAfterTest();

        // We need this or exporters (core\external\exporter) do not receive the right renderer.
        $PAGE->get_renderer('core');
    }

    public function test_get_contexts_for_userid_with_usermodified_for_framework(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $sysctx = \context_system::instance();
        $cat1ctx = \context_coursecat::instance($cat1->id);
        $cat2ctx = \context_coursecat::instance($cat2->id);

        // Test recovery through framework context.
        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $f1 = $ccg->create_framework();
        $contextlist = provider::get_contexts_for_userid($u1->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $f2 = $ccg->create_framework(['contextid' => $cat1ctx->id]);
        $contextlist = provider::get_contexts_for_userid($u1->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);

        // Test recovery of category context alone.
        $this->setUser($u2);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_framework(['contextid' => $cat2ctx->id]);
        $contextlist = provider::get_contexts_for_userid($u2->id);
        $this->assert_contextlist($contextlist, [$cat2ctx]);

        // Test recovery through competency.
        $this->setUser($u3);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $c1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c2 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c3 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u3->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $c4 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c5 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c6 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u3->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);

        // Test recovery through related competency.
        $this->setUser($u4);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $cr = $ccg->create_related_competency(['competencyid' => $c1->get('id'), 'relatedcompetencyid' => $c2->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u4->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $cr = $ccg->create_related_competency(['competencyid' => $c4->get('id'), 'relatedcompetencyid' => $c5->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u4->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_framework(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $sysctx = \context_system::instance();
        $cat1ctx = \context_coursecat::instance($cat1->id);
        $cat2ctx = \context_coursecat::instance($cat2->id);

        // Add frameworks.
        $this->setUser($u1);
        $f1 = $ccg->create_framework();
        $f2 = $ccg->create_framework(['contextid' => $cat1ctx->id]);

        $this->setUser($u2);
        $ccg->create_framework(['contextid' => $cat2ctx->id]);

        // Add competencies.
        $this->setUser($u3);
        $c1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c2 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c3 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c4 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c5 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c6 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);

        // Add related competencies.
        $this->setUser($u4);
        $cr = $ccg->create_related_competency(['competencyid' => $c1->get('id'), 'relatedcompetencyid' => $c2->get('id')]);
        $cr = $ccg->create_related_competency(['competencyid' => $c4->get('id'), 'relatedcompetencyid' => $c5->get('id')]);

        // Test correct users appear in each context.
        $component = 'core_competency';

        $userlist = new userlist($sysctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u3->id, $u4->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($cat1ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u3->id, $u4->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($cat2ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u2->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_usermodified_for_template(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $cohort = $dg->create_cohort();

        $sysctx = \context_system::instance();
        $cat1ctx = \context_coursecat::instance($cat1->id);
        $cat2ctx = \context_coursecat::instance($cat2->id);

        $f1 = $ccg->create_framework();
        $f2 = $ccg->create_framework(['contextid' => $cat1ctx->id]);
        $f3 = $ccg->create_framework(['contextid' => $cat2ctx->id]);
        $cs = [];

        foreach ([$f1, $f2, $f3] as $f) {
            $cs[$f->get('id')] = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        }

        // Test recovery through template context.
        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $t1 = $ccg->create_template();
        $contextlist = provider::get_contexts_for_userid($u1->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $t2 = $ccg->create_template(['contextid' => $cat1ctx->id]);
        $contextlist = provider::get_contexts_for_userid($u1->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);

        // Test recovery of category context alone.
        $this->setUser($u2);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_template(['contextid' => $cat2ctx->id]);
        $contextlist = provider::get_contexts_for_userid($u2->id);
        $this->assert_contextlist($contextlist, [$cat2ctx]);

        // Test recovery through template competency.
        $this->setUser($u3);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $c1 = $ccg->create_template_competency(['competencyid' => $cs[$f1->get('id')]->get('id'), 'templateid' => $t1->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u3->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $c4 = $ccg->create_template_competency(['competencyid' => $cs[$f2->get('id')]->get('id'), 'templateid' => $t2->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u3->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);

        // Test recovery through template cohort.
        $this->setUser($u4);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $c1 = $ccg->create_template_cohort(['cohortid' => $cohort->id, 'templateid' => $t1->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u4->id);
        $this->assert_contextlist($contextlist, [$sysctx]);
        $c4 = $ccg->create_template_cohort(['cohortid' => $cohort->id, 'templateid' => $t2->get('id')]);
        $contextlist = provider::get_contexts_for_userid($u4->id);
        $this->assert_contextlist($contextlist, [$sysctx, $cat1ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_template(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $cohort = $dg->create_cohort();

        $sysctx = \context_system::instance();
        $cat1ctx = \context_coursecat::instance($cat1->id);
        $cat2ctx = \context_coursecat::instance($cat2->id);

        $f1 = $ccg->create_framework();
        $f2 = $ccg->create_framework(['contextid' => $cat1ctx->id]);
        $f3 = $ccg->create_framework(['contextid' => $cat2ctx->id]);
        $cs = [];

        foreach ([$f1, $f2, $f3] as $f) {
            $cs[$f->get('id')] = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        }

        // Create template context.
        $this->setUser($u1);
        $t1 = $ccg->create_template();
        $t2 = $ccg->create_template(['contextid' => $cat1ctx->id]);

        // Add to category context.
        $this->setUser($u2);
        $ccg->create_template(['contextid' => $cat2ctx->id]);

        // Create template competencies.
        $this->setUser($u3);
        $c1 = $ccg->create_template_competency(['competencyid' => $cs[$f1->get('id')]->get('id'), 'templateid' => $t1->get('id')]);
        $c4 = $ccg->create_template_competency(['competencyid' => $cs[$f2->get('id')]->get('id'), 'templateid' => $t2->get('id')]);

        // Create template cohorts.
        $this->setUser($u4);
        $c1 = $ccg->create_template_cohort(['cohortid' => $cohort->id, 'templateid' => $t1->get('id')]);
        $c4 = $ccg->create_template_cohort(['cohortid' => $cohort->id, 'templateid' => $t2->get('id')]);

        // Test correct users appear in each context.
        $component = 'core_competency';

        $userlist = new userlist($sysctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u3->id, $u4->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($cat1ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u3->id, $u4->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($cat2ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u2->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_usermodified_for_course(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_course_competency(['courseid' => $c1->id, 'competencyid' => $comp1->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);

        $this->setUser($u2);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_course_competency(['courseid' => $c2->id, 'competencyid' => $comp2->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$c2ctx]);
        $ccg->create_course_competency(['courseid' => $c1->id, 'competencyid' => $comp2->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$c1ctx, $c2ctx]);

        $this->setUser($u3);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $ccs = new \core_competency\course_competency_settings(null, (object) ['courseid' => $c1->id]);
        $ccs->create();
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$c1ctx, $c2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$c1ctx]);

        $this->setUser($u4);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $ccg->create_user_competency_course(['courseid' => $c2->id, 'userid' => $u0->id, 'competencyid' => $comp1->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$c1ctx, $c2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), [$c2ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_course(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $ccg->create_course_competency(['courseid' => $c1->id, 'competencyid' => $comp1->get('id')]);

        $this->setUser($u2);
        $ccg->create_course_competency(['courseid' => $c2->id, 'competencyid' => $comp2->get('id')]);
        $ccg->create_course_competency(['courseid' => $c1->id, 'competencyid' => $comp2->get('id')]);

        $this->setUser($u3);
        $ccs = new \core_competency\course_competency_settings(null, (object) ['courseid' => $c1->id]);
        $ccs->create();

        $this->setUser($u4);
        $ccg->create_user_competency_course(['courseid' => $c2->id, 'userid' => $u0->id, 'competencyid' => $comp1->get('id')]);

        // Test correct users appear in each context.
        $component = 'core_competency';

        $userlist = new userlist($c1ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u2->id, $u3->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($c2ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u0->id, $u2->id, $u4->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_usermodified_for_module(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $m1 = $dg->create_module('choice', ['course' => $c1]);
        $m2 = $dg->create_module('choice', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $m1ctx = \context_module::instance($m1->cmid);
        $m2ctx = \context_module::instance($m2->cmid);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_course_module_competency(['cmid' => $m1->cmid, 'competencyid' => $comp1->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$m1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);

        $this->setUser($u2);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$m1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $ccg->create_course_module_competency(['cmid' => $m2->cmid, 'competencyid' => $comp2->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$m1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$m2ctx]);
        $ccg->create_course_module_competency(['cmid' => $m1->cmid, 'competencyid' => $comp2->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$m1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$m1ctx, $m2ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_module(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $m1 = $dg->create_module('choice', ['course' => $c1]);
        $m2 = $dg->create_module('choice', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $m1ctx = \context_module::instance($m1->cmid);
        $m2ctx = \context_module::instance($m2->cmid);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $ccg->create_course_module_competency(['cmid' => $m1->cmid, 'competencyid' => $comp1->get('id')]);

        $this->setUser($u2);
        $ccg->create_course_module_competency(['cmid' => $m2->cmid, 'competencyid' => $comp2->get('id')]);
        $ccg->create_course_module_competency(['cmid' => $m1->cmid, 'competencyid' => $comp2->get('id')]);

        // Test correct users appear in each context.
        $component = 'core_competency';

        $userlist = new userlist($m1ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u1->id, $u2->id];
        $this->assert_array_match($expected, $userlist->get_userids());

        $userlist = new userlist($m2ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u2->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_usermodified_for_plan(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $plan = $ccg->create_plan(['userid' => $u0->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);

        $this->setUser($u2);
        $ccg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);

        $this->setUser($u3);
        $ccg->create_user_competency_plan(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id'),
            'userid' => $u0->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$u0ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_plan(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $plan = $ccg->create_plan(['userid' => $u0->id]);

        $this->setUser($u2);
        $ccg->create_plan_competency(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')]);

        $this->setUser($u3);
        $ccg->create_user_competency_plan(['planid' => $plan->get('id'), 'competencyid' => $comp1->get('id'),
            'userid' => $u0->id]);

        // Test correct users appear in the context.
        $component = 'core_competency';

        $userlist = new userlist($u0ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u0->id, $u1->id, $u2->id, $u3->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_usermodified_for_competency_data(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();
        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u6->id), []);
        $uc = $ccg->create_user_competency(['userid' => $u0->id, 'competencyid' => $comp1->get('id'),
            'reviewerid' => $u6->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u6->id), [$u0ctx]);

        $this->setUser($u2);
        $e = $ccg->create_evidence(['usercompetencyid' => $uc->get('id'), 'actionuserid' => $u5->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u6->id), [$u0ctx]);

        $this->setUser($u3);
        $ccg->create_user_evidence(['userid' => $u0->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u6->id), [$u0ctx]);

        $this->setUser($u4);
        $ccg->create_user_evidence(['userid' => $u0->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), [$u0ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u6->id), [$u0ctx]);

        // Comment on competency.
        $this->allow_anyone_to_comment_anywhere();
        $this->assert_contextlist(provider::get_contexts_for_userid($u7->id), []);
        $this->setUser($u7);
        $comments = $uc->get_comment_object();
        $comments->add('Hello there!');
        $this->assert_contextlist(provider::get_contexts_for_userid($u7->id), [$u0ctx]);

        // Comment on plan.
        $this->assert_contextlist(provider::get_contexts_for_userid($u8->id), []);
        $this->setUser($u8);
        $plan = $ccg->create_plan(['userid' => $u0->id]);
        $comments = $plan->get_comment_object();
        $comments->add('Hi, planet!');
        $this->assert_contextlist(provider::get_contexts_for_userid($u8->id), [$u0ctx]);
    }

    public function test_get_users_in_context_with_usermodified_for_competency_data(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();
        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        $uc = $ccg->create_user_competency(['userid' => $u0->id, 'competencyid' => $comp1->get('id'),
            'reviewerid' => $u6->id]);

        $this->setUser($u2);
        $e = $ccg->create_evidence(['usercompetencyid' => $uc->get('id'), 'actionuserid' => $u5->id]);

        $this->setUser($u3);
        $ccg->create_user_evidence(['userid' => $u0->id]);

        $this->setUser($u4);
        $ccg->create_user_evidence(['userid' => $u0->id]);

        // Comment on competency.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u7);
        $comments = $uc->get_comment_object();
        $comments->add('Hello there!');

        // Comment on plan.
        $this->setUser($u8);
        $plan = $ccg->create_plan(['userid' => $u0->id]);
        $comments = $plan->get_comment_object();
        $comments->add('Hi, planet!');

        // Test correct users appear in the context.
        $component = 'core_competency';

        $userlist = new userlist($u0ctx, $component);
        provider::get_users_in_context($userlist);
        $expected = [$u0->id, $u1->id, $u2->id, $u3->id, $u4->id, $u5->id, $u6->id, $u7->id, $u8->id];
        $this->assert_array_match($expected, $userlist->get_userids());
    }

    public function test_get_contexts_for_userid_with_actual_data_and_actual_data_is_goooood(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);
        $u3ctx = \context_user::instance($u3->id);
        $u4ctx = \context_user::instance($u4->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);

        $ccg->create_plan(['userid' => $u1->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);

        $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp1->get('id')]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);

        $ccg->create_user_competency_course(['userid' => $u3->id, 'competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), []);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);

        $ue = $ccg->create_user_evidence(['userid' => $u4->id]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), [$u4ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), []);

        // A user editing a context relationship.
        $this->setUser($u5);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue->get('id'), 'competencyid' => $comp1->get('id')]);
        $this->setAdminUser();
        $this->assert_contextlist(provider::get_contexts_for_userid($u1->id), [$u1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u2->id), [$u2ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u3->id), [$c1ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u4->id), [$u4ctx]);
        $this->assert_contextlist(provider::get_contexts_for_userid($u5->id), [$u4ctx]);
    }

    public function test_get_users_in_context_with_actual_data_and_actual_data_is_goooood(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);
        $u3ctx = \context_user::instance($u3->id);
        $u4ctx = \context_user::instance($u4->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ccg->create_plan(['userid' => $u1->id]);

        $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp1->get('id')]);

        $ccg->create_user_competency_course(['userid' => $u3->id, 'competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);

        $ccg->create_user_evidence(['userid' => $u4->id]);

        // Test correct users appear in each context.
        $component = 'core_competency';

        $userlist = new userlist($u1ctx, $component);
        provider::get_users_in_context($userlist);
        $this->assert_array_match([$u1->id], $userlist->get_userids());

        $userlist = new userlist($u2ctx, $component);
        provider::get_users_in_context($userlist);
        $this->assert_array_match([$u2->id], $userlist->get_userids());

        $userlist = new userlist($c1ctx, $component);
        provider::get_users_in_context($userlist);
        $this->assert_array_match([$u3->id], $userlist->get_userids());

        $userlist = new userlist($u4ctx, $component);
        provider::get_users_in_context($userlist);
        $this->assert_array_match([$u4->id], $userlist->get_userids());
    }

    public function test_delete_data_for_user(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue1b = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue2 = $ccg->create_user_evidence(['userid' => $u2->id]);
        $uec1a = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $uec1b = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $uec2 = $ccg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $p1a = $ccg->create_plan(['userid' => $u1->id]);
        $p1b = $ccg->create_plan(['userid' => $u1->id]);
        $p2 = $ccg->create_plan(['userid' => $u2->id]);
        $pc1a = $ccg->create_plan_competency(['planid' => $p1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $pc1b = $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp2->get('id')]);
        $pc2 = $ccg->create_plan_competency(['planid' => $p2->get('id'), 'competencyid' => $comp1->get('id')]);
        $ucp1a = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $ucp1b = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $ucp2 = $ccg->create_user_competency_plan(['userid' => $u2->id, 'planid' => $p2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $uc1b = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp2->get('id')]);
        $uc2 = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp2->get('id')]);
        $e1a = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id')]);
        $e1b = $ccg->create_evidence(['usercompetencyid' => $uc1b->get('id')]);
        $e2 = $ccg->create_evidence(['usercompetencyid' => $uc2->get('id')]);

        $ucc1a = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);
        $ucc1b = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c2->id,
            'competencyid' => $comp1->get('id')]);
        $ucc2 = $ccg->create_user_competency_course(['userid' => $u2->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);

        // User 1 comments on both plans.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u1);
        $p1a->get_comment_object()->add('Hi...');
        $p1a->get_comment_object()->add('mister');
        $p2->get_comment_object()->add('Ahoy!');

        // User 2 comments on both competencies.
        $this->setUser($u2);
        $uc1a->get_comment_object()->add('Hi, too!');
        $uc1a->get_comment_object()->add('How are you?');
        $uc2->get_comment_object()->add('Ahoy, too!');

        $p1acommentobj = $p1a->get_comment_object();
        $p2commentobj = $p2->get_comment_object();
        $uc1acommentobj = $uc1a->get_comment_object();
        $uc2commentobj = $uc2->get_comment_object();

        $this->setAdminUser();
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1b->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
        $this->assert_has_comments($p1acommentobj);
        $this->assertEquals(2, $this->get_comments_count($p1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u2->id));
        $this->assert_has_comments($p2commentobj);
        $this->assertEquals(1, $this->get_comments_count($p2commentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p2commentobj, $u2->id));
        $this->assert_has_comments($uc1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u1->id));
        $this->assertEquals(2, $this->get_comments_count($uc1acommentobj, $u2->id));
        $this->assert_has_comments($uc2commentobj);
        $this->assertEquals(0, $this->get_comments_count($uc2commentobj, $u1->id));
        $this->assertEquals(1, $this->get_comments_count($uc2commentobj, $u2->id));

        // Deleting user context only.
        $appctx = new approved_contextlist($u1, 'core_competency', [$u1ctx->id]);
        provider::delete_data_for_user($appctx);

        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1b->get('id')));

        $this->assert_has_no_comments($p1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u2->id));
        $this->assert_has_no_comments($uc1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u2->id));

        // This should not have been affected.
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));

        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
        $this->assert_has_comments($p2commentobj);
        $this->assertEquals(1, $this->get_comments_count($p2commentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p2commentobj, $u2->id));
        $this->assert_has_comments($uc2commentobj);
        $this->assertEquals(0, $this->get_comments_count($uc2commentobj, $u1->id));
        $this->assertEquals(1, $this->get_comments_count($uc2commentobj, $u2->id));

        // Deleting course context as well.
        $appctx = new approved_contextlist($u1, 'core_competency', [$u1ctx->id, $c1ctx->id]);
        provider::delete_data_for_user($appctx);

        $this->assertFalse(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));

        // The rest belongs to another course, or the other user.
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
    }

    public function test_delete_data_for_user_with_other_user_context(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        // Create a bunch of data for user 1.
        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $uec1a = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $p1a = $ccg->create_plan(['userid' => $u1->id]);
        $pc1a = $ccg->create_plan_competency(['planid' => $p1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $ucp1a = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $e1a = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id')]);

        $p2a = $ccg->create_plan(['userid' => $u2->id]);

        // User 2 comments.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u2);
        $p1a->get_comment_object()->add('Hi...');
        $p2a->get_comment_object()->add('Hi, hi!');
        $uc1a->get_comment_object()->add('Hi, too!');

        // Confirm state.
        $this->setAdminUser();
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($p2a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p2a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));

        $this->assertTrue(\core_competency\plan::record_exists($p2a->get('id')));

        // Delete for user 2, but we pass u1 context.
        provider::delete_data_for_user(new approved_contextlist($u2, 'core_competency', [$u1ctx->id]));

        // Nothing should have happened.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($p2a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p2a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));

        $this->assertTrue(\core_competency\plan::record_exists($p2a->get('id')));

        // Delete for user 2, but we pass u1 and u2 context.
        $p2acommentobj = $p2a->get_comment_object();
        provider::delete_data_for_user(new approved_contextlist($u2, 'core_competency', [$u1ctx->id, $u2ctx->id]));

        // The plan got deleted.
        $this->assertFalse(\core_competency\plan::record_exists($p2a->get('id')));
        $this->assert_has_no_comments($p2acommentobj);

        // Nothing should have happened for u1.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));
    }

    public function test_delete_data_for_users(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $u1ctx = \context_user::instance($u1->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue1b = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue2 = $ccg->create_user_evidence(['userid' => $u2->id]);
        $uec1a = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $uec1b = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $uec2 = $ccg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $p1a = $ccg->create_plan(['userid' => $u1->id]);
        $p1b = $ccg->create_plan(['userid' => $u1->id]);
        $p2 = $ccg->create_plan(['userid' => $u2->id]);
        $pc1a = $ccg->create_plan_competency(['planid' => $p1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $pc1b = $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp2->get('id')]);
        $pc2 = $ccg->create_plan_competency(['planid' => $p2->get('id'), 'competencyid' => $comp1->get('id')]);
        $ucp1a = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $ucp1b = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $ucp2 = $ccg->create_user_competency_plan(['userid' => $u2->id, 'planid' => $p2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $uc1b = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp2->get('id')]);
        $uc2 = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp2->get('id')]);
        $e1a = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id')]);
        $e1b = $ccg->create_evidence(['usercompetencyid' => $uc1b->get('id')]);
        $e2 = $ccg->create_evidence(['usercompetencyid' => $uc2->get('id')]);

        $ucc1a = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);
        $ucc1b = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c2->id,
            'competencyid' => $comp1->get('id')]);
        $ucc2 = $ccg->create_user_competency_course(['userid' => $u2->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);

        // User 1 comments on both plans.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u1);
        $p1a->get_comment_object()->add('Hi...');
        $p1a->get_comment_object()->add('mister');
        $p2->get_comment_object()->add('Ahoy!');

        // User 2 comments on both competencies.
        $this->setUser($u2);
        $uc1a->get_comment_object()->add('Hi, too!');
        $uc1a->get_comment_object()->add('How are you?');
        $uc2->get_comment_object()->add('Ahoy, too!');

        $p1acommentobj = $p1a->get_comment_object();
        $p2commentobj = $p2->get_comment_object();
        $uc1acommentobj = $uc1a->get_comment_object();
        $uc2commentobj = $uc2->get_comment_object();

        $this->setAdminUser();
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1b->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
        $this->assert_has_comments($p1acommentobj);
        $this->assertEquals(2, $this->get_comments_count($p1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u2->id));
        $this->assert_has_comments($p2commentobj);
        $this->assertEquals(1, $this->get_comments_count($p2commentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p2commentobj, $u2->id));
        $this->assert_has_comments($uc1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u1->id));
        $this->assertEquals(2, $this->get_comments_count($uc1acommentobj, $u2->id));
        $this->assert_has_comments($uc2commentobj);
        $this->assertEquals(0, $this->get_comments_count($uc2commentobj, $u1->id));
        $this->assertEquals(1, $this->get_comments_count($uc2commentobj, $u2->id));

        // Deleting user context.
        $userlist = new approved_userlist($u1ctx, 'core_competency', [$u1->id, $u2->id]);
        provider::delete_data_for_users($userlist);

        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1b->get('id')));

        $this->assert_has_no_comments($p1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p1acommentobj, $u2->id));
        $this->assert_has_no_comments($uc1acommentobj);
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($uc1acommentobj, $u2->id));

        // This should not have been affected.
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));

        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
        $this->assert_has_comments($p2commentobj);
        $this->assertEquals(1, $this->get_comments_count($p2commentobj, $u1->id));
        $this->assertEquals(0, $this->get_comments_count($p2commentobj, $u2->id));
        $this->assert_has_comments($uc2commentobj);
        $this->assertEquals(0, $this->get_comments_count($uc2commentobj, $u1->id));
        $this->assertEquals(1, $this->get_comments_count($uc2commentobj, $u2->id));

        // Deleting course context as well.
        $userlist = new approved_userlist($c1ctx, 'core_competency', [$u1->id]);
        provider::delete_data_for_users($userlist);

        $this->assertFalse(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));

        // The rest belongs to another course, or the other user.
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));
    }

    public function test_delete_data_for_users_with_other_user_context(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        // Create a bunch of data for user 1.
        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $uec1a = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $p1a = $ccg->create_plan(['userid' => $u1->id]);
        $pc1a = $ccg->create_plan_competency(['planid' => $p1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $ucp1a = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $e1a = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id')]);

        $p2a = $ccg->create_plan(['userid' => $u2->id]);

        // User 2 comments.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u2);
        $p1a->get_comment_object()->add('Hi...');
        $p2a->get_comment_object()->add('Hi, hi!');
        $uc1a->get_comment_object()->add('Hi, too!');

        // Confirm state.
        $this->setAdminUser();
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($p2a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p2a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));

        $this->assertTrue(\core_competency\plan::record_exists($p2a->get('id')));

        // Delete for user 2, but we pass u1 context.
        $userlist = new approved_userlist($u1ctx, 'core_competency', [$u2->id]);
        provider::delete_data_for_users($userlist);

        // Nothing should have happened.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($p2a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p2a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));

        $this->assertTrue(\core_competency\plan::record_exists($p2a->get('id')));

        // Delete for user 2, in user 2 context.
        $p2acommentobj = $p2a->get_comment_object();
        $userlist = new approved_userlist($u2ctx, 'core_competency', [$u2->id]);
        provider::delete_data_for_users($userlist);

        // The plan got deleted.
        $this->assertFalse(\core_competency\plan::record_exists($p2a->get('id')));
        $this->assert_has_no_comments($p2acommentobj);

        // Nothing should have happened for u1.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assert_has_comments($p1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($p1a->get_comment_object(), $u2->id));
        $this->assert_has_comments($uc1a->get_comment_object());
        $this->assertEquals(1, $this->get_comments_count($uc1a->get_comment_object(), $u2->id));
    }

    public function test_delete_data_for_all_users_in_context(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue1b = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue2 = $ccg->create_user_evidence(['userid' => $u2->id]);
        $uec1a = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $uec1b = $ccg->create_user_evidence_competency(['userevidenceid' => $ue1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $uec2 = $ccg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $p1a = $ccg->create_plan(['userid' => $u1->id]);
        $p1b = $ccg->create_plan(['userid' => $u1->id]);
        $p2 = $ccg->create_plan(['userid' => $u2->id]);
        $pc1a = $ccg->create_plan_competency(['planid' => $p1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $pc1b = $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp2->get('id')]);
        $pc2 = $ccg->create_plan_competency(['planid' => $p2->get('id'), 'competencyid' => $comp1->get('id')]);
        $ucp1a = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1a->get('id'),
            'competencyid' => $comp1->get('id')]);
        $ucp1b = $ccg->create_user_competency_plan(['userid' => $u1->id, 'planid' => $p1b->get('id'),
            'competencyid' => $comp2->get('id')]);
        $ucp2 = $ccg->create_user_competency_plan(['userid' => $u2->id, 'planid' => $p2->get('id'),
            'competencyid' => $comp1->get('id')]);

        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $uc1b = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp2->get('id')]);
        $uc2 = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp2->get('id')]);
        $e1a = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id')]);
        $e1b = $ccg->create_evidence(['usercompetencyid' => $uc1b->get('id')]);
        $e2 = $ccg->create_evidence(['usercompetencyid' => $uc2->get('id')]);

        $ucc1a = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);
        $ucc1b = $ccg->create_user_competency_course(['userid' => $u1->id, 'courseid' => $c2->id,
            'competencyid' => $comp1->get('id')]);
        $ucc2 = $ccg->create_user_competency_course(['userid' => $u2->id, 'courseid' => $c1->id,
            'competencyid' => $comp1->get('id')]);

        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1b->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc2->get('id')));

        // Deleting the course 1 context.
        provider::delete_data_for_all_users_in_context($c1ctx);
        $this->assertFalse(\core_competency\user_competency_course::record_exists($ucc1a->get('id')));
        $this->assertFalse(\core_competency\user_competency_course::record_exists($ucc2->get('id')));

        // Not affected.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e1b->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));

        // Deleting the user 1 context.
        provider::delete_data_for_all_users_in_context($u1ctx);
        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence::record_exists($ue1b->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1a->get('id')));
        $this->assertFalse(\core_competency\user_evidence_competency::record_exists($uec1b->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1a->get('id')));
        $this->assertFalse(\core_competency\plan::record_exists($p1b->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1a->get('id')));
        $this->assertFalse(\core_competency\plan_competency::record_exists($pc1b->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1a->get('id')));
        $this->assertFalse(\core_competency\user_competency_plan::record_exists($ucp1b->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1a->get('id')));
        $this->assertFalse(\core_competency\user_competency::record_exists($uc1b->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1a->get('id')));
        $this->assertFalse(\core_competency\evidence::record_exists($e1b->get('id')));

        // Not affected.
        $this->assertTrue(\core_competency\user_evidence::record_exists($ue2->get('id')));
        $this->assertTrue(\core_competency\user_evidence_competency::record_exists($uec2->get('id')));
        $this->assertTrue(\core_competency\plan::record_exists($p2->get('id')));
        $this->assertTrue(\core_competency\plan_competency::record_exists($pc2->get('id')));
        $this->assertTrue(\core_competency\user_competency_plan::record_exists($ucp2->get('id')));
        $this->assertTrue(\core_competency\user_competency::record_exists($uc2->get('id')));
        $this->assertTrue(\core_competency\evidence::record_exists($e2->get('id')));
        $this->assertTrue(\core_competency\user_competency_course::record_exists($ucc1b->get('id')));
    }

    public function test_export_data_for_user_in_module_context_where_usermodified_matches(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $m1 = $dg->create_module('page', ['course' => $c1]);
        $m2 = $dg->create_module('page', ['course' => $c1]);

        $m1ctx = \context_module::instance($m1->cmid);
        $m2ctx = \context_module::instance($m2->cmid);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $ccg->create_course_module_competency(['competencyid' => $comp3->get('id'), 'cmid' => $m1->cmid]);

        $this->setUser($u1);
        $ccg->create_course_module_competency(['competencyid' => $comp1->get('id'), 'cmid' => $m1->cmid]);
        $ccg->create_course_module_competency(['competencyid' => $comp2->get('id'), 'cmid' => $m2->cmid]);

        $this->setUser($u2);
        $ccg->create_course_module_competency(['competencyid' => $comp3->get('id'), 'cmid' => $m2->cmid]);

        // Export.
        $this->setAdminUser();
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$m1ctx->id]));

        // Check exported context 1.
        $data = writer::with_context($m1ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertCount(1, $data->associations);
        $this->assertEquals(transform::yesno(true), $data->associations[0]['created_or_modified_by_you']);

        // Check exported context 2.
        $data = writer::with_context($m2ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertEmpty($data);

        // Export both contexts.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$m1ctx->id, $m2ctx->id]));

        // Check exported context 1.
        $data = writer::with_context($m1ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertCount(1, $data->associations);
        $this->assertEquals($comp1->get('shortname'), $data->associations[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->associations[0]['created_or_modified_by_you']);

        // Check exported context 2.
        $data = writer::with_context($m2ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertCount(1, $data->associations);
        $this->assertEquals($comp2->get('shortname'), $data->associations[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->associations[0]['created_or_modified_by_you']);
    }

    public function test_export_data_for_user_in_course_context_where_usermodified_matches(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp4 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $ccg->create_course_competency(['competencyid' => $comp3->get('id'), 'courseid' => $c1->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp3->get('id'), 'courseid' => $c1->id, 'userid' => $u0->id]);

        $this->setUser($u1);
        $ccg->create_course_competency(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id]);
        $ccg->create_course_competency(['competencyid' => $comp4->get('id'), 'courseid' => $c1->id]);
        $ccg->create_course_competency(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id, 'userid' => $u0->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp4->get('id'), 'courseid' => $c1->id, 'userid' => $u0->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id, 'userid' => $u0->id]);
        $ccs = new \core_competency\course_competency_settings(null, (object) ['courseid' => $c1->id]);
        $ccs->create();

        $this->setUser($u2);
        $ccg->create_course_competency(['competencyid' => $comp3->get('id'), 'courseid' => $c2->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp3->get('id'), 'courseid' => $c2->id, 'userid' => $u0->id]);
        $ccs = new \core_competency\course_competency_settings(null, (object) ['courseid' => $c2->id]);
        $ccs->create();

        // Export.
        $this->setAdminUser();
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$c1ctx->id]));

        // Check exported context 1.
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'associations');
        $this->assertCount(2, $data->competencies);
        $this->assertEquals($comp1->get('shortname'), $data->competencies[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->competencies[0]['created_or_modified_by_you']);
        $this->assertEquals($comp4->get('shortname'), $data->competencies[1]['name']);
        $this->assertEquals(transform::yesno(true), $data->competencies[1]['created_or_modified_by_you']);
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'settings');
        $this->assertEquals(transform::yesno(true), $data->created_or_modified_by_you);
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'rated_by_me');
        $this->assertCount(2, $data->ratings);
        $this->assertEquals($comp1->get('shortname'), $data->ratings[0]['name']);
        $this->assertEquals($comp4->get('shortname'), $data->ratings[1]['name']);

        // Check exported context 2.
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'associations');
        $this->assertEmpty($data);
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'settings');
        $this->assertEmpty($data);
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'rated_by_me');
        $this->assertEmpty($data);

        // Export both contexts.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$c1ctx->id, $c2ctx->id]));

        // Check exported context 1.
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'associations');
        $this->assertCount(2, $data->competencies);
        $this->assertEquals($comp1->get('shortname'), $data->competencies[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->competencies[0]['created_or_modified_by_you']);
        $this->assertEquals($comp4->get('shortname'), $data->competencies[1]['name']);
        $this->assertEquals(transform::yesno(true), $data->competencies[1]['created_or_modified_by_you']);
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'settings');
        $this->assertEquals(transform::yesno(true), $data->created_or_modified_by_you);
        $data = writer::with_context($c1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'rated_by_me');
        $this->assertCount(2, $data->ratings);
        $this->assertEquals($comp1->get('shortname'), $data->ratings[0]['name']);
        $this->assertEquals($comp4->get('shortname'), $data->ratings[1]['name']);

        // Check exported context 2.
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'associations');
        $this->assertCount(1, $data->competencies);
        $this->assertEquals($comp2->get('shortname'), $data->competencies[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->competencies[0]['created_or_modified_by_you']);
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'settings');
        $this->assertEmpty($data);
        $data = writer::with_context($c2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'rated_by_me');
        $this->assertCount(1, $data->ratings);
        $this->assertEquals($comp2->get('shortname'), $data->ratings[0]['name']);
    }

    public function test_export_data_for_user_in_course_context_with_real_data(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ccg->create_user_competency_course(['competencyid' => $comp1->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id, 'grade' => 1, 'proficiency' => true]);
        $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c1->id,
            'userid' => $u1->id, 'grade' => 2, 'proficiency' => false]);
        $ccg->create_user_competency_course(['competencyid' => $comp2->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id, 'grade' => 3, 'proficiency' => false]);
        $ccg->create_user_competency_course(['competencyid' => $comp3->get('id'), 'courseid' => $c2->id,
            'userid' => $u1->id]);

        $ccg->create_user_competency_course(['competencyid' => $comp3->get('id'), 'courseid' => $c1->id, 'userid' => $u2->id]);
        $ccg->create_user_competency_course(['competencyid' => $comp3->get('id'), 'courseid' => $c2->id, 'userid' => $u2->id]);

        // Export user 1, in course 1.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$c1ctx->id]));

        // Check course 1.
        $data = writer::with_context($c1ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertCount(2, $data->ratings);
        $this->assertEquals($comp1->get('shortname'), $data->ratings[0]['name']);
        $this->assertEquals('A', $data->ratings[0]['rating']['rating']);
        $this->assertEquals(transform::yesno(true), $data->ratings[0]['rating']['proficient']);
        $this->assertEquals($comp2->get('shortname'), $data->ratings[1]['name']);
        $this->assertEquals('B', $data->ratings[1]['rating']['rating']);
        $this->assertEquals(transform::yesno(false), $data->ratings[1]['rating']['proficient']);

        // Check course 2.
        $data = writer::with_context($c2ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertEmpty($data);

        // Export user 1, in course 2.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$c2ctx->id]));
        $data = writer::with_context($c2ctx)->get_data([get_string('competencies', 'core_competency')]);
        $this->assertCount(2, $data->ratings);
        $this->assertEquals($comp2->get('shortname'), $data->ratings[0]['name']);
        $this->assertEquals('C', $data->ratings[0]['rating']['rating']);
        $this->assertEquals(transform::yesno(false), $data->ratings[0]['rating']['proficient']);
        $this->assertEquals($comp3->get('shortname'), $data->ratings[1]['name']);
        $this->assertEquals('-', $data->ratings[1]['rating']['rating']);
        $this->assertEquals('-', $data->ratings[1]['rating']['proficient']);
    }

    public function test_export_data_for_user_in_system_and_category_contexts(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category();

        $cat1ctx = \context_coursecat::instance($cat1->id);
        $cat2ctx = \context_coursecat::instance($cat2->id);
        $sysctx = \context_system::instance();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setUser($u1);
        $f1 = $ccg->create_framework();
        $f1bis = $ccg->create_framework();
        $f2 = $ccg->create_framework(['contextid' => $cat1ctx->id]);
        $c2a = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $c2b = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);

        $t1 = $ccg->create_template();
        $t2 = $ccg->create_template(['contextid' => $cat1ctx->id]);
        $tc2a = $ccg->create_template_competency(['templateid' => $t2->get('id'), 'competencyid' => $c2a->get('id')]);
        $tch2 = $ccg->create_template_cohort(['templateid' => $t2->get('id'), 'cohortid' => $c1->id]);

        $this->setUser($u2);
        $f3 = $ccg->create_framework(['contextid' => $cat2ctx->id]);
        $c1a = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c1b = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $c3a = $ccg->create_competency(['competencyframeworkid' => $f3->get('id')]);
        $c3b = $ccg->create_competency(['competencyframeworkid' => $f3->get('id')]);
        $c3c = $ccg->create_competency(['competencyframeworkid' => $f3->get('id')]);
        $c3d = $ccg->create_competency(['competencyframeworkid' => $f3->get('id')]);
        $rc1 = $ccg->create_related_competency(['competencyid' => $c2a->get('id'), 'relatedcompetencyid' => $c2b->get('id')]);

        $t3 = $ccg->create_template(['contextid' => $cat2ctx->id]);
        $tch1 = $ccg->create_template_cohort(['templateid' => $t1->get('id'), 'cohortid' => $c2->id]);
        $tc1a = $ccg->create_template_competency(['templateid' => $t1->get('id'), 'competencyid' => $c1a->get('id')]);
        $tc1b = $ccg->create_template_competency(['templateid' => $t1->get('id'), 'competencyid' => $c2a->get('id')]);
        $tc3a = $ccg->create_template_competency(['templateid' => $t3->get('id'), 'competencyid' => $c3a->get('id')]);

        $this->setUser($u1);
        $rc2 = $ccg->create_related_competency(['competencyid' => $c3a->get('id'), 'relatedcompetencyid' => $c3b->get('id')]);
        $rc3 = $ccg->create_related_competency(['competencyid' => $c3a->get('id'), 'relatedcompetencyid' => $c3c->get('id')]);

        $this->setAdminUser();
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$sysctx->id, $cat1ctx->id, $cat2ctx->id]));

        // Check frameworks for u1 in system.
        $data = writer::with_context($sysctx)->get_related_data([get_string('competencies', 'core_competency')], 'frameworks');
        $this->assertCount(2, $data->frameworks);
        $this->assertEquals($f1->get('shortname'), $data->frameworks[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->frameworks[0]['created_or_modified_by_you']);
        $this->assertEquals($f1bis->get('shortname'), $data->frameworks[1]['name']);
        $this->assertEquals(transform::yesno(true), $data->frameworks[1]['created_or_modified_by_you']);
        $this->assertEmpty($data->frameworks[0]['competencies']);
        $this->assertEmpty($data->frameworks[1]['competencies']);

        // Check templates for u1 in system.
        $data = writer::with_context($sysctx)->get_related_data([get_string('competencies', 'core_competency')], 'templates');
        $this->assertCount(1, $data->templates);
        $this->assertEquals($t1->get('shortname'), $data->templates[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['created_or_modified_by_you']);
        $this->assertEmpty($data->templates[0]['competencies']);
        $this->assertEmpty($data->templates[0]['cohorts']);

        // Check frameworks for u1 in cat1.
        $data = writer::with_context($cat1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'frameworks');
        $this->assertCount(1, $data->frameworks);
        $this->assertEquals($f2->get('shortname'), $data->frameworks[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->frameworks[0]['created_or_modified_by_you']);
        $this->assertCount(2, $data->frameworks[0]['competencies']);
        $this->assertEquals($c2a->get('shortname'), $data->frameworks[0]['competencies'][0]['name']);
        $this->assertEquals(transform::yesno(true), $data->frameworks[0]['competencies'][0]['created_or_modified_by_you']);
        $this->assertEquals($c2b->get('shortname'), $data->frameworks[0]['competencies'][1]['name']);
        $this->assertEquals(transform::yesno(true), $data->frameworks[0]['competencies'][1]['created_or_modified_by_you']);

        // Check templates for u1 in cat1.
        $data = writer::with_context($cat1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'templates');
        $this->assertCount(1, $data->templates);
        $this->assertEquals($t2->get('shortname'), $data->templates[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['created_or_modified_by_you']);
        $this->assertCount(1, $data->templates[0]['competencies']);
        $this->assertEquals($c2a->get('shortname'), $data->templates[0]['competencies'][0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['competencies'][0]['created_or_modified_by_you']);
        $this->assertCount(1, $data->templates[0]['cohorts']);
        $this->assertEquals($c1->name, $data->templates[0]['cohorts'][0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['cohorts'][0]['created_or_modified_by_you']);

        // Check frameworks for u1 in cat2.
        $data = writer::with_context($cat2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'frameworks');
        $this->assertCount(1, $data->frameworks);
        $this->assertEquals($f3->get('shortname'), $data->frameworks[0]['name']);
        $this->assertEquals(transform::yesno(false), $data->frameworks[0]['created_or_modified_by_you']);
        $this->assertCount(3, $data->frameworks[0]['competencies']);
        $competency = $data->frameworks[0]['competencies'][0];
        $this->assertEquals($c3a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(false), $competency['created_or_modified_by_you']);
        $this->assertCount(2, $competency['related_competencies']);
        $this->assertEquals($c3b->get('shortname'), $competency['related_competencies'][0]['name']);
        $this->assertEquals(transform::yesno(true), $competency['related_competencies'][0]['created_or_modified_by_you']);
        $this->assertEquals($c3c->get('shortname'), $competency['related_competencies'][1]['name']);
        $this->assertEquals(transform::yesno(true), $competency['related_competencies'][1]['created_or_modified_by_you']);
        $competency = $data->frameworks[0]['competencies'][1];
        $this->assertEquals($c3b->get('shortname'), $competency['name']);
        $this->assertCount(1, $competency['related_competencies']);
        $competency = $data->frameworks[0]['competencies'][2];
        $this->assertEquals($c3c->get('shortname'), $competency['name']);
        $this->assertCount(1, $competency['related_competencies']);

        // Check templates for u1 in cat2.
        $data = writer::with_context($cat2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'templates');
        $this->assertEmpty($data->templates);

        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$sysctx->id, $cat1ctx->id, $cat2ctx->id]));

        // Check frameworks for u2 in system.
        $data = writer::with_context($sysctx)->get_related_data([get_string('competencies', 'core_competency')], 'frameworks');
        $this->assertCount(1, $data->frameworks);
        $this->assertEquals($f1->get('shortname'), $data->frameworks[0]['name']);
        $this->assertEquals(transform::yesno(false), $data->frameworks[0]['created_or_modified_by_you']);
        $this->assertCount(2, $data->frameworks[0]['competencies']);
        $competency = $data->frameworks[0]['competencies'][0];
        $this->assertEquals($c1a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(true), $competency['created_or_modified_by_you']);
        $competency = $data->frameworks[0]['competencies'][1];
        $this->assertEquals($c1b->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(true), $competency['created_or_modified_by_you']);

        // Check templates for u2 in system.
        $data = writer::with_context($sysctx)->get_related_data([get_string('competencies', 'core_competency')], 'templates');
        $this->assertCount(1, $data->templates);
        $this->assertEquals($t1->get('shortname'), $data->templates[0]['name']);
        $this->assertEquals(transform::yesno(false), $data->templates[0]['created_or_modified_by_you']);
        $this->assertCount(2, $data->templates[0]['competencies']);
        $competency = $data->templates[0]['competencies'][0];
        $this->assertEquals($c1a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(true), $competency['created_or_modified_by_you']);
        $competency = $data->templates[0]['competencies'][1];
        $this->assertEquals($c2a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(true), $competency['created_or_modified_by_you']);
        $this->assertCount(1, $data->templates[0]['cohorts']);
        $this->assertEquals($c2->name, $data->templates[0]['cohorts'][0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['cohorts'][0]['created_or_modified_by_you']);

        // Check frameworks for u2 in cat1.
        $data = writer::with_context($cat1ctx)->get_related_data([get_string('competencies', 'core_competency')], 'frameworks');
        $this->assertCount(1, $data->frameworks);
        $this->assertEquals(transform::yesno(false), $data->frameworks[0]['created_or_modified_by_you']);
        $this->assertCount(2, $data->frameworks[0]['competencies']);
        $competency = $data->frameworks[0]['competencies'][0];
        $this->assertEquals($c2a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(false), $competency['created_or_modified_by_you']);
        $this->assertCount(1, $competency['related_competencies']);
        $this->assertEquals($c2b->get('shortname'), $competency['related_competencies'][0]['name']);
        $this->assertEquals(transform::yesno(true), $competency['related_competencies'][0]['created_or_modified_by_you']);

        // Check templates for u2 in system.
        $data = writer::with_context($cat2ctx)->get_related_data([get_string('competencies', 'core_competency')], 'templates');
        $this->assertCount(1, $data->templates);
        $this->assertEquals($t3->get('shortname'), $data->templates[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->templates[0]['created_or_modified_by_you']);
        $this->assertCount(1, $data->templates[0]['competencies']);
        $competency = $data->templates[0]['competencies'][0];
        $this->assertEquals($c3a->get('shortname'), $competency['name']);
        $this->assertEquals(transform::yesno(true), $competency['created_or_modified_by_you']);
    }

    public function test_export_data_for_user_with_related_learning_plans(): void {
        global $DB;

        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('privacy:path:plans', 'core_competency'),
        ];
        $yes = transform::yesno(true);
        $no = transform::yesno(false);

        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $dg->role_assign($DB->get_field('role', 'id', ['archetype' => 'manager'], IGNORE_MULTIPLE), $u6->id);
        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp4 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $t = $ccg->create_template();
        $tc1 = $ccg->create_template_competency(['competencyid' => $comp1->get('id'), 'templateid' => $t->get('id')]);
        $tc2 = $ccg->create_template_competency(['competencyid' => $comp2->get('id'), 'templateid' => $t->get('id')]);
        $tc3 = $ccg->create_template_competency(['competencyid' => $comp3->get('id'), 'templateid' => $t->get('id')]);
        $tc4 = $ccg->create_template_competency(['competencyid' => $comp4->get('id'), 'templateid' => $t->get('id')]);

        $this->setUser($u1);
        $p1 = $ccg->create_plan(['templateid' => $t->get('id'), 'userid' => $u0->id]);

        $this->setUser($u2);
        $p2 = $ccg->create_plan(['userid' => $u0->id, 'reviewerid' => $u7->id]);

        $this->setUser($u3);
        $p1c1 = $ccg->create_plan_competency(['planid' => $p1->get('id'), 'competencyid' => $comp1->get('id')]);
        $p2c2 = $ccg->create_plan_competency(['planid' => $p2->get('id'), 'competencyid' => $comp2->get('id')]);
        $p2c3 = $ccg->create_plan_competency(['planid' => $p2->get('id'), 'competencyid' => $comp3->get('id')]);

        $this->setUser($u4);
        $uc1 = $ccg->create_user_competency(['competencyid' => $comp1->get('id'), 'userid' => $u0->id, 'grade' => 1,
            'proficiency' => true]);
        $uc2 = $ccg->create_user_competency(['competencyid' => $comp2->get('id'), 'userid' => $u0->id, 'grade' => 2,
            'proficiency' => false]);
        $uc3 = $ccg->create_user_competency(['competencyid' => $comp3->get('id'), 'userid' => $u0->id]);
        $uc4 = $ccg->create_user_competency(['competencyid' => $comp4->get('id'), 'userid' => $u0->id, 'reviewerid' => $u5->id]);

        $this->setUser($u5);
        $p3 = $ccg->create_plan(['userid' => $u0->id]);
        $p3c1 = $ccg->create_plan_competency(['planid' => $p3->get('id'), 'competencyid' => $comp1->get('id')]);
        $p3c3 = $ccg->create_plan_competency(['planid' => $p3->get('id'), 'competencyid' => $comp3->get('id')]);

        // Add comments on plan.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u0);
        $p1->get_comment_object()->add('Hello.');
        $this->setUser($u8);
        $p1->get_comment_object()->add('Hi.');

        // Export data for user 1.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p1->get('name')} ({$p1->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p1->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);

        // Export data for user 2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p2->get('name')} ({$p2->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p2->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);

        // Export data for user 3.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u3, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p1->get('name')} ({$p1->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p1->get('name'), $data->name);
        $this->assertEquals($no, $data->created_or_modified_by_you);
        $this->assertCount(1, $data->competencies);
        $this->assertEquals($comp1->get('shortname'), $data->competencies[0]['name']);
        $this->assertEquals($yes, $data->competencies[0]['created_or_modified_by_you']);

        $planpath = array_merge($path, ["{$p2->get('name')} ({$p2->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p2->get('name'), $data->name);
        $this->assertEquals($no, $data->created_or_modified_by_you);
        $competencies = $data->competencies;
        $this->assertCount(2, $competencies);
        $this->assertEquals($comp2->get('shortname'), $competencies[0]['name']);
        $this->assertEquals($yes, $competencies[0]['created_or_modified_by_you']);
        $this->assertEquals($comp3->get('shortname'), $competencies[1]['name']);
        $this->assertEquals($yes, $competencies[1]['created_or_modified_by_you']);

        // Export data for user 4.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u4, 'core_competency', [$u0ctx->id]));
        foreach ([$p1, $p2, $p3] as $plan) {
            $planpath = array_merge($path, ["{$p2->get('name')} ({$p2->get('id')})"]);
            $data = writer::with_context($u0ctx)->get_data($planpath);
            $this->assertEmpty($data);
        }

        // Export data for user 5.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u5, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p3->get('name')} ({$p3->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p3->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);
        $this->assertCount(2, $data->competencies);
        $competency = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $this->assertEquals($yes, $competency['created_or_modified_by_you']);
        $competency = $data->competencies[1];
        $this->assertEquals($comp3->get('shortname'), $competency['name']);
        $this->assertEquals($yes, $competency['created_or_modified_by_you']);

        // Do some stuff.
        $this->setUser($u6);
        api::complete_plan($p3);

        // Export data for user 6.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u6, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p3->get('name')} ({$p3->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p3->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);
        $this->assertCount(2, $data->competencies);
        $competency = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $this->assertArrayNotHasKey('created_or_modified_by_you', $competency);
        $this->assertEquals('A', $competency['rating']['rating']);
        $this->assertEquals($yes, $competency['rating']['created_or_modified_by_you']);
        $competency = $data->competencies[1];
        $this->assertEquals($comp3->get('shortname'), $competency['name']);
        $this->assertArrayNotHasKey('created_or_modified_by_you', $competency);
        $this->assertEquals('-', $competency['rating']['rating']);
        $this->assertEquals($yes, $competency['rating']['created_or_modified_by_you']);

        // Export data for user 7.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u7, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p2->get('name')} ({$p2->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p2->get('name'), $data->name);
        $this->assertEquals($no, $data->created_or_modified_by_you);
        $this->assertEquals($yes, $data->reviewer_is_you);

        // Export data for user 8.
        writer::reset();
        $this->setUser($u8);
        provider::export_user_data(new approved_contextlist($u8, 'core_competency', [$u0ctx->id]));
        $planpath = array_merge($path, ["{$p1->get('name')} ({$p1->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($planpath);
        $this->assertEquals($p1->get('name'), $data->name);
        $this->assertEquals($no, $data->created_or_modified_by_you);
        $this->assertEquals($no, $data->reviewer_is_you);
        $commentspath = array_merge($planpath,  [get_string('commentsubcontext', 'core_comment')]);
        $data = writer::with_context($u0ctx)->get_data($commentspath);
        $this->assert_exported_comments(['Hi.'], $data->comments);
    }

    public function test_export_data_for_user_with_related_competencies(): void {
        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('competencies', 'core_competency'),
        ];
        $yes = transform::yesno(true);
        $no = transform::yesno(false);
        $makecomppath = function($comp) use ($path) {
            return array_merge($path, ["{$comp->get('shortname')} ({$comp->get('id')})"]);
        };

        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u0 = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();

        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp4 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u1);
        api::add_evidence($u0->id, $comp1->get('id'), $u0ctx, \core_competency\evidence::ACTION_LOG,
            'privacy:metadata:competency_evidence', 'core_competency');
        api::add_evidence($u0->id, $comp1->get('id'), $u0ctx, \core_competency\evidence::ACTION_LOG,
            'privacy:metadata:competency_evidence', 'core_competency');
        api::add_evidence($u0->id, $comp2->get('id'), $u0ctx, \core_competency\evidence::ACTION_LOG,
            'privacy:metadata:competency_evidence', 'core_competency');

        $this->setUser($u2);
        api::add_evidence($u0->id, $comp1->get('id'), $u0ctx, \core_competency\evidence::ACTION_COMPLETE,
            'privacy:metadata:competency_evidence', 'core_competency', null, false, null, null, $u3->id);

        $this->setUser($u3);
        api::add_evidence($u0->id, $comp2->get('id'), $u0ctx, \core_competency\evidence::ACTION_OVERRIDE,
            'privacy:metadata:competency_evidence', 'core_competency', null, false, null, 1, $u4->id, 'Ze note');

        $this->setUser($u4);
        $uc3 = $ccg->create_user_competency(['userid' => $u0->id, 'competencyid' => $comp3->get('id')]);
        $uc4 = $ccg->create_user_competency(['userid' => $u0->id, 'competencyid' => $comp4->get('id'), 'reviewerid' => $u2->id]);

        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u0);
        $uc3->get_comment_object()->add('...');
        $this->setUser($u5);
        $uc3->get_comment_object()->add('Hello!');
        $uc3->get_comment_object()->add('It\'s me...');

        // Export data for user 1.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u0ctx->id]));
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp1));
        $competency = (array) $data;
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $evidence = $competency['evidence'];
        $this->assertCount(2, $evidence);
        $this->assertEquals(get_string('privacy:evidence:action:log', 'core_competency'), $evidence[0]['action']);
        $this->assertEquals('-', $evidence[0]['actionuserid']);
        $this->assertEquals($no, $evidence[0]['acting_user_is_you']);
        $this->assertEquals($yes, $evidence[0]['created_or_modified_by_you']);
        $this->assertEquals(get_string('privacy:evidence:action:log', 'core_competency'), $evidence[1]['action']);
        $this->assertEquals('-', $evidence[1]['actionuserid']);
        $this->assertEquals($no, $evidence[1]['acting_user_is_you']);
        $this->assertEquals($yes, $evidence[1]['created_or_modified_by_you']);
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp2));
        $competency = (array) $data;
        $this->assertEquals($comp2->get('shortname'), $competency['name']);
        $evidence = $competency['evidence'];
        $this->assertCount(1, $evidence);
        $this->assertEquals(get_string('privacy:evidence:action:log', 'core_competency'), $evidence[0]['action']);
        $this->assertEquals('-', $evidence[0]['actionuserid']);
        $this->assertEquals($no, $evidence[0]['acting_user_is_you']);
        $this->assertEquals($yes, $evidence[0]['created_or_modified_by_you']);

        // Export data for user 2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u0ctx->id]));
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp1));
        $competency = (array) $data;
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $evidence = $competency['evidence'];
        $this->assertCount(1, $evidence);
        $this->assertEquals(get_string('privacy:evidence:action:complete', 'core_competency'), $evidence[0]['action']);
        $this->assertEquals($u3->id, $evidence[0]['actionuserid']);
        $this->assertEquals($no, $evidence[0]['acting_user_is_you']);
        $this->assertEquals($yes, $evidence[0]['created_or_modified_by_you']);
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp4));
        $competency = (array) $data;
        $this->assertEquals($comp4->get('shortname'), $competency['name']);
        $this->assertCount(0, $competency['evidence']);
        $this->assertEquals($yes, $competency['rating']['reviewer_is_you']);
        $this->assertEquals($no, $competency['rating']['created_or_modified_by_you']);

        // Export data for user 3.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u3, 'core_competency', [$u0ctx->id]));
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp1));
        $competency = (array) $data;
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $evidence = $competency['evidence'];
        $this->assertCount(1, $evidence);
        $this->assertEquals($u3->id, $evidence[0]['actionuserid']);
        $this->assertEquals($yes, $evidence[0]['acting_user_is_you']);
        $this->assertEquals($no, $evidence[0]['created_or_modified_by_you']);
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp2));
        $competency = (array) $data;
        $this->assertEquals($comp2->get('shortname'), $competency['name']);
        $evidence = $competency['evidence'];
        $this->assertCount(1, $evidence);
        $this->assertEquals(get_string('privacy:evidence:action:override', 'core_competency'), $evidence[0]['action']);
        $this->assertEquals($u4->id, $evidence[0]['actionuserid']);
        $this->assertEquals($no, $evidence[0]['acting_user_is_you']);
        $this->assertEquals($yes, $evidence[0]['created_or_modified_by_you']);

        // Export data for user 4.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u4, 'core_competency', [$u0ctx->id]));
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp2));
        $competency = (array) $data;
        $this->assertEquals($comp2->get('shortname'), $competency['name']);
        $this->assertNull($competency['rating']);
        $this->assertCount(1, $competency['evidence']);
        $evidence = $competency['evidence'][0];
        $this->assertEquals($u4->id, $evidence['actionuserid']);
        $this->assertEquals($yes, $evidence['acting_user_is_you']);
        $this->assertEquals($no, $evidence['created_or_modified_by_you']);
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp3));
        $competency = (array) $data;
        $this->assertEquals($comp3->get('shortname'), $competency['name']);
        $this->assertEquals($no, $competency['rating']['reviewer_is_you']);
        $this->assertEquals($yes, $competency['rating']['created_or_modified_by_you']);
        $this->assertEmpty($competency['evidence']);
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp4));
        $competency = (array) $data;
        $this->assertEquals($comp4->get('shortname'), $competency['name']);
        $this->assertEquals($no, $competency['rating']['reviewer_is_you']);
        $this->assertEquals($yes, $competency['rating']['created_or_modified_by_you']);
        $this->assertEmpty($competency['evidence']);

        // Export data for user 5.
        $this->setUser($u5);
        writer::reset();
        provider::export_user_data(new approved_contextlist($u5, 'core_competency', [$u0ctx->id]));
        $data = writer::with_context($u0ctx)->get_data($makecomppath($comp3));
        $competency = (array) $data;
        $this->assertEquals($comp3->get('shortname'), $competency['name']);
        $data = writer::with_context($u0ctx)->get_data(array_merge($makecomppath($comp3),
            [get_string('commentsubcontext', 'core_comment')]));
        $this->assert_exported_comments(['Hello!', 'It\'s me...'], $data->comments);
    }

    public function test_export_data_for_user_with_related_user_evidence(): void {
        $path = [
            get_string('competencies', 'core_competency'),
            get_string('privacy:path:relatedtome', 'core_competency'),
            get_string('privacy:path:userevidence', 'core_competency')
        ];
        $yes = transform::yesno(true);
        $no = transform::yesno(false);

        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');

        $u0 = $dg->create_user();
        $u0b = $dg->create_user();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $u0ctx = \context_user::instance($u0->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $this->setUser($u0);
        $ue0 = $ccg->create_user_evidence(['userid' => $u0->id]);

        $this->setUser($u1);
        $ue1 = $ccg->create_user_evidence(['userid' => $u0->id]);
        $ue1b = $ccg->create_user_evidence(['userid' => $u0b->id]);

        $this->setUser($u2);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue1->get('id'), 'competencyid' => $comp1->get('id')]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue1b->get('id'), 'competencyid' => $comp1->get('id')]);
        $ue2 = $ccg->create_user_evidence(['userid' => $u0->id]);
        $ue2b = $ccg->create_user_evidence(['userid' => $u0b->id]);

        $this->setUser($u3);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'), 'competencyid' => $comp2->get('id')]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue2->get('id'), 'competencyid' => $comp3->get('id')]);

        // Export for user 1.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u0ctx->id]));
        $uepath = array_merge($path, ["{$ue1->get('name')} ({$ue1->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($uepath);
        $this->assertEquals($ue1->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);
        $this->assertEmpty($data->competencies);

        // Export for user 2.
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u0ctx->id]));
        $uepath = array_merge($path, ["{$ue1->get('name')} ({$ue1->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($uepath);
        $this->assertEquals($ue1->get('name'), $data->name);
        $this->assertEquals($no, $data->created_or_modified_by_you);
        $this->assertCount(1, $data->competencies);
        $competency = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $competency['name']);
        $this->assertEquals($yes, $competency['created_or_modified_by_you']);

        $uepath = array_merge($path, ["{$ue2->get('name')} ({$ue2->get('id')})"]);
        $data = writer::with_context($u0ctx)->get_data($uepath);
        $this->assertEquals($ue2->get('name'), $data->name);
        $this->assertEquals($yes, $data->created_or_modified_by_you);
        $this->assertEmpty($data->competencies);

        // Export for user 3.
        provider::export_user_data(new approved_contextlist($u3, 'core_competency', [$u0ctx->id]));
        $uepath = array_merge($path, ["{$ue2->get('name')} ({$ue2->get('id')})"]);
        $evidence = writer::with_context($u0ctx)->get_data($uepath);
        $this->assertEquals($ue2->get('name'), $evidence->name);
        $this->assertEquals($no, $evidence->created_or_modified_by_you);
        $this->assertCount(2, $evidence->competencies);
        $competency = $evidence->competencies[0];
        $this->assertEquals($comp2->get('shortname'), $competency['name']);
        $this->assertEquals($yes, $competency['created_or_modified_by_you']);
        $competency = $evidence->competencies[1];
        $this->assertEquals($comp3->get('shortname'), $competency['name']);
        $this->assertEquals($yes, $competency['created_or_modified_by_you']);
    }

    public function test_export_data_for_user_about_their_learning_plans(): void {
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $path = [get_string('competencies', 'core_competency'), get_string('privacy:path:plans', 'core_competency')];
        $yes = transform::yesno(true);
        $no = transform::yesno(false);

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp4 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $t = $ccg->create_template();
        $tc2 = $ccg->create_template_competency(['competencyid' => $comp2->get('id'), 'templateid' => $t->get('id')]);
        $tc3 = $ccg->create_template_competency(['competencyid' => $comp3->get('id'), 'templateid' => $t->get('id')]);
        $tc4 = $ccg->create_template_competency(['competencyid' => $comp4->get('id'), 'templateid' => $t->get('id')]);

        $p1a = $ccg->create_plan(['userid' => $u1->id, 'templateid' => $t->get('id'),
            'status' => \core_competency\plan::STATUS_WAITING_FOR_REVIEW]);
        $p1b = $ccg->create_plan(['userid' => $u1->id]);
        $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp1->get('id')]);
        $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp2->get('id')]);
        $ccg->create_plan_competency(['planid' => $p1b->get('id'), 'competencyid' => $comp4->get('id')]);
        $p1c = $ccg->create_plan(['userid' => $u1->id]);
        $ccg->create_plan_competency(['planid' => $p1c->get('id'), 'competencyid' => $comp1->get('id')]);
        $ccg->create_plan_competency(['planid' => $p1c->get('id'), 'competencyid' => $comp3->get('id')]);
        $ccg->create_plan_competency(['planid' => $p1c->get('id'), 'competencyid' => $comp4->get('id')]);
        $p1d = $ccg->create_plan(['userid' => $u1->id]);

        $p2a = $ccg->create_plan(['userid' => $u2->id]);
        $ccg->create_plan_competency(['planid' => $p2a->get('id'), 'competencyid' => $comp1->get('id')]);
        $ccg->create_plan_competency(['planid' => $p2a->get('id'), 'competencyid' => $comp2->get('id')]);

        $uc1a = $ccg->create_user_competency(['competencyid' => $comp1->get('id'), 'userid' => $u1->id,
            'grade' => 2, 'proficiency' => false]);
        $uc1b = $ccg->create_user_competency(['competencyid' => $comp2->get('id'), 'userid' => $u1->id,
            'grade' => 3, 'proficiency' => false]);
        $uc1c = $ccg->create_user_competency(['competencyid' => $comp3->get('id'), 'userid' => $u1->id]);

        // Add comments on plan.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u1);
        $p1a->get_comment_object()->add('Hello.');
        $p1a->get_comment_object()->add('It\'s me.');
        $this->setUser($u3);
        $p1a->get_comment_object()->add('After all these years...');

        // Complete the plan to create archiving, and modify the user competency again.
        api::complete_plan($p1c);
        $uc1a->set('grade', 1);
        $uc1a->set('proficiency', true);
        $uc1a->update();

        // Export user data in both contexts.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u1ctx->id, $u2ctx->id]));

        // This plan is based off a template.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1a->get('name')} ({$p1a->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($p1a->get('name'), $data->name);
        $this->assertEquals($p1a->get_statusname(), $data->status);
        $this->assertCount(3, $data->competencies);
        $comp = $data->competencies[0];
        $this->assertEquals($comp2->get('shortname'), $comp['name']);
        $this->assertEquals('C', $comp['rating']['rating']);
        $comp = $data->competencies[1];
        $this->assertEquals($comp3->get('shortname'), $comp['name']);
        $this->assertEquals('-', $comp['rating']['rating']);
        $comp = $data->competencies[2];
        $this->assertEquals($comp4->get('shortname'), $comp['name']);
        $this->assertNull($comp['rating']);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1a->get('name')} ({$p1a->get('id')})",
            get_string('commentsubcontext', 'core_comment')]));
        $this->assert_exported_comments(['Hello.', 'It\'s me.', 'After all these years...'], $data->comments);

        // This plan is manually created.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1b->get('name')} ({$p1b->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($p1b->get('name'), $data->name);
        $this->assertCount(3, $data->competencies);
        $comp = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $comp['name']);
        $this->assertEquals('A', $comp['rating']['rating']);
        $comp = $data->competencies[1];
        $this->assertEquals($comp2->get('shortname'), $comp['name']);
        $this->assertEquals('C', $comp['rating']['rating']);
        $comp = $data->competencies[2];
        $this->assertEquals($comp4->get('shortname'), $comp['name']);
        $this->assertNull($comp['rating']);

        // This plan is complete.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1c->get('name')} ({$p1c->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($p1c->get('name'), $data->name);
        $this->assertCount(3, $data->competencies);
        $comp = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $comp['name']);
        $this->assertEquals('B', $comp['rating']['rating']);
        $comp = $data->competencies[1];
        $this->assertEquals($comp3->get('shortname'), $comp['name']);
        $this->assertEquals('-', $comp['rating']['rating']);
        $comp = $data->competencies[2];
        $this->assertEquals($comp4->get('shortname'), $comp['name']);
        $this->assertEquals('-', $comp['rating']['rating']);

        // This plan is empty.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1d->get('name')} ({$p1d->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($p1d->get('name'), $data->name);
        $this->assertEquals($p1d->get_statusname(), $data->status);
        $this->assertEmpty($data->competencies);

        // Confirm that we do not get export what we shouldn't.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p2a->get('name')} ({$p2a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1a->get('name')} ({$p1a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1b->get('name')} ({$p1b->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1c->get('name')} ({$p1c->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p2a->get('name')} ({$p2a->get('id')})"]));
        $this->assertEmpty($data);

        // Export for user 2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u1ctx->id, $u2ctx->id]));

        // Validate the basic plan.
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p2a->get('name')} ({$p2a->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($p2a->get('name'), $data->name);
        $this->assertCount(2, $data->competencies);
        $comp = $data->competencies[0];
        $this->assertEquals($comp1->get('shortname'), $comp['name']);
        $this->assertNull($comp['rating']);
        $comp = $data->competencies[1];
        $this->assertEquals($comp2->get('shortname'), $comp['name']);
        $this->assertNull($comp['rating']);

        // Confirm that we do not get export what we shouldn't.
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1a->get('name')} ({$p1a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1b->get('name')} ({$p1b->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$p1c->get('name')} ({$p1c->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1a->get('name')} ({$p1a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1b->get('name')} ({$p1b->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$p1c->get('name')} ({$p1c->get('id')})"]));
        $this->assertEmpty($data);
    }

    public function test_export_data_for_user_about_their_competencies(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $path = [get_string('competencies', 'core_competency'), get_string('competencies', 'core_competency')];
        $no = transform::yesno(false);

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $uc1a = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp1->get('id')]);
        $uc1b = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp2->get('id'),
            'grade' => 2, 'proficiency' => false]);
        $uc1c = $ccg->create_user_competency(['userid' => $u1->id, 'competencyid' => $comp3->get('id')]);
        $e1a1 = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id'),
            'action' => \core_competency\evidence::ACTION_COMPLETE, 'grade' => 1]);
        $e1a2 = $ccg->create_evidence(['usercompetencyid' => $uc1a->get('id'), 'note' => 'Not too bad']);
        $e1b1 = $ccg->create_evidence(['usercompetencyid' => $uc1b->get('id'), 'url' => 'https://example.com']);

        $uc2a = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp1->get('id')]);
        $uc2b = $ccg->create_user_competency(['userid' => $u2->id, 'competencyid' => $comp2->get('id')]);
        $e2a1 = $ccg->create_evidence(['usercompetencyid' => $uc2b->get('id'), 'note' => 'A']);
        $e2a2 = $ccg->create_evidence(['usercompetencyid' => $uc2b->get('id'), 'note' => 'B']);
        $e2a3 = $ccg->create_evidence(['usercompetencyid' => $uc2b->get('id'), 'note' => 'C']);

        // Add comments on competency.
        $this->allow_anyone_to_comment_anywhere();
        $this->setUser($u1);
        $uc1a->get_comment_object()->add('Hello.');
        $uc1a->get_comment_object()->add('It\'s me.');
        $this->setUser($u3);
        $uc1a->get_comment_object()->add('After all these years...');

        // Export for user 1 in both contexts.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp1->get('shortname')} ({$comp1->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($comp1->get('shortname'), $data->name);
        $this->assertEquals('-', $data->rating['rating']);
        $this->assertCount(2, $data->evidence);
        $this->assertEquals(get_string('privacy:evidence:action:complete', 'core_competency'), $data->evidence[1]['action']);
        $this->assertEquals('Not too bad', $data->evidence[0]['note']);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp1->get('shortname')} ({$comp1->get('id')})",
            get_string('commentsubcontext', 'core_comment')]));
        $this->assert_exported_comments(['Hello.', 'It\'s me.', 'After all these years...'], $data->comments);

        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp2->get('shortname')} ({$comp2->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($comp2->get('shortname'), $data->name);
        $this->assertEquals('B', $data->rating['rating']);
        $this->assertEquals($no, $data->rating['proficient']);
        $this->assertCount(1, $data->evidence);
        $this->assertEquals('https://example.com', $data->evidence[0]['url']);

        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp3->get('shortname')} ({$comp3->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($comp3->get('shortname'), $data->name);
        $this->assertEquals('-', $data->rating['rating']);
        $this->assertEquals('-', $data->rating['proficient']);
        $this->assertEmpty($data->evidence);

        // We don't know anything about user 2.
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp1->get('shortname')} ({$comp1->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp2->get('shortname')} ({$comp2->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp3->get('shortname')} ({$comp3->get('id')})"]));
        $this->assertEmpty($data);

        // Export for user 2 in both contexts.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp1->get('shortname')} ({$comp1->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($comp1->get('shortname'), $data->name);
        $this->assertEquals('-', $data->rating['rating']);
        $this->assertCount(0, $data->evidence);

        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp2->get('shortname')} ({$comp2->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($comp2->get('shortname'), $data->name);
        $this->assertEquals('-', $data->rating['rating']);
        $this->assertCount(3, $data->evidence);
        $this->assertEquals('C', $data->evidence[0]['note']);
        $this->assertEquals('B', $data->evidence[1]['note']);
        $this->assertEquals('A', $data->evidence[2]['note']);

        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$comp3->get('shortname')} ({$comp3->get('id')})"]));
        $this->assertEmpty($data);

        // We don't know anything about user 1.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp1->get('shortname')} ({$comp1->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp2->get('shortname')} ({$comp2->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$comp3->get('shortname')} ({$comp3->get('id')})"]));
        $this->assertEmpty($data);
    }

    public function test_export_data_for_user_about_their_user_evidence(): void {
        $dg = $this->getDataGenerator();
        $ccg = $dg->get_plugin_generator('core_competency');
        $path = [get_string('competencies', 'core_competency'), get_string('privacy:path:userevidence', 'core_competency')];

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);
        $u3ctx = \context_user::instance($u3->id);

        $f = $ccg->create_framework();
        $comp1 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp2 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);
        $comp3 = $ccg->create_competency(['competencyframeworkid' => $f->get('id')]);

        $ue1a = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue1b = $ccg->create_user_evidence(['userid' => $u1->id]);
        $ue2a = $ccg->create_user_evidence(['userid' => $u2->id]);
        $ue3a = $ccg->create_user_evidence(['userid' => $u3->id]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'), 'competencyid' => $comp1->get('id')]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue1a->get('id'), 'competencyid' => $comp2->get('id')]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue1b->get('id'), 'competencyid' => $comp2->get('id')]);
        $ccg->create_user_evidence_competency(['userevidenceid' => $ue2a->get('id'), 'competencyid' => $comp2->get('id')]);

        // Export for user 1 in two contexts to make sure.
        provider::export_user_data(new approved_contextlist($u1, 'core_competency', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$ue1a->get('name')} ({$ue1a->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($ue1a->get('name'), $data->name);
        $this->assertCount(2, $data->competencies);
        $this->assertEquals($comp1->get('shortname'), $data->competencies[0]['name']);
        $this->assertEquals($comp2->get('shortname'), $data->competencies[1]['name']);

        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$ue1b->get('name')} ({$ue1b->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($ue1b->get('name'), $data->name);
        $this->assertCount(1, $data->competencies);
        $this->assertEquals($comp2->get('shortname'), $data->competencies[0]['name']);

        // We should not have access to other's info.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$ue2a->get('name')} ({$ue2a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$ue2a->get('name')} ({$ue2a->get('id')})"]));
        $this->assertEmpty($data);

        // Export for user 2 in two contexts to make sure.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_competency', [$u2ctx->id, $u1ctx->id]));
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$ue2a->get('name')} ({$ue2a->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($ue2a->get('name'), $data->name);
        $this->assertCount(1, $data->competencies);
        $this->assertEquals($comp2->get('shortname'), $data->competencies[0]['name']);

        // We should not have access to other's info.
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$ue1a->get('name')} ({$ue1a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$ue1a->get('name')} ({$ue1a->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data(array_merge($path, ["{$ue1b->get('name')} ({$ue1b->get('id')})"]));
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data(array_merge($path, ["{$ue1b->get('name')} ({$ue1b->get('id')})"]));
        $this->assertEmpty($data);

        // Export for user 3.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u3, 'core_competency', [$u3ctx->id]));
        $data = writer::with_context($u3ctx)->get_data(array_merge($path, ["{$ue3a->get('name')} ({$ue3a->get('id')})"]));
        $this->assertNotEmpty($data);
        $this->assertEquals($ue3a->get('name'), $data->name);
        $this->assertCount(0, $data->competencies);
    }

    /**
     * Helps testing comments on plans.
     *
     * @return void
     */
    protected function allow_anyone_to_comment_anywhere() {
        global $DB;
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'user'], MUST_EXIST);
        assign_capability('moodle/competency:plancomment', CAP_ALLOW, $roleid, SYSCONTEXTID, true);
        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $roleid, SYSCONTEXTID, true);
        assign_capability('moodle/competency:planmanagedraft', CAP_ALLOW, $roleid, SYSCONTEXTID, true);
        assign_capability('moodle/competency:usercompetencycomment', CAP_ALLOW, $roleid, SYSCONTEXTID, true);
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $roleid, SYSCONTEXTID, true);
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Assert the content of a contextlist.
     *
     * @param contextlist $contextlist The list.
     * @param array $expectedcontextsorids The expected content.
     * @return void
     */
    protected function assert_contextlist(contextlist $contextlist, $expectedcontextsorids) {
        $contextids = array_unique($contextlist->get_contextids());
        $expectedids = array_unique(array_map(function($item) {
            return $item instanceof \context ? $item->id : $id;
        }, $expectedcontextsorids));
        $this->assert_array_match($expectedids, $contextids);
    }

    /**
     * Assert that array match.
     *
     * @param array $array1 The first one.
     * @param array $array2 The second one.
     * @return void
     */
    protected function assert_array_match($array1, $array2) {
        $array1 = (array) (object) $array1;
        $array2 = (array) (object) $array2;
        sort($array1);
        sort($array2);
        $this->assertEquals($array1, $array2);
    }

    /**
     * Assert the content of exported comments.
     *
     * @param array $expected The content of the comments.
     * @param array $comments The exported comments.
     * @return void
     */
    protected function assert_exported_comments($expected, $comments) {
        $this->assertCount(count($expected), $comments);
        $contents = array_map(function($comment) {
            return strip_tags($comment->content);
        }, $comments);
        $this->assert_array_match($expected, $contents);
    }

    /**
     * Assert that a comment object has comments.
     *
     * @param \comment $comment The comment object.
     * @return void
     */
    protected function assert_has_comments(\comment $comment) {
        global $DB;
        $this->assertTrue($DB->record_exists('comments', [
            'contextid' => $comment->get_context()->id,
            'component' => $comment->get_component(),
            'commentarea' => $comment->get_commentarea(),
            'itemid' => $comment->get_itemid()
        ]));
    }

    /**
     * Assert that a comment object does not have any comments.
     *
     * @param \comment $comment The comment object.
     * @return void
     */
    protected function assert_has_no_comments(\comment $comment) {
        global $DB;
        $this->assertFalse($DB->record_exists('comments', [
            'contextid' => $comment->get_context()->id,
            'component' => $comment->get_component(),
            'commentarea' => $comment->get_commentarea(),
            'itemid' => $comment->get_itemid()
        ]));
    }

    /**
     * Get the count of comments.
     *
     * @param \comment $comment The comment object.
     * @param int $userid The user ID.
     * @return int
     */
    protected function get_comments_count(\comment $comment, $userid = null) {
        global $DB;
        $params = [
            'contextid' => $comment->get_context()->id,
            'component' => $comment->get_component(),
            'commentarea' => $comment->get_commentarea(),
            'itemid' => $comment->get_itemid(),
        ];
        if ($userid) {
            $params['userid'] = $userid;
        }
        return $DB->count_records('comments', $params);
    }
}
