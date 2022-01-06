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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @authors   Rabea de Groot and Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

class mod_pdfannotator_renderer extends plugin_renderer_base {

    /**
     *
     * @param type $index
     * @return type
     */
    public function render_index($index) {
        return $this->render_from_template('pdfannotator/index', $index->export_for_template($this));
    }
    /**
     *
     * @param \templatable $statistic
     * @return type
     */
    public function render_statistic(\templatable $statistic) {
        $data = $statistic->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/statistic', $data);
    }

    /**
     * renders dropdown-actionmenu. Currently used on overview in the categories "answers" and "reports".
     * @param \templatable $dropdownmenu
     * @return type
     */
    public function render_dropdownmenu(\templatable $dropdownmenu) {
        $data = $dropdownmenu->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/dropdownmenu', $data);
    }

    /**
     * Render a table containing information about a comment the user wants to report
     *
     * @param pdfannotator_comment_info $info a renderable
     * @return string
     */
    public function render_pdfannotator_comment_info(pdfannotator_comment_info $info) {
        $o = '';
        $o .= $this->output->container_start('appointmentinfotable');
        $o .= $this->output->box_start('boxaligncenter appointmentinfotable');

        $t = new html_table();

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('slotdatetimelabel', 'pdfannotator'));
        $cell2 = $info->datetime;
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('author', 'pdfannotator'));
        $cell2 = new html_table_cell($info->author);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('comment', 'pdfannotator'));
        $cell2 = new html_table_cell($info->content);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $o .= html_writer::table($t);
        $o .= $this->output->box_end();
        $o .= $this->output->container_end();
        return $o;
    }
    /**
     * Construct a tab header.
     *
     * @param moodle_url $baseurl
     * @param string $namekey
     * @param string $what
     * @param string $subpage
     * @param string $nameargs
     * @return tabobject
     */
    private function pdfannotator_create_tab(moodle_url $baseurl, $namekey = null, $action, $pdfannotatorname = null, $nameargs = null) {
        $taburl = new moodle_url($baseurl, array('action' => $action));
        $tabname = get_string($namekey, 'pdfannotator', $nameargs);
        if ($pdfannotatorname) {
            strlen($pdfannotatorname) > 20 ? $tabname = substr($pdfannotatorname, 0, 21) . "..." : $tabname = $pdfannotatorname;
        }
        $id = $action;
        $tab = new tabobject($id, $taburl, $tabname);
        return $tab;
    }
    /**
     * Render the tab header hierarchy.
     *
     * @param moodle_url $baseurl
     * @param type $selected
     * @param type $pdfannotatorname
     * @param type $context
     * @param type $inactive
     * @return type
     */
    public function pdfannotator_render_tabs(moodle_url $baseurl, $selected = null, $pdfannotatorname, $context, $inactive = null) {

        $overviewtab = $this->pdfannotator_create_tab($baseurl, 'overview', 'overview');

        $level1 = array(
            $overviewtab,
            $this->pdfannotator_create_tab($baseurl, 'document', 'view', $pdfannotatorname),
            $this->pdfannotator_create_tab($baseurl, 'statistic', 'statistic'),
        );
        return $this->tabtree($level1, $selected, $inactive);
    }

}
