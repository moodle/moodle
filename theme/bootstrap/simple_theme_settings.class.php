<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * A wrapper round Moodle's settings API to simplify the common cases
 * for themers, often via "convention over configuration" and the reduction
 * in repetitive typing
 *
 * Assumes all strings are in the theme lang file, assumes the title
 * langstring is the same as the name, and that the description langstring
 * is the same as the title with 'desc' added to the end.
 *
 * @package    theme_bootstrap
 * @copyright  2014 Bas Brands, www.basbrands.nl
 * @authors    Bas Brands, David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class simple_theme_settings {
    private $themename;
    private $settingspage;

    public function __construct($settingspage, $themename) {
        $this->themename = $themename;
        $this->settingspage = $settingspage;
    }

    private function name_for($setting, $suffix='') {
        return $this->themename.'/'.$setting.$suffix;
    }

    private function title_for($setting, $additional = null) {
        return get_string($setting, $this->themename, $additional);
    }

    private function description_for($setting) {
        return get_string($setting.'desc', $this->themename);
    }

    public function add_checkbox($setting, $default='0', $checked='1', $unchecked='0') {
        $checkbox = new admin_setting_configcheckbox(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default,
            $checked,
            $unchecked
        );
        $checkbox->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($checkbox);
    }

    public function add_text($setting, $default='') {
        $text = new admin_setting_configtext(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default
        );
        $text->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($text);
    }
    public function add_heading($setting) {
        $heading = new admin_setting_heading(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting)
        );
        $this->settingspage->add($heading);
    }

    public function add_select($setting, $default, $options) {
        $select = new admin_setting_configselect(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default,
            $options
        );
        $select->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($select);
    }

    public function add_textarea($setting, $default='') {
        $textarea = new admin_setting_configtextarea(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default
        );
        $textarea->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($textarea);
    }

    public function add_htmleditor($setting, $default='') {
        $htmleditor = new admin_setting_confightmleditor(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default
        );
        $htmleditor->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($htmleditor);
    }
    public function add_colourpicker($setting, $default='#666') {
        $colorpicker = new admin_setting_configcolourpicker(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $default,
            null // Don't hook up any javascript preview of color change.
        );
        $colorpicker->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($colorpicker);
    }
    public function add_file($setting) {
        $file = new admin_setting_configstoredfile(
            $this->name_for($setting),
            $this->title_for($setting),
            $this->description_for($setting),
            $setting // TODO find out what this does,
                     // for now assume it just needs to be unique.
        );
        $file->set_updatedcallback('theme_reset_all_caches');
        $this->settingspage->add($file);
    }
    public function add_numbered_textareas($setting, $count, $default='') {
        for ($i = 1; $i <= $count; $i++) {
            $textarea = new admin_setting_configtextarea(
                $this->name_for($setting, $i),
                $this->title_for($setting, $i),
                $this->description_for($setting),
                $default
            );
            $textarea->set_updatedcallback('theme_reset_all_caches');
            $this->settingspage->add($textarea);
        }
    }
}

