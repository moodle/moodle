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
 * Internal library of functions for module lightboxgallery
 *
 * All the newmodule specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 NetSpot Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/filelib.php");

define('THUMB_WIDTH', 150);
define('THUMB_HEIGHT', 150);
define('MAX_IMAGE_LABEL', 13);
define('MAX_COMMENT_PREVIEW', 20);

define('AUTO_RESIZE_SCREEN', 1);
define('AUTO_RESIZE_UPLOAD', 2);
define('AUTO_RESIZE_BOTH', 3);

/**
 * Add a set of uploaded files to the gallery.
 *
 * @param array $files A list of stored_file objects.
 * @param context $context
 * @param cm_info $cm
 * @param $gallery
 * @param int $resize
 * @access public
 * @return void
 */
function lightboxgallery_add_images($files, $context, $cm, $gallery, $resize = 0) {
    require_once(dirname(__FILE__).'/imageclass.php');

    $fs = get_file_storage();

    $images = array();
    foreach ($files as $storedfile) {
        if ($storedfile->get_mimetype() == 'application/zip') {
            // Unpack.
            $packer = get_file_packer('application/zip');
            $fs->delete_area_files($context->id, 'mod_lightboxgallery', 'unpacktemp', 0);
            $storedfile->extract_to_storage($packer, $context->id, 'mod_lightboxgallery', 'unpacktemp', 0, '/');
            $images = $fs->get_area_files($context->id, 'mod_lightboxgallery', 'unpacktemp', 0);
            $storedfile->delete();
        } else {
            $images[] = $storedfile;
        }
    }

    foreach ($images as $storedfile) {
        if ($storedfile->is_valid_image()) {
            $filename = $storedfile->get_filename();
            $fileinfo = array(
                'contextid'     => $context->id,
                'component'     => 'mod_lightboxgallery',
                'filearea'      => 'gallery_images',
                'itemid'        => 0,
                'filepath'      => '/',
                'filename'      => $filename
            );
            if (!$fs->get_file($context->id, 'mod_lightboxgallery', 'gallery_images', 0, '/', $filename)) {
                $storedfile = $fs->create_file_from_storedfile($fileinfo, $storedfile);
                $image = new lightboxgallery_image($storedfile, $gallery, $cm);

                if ($resize > 0) {
                    $resizeoptions = lightboxgallery_resize_options();
                    list($width, $height) = explode('x', $resizeoptions[$resize]);
                    $image->resize_image($width, $height);
                }

                $image->set_caption($filename);
            }
        }
    }
    $fs->delete_area_files($context->id, 'mod_lightboxgallery', 'unpacktemp', 0);
}

function lightboxgallery_config_defaults() {
    $defaults = array(
        'disabledplugins' => '',
        'enablerssfeeds' => 0,
    );

    $localcfg = get_config('lightboxgallery');

    foreach ($defaults as $name => $value) {
        if (! isset($localcfg->$name)) {
            set_config($name, $value, 'lightboxgallery');
        }
    }
}

function lightboxgallery_edit_types($showall = false) {
    $result = array();

    $disabledplugins = explode(',', get_config('lightboxgallery', 'disabledplugins'));

    // TODO: Remove this once crop functionality is working.
    $disabledplugins[] = 'crop';

    $edittypes = get_list_of_plugins('mod/lightboxgallery/edit');

    foreach ($edittypes as $edittype) {
        if ($showall || !in_array($edittype, $disabledplugins)) {
            $result[$edittype] = get_string('edit_' . $edittype, 'lightboxgallery');
        }
    }

    return $result;
}

function lightboxgallery_print_tags($heading, $tags, $courseid, $galleryid) {
    global $OUTPUT;

    echo $OUTPUT->box_start();

    echo '<form action="search.php" style="float: right; margin-left: 4px;">'.
         ' <fieldset class="invisiblefieldset">'.
         '  <input type="hidden" name="id" value="'.$courseid.'" />'.
         '  <input type="hidden" name="gallery" value="'.$galleryid.'" />'.
         '  <input type="text" name="search" size="8" />'.
         '  <input type="submit" value="'.get_string('search').'" />'.
         ' </fieldset>'.
         '</form>'.
         $heading.': ';

    $tagarray = array();
    foreach ($tags as $tag) {
        $tagparams = array('id' => $courseid, 'gallery' => $galleryid, 'search' => stripslashes($tag->description));
        $tagurl = new moodle_url('/mod/lightboxgallery/search.php', $tagparams);
        $tagarray[] = html_writer::link($tagurl, s($tag->description), array('class' => 'taglink'));
    }

    echo implode(', ', $tagarray);

    echo $OUTPUT->box_end();
}

function lightboxgallery_resize_options() {
    return array(1 => '1280x1024', 2 => '1024x768', 3 => '800x600', 4 => '640x480');
}

function lightboxgallery_index_thumbnail($courseid, $gallery, $newimage = null) {
    global $CFG;

    require_once(dirname(__FILE__).'/imageclass.php');
    $cm = get_coursemodule_from_instance("lightboxgallery", $gallery->id, $courseid);
    $context = context_module::instance($cm->id);

    $imageid = 'Gallery Index Image';

    $fs = get_file_storage();
    $storedfile = $fs->get_file($context->id, 'mod_lightboxgallery', 'gallery_index', '0', '/', 'index.png');

    if (!is_null($newimage) && is_object($storedfile)) { // Delete any existing index.
        $storedfile->delete();
    }
    if (is_object($storedfile) && is_null($newimage)) {
        // Grab the index.
        $index = $storedfile;
    } else {
        // Get first image and create an index for that.
        if (is_null($newimage)) {
            $files = $fs->get_area_files($context->id, 'mod_lightboxgallery', 'gallery_images');
            $file = array_shift($files);
            while (substr($file->get_mimetype(), 0, 6) != 'image/') {
                $file = array_shift($files);
            }
            $image = new lightboxgallery_image($file, $gallery, $cm);
        } else {
            $image = $newimage;
        }
        $index = $image->create_index();
    }
    $path = $CFG->wwwroot.'/pluginfile.php/'.$context->id.'/mod_lightboxgallery/gallery_index/'.
                $index->get_itemid().$index->get_filepath().$index->get_filename();

    return '<img src="' . $path . '" alt="" ' . (! empty($imageid) ? 'id="' . $imageid . '"' : '' )  . ' />';
}


/**
 * File browsing support class
 */
class lightboxgallery_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}
