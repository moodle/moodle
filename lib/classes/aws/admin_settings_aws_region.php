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
 * Admin setting for AWS regions.
 *
 * @package    core
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\aws;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/adminlib.php');

/**
 * Admin setting for a list of AWS regions.
 *
 * @package    core
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settings_aws_region extends \admin_setting_configtext {

    /**
     * Return part of form with setting.
     *
     * @param mixed $data array or string depending on setting
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $default = $this->get_defaultsetting();
        $options = [];
        // We do require() not require_once() here, as the file returns a value and we may need to get
        // this value more than once.
        $all = require($CFG->dirroot . '/lib/aws-sdk/src/data/endpoints.json.php');
        $ends = $all['partitions'][0]['regions'];
        if ($ends) {
            foreach ($ends as $key => $value) {
                $options[] = [
                    'value' => $key,
                    'label' => $key . ' - ' . $value['description'],
                ];
            }
        }

        $context = [
            'list' => $this->get_full_name(),
            'name' => $this->get_full_name(),
            'id' => $this->get_id(),
            'value' => $data,
            'size' => $this->size,
            'options' => $options,
        ];
        $element = $OUTPUT->render_from_template('core/aws/setting_aws_region', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}
