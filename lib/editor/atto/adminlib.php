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
 * Atto admin setting stuff.
 *
 * @package   editor_atto
 * @copyright 2014 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting for toolbar.
 *
 * @package    editor_atto
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_atto_toolbar_setting extends admin_setting_configtextarea {

    /**
     * Validate data.
     *
     * This ensures that:
     * - Plugins are only used once,
     * - Group names are unique,
     * - Lines match: group = plugin[, plugin[, plugin ...]],
     * - There are some groups and plugins defined,
     * - The plugins used are installed.
     *
     * @param string $data
     * @return mixed True on success, else error message.
     */
    public function validate($data) {
        $result = parent::validate($data);
        if ($result !== true) {
            return $result;
        }

        $lines = explode("\n", $data);
        $groups = array();
        $plugins = array();

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $matches = array();
            if (!preg_match('/^\s*([a-z0-9]+)\s*=\s*([a-z0-9]+(\s*,\s*[a-z0-9]+)*)+\s*$/', $line, $matches)) {
                $result = get_string('errorcannotparseline', 'editor_atto', $line);
                break;
            }

            $group = $matches[1];
            if (isset($groups[$group])) {
                $result = get_string('errorgroupisusedtwice', 'editor_atto', $group);
                break;
            }
            $groups[$group] = true;

            $lineplugins = array_map('trim', explode(',', $matches[2]));
            foreach ($lineplugins as $plugin) {
                if (isset($plugins[$plugin])) {
                    $result = get_string('errorpluginisusedtwice', 'editor_atto', $plugin);
                    break 2;
                } else if (!core_component::get_component_directory('atto_' . $plugin)) {
                    $result = get_string('errorpluginnotfound', 'editor_atto', $plugin);
                    break 2;
                }
                $plugins[$plugin] = true;
            }
        }

        // We did not find any groups or plugins.
        if (empty($groups) || empty($plugins)) {
            $result = get_string('errornopluginsorgroupsfound', 'editor_atto');
        }

        return $result;
    }

}

/**
 * Special class for Atto plugins administration.
 *
 * @package   editor_atto
 * @copyright 2014 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class atto_subplugins_settings extends admin_setting {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('attosubplugins', get_string('subplugintype_atto_plural', 'editor_atto'), '', '');
    }

    /**
     * Returns current value of this setting.
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Returns default setting if exists.
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Store new setting.
     * Always returns '', does not write anything.
     *
     * @param string $data string or array, must not be NULL.
     * @return string Always returns ''.
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available subplugins.
     *
     * @param string $query The string to search for.
     * @return bool Returns true if found, false if not.
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $subplugins = core_component::get_plugin_list('atto');
        foreach ($subplugins as $name => $dir) {
            if (stripos($name, $query) !== false) {
                return true;
            }

            $namestr = get_string('pluginname', 'atto_' . $name);
            if (strpos(core_text::strtolower($namestr), core_text::strtolower($query)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control.
     *
     * @param mixed $data Unused.
     * @param string $query
     * @return string highlight.
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT, $PAGE;
        require_once($CFG->libdir . "/editorlib.php");
        require_once(__DIR__ . '/lib.php');
        $pluginmanager = core_plugin_manager::instance();

        // Display strings.
        $strtoolbarconfig = get_string('toolbarconfig', 'editor_atto');
        $strname = get_string('name');
        $strsettings = get_string('settings');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strversion = get_string('version');

        $subplugins = core_component::get_plugin_list('atto');

        $return = $OUTPUT->heading(get_string('subplugintype_atto_plural', 'editor_atto'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox attosubplugins');

        $table = new html_table();
        $table->head  = array($strname, $strversion, $strtoolbarconfig, $strsettings, $struninstall);
        $table->align = array('left', 'left', 'center', 'center', 'center', 'center');
        $table->data  = array();
        $table->attributes['class'] = 'admintable generaltable';

        $corepluginicons = array(
            'accessibilitychecker' => $OUTPUT->pix_url('e/visual_blocks', 'core'),
            'accessibilityhelper' => $OUTPUT->pix_url('e/visual_aid'),
            'align' => array(
                $OUTPUT->pix_url('e/align_left', 'core'),
                $OUTPUT->pix_url('e/align_center', 'core'), $OUTPUT->pix_url('e/align_right', 'core')
            ),
            'backcolor' => $OUTPUT->pix_url('e/text_highlight', 'core'),
            'bold' => $OUTPUT->pix_url('e/bold', 'core'),
            'charmap' => $OUTPUT->pix_url('e/special_character', 'core'),
            'clear' => $OUTPUT->pix_url('e/clear_formatting', 'core'),
            'emoticon' => $OUTPUT->pix_url('e/emoticons', 'core'),
            'equation' => $OUTPUT->pix_url('e/math', 'core'),
            'fontcolor' => $OUTPUT->pix_url('e/text_color', 'core'),
            'html' => $OUTPUT->pix_url('e/source_code', 'core'),
            'image' => $OUTPUT->pix_url('e/insert_edit_image', 'core'),
            'indent' => array(
                $OUTPUT->pix_url('e/increase_indent', 'core'),
                $OUTPUT->pix_url('e/decrease_indent', 'core'),
            ),
            'italic' => $OUTPUT->pix_url('e/italic', 'core'),
            'link' => $OUTPUT->pix_url('e/insert_edit_link', 'core'),
            'managefiles' => $OUTPUT->pix_url('e/manage_files', 'core'),
            'media' => $OUTPUT->pix_url('e/insert_edit_video', 'core'),
            'orderedlist' => $OUTPUT->pix_url('e/numbered_list', 'core'),
            'rtl' => array($OUTPUT->pix_url('e/left_to_right', 'core'),
                $OUTPUT->pix_url('e/right_to_left', 'core')),
            'strike' => $OUTPUT->pix_url('e/strikethrough', 'core'),
            'subscript' => $OUTPUT->pix_url('e/subscript', 'core'),
            'superscript' => $OUTPUT->pix_url('e/superscript', 'core'),
            'table' => $OUTPUT->pix_url('e/table', 'core'),
            'title' => $OUTPUT->pix_url('e/styleprops', 'core'),
            'underline' => $OUTPUT->pix_url('e/underline', 'core'),
            'undo' => array($OUTPUT->pix_url('e/undo', 'core'), $OUTPUT->pix_url('e/redo', 'core')),
            'unlink' => $OUTPUT->pix_url('e/remove_link', 'core'),
            'unorderedlist' => $OUTPUT->pix_url('e/bullet_list', 'core')
        );

        // Iterate through subplugins.
        foreach ($subplugins as $name => $dir) {
            $namestr = get_string('pluginname', 'atto_' . $name);
            $version = get_config('atto_' . $name, 'version');
            if ($version === false) {
                $version = '';
            }
            $plugininfo = $pluginmanager->get_plugin_info('atto_' . $name);

            $toolbarconfig = $name;

            $displayname = $namestr;

            // Check if there is a pix folder in the atto plugin.
            if ($PAGE->theme->resolve_image_location('icon', 'atto_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'atto_' . $name, array('class' => 'icon pluginicon'));
            } else {
                // Attempt to find out the icons for core atto plugins.
                if (array_key_exists($name, $corepluginicons)) {
                    // It's a core plugin.
                    $icons = array();
                    if (!is_array($corepluginicons[$name])) {
                        $icons[] = $corepluginicons[$name];
                    } else {
                        $icons = $corepluginicons[$name];
                    }
                    $icon = '';
                    foreach ($icons as $anicon) {
                        $icon .= html_writer::empty_tag('img', array('src' => $anicon,
                            "class" => "pluginicon", "alt" => $displayname));
                    }
                } else {
                    // No icon found.
                    $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
                }
            }
            $displayname  = $icon . ' ' . $displayname;

            // Add settings link.
            if (!$version) {
                $settings = '';
            } else if ($url = $plugininfo->get_settings_url()) {
                $settings = html_writer::link($url, $strsettings);
            } else {
                $settings = '';
            }

            // Add uninstall info.
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('atto_' . $name, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            // Add a row to the table.
            $row = new html_table_row(array($displayname, $version, $toolbarconfig, $settings, $uninstall));
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $return .= html_writer::tag('p', get_string('tablenosave', 'admin'));
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

