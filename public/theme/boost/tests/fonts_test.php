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
 * Unit tests for the webfonts bundled with the theme.
 *
 * These tests assert the content of the theme's compiled CSS rather than the behaviour of any
 * one class, so there is no meaningful coverage target.
 *
 * @package   theme_boost
 * @copyright 2026 Moodle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversNothing]
final class fonts_test extends \advanced_testcase {
    /**
     * Noto Sans must be reachable from the default font stack.
     *
     * Shipping the @font-face declarations is not enough on its own: unless the family is
     * also named in the font stack, nothing can ever select it and the bundled files are
     * dead weight. The stack is asserted via the --bs-font-sans-serif custom property
     * rather than a body rule, as that is where Bootstrap exposes it regardless of how the
     * theme chooses to apply it.
     *
     * Noto Sans JP must NOT be reachable from this same stack, or it hijacks glyphs it
     * covers for every other language, not just Japanese.
     *
     * The stack must also be flat, as a stack that names the right families is still dead
     * weight if the declaration itself is malformed.
     */
    public function test_font_stack_includes_noto_sans(): void {
        $this->resetAfterTest();

        $css = theme_config::load('boost')->editor_scss_to_css();

        $this->assertSame(
            1,
            preg_match('/--bs-font-sans-serif:\s*([^;]+);/', $css, $matches),
            'The compiled editor CSS does not define a sans-serif font stack.'
        );

        $this->assertStringNotContainsString(
            '(',
            $matches[1],
            'The font stack must be a flat list. A nested SCSS list is emitted with its parentheses '
                . 'intact by inspect(), which makes the declaration invalid and drops the whole stack.'
        );

        $stack = array_map(
            static fn(string $family): string => trim(trim($family), '"\''),
            explode(',', $matches[1])
        );

        $this->assertContains('Noto Sans', $stack, 'The font stack is missing Noto Sans.');
        $this->assertNotContains('Noto Sans JP', $stack, 'Noto Sans JP must not be in the default font stack.');
    }

    /**
     * Noto Sans JP must be reachable, via :lang(ja) rather than --bs-font-sans-serif.
     */
    public function test_font_stack_includes_noto_sans_jp(): void {
        $this->resetAfterTest();

        $css = theme_config::load('boost')->editor_scss_to_css();

        $this->assertSame(
            1,
            preg_match('/:lang\(ja\)[^{]*\{\s*font-family:\s*([^;]+);/', $css, $matches),
            'The compiled editor CSS does not define a :lang(ja) font-family rule.'
        );

        $this->assertStringNotContainsString(
            '(',
            $matches[1],
            'The :lang(ja) font stack must be a flat list.'
        );

        $stack = array_map(
            static fn(string $family): string => trim(trim($family), '"\''),
            explode(',', $matches[1])
        );

        $this->assertContains('Noto Sans JP', $stack, 'The :lang(ja) rule is missing Noto Sans JP.');
    }

    /**
     * The :lang(ja) rule must not apply to icon elements.
     */
    public function test_lang_ja_excludes_icon_elements(): void {
        $this->resetAfterTest();

        $css = theme_config::load('boost')->editor_scss_to_css();

        $this->assertSame(
            1,
            preg_match('/:lang\(ja\):not\(([^)]+)\)\s*\{/', $css, $matches),
            'The compiled editor CSS does not define an exclusion-guarded :lang(ja) rule.'
        );

        $exclusions = array_map('trim', explode(',', $matches[1]));

        $this->assertContains('.fa', $exclusions, 'Must exclude the .fa class.');
        $this->assertContains('.fas', $exclusions, 'Must exclude .fas.');
        $this->assertContains('.far', $exclusions, 'Must exclude .far.');
        $this->assertContains('.fab', $exclusions, 'Must exclude .fab.');
        $this->assertContains(
            '[class*=fa-]',
            array_map(static fn(string $e): string => str_replace('"', '', $e), $exclusions),
            'Must exclude any fa-* class (fa-solid, fa-regular, fa-brands, fa-classic, and icon-name classes).'
        );
    }
}
