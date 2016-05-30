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
 * Search report renderer.
 *
 * @package    report_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_search\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for search report.
 *
 * @package    report_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Renders the global search admin interface.
     *
     * @param \report_search\output\form\admin $form
     * @param \core_search\area\base[] $searchareas
     * @param \stdClass[] $areasconfig
     * @return string HTML
     */
    public function render_report($form, $searchareas, $areasconfig) {

        $table = new \html_table();
        $table->head = array(get_string('searcharea', 'search'), get_string('newestdocindexed', 'report_search'),
            get_string('lastrun', 'report_search'));

        foreach ($searchareas as $areaid => $searcharea) {
            $cname = new \html_table_cell($searcharea->get_visible_name());
            $clastrun = new \html_table_cell($areasconfig[$areaid]->lastindexrun);
            if ($areasconfig[$areaid]->indexingstart) {
                $timediff = $areasconfig[$areaid]->indexingend - $areasconfig[$areaid]->indexingstart;
                $ctimetaken = new \html_table_cell($timediff . ' , ' .
                                                  $areasconfig[$areaid]->docsprocessed . ' , ' .
                                                  $areasconfig[$areaid]->recordsprocessed . ' , ' .
                                                  $areasconfig[$areaid]->docsignored);
            } else {
                $ctimetaken = '';
            }
            $row = new \html_table_row(array($cname, $clastrun, $ctimetaken));
            $table->data[] = $row;
        }

        // Display the table.
        $content = \html_writer::table($table);

        // Display the form.
        $formcontents = $this->output->heading(get_string('indexform', 'report_search'), 3) .
            $this->output->notification(get_string('indexinginfo', 'report_search'), 'notifymessage') . $form->render();
        $content .= \html_writer::tag('div', $formcontents, array('id' => 'searchindexform'));

        return $content;
    }

}
