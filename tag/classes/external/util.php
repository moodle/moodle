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

namespace core_tag\external;

use core_tag_tag;

/**
 * Tag external functions utility class.
 *
 * @package   core_tag
 * @copyright 2019 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 3.7
 */
class util {
    /**
     * Get the array of core_tag_tag objects for external functions associated with an item (instances).
     *
     * @param string $component component responsible for tagging. For BC it can be empty but in this case the
     *               query will be slow because DB index will not be used.
     * @param string $itemtype type of the tagged item
     * @param int $itemid
     * @param int $standardonly wether to return only standard tags or any
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging
     * @return array tags for external functions
     */
    public static function get_item_tags(
        $component,
        $itemtype,
        $itemid,
        $standardonly = core_tag_tag::BOTH_STANDARD_AND_NOT,
        $tiuserid = 0
    ) {
        global $PAGE;

        $output = $PAGE->get_renderer('core');

        $tagitems = core_tag_tag::get_item_tags($component, $itemtype, $itemid, $standardonly, $tiuserid);
        $exportedtags = [];
        foreach ($tagitems as $tagitem) {
            $exporter = new tag_item_exporter($tagitem->to_object());
            $exportedtags[] = (array) $exporter->export($output);
        }
        return $exportedtags;
    }
}
