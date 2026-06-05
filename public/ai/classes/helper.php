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

namespace core_ai;

/**
 * AI helper class.
 *
 * @package    core_ai
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Reasoning tag names to strip from AI-generated text.
     *
     * Add new tag names here when additional AI models are found to include
     * reasoning content in their responses.
     *
     * @var string[]
     */
    public const REASONING_TAGS = [
        'think',
    ];

    /**
     * Strip reasoning tags from AI-generated content.
     *
     * Some AI models include reasoning or chain-of-thought content wrapped in
     * XML-like tags (e.g. <think>...</think>). This method removes those tags
     * and their content so only the final response is returned to the user.
     *
     * @param string $content The AI-generated content.
     * @return string The content with reasoning tags removed.
     */
    public static function strip_reasoning_tags(string $content): string {
        $pattern = implode('|', array_map('preg_quote', self::REASONING_TAGS));
        return trim(preg_replace('/<(' . $pattern . ')>.*?<\/\1>\s*/is', '', $content) ?? $content);
    }
}
