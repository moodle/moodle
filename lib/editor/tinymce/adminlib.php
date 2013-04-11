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
 * TinyMCE admin setting stuff.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/pluginlib.php");


/**
 * Editor subplugin info class.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo_tinymce extends plugininfo_base {

    public function is_uninstall_allowed() {
        return true;
    }

    public function get_uninstall_url() {
        return new moodle_url('/lib/editor/tinymce/subplugins.php', array('delete' => $this->name, 'sesskey' => sesskey()));
    }

    public function get_settings_section_name() {
        return 'tinymce'.$this->name.'settings';
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // in case settings.php wants to refer to them
        $ADMIN = $adminroot; // may be used in settings.php

        $settings = null;
        if ($hassiteconfig && file_exists($this->full_path('settings.php'))) {
            $section = $this->get_settings_section_name();
            $settings = new admin_settingpage($section, $this->displayname,
                    'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // this may also set $settings to null
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public function is_enabled() {
        static $disabledsubplugins = null; // TODO: MDL-34344 remove this once get_config() is cached via MUC!

        if (is_null($disabledsubplugins)) {
            $disabledsubplugins = array();
            $config = get_config('editor_tinymce', 'disabledsubplugins');
            if ($config) {
                $config = explode(',', $config);
                foreach ($config as $sp) {
                    $sp = trim($sp);
                    if ($sp !== '') {
                        $disabledsubplugins[$sp] = $sp;
                    }
                }
            }
        }

        return !isset($disabledsubplugins[$this->name]);
    }
}


/**
 * Special class for TinyMCE subplugin administration.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tiynce_subplugins_settings extends admin_setting {
    public function __construct() {
        $this->nosave = true;
        parent::__construct('tinymcesubplugins', get_string('subplugintype_tinymce_plural', 'editor_tinymce'), '', '');
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything.
     *
     * @param string $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available subplugins.
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $subplugins = get_plugin_list('tinymce');
        foreach ($subplugins as $name=>$dir) {
            if (stripos($name, $query) !== false) {
                return true;
            }

            $namestr = get_string('pluginname', 'tinymce_'.$name);
            if (strpos(textlib::strtolower($namestr), textlib::strtolower($query)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $PAGE;
        require_once("$CFG->libdir/editorlib.php");
        require_once("$CFG->libdir/pluginlib.php");
        require_once(__DIR__.'/lib.php');
        $tinymce = new tinymce_texteditor();
        $pluginmanager = plugin_manager::instance();

        // display strings
        $strbuttons = get_string('availablebuttons', 'editor_tinymce');
        $strdisable = get_string('disable');
        $strenable = get_string('enable');
        $strname = get_string('name');
        $strsettings = get_string('settings');
        $struninstall = get_string('uninstallplugin', 'admin');
        $strversion = get_string('version');

        $subplugins = get_plugin_list('tinymce');

        $return = $OUTPUT->heading(get_string('subplugintype_tinymce_plural', 'editor_tinymce'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox tinymcesubplugins');

        $table = new html_table();
        $table->head  = array($strname, $strbuttons, $strversion, $strenable, $strsettings, $struninstall);
        $table->align = array('left', 'left', 'center', 'center', 'center', 'center');
        $table->data  = array();
        $table->width = '100%';

        // Iterate through subplugins.
        foreach ($subplugins as $name => $dir) {
            $namestr = get_string('pluginname', 'tinymce_'.$name);
            $version = get_config('tinymce_'.$name, 'version');
            if ($version === false) {
                $version = '';
            }
            $plugin = $tinymce->get_plugin($name);
            $plugininfo = $pluginmanager->get_plugin_info('tinymce_'.$name);

            // Add hide/show link.
            if (!$version) {
                $hideshow = '';
                $displayname = html_writer::tag('span', $name, array('class'=>'error'));
            } else if ($plugininfo->is_enabled()) {
                $url = new moodle_url('/lib/editor/tinymce/subplugins.php', array('sesskey'=>sesskey(), 'return'=>'settings', 'disable'=>$name));
                $hideshow = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/hide'), 'class'=>'iconsmall', 'alt'=>$strdisable));
                $hideshow = html_writer::link($url, $hideshow);
                $displayname = html_writer::tag('span', $namestr);
            } else {
                $url = new moodle_url('/lib/editor/tinymce/subplugins.php', array('sesskey'=>sesskey(), 'return'=>'settings', 'enable'=>$name));
                $hideshow = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/show'), 'class'=>'iconsmall', 'alt'=>$strenable));
                $hideshow = html_writer::link($url, $hideshow);
                $displayname = html_writer::tag('span', $namestr, array('class'=>'dimmed_text'));
            }

            if ($PAGE->theme->resolve_image_location('icon', 'tinymce_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'tinymce_' . $name, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
            }
            $displayname  = $icon . ' ' . $displayname;

            // Add available buttons.
            $buttons = implode(', ', $plugin->get_buttons());
            $buttons = html_writer::tag('span', $buttons, array('class'=>'tinymcebuttons'));

            // Add settings link.
            if (!$version) {
                $settings = '';
            } else if ($url = $plugininfo->get_settings_url()) {
                $settings = html_writer::link($url, $strsettings);
            } else {
                $settings = '';
            }

            // Add uninstall info.
            if ($version) {
                $url = new moodle_url($plugininfo->get_uninstall_url(), array('return'=>'settings'));
                $uninstall = html_writer::link($url, $struninstall);
            } else {
                $uninstall = '';
            }

            // Add a row to the table.
            $table->data[] = array($displayname, $buttons, $version, $hideshow, $settings, $uninstall);
        }
        $return .= html_writer::table($table);
        $return .= html_writer::tag('p', get_string('tablenosave', 'admin'));
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

class editor_tinymce_json_setting_textarea extends admin_setting_configtextarea {
    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        $result = parent::output_html($data, $query);

        $data = trim($data);
        if ($data) {
            $decoded = json_decode($data, true);
            // Note: it is not very nice to abuse these file classes, but anyway...
            if (is_array($decoded)) {
                $valid = '<span class="pathok">&#x2714;</span>';
            } else {
                $valid = '<span class="patherror">&#x2718;</span>';
            }
            $result = str_replace('</textarea>', '</textarea>'.$valid, $result);
        }

        return $result;
    }
}
