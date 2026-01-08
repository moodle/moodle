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

namespace aiprovider_gemini\aimodel;

use core_ai\aimodel\base;
use MoodleQuickForm;

/**
 * Gemini 2.5 Flashlite AI model.
 *
 * @package    aiprovider_gemini
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gemini25flashlite extends base implements gemini_base {
    #[\Override]
    public function get_model_name(): string {
        return 'gemini-2.5-flash-lite';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'Gemini 2.5 Flash lite';
    }

    #[\Override]
    public function get_model_settings(): array {
        return [
            // Temperature: controls how creative the AI responses are.
            // 0.0 = very predictable, 2.0 = very creative.
            'temperature' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_gemini',
                ],
                'type' => PARAM_FLOAT, // Float value from 0.0 to 2.0 controlling randomness/creativity.
                'help' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_gemini',
                ],
            ],
            // Top‑p: controls randomness using nucleus sampling.
            // 0.0 = most predictable, 1.0 = less restrictive.
            'top_p' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_gemini',
                ],
                'type' => PARAM_FLOAT, // Float value from 0.0 to 1.0 controlling nucleus sampling.
                'help' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_gemini',
                ],
            ],
            // Top‑k: maximum number of tokens considered when sampling.
            'top_k' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_top_k',
                    'component' => 'aiprovider_gemini',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_top_k',
                    'component' => 'aiprovider_gemini',
                ],
            ],
            // Max output tokens: limits the number of tokens the model will generate.
            'max_output_tokens' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_max_output_tokens',
                    'component' => 'aiprovider_gemini',
                ],
                'type' => PARAM_INT,
                'help' => [
                    'identifier' => 'settings_max_output_tokens',
                    'component' => 'aiprovider_gemini',
                ],
            ],
            // Stop Sequences – character sequences where the AI should stop generating text.
            'stop_sequences' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_stop_sequences',
                    'component' => 'aiprovider_gemini',
                ],
                'type' => PARAM_TEXT, // String or comma-separated list of sequences.
                'help' => [
                    'identifier' => 'settings_stop_sequences',
                    'component' => 'aiprovider_gemini',
                ],
            ],
        ];
    }

    #[\Override]
    public function add_model_settings(MoodleQuickForm $mform): void {
        $settings = $this->get_model_settings();
        foreach ($settings as $key => $setting) {
            $mform->addElement(
                $setting['elementtype'],
                $key,
                get_string($setting['label']['identifier'], $setting['label']['component']),
            );
            $mform->setType($key, $setting['type']);
            if (isset($setting['help'])) {
                $mform->addHelpButton($key, $setting['help']['identifier'], $setting['help']['component']);
            }
        }
    }

    #[\Override]
    public function model_type(): array {
        return [self::MODEL_TYPE_TEXT];
    }

    #[\Override]
    public function get_model_endpoint(): string {
        return 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent';
    }
}
