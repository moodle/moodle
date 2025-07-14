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

namespace filter_emoticon;

/**
 * Filter converting emoticon texts into images
 *
 * This filter uses the emoticon settings in Site admin > Appearance > HTML settings
 * and replaces emoticon texts with images.
 *
 * @package    filter_emoticon
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    /**
     * Internal cache used for replacing. Multidimensional array;
     * - dimension 1: language,
     * - dimension 2: theme.
     * @var array
     */
    protected static $emoticontexts = [];

    /**
     * Internal cache used for replacing. Multidimensional array;
     * - dimension 1: language,
     * - dimension 2: theme.
     * @var array
     */
    protected static $emoticonimgs = [];

    #[\Override]
    public function filter($text, array $options = []) {
        if (!isset($options['originalformat'])) {
            // If the format is not specified, we are probably called by {@see format_string()}
            // in that case, it would be dangerous to replace text with the image because it could
            // be stripped. therefore, we do nothing.
            return $text;
        }
        if (in_array($options['originalformat'], explode(',', get_config('filter_emoticon', 'formats')))) {
            return $this->replace_emoticons($text);
        }
        return $text;
    }

    /**
     * Replace emoticons found in the text with their images
     *
     * @param string $text to modify
     * @return string the modified result
     */
    protected function replace_emoticons($text) {
        global $CFG, $OUTPUT, $PAGE;

        $lang = current_language();
        $theme = $PAGE->theme->name;

        if (!isset(self::$emoticontexts[$lang][$theme]) || !isset(self::$emoticonimgs[$lang][$theme])) {
            // Prepare internal caches.
            $manager = get_emoticon_manager();
            $emoticons = $manager->get_emoticons();
            self::$emoticontexts[$lang][$theme] = [];
            self::$emoticonimgs[$lang][$theme] = [];
            foreach ($emoticons as $emoticon) {
                self::$emoticontexts[$lang][$theme][] = $emoticon->text;
                self::$emoticonimgs[$lang][$theme][] = $OUTPUT->render($manager->prepare_renderable_emoticon($emoticon));
            }
            unset($emoticons);
        }

        if (empty(self::$emoticontexts[$lang][$theme])) { // No emoticons defined, nothing to process here.
            return $text;
        }

        // Detect all zones that we should not handle (including the nested tags).
        $processing = preg_split('/(<\/?(?:span|script|pre)[^>]*>)/is', $text, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Initialize the results.
        $resulthtml = "";
        $exclude = 0;

        // Define the patterns that mark the start of the forbidden zones.
        $excludepattern = ['/^<script/is', '/^<span[^>]+class="nolink[^"]*"/is', '/^<pre/is'];

        // Loop through the fragments.
        foreach ($processing as $fragment) {
            // If we are not ignoring, we MUST test if we should.
            if ($exclude == 0) {
                foreach ($excludepattern as $exp) {
                    if (preg_match($exp, $fragment)) {
                        $exclude = $exclude + 1;
                        break;
                    }
                }
            }
            if ($exclude > 0) {
                // If we are ignoring the fragment, then we must check if we may have reached the end of the zone.
                if (
                    strpos($fragment, '</span') !== false || strpos($fragment, '</script') !== false
                    || strpos($fragment, '</pre') !== false
                ) {
                    $exclude -= 1;
                    // This is needed because of a double increment at the first element.
                    if ($exclude == 1) {
                        $exclude -= 1;
                    }
                } else if (
                    strpos($fragment, '<span') !== false || strpos($fragment, '<script') !== false
                    || strpos($fragment, '<pre') !== false
                ) {
                    // If we find a nested tag we increase the exclusion level.
                    $exclude = $exclude + 1;
                }
            } else if (
                strpos($fragment, '<span') === false ||
                       strpos($fragment, '</span') === false
            ) {
                // This is the meat of the code - this is run every time.
                // This code only runs for fragments that are not ignored (including the tags themselves).
                $fragment = str_replace(self::$emoticontexts[$lang][$theme], self::$emoticonimgs[$lang][$theme], $fragment);
            }
            $resulthtml .= $fragment;
        }

        return $resulthtml;
    }
}
