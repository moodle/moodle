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

namespace filter_generico;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 *
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templateadmintools {

    /**
     * Returns an HTML string
     *
     * @return string Returns an HTML string
     */
    public static function fetch_template_table() {
        global $OUTPUT, $CFG;
        $conf = get_config('filter_generico');
        $template_details = self::fetch_template_details($conf);
        $have_updates = false;

        $table = new \html_table();
        $table->id = 'filter_generico_template_list';
        $table->head = array(
                get_string('name'),
                get_string('version'),
                get_string('description')
        );
        $table->headspan = array(1, 1, 1);
        $table->colclasses = array(
                'templatenamecol', 'templateversioncol', 'templateinstructionscol'
        );

        //loop through templates and add to table
        foreach ($template_details as $item) {
            $row = new \html_table_row();

            $titlelink = $editlink = \html_writer::link($item->url, $item->title);
            $titlecell = new \html_table_cell($titlelink);

            //version cell
            $updateversion = presets_control::template_has_update($item->index);
            if ($updateversion) {
                $button = new \single_button(
                        new \moodle_url($CFG->wwwroot . '/filter/generico/genericotemplatesadmin.php',
                                array('updatetemplate' => $item->index)),
                        get_string('updatetoversion', 'filter_generico', $updateversion));
                $update_html = $OUTPUT->render($button);
                $versioncell = new \html_table_cell($item->version . $update_html);
                $have_updates = true;
            } else {
                $versioncell = new \html_table_cell($item->version);
            }

            $instructionscell = new \html_table_cell($item->instructions);

            $row->cells = array(
                    $titlecell, $versioncell, $instructionscell
            );
            $table->data[] = $row;
        }

        $template_table = \html_writer::table($table);

        //if have_updates
        $update_all_html = '';
        if ($have_updates) {
            $all_button = new \single_button(
                    new \moodle_url($CFG->wwwroot . '/filter/generico/genericotemplatesadmin.php', array('updatetemplate' => -1)),
                    get_string('updateall', 'filter_generico'));
            $update_all_html = $OUTPUT->render($all_button);
        }

        return $update_all_html . $template_table;

    }//end of output html

    public static function fetch_template_details($conf) {
        global $CFG;
        $ret = array();

        //Get template count
        if ($conf && property_exists($conf, 'templatecount')) {
            $templatecount = $conf->templatecount;
        } else {
            $templatecount = generico_utils::FILTER_GENERICO_TEMPLATE_COUNT;
        }
        for ($tindex = 1; $tindex <= $templatecount; $tindex++) {


            //template display name
            if ($conf && property_exists($conf, 'templatename_' . $tindex)) {
                $template_title = $conf->{'templatename_' . $tindex};
                if (empty($template_title)) {
                    if (property_exists($conf, 'templatekey_' . $tindex)) {
                        $template_title = $conf->{'templatekey_' . $tindex};
                    }
                    if (empty($template_title)) {
                        $template_title = $tindex;
                    }
                }
            } else if ($conf && property_exists($conf, 'templatekey_' . $tindex)) {
                $template_title = $conf->{'templatekey_' . $tindex};
                if (empty($template_title)) {
                    $template_title = $tindex;
                }
            } else {
                $template_title = $tindex;
            }

            $template_details = new \stdClass();
            $template_details->index = $tindex;
            $template_details->title = $template_title;

            $template_details->version = "";
            if (property_exists($conf, 'templateversion_' . $tindex)) {
                $template_details->version = $conf->{'templateversion_' . $tindex};
            }

            $template_details->instructions = "";
            if (property_exists($conf, 'templateinstructions_' . $tindex)) {
                $template_details->instructions = $conf->{'templateinstructions_' . $tindex};
            }

            $template_details->url =
                    new \moodle_url('/admin/settings.php', array('section' => 'filter_generico_templatepage_' . $tindex));
            $ret[] = $template_details;
        }
        return $ret;
    }//end of fetch_templates function
}//end of class