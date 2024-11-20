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
 * Defines backup_setting class
 *
 * @package     core_backup
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Parent class for all backup settings
 */
abstract class backup_setting extends base_setting implements checksumable {

    // Some constants defining levels of setting
    const ROOT_LEVEL     = 1;
    const COURSE_LEVEL   = 5;
    const SECTION_LEVEL  = 9;
    const ACTIVITY_LEVEL = 13;

    /** @var int the subsection level. */
    const SUBSECTION_LEVEL = 17;

    /** @var int the activity inside a subsection level. */
    const SUBACTIVITY_LEVEL = 21;

    /** @var int Level of the setting, eg {@link self::ROOT_LEVEL} */
    protected $level;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
        // Generate a default ui
        $this->uisetting = new backup_setting_ui_checkbox($this, $name);
    }

    /**
     * @return int Level of the setting, eg {@link self::ROOT_LEVEL}
     */
    public function get_level() {
        return $this->level;
    }

    /**
     * Creates and sets a user interface for this setting given appropriate arguments
     *
     * @param int $type
     * @param string $label
     * @param array $attributes
     * @param array $options
     */
    public function make_ui($type, $label, ?array $attributes = null, ?array $options = null) {
        $this->uisetting = backup_setting_ui::make($this, $type, $label, $attributes, $options);
        if (is_array($options) || is_object($options)) {
            $options = (array)$options;
            switch (get_class($this->uisetting)) {
                case 'backup_setting_ui_radio' :
                    // text
                    if (array_key_exists('text', $options)) {
                        $this->uisetting->set_text($options['text']);
                    }
                case 'backup_setting_ui_checkbox' :
                    // value
                    if (array_key_exists('value', $options)) {
                        $this->uisetting->set_value($options['value']);
                    }
                    break;
                case 'backup_setting_ui_select' :
                    // options
                    if (array_key_exists('options', $options)) {
                        $this->uisetting->set_values($options['options']);
                    }
                    break;
            }
        }
    }

    public function add_dependency(base_setting $dependentsetting, $type=setting_dependency::DISABLED_VALUE, $options=array()) {
        if (!($dependentsetting instanceof backup_setting)) {
            throw new backup_setting_exception('invalid_backup_setting_parameter');
        }
        // Check the dependency level is >= current level
        if ($dependentsetting->get_level() < $this->level) {
            throw new backup_setting_exception('cannot_add_upper_level_dependency', [
                $dependentsetting->get_level(),
                $dependentsetting->get_name(),
                $this->level,
                $this->get_name(),
            ]);
        }
        parent::add_dependency($dependentsetting, $type, $options);
    }

// checksumable interface methods

    public function calculate_checksum() {
        // Checksum is a simple md5 hash of name, value, level
        // Not following dependencies at all. Each setting will
        // calculate its own checksum
        return md5($this->name . '-' . $this->value . '-' . $this->level);
    }

    public function is_checksum_correct($checksum) {
        return $this->calculate_checksum() === $checksum;
    }
}

/**
 * Exception class used by all the @backup_setting stuff
 */
class backup_setting_exception extends base_setting_exception {
}
