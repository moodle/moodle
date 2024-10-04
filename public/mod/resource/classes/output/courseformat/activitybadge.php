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

namespace mod_resource\output\courseformat;

/**
 * Activity badge resource class, used for displaying the file type.
 *
 * @package    mod_resource
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activitybadge extends \core_courseformat\output\activitybadge {

    /**
     * This method will be called before exporting the template.
     */
    protected function update_content(): void {
        $customdata = $this->cminfo->customdata;
        if (is_array($customdata) && isset($customdata['displayoptions'])) {
            $options = (object) ['displayoptions' => $customdata['displayoptions']];
            $this->content = resource_get_optional_filetype($options, $this->cminfo);
        }
    }
}
