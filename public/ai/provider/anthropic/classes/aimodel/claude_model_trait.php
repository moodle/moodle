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

namespace aiprovider_anthropic\aimodel;

use MoodleQuickForm;

/**
 * Shared settings implementation for Anthropic Claude AI models.
 *
 * Every Claude model exposes the same two generation settings (max_tokens and, where the
 * model still accepts it, temperature), so the implementation lives here rather than being
 * repeated in each model class.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait claude_model_trait {
    /**
     * Get the default max_tokens value for this model.
     *
     * @return int The default maximum number of tokens to generate.
     */
    public function get_default_max_tokens(): int {
        return claude_base::DEFAULT_MAX_TOKENS;
    }

    /**
     * Whether this model accepts the temperature sampling parameter.
     *
     * Defaults to false: starting with Claude Opus 4.7, and spreading to Sonnet-class
     * models from Claude Sonnet 5, newer Claude models reject temperature (and top_p/top_k)
     * with a 400 error. This is an evolving, generation-wide restriction rather than a
     * one-off exception, so an unverified or future model is more likely to reject it than
     * accept it. Models confirmed to still accept temperature must explicitly override this
     * to return true.
     *
     * @return bool True if the model accepts a temperature value.
     */
    public function supports_temperature(): bool {
        return false;
    }

    /**
     * Get all settings that can be configured for this model.
     *
     * @return array Array of settings.
     */
    public function get_model_settings(): array {
        $settings = [
            // Max tokens: the maximum number of tokens the model will generate.
            // Required on every Anthropic Messages API request.
            'max_tokens' => [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_max_tokens',
                    'component' => 'aiprovider_anthropic',
                ],
                'type' => PARAM_INT,
                'default' => $this->get_default_max_tokens(),
                'help' => [
                    'identifier' => 'settings_max_tokens',
                    'component' => 'aiprovider_anthropic',
                ],
            ],
        ];

        if ($this->supports_temperature()) {
            // Temperature: controls how creative the AI responses are.
            // 0.0 = very predictable, 1.0 = very creative.
            $settings['temperature'] = [
                'elementtype' => 'text',
                'label' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_anthropic',
                ],
                'type' => PARAM_FLOAT,
                'help' => [
                    'identifier' => 'settings_temperature',
                    'component' => 'aiprovider_anthropic',
                ],
            ];
        }

        return $settings;
    }

    /**
     * Add the model settings to the form.
     *
     * @param MoodleQuickForm $mform The form to add the model settings to.
     */
    public function add_model_settings(MoodleQuickForm $mform): void {
        $settings = $this->get_model_settings();
        foreach ($settings as $key => $setting) {
            $mform->addElement(
                $setting['elementtype'],
                $key,
                get_string($setting['label']['identifier'], $setting['label']['component']),
            );
            $mform->setType($key, $setting['type']);
            // Only apply the fallback default if a stored value has not already been merged in via set_data().
            if (array_key_exists('default', $setting) && !array_key_exists($key, $mform->_defaultValues)) {
                $mform->setDefault($key, $setting['default']);
            }
            if (isset($setting['help'])) {
                $mform->addHelpButton($key, $setting['help']['identifier'], $setting['help']['component']);
            }
        }
    }
}
