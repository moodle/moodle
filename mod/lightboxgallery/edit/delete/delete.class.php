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

defined('MOODLE_INTERNAL') || die();

class edit_delete extends edit_base {

    public function __construct($gallery, $cm, $image, $tab) {
        parent::__construct($gallery, $cm, $image, $tab, true);
    }

    public function output() {
        global $page;
        $result = get_string('deletecheck', '', $this->image).'<br /><br />';
        $result .= '<input type="hidden" name="page" value="'.$page.'" />';
        $result .= '<input type="submit" value="'.get_string('yes').'" />';
        return $this->enclose_in_form($result);
    }

    public function process_form() {
        global $CFG, $page;
        $this->lbgimage->delete_file();
        redirect($CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$this->cm->id.'&page='.$page.'&editing=1');
    }

}
