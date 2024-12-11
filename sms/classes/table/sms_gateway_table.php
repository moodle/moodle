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

namespace core_sms\table;

use context_system;
use core_table\dynamic as dynamic_table;
use flexible_table;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/tablelib.php");

/**
 * List sms gateway instances in a table.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sms_gateway_table extends flexible_table implements dynamic_table {

    /**
     * @var array $smsgateways List of gateway instances from the db.
     */
    protected array $smsgateways = [];

    /**
     * Constructor for the sms gateway table.
     */
    public function __construct() {
        parent::__construct($this->get_table_id());

        $this->smsgateways = $this->get_sms_gateways();
        $this->setup_column_configuration();
        $this->set_filterset(new sms_gateway_table_filterset());
        $this->setup();
        $tableclasses = $this->attributes['class'] . ' ' . $this->get_table_id();
        $this->set_attribute('class', $tableclasses);
    }

    #[\Override]
    public function get_context(): context_system {
        return context_system::instance();
    }

    /**
     * Get the js module needed for the table.
     *
     * This module can include table specific ajax calls etc.
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
     * Setup the column configs for the table.
     */
    protected function setup_column_configuration(): void {
        $columnlist = $this->get_column_list();
        $this->define_columns(array_keys($columnlist));
        $this->define_headers(array_values($columnlist));
    }

    /**
     * Get the columns for the table.
     *
     * @return array
     */
    protected function get_column_list(): array {
        return [
            'name'    => get_string('name'),
            'gateway' => get_string('gateway', 'sms'),
            'enabled' => get_string('status'),
            'actions' => get_string('actions', 'sms'),
        ];
    }

    /**
     * Get the table id for the table.
     *
     * @return string
     */
    protected function get_table_id(): string {
        return 'sms_gateways_table';
    }

    #[\Override]
    public function guess_base_url(): void {
        $this->define_baseurl(new moodle_url('/sms/sms_gateways.php'));
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
     * Get the sms gateways from the manager.
     *
     * @return array
     */
    protected function get_sms_gateways(): array {
        $gateways = \core\di::get(\core_sms\manager::class)->get_gateway_records();
        if (!empty($gateways)) {
            \core_collator::asort_objects_by_property($gateways, 'id');
        }
        return $gateways;
    }

    /**
     * Add the row data for the table.
     */
    public function out(): void {
        foreach ($this->smsgateways as $gateway) {
            $rowdata = (object) [
                'id' => $gateway->id,
                'name' => $gateway->name,
                'gateway' => $gateway->gateway,
                'enabled' => (int)$gateway->enabled,
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
     * @param stdClass $row The row object
     * @return string
     */
    protected function get_row_class(stdClass $row): string {
        if ($row->enabled) {
            return '';
        }
        return 'dimmed_text';
    }

    /**
     * The column for the gateway instance Name.
     *
     * @param stdClass $row The row object
     * @return string
     */
    public function col_name(stdClass $row): string {
        return $row->name;
    }

    /**
     * The column for the Gateway plugin name.
     *
     * @param stdClass $row The row object
     * @return string
     */
    public function col_gateway(stdClass $row): string {
        return $this->get_gateway_name($row->gateway);
    }

    /**
     * Get the gateway name according to the gateway class from db.
     *
     * @param string $gateway The name of the gateway
     * @return string
     */
    public function get_gateway_name(string $gateway): string {
        $values = explode('\\', $gateway);
        $gateway = $values[0];
        return get_string('pluginname', $gateway);
    }

    /**
     * Webservice for toggle.
     *
     * @return string
     */
    protected function get_toggle_service(): string {
        return 'core_sms_set_gateway_status';
    }

    /**
     * The column for enabled or disabled status of the gateway instance.
     *
     * @param stdClass $row The row object
     * @return string
     */
    public function col_enabled(stdClass $row): string {
        global $OUTPUT;

        $enabled = $row->enabled;
        if ($enabled) {
            $labelstr = get_string('disableplugin', 'core_admin', $row->name);
        } else {
            $labelstr = get_string('enableplugin', 'core_admin', $row->name);
        }

        $params = [
            'id' => 'sms-gateway-toggle-' . $row->id,
            'checked' => $enabled,
            'dataattributes' => [
                'name' => 'id',
                'value' => $this->get_gateway_name($row->gateway),
                'toggle-method' => $this->get_toggle_service(),
                'action' => 'togglestate',
                'plugin' => $row->id, // Set plugin attribute to gateway ID.
                'state' => $enabled ? 1 : 0,
            ],
            'title' => $labelstr,
            'label' => $labelstr,
            'labelclasses' => 'visually-hidden',
        ];

        return $OUTPUT->render_from_template('core_admin/setting_configtoggle', $params);
    }

    /**
     * The column to show the actions of the gateway instance.
     *
     * @param stdClass $row The row object.
     * @return string
     */
    public function col_actions(stdClass $row): string {
        global $OUTPUT;

        $editurl = new moodle_url('/sms/configure.php', ['id' => $row->id]);
        $deleteurl = new moodle_url('/sms/sms_gateways.php', ['id' => $row->id, 'action' => 'delete']);

        $templatecontext = [
            'editurl' => $editurl->out(false),
            'deleteurl' => $deleteurl->out(false),
        ];

        return $OUTPUT->render_from_template('core_sms/sms_action_icons', $templatecontext);
    }

    #[\Override]
    public function has_capability(): bool {
        return has_capability('moodle/site:config', $this->get_context());
    }
}
