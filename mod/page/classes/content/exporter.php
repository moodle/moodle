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
 * Content export definition.
 *
 * @package     mod_page
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_page\content;

use core\content\export\exportable_items\exportable_textarea;
use core\content\export\exporters\abstract_mod_exporter;

/**
 * A class which assists a component to export content.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exporter extends abstract_mod_exporter {

    /**
     * Get the exportable items for mod_page.
     *
     * @param   bool $includeuserdata Whether to include user data, in addition to shared content.
     * @return  \core\content\export\exportable_item[]
     */
    public function get_exportables(bool $includeuserdata = false): array {
        $contentitems = [];

        $contentitems[] = new exportable_textarea(
            $this->get_context(),
            $this->get_component(),
            get_string('content', 'mod_page'),

            // Content is in the 'content' field of the 'page' table.
            $this->get_modname(),
            'content',

            // The record ID in the database is the CMID.
            $this->cm->instance,
            'contentformat',

            // The mod_page content has files in 'content/0', and the itemid (0) is present in the URL.
            'content',
            0,
            0
        );

        return $contentitems;
    }
}
