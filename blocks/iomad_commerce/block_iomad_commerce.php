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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->dirroot.'/blocks/iomad_commerce/lib.php');

/**
 *
 */

class block_iomad_commerce extends block_base {
    public function init() {
        $this->title = get_string('buycourses', 'block_iomad_commerce');
    }

    public function hide_header() {
        return false;
    }

    public function get_content() {
        global $CFG, $USER, $DB;

        // Hide the shop content if the user's company doesn't support ecommerce
        // Always show it if the user is a siteadmin
        // PWG
        $ecommerce = $DB->get_field_sql("SELECT c.ecommerce
                                         FROM {user} u
                                         JOIN {company_users} cu ON cu.userid = u.id
                                         JOIN {company} c ON cu.companyid = c.id
                                         WHERE u.id = :userid",
                                         array('userid' => $USER->id));

        if (!is_siteadmin() && !$ecommerce && !$CFG->commerce_admin_enableall) {
            return null;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if (!empty($CFG->commerce_enable_external)) {
            $link = $CFG->commerce_externalshop_url . "?userid=" . $USER->id . "&code=" . time() - $USER->currentlogin;
            $this->content->text = "<a class='btn' href='$link'>" . get_string('gotoshop', 'block_iomad_commerce') . '</a>';
        } else {
            // Has this been setup properly
            if (!is_commerce_configured()) {
                $link = new moodle_url('/admin/settings.php', array('section' => 'blocksettingiomad_commerce'));
                $this->content->text = '<div class="alert alert-danger">' . get_string('notconfigured', 'block_iomad_commerce', $link->out()) . '</div>';
                return $this->content;
            }
    
            $fatype = "fa-" . strtolower($CFG->commerce_admin_currency);
            $this->content->text = "<p><span class='fa $fatype'></span>"; 
            $this->content->text .= ' <a href="' . new moodle_url('/blocks/iomad_commerce/shop.php') .
                                   '">' . get_string('shop_title', 'block_iomad_commerce') . '</a></p>';
    
            $this->content->text .= get_basket_info();
        }

        return $this->content;
    }

    function has_config() {
        return true;
    }
}