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

/**
 * Class that allow configuring a fontcolor color list.
 *
 * @package     tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_fontcolor;

use admin_setting;

/**
 * Tiny Font color plugin config utility.
 *
 * @package     tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_colorlist extends admin_setting {

    /**
     * Placeholder that is used as a value for the value of the original settings hidden input field.
     * @var string
     */
    private const PLACEHOLDER_ORIG_VALUE = '~~1~~+';

    /**
     * Store here the data that where extracted from the post request when saving.
     * @var array
     */
    private $settingval;

    /**
     * Return an XHTML string for the setting
     * @param mixed $data
     * @param string $query
     * @return string Returns an XHTML string
     * @throws \coding_exception
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        // The original object is destroyed, so we don't have information about the error. However, if
        // we identify the value being sent from the current post, then just fetch the original data again
        // from the request, validate it to know which field exactly caused the trouble being not valid.
        if ($data === static::PLACEHOLDER_ORIG_VALUE) {
            $this->get_setting_val_from_request();
            $this->validate($data);
            $colors = $this->settingval;
        } else {
            // Assume here that we got a json from the config out of the DB.
            $colors = json_decode($data, true);
            if (!is_array($colors)) {
                $colors = [];
            }
        }

        // Add an empty value to have a black input line below the already defined colors.
        $colors[] = [
            'name' => '',
            'value' => '',
        ];

        $default = $this->get_defaultsetting();
        $context = (object) [
            'header' => [
                'name' => get_string('placeholdercolorname', 'tiny_fontcolor'),
                'value' => get_string('placeholdercolorvalue', 'tiny_fontcolor'),
            ],
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => static::PLACEHOLDER_ORIG_VALUE,
            'forceltr' => $this->get_force_ltr(),
            'readonly' => $this->is_readonly(),
            'colors' => [],
        ];

        foreach (\array_keys($colors) as $i) {
            $row = [];
            foreach (['name', 'value'] as $field) {
                $suffix = '_' . $field . '_' . ($i + 1);
                $row[$field] = (object)[
                    'id' => $this->get_id() . $suffix,
                    'name' => $this->get_full_name() . $suffix,
                    'value' => $colors[$i][$field] ?? '',
                    'invalid' => $colors[$i][$field . '_error'] ?? false,
                    'last' => $i + 1 === count($colors),
                ];
            }
            $context->colors[] = $row;
        }
        $html = $OUTPUT->render_from_template('tiny_fontcolor/settings_config_color', $context);

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', $default, $query);
    }

    /**
     * Data must be validated.
     * @return bool
     */
    public function validate(): bool {
        $this->get_setting_val_from_request();
        $isvalid = true;
        foreach (\array_keys($this->settingval) as $i) {
            if (!plugininfo::validatecolorcode($this->settingval[$i]['value'])) {
                $this->settingval[$i]['value_error'] = true;
                $isvalid = false;
            }
            if (empty($this->settingval[$i]['name'])) {
                $this->settingval[$i]['name_error'] = true;
                $isvalid = false;
            }
        }
        return $isvalid;
    }

    /**
     * Get complex settings value (that is later converted into a json) from the
     * POST params (i.e. from the single input fields of color name and value).
     *
     * @return array
     */
    protected function get_setting_val_from_request(): array {
        if ($this->settingval === null) {
            $this->settingval = [];
            $names = [];
            $values = [];
            foreach ($_REQUEST as $key => $val) {
                if (strpos($key, $this->name . '_name_') !== false) {
                    $names[$key] = trim($val);
                } else if (strpos($key, $this->name . '_value_') !== false) {
                    $values[$key] = trim($val);
                }
            }
            foreach (\array_keys($names) as $i) {
                $j = str_replace('_name_', '_value_', $i);
                if (empty($names[$i]) && empty($values[$j])) {
                    continue;
                }
                $this->settingval[] = [
                    'name' => $names[$i],
                    'value' => $values[$j],
                ];
            }
        }
        return $this->settingval;
    }

    /**
     * Reads out a setting
     *
     * @return false|mixed|string|null
     */
    public function get_setting() {
        if ($this->settingval !== null) {
            return json_encode($this->settingval);
        }
        return $this->config_read($this->name);
    }

    /**
     * Writes in a setting
     *
     * @param array $data The data to write
     * @return bool|\lang_string|string
     * @throws \coding_exception
     */
    public function write_setting($data) {
        // The content of $data is ignored here, it must be fetched from the request.
        $data = $this->get_setting_val_from_request();
        if ($this->validate() !== true) {
            return false;
        }
        return ($this->config_write($this->name, json_encode($data)) ? '' : get_string('errorsetting', 'admin'));
    }
}
