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
 * Base class for the table used by iomad_company_admin/editusers.php.
 *
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_commerce\tables;

use \table_sql;
use \moodle_url;
use \iomad;
use \html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class orders_table extends table_sql {

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($row) {
        $name = fullname($row, has_capability('moodle/site:viewfullnames', $this->get_context()));

        $profileurl = new moodle_url('/user/profile.php', ['id' => $row->id]);
        return html_writer::tag('a', $name, ['href' => $profileurl]);
    }

    /**
     * Generate the display of the order reference.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_reference($row) {
        return format_string($row->reference);
    }

    /**
     * Generate the display of the order reference.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_value($row) {
        return $row->value . "&nbsp" . $row->currency;
    }

    /**
     * Generate the display of theorder payment provider
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_paymentprovider($row) {
        global $DB;

        if (!empty($row->gateway)) {
            return get_string('pluginname', 'paygw_' . $row->gateway);
        }

        if ($row->status == 'p') {
            return get_string('pp_historic', 'block_iomad_commerce');
        }
        return '';
    }

    /**
     * Generate the display of the order status.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_status($row) {

        return get_string('status_' . $row->status, 'block_iomad_commerce');
    }

    /**
     * Generate the display of the order status.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_unprocesseditems($row) {

        return  ($row->unprocesseditems > 0 ? $row->unprocesseditems : "");
    }

    /**
    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_company($row) {

        return format_string($row->company);
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_date($row) {
        global $CFG;

        return format_string(date($CFG->iomad_date_format, $row->date));
    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $companycontext;

        $stredit   = get_string('edit');
        if (iomad::has_capability('block/iomad_commerce:admin_view', $companycontext)) {
            $editbutton = "<a href='" . new moodle_url('edit_order_form.php', array("id" => $row->id)) . "'>
            <i class='icon fa fa-cog fa-fw ' title='$stredit' role='img' aria-label='$stredit'></i></a>";
        } else {
            $editbutton = "";
        }
        return $editbutton;
    }

    /**
     * @return string sql to add to where statement.
     */
    function get_sql_where() {
        global $DB, $SESSION;

        $uniqueid = 'block_iomad_commerce_orders_table';

        if (isset($SESSION->flextable[$uniqueid])) {
            $prefs = $SESSION->flextable[$uniqueid];
        } else if (!$prefs = json_decode(get_user_preferences("flextable_{$uniqueid}", ''), true)) {
            return '';
        }

        $conditions = array();
        $params = array();

        static $i = 0;
        $i++;

        if (!empty($prefs['i_first'])) {
            $conditions[] = $DB->sql_like('u.firstname', ':ifirstc'.$i, false, false);
            $params['ifirstc'.$i] = $prefs['i_first'].'%';
        }
        if (!empty($prefs['i_last'])) {
            $conditions[] = $DB->sql_like('u.lastname', ':ilastc'.$i, false, false);
            $params['ilastc'.$i] = $prefs['i_last'].'%';
        }

        return array(implode(" AND ", $conditions), $params);
    }
}