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

namespace core_filters;

/**
 * Filter manager subclass that tracks how much work it does.
 *
 * @package core_filters
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class performance_measuring_filter_manager extends filter_manager {
    /** @var int number of filter objects created. */
    protected int $filterscreated = 0;

    /** @var int number of calls to filter_text. */
    protected int $textsfiltered = 0;

    /** @var int number of calls to filter_string. */
    protected int $stringsfiltered = 0;

    #[\Override]
    protected function unload_all_filters() {
        parent::unload_all_filters();
        $this->filterscreated = 0;
        $this->textsfiltered = 0;
        $this->stringsfiltered = 0;
    }

    #[\Override]
    protected function make_filter_object($filtername, $context, $localconfig) {
        $this->filterscreated++;
        return parent::make_filter_object($filtername, $context, $localconfig);
    }

    #[\Override]
    public function filter_text(
        $text,
        $context,
        array $options = [],
        ?array $skipfilters = null
    ) {
        if (!isset($options['stage']) || $options['stage'] === 'post_clean') {
            $this->textsfiltered++;
        }
        return parent::filter_text($text, $context, $options, $skipfilters);
    }

    #[\Override]
    public function filter_string($string, $context) {
        $this->stringsfiltered++;
        return parent::filter_string($string, $context);
    }

    /**
     * Return performance information, in the form required by {@see get_performance_info()}.
     *
     * @return array the performance info.
     */
    public function get_performance_summary(): array {
        return [
            [
                'contextswithfilters' => count($this->textfilters),
                'filterscreated' => $this->filterscreated,
                'textsfiltered' => $this->textsfiltered,
                'stringsfiltered' => $this->stringsfiltered,
            ],
            [
                'contextswithfilters' => 'Contexts for which filters were loaded',
                'filterscreated' => 'Filters created',
                'textsfiltered' => 'Pieces of content filtered',
                'stringsfiltered' => 'Strings filtered',
            ],
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(performance_measuring_filter_manager::class, \performance_measuring_filter_manager::class);
