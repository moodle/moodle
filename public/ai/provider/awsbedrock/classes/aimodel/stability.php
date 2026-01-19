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
 * Stability model catalog.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stability extends awsbedrock_base {
    /**
     * Get Stability models.
     *
     * @return array<string, \aiprovider_awsbedrock\model_definition>
     */
    public static function get_models(): array {
        $settings = self::get_settings();

        return [
            'stability.stable-image-core-v1:1' => self::create_model(
                'stability.stable-image-core-v1:1',
                model_definition::MODEL_TYPE_IMAGE,
                $settings,
            ),
            'stability.stable-image-ultra-v1:1' => self::create_model(
                'stability.stable-image-ultra-v1:1',
                model_definition::MODEL_TYPE_IMAGE,
                $settings,
            ),
            'stability.sd3-5-large-v1:0' => self::create_model(
                'stability.sd3-5-large-v1:0',
                model_definition::MODEL_TYPE_IMAGE,
                $settings,
            ),
        ];
    }

    /**
     * Stability image settings.
     *
     * @return array
     */
    private static function get_settings(): array {
        return [
            // A specific value that is used to guide the 'randomness' of the generation.
            'seed' => self::setting(
                'settings_seed_img',
                PARAM_INT,
                'settings_seed_img',
                ['min' => 0, 'max' => 4294967295, 'default' => 0],
            ),
            // Keywords of what you do not wish to see in the output image. Max: 10.000 characters.
            'negative_prompt' => self::setting(
                'settings_negative_prompt_img',
                PARAM_TEXT,
                'settings_negative_prompt_img',
            ),
        ];
    }
}
