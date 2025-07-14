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
 * Helpers for the core_tag subsystem implementation of privacy.
 *
 * @package    core_tag
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\tests;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\request\content_writer;

global $CFG;

/**
 * Helpers for the core_tag subsystem implementation of privacy.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait privacy_helper {
    /**
     * Fetch all tags on a subcontext.
     *
     * @param   \context            $context    The context being stored.
     * @param   array               $subcontext The subcontext path to check.
     * @return  \stdClass|array
     */
    protected function get_tags_on_subcontext(\context $context, array $subcontext) {
        $writer = \core_privacy\local\request\writer::with_context($context);
        return $writer->get_related_data($subcontext, 'tags');
    }

    /**
     * Check that all tags match on the specified context.
     *
     * @param   int                 $userid     The ID of the user being stored.
     * @param   \context            $context    The context being stored.
     * @param   array               $subcontext The subcontext path to check.
     * @param   string              $component  The component being stored.
     * @param   string              $itemtype    The tag area to store results for.
     * @param   int                 $itemid     The itemid to store.
     */
    protected function assert_all_tags_match_on_context(
        int $userid,
        \context $context,
        array $subcontext,
        $component,
        $itemtype,
        $itemid
    ) {
        $writer = \core_privacy\local\request\writer::with_context($context);

        $dbtags = \core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $exportedtags = $this->get_tags_on_subcontext($context, $subcontext);

        $this->assertCount(count($dbtags), $exportedtags);

        foreach ($dbtags as $tag) {
            $this->assertContains($tag->rawname, $exportedtags);
        }
    }
}
