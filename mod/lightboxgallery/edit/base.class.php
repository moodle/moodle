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

/**
 * Base class to be extended for edit plugins
 *
 * @package   mod_lighboxgallery
 * @copyright 2010 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class edit_base {

    public $imageobj;

    public $gallery;
    public $image;
    public $tab;
    public $showthumb;
    public $context;

    public function __construct($gallery, $cm, $image, $tab, $showthumb = true) {
        $this->gallery = $gallery;
        $this->cm = $cm;
        $this->image = $image;
        $this->tab = $tab;
        $this->showthumb = $showthumb;
        $this->context = context_module::instance($this->cm->id);
    }

    public function processing() {
        return optional_param('process', false, PARAM_BOOL);
    }

    public function enclose_in_form($text) {
        global $CFG, $USER;

        return '<form action="'.$CFG->wwwroot.'/mod/lightboxgallery/imageedit.php" method="post">'.
               '<fieldset class="invisiblefieldset">'.
               '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />'.
               '<input type="hidden" name="id" value="'.$this->cm->id.'" />'.
               '<input type="hidden" name="image" value="'.$this->image.'" />'.
               '<input type="hidden" name="tab" value="'.$this->tab.'" />'.
               '<input type="hidden" name="process" value="1" />'.$text.'</fieldset></form>';
    }

    public function output() {

    }

    public function process_form() {

    }

}
