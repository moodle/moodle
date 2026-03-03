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
 * Meta model catalog.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meta extends awsbedrock_base {
    /**
     * Get Meta models.
     *
     * @return array<string, \aiprovider_awsbedrock\model_definition>
     */
    public static function get_models(): array {
        $settings = self::get_settings();

        return [
            'meta.llama3-8b-instruct-v1:0' => self::create_model(
                'meta.llama3-8b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $settings,
            ),
            'meta.llama3-70b-instruct-v1:0' => self::create_model(
                'meta.llama3-70b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $settings,
            ),
            'meta.llama3-1-8b-instruct-v1:0' => self::create_model(
                'meta.llama3-1-8b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-1-8b-instruct-v1:0'),
            ),
            'meta.llama3-1-70b-instruct-v1:0' => self::create_model(
                'meta.llama3-1-70b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-1-70b-instruct-v1:0'),
            ),
            'meta.llama3-1-405b-instruct-v1:0' => self::create_model(
                'meta.llama3-1-405b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-1-405b-instruct-v1:0'),
            ),
            'meta.llama3-2-1b-instruct-v1:0' => self::create_model(
                'meta.llama3-2-1b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-2-1b-instruct-v1:0'),
            ),
            'meta.llama3-2-3b-instruct-v1:0' => self::create_model(
                'meta.llama3-2-3b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-2-3b-instruct-v1:0'),
            ),
            'meta.llama3-2-11b-instruct-v1:0' => self::create_model(
                'meta.llama3-2-11b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-2-11b-instruct-v1:0'),
            ),
            'meta.llama3-2-90b-instruct-v1:0' => self::create_model(
                'meta.llama3-2-90b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-2-90b-instruct-v1:0'),
            ),
            'meta.llama3-3-70b-instruct-v1:0' => self::create_model(
                'meta.llama3-3-70b-instruct-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                self::with_cross_region($settings, 'us.meta.llama3-3-70b-instruct-v1:0'),
            ),
        ];
    }

    /**
     * Meta settings.
     *
     * @return array
     */
    private static function get_settings(): array {
        return [
            // Temperature – Use a lower value to decrease randomness in responses.
            'temperature' => self::setting(
                'settings_temperature',
                PARAM_FLOAT,
                'settings_temperature',
                ['min' => 0, 'max' => 1, 'default' => 0.5],
            ),
            // Top_p – Use a lower value to ignore less probable options and decrease the diversity of responses.
            'top_p' => self::setting(
                'settings_top_p',
                PARAM_FLOAT,
                'settings_top_p',
                ['min' => 0, 'max' => 1, 'default' => 0.9],
            ),
            // Max token  – The maximum number of tokens to generate in the response. Maximum token limits are strictly enforced.
            'max_gen_len' => self::setting(
                'settings_max_tokens',
                PARAM_INT,
                'settings_max_tokens',
                ['min' => 1, 'max' => 2048, 'default' => 512],
            ),
        ];
    }
}
