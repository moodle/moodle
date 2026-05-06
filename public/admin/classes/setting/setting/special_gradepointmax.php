<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * A setting for setting the maximum grade value. Must be an integer between 1 and 10000.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_gradepointmax extends \core_admin\setting\setting\configtext {
    /**
     * Config gradepointmax constructor.
     *
     * @param string $name Overidden by "gradepointmax"
     * @param string $visiblename Overridden by "gradepointmax" language string.
     * @param string $description Overridden by "gradepointmax_help" language string.
     * @param string $defaultsetting Not used, overridden by 100.
     * @param mixed $paramtype Overridden by PARAM_INT.
     * @param int $size Overridden by 5.
     */
    public function __construct(
        $name = '',
        $visiblename = '',
        $description = '',
        $defaultsetting = '',
        $paramtype = PARAM_INT,
        $size = 5,
    ) {
        $name = 'gradepointmax';
        $visiblename = get_string('gradepointmax', 'grades');
        $description = get_string('gradepointmax_help', 'grades');
        $defaultsetting = 100;
        $paramtype = PARAM_INT;
        $size = 5;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    #[\Override]
    public function write_setting($data) {
        if ($data === '') {
            $data = (int)$this->defaultsetting;
        } else {
            $data = $data;
        }
        return parent::write_setting($data);
    }

    #[\Override]
    public function validate($data) {
        if (((string)(int)$data === (string)$data && $data > 0 && $data <= 10000)) {
            return true;
        } else {
            return get_string('gradepointmax_validateerror', 'grades');
        }
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'attributes' => [
                'maxlength' => 5,
            ],
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtext', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_gradepointmax::class, \admin_setting_special_gradepointmax::class);
