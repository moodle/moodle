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

namespace theme_boost;

use core\output\theme_config;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Unit tests for the contrast of the focus indicators in the dark colour mode.
 *
 * These tests assert the content of the theme's compiled CSS rather than the behaviour of any
 * one class, so there is no meaningful coverage target.
 *
 * @package   theme_boost
 * @copyright 2026 Moodle Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversNothing]
final class dark_focus_contrast_test extends \advanced_testcase {
    /** @var string[]|null The custom properties of the dark palette, compiled once for the whole class. */
    private static ?array $darkpalette = null;

    /** @var string|null The compiled CSS of Boost, compiled once for the whole class. */
    private static ?string $css = null;

    /** @var string|null The compiled CSS of Boost with a custom preset, compiled once for the whole class. */
    private static ?string $customcss = null;

    /** @var string[] The dark surfaces a focus indicator is drawn on, and the property each is read from. */
    private const SURFACES = [
        'page' => '--bs-gray-100',
        'drawer' => '--bs-gray-200',
        'card' => '--bs-white',
    ];

    /**
     * The design system focus colour reaches 3:1 on every dark surface.
     *
     * The token is an alias of the primary interactive border in the design system, but the SCSS token build resolves
     * it to a fixed colour, so re-pointing the border for dark mode left every focus outline drawn from this token in
     * its light mode colour. The dark palette points it back at the border, so the two cannot drift apart.
     */
    public function test_design_system_focus_colour(): void {
        $palette = $this->get_dark_palette();

        $this->assertArrayHasKey('--mds-focus-default', $palette, 'The dark palette does not re-point the focus colour.');
        $this->assertSame(
            'var(--mds-border-interactive-primary-default)',
            $palette['--mds-focus-default'],
            'The focus colour no longer refers to the primary interactive border it is an alias of.'
        );

        $focus = $this->parse_colour($palette['--mds-border-interactive-primary-default']);
        foreach (self::SURFACES as $surface => $property) {
            $background = $this->parse_colour($palette[$property]);
            $this->assertGreaterThanOrEqual(3, $this->contrast($focus, $background), "Focus colour on the {$surface}.");
        }
    }

    /**
     * The Bootstrap focus ring reaches 3:1 on every dark surface once its opacity is applied.
     */
    public function test_bootstrap_focus_ring(): void {
        $palette = $this->get_dark_palette();

        $ring = $this->parse_colour($palette['--bs-focus-ring-color']);
        $this->assertLessThan(1, $ring[3], 'The focus ring is expected to be translucent.');
        foreach (self::SURFACES as $surface => $property) {
            $background = $this->parse_colour($palette[$property]);
            $this->assertGreaterThanOrEqual(
                3,
                $this->contrast($this->blend($ring, $background), $background),
                "Focus ring on the {$surface}."
            );
        }
    }

    /**
     * Focus rings compiled from Bootstrap's focus variables follow the colour mode.
     *
     * These rings were compiled to the light mode colour, which the dark palette cannot reach, so the course index and
     * the form controls of a page such as the course settings kept a ring well below 3:1 in dark mode. Drawing them from
     * the custom property gives them the ring measured in test_bootstrap_focus_ring().
     *
     * @param string $selector The selector of the focus rule, with attribute values unquoted.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('focus_ring_rules_provider')]
    public function test_focus_rings_follow_colour_mode(string $selector): void {
        $this->resetAfterTest();
        $this->assert_focus_ring_follows_colour_mode($selector, $this->get_css());
    }

    /**
     * A preset which does not configure the focus rings itself gets the same rings.
     *
     * The focus variables are set next to the Bootstrap import, which every preset goes through, rather than in the
     * default preset, or a site using another preset would keep the light mode rings in dark mode. The custom preset
     * used here is the default one without any focus ring variable of its own.
     *
     * @param string $selector The selector of the focus rule, with attribute values unquoted.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('focus_ring_rules_provider')]
    public function test_focus_rings_follow_colour_mode_in_custom_preset(string $selector): void {
        global $CFG;

        $this->resetAfterTest();
        if (self::$customcss === null) {
            $preset = preg_replace(
                '/^\$(focus-ring-(width|blur|box-shadow)|input-btn-focus-color):.*$/m',
                '',
                file_get_contents("{$CFG->dirroot}/theme/boost/scss/preset/default.scss")
            );
            get_file_storage()->create_file_from_string([
                'contextid' => \core\context\system::instance()->id,
                'component' => 'theme_boost',
                'filearea' => 'preset',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'custom.scss',
            ], $preset);
            set_config('preset', 'custom.scss', 'theme_boost');
            self::$customcss = theme_config::load('boost')->get_css_content_debug('scss', null, null);
        }
        $this->assert_focus_ring_follows_colour_mode($selector, self::$customcss);
    }

    /**
     * Assert that every focus ring of a rule is drawn from the focus ring custom property.
     *
     * @param string $selector The selector of the focus rule, with attribute values unquoted.
     * @param string $css The compiled CSS to look in.
     */
    private function assert_focus_ring_follows_colour_mode(string $selector, string $css): void {
        $shadows = [];
        preg_match_all('/([^{}]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER);
        foreach ($rules as [, $selectors, $declarations]) {
            $selectors = array_map(
                static fn(string $item): string => preg_replace('/\s+/', ' ', str_replace(['"', "'"], '', trim($item))),
                explode(',', $selectors)
            );
            if (in_array($selector, $selectors) && preg_match('/box-shadow\s*:\s*([^;]+)/', $declarations, $matches)) {
                $shadows[] = trim($matches[1]);
            }
        }

        $this->assertNotEmpty($shadows, "No focus ring found for {$selector}.");
        foreach ($shadows as $shadow) {
            $this->assertStringContainsString('var(--bs-focus-ring-color)', $shadow, "Focus ring of {$selector}.");
        }
    }

    /**
     * The focus rules of the course index and of the form controls.
     *
     * @return array
     */
    public static function focus_ring_rules_provider(): array {
        return [
            'Collapsed course index item' => ['[role=treeitem]:not([aria-expanded=true]):focus'],
            'Expanded course index item' => ['[role=treeitem][aria-expanded=true]:focus > *:first-child'],
            'Text field' => ['.form-control:focus'],
            'Select' => ['.form-select:focus'],
            'Checkbox' => ['.form-check-input:focus'],
        ];
    }

    /**
     * A colour which brings its own opacity is measured at that opacity when no other is given.
     *
     * The probe is compiled through Boost's raw SCSS setting, so that it runs against the real helper.
     */
    public function test_dark_readable_measures_own_alpha(): void {
        $this->resetAfterTest();
        set_config('scss', '.dark-readable-probe { --probe: #{dark-readable(rgba(#0f6cbf, .5))}; }', 'theme_boost');
        $css = theme_config::load('boost')->get_css_content_debug('scss', null, null);

        $this->assertSame(1, preg_match('/--probe\s*:\s*([^;}]+)/', $css, $matches), 'The probe was not compiled.');
        $probe = $this->parse_colour(strtolower(trim($matches[1])));
        $raised = $this->parse_colour($this->get_dark_palette()['--bs-white']);
        // The helper aims for 4.6, so that a colour which lands on the 4.5 threshold is not rounded below it.
        $this->assertGreaterThanOrEqual(4.5, $this->contrast($this->blend($probe, $raised), $raised));
    }

    /**
     * Compile Boost once for the whole class.
     *
     * @return string The compiled CSS.
     */
    private function get_css(): string {
        if (self::$css === null) {
            self::$css = theme_config::load('boost')->get_css_content_debug('scss', null, null);
        }
        return self::$css;
    }

    /**
     * Compile Boost and return the custom properties of the root dark palette.
     *
     * @return string[] The values, keyed by property name.
     */
    private function get_dark_palette(): array {
        if (self::$darkpalette === null) {
            $this->resetAfterTest();
            $css = $this->get_css();

            // The root palette is the dark block which re-points the primary interactive border.
            $matched = preg_match(
                '/\[data-bs-theme=["\']?dark["\']?\]\s*\{([^{}]*--mds-border-interactive-primary-default:[^{}]*)\}/',
                $css,
                $matches
            );
            $this->assertSame(1, $matched, 'The compiled CSS has no dark palette.');

            preg_match_all('/(--[\w-]+)\s*:\s*([^;]+)/', $matches[1], $declarations, PREG_SET_ORDER);
            self::$darkpalette = [];
            foreach ($declarations as [, $name, $value]) {
                self::$darkpalette[$name] = strtolower(trim($value));
            }
        }
        return self::$darkpalette;
    }

    /**
     * Parse a hex, rgb() or rgba() colour.
     *
     * @param string $colour The CSS colour.
     * @return float[] Red, green and blue from 0 to 255, then the alpha from 0 to 1.
     */
    private function parse_colour(string $colour): array {
        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/', $colour, $matches)) {
            $hex = strlen($matches[1]) === 3 ? preg_replace('/(.)/', '$1$1', $matches[1]) : $matches[1];
            return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)), 1.0];
        }
        if (preg_match('/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)\s*(?:,\s*([\d.]+)\s*)?\)$/', $colour, $matches)) {
            return [(float) $matches[1], (float) $matches[2], (float) $matches[3], (float) ($matches[4] ?? 1)];
        }
        $this->fail("Unexpected colour format: {$colour}");
    }

    /**
     * Blend a translucent colour into an opaque background.
     *
     * @param float[] $colour The colour, as returned by parse_colour().
     * @param float[] $background The background, as returned by parse_colour().
     * @return float[] The opaque result.
     */
    private function blend(array $colour, array $background): array {
        $alpha = $colour[3];
        return [
            $colour[0] * $alpha + $background[0] * (1 - $alpha),
            $colour[1] * $alpha + $background[1] * (1 - $alpha),
            $colour[2] * $alpha + $background[2] * (1 - $alpha),
            1.0,
        ];
    }

    /**
     * The WCAG contrast ratio of two opaque colours.
     *
     * @param float[] $first A colour, as returned by parse_colour().
     * @param float[] $second A colour, as returned by parse_colour().
     * @return float
     */
    private function contrast(array $first, array $second): float {
        $luminance = static function (array $colour): float {
            $channels = array_map(static function (float $channel): float {
                $channel /= 255;
                return $channel <= 0.04045 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
            }, array_slice($colour, 0, 3));
            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };
        $lighter = max($luminance($first), $luminance($second));
        $darker = min($luminance($first), $luminance($second));
        return ($lighter + 0.05) / ($darker + 0.05);
    }
}
