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
 * Unit tests for the filter_displayh5p
 *
 * @package    filter_displayh5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_displayh5p;

/**
 * Unit tests for the display H5P filter.
 *
 * @package filter_displayh5p
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_displayh5p\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Check that h5p tags with urls from allowed domains are filtered.
     *
     * @param string $text Original text
     * @param string $filteredtextpattern Text pattern after display H5P filter
     * @dataProvider texts_provider
     */
    public function test_filter_urls($text, $filteredtextpattern): void {
        $this->resetAfterTest(true);

        set_config(
            'allowedsources',
            "https://moodle.h5p.com/content/[id]/embed\nhttps://moodle.h5p.com/content/[id]
                \nhttps://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php?action=h5p_embed&id=[id]",
            'filter_displayh5p'
        );

        $filterplugin = new text_filter(null, []);

        $filteredtext = $filterplugin->filter($text);
        $this->assertMatchesRegularExpression($filteredtextpattern, $filteredtext);
    }

    /**
     * Provides texts to filter for the {@see self::test_filter_urls} method.
     *
     * @return array
     */
    public static function texts_provider(): array {
        global $CFG;

        $contenturla = "https://moodle.h5p.com/content/1290848995208939539/embed";
        $contenturlb = "https://moodle.h5p.com/content/1290729733828858779/embed";

        return [
            [
                "http:://example.com",
                "#http:://example.com#",
            ],
            [
                "http://google.es/h5p/embed/3425234",
                "#http://google.es/h5p/embed/3425234#",
            ],
            [
                $contenturlb,
                "#<iframe src=\"{$contenturlb}\"[^>]+?>#",
            ],
            [
                "https://moodle.h5p.com/content/1290729733828858779",
                "#<iframe src=\"{$contenturlb}\"[^>]+?>#",
            ],
            [
                "<a href=\"{$contenturla}\">{$contenturla}</a>",
                "#<iframe src=\"{$contenturla}\"[^>]+?>#", ],
            [
                "<a href=\"https://moodle.org\">{$contenturla}</a>",
                "#^((?!iframe).)*$#",
            ],
            [
                "<a href=\"{$contenturla}\">link</a>",
                "#^((?!iframe).)*$#",
            ],
            [
                "this is a text with an h5p url {$contenturla} inside",
                "#this is a text with an h5p url <iframe src=\"{$contenturla}\"(.|\n)*> inside#",
            ],
            [
                "https://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php?action=h5p_embed&amp;id=13",
                // phpcs:ignore moodle.Files.LineLength.TooLong
                "#<iframe src=\"https://generic.wordpress.soton.ac.uk/altc/wp-admin/admin-ajax.php\?action=h5p_embed\&amp\;id=13\"[^>]+?>#",
            ],
            [
                "{$contenturla} another content in the same page {$contenturlb}",
                "#<iframe src=\"{$contenturla}\"[^>]+?>((?!<iframe).)*" .
                "<iframe src=\"{$contenturlb}\"[^>]+?>#",
            ],
            [
                $CFG->wwwroot . "/pluginfile.php/5/user/private/interactive-video.h5p?export=1&embed=1",
                "#<iframe src=\"{$CFG->wwwroot}/h5p/embed.php\?url=" .
                    rawurlencode("{$CFG->wwwroot}/pluginfile.php/5/user/private/interactive-video.h5p") .
                    "&export=1&embed=1\"[^>]*?></iframe>#",
            ],
            [
                $CFG->wwwroot . "/pluginfile.php/5/user/private/accordion-6-7138%20%281%29.h5p.h5p",
                "#<iframe src=\"{$CFG->wwwroot}/h5p/embed.php\?url=" .
                    rawurlencode("{$CFG->wwwroot}/pluginfile.php/5/user/private/accordion-6-7138%20%281%29.h5p.h5p") .
                    "\"[^>]*?></iframe>#",
            ],
        ];
    }
}
