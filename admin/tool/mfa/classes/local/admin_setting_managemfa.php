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

namespace tool_mfa\local;

use tool_mfa\local\factor\object_factor_base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddllib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/messagelib.php');

/**
 * Admin setting for MFA.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managemfa extends \admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('mfaui', get_string('mfasettings', 'tool_mfa'), '', '');
    }

    /**
     * Always returns true
     *
     * @return bool
     */
    public function get_setting(): bool {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data): string {
        return '';
    }

    /**
     * Returns XHTML to display Manage MFA admin page.
     *
     * @param mixed $data Unused
     * @param string $query
     *
     * @return string highlight
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function output_html($data, $query=''): string {
        global $OUTPUT;

        $return = $OUTPUT->box_start('generalbox');
        $return .= $this->define_manage_mfa_table();
        $return .= $OUTPUT->box_end();

        $return .= $OUTPUT->heading(get_string('settings:combinations', 'tool_mfa'), 3);
        $return .= $OUTPUT->box_start('generalbox');
        $return .= $this->define_factor_combinations_table();
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }

    /**
     * Defines main table with configurable factors.
     *
     * @return string HTML code
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function define_manage_mfa_table() {
        global $OUTPUT;
        $sesskey = sesskey();

        $txt = get_strings(['enable', 'disable', 'moveup', 'movedown', 'order', 'settings']);
        $txt->factor = get_string('factor', 'tool_mfa');
        $txt->weight = get_string('weight', 'tool_mfa');
        $txt->setup = get_string('setuprequired', 'tool_mfa');
        $txt->input = get_string('inputrequired', 'tool_mfa');

        $table = new \html_table();
        $table->id = 'managemfatable';
        $table->attributes['class'] = 'admintable generaltable';
        $table->head  = [
            $txt->factor,
            $txt->enable,
            $txt->order,
            $txt->weight,
            $txt->settings,
            $txt->setup,
            $txt->input,
        ];
        $table->colclasses = ['leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign'];
        $table->data  = [];

        $factors = \tool_mfa\plugininfo\factor::get_factors();
        $enabledfactors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $order = 1;

        foreach ($factors as $factor) {
            $settingsparams = ['section' => 'factor_'.$factor->name];
            $settingsurl = new \moodle_url('settings.php', $settingsparams);
            $settingslinkattrtext = get_string('editfactor', 'tool_mfa', $factor->get_display_name());
            $settingslinkattr = [
                'title' => $settingslinkattrtext,
                'aria-label' => $settingslinkattrtext,
            ];
            $settingslink = \html_writer::link($settingsurl, $txt->settings, $settingslinkattr);

            if ($factor->is_enabled()) {
                $hideshowparams = ['action' => 'disable', 'factor' => $factor->name, 'sesskey' => $sesskey];
                $hideshowurl = new \moodle_url('tool/mfa/index.php', $hideshowparams);
                $hideshowlink = \html_writer::link($hideshowurl, $OUTPUT->pix_icon('t/hide', $txt->disable));
                $class = '';

                if ($order > 1) {
                    $upparams = ['action' => 'up', 'factor' => $factor->name, 'sesskey' => $sesskey];
                    $upurl = new \moodle_url('tool/mfa/index.php', $upparams);
                    $uplink = \html_writer::link($upurl, $OUTPUT->pix_icon('t/up', $txt->moveup));
                } else {
                    $uplink = \html_writer::link('', $uplink = $OUTPUT->spacer(['style' => 'margin-right: .5rem']));
                }

                if ($order < count($enabledfactors)) {
                    $downparams = ['action' => 'down', 'factor' => $factor->name, 'sesskey' => $sesskey];
                    $downurl = new \moodle_url('tool/mfa/index.php', $downparams);
                    $downlink = \html_writer::link($downurl, $OUTPUT->pix_icon('t/down', $txt->movedown));
                } else {
                    $downlink = '';
                }
                $updownlink = $uplink.$downlink;
                $order++;
            } else {
                $hideshowparams = ['action' => 'enable', 'factor' => $factor->name, 'sesskey' => $sesskey];
                $hideshowurl = new \moodle_url('tool/mfa/index.php', $hideshowparams);
                $hideshowlink = \html_writer::link($hideshowurl, $OUTPUT->pix_icon('t/show', $txt->enable));
                $class = 'dimmed_text';
                $updownlink = '';
            }

            $hassetup = $factor->has_setup() ? get_string('yes') : get_string('no');
            $hasinput = $factor->has_input() ? get_string('yes') : get_string('no');

            $rowarray = [
                $factor->get_display_name(),
                $hideshowlink,
                $updownlink,
                $factor->get_weight(),
                $settingslink,
                $hassetup,
                $hasinput,
            ];
            $row = new \html_table_row($rowarray);
            $row->attributes['class'] = $class;

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }

    /**
     * Defines supplementary table that shows available combinations of factors enough for successful authentication.
     *
     * @return string HTML code
     */
    public function define_factor_combinations_table() {
        global $OUTPUT;

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $this->get_factor_combinations($factors, 0, count($factors) - 1);

        if (empty($combinations)) {
            return $OUTPUT->notification(get_string('error:notenoughfactors', 'tool_mfa'), 'notifyproblem');
        }

        $txt = get_strings(['combination', 'totalweight'], 'tool_mfa');
        $table = new \html_table();
        $table->id = 'managemfatable';
        $table->attributes['class'] = 'admintable generaltable table table-bordered';
        $table->head  = [$txt->combination, $txt->totalweight];
        $table->colclasses = ['leftalign', 'centeralign'];
        $table->data  = [];

        $factorstringconnector = get_string('connector', 'tool_mfa');
        foreach ($combinations as $combination) {
            $factorstrings = array_map(static function(object_factor_base $factor): string {
                return $factor->get_summary_condition() . ' <sup>' . $factor->get_weight() . '</sup>';
            }, $combination['combination']);

            $string = implode(" {$factorstringconnector} ", $factorstrings);
            $table->data[] = new \html_table_row([$string, $combination['totalweight']]);
        }

        return \html_writer::table($table);
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
        $totalweight = 0, $combination = [], $result = []) {

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
            $allfactors,
            $start + 1,
            $end,
            $totalweight + $allfactors[$start]->get_weight(),
            $combinationnext,
            $result);

        $result = $this->get_factor_combinations(
            $allfactors,
            $start + 1,
            $end,
            $totalweight,
            $combination,
            $result);

        return $result;
    }
}
