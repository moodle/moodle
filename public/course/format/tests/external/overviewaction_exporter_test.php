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

namespace core_courseformat\external;

/**
 * Tests for overviewaction_exporter.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewaction_exporter::class)]
final class overviewaction_exporter_test extends \advanced_testcase {
    /**
     * Test the export returns the right structure when the content is a string.
     *
     * @param string|null $badgevalue The value of the badge.
     * @param string|null $badgetitle The title of the badge.
     * @param \core\output\local\properties\badge|null $badgestyle The style of the badge.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_export')]
    public function test_export(
        ?string $badgevalue = null,
        ?string $badgetitle = null,
        ?\core\output\local\properties\badge $badgestyle = null,
    ): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $url = new \core\url('/some/url');
        $text = 'My information';
        $attributes = ['class' => 'me-0 pb-1'];
        $overviewaction = new \core_courseformat\output\local\overview\overviewaction(
            url: $url,
            text: $text,
            attributes: $attributes,
            badgevalue: $badgevalue,
            badgetitle: $badgetitle,
            badgestyle: $badgestyle,
        );

        $exporter = new overviewaction_exporter($overviewaction, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('linkurl', $data);
        $this->assertObjectHasProperty('content', $data);
        $this->assertObjectHasProperty('classes', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertObjectHasProperty('badge', $data);
        $this->assertObjectHasProperty('onlytext', $data);
        $this->assertCount(8, get_object_vars($data));

        $this->assertEquals($url->out(false), $data->linkurl);
        $this->assertStringContainsString($text, $data->content);
        if ($badgevalue !== null && $badgetitle !== null) {
            $this->assertStringContainsString($badgevalue, $data->content);
        }
        $this->assertEquals($attributes['class'], $data->classes);
        $this->assertEquals('string', $data->contenttype);
        $this->assertNull($data->contentjson);
        $this->assertEquals($text, $data->onlytext);
        if ($badgevalue !== null) {
            $this->assertEquals($badgevalue, $data->badge['value']);
            $this->assertEquals($badgetitle, $data->badge['title']);
            if ($badgestyle == null) {
                // If no style is provided, overviewaction defaults to PRIMARY.
                $badgestyle = \core\output\local\properties\badge::PRIMARY;
            }
            $this->assertEquals($badgestyle, $data->badge['style']);
        } else {
            $this->assertNull($data->badge);
        }
    }

    /**
     * Provider for test_export.
     *
     * @return \Generator
     */
    public static function provider_test_export(): \Generator {
        yield 'All badge fields' => [
            'badgevalue' => '5',
            'badgetitle' => 'New items',
            'badgestyle' => \core\output\local\properties\badge::SUCCESS,
        ];
        yield 'No badge' => [];
        yield 'Badge without value (equivalent to no badge)' => [
            'badgetitle' => 'New items',
            'badgestyle' => \core\output\local\properties\badge::SUCCESS,
        ];
        yield 'Badge without title' => [
            'badgevalue' => '5',
            'badgestyle' => \core\output\local\properties\badge::SUCCESS,
        ];
        yield 'Badge without style (defaults to PRIMARY)' => [
            'badgevalue' => '5',
            'badgetitle' => 'New items',
        ];
    }
}
