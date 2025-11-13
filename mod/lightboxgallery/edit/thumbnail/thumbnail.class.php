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

class edit_thumbnail extends edit_base {

    public function __construct($gallery, $cm, $image, $tab) {
        parent::__construct($gallery, $cm, $image, $tab, true);
    }

    public function output() {
        $result = '<input type="submit" class="btn btn-secondary" name="index" value="' .
            get_string('setasindex', 'lightboxgallery') . '" /><br /><br />' .
            get_string('selectthumbpos', 'lightboxgallery') . '<br /><br />';

        if ($this->lbgimage->width < $this->lbgimage->height) {
            $result .= '<label class="me-3"><input type="radio" class="form-check-input me-1" name="move" value="1" />'.
                       get_string('dirup', 'lightboxgallery') . '</label>&nbsp;'.
                       '<label><input type="radio" class="form-check-input me-1" name="move" value="2" />'.
                       get_string('dirdown', 'lightboxgallery') . '</label>';
        } else {
            $result .= '<label class="me-3"><input type="radio" class="form-check-input me-1"  name="move" value="3" />'.
                       get_string('dirleft', 'lightboxgallery') . '</label>&nbsp;'.
                       '<label><input type="radio" class="form-check-input me-1" name="move" value="4" />'.
                       get_string('dirright', 'lightboxgallery') . '</label>';
        }
        $result .= '<br /><br /><div class="d-flex flex-wrap align-items-center"><label for="offset" class="me-1">' .
                   get_string('thumbnailoffset', 'lightboxgallery').
                   ':</label> <input type="text" class="form-control" name="offset" value="20" size="4" /></div><br /><br />'.
                   '<input type="submit" class="btn btn-secondary" value="' . get_string('move').
                   '" />&nbsp;<input type="submit" class="btn btn-secondary" name="reset" value="' . get_string('reset') . '" />';

        return $this->enclose_in_form($result);
    }

    public function process_form() {
        $domove = true;

        if (optional_param('index', '', PARAM_TEXT)) {
            return lightboxgallery_index_thumbnail($this->gallery->course, $this->gallery, $this->lbgimage);
        } else if (optional_param('reset', '', PARAM_TEXT)) {
            $offsetx = 0;
            $offsety = 0;
        } else {
            $move = optional_param('move', -1, PARAM_INT);
            $offset = optional_param('offset', 20, PARAM_INT);
            switch ($move) {
                case 1:
                    $offsetx = 0;
                    $offsety = -$offset;
                    break;
                case 2:
                    $offsetx = 0;
                    $offsety = $offset;
                    break;
                case 3:
                    $offsetx = -$offset;
                    $offsety = 0;
                    break;
                case 4:
                    $offsetx = $offset;
                    $offsety = 0;
                    break;
                default:
                    $domove = false;
            }
        }

        if ($domove) {
            $this->lbgimage->create_thumbnail($offsetx, $offsety);
        }
    }

}
