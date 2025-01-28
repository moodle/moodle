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
    public function has_model_settings(): bool {
        return true;
    }

    #[\Override]
    public function add_model_settings(MoodleQuickForm $mform): void {
        $mform->addElement(
            'text',
            'mirostat',
            get_string('settings_mirostat', 'aiprovider_ollama'),
        );
        $mform->setType('mirostat', PARAM_INT);
        $mform->addHelpButton('mirostat', 'settings_mirostat', 'aiprovider_ollama');

        $mform->addElement(
            'text',
            'temperature',
            get_string('settings_temperature', 'aiprovider_ollama'),
        );
        $mform->setType('temperature', PARAM_FLOAT);
        $mform->addHelpButton('temperature', 'settings_temperature', 'aiprovider_ollama');

        $mform->addElement(
            'text',
            'seed',
            get_string('settings_seed', 'aiprovider_ollama'),
        );
        $mform->setType('seed', PARAM_INT);
        $mform->addHelpButton('seed', 'settings_seed', 'aiprovider_ollama');

        $mform->addElement(
            'text',
            'top_k',
            get_string('settings_top_k', 'aiprovider_ollama'),
        );
        $mform->setType('top_k', PARAM_FLOAT);
        $mform->addHelpButton('top_k', 'settings_top_k', 'aiprovider_ollama');

        $mform->addElement(
            'text',
            'top_p',
            get_string('settings_top_p', 'aiprovider_ollama'),
        );
        $mform->setType('top_p', PARAM_FLOAT);
        $mform->addHelpButton('top_p', 'settings_top_p', 'aiprovider_ollama');
    }

    #[\Override]
    public function model_type(): int {
        return self::MODEL_TYPE_TEXT;
    }
}
