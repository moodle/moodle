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
 * Renderer for core_admin subsystem
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/pluginlib.php');

/**
 * Standard HTML output renderer for core_admin subsystem
 */
class core_admin_renderer extends plugin_renderer_base {

    /**
     * Displays all known plugins and information about their installation or upgrade
     *
     * This default implementation renders all plugins into one big table. The rendering
     * options support:
     *     (bool)full = false: whether to display up-to-date plugins, too
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @param array $options rendering options
     * @return string HTML code
     */
    public function plugins_check(plugin_manager $pluginman, array $options = null) {
        global $CFG;
        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        if (empty($options)) {
            $options = array(
                'full' => false,
            );
        }

        $table = new html_table();
        $table->id = 'plugins-check';
        $table->head = array(
            get_string('displayname', 'core_plugin'),
            get_string('rootdir', 'core_plugin'),
            get_string('source', 'core_plugin'),
            get_string('versiondb', 'core_plugin'),
            get_string('versiondisk', 'core_plugin'),
            get_string('requires', 'core_plugin'),
            get_string('status', 'core_plugin'),
        );
        $table->colclasses = array(
            'displayname', 'rootdir', 'source', 'versiondb', 'versiondisk', 'requires', 'status',
        );
        $table->data = array();

        $numofhighlighted = array();    // number of highlighted rows per this subsection

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = count($table->head);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;

            $numofhighlighted[$type] = 0;

            if (empty($plugins) and $options['full']) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = count($table->head);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $header;
                $table->data[] = $row;
                continue;
            }

            $plugintyperows = array();

            foreach ($plugins as $name => $plugin) {
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'smallicon pluginicon'));
                } else {
                    $icon = $this->output->pix_icon('spacer', '', 'moodle', array('class' => 'smallicon pluginicon noicon'));
                }
                $displayname  = $icon . ' ' . $plugin->displayname;
                $displayname = new html_table_cell($displayname);

                $rootdir = new html_table_cell($plugin->get_dir());

                if ($isstandard = $plugin->is_standard()) {
                    $row->attributes['class'] .= ' standard';
                    $source = new html_table_cell(get_string('sourcestd', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' extension';
                    $source = new html_table_cell(get_string('sourceext', 'core_plugin'));
                }

                $versiondb = new html_table_cell($plugin->versiondb);
                $versiondisk = new html_table_cell($plugin->versiondisk);

                $statuscode = $plugin->get_status();
                $row->attributes['class'] .= ' status-' . $statuscode;

                $status = new html_table_cell(get_string('status_' . $statuscode, 'core_plugin'));

                $requires = new html_table_cell($this->required_column($plugin, $pluginman));

                $statusisboring = in_array($statuscode, array(
                        plugin_manager::PLUGIN_STATUS_NODB, plugin_manager::PLUGIN_STATUS_UPTODATE));
                $dependanciesok = $pluginman->are_dependancies_satisfied(
                        $plugin->get_other_required_plugins());
                if ($isstandard and $statusisboring and $dependanciesok) {
                    if (empty($options['full'])) {
                        continue;
                    }
                } else {
                    $numofhighlighted[$type]++;
                }

                $row->cells = array($displayname, $rootdir, $source,
                    $versiondb, $versiondisk, $requires, $status);
                $plugintyperows[] = $row;
            }

            if (empty($numofhighlighted[$type]) and empty($options['full'])) {
                continue;
            }

            $table->data[] = $header;
            $table->data = array_merge($table->data, $plugintyperows);
        }

        $sumofhighlighted = array_sum($numofhighlighted);

        if ($sumofhighlighted == 0) {
            $out  = $this->output->container_start('nonehighlighted', 'plugins-check-info');
            $out .= $this->output->heading(get_string('nonehighlighted', 'core_plugin'));
            if (empty($options['full'])) {
                $out .= html_writer::link(new moodle_url('/admin/index.php',
                    array('confirmupgrade' => 1, 'confirmrelease' => 1, 'showallplugins' => 1)),
                    get_string('nonehighlightedinfo', 'core_plugin'));
            }
            $out .= $this->output->container_end();

        } else {
            $out  = $this->output->container_start('somehighlighted', 'plugins-check-info');
            $out .= $this->output->heading(get_string('somehighlighted', 'core_plugin', $sumofhighlighted));
            if (empty($options['full'])) {
                $out .= html_writer::link(new moodle_url('/admin/index.php',
                    array('confirmupgrade' => 1, 'confirmrelease' => 1, 'showallplugins' => 1)),
                    get_string('somehighlightedinfo', 'core_plugin'));
            }
            $out .= $this->output->container_end();
        }

        if ($sumofhighlighted > 0 or $options['full']) {
            $out .= html_writer::table($table);
        }

        return $out;
    }

    /**
     * Formats the information that needs to go in the 'Requires' column.
     * @param plugin_information $plugin the plugin we are rendering the row for.
     * @param plugin_manager $pluginman provides data on all the plugins.
     */
    protected function required_column($plugin, $pluginman) {
        global $CFG;
        $requires = array();

        if (!empty($plugin->versionrequires)) {
            if ($plugin->versionrequires <= $CFG->version) {
                $class = 'requires-ok';
            } else {
                $class = 'requires-failed';
            }
            $requires[] = html_writer::tag('li',
                get_string('moodleversion', 'core_plugin', $plugin->versionrequires),
                array('class' => $class));
        }

        foreach ($plugin->get_other_required_plugins() as $component => $requiredversion) {
            $ok = true;
            $otherplugin = $pluginman->get_plugin_info($component);

            if (is_null($otherplugin)) {
                $ok = false;
            }
            if ($requiredversion != ANY_VERSION and $otherplugin->versiondb < $requiredversion) {
                $ok = false;
            }

            if ($ok) {
                $class = 'requires-ok';
            } else {
                $class = 'requires-failed';
            }

            if ($requiredversion != ANY_VERSION) {
                $str = 'otherpluginversion';
            } else {
                $str = 'otherplugin';
            }
            $requires[] = html_writer::tag('li',
                    get_string($str, 'core_plugin',
                            array('component' => $component, 'version' => $requiredversion)),
                    array('class' => $class));
        }

        if (!$requires) {
            return '';
        }
        return html_writer::tag('ul', implode("\n", $requires));
    }

    /**
     * Displays all known plugins and links to manage them
     *
     * This default implementation renders all plugins into one big table.
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @return string HTML code
     */
    public function plugins_control_panel(plugin_manager $pluginman) {
        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        $table = new html_table();
        $table->id = 'plugins-control-panel';
        $table->head = array(
            get_string('displayname', 'core_plugin'),
            get_string('systemname', 'core_plugin'),
            get_string('source', 'core_plugin'),
            get_string('version', 'core_plugin'),
            get_string('availability', 'core_plugin'),
            get_string('settings', 'core_plugin'),
            get_string('uninstall','core_plugin'),
        );
        $table->colclasses = array(
            'displayname', 'systemname', 'source', 'version', 'availability', 'settings', 'uninstall',
        );

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = count($table->head);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;
            $table->data[] = $header;

            if (empty($plugins)) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = count($table->head);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $row;
                continue;
            }

            foreach ($plugins as $name => $plugin) {
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'smallicon pluginicon'));
                } else {
                    $icon = $this->output->pix_icon('spacer', '', 'moodle', array('class' => 'smallicon pluginicon noicon'));
                }
                if ($plugin->get_status() === plugin_manager::PLUGIN_STATUS_MISSING) {
                    $msg = html_writer::tag('span', get_string('status_missing', 'core_plugin'), array('class' => 'notifyproblem'));
                    $row->attributes['class'] .= ' missingfromdisk';
                } else {
                    $msg = '';
                }
                $displayname  = $icon . ' ' . $plugin->displayname . ' ' . $msg;
                $displayname = new html_table_cell($displayname);

                $systemname = new html_table_cell($plugin->type . '_' . $plugin->name);

                if ($plugin->is_standard()) {
                    $row->attributes['class'] .= ' standard';
                    $source = new html_table_cell(get_string('sourcestd', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' extension';
                    $source = new html_table_cell(get_string('sourceext', 'core_plugin'));
                }

                $version = new html_table_cell($plugin->versiondb);

                $isenabled = $plugin->is_enabled();
                if (is_null($isenabled)) {
                    $availability = new html_table_cell('');
                } else if ($isenabled) {
                    $row->attributes['class'] .= ' enabled';
                    $icon = $this->output->pix_icon('i/hide', get_string('pluginenabled', 'core_plugin'));
                    $availability = new html_table_cell($icon . ' ' . get_string('pluginenabled', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' disabled';
                    $icon = $this->output->pix_icon('i/show', get_string('plugindisabled', 'core_plugin'));
                    $availability = new html_table_cell($icon . ' ' . get_string('plugindisabled', 'core_plugin'));
                }

                $settingsurl = $plugin->get_settings_url();
                if (is_null($settingsurl)) {
                    $settings = new html_table_cell('');
                } else {
                    $settings = html_writer::link($settingsurl, get_string('settings', 'core_plugin'));
                    $settings = new html_table_cell($settings);
                }

                $uninstallurl = $plugin->get_uninstall_url();
                if (is_null($uninstallurl)) {
                    $uninstall = new html_table_cell('');
                } else {
                    $uninstall = html_writer::link($uninstallurl, get_string('uninstall', 'core_plugin'));
                    $uninstall = new html_table_cell($uninstall);
                }

                $row->cells = array(
                    $displayname, $systemname, $source, $version, $availability, $settings, $uninstall
                );
                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
    }
}
