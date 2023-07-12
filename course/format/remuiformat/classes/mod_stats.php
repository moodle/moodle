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
 * This is built using the bootstrapbase template to allow for new theme's using Moodle's new Bootstrap theme engine
 *
 * @package   format_remuiformat
 * @copyright Copyright (c) 2016 WisdmLabs. (http://www.wisdmlabs.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat;

defined('MOODLE_INTERNAL') || die;

use html_writer;

/**
 * This is built using the bootstrapbase template to allow for new theme's using Moodle's new Bootstrap theme engine
 */
class ModStats {

    /**
     * Singltone instance
     * @var ModStats
     */
    protected static $instance;

    /**
     * Plugin config
     * @var string
     */
    private $_plugin_config;

    /**
     * Private constructor
     */
    private function __construct() {
        $this->plugin_config = "format_remuiformat";
    }

    /**
     * Singleton Implementation.
     * @return ModStats Object
     */
    public static function getinstance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns the formatted summary of section
     * @param  string $summary  Summary text
     * @param  array  $settings Settings
     * @return string           Formatted summary
     */
    public function get_formatted_summary($summary, $settings) {
        $output = '';
        $summarylength = $settings['sectiontitlesummarymaxlength'];
        $summary = strip_tags($summary);
        if ($summary) {
            $end = "";
            if (strlen($summary) > $summarylength) {
                $end = " ...";
            }
            $summary = substr($summary, 0, $summarylength);
            $summary .= $end;
        }
        $output .= html_writer::start_tag('div', array('class' => 'overflowdiv '));
        $output .= html_writer::start_tag('div', array('class' => 'noclean '));
        $output .= $summary;
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        return $summary;
    }
}
