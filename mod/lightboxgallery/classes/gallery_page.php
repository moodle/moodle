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

namespace mod_lightboxgallery;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/../imageclass.php');

class gallery_page {

    private $cm;
    private $editing;
    private $files;
    private $gallery;
    private $imagecount;
    private $metadata = [];
    private $page = 0;
    private $pagefiles = [];
    private $pagethumbs = [];
    private $thumbnails;

    /**
     * Gallery view constructor.
     *
     * @param cm_info $cm
     * @param stdClass $gallery
     * @param bool $editing
     * @param int $page Which page are we viewing.
     */
    public function __construct($cm, $gallery, $editing = false, $page = 0) {
        $this->cm = $cm;
        $this->editing = $editing;
        $this->gallery = $gallery;
        $this->page = $page;

        $fs = get_file_storage();
        $this->files = $fs->get_area_files($this->cm->context->id, 'mod_lightboxgallery', 'gallery_images');
        $this->thumbnails = $fs->get_area_files($this->cm->context->id, 'mod_lightboxgallery', 'gallery_thumbs');
        $this->load_metadata();
    }

    public function display_images() {
        $html = '';
        foreach ($this->pagefiles as $filename => $file) {
            $image = new \lightboxgallery_image($file, $this->gallery, $this->cm,
                $this->metadata[$filename], $this->pagethumbs[$filename], $this->gallery->extinfo);
            $html .= $image->get_image_display_html($this->editing);
        }
        return $html;
    }

    protected function load_metadata() {
        global $DB;

        $filenames = [];
        $this->imagecount = 0;
        foreach ($this->files as $storedfile) {
            if (!$storedfile->is_valid_image()) {
                continue;
            }

            $this->imagecount++;

            if ($this->gallery->perpage > 0) {
                if ($this->imagecount > (($this->gallery->perpage * $this->page) + $this->gallery->perpage)) {
                    // We've already found all the images to display on this page.
                    break;
                } else if ($this->imagecount <= ($this->gallery->perpage * $this->page)) {
                    // We haven't gotten to the first image of this page yet.
                    continue;
                }
            }

            $filename = $storedfile->get_filename();
            $filenames[] = $filename;
            $this->pagefiles[$filename] = $storedfile;
            $this->pagethumbs[$filename] = false;
            $this->metadata[$filename] = [];
        }

        foreach ($this->thumbnails as $thumbnail) {
            // Thumbnails have ".png" suffixed in the filepool.
            $filename = substr($thumbnail->get_filename(), 0, -4);
            $this->pagethumbs[$filename] = $thumbnail;
        }

        if (!$filenames) {
            return;
        }

        list ($insql, $params) = $DB->get_in_or_equal($filenames, SQL_PARAMS_NAMED);
        $params['gallery'] = $this->gallery->id;
        $select = "gallery = :gallery AND image $insql";
        $metadata = $DB->get_records_select('lightboxgallery_image_meta', $select, $params);

        // Store the records keyed on the image name.
        foreach ($metadata as $metarecord) {
            $this->metadata[$metarecord->image][] = $metarecord;
        }
    }

    public function image_count() {
        return $this->imagecount;
    }
}