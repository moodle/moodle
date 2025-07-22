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

namespace aiprovider_openai\aimodel;

/**
 * O1 AI model.
 *
 * @package    aiprovider_openai
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class o1 extends gpt4o {

    #[\Override]
    public function get_model_name(): string {
        return 'o1';
    }

    #[\Override]
    public function get_model_display_name(): string {
        return 'O1';
    }

    #[\Override]
    public function get_model_settings(): array {
        return [
            'top_p' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_openai',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_top_p',
                    'component' => 'aiprovider_openai',
                ],
            ],
            'max_completion_tokens' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_max_completion_tokens',
                    'component' => 'aiprovider_openai',
                ],
                'type' => PARAM_INT,
                'help' => [
                    'identifier' => 'settings_max_completion_tokens',
                    'component' => 'aiprovider_openai',
                ],
            ],
        ];
    }

    #[\Override]
    public function model_type(): array {
        return [self::MODEL_TYPE_TEXT];
    }
}
