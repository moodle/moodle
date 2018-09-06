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

require_once($CFG->dirroot . "/local/iomad/lib/company.php");

class block_iomad_company_selector extends block_base {

    public function init() {
        $this->title = get_string('title', 'block_iomad_company_selector');
    }

    public function has_config() {
        return false;
    }

    public function hide_header() {
        return false;
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT, $SESSION;

        // Only display if you have the correct capability.
        if (!iomad::has_capability('block/iomad_company_admin:company_add', context_system::instance())) {
            return;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!isloggedin()) {
            $this->content->text = get_string('pleaselogin', 'block_iomad_company_selector');
            return $this->content;
        }

        //  Check users session and profile settings to get the current editing company.
        if (!empty($SESSION->currenteditingcompany)) {
            $selectedcompany = $SESSION->currenteditingcompany;
        } else if (!empty($USER->profile->company)) {
            $usercompany = company::by_userid($USER->id);
            $selectedcompany = $usercompany->id;
        } else {
            $selectedcompany = "";
        }

        // Get the company name if set.
        if (!empty($selectedcompany)) {
            $companyname = company::get_companyname_byid($selectedcompany);
        } else {
            $companyname = "";
        }

        // Get a list of companies.
        $companylist = company::get_companies_select();
        $select = new single_select(new moodle_url('/my/index.php'), 'company', $companylist, $selectedcompany);
        $select->label = get_string('selectacompany', 'block_iomad_company_selector');
        $select->formid = 'choosecompany';
        $fwselectoutput = html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_company_selector'));
        $this->content->text = $OUTPUT->container_start('companyselect');
        if (!empty($SESSION->currenteditingcompany)) {
            $this->content->text .= '<p>'. get_string('currentcompanyname', 'block_iomad_company_selector', $companyname) .'</p>';
        } else {
            $this->content->text .= '<p>'. get_string('nocurrentcompany', 'block_iomad_company_selector').'</p>';
        }
        $this->content->text .= $fwselectoutput;
        $this->content->text .= $OUTPUT->container_end();

        return $this->content;
    }
}

