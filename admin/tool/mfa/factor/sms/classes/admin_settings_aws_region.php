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
 * @package    local_aws
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aws;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/adminlib.php');

/**
 * Admin setting for a list of AWS regions.
 *
 * @package    local_aws
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
        global $CFG;

        $default = $this->get_defaultsetting();

        $options = [];

        $all = require($CFG->dirroot . '/local/aws/sdk/Aws/data/endpoints.json.php');
        $ends = $all['partitions'][0]['regions'];
        if ($ends) {
            foreach ($ends as $key => $value) {
                $options[] = [
                    'value' => $key,
                    'label' => $key . ' - ' . $value['description'],
                ];
            }
        }

        $inputparams = array(
            'type' => 'text',
            'list' => $this->get_full_name(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'size' => $this->size,
            'id' => $this->get_id(),
            'class' => 'form-control text-ltr',
        );

        $element = \html_writer::start_tag('div', array('class' => 'form-text defaultsnext'));
        $element .= \html_writer::empty_tag('input', $inputparams);
        $element .= \html_writer::start_tag('datalist', array('id' => $this->get_full_name()));
        foreach ($options as $option) {
            $element .= \html_writer::tag('option', $option['label'], array('value' => $option['value']));
        }
        $element .= \html_writer::end_tag('datalist');
        $element .= \html_writer::end_tag('div');

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}
