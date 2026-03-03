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

namespace aiprovider_awsbedrock\aimodel;

use aiprovider_awsbedrock\model_definition;

/**
 * Amazon model catalog.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class amazon extends awsbedrock_base {
    /**
     * Get Amazon models.
     *
     * @return array<string, \aiprovider_awsbedrock\model_definition>
     */
    public static function get_models(): array {
        $textsettings = self::get_text_settings();
        $image = self::get_image_settings();

        return [
            'amazon.nova-pro-v1:0' => self::create_model(
                'amazon.nova-pro-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $textsettings,
            ),
            'amazon.nova-lite-v1:0' => self::create_model(
                'amazon.nova-lite-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $textsettings,
            ),
            'amazon.nova-micro-v1:0' => self::create_model(
                'amazon.nova-micro-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $textsettings,
            ),
            'amazon.nova-canvas-v1:0' => self::create_model(
                'amazon.nova-canvas-v1:0',
                model_definition::MODEL_TYPE_IMAGE,
                $image
            ),
            'amazon.titan-image-generator-v2:0' => self::create_model(
                'amazon.titan-image-generator-v2:0',
                model_definition::MODEL_TYPE_IMAGE,
                self::get_image_settings(8.0, 42),
            ),
        ];
    }

    /**
     * Amazon Nova text settings.
     *
     * @return array
     */
    private static function get_text_settings(): array {
        return [
            // Temperature – Use a lower value to decrease randomness in responses.
            'temperature' => self::setting(
                'settings_temperature',
                PARAM_FLOAT,
                'settings_temperature',
                ['min' => 0, 'max' => 1, 'default' => 0.7],
            ),
            // Top_p – Use a lower value to ignore less probable options and decrease the diversity of responses.
            'topP' => self::setting(
                'settings_top_p',
                PARAM_FLOAT,
                'settings_top_p',
                ['min' => 0, 'max' => 1, 'default' => 0.9],
            ),
            // Top_k – Use to remove long tail low probability responses.
            'topK' => self::setting(
                'settings_top_k',
                PARAM_FLOAT,
                'settings_top_k',
                ['min' => 0, 'max' => 128, 'default' => get_string('none', 'aiprovider_awsbedrock')],
            ),
            // Max token - The maximum number of tokens to generate in the response. Maximum token limits are strictly enforced.
            'maxTokens' => self::setting(
                'settings_max_tokens',
                PARAM_INT,
                'settings_max_tokens',
                ['min' => 0, 'max' => 5000, 'default' => get_string('none', 'aiprovider_awsbedrock')],
            ),
            // Schema version to use for the request.
            'schemaVersion' => self::setting(
                'settings_schema_version',
                PARAM_TEXT,
                'settings_schema_version',
                ['default' => 'messages-v1'],
            ),
        ];
    }

    /**
     * Amazon image settings.
     *
     * @param float $cfgscaledefault cfgScale default value.
     * @param int $seeddefault Seed default value.
     * @return array
     */
    private static function get_image_settings(float $cfgscaledefault = 6.5, int $seeddefault = 12): array {
        return [
            // Specifies how strongly the generated image should adhere to the prompt.
            // Use a lower value to introduce more randomness in the generation.
            'cfgScale' => self::setting(
                'settings_cfg_scale',
                PARAM_FLOAT,
                'settings_cfg_scale',
                ['min' => 1.1, 'max' => 10, 'default' => $cfgscaledefault],
            ),
            // Determines the initial noise setting for the generation process.
            // Changing the seed value while leaving all other parameters the same will
            // produce a totally new image that still adheres to your prompt, dimensions, and other settings.
            'seed' => self::setting(
                'settings_seed_img',
                PARAM_INT,
                'settings_seed_img',
                ['min' => 0, 'max' => 2147483646, 'default' => $seeddefault],
            ),
        ];
    }
}
