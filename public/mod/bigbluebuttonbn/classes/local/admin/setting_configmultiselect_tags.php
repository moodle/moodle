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

namespace mod_bigbluebuttonbn\local\admin;

use admin_setting_configmultiselect;

/**
 * Multiselect admin setting with autocomplete that supports custom tag values.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2025 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class setting_configmultiselect_tags extends admin_setting_configmultiselect {
    /** @var string Placeholder shown in the enhanced select. */
    protected string $placeholder;
    /** @var bool Whether to show suggestions immediately. */
    protected bool $showsuggestions;
    /** @var string Text displayed when no selections exist. */
    protected string $noselectionstring;

    /**
     * Constructor.
     *
     * @param string $name Setting name.
     * @param string $visiblename Visible setting name.
     * @param string $description Setting description.
     * @param array $defaultsetting Default values.
     * @param array $choices Preset choices.
     * @param string $placeholder Placeholder string.
     * @param string $noselectionstring String displayed when there are no selections.
     * @param bool $showsuggestions Whether to show suggestions on focus.
     */
    public function __construct(
        string $name,
        string $visiblename,
        string $description,
        array $defaultsetting,
        array $choices,
        string $placeholder,
        string $noselectionstring,
        bool $showsuggestions = true
    ) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices);
        $this->placeholder = $placeholder;
        $this->showsuggestions = $showsuggestions;
        $this->noselectionstring = $noselectionstring;
    }

    /**
     * Get the current choices array.
     *
     * @return array
     */
    public function get_choices(): array {
        return $this->choices ?? [];
    }

    /**
     * Ensure choices include any stored values so they remain visible.
     *
     * @return bool
     */
    public function load_choices(): bool {
        $loaded = parent::load_choices();
        if (!$loaded) {
            return false;
        }

        // Remove any empty keys that may have been introduced.
        $cleaned = [];
        foreach ($this->choices as $key => $label) {
            if ((string)$key !== '') {
                $cleaned[$key] = $label;
            }
        }
        $this->choices = $cleaned;

        $current = $this->config_read($this->name);
        if (!empty($current)) {
            $values = array_filter(array_map('trim', explode(',', $current)), static function (string $value): bool {
                return $value !== '';
            });
            // Add any custom tags (not in preset choices) so they appear in the select.
            foreach ($values as $value) {
                // Normalize to lowercase to match storage format.
                $value = \core_text::strtolower($value);
                if ($value !== '' && !array_key_exists($value, $this->choices)) {
                    $this->choices[$value] = $this->resolve_label($value);
                }
            }
        }
        return true;
    }

    /**
     * Persist the selected values, allowing arbitrary entries.
     *
     * @param array $data
     * @return string
     */
    public function write_setting($data): string {
        if (!is_array($data)) {
            return '';
        }
        // Core multiselect sends this placeholder key when nothing is selected.
        unset($data['xxxxx']);

        // Collect and normalize submitted values.
        $submitted = [];
        foreach ($data as $value) {
            $value = trim(clean_param($value, PARAM_ALPHANUMEXT));
            $value = \core_text::strtolower($value);
            if ($value === '' || in_array($value, $submitted, true)) {
                continue;
            }
            $submitted[] = $value;
        }

        // Preserve order: keep existing stored values that are still selected.
        $current = $this->config_read($this->name);
        $values = [];
        if (!empty($current)) {
            $existing = array_filter(array_map('trim', explode(',', $current)), static function (string $v): bool {
                return $v !== '';
            });
            foreach ($existing as $value) {
                $value = \core_text::strtolower($value);
                // Only add if submitted and not already in values (defensive deduplication).
                if (in_array($value, $submitted, true) && !in_array($value, $values, true)) {
                    $values[] = $value;
                }
            }
        }

        // Append any new values that weren't previously stored.
        foreach ($submitted as $value) {
            if (!in_array($value, $values, true)) {
                $values[] = $value;
            }
        }

        $stored = implode(',', $values);
        return $this->config_write($this->name, $stored) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Resolve the label for a value, preferring existing language strings.
     *
     * @param string $value
     * @return string
     */
    protected function resolve_label(string $value): string {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $component = 'mod_bigbluebuttonbn';
        $stringmanager = get_string_manager();
        $candidates = [
            'view_recording_format_' . $value,
            $value,
        ];

        foreach ($candidates as $identifier) {
            if ($stringmanager->string_exists($identifier, $component)) {
                return get_string($identifier, $component);
            }
        }

        $formatted = str_replace(['_', '-'], ' ', $value);
        $formatted = preg_replace('/\s+/', ' ', $formatted);
        $formatted = trim($formatted);

        $formatted = \core_text::strtolower($formatted);

        return $formatted === '' ? $value : ucfirst($formatted);
    }

    /**
     * Render the autocomplete-enhanced multiselect input.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = ''): string {
        global $PAGE;

        $html = parent::output_html($data, $query);
        if ($html === '') {
            return $html;
        }

        if ($this->is_readonly()) {
            return $html;
        }

        // Use core autocomplete with a light wrapper that capitalizes newly typed tags.
        $params = [
            '#' . $this->get_id(),
            true,
            '',
            $this->placeholder,
            false,
            $this->showsuggestions,
            $this->noselectionstring,
        ];
        $PAGE->requires->js_call_amd('mod_bigbluebuttonbn/multiselect_tags', 'init', $params);

        return $html;
    }
}
