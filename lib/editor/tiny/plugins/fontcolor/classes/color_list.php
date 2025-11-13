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
 * Class that contains function for handling the color lists.
 *
 * @package     tiny_fontcolor
 * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_fontcolor;

/**
 * Tiny Font color plugin utility class for handling color lists.
 *
 * @package     tiny_fontcolor
 * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class color_list {

    /**
     * @var color[]
     */
    protected $colors = [];

    /**
     * Constructor, if a list is provided, it must be an array of tuplets with keys name and value.
     *
     * @param ?array $list
     */
    public function __construct(?array $list = []) {
        if ($list) {
            $this->colors = array_map(fn($x) => new color($x['name'] ?? '', $x['value'] ?? ''), $list);
        }
    }

    /**
     * Initialize the list from a json encoded string.
     *
     * @param string $json
     * @return self
     */
    public static function load_from_json(string $json) {
        $data = json_decode($json, true);
        $instance = (\is_array($data)) ? new self($data) : new self();
        return $instance;
    }

    /**
     * Add a color to the list.
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function add_color(string $name, string $value): self {
        $color = new color($name, $value);
        $this->colors[] = $color;
        return $this;
    }

    /**
     * Get number of colors.
     *
     * @return int
     */
    public function length(): int {
        return count($this->colors);
    }

    /**
     * Iterate over the color list.
     *
     * @return iterable color
     */
    public function get_list(): iterable {
        foreach ($this->colors as $color) {
            yield $color;
        }
    }

    /**
     * Return the list as an json with an array of tuples with keys name and value.
     *
     * @return string
     */
    public function to_json(): string {
        return json_encode(\array_map(fn($x) => ['name' => $x->get_name(), 'value' => $x->get_value()], $this->colors));
    }

    /**
     * From the color names, create a class name (must consist of lowercase a-z only).
     * To avoid conflicts the suffix -n where n is a number, is added.
     * The result is an array with the css class name as the key and the css value as value.
     *
     * @param ?string $prefix
     * @return array
     */
    public function get_css_class_list(?string $prefix = ''): array {
        $list = [];
        if ($this->colors === null) {
            $list;
        }

        foreach ($this->colors as $color) {
            $sanitized = preg_replace('/[^a-z]/', '', strtolower(strip_tags($color->get_name())));
            if (empty($sanitized)) {
                $sanitized = chr(97 + count($list));
            }
            if (\array_key_exists($sanitized, $list)) {
                $sanitized .= '-' . (count($list) + 1);
            }
            $list[$prefix . $sanitized] = $color->get_value();
        }
        return $list;
    }

    /**
     * Having a list of css classes and their values, this function returns a valid
     * css string. The css class names are prefixed with the plugin name and the settings key.
     *
     * @param string $setting
     * @return string
     */
    public function get_css_string(string $setting): string {
        $css = '';
        $prefixclass = plugininfo::PLUGIN_NAME . "-{$setting}-";
        $cssproperty = $setting === 'backgroundcolors' ? 'background-color' : 'color';
        foreach ($this->get_css_class_list() as $name => $value) {
            $css .= sprintf(".%s%s{%s:%s}\n", $prefixclass, $name, $cssproperty, $value);
        }
        return $css;
    }
}
