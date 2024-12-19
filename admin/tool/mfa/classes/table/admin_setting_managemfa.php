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

namespace tool_mfa\table;

use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * Admin setting for MFA.
 *
 * @package     tool_mfa
 * @copyright   Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managemfa extends \core_admin\table\plugin_management_table {

    #[\Override]
    protected function get_plugintype(): string {
        return 'factor';
    }

    #[\Override]
    public function guess_base_url(): void {
        $this->define_baseurl(
            new \moodle_url('/admin/settings.php', ['section' => 'managemfa'])
        );
    }

    #[\Override]
    protected function get_action_url(array $params = []): \moodle_url {
        return new \moodle_url('/admin/tool/mfa/index.php', $params);
    }

    #[\Override]
    protected function get_column_list(): array {
        $columns = [
            'name' => get_string('factor', 'tool_mfa'),
        ];

        if ($this->supports_disabling()) {
            $columns['enabled'] = get_string('pluginenabled', 'core_plugin');
        }

        if ($this->supports_ordering()) {
            $columns['order'] = get_string('order', 'core');
        }

        $columns['weight'] = get_string('weight', 'tool_mfa');
        $columns['settings'] = get_string('settings', 'core');

        return $columns;
    }

    #[\Override]
    protected function col_settings(stdClass $row): string {
        if ($settingsurl = $row->plugininfo->get_settings_url()) {
            $factor = $row->plugininfo->get_factor($row->plugininfo->name);
            return \html_writer::link(
                url: $settingsurl,
                text: get_string('settings'),
                attributes: ["title" => get_string('editfactor', 'tool_mfa', $factor->get_display_name())],
            );
        }

        return '';
    }

    #[\Override]
    public function wrap_html_finish() {
        $this->output_factor_combinations_table();
    }

    /**
     * Show the name & short description column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_name(stdClass $row): string {
        global $OUTPUT;
        $factor = $row->plugininfo->get_factor($row->plugininfo->name);
        $params = [
            'name' => $factor->get_display_name(),
            'description' => $factor->get_short_description(),
        ];

        return $OUTPUT->render_from_template('core_admin/table/namedesc', $params);
    }

    /**
     * Show the weight column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_weight(stdClass $row): string {
        $factor = $row->plugininfo->get_factor($row->plugininfo->name);
        return $factor->get_weight();
    }

    /**
     * Defines supplementary table that shows available combinations of factors enough for successful authentication.
     */
    public function output_factor_combinations_table(): void {
        global $OUTPUT;

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $this->get_factor_combinations($factors, 0, count($factors) - 1);

        echo \html_writer::tag('h3', get_string('settings:combinations', 'tool_mfa'));

        if (empty($combinations)) {
            echo $OUTPUT->notification(get_string('error:notenoughfactors', 'tool_mfa'), 'notifyproblem');
            return;
        }

        $txt = get_strings(['combination', 'totalweight'], 'tool_mfa');
        $table = new \html_table();
        $table->id = 'mfacombinations';
        $table->attributes['class'] = 'admintable generaltable table table-bordered';
        $table->head  = [$txt->combination, $txt->totalweight];
        $table->data  = [];

        $factorstringconnector = get_string('connector', 'tool_mfa');
        foreach ($combinations as $combination) {
            $factorstrings = array_map(static function(object_factor_base $factor): string {
                return $factor->get_summary_condition() . ' <sup>' . $factor->get_weight() . '</sup>';
            }, $combination['combination']);

            $string = implode(" {$factorstringconnector} ", $factorstrings);
            $table->data[] = new \html_table_row([$string, $combination['totalweight']]);
        }

        echo \html_writer::table($table);
    }

    /**
     * Recursive method to get all possible combinations of given factors.
     * Output is filtered by combination total weight (should be greater than 100).
     *
     * @param array $allfactors initial array of factor objects
     * @param int $start start position in initial array
     * @param int $end end position in initial array
     * @param int $totalweight total weight of combination
     * @param array $combination combination candidate
     * @param array $result array that includes combination total weight and subarray of factors combination
     *
     * @return array
     */
    public function get_factor_combinations($allfactors, $start = 0, $end = 0,
        $totalweight = 0, $combination = [], $result = []): array {

        if ($totalweight >= 100) {
            // Ensure this is a valid combination before appending result.
            $valid = true;
            foreach ($combination as $factor) {
                if (!$factor->check_combination($combination)) {
                    $valid = false;
                }
            }
            if ($valid) {
                $result[] = ['totalweight' => $totalweight, 'combination' => $combination];
            }
            return $result;
        } else if ($start > $end) {
            return $result;
        }

        $combinationnext = $combination;
        $combinationnext[] = $allfactors[$start];

        $result = $this->get_factor_combinations(
            allfactors: $allfactors,
            start: $start + 1,
            end: $end,
            totalweight: $totalweight + $allfactors[$start]->get_weight(),
            combination: $combinationnext,
            result: $result,
        );

        $result = $this->get_factor_combinations(
            allfactors: $allfactors,
            start: $start + 1,
            end: $end,
            totalweight: $totalweight,
            combination: $combination,
            result: $result,
        );

        return $result;
    }
}
