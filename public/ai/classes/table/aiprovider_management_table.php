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

use context_system;
use core_table\dynamic as dynamic_table;
use flexible_table;
use moodle_url;
use html_writer;

/**
 * Table to manage AI provider plugins.
 *
 * @package core_ai
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aiprovider_management_table extends flexible_table implements dynamic_table {
    /**
     * @var array $aiproviders List of configured provider instances.
     */
    protected array $aiproviders = [];

    /** @var int The number of enabled provider instances. */
    protected int $enabledprovidercount = 0;

    /**
     * Constructor for the AI provider table.
     */
    public function __construct() {
        parent::__construct($this->get_table_id());

        $this->aiproviders = $this->get_providers();
        $this->setup_column_configuration();
        $this->set_filterset(new aiprovider_management_table_filterset());
        $this->setup();
        $tableclasses = $this->attributes['class'] . ' ' . $this->get_table_id();
        $this->set_attribute('class', $tableclasses);

        $this->enabledprovidercount = count(array_filter($this->aiproviders, function ($provider) {
            return $provider->enabled;
        }));
    }

    /**
     * Get the plugin type for the table.
     *
     * @return string
     * @deprecated since 5.0
     */
    #[\core\attribute\deprecated(replacement: null, since: '5.0', mdl: 'MDL-82977')]
    protected function get_plugintype(): string {
        \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
        return 'aiprovider';
    }

    #[\Override]
    public function get_context(): context_system {
        return context_system::instance();
    }

    #[\Override]
    public function has_capability(): bool {
        return has_capability('moodle/site:config', $this->get_context());
    }

    /**
     * Get the table id for the table.
     *
     * @return string
     */
    protected function get_table_id(): string {
        return 'aiproviders_table';
    }

    /**
     * Get the js module needed for the table.
     *
     * This module can include table specific ajax calls etc.
     *
     * @return string
     */
    protected function get_table_js_module(): string {
        return 'core_ai/aiprovider_instance_management_table';
    }

    /**
     * Webservice for toggle.
     *
     * @return string
     */
    protected function get_toggle_service(): string {
        return 'core_ai_set_provider_status';
    }

    /**
     * Webservice for delete.
     *
     * @return string
     */
    protected function get_delete_service(): string {
        return 'core_ai_delete_provider_instance';
    }

    /**
     * Get the action URL for the table.
     *
     * @param array $params The params to pass to the URL.
     * @return moodle_url
     * @deprecated since 5.0
     */
    #[\core\attribute\deprecated(replacement: null, since: '5.0', mdl: 'MDL-82977')]
    protected function get_action_url(array $params = []): moodle_url {
        \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
        return new moodle_url('/admin/ai.php', $params);
    }

    #[\Override]
    protected function get_dynamic_table_html_end(): string {
        global $PAGE;

        $PAGE->requires->js_call_amd($this->get_table_js_module(), 'init');
        return parent::get_dynamic_table_html_end();
    }

    /**
     * Get the configured ai providers from the manager.
     *
     * @return array
     */
    protected function get_providers(): array {
        return \core\di::get(\core_ai\manager::class)->get_sorted_providers();
    }

    /**
     * Setup the column configs for the table.
     */
    protected function setup_column_configuration(): void {
        $columnlist = $this->get_column_list();
        $this->define_columns(array_keys($columnlist));
        $this->define_headers(array_values($columnlist));
    }

    #[\Override]
    public function guess_base_url(): void {
        $this->define_baseurl(new moodle_url('/admin/ai.php'));
    }

    /**
     * Get the column list for the table.
     *
     * @return array
     */
    protected function get_column_list(): array {
        return [
            'name' => get_string('name'),
            'provider' => get_string('provider', 'core_ai'),
            'enabled' => get_string('pluginenabled', 'core_plugin'),
            'order' => get_string('order', 'core'),
            'settings' => get_string('settings', 'core'),
            'delete' => get_string('delete'),
        ];
    }

    /**
     * Get the content of the table.
     *
     * @return string
     */
    public function get_content(): string {
        ob_start();
        $this->out();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Add the row data for the table.
     */
    public function out(): void {
        foreach ($this->aiproviders as $provider) {
            $rowdata = (object) [
                'id' => $provider->id,
                'name' => $provider->name,
                'provider' => $provider->provider,
                'enabled' => (int)$provider->enabled,
            ];
            $this->add_data_keyed(
                $this->format_row($rowdata),
                $this->get_row_class($rowdata)
            );
        }

        $this->finish_output(false);
    }

    /**
     * Get the class for row whether is dimmed or not according to enabled or disabled.
     *
     * @param \stdClass $row The row object
     * @return string
     */
    protected function get_row_class(\stdClass $row): string {
        if ($row->enabled) {
            return '';
        }
        return 'dimmed_text';
    }

    /**
     * The column for the provider instance Name.
     *
     * @param \stdClass $row The row object
     * @return string
     */
    public function col_name(\stdClass $row): string {
        return $row->name;
    }

    /**
     * The column for the provider plugin name.
     *
     * @param \stdClass $row The row object
     * @return string
     */
    public function col_provider(\stdClass $row): string {
        $component = \core\component::get_component_from_classname($row->provider);
        return get_string('pluginname', $component);
    }

    /**
     * The column for enabled or disabled status of the provider instance.
     *
     * @param \stdClass $row The row object
     * @return string
     */
    public function col_enabled(\stdClass $row): string {
        global $OUTPUT;

        $enabled = $row->enabled;
        if ($enabled) {
            $labelstr = get_string('disableplugin', 'core_admin', $row->name);
        } else {
            $labelstr = get_string('enableplugin', 'core_admin', $row->name);
        }

        $params = [
            'id' => 'ai-provider-toggle-' . $row->id,
            'checked' => $enabled,
            'dataattributes' => [
                'name' => 'id',
                'value' => $row->provider,
                'toggle-method' => $this->get_toggle_service(),
                'action' => 'togglestate',
                'plugin' => $row->id, // Set plugin attribute to provider ID.
                'state' => $enabled ? 1 : 0,
            ],
            'title' => $labelstr,
            'label' => $labelstr,
            'labelclasses' => 'visually-hidden',
        ];

        return $OUTPUT->render_from_template('core_admin/setting_configtoggle', $params);
    }

    /**
     * The column to show the settings of the provider instance.
     *
     * @param \stdClass $row The row object.
     * @return string
     */
    public function col_settings(\stdClass $row): string {
        $settingsurl = new moodle_url('/ai/configure.php', ['id' => $row->id]);
        return \html_writer::link($settingsurl, get_string('settings'));

    }

    /**
     * The column to show the delete action for the provider instance.
     *
     * @param \stdClass $row The row object.
     * @return string
     */
    public function col_delete(\stdClass $row): string {
        global $OUTPUT;

        // Render the delete button from the template.
        $component = \core\component::get_component_from_classname($row->provider);
        $provider = get_string('pluginname', $component);
        $params = [
            'id' => $row->id,
            'name' => $row->name,
            'provider' => $provider,
            'delete-method' => $this->get_delete_service(),
        ];
        return $OUTPUT->render_from_template('core_ai/admin_delete_provider', $params);
    }

    /**
     * Get the web service method used to order provider instances.
     *
     * @return null|string
     */
    protected function get_sortorder_service(): ?string {
        return 'core_ai_set_provider_order';
    }

    /**
     * Generates the HTML for the order column with up and down controls.
     *
     * @param \stdClass $row An object representing a row of data.
     * @return string The HTML string for the order controls, or an empty string if no controls are needed.
     */
    protected function col_order(\stdClass $row): string {
        global $OUTPUT;

        if (!$row->enabled) {
            return '';
        }

        if ($this->enabledprovidercount <= 1) {
            // There is only one row.
            return '';
        }

        $hasup = true;
        $hasdown = true;

        if (empty($this->currentrow)) {
            // This is the top row.
            $hasup = false;
        }

        if ($this->currentrow === ($this->enabledprovidercount - 1)) {
            // This is the last row.
            $hasdown = false;
        }

        $dataattributes = [
            'data-method' => $this->get_sortorder_service(),
            'data-action' => 'move',
            'data-plugin' => $row->id,
        ];

        if ($hasup) {
            $upicon = html_writer::link(
                $this->get_base_action_url([
                    'sesskey' => sesskey(),
                    'action' => \core\plugininfo\aiprovider::UP,
                    'id' => $row->id,
                ]),
                $OUTPUT->pix_icon(
                    pix: 't/up',
                    alt: '',
                ),
                array_merge($dataattributes, [
                    'data-direction' => \core\plugininfo\aiprovider::UP,
                    'role' => 'button',
                    'aria-label' => get_string('moveitemup', 'core', $row->name),
                    'title' => get_string('moveitemup', 'core', $row->name),
                    'class' => 'btn btn-link',
                ]),
            );
        } else {
            $upicon = '';
        }

        if ($hasdown) {
            $downicon = html_writer::link(
                $this->get_base_action_url([
                    'sesskey' => sesskey(),
                    'action' => \core\plugininfo\aiprovider::DOWN,
                    'id' => $row->id,
                ]),
                $OUTPUT->pix_icon(
                    pix: 't/down',
                    alt: '',
                ),
                array_merge($dataattributes, [
                    'data-direction' => \core\plugininfo\aiprovider::DOWN,
                    'role' => 'button',
                    'aria-label' => get_string('moveitemdown', 'core', $row->name),
                    'title' => get_string('moveitemdown', 'core', $row->name),
                    'class' => 'btn btn-link',
                ]),
            );
        } else {
            $downicon = '';
        }

        $spacer = ($hasup && $hasdown) ? $OUTPUT->spacer() : '';
        return html_writer::div($upicon . $spacer . $downicon, '', ['class' => 'w-25 d-flex justify-content-center']);
    }

    /**
     * Get the action URL for this table.
     *
     * The action URL is used to perform all actions when JS is not available.
     *
     * @param array $params
     * @return moodle_url
     */
    protected function get_base_action_url(array $params = []): moodle_url {
        return new moodle_url('/ai/configure_providers.php', $params);
    }
}
