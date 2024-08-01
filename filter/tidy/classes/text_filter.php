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

namespace filter_tidy;

/**
 * HTML tidy text filter.
 *
 * This class looks for text including markup and
 * applies tidy's repair function to it.
 * Tidy is a HTML clean and
 * repair utility, which is currently available for PHP 4.3.x and PHP 5 as a
 * PECL extension from http://pecl.php.net/package/tidy, in PHP 5 you need only
 * to compile using the --with-tidy option.
 * If you don't have the tidy extension installed or don't know, you can enable
 * or disable this filter, it just won't have any effect.
 * If you want to know what you can set in $tidyoptions and what their default
 * values are, see http://php.net/manual/en/function.tidy-get-config.php.
 *
 * @package    filter_tidy
 * @subpackage tiny
 * @copyright  2004 Hannes Gassert <hannes at mediagonal dot ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        // Configuration for tidy.
        // See https://api.html-tidy.org/tidy/quickref_5.0.0.html for details.
        $tidyoptions = [
            'output-xhtml' => true,
            'show-body-only' => true,
            'tidy-mark' => false,
            'drop-proprietary-attributes' => true,
            'drop-empty-paras' => true,
            'indent' => true,
            'quiet' => true,
        ];

        // Do a quick check using strpos to avoid unnecessary work.
        if (strpos($text, '<') === false) {
            return $text;
        }

        // If enabled: run tidy over the entire string.
        if (extension_loaded('tidy')) {
            $currentlocale = \core\locale::get_locale();
            try {
                $text = (new \tidy())->repairString($text, $tidyoptions, 'utf8');
            } finally {
                \core\locale::set_locale(LC_ALL, $currentlocale);
            }
        }

        return $text;
    }
}
