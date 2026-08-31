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

namespace aiprovider_anthropic;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Tests for the Anthropic Claude provider hook listener.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\hook_listener::class)]
final class hook_listener_test extends \advanced_testcase {
    use testcase_helper_trait;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test that building the generate_text action settings form wires the model settings
     * via after_ai_action_settings_form_hook, instead of leaving the shared
     * add_model_settings()/get_model_settings() implementation unused.
     */
    public function test_action_settings_form_adds_model_settings_via_hook(): void {
        $mform = $this->build_generate_text_form();

        $this->assertTrue($mform->elementExists('max_tokens'));
        $this->assertTrue($mform->elementExists('temperature'));
    }

    /**
     * Test the temperature field is only offered for models that accept it.
     *
     * @param string $model The configured model name.
     * @param bool $expectstemperature Whether the temperature field should be present.
     */
    #[DataProvider('temperature_field_provider')]
    public function test_temperature_field_visibility(string $model, bool $expectstemperature): void {
        $mform = $this->build_generate_text_form(['model' => $model]);

        $this->assertTrue($mform->elementExists('max_tokens'));
        $this->assertSame($expectstemperature, $mform->elementExists('temperature'));
    }

    /**
     * Data provider for test_temperature_field_visibility.
     *
     * @return array
     */
    public static function temperature_field_provider(): array {
        return [
            'Claude Haiku 4.5' => ['claude-haiku-4-5-20251001', true],
            'Claude Sonnet 4.5' => ['claude-sonnet-4-5-20250929', true],
            'Claude Opus 4.5' => ['claude-opus-4-5-20251101', true],
            'Claude Opus 4.8' => ['claude-opus-4-8', false],
            'Claude Opus 5' => ['claude-opus-5', false],
            'Claude Sonnet 5' => ['claude-sonnet-5', false],
            // An unlisted model entered by an admin is unknown to this plugin, so both
            // settings are offered and the admin decides.
            'Unlisted custom model' => ['claude-not-bundled-yet', true],
        ];
    }

    /**
     * Build a rendered generate_text action settings form.
     *
     * @param array $settings Action settings to configure the provider with.
     * @return \MoodleQuickForm
     */
    private function build_generate_text_form(array $settings = []): \MoodleQuickForm {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: $settings,
        );

        $customdata = [
            'actionconfig' => ['settings' => $provider->actionconfig[\core_ai\aiactions\generate_text::class]['settings']],
            'actionname' => 'generate_text',
            'action' => \core_ai\aiactions\generate_text::class,
            'providerid' => 1,
            'providername' => 'aiprovider_anthropic',
        ];

        $form = new form\action_generate_text_form(customdata: $customdata);
        // Mirrors moodleform::display(), which finalizes the definition (and dispatches
        // after_ai_action_settings_form_hook) on first render.
        $form->definition_after_data();
        $property = new \ReflectionProperty($form, '_form');

        return $property->getValue($form);
    }
}
