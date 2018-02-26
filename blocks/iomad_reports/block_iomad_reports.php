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

class block_iomad_reports extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_iomad_reports');
    }

    public function hide_header() {
        return true;
    }

    public function get_content() {
        global $SITE, $CFG, $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (!iomad::has_capability('block/iomad_reports:view', $this->context)) {
            return $this->content;
        }

        // Title.
        $this->content = new stdClass();
        $this->content->text = '<h3>'.get_string('pluginname', 'block_iomad_reports')."</h3>\n";

        // If no selected company then no report options to be shown.
        if (!iomad::get_my_companyid(context_system::instance(), false)) {
            $this->content->text .= '<div class="alert alert-warning">' . get_string('nocompanyselected', 'block_iomad_reports') . '</div>';
            return $this->content;
        }

        // Get all local/report_*.
        $reports = $this->getreportpaths();

        // Loop over reports.
        $this->content->text .= '<div class="iomadlink_container clearfix">';
        foreach ($reports as $report) {
            if (iomad::has_capability("local/$report:view", $this->context)) {
                $imgsrc = $OUTPUT->image_url('logo', "local_$report");
                $url = new moodle_url("/local/$report/index.php");
                $name = get_string( 'pluginname', "local_$report" );
                $icon = '<img src="'.$imgsrc.'" alt="'.$name.'" /><br />';

                // Report link html.
                // $this->content->text .= '<div class="iomadlink">';
                // $this->content->text .= "<a href=\"$url\">" . $icon . $name. "</a>";
                // $this->content->text .= '</div>';


                // Put together link.
                $this->content->text .= "<a class=\"testlink\" href=\"$url\">";
                $this->content->text .= '<div class="iomadlinkreports">';

                if ((empty($USER->theme) && (strpos($CFG->theme, 'iomad') !== false)) || (strpos($USER->theme, 'iomad') !== false)) {
                    $this->content->text .= '<div class="iomadicon"><div class="fa fa-topic fa-bar-chart-o"> </div>';
                } else {
                    $this->content->text .= '<div class="iomadicon">' . $icon ;
                }
                $this->content->text .= '<div class="actiondescription">' . $name . "</div>";
                $this->content->text .= '</div>';
                $this->content->text .= '</div>';
                $this->content->text .= '</a>';

            }

        }
        $this->content->text .= '</div>';

        // A clearfix for the floated linked.
        $this->content->text .= '<div class="clearfix"></div>';

        return $this->content;
    }

    private function getreportpaths() {
        // Find all /local/report_* directories.
        global $CFG;

        $path = "{$CFG->dirroot}/local/";
        $items = new DirectoryIterator( $path );
        $reports = array();
        foreach ($items as $item) {
            if ($item->isDot() or !$item->isDir()) {
                continue;
            }
            $dirname = $item->getFilename();
            if (stripos($dirname, 'report_') === 0) {
                $reports[] = $dirname;
            }
        }
        return $reports;
    }
}

