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

namespace core_ai\table;

use core_ai\manager;
use core_table\dynamic as dynamic_table;
use flexible_table;
use moodle_url;
use stdClass;

/**
 * Table to manage AI actions used in placement plugins.
 *
 * @package core_ai
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aiplacement_action_management_table extends flexible_table implements dynamic_table {
    /** @var string The name of the plugin these actions related too */
    protected string $pluginname;

    /** @var array The list of actions this manager covers */
    protected array $actions;

    /** @var \core_ai\manager The AI manager */
    protected \core_ai\manager $manager;

    /**
     * Constructor.
     *
     * @param string $uniqueid The table unique id.
     */
    public function __construct(string $uniqueid) {
        // Parse the unique id and get the plugin name.
        $parseuniqueid = explode('-', $uniqueid);
        $pluginname = end($parseuniqueid);
        $this->pluginname = $pluginname;

        // Get the list of actions that this provider supports.
        $this->actions = manager::get_supported_actions($this->pluginname);

        parent::__construct($this->get_table_id());

        $this->setup_column_configuration();
        $this->set_filterset(new aiplacement_action_management_table_filterset());
        $this->manager = \core\di::get(manager::class);
        $this->setup();
    }

    #[\Override]
    public function get_context(): \context_system {
        return \context_system::instance();
    }

    /**
     * Set up the column configuration for this table.
     */
    protected function setup_column_configuration(): void {
        $columnlist = $this->get_column_list();
        $this->define_columns(array_keys($columnlist));
        $this->define_headers(array_values($columnlist));
    }

    /**
     * Get the ID of the table.
     *
     * @return string
     */
    protected function get_table_id(): string {
        return "aiplacementaction_management_table-{$this->pluginname}";
    }

    /**
     * Get the web service method used to toggle state.
     *
     * @return null|string
     */
    protected function get_toggle_service(): ?string {
        return 'core_ai_set_action';
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

    #[\Override]
    protected function get_dynamic_table_html_end(): string {
        global $PAGE;

        $PAGE->requires->js_call_amd($this->get_table_js_module(), 'init');
        return parent::get_dynamic_table_html_end();
    }

    /**
     * Get a list of the column titles
     * @return string[]
     */
    protected function get_column_list(): array {
        return [
            'namedesc' => get_string('name', 'core'),
            'enabled' => get_string('pluginenabled', 'core_plugin'),
        ];
    }

    /**
     * Show the name column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_namedesc(stdClass $row): string {
        global $OUTPUT;

        $params = [
            'name' => $row->action::get_name(),
            'description' => $row->action::get_description(),
        ];
        $output = $OUTPUT->render_from_template('core_admin/table/namedesc', $params);

        if (!$this->manager->is_action_available($row->action)) {
            $providerurl = new moodle_url('/admin/settings.php', ['section' => 'aiprovider']);
            $output .= $OUTPUT->render_from_template('core_ai/admin_noproviders', [
                'providerurl' => $providerurl->out(),
            ]);
        }

        return $output;
    }

    /**
     * Show the enable/disable column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_enabled(stdClass $row): string {
        global $OUTPUT;

        $enabled = $row->enabled;
        $identifier = $enabled ? 'disableplugin' : 'enableplugin';
        $labelstr = get_string($identifier, 'core_admin', $row->action::get_name());

        $params = [
            'id' => 'admin-toggle-' . $row->action::get_basename(),
            'checked' => $enabled,
            'dataattributes' => [
                'name' => 'id',
                'value' => $row->action,
                'toggle-method' => $this->get_toggle_service(),
                'action' => 'togglestate',
                'plugin' => $this->pluginname . "-" . $row->action::get_basename(),
                'state' => $enabled ? 1 : 0,
            ],
            'title' => $labelstr,
            'label' => $labelstr,
            'labelclasses' => 'visually-hidden',
        ];

        return $OUTPUT->render_from_template('core_admin/setting_configtoggle', $params);
    }

    /**
     * Get any class to add to the row.
     *
     * @param mixed $row
     * @return string
     */
    protected function get_row_class($row): string {
        if (!$row->enabled) {
            return 'dimmed_text';
        }
        return '';
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
        foreach ($this->actions as $actionclass) {
            // Construct the row data.
            $rowdata = (object) [
                'action' => $actionclass,
                'enabled' => $this->manager->is_action_enabled($this->pluginname, $actionclass),
            ];
            $this->add_data_keyed(
                $this->format_row($rowdata),
                $this->get_row_class($rowdata)
            );
        }

        $this->finish_output(false);
    }

    #[\Override]
    public function is_downloadable($downloadable = null): bool {
        return false;
    }

    #[\Override]
    public function guess_base_url(): void {
        $url = new moodle_url('/');
        $this->define_baseurl($url);
    }

    #[\Override]
    public function has_capability(): bool {
        return has_capability('moodle/site:config', $this->get_context());
    }
}
