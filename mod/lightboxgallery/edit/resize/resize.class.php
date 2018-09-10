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

class edit_resize extends edit_base {

    private $strresize;
    private $strscale;
    private $resizeoptions;

    public function __construct($gallery, $cm, $image, $tab) {
        parent::__construct($gallery, $cm, $image, $tab, true);
        $this->strresize = get_string('edit_resize', 'lightboxgallery');
        $this->strscale = get_string('edit_resizescale', 'lightboxgallery');
        $this->resizeoptions = lightboxgallery_resize_options();
    }

    public function output() {
        $fs = get_file_storage();
        $storedfile = $fs->get_file($this->context->id, 'mod_lightboxgallery', 'gallery_images', '0', '/', $this->image);
        $image = new lightboxgallery_image($storedfile, $this->gallery, $this->cm);

        $currentsize = sprintf('%s: %dx%d', get_string('currentsize', 'lightboxgallery'), $image->width, $image->height).
                       '<br /><br />';

        $sizeselect = '<select name="size">';
        foreach ($this->resizeoptions as $index => $option) {
            $sizeselect .= '<option value="' . $index . '">' . $option . '</option>';
        }
        $sizeselect .= '</select>&nbsp;<input type="submit" name="button" value="' . $this->strresize . '" /><br /><br />';

        $scaleselect = '<select name="scale">'.
                       '  <option value="200">200&#37;</option>'.
                       '  <option value="150">150&#37;</option>'.
                       '  <option value="125">125&#37;</option>'.
                       '  <option value="75">75&#37;</option>'.
                       '  <option value="50">50&#37;</option>'.
                       '  <option value="25">25&#37;</option>'.
                       '</select>&nbsp;<input type="submit" name="button" value="' . $this->strscale . '" />';

        return $this->enclose_in_form($currentsize . $sizeselect . $scaleselect);
    }

    public function process_form() {
        $button = required_param('button', PARAM_TEXT);

        $fs = get_file_storage();
        $storedfile = $fs->get_file($this->context->id, 'mod_lightboxgallery', 'gallery_images', '0', '/', $this->image);
        $image = new lightboxgallery_image($storedfile, $this->gallery, $this->cm);

        switch ($button) {
            case $this->strresize:
                $size = required_param('size', PARAM_INT);
                list($width, $height) = explode('x', $this->resizeoptions[$size]);
            break;
            case $this->strscale:
                $scale = required_param('scale', PARAM_INT);
                $width = $image->width * ($scale / 100);
                $height = $image->height * ($scale / 100);
            break;
        }

        $this->image = $image->resize_image($width, $height);
    }

}
