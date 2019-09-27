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
 * Unit tests for the filter_h5p
 *
 * @package    filter_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/filter/h5p/filter.php');

/**
 * Unit tests for the H5P filter.
 *
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_h5p_testcase extends advanced_testcase {

    public function setUp() {
        parent::setUp();

        $this->resetAfterTest(true);

        set_config('allowedsources', "https://h5p.org/h5p/embed/[id]\nhttps://*.h5p.com/content/[id]/embed\nhttps://*.h5p.com/content/[id]
                \nhttps://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php?action=h5p_embed&id=[id]", 'filter_h5p');
        // Enable h5p filter at top level.
        filter_set_global_state('h5p', TEXTFILTER_ON);
    }

    /**
     * Check that h5p tags with urls from allowed domains are filtered.
     *
     * @param string $text Original text
     * @param string $filteredtextpattern Text pattern after H5P filter
     *
     * @dataProvider texts_provider
     */
    public function test_filter_urls($text, $filteredtextpattern) {

        $filterplugin = new filter_h5p(null, array());

        $filteredtext = $filterplugin->filter($text);
        $this->assertRegExp($filteredtextpattern, $filteredtext);
    }

    /**
     * Provides texts to filter for the {@link self::test_filter_urls} method.
     *
     * @return array
     */
    public function texts_provider() {
        return [
            ["http:://example.com", "#http:://example.com#"],
            ["http://google.es/h5p/embed/3425234", "#http://google.es/h5p/embed/3425234#"],
            ["https://h5p.org/h5p/embed/547225", "#<iframe src=\"https://h5p.org/h5p/embed/547225\"[^>]+?>#"],
            ["https://moodle.h5p.com/content/1290729733828858779/embed", "#<iframe src=\"https://moodle.h5p.com/content/1290729733828858779/embed\"[^>]+?>#"],
            ["https://moodle.h5p.com/content/1290729733828858779", "#<iframe src=\"https://moodle.h5p.com/content/1290729733828858779/embed\"[^>]+?>#"],
            ["<a href=\"https://h5p.org/h5p/embed/547225\">link</a>",  "#^((?!iframe).)*$#"],
            ["this is a text with an h5p url https://h5p.org/h5p/embed/547225 inside",
                    "#this is a text with an h5p url <iframe src=\"https://h5p.org/h5p/embed/547225\"(.|\n)*> inside#"],
            ["https://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php?action=h5p_embed&amp;id=13",
                    "#<iframe src=\"https://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php\?action=h5p_embed\&amp\;id=13\"[^>]+?>#"],
            ["https://h5p.org/h5p/embed/547225 another content in the same page https://moodle.h5p.com/content/1290729733828858779/embed",
                    "#<iframe src=\"https://h5p.org/h5p/embed/547225\"[^>]+?>((?!<iframe).)*<iframe src=\"https://moodle.h5p.com/content/1290729733828858779/embed\"[^>]+?>#"]
        ];
    }
}