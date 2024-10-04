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

namespace aiprovider_ollama\aimodel;

use core_ai\aimodel\base;
use MoodleQuickForm;

/**
 * Llama 3.3 AI model.
 *
 * @package    aiprovider_ollama
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class llama33 extends base implements ollama_base {

    #[\Override]
    public function get_model_name(): string {
        return 'llama3.3';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'Llama 3.3';
    }

    #[\Override]
    public function get_model_settings(): array {
        return [
            'mirostat' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_mirostat',
                    'component' => 'aiprovider_ollama',
                ],
                'type' => PARAM_INT,
                'help' => [
                    'identifier' => 'settings_mirostat',
                    'component' => 'aiprovider_ollama',
                ],
            ],
            'temperature' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_ollama',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_ollama',
                ],
            ],
            'seed' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_seed',
                    'component' => 'aiprovider_ollama',
                ],
                'type' => PARAM_INT,
                'help' => [
                    'identifier' => 'settings_seed',
                    'component' => 'aiprovider_ollama',
                ],
            ],
            'top_k' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_top_k',
                    'component' => 'aiprovider_ollama',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_top_k',
                    'component' => 'aiprovider_ollama',
                ],
            ],
            'top_p' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_ollama',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_ollama',
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
    public function model_type(): int {
        return self::MODEL_TYPE_TEXT;
    }
}
