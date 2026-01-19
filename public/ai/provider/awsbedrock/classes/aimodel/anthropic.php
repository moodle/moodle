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
 * Anthropic model catalog.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class anthropic extends awsbedrock_base {
    /**
     * Get Anthropic models.
     *
     * @return array<string, \aiprovider_awsbedrock\model_definition>
     */
    public static function get_models(): array {
        $default = self::get_settings();
        $v45 = self::get_settings(65536);

        return [
            'anthropic.claude-3-5-sonnet-20240620-v1:0' => self::create_model(
                'anthropic.claude-3-5-sonnet-20240620-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $default,
            ),
            'anthropic.claude-3-5-sonnet-20241022-v2:0' => self::create_model(
                'anthropic.claude-3-5-sonnet-20241022-v2:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($default, 'us.anthropic.claude-3-5-sonnet-20241022-v2:0'),
            ),
            'anthropic.claude-3-7-sonnet-20250219-v1:0' => self::create_model(
                'anthropic.claude-3-7-sonnet-20250219-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($default, 'us.anthropic.claude-3-7-sonnet-20250219-v1:0'),
            ),
            'anthropic.claude-3-haiku-20240307-v1:0' => self::create_model(
                'anthropic.claude-3-haiku-20240307-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $default,
            ),
            'anthropic.claude-sonnet-4-20250514-v1:0' => self::create_model(
                'anthropic.claude-sonnet-4-20250514-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($default, 'global.anthropic.claude-sonnet-4-20250514-v1:0'),
            ),
            'anthropic.claude-haiku-4-5-20251001-v1:0' => self::create_model(
                'anthropic.claude-haiku-4-5-20251001-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($v45, 'global.anthropic.claude-haiku-4-5-20251001-v1:0'),
            ),
            'anthropic.claude-sonnet-4-5-20250929-v1:0' => self::create_model(
                'anthropic.claude-sonnet-4-5-20250929-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($v45, 'global.anthropic.claude-sonnet-4-5-20250929-v1:0'),
            ),
        ];
    }

    /**
     * Anthropic settings.
     *
     * @param int $maxtokensmax Max allowed max_tokens value.
     * @return array
     */
    private static function get_settings(int $maxtokensmax = 4096): array {
        return [
            // Temperature – Use a lower value to decrease randomness in responses.
            'temperature' => self::setting(
                'settings_temperature',
                PARAM_FLOAT,
                'settings_temperature',
                ['min' => 0, 'max' => 1, 'default' => 1],
            ),
            // Top_p – Use a lower value to ignore less probable options and decrease the diversity of responses.
            'top_p' => self::setting(
                'settings_top_p',
                PARAM_FLOAT,
                'settings_top_p',
                ['min' => 0, 'max' => 1, 'default' => 0.999],
            ),
            // Top_k – Use to remove long tail low probability responses.
            'top_k' => self::setting(
                'settings_top_k',
                PARAM_FLOAT,
                'settings_top_k',
                ['min' => 0, 'max' => 500, 'default' => 250],
            ),
            // Max token – The maximum number of tokens to generate in the response. Maximum token limits are strictly enforced.
            'max_tokens' => self::setting(
                'settings_max_tokens',
                PARAM_INT,
                'settings_max_tokens',
                ['min' => 0, 'max' => $maxtokensmax, 'default' => 4096],
                true,
            ),
            // Stop Sequences – Specify a character sequence to indicate where the model should stop.
            'stop_sequences' => self::setting(
                'settings_stop_sequences',
                PARAM_TEXT,
                'settings_stop_sequences',
            ),
        ];
    }
}
