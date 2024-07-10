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

namespace filter_urltolink;

/**
 * Filter converting URLs in the text to HTML links
 *
 * @package    filter_urltolink
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    /**
     * This might be eventually moved into parent class if we found it
     * useful for other filters, too.
     *
     * @var array global configuration for this filter
     */
    protected static $globalconfig;

    #[\Override]
    public function filter($text, array $options = []) {
        if (!isset($options['originalformat'])) {
            // If the format is not specified, we are probably called by {@see format_string()}
            // in that case, it would be dangerous to replace URL with the link because it could
            // be stripped. therefore, we do nothing.
            return $text;
        }
        if (in_array($options['originalformat'], explode(',', get_config('filter_urltolink', 'formats')))) {
            $this->convert_urls_into_links($text);
        }
        return $text;
    }

    /**
     * Given some text this function converts any URLs it finds into HTML links
     *
     * @param string $text Passed in by reference. The string to be searched for urls.
     */
    protected function convert_urls_into_links(&$text) {
        // I've added img tags to this list of tags to ignore.
        // See MDL-21168 for more info. A better way to ignore tags whether or not
        // they are escaped partially or completely would be desirable. For example:
        // <a href="blah">
        // &lt;a href="blah"&gt;
        // &lt;a href="blah">.
        $filterignoretagsopen  = ['<a\s[^>]+?>', '<span[^>]+?class="nolink"[^>]*?>'];
        $filterignoretagsclose = ['</a>', '</span>'];
        $ignoretags = [];
        filter_save_ignore_tags($text, $filterignoretagsopen, $filterignoretagsclose, $ignoretags);

        // Check if we support unicode modifiers in regular expressions. Cache it.
        // TODO: this check should be a environment requirement in Moodle 2.0, as far as unicode
        // chars are going to arrive to URLs officially really soon (2010?)
        // Original RFC regex from: http://www.bytemycode.com/snippets/snippet/796/
        // Various ideas from: http://alanstorm.com/url_regex_explained
        // Unicode check, negative assertion and other bits from Moodle.
        static $unicoderegexp;
        if (!isset($unicoderegexp)) {
            $unicoderegexp = @preg_match('/\pL/u', 'a'); // This will fail silently, returning false.
        }

        // TODO MDL-21296 - use of unicode modifiers may cause a timeout.
        $urlstart = '(?:http(s)?://|(?<!://)(www\.))';
        $domainsegment = '(?:[\pLl0-9][\pLl0-9-]*[\pLl0-9]|[\pLl0-9])';
        $numericip = '(?:(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $port = '(?::\d*)';
        $pathchar = '(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@-]|%[a-f0-9]{2})';
        $path = "(?:/$pathchar*)*";
        $querystring = '(?:\?(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@/?-]|%[a-fA-F0-9]{2})*)';
        $fragment = '(?:\#(?:[\pL0-9\.!$&\'\(\)*+,;=_~:@/?-]|%[a-fA-F0-9]{2})*)';

        // Lookbehind assertions.
        // Is not HTML attribute or CSS URL property. Unfortunately legit text like "url(http://...)" will not be a link.
        $lookbehindend = "(?<![]),.;])";

        $regex = "$urlstart((?:$domainsegment\.)+$domainsegment|$numericip)" .
                "($port?$path$querystring?$fragment?)$lookbehindend";
        if ($unicoderegexp) {
            $regex = '#' . $regex . '#ui';
        } else {
            $regex = '#' . preg_replace(['\pLl', '\PL'], 'a-z', $regex) . '#i';
        }

        // Locate any HTML tags.
        $matches = preg_split('/(<[^<|>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Iterate through the tokenized text to handle chunks (html and content).
        foreach ($matches as $idx => $chunk) {
            // Nothing to do. We skip completely any html chunk.
            if (strpos(trim($chunk), '<') === 0) {
                continue;
            }

            // Nothing to do. We skip any content chunk having any of these attributes.
            if (preg_match('#(background=")|(action=")|(style="background)|(href=")|(src=")|(url [(])#', $chunk)) {
                continue;
            }

            // Arrived here, we want to process every word in this chunk.
            $text = $chunk;
            $words = explode(' ', $text);

            foreach ($words as $idx2 => $word) {
                // ReDoS protection. Stop processing if a word is too large.
                if (strlen($word) < 4096) {
                    $words[$idx2] = preg_replace($regex, '<a href="http$1://$2$3$4" class="_blanktarget">$0</a>', $word);
                }
            }
            $text = implode(' ', $words);

            // Copy the result back to the array.
            $matches[$idx] = $text;
        }

        $text = implode('', $matches);

        if (!empty($ignoretags)) {
            $ignoretags = array_reverse($ignoretags); // Reversed so "progressive" str_replace() will solve some nesting problems.
            $text = str_replace(array_keys($ignoretags), $ignoretags, $text);
        }

        if (get_config('filter_urltolink', 'embedimages')) {
            // Now try to inject the images, this code was originally in the mediapluing filter
            // this may be useful only if somebody relies on the fact the links in FORMAT_MOODLE get converted
            // to URLs which in turn change to real images.
            $search = '/<a href="([^"]+\.(jpg|png|gif))" class="_blanktarget">([^>]*)<\/a>/is';
            $text = preg_replace_callback($search, [self::class, 'get_image_markup'], $text);
        }
    }

    /**
     * Change links to images into embedded images.
     *
     * This plugin is intended for automatic conversion of image URLs when FORMAT_MOODLE used.
     *
     * @param array $link
     * @return string
     */
    private function get_image_markup($link) {
        if ($link[1] !== $link[3]) {
            // This is not a link created by this filter, because the url does not match the text.
            return $link[0];
        }
        return '<img class="filter_urltolink_image" alt="" src="' . $link[1] . '" />';
    }
}
