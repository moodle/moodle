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
 * An additional option that can be applied to an admin setting.
 *
 * The currently supported options are 'ADVANCED', 'LOCKED' and 'REQUIRED'.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class flag {
    /** @var bool Flag to indicate if this option can be toggled for this setting */
    private $enabled = false;
    /** @var bool Flag to indicate if this option defaults to true or false */
    private $default = false;
    /** @var string Short string used to create setting name - e.g. 'adv' */
    private $shortname = '';
    /** @var string String used as the label for this flag */
    private $displayname = '';
    /** @var bool Checkbox for this flag is displayed in admin page */
    const ENABLED = true;
    /** @var bool Checkbox for this flag is not displayed in admin page */
    const DISABLED = false;

    /**
     * Constructor
     *
     * @param bool $enabled Can this option can be toggled.
     *                      Should be one of self::ENABLED or self::DISABLED.
     * @param bool $default The default checked state for this setting option.
     * @param string $shortname The shortname of this flag. Currently supported flags are 'locked' and 'adv'
     * @param string $displayname The displayname of this flag. Used as a label for the flag.
     */
    public function __construct($enabled, $default, $shortname, $displayname) {
        $this->shortname = $shortname;
        $this->displayname = $displayname;
        $this->set_options($enabled, $default);
    }

    /**
     * Update the values of this setting options class
     *
     * @param bool $enabled Can this option can be toggled.
     *                      Should be one of self::ENABLED or self::DISABLED.
     * @param bool $default The default checked state for this setting option.
     */
    public function set_options($enabled, $default) {
        $this->enabled = $enabled;
        $this->default = $default;
    }

    /**
     * Should this option appear in the interface and be toggleable?
     *
     * @return bool Is it enabled?
     */
    public function is_enabled() {
        return $this->enabled;
    }

    /**
     * Should this option be checked by default?
     *
     * @return bool Is it on by default?
     */
    public function get_default() {
        return $this->default;
    }

    /**
     * Return the short name for this flag. e.g. 'adv' or 'locked'
     *
     * @return string
     */
    public function get_shortname() {
        return $this->shortname;
    }

    /**
     * Return the display name for this flag. e.g. 'Advanced' or 'Locked'
     *
     * @return string
     */
    public function get_displayname() {
        return $this->displayname;
    }

    /**
     * Save the submitted data for this flag - or set it to the default if $data is null.
     *
     * @param \core_admin\setting $setting - The admin setting for this flag
     * @param array $data - The data submitted from the form or null to set the default value for new installs.
     * @return bool
     */
    public function write_setting_flag(\admin_setting $setting, $data) {
        $result = true;
        if ($this->is_enabled()) {
            if (!isset($data)) {
                $value = $this->get_default();
            } else {
                $value = !empty($data[$setting->get_full_name() . '_' . $this->get_shortname()]);
            }
            $result = $setting->config_write($setting->name . '_' . $this->get_shortname(), $value);
        }

        return $result;

    }

    /**
     * Output the checkbox for this setting flag. Should only be called if the flag is enabled.
     *
     * @param \core_admin\setting $setting - The admin setting for this flag
     * @return string - The html for the checkbox.
     */
    public function output_setting_flag(\admin_setting $setting) {
        global $OUTPUT;

        $value = $setting->get_setting_flag_value($this);

        $context = new \stdClass();
        $context->id = $setting->get_id() . '_' . $this->get_shortname();
        $context->name = $setting->get_full_name() .  '_' . $this->get_shortname();
        $context->value = 1;
        $context->checked = $value ? true : false;
        $context->label = $this->get_displayname();

        return $OUTPUT->render_from_template('core_admin/setting_flag', $context);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(flag::class, \self::class);
