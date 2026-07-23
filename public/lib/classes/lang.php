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

namespace core;

/**
 * Helper utility for Moodle language pack selection.
 *
 * @package    core
 * @copyright  2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang {
    /**
     * Given an HTTP Accept-Language header value, return the best matching installed
     * Moodle language pack code.
     *
     * Returns null when the header is absent or when no installed pack matches
     * any of the browser's preferred languages.
     *
     * Matching is attempted from most exact match to more relaxed matching:
     *   1. Exact match after normalising  eg 'en-AU' -> "en_au'.
     *   2. Base language match eg 'en-US' -> 'en'.
     *   3. Same base family match eg 'en-CA' -> 'en_us'.
     *
     * @param string|null $header the raw value of the HTTP Accept-Language header,
     *                            or null when the header is not present
     * @return string|null matched language pack code, null if no match
     */
    public static function match_lang_from_browser_header(?string $header): ?string {
        if ($header === null) {
            return null;
        }

        // Extract and clean langs from header, preserving q-value order.
        $rawlangs = str_replace('-', '_', $header);
        $rawlangs = explode(',', $rawlangs);

        // Build a list of [lang, q, sequence] to handle equal-priority entries correctly.
        $candidates = [];
        $sequence = 0;
        foreach ($rawlangs as $lang) {
            $lang = trim($lang);
            if (strpos($lang, ';') === false) {
                $candidates[] = ['lang' => $lang, 'q' => 1.0, 'seq' => $sequence++];
            } else {
                $parts = explode(';', $lang);
                $pos = strpos($parts[1], '=');
                $q = (float) substr($parts[1], $pos + 1);
                $candidates[] = ['lang' => trim($parts[0]), 'q' => $q, 'seq' => $sequence++];
            }
        }

        // Sort by q descending, then by original sequence ascending as tie-breaker.
        usort($candidates, function ($a, $b) {
            if ($b['q'] !== $a['q']) {
                return $b['q'] <=> $a['q'];
            }
            return $a['seq'] <=> $b['seq'];
        });

        $langs = array_column($candidates, 'lang');

        $stringmanager = get_string_manager();
        $installed = $stringmanager->get_list_of_translations();

        // For each language in priority order, try matching from most to least specific before
        // moving on to the next candidate. This ensures a higher-priority language with only a
        // family match (e.g. en-AU → en_us) correctly beats a lower-priority exact match (e.g. fr).
        foreach ($langs as $lang) {
            $lang = strtolower(clean_param($lang, PARAM_SAFEDIR));
            $base = explode('_', $lang, 2)[0];

            // 1. Exact match, e.g. 'en-AU' → 'en_au'.
            if ($stringmanager->translation_exists($lang, false)) {
                return $lang;
            }

            // 2. Base language match, e.g. 'en-US' → 'en'.
            if ($stringmanager->translation_exists($base, false)) {
                return $base;
            }

            // 3. Same-base family match, e.g. 'en-CA' → 'en_us'.
            foreach ($installed as $pack => $name) {
                if ($base === explode('_', $pack, 2)[0]) {
                    return $pack;
                }
            }
        }

        return null;
    }
}
