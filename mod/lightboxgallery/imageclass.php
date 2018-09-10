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

require_once($CFG->libdir.'/gdlib.php');

define('THUMBNAIL_WIDTH', 162);
define('THUMBNAIL_HEIGHT', 132);
define('LIGHTBOXGALLERY_POS_HID', 2);
define('LIGHTBOXGALLERY_POS_TOP', 1);
define('LIGHTBOXGALLERY_POS_BOT', 0);

/**
 * Main image class with all image manipulations as methods
 *
 * @package   mod_lightboxgallery
 * @copyright 2010 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lightboxgallery_image {

    private $cm;
    private $cmid;
    private $context;
    private $gallery;
    private $imageurl;

    // A quick lookup cache of this images metadata. Mainly useful during initial display.
    private $metadata = null;


    /**
     * The filepool object.
     *
     * @var stored_file
     */
    private $storedfile;
    private $tags;
    private $thumbnail;
    private $thumburl;

    public $height = null;
    public $width = null;

    public function __construct($storedfile, $gallery, $cm, $metadata = null, $thumbnail = false, $loadextrainfo = true) {
        global $CFG;

        $this->storedfile = &$storedfile;
        $this->gallery = &$gallery;
        $this->cm = &$cm;
        $this->cmid = $cm->id;
        $this->context = context_module::instance($cm->id);

        $this->imageurl = $CFG->wwwroot.'/pluginfile.php/'.$this->context->id.'/mod_lightboxgallery/gallery_images/'.
                           $this->storedfile->get_itemid().$this->storedfile->get_filepath().$this->storedfile->get_filename();
        $this->thumburl = $CFG->wwwroot.'/pluginfile.php/'.$this->context->id.'/mod_lightboxgallery/gallery_thumbs/0'.
                           $this->storedfile->get_filepath().$this->storedfile->get_filename().'.png';

        if ($loadextrainfo) {
            $imageinfo = $this->storedfile->get_imageinfo();
            $this->height = $imageinfo['height'];
            $this->width = $imageinfo['width'];
        }

        $this->thumbnail = $thumbnail;

        // If we weren't given a thumbnail, double check if it exists before generating one.
        if (!$thumbnail && (!$this->thumbnail = $this->get_thumbnail())) {
            $this->thumbnail = $this->create_thumbnail();
        }

        $this->metadata = $metadata;
    }

    public function add_tag($tag) {
        global $DB;

        $imagemeta = new stdClass();
        $imagemeta->gallery = $this->cm->instance;
        $imagemeta->image = $this->storedfile->get_filename();
        $imagemeta->metatype = 'tag';
        $imagemeta->description = $tag;

        return $DB->insert_record('lightboxgallery_image_meta', $imagemeta);
    }

    public function create_thumbnail($offsetx = 0, $offsety = 0) {
        $fileinfo = array(
            'contextid' => $this->context->id,
            'component' => 'mod_lightboxgallery',
            'filearea' => 'gallery_thumbs',
            'itemid' => 0,
            'filepath' => $this->storedfile->get_filepath(),
            'filename' => $this->storedfile->get_filename().'.png');

        ob_start();
        imagepng($this->get_image_resized(THUMBNAIL_HEIGHT, THUMBNAIL_WIDTH, $offsetx, $offsety));
        $thumbnail = ob_get_clean();

        $this->delete_thumbnail();
        $fs = get_file_storage();
        return $fs->create_file_from_string($fileinfo, $thumbnail);
    }

    public function create_index() {
        global $CFG;

        $fileinfo = array(
            'contextid' => $this->context->id,
            'component' => 'mod_lightboxgallery',
            'filearea' => 'gallery_index',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'index.png');

        $base = imagecreatefrompng($CFG->dirroot.'/mod/lightboxgallery/pix/index.png');
        $transparent = imagecolorat($base, 0, 0);

        $shrunk = imagerotate($this->get_image_resized(48, 48, 0, 0), 351, $transparent, 0);

        imagecolortransparent($base, $transparent);

        imagecopy($base, $shrunk, 2, 3, 0, 0, imagesx($shrunk), imagesy($shrunk));

        ob_start();
        imagepng($base);
        $index = ob_get_clean();

        $fs = get_file_storage();
        return $fs->create_file_from_string($fileinfo, $index);
    }

    public function delete_file($meta = true) {
        global $DB;

        $this->delete_thumbnail();

        // Delete all image_meta records for this file.
        if ($meta) {
            $DB->delete_records('lightboxgallery_image_meta', array(
                'gallery' => $this->cm->instance,
                'image' => $this->storedfile->get_filename()));
        }

        $this->storedfile->delete();
    }

    public function delete_tag($tag) {
        global $DB;

        return $DB->delete_records('lightboxgallery_image_meta', array('id' => $tag));
    }

    private function delete_thumbnail() {
        if (isset($this->thumbnail) && is_object($this->thumbnail)) {
            $this->thumbnail->delete();
            unset($this->thumbnail);
        }
    }

    public function flip_image($direction) {

        $fileinfo = array(
            'contextid'     => $this->context->id,
            'component'     => 'mod_lightboxgallery',
            'filearea'      => 'gallery_images',
            'itemid'        => 0,
            'filepath'      => $this->storedfile->get_filepath(),
            'filename'      => $this->storedfile->get_filename());

        ob_start();
        $fileinfo['filename'] = $this->output_by_mimetype($this->get_image_flipped($direction));
        $flipped = ob_get_clean();
        $this->delete_file(false);
        $fs = get_file_storage();
        $this->set_stored_file($fs->create_file_from_string($fileinfo, $flipped));

        $this->create_thumbnail();
    }

    private function get_editing_options() {
        global $CFG;

        $html = '<form action="'.$CFG->wwwroot.'/mod/lightboxgallery/imageedit.php" method="post"/>'.
                    '<input type="hidden" name="id" value="'.$this->cmid.'" />'.
                    '<input type="hidden" name="image" value="'.$this->storedfile->get_filename().'" />'.
                    '<input type="hidden" name="page" value="0" />'.
                    '<select name="tab" class="lightbox-edit-select" onchange="submit();">'.
                        '<option disabled selected>'.get_string('edit_choose', 'lightboxgallery').'</option>'.
                        '<option value="caption">'.get_string('edit_caption', 'lightboxgallery').'</option>'.
                        '<!--<option value="crop">'.get_string('edit_crop', 'lightboxgallery').'</option>-->'.
                        '<option value="delete">'.get_string('edit_delete', 'lightboxgallery').'</option>'.
                        '<option value="flip">'.get_string('edit_flip', 'lightboxgallery').'</option>'.
                        '<option value="resize">'.get_string('edit_resize', 'lightboxgallery').'</option>'.
                        '<option value="rotate">'.get_string('edit_rotate', 'lightboxgallery').'</option>'.
                        '<option value="tag">'.get_string('edit_tag', 'lightboxgallery').'</option>'.
                        '<option value="thumbnail">'.get_string('edit_thumbnail', 'lightboxgallery').'</option>'.
                    '</select>'.
                '</form>';

        return $html;
    }

    public function get_image_caption() {
        global $DB;
        $caption = '';

        if ($this->metadata !== null) {
            foreach ($this->metadata as $metarecord) {
                if ($metarecord->metatype == 'caption') {
                    return $metarecord->description;
                }
            }
        }

        if ($imagemeta = $DB->get_record('lightboxgallery_image_meta',
                array('gallery' => $this->gallery->id, 'image' => $this->storedfile->get_filename(), 'metatype' => 'caption'))) {
            $caption = $imagemeta->description;
        }

        return $caption;
    }

    public function get_image_display_html($editing = false) {
        if ($this->gallery->captionfull) {
            $caption = $this->get_image_caption();
        } else {
            $caption = lightboxgallery_resize_text($this->get_image_caption(), MAX_IMAGE_LABEL);
        }
        $timemodified = strftime(get_string('strftimedatetimeshort', 'langconfig'), $this->storedfile->get_timemodified());
        $filesize = round($this->storedfile->get_filesize() / 100) / 10;

        // Hide the caption.
        if ($this->gallery->captionpos == LIGHTBOXGALLERY_POS_HID) {
            $caption = ''; // Hide by cleaning the content (looks better than cleaning the whole div).
        }
        $posclass = ($this->gallery->captionpos == LIGHTBOXGALLERY_POS_TOP) ? 'top' : 'bottom';
        $captiondiv = html_writer::tag('div', $caption, array('class' => "lightbox-gallery-image-caption $posclass"));

        $html = '<div class="lightbox-gallery-image-container">'.
                    '<div class="lightbox-gallery-image-wrapper">'.
                        '<div class="lightbox-gallery-image-frame">';
        if ($this->gallery->captionpos == LIGHTBOXGALLERY_POS_TOP) {
            $html .= $captiondiv;
        }
        $html .= '<a class="lightbox-gallery-image-thumbnail" href="'.
                 $this->imageurl.'" rel="lightbox_gallery" title="'.$caption.
                 '" style="background-image: url(\''.$this->thumburl.
                 '\'); width: '.THUMBNAIL_WIDTH.'px; height: '.THUMBNAIL_HEIGHT.'px;"></a>';
        if ($this->gallery->captionpos == LIGHTBOXGALLERY_POS_BOT or $this->gallery->captionpos == LIGHTBOXGALLERY_POS_HID) {
            $html .= $captiondiv;
        }
        $html .= $this->gallery->extinfo ? '<div class="lightbox-gallery-image-extinfo">'.$timemodified.
                 '<br/>'.$filesize.'KB '.$this->width.'x'.$this->height.'px</div>' : '';
        $html .= ($editing ? $this->get_editing_options() : '');
        $html .= '</div>'.
                    '</div>'.
                '</div>';

        return $html;

    }

    private function get_image_flipped($direction) {
        $image = imagecreatefromstring($this->storedfile->get_content());
        $flipped = imagecreatetruecolor($this->width, $this->height);
        $w = $this->width;
        $h = $this->height;
        if ($direction == 'horizontal') {
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    imagecopy($flipped, $image, $x, $h - $y - 1, $x, $y, 1, 1);
                }
            }
        } else {
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    imagecopy($flipped, $image, $w - $x - 1, $y, $x, $y, 1, 1);
                }
            }
        }

        return $flipped;

    }

    private function get_image_resized($height = THUMBNAIL_HEIGHT, $width = THUMBNAIL_WIDTH, $offsetx = 0, $offsety = 0) {
        $image = imagecreatefromstring($this->storedfile->get_content());
        $resized = imagecreatetruecolor($width, $height);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        $cx = $this->width / 2;
        $cy = $this->height / 2;

        $ratiow = $width / $this->width;
        $ratioh = $height / $this->height;

        if ($ratiow < $ratioh) {
            $srcw = floor($width / $ratioh);
            $srch = $this->height;
            $srcx = floor($cx - ($srcw / 2)) + $offsetx;
            $srcy = $offsety;
        } else {
            $srcw = $this->width;
            $srch = floor($height / $ratiow);
            $srcx = $offsetx;
            $srcy = floor($cy - ($srch / 2)) + $offsety;
        }

        imagecopybicubic($resized, $image, 0, 0, $srcx, $srcy, $width, $height, $srcw, $srch);

        return $resized;

    }

    private function get_image_rotated($angle) {
        $image = imagecreatefromstring($this->storedfile->get_content());
        $rotated = imagerotate($image, $angle, 0);

        return $rotated;
    }

    public function get_image_url() {
        return $this->imageurl;
    }

    public function get_tags() {
        global $DB;

        if (isset($this->tags)) {
            return $this->tags;
        }

        $tags = [];
        if ($this->metadata !== null) {
            foreach ($this->metadata as $metarecord) {
                if ($metarecord->metatype == 'tag') {
                    $tags[$metarecord->id] = $metarecord;
                }
            }
        } else {
            $tags = $DB->get_records('lightboxgallery_image_meta',
                ['image' => $this->storedfile->get_filename(), 'metatype' => 'tag']);
        }

        return $this->tags = $tags;
    }

    private function get_thumbnail() {
        $fs = get_file_storage();

        if ($thumbnail = $fs->get_file($this->context->id, 'mod_lightboxgallery', 'gallery_thumbs', '0', '/',
                                       $this->storedfile->get_filename().'.png')) {
            return $thumbnail;
        }

        return false;
    }

    public function get_thumbnail_url() {
        return $this->thumburl;
    }

    protected function output_by_mimetype($gdcall) {
        if ($this->storedfile->get_mimetype() == 'image/png') {
            $imgfunc = 'imagepng';
        } else {
            $imgfunc = 'imagejpeg';
        }
        $imgfunc($gdcall);
        if ($this->storedfile->get_mimetype() == 'image/png') {
            return preg_replace('/\..+$/', '.png', $this->storedfile->get_filename());
        } else {
            return preg_replace('/\..+$/', '.jpg', $this->storedfile->get_filename());
        }
    }

    public function resize_image($width, $height) {
        $fileinfo = array(
            'contextid'     => $this->context->id,
            'component'     => 'mod_lightboxgallery',
            'filearea'      => 'gallery_images',
            'itemid'        => 0,
            'filepath'      => $this->storedfile->get_filepath(),
            'filename'      => $this->storedfile->get_filename());

        ob_start();
        $fileinfo['filename'] = $this->output_by_mimetype($this->get_image_resized($height, $width));
        $resized = ob_get_clean();

        $this->delete_file(false);
        $fs = get_file_storage();
        $this->storedfile = $fs->create_file_from_string($fileinfo, $resized);
        $imageinfo = $this->storedfile->get_imageinfo();
        $this->height = $imageinfo['height'];
        $this->width = $imageinfo['width'];

        $this->thumbnail = $this->create_thumbnail();

        return $fileinfo['filename'];
    }

    public function rotate_image($angle) {
        $fileinfo = array(
            'contextid'     => $this->context->id,
            'component'     => 'mod_lightboxgallery',
            'filearea'      => 'gallery_images',
            'itemid'        => 0,
            'filepath'      => $this->storedfile->get_filepath(),
            'filename'      => $this->storedfile->get_filename());

        ob_start();
        $fileinfo['filename'] = $this->output_by_mimetype($this->get_image_rotated($angle));
        $rotated = ob_get_clean();

        $this->delete_file(false);
        $fs = get_file_storage();
        $this->set_stored_file($fs->create_file_from_string($fileinfo, $rotated));

        $this->create_thumbnail();
        return $fileinfo['filename'];
    }

    public function set_caption($caption) {
        global $DB;

        $imagemeta = new stdClass();
        $imagemeta->gallery = $this->cm->instance;
        $imagemeta->image = $this->storedfile->get_filename();
        $imagemeta->metatype = 'caption';
        $imagemeta->description = $caption;

        if ($meta = $DB->get_record('lightboxgallery_image_meta', array('gallery' => $this->cm->instance,
                'image' => $this->storedfile->get_filename(), 'metatype' => 'caption'))) {
            $imagemeta->id = $meta->id;
            return $DB->update_record('lightboxgallery_image_meta', $imagemeta);
        } else {
            return $DB->insert_record('lightboxgallery_image_meta', $imagemeta);
        }
    }

    public function set_stored_file($storedfile) {
        $this->storedfile = $storedfile;
        $imageinfo = $this->storedfile->get_imageinfo();

        $this->height = $imageinfo['height'];
        $this->width = $imageinfo['width'];
    }
}
