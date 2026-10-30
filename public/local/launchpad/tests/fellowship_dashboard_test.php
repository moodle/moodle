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

namespace local_launchpad;

/**
 * Fellowship dashboard helper tests (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\fellowship_dashboard
 */
final class fellowship_dashboard_test extends \basic_testcase {

    public function test_list_courses_invalid_user_empty(): void {
        $this->assertSame([], fellowship_dashboard::list_courses(0));
        $this->assertSame([], fellowship_dashboard::list_courses(-1));
    }

    public function test_can_access_rejects_invalid(): void {
        $this->assertFalse(fellowship_dashboard::can_access(0));
        $this->assertFalse(fellowship_dashboard::can_access(-3));
    }

    public function test_mod_icon_class_known_modules(): void {
        $this->assertSame('quiz', fellowship_dashboard::mod_icon_class('quiz'));
        $this->assertSame('assign', fellowship_dashboard::mod_icon_class('assign'));
        $this->assertSame('page', fellowship_dashboard::mod_icon_class('page'));
        $this->assertSame('activity', fellowship_dashboard::mod_icon_class('unknownmod'));
    }

    public function test_initials(): void {
        $user = (object) ['firstname' => 'Aisha', 'lastname' => 'Khan'];
        $this->assertSame('AK', fellowship_dashboard::initials($user));
        $empty = (object) ['firstname' => '', 'lastname' => ''];
        $this->assertSame('?', fellowship_dashboard::initials($empty));
    }

    public function test_data_region_marker(): void {
        $this->assertSame('local-launchpad-fellowship', fellowship_dashboard::DATA_REGION);
    }

    public function test_home_summary_orders_recent_badges_newest_first(): void {
        $outline = [
            'percent' => 20,
            'completed' => 1,
            'trackable' => 5,
        ];
        $journey = [
            'gates' => [
                [
                    'name' => 'Foundations',
                    'short' => 'Foundations',
                    'is_current' => true,
                    'is_done' => false,
                ],
            ],
        ];
        $live = [
            'badges_earned' => 2,
            'badges' => [
                [
                    'id' => 'older',
                    'name' => 'Older badge',
                    'earned' => true,
                    'at' => 100,
                    'display_icon' => 'A',
                    'detail' => 'Earned earlier',
                    'css' => 'on',
                ],
                [
                    'id' => 'newer',
                    'name' => 'Newer badge',
                    'earned' => true,
                    'at' => 200,
                    'display_icon' => 'B',
                    'detail' => 'Earned later',
                    'css' => 'on',
                ],
                [
                    'id' => 'locked',
                    'name' => 'Locked',
                    'earned' => false,
                    'at' => 0,
                    'display_icon' => '🔒',
                    'detail' => 'How',
                    'css' => '',
                ],
            ],
        ];

        $summary = fellowship_dashboard::home_summary($outline, $journey, $live, 4);
        $this->assertTrue($summary['show_hero_ring']);
        $this->assertTrue($summary['has_percent']);
        $this->assertSame(20, $summary['ring_pct']);
        $this->assertSame('Foundations', $summary['ring_label']);
        $this->assertSame('2/3', $summary['stat_badges_value']);
        $this->assertSame('1/5', $summary['stat_required_value']);
        $this->assertSame('Foundations', $summary['stat_gate_value']);
        $this->assertTrue($summary['has_recent_badges']);
        $this->assertCount(2, $summary['recent_badges']);
        $this->assertSame('newer', $summary['recent_badges'][0]['id']);
        $this->assertSame('older', $summary['recent_badges'][1]['id']);
    }

    public function test_home_summary_shows_ring_when_completion_untracked(): void {
        $summary = fellowship_dashboard::home_summary(
            ['percent' => null, 'completed' => 0, 'trackable' => 0, 'sections' => []],
            ['gates' => [[
                'name' => 'Foundations',
                'short' => 'Foundations',
                'sectionnum' => 1,
                'is_current' => true,
                'is_done' => false,
            ]]],
            ['badges_earned' => 0, 'badges' => []],
        );
        $this->assertTrue($summary['show_hero_ring']);
        $this->assertFalse($summary['has_percent']);
        $this->assertSame(0, $summary['ring_pct']);
        $this->assertSame('Foundations', $summary['ring_label']);
        $this->assertFalse($summary['has_recent_badges']);
        $this->assertSame('—', $summary['stat_required_value']);
        $this->assertFalse($summary['has_foundations_activities']);
        $this->assertSame([], $summary['foundations_activities']);
    }

    public function test_section_activities_from_outline_picks_section(): void {
        $outline = [
            'sections' => [
                [
                    'sectionnum' => 0,
                    'activities' => [['name' => 'Orient']],
                ],
                [
                    'sectionnum' => 1,
                    'activities' => [
                        ['name' => 'Lesson A', 'modname' => 'page'],
                        ['name' => 'Quiz 1', 'modname' => 'quiz'],
                    ],
                ],
            ],
        ];
        $acts = fellowship_dashboard::section_activities_from_outline($outline, 1);
        $this->assertCount(2, $acts);
        $this->assertSame('Lesson A', $acts[0]['name']);
        $this->assertSame([], fellowship_dashboard::section_activities_from_outline($outline, 9));
    }

    public function test_list_portfolio_evidence_empty_without_courses(): void {
        $out = fellowship_dashboard::list_portfolio_evidence(1, []);
        $this->assertFalse($out['has_items']);
        $this->assertSame([], $out['items']);
    }
}
