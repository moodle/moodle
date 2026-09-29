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

namespace core;

use core_renderer;
use moodle_page;

/**
 * Tests for \core_renderer.
 *
 * @package   core
 * @category  test
 * @copyright 2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_renderer
 */
final class core_renderer_test extends \advanced_testcase {

    /**
     * Data provider for testing language crawling headers
     *
     * @return  array
     */
    public static function language_header_links_provider(): array {
        return [
            'Default' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                ],
                'langscrawlable' => '',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />

EOF
,
            ],
            'Single language french' => [
                'lang' => 'fr',
                'languages' => [
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => '',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />

EOF
,
            ],
            'Dual language en and fr, no crawling, no param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => '',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />

EOF
,
            ],
            'Dual language en and fr, no crawling, fr param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => '',
                'param' => 'fr',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />

EOF
,
            ],
            'Dual language en and fr, dual crawling, no param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => 'fr',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],
            'Dual language en and fr, dual crawling, fr param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => 'fr',
                'param' => 'fr',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php?lang=fr" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],
            'Triple language en, fr, de, dual crawling, no param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                    'de' => 'German (de)',
                ],
                'langscrawlable' => 'fr',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],
            'Triple language en, fr, de, dual crawling, fr param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                    'de' => 'German (de)',
                ],
                'langscrawlable' => 'fr',
                'param' => 'fr',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php?lang=fr" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],
            'Triple language en, fr, de, dual crawling, de param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                    'de' => 'German (de)',
                ],
                'langscrawlable' => 'fr',
                'param' => 'de',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],

            'Non standard language en, de_kids mapped to de, dual crawling, no param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'de_kids' => 'German for kids (de)',
                ],
                'langscrawlable' => 'de_kids|de',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="de" href="https://www.example.com/moodle/page.php?lang=de_kids" />

EOF
,
            ],

            'Non standard language en, de_kids mapped to de, dual crawling, de_kids param' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'de_kids' => 'German for kids (de)',
                ],
                'langscrawlable' => 'de_kids|de',
                'param' => 'de_kids',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php?lang=de_kids" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="de" href="https://www.example.com/moodle/page.php?lang=de_kids" />

EOF
,
            ],

            'Crawlable language not installed is silently ignored' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => 'fr,de',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],

            'Default language listed in langscrawlable does not produce duplicate hreflang' => [
                'lang' => 'en',
                'languages' => [
                    'en' => 'English (en)',
                    'fr' => 'French (fr)',
                ],
                'langscrawlable' => 'en,fr',
                'param' => '',
                'expected' => <<<EOF
<link rel="canonical" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="en" href="https://www.example.com/moodle/page.php" />
<link rel="alternate" hreflang="fr" href="https://www.example.com/moodle/page.php?lang=fr" />

EOF
,
            ],

        ];
    }

    /**
     * Tests the various SEO headers for language links
     *
     * @covers \core\output\core_renderer::language_header_links
     * @dataProvider language_header_links_provider
     * @param string $lang what is the default language
     * @param string[] $languages what are all the languages installed
     * @param string $langscrawlable what languages are ok to be crawled
     * @param string $param what is the optional param lang param
     * @param string $expected header links
     */
    public function test_language_header_links($lang, $languages, $langscrawlable, $param, $expected): void {
        global $CFG;
        $beforelang = $CFG->lang;
        $beforelangscrawlable = $CFG->langscrawlable ?? '';

        $CFG->lang = $lang;
        $CFG->langscrawlable = $langscrawlable;

        $page = new moodle_page();
        $page->set_url('/page.php');
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);
        $links = $renderer->language_header_links($languages, $param);
        $this->assertEquals($expected, $links);

        $CFG->lang = $beforelang;
        $CFG->langscrawlable = $beforelangscrawlable;
    }

    /**
     * @covers \core\hook\output\before_standard_top_of_body_html_generation
     */
    public function test_standard_top_of_body_html(): void {
        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_top_of_body_html();
        $this->assertIsString($html);
        $this->assertStringNotContainsString('A heading can be added to the top of the body HTML', $html);
    }

    /**
     * @covers \core\hook\output\before_standard_top_of_body_html_generation
     */
    public function test_before_standard_top_of_body_html_generation_hooked(): void {
        require_once(__DIR__ . '/fixtures/core_renderer/before_standard_top_of_body_html_generation_callbacks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => __DIR__ . '/fixtures/core_renderer/before_standard_top_of_body_html_generation_hooks.php',
            ]),
        );

        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_top_of_body_html();
        $this->assertIsString($html);
        $this->assertStringContainsString('A heading can be added to the top of the body HTML', $html);
    }

    /**
     * @covers \core\hook\output\before_footer_html_generation
     */
    public function test_before_footer_html_generation(): void {
        $this->resetAfterTest();
        $page = new moodle_page();
        $page->set_state(moodle_page::STATE_PRINTING_HEADER);
        $page->set_state(moodle_page::STATE_IN_BODY);
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $page->opencontainers->push('header/footer', '</body></html>');
        $html = $renderer->footer();
        $this->assertIsString($html);
        $this->assertStringNotContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\before_footer_html_generation
     */
    public function test_before_footer_html_generation_hooked(): void {
        $this->resetAfterTest();
        require_once(__DIR__ . '/fixtures/core_renderer/before_footer_html_generation_callbacks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => __DIR__ . '/fixtures/core_renderer/before_footer_html_generation_hooks.php',
            ]),
        );

        $page = new moodle_page();
        $page->set_state(moodle_page::STATE_PRINTING_HEADER);
        $page->set_state(moodle_page::STATE_IN_BODY);
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $page->opencontainers->push('header/footer', '</body></html>');
        $html = $renderer->footer();
        $this->assertIsString($html);
        $this->assertStringContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\before_standard_footer_html_generation
     */
    public function before_standard_footer_html_generation(): void {
        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_footer_html();
        $this->assertIsString($html);
        $this->assertStringNotContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\before_standard_footer_html_generation
     */
    public function test_before_standard_footer_html_generation_hooked(): void {
        require_once(__DIR__ . '/fixtures/core_renderer/before_standard_footer_html_generation_callbacks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => __DIR__ . '/fixtures/core_renderer/before_standard_footer_html_generation_hooks.php',
            ]),
        );

        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_footer_html();
        $this->assertIsString($html);
        $this->assertStringContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\after_standard_main_region_html_generation
     */
    public function test_after_standard_main_region_html_generation(): void {
        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_after_main_region_html();
        $this->assertIsString($html);
        $this->assertStringNotContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\after_standard_main_region_html_generation
     */
    public function test_after_standard_main_region_html_generation_hooked(): void {
        require_once(__DIR__ . '/fixtures/core_renderer/after_standard_main_region_html_generation_callbacks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => __DIR__ . '/fixtures/core_renderer/after_standard_main_region_html_generation_hooks.php',
            ]),
        );

        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_after_main_region_html();
        $this->assertIsString($html);
        $this->assertStringContainsString('A heading can be added', $html);
    }

    /**
     * @covers \core\hook\output\before_html_attributes
     */
    public function test_htmlattributes(): void {
        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $attributes = $renderer->htmlattributes();
        $this->assertIsString($attributes);
        $this->assertStringNotContainsString('data-test="test"', $attributes);
    }

    /**
     * @covers \core\hook\output\before_html_attributes
     */
    public function test_htmlattributes_hooked(): void {
        require_once(__DIR__ . '/fixtures/core_renderer/htmlattributes_callbacks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => __DIR__ . '/fixtures/core_renderer/htmlattributes_hooks.php',
            ]),
        );

        $page = new moodle_page();
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $attributes = $renderer->htmlattributes();
        $this->assertIsString($attributes);
        $this->assertStringContainsString('data-test="test"', $attributes);
    }

    /**
     * The Edit mode switch renders the design system Switch component with matching props.
     */
    public function test_edit_switch_renders_design_system_switch(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $page = new moodle_page();
        $page->set_url('/course/view.php', ['id' => 2]);
        $page->set_context(\context_system::instance());
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->edit_switch();

        $this->assertNotNull($html);
        $this->assertStringContainsString('data-react-component="@moodle/lms/core/EditModeSwitch"', $html);
        $this->assertStringContainsString('editmode-switch-form', $html);
        $this->assertStringContainsString(get_string('editmode'), $html);
        $this->assertStringContainsString(get_string('setmode', 'core'), $html);
        $this->assertStringContainsString('name="sesskey"', $html);
        $this->assertStringContainsString('name="pageurl"', $html);
        $this->assertStringContainsString('name="context"', $html);

        // Extract and decode the JSON props handed to the React component.
        $this->assertMatchesRegularExpression('/data-react-props=\'([^\']+)\'/', $html);
        preg_match('/data-react-props=\'([^\']+)\'/', $html, $matches);
        $props = json_decode($matches[1], true);
        $this->assertIsArray($props);
        $this->assertSame($page->context->id, $props['context']);
        $this->assertSame($page->url->out(false), $props['pageurl']);
        $this->assertArrayNotHasKey('sesskey', $props);
        $this->assertFalse($props['checked']);
        $this->assertSame(get_string('editmode'), $props['label']);

        // The same id must be shared between the props and the fallback checkbox/label, so
        // core/edit_switch.js and Behat's label-based field lookup keep working either way.
        $this->assertStringContainsString('id="' . $props['id'] . '"', $html);
        $this->assertStringContainsString('for="' . $props['id'] . '"', $html);

        // The accessibility fix itself: the fallback must use the design system's "enable" variant
        // and its thumb icon (both states rendered, toggled via CSS), not a bare unlabelled circle.
        $this->assertStringContainsString('mds-switch--variant-enable', $html);
        $this->assertStringContainsString('mds-switch-icon-item mds-switch-icon-item--unchecked', $html);
        $this->assertStringContainsString('mds-switch-icon-item mds-switch-icon-item--checked', $html);
    }

    /**
     * The switch's checked state and props reflect that the current user has editing on.
     */
    public function test_edit_switch_reflects_editing_on(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $USER->editing = 1;

        $page = new moodle_page();
        $page->set_url('/course/view.php', ['id' => 2]);
        $page->set_context(\context_system::instance());
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->edit_switch();

        $this->assertMatchesRegularExpression('/data-react-props=\'([^\']+)\'/', $html);
        preg_match('/data-react-props=\'([^\']+)\'/', $html, $matches);
        $props = json_decode($matches[1], true);
        $this->assertTrue($props['checked']);

        // The fallback markup always emits both icon spans (toggled via CSS opacity, not
        // conditional markup), so assert the checkbox's checked attribute specifically rather
        // than the literal substring 'checked', which is present regardless of switch state.
        $this->assertMatchesRegularExpression('/<input[^>]*\bchecked\b[^>]*class="mds-switch-input"/', $html);
    }

    /**
     * The switch is not rendered at all for a user without editing capability.
     */
    public function test_edit_switch_returns_null_when_user_cannot_edit(): void {
        $this->resetAfterTest();
        $this->setGuestUser();

        $page = new moodle_page();
        $page->set_url('/course/view.php', ['id' => 2]);
        $page->set_context(\context_system::instance());
        $renderer = new core_renderer($page, RENDERER_TARGET_GENERAL);

        $this->assertNull($renderer->edit_switch());
    }
}
