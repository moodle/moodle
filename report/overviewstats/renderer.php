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
 * HTML rendering methods are defined here
 *
 * @package     report_overviewstats
 * @category    output
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Overview statistics renderer
 */
class report_overviewstats_renderer extends plugin_renderer_base {

    /**
     * Render the report charts
     *
     * @see report_overviewstats_chart::get_content() for the expected structure
     * @param array $charts list of {@link report_overviewstats_chart} instances
     * @return string
     */
    public function charts(array $charts) {

        $outlist = '';
        $outbody = '';

        $counter = 0;
        foreach ($charts as $chart) {
            foreach ($chart->get_content() as $title => $content) {
                $counter++;
                $outlist .= html_writer::tag('li', html_writer::link('#chart_seq_'.$counter, s($title)));
                $outbody .= html_writer::start_div('chart', array('id' => 'chart_seq_'.$counter));
                $outbody .= $this->output->heading($title, 2);
                if (is_array($content)) {
                    foreach ($content as $subtitle => $subcontent) {
                        $outbody .= html_writer::start_div('subchart');
                        $outbody .= $this->output->heading($subtitle, 3);
                        $outbody .= $subcontent;
                        $outbody .= html_writer::end_div();
                    }
                } else {
                    $outbody .= $content;
                }
                $outbody .= html_writer::end_div();
            }

        }

        $out  = $this->output->header();
        $out .= html_writer::start_tag('ul', array('class' => 'chartslist'));
        $out .= $outlist;
        $out .= html_writer::end_tag('ul');
        $out .= html_writer::div($outbody, 'charts');
        $out .= $this->output->footer();

        return $out;
    }
}
