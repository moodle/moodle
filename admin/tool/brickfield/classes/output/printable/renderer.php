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

namespace tool_brickfield\output\printable;

use tool_brickfield\local\tool\bfpdf;
use core\chart_bar;
use core\chart_pie;
use core\chart_series;
use tool_brickfield\accessibility;
use tool_brickfield\area_base;
use tool_brickfield\local\tool\filter;
use tool_brickfield\manager;

/**
 * tool_brickfield/printable renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \tool_brickfield\output\renderer {
    /**
     * Render the page containing the Printable report.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @return String HTML showing charts.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function display(\stdClass $data, filter $filter): string {
        $css = '';

        // Page data.
        $out = '';

        if (empty($filter->target)) {
            $linkname = get_string('printable:downloadpdf', 'tool_brickfield');
            $link = new \moodle_url(
                accessibility::get_plugin_url(),
                [
                    'tab' => 'printable',
                    'courseid' => $filter->courseid,
                    'target' => 'pdf',
                ]
            );
            $htmlicon = new \pix_icon('t/print', $linkname);
            $class = 'tool_brickfield_floatprinticon';
            $printlink = $this->action_link($link, $linkname, null, ['class' => $class, 'title' => $linkname], $htmlicon);
        }

        $out .= \html_writer::tag('h3', accessibility::get_title($filter, $data->countdata));
        $out .= !empty($printlink) ? $printlink : '';

        $div1 = \html_writer::div($this->pix_icon('f/award',
                get_string('totalactivities', manager::PLUGINNAME), manager::PLUGINNAME).
            get_string('totalactivitiescount', manager::PLUGINNAME, $data->combodata['total']), '',
            ['class' => 'col-sm-3'.$css]);
        $div2 = \html_writer::div($this->pix_icon('f/done2',
                get_string('passed', manager::PLUGINNAME), manager::PLUGINNAME).
            get_string('passedcount', manager::PLUGINNAME, $data->combodata['passed']), '',
            ['class' => 'col-sm-3'.$css]);
        $div3 = \html_writer::div($this->pix_icon('f/error',
                get_string('failed', manager::PLUGINNAME), manager::PLUGINNAME).
            get_string('failedcount', manager::PLUGINNAME, $data->combodata['failed']), '',
            ['class' => 'col-sm-3'.$css]);
        $out .= \html_writer::div($div1.$div2.$div3, '', ['id' => 'rowa', 'class' => 'row h4']);

        $out .= \html_writer::div('&nbsp;'); // Padding row.
        $str1 = \html_writer::tag('h4', get_string('toperrors', manager::PLUGINNAME));

        $table = new \html_table();
        $table->head  = [
            get_string('tblcheck', manager::PLUGINNAME),
            get_string('count', manager::PLUGINNAME),
        ];
        $table->size = ['80%', '20%'];
        $table->align = ['left', 'center'];
        $data->checkcountdata = !empty($data->checkcountdata) ? $data->checkcountdata : [];
        foreach ($data->checkcountdata as $key => $value) {
            if ($value->checkcount > 0) {
                $table->data[] = [$value->checkname, $value->checkcount];
            }
        }

        if (count($data->checkcountdata) > 0) {
            $str1 .= \html_writer::table($table, true);
        } else {
            $str1 .= \html_writer::tag('p', get_string('noerrorsfound', manager::PLUGINNAME));
        }
        $out .= \html_writer::start_div('row', ['id' => 'row2']);
        $out .= \html_writer::div($str1, '', ['class' => 'col-sm-4']);

        $str2 = \html_writer::tag('h4', get_string('toptargets', manager::PLUGINNAME));
        $table = new \html_table();
        $table->head  = [
            get_string('tbltarget', manager::PLUGINNAME),
            get_string('count', manager::PLUGINNAME),
        ];
        $table->size = ['80%', '20%'];
        $table->align = ['left', 'center'];
        $data->toptargetdata = !empty($data->toptargetdata) ? $data->toptargetdata : [];
        $table->data = $data->toptargetdata;
        if (count($data->toptargetdata) > 0) {
            $str2 .= \html_writer::table($table, true);
        } else {
            $str2 .= \html_writer::tag('p', get_string('noerrorsfound', manager::PLUGINNAME));
        }
        $out .= \html_writer::div($str2, '', ['class' => 'col-sm-4']);

        $str3 = \html_writer::tag('h4', get_string('taberrors', manager::PLUGINNAME));
        $table = new \html_table();
        $table->head  = [
            get_string('checktype', manager::PLUGINNAME),
            get_string('count', manager::PLUGINNAME),
        ];
        $table->size = ['80%', '20%'];
        $table->align = ['left', 'center'];
        foreach ($data->groupdata as $key => $group) {
            $checkgroup = area_base::checkgroup_name($key);
            $tmplabel = get_string('checktype:' . $checkgroup, manager::PLUGINNAME);
            $icon = $this->pix_icon('f/' . $checkgroup, $tmplabel, manager::PLUGINNAME);
            $table->data[] = [get_string('checktype:' . $checkgroup, manager::PLUGINNAME), $group->errorinstances];
        }
        $str3 .= \html_writer::table($table, true);
        $out .= \html_writer::div($str3, '', ['class' => 'col-sm-4']);

        $out .= \html_writer::end_div(); // End row2.

        $out .= \html_writer::start_div('row', ['id' => 'row3']);

        foreach ($data->combotardata as $key => &$combotar) {
            $combotar['passed'] = $combotar['total'] - $combotar['failed'];
            $combos[] = $combotar['total'] - $combotar['failed'];
            $combosf[] = $combotar['failed'];
        }

        $str4 = \html_writer::tag('h4', get_string('targetratio', manager::PLUGINNAME));
        $table = new \html_table();
        $table->head  = [
            get_string('tbltarget', manager::PLUGINNAME),
            get_string('passed', manager::PLUGINNAME),
            get_string('failed', manager::PLUGINNAME),
            get_string('total')
            ];

        foreach ($data->combotardata as $tar => $tarvalue) {
            $table->data[] = [$data->tarlabels[$tar],
                $tarvalue['passed'], $tarvalue['failed'], $tarvalue['total']];
        }

        $table->size = ['40%', '20%', '20%', '20%'];
        $table->align = ['left', 'center', 'center', 'center'];
        $str4 .= \html_writer::table($table, true);
        $out .= \html_writer::div($str4, '', ['class' => 'col-sm-4']);

        $str5 = \html_writer::tag('h4', get_string('titleerrorscount', manager::PLUGINNAME, $data->errordetailscount));
        $table = new \html_table();
        $table->head  = [
            get_string('tbltarget', manager::PLUGINNAME),
            get_string('tblcheck', manager::PLUGINNAME),
            get_string('tblline', manager::PLUGINNAME),
            get_string('tblhtmlcode', manager::PLUGINNAME)
            ];
        $data->errordata = !empty($data->errordata) ? $data->errordata : [];
        foreach ($data->errordata as $err) {
            $err->htmlcode = htmlentities($err->htmlcode);
            $row = [$data->tarlabels[$err->component], $err->shortname, $err->errline, $err->htmlcode];
            $table->data[] = $row;
        }

        $table->size = ['10%', '25%', '5%', '60%'];
        if (count($data->errordata) > 0) {
            $str5 .= \html_writer::table($table, true);
        } else {
            $str5 .= \html_writer::tag('p', get_string('noerrorsfound', manager::PLUGINNAME));
        }
        $out .= \html_writer::div($str5, '', ['class' => 'col-sm-8']);

        $out .= \html_writer::end_div(); // End row3.

        if ($filter->target == 'pdf') {
            // Converting divs to spans for better PDF display.
            $out = str_replace(['<div>', '</div>'], ['<span>', '</span>'], $out);
        }

        return $out;
    }

    /**
     * Return the path to use for PDF images.
     *
     * @return string
     */
    private function image_path(): string {
        global $CFG;
        return $CFG->wwwroot . '/admin/tool/brickfield/pix/pdf/';
    }

    /**
     * Renders the accessability report using the pdflib.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @return void
     */
    public function pdf_renderer(\stdClass $data, filter $filter) {
        $pdf = new bfpdf();

        $pdf->setFooterFont(Array('Helvetica', '', 10));
        $pdf->setPrintHeader(false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Brickfield Accessibility Report');
        $pdf->SetTitle('Brickfield Accessibility Report');
        $pdf->SetFont('Helvetica');

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins.
        $pdf->SetMargins(20, 20, 20, 100);

        $pdf->AddPage('P');
        $errorreporting = error_reporting(0);

        // Get current date for current user.
        $date = new \DateTime("now", \core_date::get_user_timezone_object());
        $pdf->SetLineWidth(0.0);

        $html = '
            <h1>' . get_string('accessibilityreport', manager::PLUGINNAME) . '</h1>
            <h2>'. accessibility::get_title($filter, $data->countdata) .'</h2>
            <p>' . userdate($date->getTimestamp()) . '</p>
            <table cellspacing="0" cellpadding="1">
                <tr>
                    <td>
                        <img src="' . $this->image_path() . 'tachometer-alt-solid.svg" width="15" height="15">' .
                        ' <td style="line-height: 10px;"> ' .
                        get_string('totalactivitiescount', manager::PLUGINNAME, $data->combodata['total']) .
                        '</td></td>
                    <td>
                        <img src="' . $this->image_path() . 'check-square-regular.svg" width="15" height="15">' .
                        ' <td style="line-height: 10px;"> ' .
                        get_string('passedcount', manager::PLUGINNAME, $data->combodata['passed']) .
                        '</td></td>
                    <td>
                        <img src="' . $this->image_path() . 'times-circle-regular.svg" width="15" height="15">' .
                        ' <td style="line-height: 10px;"> ' .
                        get_string('failedcount', manager::PLUGINNAME, $data->combodata['failed']) .
                        '</td></td>
                </tr>
            </table>';

        $pdf->writeHTML($html);

        if (!empty($data->checkcountdata)) {
            $pdf->writeHTML($this->get_errors_table($data));

            $tablegroup = '<table><tr>
                <td width="45%">'. $this->get_group_table($data) . '</td>
                <td width="10%"></td>
                <td width="45%">'. $this->get_inaccessible_table($data) . '</td>
            </tr></table>';

            $pdf->writeHTML($tablegroup);

            $pdf->AddPage('P');
        } else {
            $pdf->writeHTML('<div>'.get_string('noerrorsfound', manager::PLUGINNAME).'</div><div></div>');
        }

        $pdf->writeHTML($this->get_ratio_table($data));

        // Output the pdf.
        @$pdf->Output(get_string('pdf:filename', 'tool_brickfield', $filter->courseid).
            '_' . userdate($date->getTimestamp(), '%Y_%m_%d') . '.pdf', 'D');
        error_reporting($errorreporting);

    }

    /**
     * Builds the HTML for a styled table used in the pdf report.
     *
     * @param array $headers The headers of the table.
     * @param array $data The table data.
     * @param string $title The title of the table.
     * @param array $widths The widths of the table columns.
     * @return string The HTML code of the table.
     */
    public function render_table(array $headers, array $data, string $title, array $widths): string {
        $numheaders = count($headers);
        $html = '';
        $html .= '<table cellspacing="0" cellpadding="5"  style="background-color: #ffffff;">';
        $html .= '<tr><td style="border-bottom:.1 solid #333333;" colspan="'.$numheaders.'"><h3>'.$title.'</h3></td></tr>';
        $html .= '<tr>';

        for ($i = 0; $i < $numheaders; ++$i) {
            $align = $i > 0 ? "center" : "left";
            $html .= '<th style="text-align:'.$align.';" width="'.$widths[$i].'">';
            $html .= '<b>'.$headers[$i].'</b>';
            $html .= '</th>';
        }
        $html .= '</tr>';
        $j = 1;
        foreach ($data as $row) {
            ++$j;
            $color = $j % 2 == 1 ? "#FFFFFF" : "#F2F2F2";
            $html .= '<tr border ="1" style="background-color:'.$color.';">';
            for ($i = 0; $i < $numheaders; ++$i) {
                $align = $i > 0 ? "center" : "left";
                $html .= '<td width="'.$widths[$i].'" style="text-align:'.$align.';, background-color:'.$color.';">';
                $html .= $row[$i];
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * Gets the Activity Pass Ratio table.
     *
     * @param \stdClass $data Report data.
     * @return string The HTML code of the table.
     */
    public function get_ratio_table(\stdClass $data): string {
        $headers = [
        get_string('tbltarget', manager::PLUGINNAME),
        get_string('passed', manager::PLUGINNAME),
        get_string('failed', manager::PLUGINNAME),
        get_string('total')
        ];

        $tabledata = [];
        foreach ($data->combotardata as $tar => $tarvalue) {
            $tabledata[] = [
                $data->tarlabels[$tar],
                $tarvalue['total'] - $tarvalue['failed'],
                $tarvalue['failed'],
                $tarvalue['total']
            ];
        }

        return $this->render_table(
        $headers,
        $tabledata,
        get_string('targetratio', manager::PLUGINNAME),
        ["40%", "20%", "20%", "20%"]
        );
    }

    /**
     * Gets the Check Errors table.
     *
     * @param \stdClass $data Report data.
     * @return string The HTML code of the table.
     */
    public function get_group_table(\stdClass $data): string {
        $headers = [
        get_string('checktype', manager::PLUGINNAME),
        get_string('count', manager::PLUGINNAME),
        ];

        $tabledata = [];

        // Numbers are constants from \tool_brickfield\area_base::checkgroup.
        $icons = [
            area_base::CHECKGROUP_IMAGE  => $this->image_path() . 'image-regular.svg',
            area_base::CHECKGROUP_LAYOUT => $this->image_path() . 'th-large-solid.svg',
            area_base::CHECKGROUP_LINK   => $this->image_path() . 'link.png',
            area_base::CHECKGROUP_MEDIA  => $this->image_path() . 'play-circle-regular.svg',
            area_base::CHECKGROUP_TABLE  => $this->image_path() . 'table-solid.svg',
            area_base::CHECKGROUP_TEXT   => $this->image_path() . 'font-solid.svg',
        ];

        foreach ($data->groupdata as $key => $group) {
            $checkgroup = area_base::checkgroup_name($key);
            $icon = $icons[$key];
            $tabledata[] = ['<img src="'.$icon.'" width="15" height="15">' . ' ' .' <td style="line-height: 10px;">  '.
                get_string('checktype:' . $checkgroup, manager::PLUGINNAME).'</td>', $group->errorinstances];
        }

        return $this->render_table(
            $headers,
            $tabledata,
            get_string('taberrors', manager::PLUGINNAME),
            ["70%", "30%"]
        );
    }

    /**
     * Gets the Failed Activities table.
     *
     * @param \stdClass $data Report data.
     * @return string The HTML code of the table.
     */
    public function get_inaccessible_table(\stdClass $data): string {
        $headers = [get_string('tbltarget', manager::PLUGINNAME),
        get_string('count', manager::PLUGINNAME)];

        $tabledata = [];

        foreach ($data->toptargetdata as $key => $value) {
            if ($value->errorsum > 0) {
                $tabledata[] = [$value->component, $value->errorsum];
            }
        }
        return $this->render_table(
            $headers,
            $tabledata,
            get_string('toptargets', manager::PLUGINNAME),
            ["70%", "30%"]
        );
    }

    /**
     * Gets the Top Errors table.
     *
     * @param \stdClass $data Report data.
     * @return string The HTML code of the table.
     */
    public function get_errors_table(\stdClass $data): string {
        $headers = [get_string('tblcheck', manager::PLUGINNAME), get_string('count', manager::PLUGINNAME)];
        $tabledata = [];

        $data->checkcountdata = !empty($data->checkcountdata) ? $data->checkcountdata : [];

        foreach ($data->checkcountdata as $value) {
            if ($value->checkcount > 0) {
                $tabledata[] = [$value->checkname, $value->checkcount];
            }
        }
        return $this->render_table($headers, $tabledata, get_string('toperrors', manager::PLUGINNAME), ["80%", "20%"]);
    }
}
