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
 * Provides the {@link tool_iomadpolicy\iomadpolicy_version_exporter} class.
 *
 * @package   tool_iomadpolicy
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use tool_iomadpolicy\api;

/**
 * Exporter of a single iomadpolicy document version.
 *
 * Note we cannot use the persistent_exporter as our super class because we want to add some properties not present in
 * the persistent (e.g. acceptancescount).
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class iomadpolicy_version_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {

        return iomadpolicy_version::properties_definition() + [
            'acceptancescount' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'status' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    /**
     * Return the list of additional (calculated and readonly) properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            // Human readable type of the iomadpolicy document version.
            'typetext' => [
                'type' => PARAM_TEXT,
            ],
            // Human readable audience of the iomadpolicy document audience.
            'audiencetext' => [
                'type' => PARAM_TEXT,
            ],
            // Detailed information about the number of iomadpolicy acceptances.
            'acceptancescounttext' => [
                'type' => PARAM_TEXT,
            ],
            // Link to view acceptances.
            'acceptancescounturl' => [
                'type' => PARAM_LOCALURL,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $othervalues = [
            'typetext' => get_string('iomadpolicydoctype'.$this->data->type, 'tool_iomadpolicy'),
            'audiencetext' => get_string('iomadpolicydocaudience'.$this->data->audience, 'tool_iomadpolicy'),
        ];

        if (!isset($this->data->acceptancescount) || $this->data->status == iomadpolicy_version::STATUS_DRAFT) {
            // Return "N/A" for acceptances count.
            $othervalues['acceptancescounttext'] = get_string('useracceptancecountna', 'tool_iomadpolicy');
            $othervalues['acceptancescounturl'] = null;
            return $othervalues;
        }

        $acceptancescount = empty($this->data->acceptancescount) ? 0 : $this->data->acceptancescount;
        $acceptancesexpected = api::count_total_users();

        $a = [
            'agreedcount' => $acceptancescount,
            'userscount' => $acceptancesexpected,
            'percent' => min(100, round($acceptancescount * 100 / max($acceptancesexpected, 1))),
        ];

        $othervalues['acceptancescounttext'] = get_string('useracceptancecount', 'tool_iomadpolicy', $a);
        $acceptancesurl = new \moodle_url('/admin/tool/iomadpolicy/acceptances.php', ['iomadpolicyid' => $this->data->iomadpolicyid]);
        if ($this->data->status != iomadpolicy_version::STATUS_ACTIVE) {
            $acceptancesurl->param('versionid', $this->data->id);
        }
        $othervalues['acceptancescounturl'] = $acceptancesurl->out(false);

        return $othervalues;
    }

    /**
     * Get the formatting parameters for the summary field.
     *
     * @return array
     */
    protected function get_format_parameters_for_summary() {
        return [
            'component' => 'tool_iomadpolicy',
            'filearea' => 'iomadpolicydocumentsummary',
            'itemid' => $this->data->id
        ];
    }

    /**
     * Get the formatting parameters for the content field.
     *
     * @return array
     */
    protected function get_format_parameters_for_content() {
        return [
            'component' => 'tool_iomadpolicy',
            'filearea' => 'iomadpolicydocumentcontent',
            'itemid' => $this->data->id
        ];
    }
}
