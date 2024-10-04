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

declare(strict_types=1);

namespace core_reportbuilder\output;

use core\output\inplace_editable;
use core_external\external_api;
use core_reportbuilder\permission;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\models\audience;

/**
 * Audience heading editable component
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience_heading_editable extends inplace_editable {

    /**
     * Class constructor
     *
     * @param int $audienceid
     * @param audience|null $audience
     */
    public function __construct(int $audienceid, ?audience $audience = null) {
        if ($audience === null) {
            $audience = new audience($audienceid);
        }

        $report = $audience->get_report();
        $editable = permission::can_edit_report($report);

        $audienceinstance = base::instance(0, $audience->to_record());

        // Use audience defined title if custom heading not set.
        if ('' !== $value = (string) $audience->get('heading')) {
            $displayvalue = $audience->get_formatted_heading($report->get_context());
        } else {
            $displayvalue = $value = $audienceinstance->get_name();
        }

        parent::__construct('core_reportbuilder', 'audienceheading', $audience->get('id'), $editable, $displayvalue, $value,
            get_string('renameaudience', 'core_reportbuilder', $audienceinstance->get_name()));
    }

    /**
     * Update audience persistent and return self, called from inplace_editable callback
     *
     * @param int $audienceid
     * @param string $value
     * @return self
     */
    public static function update(int $audienceid, string $value): self {
        $audience = new audience($audienceid);

        $report = $audience->get_report();

        external_api::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        $value = clean_param($value, PARAM_TEXT);
        $audience
            ->set('heading', $value)
            ->update();

        return new self(0, $audience);
    }
}
