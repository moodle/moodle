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

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Special class for media player plugins management.
 *
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managemediaplayers extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('managemediaplayers', get_string('managemediaplayers', 'media'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available enrol plugins
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = \core_text::strtolower($query);
        $plugins = \core_plugin_manager::instance()->get_plugins_of_type('media');
        foreach ($plugins as $name => $plugin) {
            $localised = $plugin->displayname;
            if (strpos(\core_text::strtolower($name), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(\core_text::strtolower($localised), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Sort plugins so enabled plugins are displayed first and all others are displayed in the end sorted by rank.
     * @return \core\plugininfo\media[]
     */
    protected function get_sorted_plugins() {
        $pluginmanager = \core_plugin_manager::instance();

        $plugins = $pluginmanager->get_plugins_of_type('media');
        $enabledplugins = $pluginmanager->get_enabled_plugins('media');

        // Sort plugins so enabled plugins are displayed first and all others are displayed in the end sorted by rank.
        \core_collator::asort_objects_by_method($plugins, 'get_rank', \core_collator::SORT_NUMERIC);

        $order = array_values($enabledplugins);
        $order = array_merge($order, array_diff(array_reverse(array_keys($plugins)), $order));

        $sortedplugins = array();
        foreach ($order as $name) {
            $sortedplugins[$name] = $plugins[$name];
        }

        return $sortedplugins;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB, $PAGE;

        // Display strings.
        $strup        = get_string('up');
        $strdown      = get_string('down');
        $strsettings  = get_string('settings');
        $strenable    = get_string('enable');
        $strdisable   = get_string('disable');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strversion   = get_string('version');
        $strname      = get_string('name');
        $strsupports  = get_string('supports', 'core_media');

        $pluginmanager = \core_plugin_manager::instance();

        $plugins = $this->get_sorted_plugins();
        $enabledplugins = $pluginmanager->get_enabled_plugins('media');

        $return = $OUTPUT->box_start('generalbox mediaplayersui');

        $table = new \html_table();
        $table->head  = array($strname, $strsupports, $strversion,
            $strenable, $strup.'/'.$strdown, $strsettings, $struninstall);
        $table->colclasses = array('leftalign', 'leftalign', 'centeralign',
            'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'mediaplayerplugins';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data  = array();

        // Iterate through media plugins and add to the display table.
        $updowncount = 1;
        $url = new \moodle_url('/admin/media.php', array('sesskey' => sesskey()));
        $printed = array();
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));

        $usedextensions = [];
        foreach ($plugins as $name => $plugin) {
            $url->param('media', $name);
            /** @var \core\plugininfo\media $plugininfo */
            $plugininfo = $pluginmanager->get_plugin_info('media_'.$name);
            $version = $plugininfo->versiondb;
            $supports = $plugininfo->supports($usedextensions);

            // Hide/show links.
            $class = '';
            if (!$plugininfo->is_installed_and_upgraded()) {
                $hideshow = '';
                $enabled = false;
                $displayname = '<span class="notifyproblem">'.$name.'</span>';
            } else {
                $enabled = $plugininfo->is_enabled();
                if ($enabled) {
                    $hideshow = \html_writer::link(new \moodle_url($url, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $strdisable, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $hideshow = \html_writer::link(new \moodle_url($url, array('action' => 'enable')),
                        $OUTPUT->pix_icon('t/show', $strenable, 'moodle', array('class' => 'iconsmall')));
                    $class = 'dimmed_text';
                }
                $displayname = $plugin->displayname;
                if (get_string_manager()->string_exists('pluginname_help', 'media_' . $name)) {
                    $displayname .= '&nbsp;' . $OUTPUT->help_icon('pluginname', 'media_' . $name);
                }
            }
            if ($PAGE->theme->resolve_image_location('icon', 'media_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'media_' . $name, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
            }

            // Up/down link (only if enrol is enabled).
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown = \html_writer::link(new \moodle_url($url, array('action' => 'up')),
                        $OUTPUT->pix_icon('t/up', $strup, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $updown = $spacer;
                }
                if ($updowncount < count($enabledplugins)) {
                    $updown .= \html_writer::link(new \moodle_url($url, array('action' => 'down')),
                        $OUTPUT->pix_icon('t/down', $strdown, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $updown .= $spacer;
                }
                ++$updowncount;
            }

            $uninstall = '';
            $status = $plugininfo->get_status();
            if ($status === \core_plugin_manager::PLUGIN_STATUS_MISSING) {
                $uninstall = get_string('status_missing', 'core_plugin') . '<br/>';
            }
            if ($status === \core_plugin_manager::PLUGIN_STATUS_NEW) {
                $uninstall = get_string('status_new', 'core_plugin');
            } else if ($uninstallurl = $pluginmanager->get_uninstall_url('media_'.$name, 'manage')) {
                $uninstall .= \html_writer::link($uninstallurl, $struninstall);
            }

            $settings = '';
            if ($plugininfo->get_settings_url()) {
                $settings = \html_writer::link($plugininfo->get_settings_url(), $strsettings);
            }

            // Add a row to the table.
            $row = new \html_table_row(array($icon.$displayname, $supports, $version, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;

            $printed[$name] = true;
        }

        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managemediaplayers::class, \admin_setting_managemediaplayers::class);
