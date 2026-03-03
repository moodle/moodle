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
 * Mistral model catalog.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mistral extends awsbedrock_base {
    /**
     * Get Mistral models.
     *
     * @return array<string, \aiprovider_awsbedrock\model_definition>
     */
    public static function get_models(): array {
        $base = self::get_settings();

        $mixtral = $base;
        if (isset($mixtral['max_tokens']['help']['a']['max'])) {
            $mixtral['max_tokens']['help']['a']['max'] = 4096;
        }

        $large = $base;
        if (isset($large['temperature']['help']['a']['default'])) {
            $large['temperature']['help']['a']['default'] = 0.7;
        }
        if (isset($large['top_p']['help']['a']['default'])) {
            $large['top_p']['help']['a']['default'] = 1;
        }
        if (isset($large['top_k']['help']['a']['default'])) {
            $large['top_k']['help']['a']['default'] = get_string('none', 'aiprovider_awsbedrock');
        }
        if (isset($large['max_tokens']['help']['a']['default'])) {
            $large['max_tokens']['help']['a']['default'] = 8192;
        }

        $small = $large;

        return [
            'mistral.mistral-7b-instruct-v0:2' => self::create_model(
                'mistral.mistral-7b-instruct-v0:2',
                model_definition::MODEL_TYPE_TEXT,
                $base,
            ),
            'mistral.mixtral-8x7b-instruct-v0:1' => self::create_model(
                'mistral.mixtral-8x7b-instruct-v0:1',
                model_definition::MODEL_TYPE_TEXT,
                $mixtral,
            ),
            'mistral.mistral-large-2402-v1:0' => self::create_model(
                'mistral.mistral-large-2402-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $large,
            ),
            'mistral.mistral-small-2402-v1:0' => self::create_model(
                'mistral.mistral-small-2402-v1:0',
                model_definition::MODEL_TYPE_TEXT,
                $small,
            ),
        ];
    }

    /**
     * Mistral settings.
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
            // Top_k – Use to remove long tail low probability responses.
            'top_k' => self::setting(
                'settings_top_k',
                PARAM_FLOAT,
                'settings_top_k',
                ['min' => 1, 'max' => 200, 'default' => 50],
            ),
            // Max token  – The maximum number of tokens to generate in the response. Maximum token limits are strictly enforced.
            'max_tokens' => self::setting(
                'settings_max_tokens',
                PARAM_INT,
                'settings_max_tokens',
                ['min' => 1, 'max' => 8192, 'default' => 512],
            ),
            // Stop Sequences – Specify a character sequence to indicate where the model should stop.
            'stop' => self::setting(
                'settings_stop_sequences',
                PARAM_TEXT,
                'settings_stop_sequences',
            ),
        ];
    }
}
