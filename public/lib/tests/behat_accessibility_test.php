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

use behat_accessibility;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/tests/behat/behat_accessibility.php');

/**
 * Tests for the accessibility behat step definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(behat_accessibility::class)]
final class behat_accessibility_test extends \basic_testcase {
    /**
     * Call the protected get_axe_config_for_tags() method and return the decoded configuration.
     *
     * @param array|null $standardtags The list of standard tags to run.
     * @param array|null $extratags The list of tags, in addition to the standard tags, to run.
     * @return stdClass The decoded Axe configuration.
     */
    protected function get_axe_config(?array $standardtags = [], ?array $extratags = []): stdClass {
        $context = new behat_accessibility();
        $method = new ReflectionMethod($context, 'get_axe_config_for_tags');
        $config = json_decode($method->invoke($context, $standardtags, $extratags));

        $this->assertInstanceOf(stdClass::class, $config, 'The Axe configuration must be a JSON object.');

        return $config;
    }

    /**
     * The configuration must always tell Axe that the supplied values are tags.
     *
     * Without a valid runOnly type Axe cannot reliably map the values onto its rule set, which would silently
     * change which rules every accessibility scenario is checked against.
     */
    public function test_run_only_type_is_tag(): void {
        $config = $this->get_axe_config();

        $this->assertObjectHasProperty('runOnly', $config);
        $this->assertObjectHasProperty('type', $config->runOnly);
        $this->assertSame('tag', $config->runOnly->type);
    }

    /**
     * Data provider listing the tags which must always be part of the default rule set.
     *
     * @return array
     */
    public static function default_tag_provider(): array {
        return [
            // Meet WCAG 2.2 Level A success criteria.
            'WCAG 2.0 Level A' => ['wcag2a'],
            'WCAG 2.1 Level A' => ['wcag21a'],
            'WCAG 2.2 Level A' => ['wcag22a'],

            // Meet WCAG 2.2 Level AA success criteria.
            'WCAG 2.0 Level AA' => ['wcag2aa'],
            'WCAG 2.1 Level AA' => ['wcag21aa'],
            'WCAG 2.2 Level AA' => ['wcag22aa'],

            // Meet Section 508 requirements.
            'Section 508' => ['section508'],

            // Ensure that ARIA attributes are correctly defined.
            'ARIA' => ['cat.aria'],

            // Requirements for sensory and visual cues.
            'Sensory and visual cues' => ['cat.sensory-and-visual-cues'],

            // Meet WCAG 1.3.4 requirements for orientation.
            'Orientation' => ['wcag134'],
        ];
    }

    /**
     * The default rule set must cover the standard Moodle accessibility conformance target.
     *
     * @param string $tag The tag which must be present in the default configuration.
     */
    #[DataProvider('default_tag_provider')]
    public function test_default_tags(string $tag): void {
        $config = $this->get_axe_config();

        $this->assertContains($tag, $config->runOnly->values);
    }

    /**
     * Extra tags must be run in addition to the default tags, and not instead of them.
     */
    public function test_extra_tags_are_added_to_the_defaults(): void {
        $defaults = $this->get_axe_config()->runOnly->values;
        $config = $this->get_axe_config(null, ['best-practice']);

        $this->assertSame('tag', $config->runOnly->type);
        $this->assertSame([...$defaults, 'best-practice'], $config->runOnly->values);
    }

    /**
     * Standard tags, when given, must replace the default tags entirely.
     */
    public function test_standard_tags_replace_the_defaults(): void {
        $config = $this->get_axe_config(['wcag2a'], ['best-practice']);

        $this->assertSame('tag', $config->runOnly->type);
        $this->assertSame(['wcag2a', 'best-practice'], $config->runOnly->values);
    }
}
