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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class component_permissions
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class component_permissions extends component_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->plugins = true;
        $this->ordering = false;
        $this->form = true;
        $this->help = true;
    }

    /**
     * form_process_data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_process_data(moodleform $cform) {
        global $DB;

        if ($this->form) {
            $data = $cform->get_data();
            // Function cr_serialize() will add slashes.

            $components = cr_unserialize($this->config->components);
            $components['permissions']['config'] = $data;
            if (isset($components['permissions']['config']->conditionexpr)) {
                $components['permissions']['config']->conditionexpr =
                    $this->add_missing_conditions($components['permissions']['config']->conditionexpr);
            }
            $this->config->components = cr_serialize($components);
            $DB->update_record('block_configurable_reports', $this->config);
        }
    }

    /**
     * Add missing conditions.
     *
     * @param string $cond
     * @return array|mixed|string|string[]|null
     */
    public function add_missing_conditions($cond) {
        $components = cr_unserialize($this->config->components);
        if (isset($components['permissions']['elements'])) {
            $elements = $components['permissions']['elements'];
            $count = count($elements);

            if ($count === 0 || $count === 1) {
                return '';
            }

            for ($i = $count; $i > 0; $i--) {
                if (strpos($cond, 'c' . $i) === false) {
                    if ($count > 1 && $cond) {
                        $cond .= " and c$i";
                    } else {
                        $cond .= "c$i";
                    }
                }
            }
            $cond = trim($cond);

            // Deleting extra conditions.
            for ($i = $count + 1; $i <= $count + 5; $i++) {
                $cond = preg_replace('/(\bc' . $i . '\b\s+\b(and|or|not)\b\s*)/i', '', $cond);
                $cond = preg_replace('/(\s+\b(and|or|not)\b\s+\bc' . $i . '\b)/i', '', $cond);
            }
        }

        return $cond;

    }

    /**
     * Form set data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_set_data(moodleform $cform) {
        global $DB;

        if ($this->form) {
            $fdata = new stdclass;
            $components = cr_unserialize($this->config->components);
            $conditionsconfig = (object) ($components['permissions']['config'] ?? []);

            if (!isset($conditionsconfig->conditionexpr)) {
                $fdata->conditionexpr = '';
                $conditionsconfig->conditionexpr = '';
            }

            $conditionsconfig->conditionexpr = $this->add_missing_conditions($conditionsconfig->conditionexpr);
            $fdata->conditionexpr = $conditionsconfig->conditionexpr;

            if (empty($components['permissions'])) {
                $components['permissions'] = [];
            }

            if (!array_key_exists('config', $components['permissions'])) {
                $components['permissions']['config'] = new StdClass;
            }
            $components['permissions']['config']->conditionexpr = $fdata->conditionexpr;

            $this->config->components = cr_serialize($components);
            $DB->update_record('block_configurable_reports', $this->config);

            $cform->set_data($fdata);
        }
    }

}
