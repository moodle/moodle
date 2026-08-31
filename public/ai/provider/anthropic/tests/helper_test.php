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

use aiprovider_anthropic\aimodel\claude_base;
use aiprovider_anthropic\aimodel\claudeopus5;
use aiprovider_anthropic\aimodel\claudesonnet45;
use aiprovider_anthropic\aimodel\custommodel;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for the Anthropic Claude provider helper.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\helper::class)]
#[CoversClass(\aiprovider_anthropic\aimodel\custommodel::class)]
final class helper_test extends \advanced_testcase {
    /**
     * Test every discovered model class implements the Claude model contract.
     */
    public function test_get_model_classes(): void {
        $classes = helper::get_model_classes();

        $this->assertNotEmpty($classes);
        foreach ($classes as $class) {
            $this->assertInstanceOf(claude_base::class, new $class());
            $this->assertInstanceOf(\core_ai\aimodel\base::class, new $class());
        }
        // The interface and the shared trait must not be offered as models.
        $this->assertNotContains(claude_base::class, $classes);
    }

    /**
     * Test get_model_class resolves a bundled model name and returns null otherwise.
     */
    public function test_get_model_class(): void {
        $this->assertInstanceOf(claudeopus5::class, helper::get_model_class('claude-opus-5'));
        $this->assertNull(helper::get_model_class('claude-not-bundled-yet'));
    }

    /**
     * Test resolve_model falls back to the custom model for an unlisted model name.
     */
    public function test_resolve_model(): void {
        $this->assertInstanceOf(claudesonnet45::class, helper::resolve_model('claude-sonnet-4-5-20250929'));
        $this->assertInstanceOf(custommodel::class, helper::resolve_model('claude-not-bundled-yet'));
        $this->assertInstanceOf(custommodel::class, helper::resolve_model(''));
    }

    /**
     * Test the default model is one of the bundled models.
     */
    public function test_get_default_model(): void {
        $this->assertNotNull(helper::get_model_class(helper::get_default_model()));
    }

    /**
     * Test which models offer the temperature setting.
     *
     * Anthropic dropped temperature support from Claude Opus 4.7 onwards, and extended that
     * to Sonnet-class models from Claude Sonnet 5.
     */
    public function test_model_temperature_support(): void {
        $expected = [
            'claude-haiku-4-5-20251001' => true,
            'claude-sonnet-4-5-20250929' => true,
            'claude-opus-4-5-20251101' => true,
            'claude-opus-4-8' => false,
            'claude-opus-5' => false,
            'claude-sonnet-5' => false,
            // An admin-entered model is unknown to this plugin, so the field is offered.
            'custom' => true,
        ];

        foreach ($expected as $modelname => $supportstemperature) {
            $model = helper::get_model_class($modelname);
            $this->assertNotNull($model, "Model {$modelname} was not found");
            $this->assertSame($supportstemperature, $model->supports_temperature(), "Model {$modelname}");
            $this->assertSame(
                $supportstemperature,
                array_key_exists('temperature', $model->get_model_settings()),
                "Model {$modelname}",
            );
            // Max tokens is required by the Anthropic API, so it is always configurable.
            $this->assertArrayHasKey('max_tokens', $model->get_model_settings());
        }
    }
}
