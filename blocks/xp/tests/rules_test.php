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
 * Test rules.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp_rule;
use block_xp_rule_base;
use block_xp_rule_property;
use block_xp_ruleset;

/**
 * Rules testcase.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class rules_test extends \advanced_testcase {

    /**
     * Test rule property.
     *
     * @covers \block_xp_rule_property
     */
    public function test_rule_property(): void {
        $subject = (object) [
            'int' => 10,
            'str' => 'I am here.',
        ];

        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'I', 'str');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'am not', 'str');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 11, 'int');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 'I am here.', 'str');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 'I am not here.', 'str');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::EQS, 10, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::EQS, '10', 'int');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::GT, 5, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::GT, 15, 'int');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::GTE, 5, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::GTE, 10, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::GTE, 11, 'int');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::LT, 5, 'int');
        $this->assertFalse($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::LT, 15, 'int');
        $this->assertTrue($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::LTE, 5, 'int');
        $this->assertFalse($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::LTE, 10, 'int');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::LTE, 9, 'int');
        $this->assertFalse($rule->match($subject));

        $rule = new block_xp_rule_property(block_xp_rule_base::RX, '/^I/', 'str');
        $this->assertTrue($rule->match($subject));
        $rule = new block_xp_rule_property(block_xp_rule_base::RX, '/^You/', 'str');
        $this->assertFalse($rule->match($subject));

    }

    /**
     * Test ruleset.
     *
     * @covers \block_xp_ruleset
     */
    public function test_ruleset(): void {
        $subject = (object) [
            'int' => 10,
        ];

        $rs = new block_xp_ruleset([], block_xp_ruleset::ANY);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 99, 'int'));
        $this->assertTrue($rs->match($subject));

        $rs = new block_xp_ruleset([], block_xp_ruleset::ANY);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 0, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 99, 'int'));
        $this->assertFalse($rs->match($subject));

        $rs = new block_xp_ruleset([], block_xp_ruleset::ALL);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 9, 'int'));
        $this->assertFalse($rs->match($subject));

        $rs = new block_xp_ruleset([], block_xp_ruleset::ALL);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::LTE, 10, 'int'));
        $this->assertTrue($rs->match($subject));

        $rs = new block_xp_ruleset([], block_xp_ruleset::NONE);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 99, 'int'));
        $this->assertFalse($rs->match($subject));

        $rs = new block_xp_ruleset([], block_xp_ruleset::NONE);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 0, 'int'));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 99, 'int'));
        $this->assertTrue($rs->match($subject));
    }

    /**
     * Test nested ruleset.
     *
     * @covers \block_xp_ruleset
     */
    public function test_nested_ruleset(): void {
        $subject = (object) [
            'int' => 10,
        ];

        $rs = new block_xp_ruleset([], block_xp_ruleset::ANY);
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 0, 'int'));
        $rs->add_rule(new block_xp_ruleset([
            new block_xp_rule_property(block_xp_rule_base::EQ, 10, 'int'),
            new block_xp_rule_property(block_xp_rule_base::LTE, 10, 'int'),
            new block_xp_rule_property(block_xp_rule_base::GTE, 10, 'int'),
        ], block_xp_ruleset::ALL));
        $rs->add_rule(new block_xp_rule_property(block_xp_rule_base::EQ, 99, 'int'));
        $this->assertTrue($rs->match($subject));

    }

    /**
     * Test export create.
     *
     * @covers \block_xp_rule::create
     */
    public function test_export_create(): void {
        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'I', 'str');
        $newrule = block_xp_rule::create($rule->export());
        $this->assertEquals($rule, $newrule);

        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'I', 'str');
        $rs = new block_xp_ruleset([$rule], block_xp_ruleset::ALL);
        $newrs = block_xp_rule::create($rs->export());
        $this->assertEquals($rs, $newrs);

        // Test with bad data.
        $data = $rs->export();
        $data['_class'] = 'Does not exist';
        $this->assertFalse(block_xp_rule::create($data));
    }
}
