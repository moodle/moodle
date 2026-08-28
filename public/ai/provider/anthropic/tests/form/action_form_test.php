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

namespace aiprovider_anthropic\form;

use aiprovider_anthropic\aimodel\custommodel;
use aiprovider_anthropic\testcase_helper_trait;
use PHPUnit\Framework\Attributes\CoversClass;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../testcase_helper_trait.php');

/**
 * Tests for the Anthropic Claude provider action settings form.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\form\action_form::class)]
#[CoversClass(\aiprovider_anthropic\form\action_generate_text_form::class)]
final class action_form_test extends \advanced_testcase {
    use testcase_helper_trait;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test the model selector lists every bundled model plus the custom option last.
     */
    public function test_model_list(): void {
        $form = $this->build_form();
        $method = new \ReflectionMethod($form, 'get_model_list');
        $models = $method->invoke($form);

        $this->assertSame([
            'claude-haiku-4-5-20251001',
            'claude-opus-4-5-20251101',
            'claude-opus-4-8',
            'claude-opus-5',
            'claude-sonnet-4-5-20250929',
            'claude-sonnet-5',
            custommodel::MODEL_NAME,
        ], array_keys($models));
        $this->assertEquals(get_string('custom', 'core_form'), $models[custommodel::MODEL_NAME]);
    }

    /**
     * Test a bundled model comes back selected in the model selector.
     */
    public function test_bundled_model_selection(): void {
        $mform = $this->build_mform(['model' => 'claude-opus-5']);

        $this->assertEquals('claude-opus-5', self::get_value($mform, 'modeltemplate'));
        $this->assertEquals('claude-opus-5', self::get_value($mform, 'model'));
        $this->assertEmpty(self::get_value($mform, 'custommodel'));
    }

    /**
     * Test an unlisted stored model comes back as the custom option, with the name preserved.
     */
    public function test_custom_model_selection(): void {
        $mform = $this->build_mform(['model' => 'claude-not-bundled-yet']);

        $this->assertEquals(custommodel::MODEL_NAME, self::get_value($mform, 'modeltemplate'));
        $this->assertEquals('claude-not-bundled-yet', self::get_value($mform, 'model'));
        $this->assertEquals('claude-not-bundled-yet', self::get_value($mform, 'custommodel'));
    }

    /**
     * Test the custom model name is required when the custom option is selected.
     */
    public function test_validation_requires_custom_model_name(): void {
        $form = $this->build_form();

        $errors = $form->validation(['modeltemplate' => custommodel::MODEL_NAME, 'custommodel' => ''], []);
        $this->assertArrayHasKey('custommodel', $errors);

        $errors = $form->validation(['modeltemplate' => custommodel::MODEL_NAME, 'custommodel' => '  '], []);
        $this->assertArrayHasKey('custommodel', $errors);

        $errors = $form->validation(
            ['modeltemplate' => custommodel::MODEL_NAME, 'custommodel' => 'claude-not-bundled-yet'],
            [],
        );
        $this->assertArrayNotHasKey('custommodel', $errors);

        $errors = $form->validation(['modeltemplate' => 'claude-opus-5', 'custommodel' => ''], []);
        $this->assertArrayNotHasKey('custommodel', $errors);
    }

    /**
     * Test the selector helper fields are not stored as action settings.
     */
    public function test_get_defaults_excludes_helper_fields(): void {
        $defaults = $this->build_form()->get_defaults();

        $this->assertArrayNotHasKey('modeltemplate', $defaults);
        $this->assertArrayNotHasKey('custommodel', $defaults);
        $this->assertArrayHasKey('model', $defaults);
    }

    /**
     * Get an element's current value, flattening the array a select element returns.
     *
     * @param \MoodleQuickForm $mform The form to read from.
     * @param string $elementname The element to read.
     * @return string|null
     */
    private static function get_value(\MoodleQuickForm $mform, string $elementname): ?string {
        $value = $mform->getElementValue($elementname);

        return is_array($value) ? reset($value) : $value;
    }

    /**
     * Build a generate_text action settings form.
     *
     * @param array $settings Action settings to configure the provider with.
     * @return action_generate_text_form
     */
    private function build_form(array $settings = []): action_generate_text_form {
        $provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: $settings,
        );

        return new action_generate_text_form(customdata: [
            'actionconfig' => ['settings' => $provider->actionconfig[\core_ai\aiactions\generate_text::class]['settings']],
            'actionname' => 'generate_text',
            'action' => \core_ai\aiactions\generate_text::class,
            'providerid' => 1,
            'providername' => 'aiprovider_anthropic',
        ]);
    }

    /**
     * Build a generate_text action settings form and return the underlying MoodleQuickForm.
     *
     * @param array $settings Action settings to configure the provider with.
     * @return \MoodleQuickForm
     */
    private function build_mform(array $settings = []): \MoodleQuickForm {
        $form = $this->build_form($settings);
        // Mirrors moodleform::display(), which finalizes the definition on first render.
        $form->definition_after_data();
        $property = new \ReflectionProperty($form, '_form');

        return $property->getValue($form);
    }
}
