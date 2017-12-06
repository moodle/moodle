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
 * @package dataformview
 * @subpackage csv
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 * Specialized class for csv view patterns.
 */
class dataformview_csv_patterns extends mod_dataform\pluginbase\dataformviewpatterns {

    /**
     * Search and collate view patterns that occur in given text.
     * Overriding {@link dataformviewpatterns::search()} to add searching
     * for import/export regexp patterns of the form ##import|export:linklable##.
     *
     * @return array View patterns found in the text
     */
    public function search($text, array $patterns = null) {
        $viewid = $this->_view->id;

        $found = parent::search($text, $patterns);

        if (!$patterns) {
            // Regexp patterns.
            if ($patterns = array_keys($this->patterns_expimp_label())) {
                foreach ($patterns as $pattern) {
                    if (preg_match_all("/$pattern/", $text, $matches)) {
                        foreach ($matches[0] as $match) {
                            $found[$match] = $match;
                        }
                    }
                }
            }
        }

        return $found;
    }

    /**
     *
     */
    public function get_replacements(array $patterns, $entry = null, array $options = array()) {
        global $CFG, $OUTPUT;

        $replacements = parent::get_replacements($patterns, $entry, $options);

        $view = $this->_view;
        $df = $view->get_df();
        $filter = $view->get_filter();
        $baseurl = new moodle_url($view->get_baseurl());
        $baseurl->param('sesskey', sesskey());

        foreach ($patterns as $pattern) {
            list($expimp, $label) = explode(':', trim($pattern, '#'). '::');
            switch ($expimp) {
                case 'exportall':
                    if ($view->param4) {
                        $actionurl = new moodle_url($baseurl, array('exportcsv' => $view::EXPORT_ALL));
                        $label = $label ? $label : html_writer::tag('span', get_string('exportall', 'dataformview_csv'));
                        $replacements[$pattern] = html_writer::link($actionurl, $label, array('class' => 'actionlink exportall'));
                    } else {
                        $replacements[$pattern] = '';
                    }

                    break;
                case 'exportpage':
                    if ($view->param4) {
                        $actionurl = new moodle_url($baseurl, array('exportcsv' => $view::EXPORT_PAGE));
                        $label = $label ? $label : html_writer::tag('span', get_string('exportpage', 'dataformview_csv'));
                        $replacements[$pattern] = html_writer::link($actionurl, $label, array('class' => 'actionlink exportpage'));
                    } else {
                        $replacements[$pattern] = '';
                    }

                    break;
                case 'import':
                    if ($view->param5) {
                        $actionurl = new moodle_url($baseurl, array('importcsv' => 1));
                        $label = $label ? $label : html_writer::tag('span', get_string('import'));
                        $replacements[$pattern] = html_writer::link($actionurl, $label, array('class' => 'actionlink import'));
                    } else {
                        $replacements[$pattern] = '';
                    }

                    break;
            }
        }

        return $replacements;
    }

    /**
     *
     */
    protected function patterns() {
        $patterns = parent::patterns();
        $cat = get_string('pluginname', 'dataformview_csv');
        $patterns['##exportall##'] = array(true, $cat);
        $patterns['##exportpage##'] = array(true, $cat);
        $patterns['##import##'] = array(true, $cat);

        return $patterns;
    }

    /**
     * Import/export with label patterns. Not included in menu.
     *
     * @return array
     */
    protected function patterns_expimp_label() {
        $patterns = array();

        $patterns['##exportall:[^#]+##'] = array(false);
        $patterns['##exportpage:[^#]+##'] = array(false);
        $patterns['##import:[^#]+##'] = array(false);

        return $patterns;
    }

}
