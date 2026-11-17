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

namespace core\output\requirements;

/**
 * Tests for the page_requirements_manager ESM import map output.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(page_requirements_manager::class)]
final class page_requirements_manager_test extends \advanced_testcase {
    /**
     * Extract the modulepreload hrefs and the importmap script's decoded imports from a
     * get_import_map() output string.
     *
     * @param string $output
     * @return array{preloads: string[], imports: array<string, string>}
     */
    protected function parse_import_map_output(string $output): array {
        preg_match_all('#<link rel="modulepreload" href="([^"]*)"\s*/?>#', $output, $linkmatches);

        preg_match('#<script type="importmap">(.*?)</script>#s', $output, $scriptmatches);
        $this->assertNotEmpty($scriptmatches, 'Expected an importmap script tag in the output.');
        $importmapdata = json_decode($scriptmatches[1], true);
        $this->assertIsArray($importmapdata);

        return [
            'preloads' => $linkmatches[1],
            'imports' => $importmapdata['imports'],
        ];
    }

    /**
     * get_import_map() emits a modulepreload link, after the importmap script tag, for exactly
     * the entries import_map::get_preload_specifiers() opts in - a closed allowlist of core,
     * site-wide, single-file dependencies (react, react-dom, tabbable, bootstrap, and the active
     * theme's design-system bundle) - so the browser can start fetching those foundational ESM
     * dependencies without waiting to discover them via import-graph parsing.
     */
    public function test_get_import_map_preloads_exactly_the_allowlisted_entries(): void {
        global $PAGE;

        $this->resetAfterTest();

        $output = $PAGE->requires->get_import_map($PAGE);
        ['preloads' => $preloads, 'imports' => $imports] = $this->parse_import_map_output($output);

        $importmap = \core\di::get(import_map::class);
        $expected = [];
        foreach ($importmap->get_preload_specifiers() as $specifier) {
            $this->assertArrayHasKey($specifier, $imports, "Preload specifier '{$specifier}' missing from importmap output.");
            $expected[] = $imports[$specifier];
        }

        $this->assertNotEmpty($expected, 'Expected at least one allowlisted entry to preload.');
        sort($expected);
        $actual = $preloads;
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * A plugin-registered import map entry added via the before_import_map_config hook must
     * never be preloaded, even though it is a concrete, non-prefix specifier that the old
     * shape-based filter (excluding only trailing-slash prefixes and '/theme-' variants) would
     * have preloaded. Only entries core explicitly opts in via import_map::add_import(preload:
     * true) may be preloaded; the hook's add_import() wrapper does not expose that parameter.
     */
    public function test_plugin_registered_import_is_not_preloaded(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->redirectHook(
            \core\hook\output\before_import_map_config::class,
            function (\core\hook\output\before_import_map_config $hook): void {
                $hook->add_import('theme-switcher');
            },
        );

        $output = $PAGE->requires->get_import_map($PAGE);
        ['preloads' => $preloads, 'imports' => $imports] = $this->parse_import_map_output($output);

        $this->assertArrayHasKey('theme-switcher', $imports, 'Expected the plugin import to still be registered.');
        $this->assertNotContains(
            $imports['theme-switcher'],
            $preloads,
            'A plugin-registered import must not be auto-preloaded.',
        );
    }

    /**
     * The importmap script tag must be emitted before any modulepreload link. Browsers only
     * honour an import map for bare specifier resolution ("react", "@moodlehq/design-system",
     * ...) if it is registered before the first module-related resource - including a
     * modulepreload link - is processed. Emitting a preload link first silently breaks specifier
     * resolution for every module script on the page.
     */
    public function test_importmap_script_precedes_modulepreload_links(): void {
        global $PAGE;

        $this->resetAfterTest();

        $output = $PAGE->requires->get_import_map($PAGE);

        $scriptpos = strpos($output, '<script type="importmap">');
        $linkpos = strpos($output, '<link rel="modulepreload"');
        $this->assertNotFalse($scriptpos, 'Expected the importmap script tag in the output.');
        $this->assertNotFalse($linkpos, 'Expected at least one modulepreload link in the output.');
        $this->assertLessThan($linkpos, $scriptpos, 'The importmap script tag must precede modulepreload links.');
    }

    /**
     * When editing is on, the block drag and drop module is given the URL of the page, so that it can load the
     * page again without repeating the request that produced it.
     */
    public function test_block_drag_and_drop_is_given_the_page_url(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $USER->editing = 1;

        $page = new \moodle_page();
        $page->set_context(\core\context\system::instance());
        $page->set_pagelayout('standard');
        $page->set_url('/my/index.php', ['example' => 1, 'other' => 2]);
        $page->requires->get_head_code($page, $page->get_renderer('core'));

        $this->assertStringContainsString(
            '"pageurl":' . json_encode($page->url->out(false)),
            $page->requires->get_end_code(),
        );
    }
}
