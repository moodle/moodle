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
     * @var color_list
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
            $this->validate();
            $mustvalidate = true;
        } else {
            // Assume here that we got a json from the config out of the DB.
            $this->settingval = color_list::load_from_json($data);
            $mustvalidate = false;
        }

        // Add an empty value to have a black input line below the already defined colors.
        $this->settingval->add_color('', '');

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
            'plugindir' => plugininfo::get_base_dir(),
            'readonly' => $this->is_readonly(),
            'colors' => [],
        ];

        $i = 1;
        foreach ($this->settingval->get_list() as $color) {
            $row = [];
            foreach (['name', 'value'] as $field) {
                $suffix = '_' . $field . '_' . $i;
                $getter = 'get_' . $field;
                $haserror = "has_{$field}_error";
                $row[$field] = (object)[
                    'id' => $this->get_id() . $suffix,
                    'name' => $this->get_full_name() . $suffix,
                    'value' => $color->$getter(),
                    'invalid' => $mustvalidate && $color->$haserror(),
                    'last' => $i === $this->settingval->length(),
                ];
            }
            $i++;
            $context->colors[] = $row;
        }
        $html = $OUTPUT->render_from_template('tiny_fontcolor/settings_config_color', $context);

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', $default, $query);
    }

    /**
     * Data must be validated. Check that each color has a name and a valid hex code.
     * @return bool
     */
    public function validate(): bool {
        foreach ($this->settingval->get_list() as $color) {
            if (!$color->is_valid()) {
                return false;

            }
        }
        return true;
    }

    /**
     * Get complex settings value (that is later converted into a json) from the
     * POST params (i.e. from the single input fields of color name and value).
     *
     * @return color_list
     */
    protected function get_setting_val_from_request(): color_list {
        if ($this->settingval === null) {
            $this->settingval = new color_list();
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
                $this->settingval->add_color($names[$i], $values[$j]);
            }
        }
        return $this->settingval;
    }

    /**
     * Get the current setting whether to use css classnames or the color code directly.
     * The settings is taken from the request (it might be changed in the current settings
     * save action) or read from the settings values, when no information is in the request.
     *
     * @return bool
     */
    protected function use_css_classnames(): bool {

        $name = substr($this->get_full_name(), 0, strrpos($this->get_full_name(), '_')) . '_usecssclassnames';
        if (isset($_REQUEST) && isset($_REQUEST[$name])) {
            return (bool)$_REQUEST[$name];
        }
        return (bool)$this->config_read('usecssclassnames');
    }

    /**
     * Save the css class names of the colors in the theme / scss setting.
     * Do this for all themes.
     *
     * @return bool
     */
    protected function save_css_classnames(): bool {
        $themes = \core_component::get_plugin_list('theme');
        foreach (\array_keys($themes) as $theme) {
            $key = 'theme_' . $theme;
            $scss = get_config($key, 'scss');
            $p = mb_strpos($scss, $this->get_custom_css_marker('start'));
            $q = mb_strpos($scss, $this->get_custom_css_marker('end'));
            if ($p !== 0 && $q !== 0 && $p < $q) {
                $scss = mb_substr($scss, 0, $p)
                    . $this->get_custom_css_marker('start') . PHP_EOL
                    . $this->settingval->get_css_string($this->name)
                    . mb_substr($scss, $q);
            } else {
                $scss .= PHP_EOL . $this->get_custom_css_marker('start') . PHP_EOL
                    . $this->settingval->get_css_string($this->name)
                    . PHP_EOL . $this->get_custom_css_marker('end') . PHP_EOL;
            }
            try {
                set_config('scss', $scss, $key);
            } catch (\Exception $e) {
                debugging("Error updating $key/scss -> reason: " . $e->getMessage(), DEBUG_NORMAL, $e->getTrace());
                return false;
            }
        }
        return true;
    }

    /**
     * Marker string to find start and end of the custom scss that is set from this color
     * option in the theme scss setting.
     *
     * @param string $type
     * @return string
     */
    protected function get_custom_css_marker(string $type): string {
        return sprintf('/* automatically set by %s %s */', $this->get_full_name(), $type);
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
     * Writes the color list into a settings key.
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
        // Write the settings as a json encoded string into the apporpiate settings key.
        $res = ($this->config_write($this->name, $data->to_json()) ? '' : get_string('errorsetting', 'admin'));
        if (!empty($res)) {
            $res;
        }
        // In case there are css classes used, we need to write the class list with the colors into
        // the themes scss setting as well.
        if ($this->use_css_classnames() && !$this->save_css_classnames()) {
            return get_string('errorupdatingthemescss', $this->plugin);
        }
        return '';
    }
}
