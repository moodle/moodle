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

namespace core_admin\table;

use context_system;
use core_plugin_manager;
use core_table\dynamic as dynamic_table;
use flexible_table;
use html_writer;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/tablelib.php");

/**
 * Plugin Management table.
 *
 * @package    core_admin
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plugin_management_table extends flexible_table implements dynamic_table {

    /** @var \core\plugininfo\base[] The plugin list */
    protected array $plugins = [];

    /** @var int The number of enabled plugins of this type */
    protected int $enabledplugincount = 0;

    /** @var core_plugin_manager */
    protected core_plugin_manager $pluginmanager;

    /** @var string The plugininfo class for this plugintype */
    protected string $plugininfoclass;

    public function __construct() {
        global $CFG;

        parent::__construct($this->get_table_id());
        require_once($CFG->libdir . '/adminlib.php');

        // Fetch the plugininfo class.
        $this->pluginmanager = core_plugin_manager::instance();
        $this->plugininfoclass = $this->pluginmanager::resolve_plugininfo_class($this->get_plugintype());

        $this->guess_base_url();

        $this->plugins = $this->get_sorted_plugins();
        $this->enabledplugincount = count(array_filter($this->plugins, function ($plugin) {
            return $plugin->is_enabled();
        }));

        $this->setup_column_configuration();
        $this->set_filterset(new plugin_management_table_filterset());
        $this->setup();
    }

    /**
     * Get the list of sorted plugins.
     *
     * @return \core\plugininfo\base[]
     */
    protected function get_sorted_plugins(): array {
        if ($this->plugininfoclass::plugintype_supports_ordering()) {
            return $this->plugininfoclass::get_sorted_plugins();
        } else {
            $plugins = $this->pluginmanager->get_plugins_of_type($this->get_plugintype());
            return self::sort_plugins($plugins);
        }
    }

    /**
     * Sort the plugins list.
     *
     * Note: This only applies to plugins which do not support ordering.
     *
     * @param \core\plugininfo\base[] $plugins
     * @return \core\plugininfo\base[]
     */
    protected function sort_plugins(array $plugins): array {
        // The asort functions work by reference.
        \core_collator::asort_objects_by_property($plugins, 'displayname');

        return $plugins;
    }

    /**
     * Set up the column configuration for this table.
     */
    protected function setup_column_configuration(): void {
        $columnlist = $this->get_column_list();
        $this->define_columns(array_keys($columnlist));
        $this->define_headers(array_values($columnlist));

        $columnswithhelp = $this->get_columns_with_help();
        $columnhelp = array_map(function (string $column) use ($columnswithhelp): ?\renderable {
            if (array_key_exists($column, $columnswithhelp)) {
                return $columnswithhelp[$column];
            }

            return null;
        }, array_keys($columnlist));
        $this->define_help_for_headers($columnhelp);
    }

    /**
     * Set the standard order of the plugins.
     *
     * @param array $plugins
     * @return array
     */
    protected function order_plugins(array $plugins): array {
        uasort($plugins, function ($a, $b) {
            if ($a->is_enabled() && !$b->is_enabled()) {
                return -1;
            } else if (!$a->is_enabled() && $b->is_enabled()) {
                return 1;
            }
            return strnatcasecmp($a->name, $b->name);
        });

        return $plugins;
    }

    /**
     * Get the plugintype for this table.
     *
     * @return string
     */
    abstract protected function get_plugintype(): string;

    /**
     * Get the action URL for this table.
     *
     * The action URL is used to perform all actions when JS is not available.
     *
     * @param array $params
     * @return moodle_url
     */
    abstract protected function get_action_url(array $params = []): moodle_url;

    /**
     * Provide a default implementation for guessing the base URL from the action URL.
     */
    public function guess_base_url(): void {
        $this->define_baseurl($this->get_action_url());
    }

    /**
     * Get the web service method used to toggle state.
     *
     * @return null|string
     */
    protected function get_toggle_service(): ?string {
        return 'core_admin_set_plugin_state';
    }

    /**
     * Get the web service method used to order plugins.
     *
     * @return null|string
     */
    protected function get_sortorder_service(): ?string {
        return 'core_admin_set_plugin_order';
    }

    /**
     * Get the ID of the table.
     *
     * @return string
     */
    protected function get_table_id(): string {
        return 'plugin_management_table-' . $this->get_plugintype();
    }

    /**
     * Get a list of the column titles
     * @return string[]
     */
    protected function get_column_list(): array {
        $columns = [
            'name' => get_string('name', 'core'),
            'version' => get_string('version', 'core'),
        ];

        if ($this->supports_disabling()) {
            $columns['enabled'] = get_string('pluginenabled', 'core_plugin');
        }

        if ($this->supports_ordering()) {
            $columns['order'] = get_string('order', 'core');
        }

        $columns['settings'] = get_string('settings', 'core');
        $columns['uninstall'] = get_string('uninstallplugin', 'core_admin');

        return $columns;
    }

    protected function get_columns_with_help(): array {
        return [];
    }

    /**
     * Get the context for this table.
     *
     * @return context_system
     */
    public function get_context(): context_system {
        return context_system::instance();
    }

    /**
     * Get the table content.
     */
    public function get_content(): string {
        ob_start();
        $this->out();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Print the table.
     */
    public function out(): void {
        $plugintype = $this->get_plugintype();
        foreach ($this->plugins as $plugininfo) {
            $plugin = "{$plugintype}_{$plugininfo->name}";
            $rowdata = (object) [
                'plugin' => $plugin,
                'plugininfo' => $plugininfo,
                'name' => $plugininfo->displayname,
                'version' => $plugininfo->versiondb,
            ];
            $this->add_data_keyed(
                $this->format_row($rowdata),
                $this->get_row_class($rowdata)
            );
        }

        $this->finish_output(false);
    }

    /**
     * This table is not downloadable.
     * @param bool $downloadable
     * @return bool
     */
    // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
    public function is_downloadable($downloadable = null): bool {
        return false;
    }

    /**
     * Show the name column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_name(stdClass $row): string {
        $status = $row->plugininfo->get_status();
        if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
            return html_writer::span(
                get_string('pluginmissingfromdisk', 'core', $row->plugininfo),
                'notifyproblem'
            );
        }

        if ($row->plugininfo->is_installed_and_upgraded()) {
            return $row->plugininfo->displayname;
        }

        return html_writer::span(
            $row->plugininfo->displayname,
            'notifyproblem'
        );
    }

    /**
     * Show the enable/disable column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_enabled(stdClass $row): string {
        global $OUTPUT;

        $enabled = $row->plugininfo->is_enabled();
        if ($enabled) {
            $labelstr = get_string('disableplugin', 'core_admin', $row->plugininfo->displayname);
        } else {
            $labelstr = get_string('enableplugin', 'core_admin', $row->plugininfo->displayname);
        }

        $params = [
            'id' => 'admin-toggle-' . $row->plugininfo->name,
            'checked' => $enabled,
            'dataattributes' => [
                'name' => 'id',
                'value' => $row->plugininfo->name,
                'toggle-method' => $this->get_toggle_service(),
                'action' => 'togglestate',
                'plugin' => $row->plugin,
                'state' => $enabled ? 1 : 0,
            ],
            'title' => $labelstr,
            'label' => $labelstr,
            'labelclasses' => 'visually-hidden',
        ];

        return $OUTPUT->render_from_template('core_admin/setting_configtoggle', $params);
    }

    protected function col_order(stdClass $row): string {
        global $OUTPUT;

        if (!$this->supports_ordering()) {
            return '';
        }

        if (!$row->plugininfo->is_enabled()) {
            return '';
        }

        if ($this->enabledplugincount <= 1) {
            // There is only one row.
            return '';
        }

        $hasup = true;
        $hasdown = true;

        if (empty($this->currentrow)) {
            // This is the top row.
            $hasup = false;
        }

        if ($this->currentrow === ($this->enabledplugincount - 1)) {
            // This is the last row.
            $hasdown = false;
        }

        if ($this->supports_ordering()) {
            $dataattributes = [
                'data-method' => $this->get_sortorder_service(),
                'data-action' => 'move',
                'data-plugin' => $row->plugin,
            ];
        } else {
            $dataattributes = [];
        }

        if ($hasup) {
            $upicon = html_writer::link(
                $this->get_action_url([
                    'sesskey' => sesskey(),
                    'action' => 'up',
                    'plugin' => $row->plugininfo->name,
                ]),
                $OUTPUT->pix_icon('t/up', get_string('moveup')),
                array_merge($dataattributes, ['data-direction' => 'up']),
            );
        } else {
            $upicon = $OUTPUT->spacer();
        }

        if ($hasdown) {
            $downicon = html_writer::link(
                $this->get_action_url([
                    'sesskey' => sesskey(),
                    'action' => 'down',
                    'plugin' => $row->plugininfo->name,
                ]),
                $OUTPUT->pix_icon('t/down', get_string('movedown')),
                array_merge($dataattributes, ['data-direction' => 'down']),
            );
        } else {
            $downicon = $OUTPUT->spacer();
        }

        // For now just add the up/down icons.
        return html_writer::span($upicon . $downicon);
    }

    /**
     * Show the settings column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_settings(stdClass $row): string {
        if ($settingsurl = $row->plugininfo->get_settings_url()) {
            return html_writer::link($settingsurl, get_string('settings'));
        }

        return '';
    }

    /**
     * Show the Uninstall column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_uninstall(stdClass $row): string {
        $status = $row->plugininfo->get_status();

        if ($status === core_plugin_manager::PLUGIN_STATUS_NEW) {
            return get_string('status_new', 'core_plugin');
        }

        if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
            $uninstall = get_string('status_missing', 'core_plugin') . '<br/>';
        } else {
            $uninstall = '';
        }

        if ($uninstallurl = $this->pluginmanager->get_uninstall_url($row->plugin)) {
            $uninstall .= html_writer::link($uninstallurl, get_string('uninstallplugin', 'core_admin'));
        }

        return $uninstall;
    }

    /**
     * Get the JS module used to manage this table.
     *
     * This should be a class which extends 'core_admin/plugin_management_table'.
     *
     * @return string
     */
    protected function get_table_js_module(): string {
        return 'core_admin/plugin_management_table';
    }

    /**
     * Add JS specific to this implementation.
     *
     * @return string
     */
    protected function get_dynamic_table_html_end(): string {
        global $PAGE;

        $PAGE->requires->js_call_amd($this->get_table_js_module(), 'init');
        return parent::get_dynamic_table_html_end();
    }

    /**
     * Get any class to add to the row.
     *
     * @param mixed $row
     * @return string
     */
    protected function get_row_class($row): string {
        $plugininfo = $row->plugininfo;
        if ($plugininfo->get_status() === core_plugin_manager::PLUGIN_STATUS_MISSING) {
            return '';
        }

        if (!$plugininfo->is_enabled()) {
            return 'dimmed_text';
        }
        return '';
    }

    public static function get_filterset_class(): string {
        return self::class . '_filterset';
    }

    /**
     * Whether this plugin type supports the disabling of plugins.
     *
     * @return bool
     */
    protected function supports_disabling(): bool {
        return $this->plugininfoclass::plugintype_supports_disabling();
    }

    /**
     * Whether this table should show ordering fields.
     *
     * @return bool
     */
    protected function supports_ordering(): bool {
        return $this->plugininfoclass::plugintype_supports_ordering();
    }

    /**
     * Check if the user has the capability to access this table.
     *
     * Default implementation for plugin management tables is to require 'moodle/site:config' capability
     *
     * @return bool Return true if capability check passed.
     */
    public function has_capability(): bool {
        return has_capability('moodle/site:config', $this->get_context());
    }
}
