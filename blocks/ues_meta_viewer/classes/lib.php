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
 * Utility classes.
 *
 * @package    block_ues_meta_viewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class meta_data_ui_element {
    protected $name;
    protected $key;
    protected $value;

    public function __construct($field, $name) {
        $this->key($field);
        $this->name($name);
        $this->value = optional_param($this->key, null, PARAM_TEXT);
    }

    public function key($key = null) {
        if ($key) {
            $this->key = $key;
        }
        return $this->key;
    }

    public function name($name = null) {
        if ($name) {
            $this->name = $name;
        }
        return $this->name;
    }

    public function value() {
        return $this->value;
    }

    public function format($user) {
        if (!isset($user->{$this->key()})) {
            return get_string('not_available', 'block_ues_meta_viewer');
        }
        return $user->{$this->key()};
    }

    public function translate_value($dsl) {
        $value = trim($this->value());
        $strip = function ($what) use ($value) {
            return preg_replace('/%/', '', $value);
        };

        if (strpos($value, ',')) {
            return $dsl->in(explode(',', $value));
        } else if (strpos($value, '%') === 0 and strpos($value, '%', 1) > 0) {
            return $dsl->like($strip('%'));
        } else if (strpos($value, '%') === 0) {
            return $dsl->ends_with($strip('%'));
        } else if (strpos($value, '%') > 0) {
            return $dsl->starts_with($strip('%'));
        } else if (strpos($value, '<') === 0) {
            return $dsl->less($strip('<'));
        } else if (strpos($value, '>') === 0) {
            return $dsl->greater($strip('>'));
        } else if (strtolower($value) == 'null') {
            return $dsl->is(null)->equal('');
        } else if (strtolower($value) == 'not null') {
            return $dsl->not_equal('');
        } else {
            return $dsl->equal($value);
        }
    }
    public abstract function html();
    public abstract function sql($dsl);
}

class meta_data_text_box extends meta_data_ui_element {
    public function html() {
        $data = $this->value() !== null ? $this->value() : '';
        $params = array(
            'type' => 'text',
            'placeholder' => $this->name(),
            'name' => $this->key()
        );

        if (trim($data) !== '') {
            $params['value'] = $this->value();
        }
        return html_writer::empty_tag('input', $params);
    }

    public function sql($dsl) {
        $key = $this->key();
        $value = $this->value();

        if (trim($value) === '') {
            return $dsl;
        }
        return $this->translate_value($dsl->{$key});
    }
}
