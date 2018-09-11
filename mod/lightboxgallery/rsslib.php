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
 * Handles all the RSS related tasks for the module
 *
 * @package   mod_lightboxgallery
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');
require_once('imageclass.php');

/**
 * Returns the path to the cached rss feed contents. Creates/updates the cache if necessary.
 * @global object $CFG
 * @global object $DB
 * @param object $context the context
 * @param array $args the arguments received in the url
 * @return string the full path to the cached RSS feed directory. Null if there is a problem.
 */
function lightboxgallery_rss_get_feed($context, $args) {
    global $CFG, $DB;

    $config = get_config('lightboxgallery');

    $status = true;

    // Are RSS feeds enabled?
    if (empty($config->enablerssfeeds)) {
        debugging('DISABLED (module configuration)');
        return null;
    }

    $galleryid = clean_param($args[3], PARAM_INT);
    $cm = get_coursemodule_from_instance('lightboxgallery', $galleryid, 0, false, MUST_EXIST);
    if ($cm) {
        $modcontext = context_module::instance($cm->id);

        // Context id from db should match the submitted one.
        if ($context->id != $modcontext->id) {
            return null;
        }
    }

    $gallery = $DB->get_record('lightboxgallery', array('id' => $galleryid), '*', MUST_EXIST);

    $captions = array();
    if ($cobjs = $DB->get_records('lightboxgallery_image_meta',  array('metatype' => 'caption', 'gallery' => $gallery->id))) {
        foreach ($cobjs as $cobj) {
            $captions[$cobj->image] = $cobj->description;
        }
    }

    $fs = get_file_storage();
    $storedfiles = $fs->get_area_files($context->id, 'mod_lightboxgallery', 'gallery_images');

    $items = array();
    $counter = 1;
    $articles = '';
    foreach ($storedfiles as $file) {
        $filename = $file->get_filename();
        if ($filename == '.') {
            continue;
        }
        $description = isset($captions[$filename]) ? $captions[$filename] : $filename;
        $image = new lightboxgallery_image($file, $gallery, $cm);
        $item = new stdClass();
        $item->{"media:description"} = $description;

        $articles .= rss_start_tag('item', 2, true);
        $articles .= rss_full_tag('title', 3, false, $filename);
        $articles .= rss_full_tag('link', 3, false, $image->get_image_url());
        $articles .= rss_full_tag('guid', 3, false, 'img' . $counter);

        $articles .= rss_full_tag('media:description', 3, false, $description);
        $articles .= rss_full_tag('media:thumbnail', 3, false, '', array('url' => $image->get_thumbnail_url()));
        $articles .= rss_full_tag('media:content', 3, false, '',
                        array('url' => $image->get_image_url(), 'type' => $file->get_mimetype()));

        $articles .= rss_end_tag('item', 2, true);

    }

    // Get the cache file info.
    $filename = rss_get_file_name($gallery, $sql);
    $cachedfilepath = rss_get_file_full_name('mod_lightboxgallery', $filename);

    // Is the cache out of date?
    $cachedfilelastmodified = 0;
    if (file_exists($cachedfilepath)) {
        $cachedfilelastmodified = filemtime($cachedfilepath);
    }

    // First all rss feeds common headers.
    $header = lightboxgallery_rss_header(format_string($gallery->name, true),
                                  $CFG->wwwroot."/mod/lightboxgallery/view.php?id=".$cm->id,
                                  format_string($gallery->intro, true));

    // Now all rss feeds common footers.
    if (!empty($header) && !empty($articles)) {
        $footer = rss_standard_footer();
    }
    // Now, if everything is ok, concatenate it.
    if (!empty($header) && !empty($articles) && !empty($footer)) {
        $rss = $header.$articles.$footer;

        // Save the XML contents to file.
        $status = rss_save_file('mod_lightboxgallery', $filename, $rss);
    }

    if (!$status) {
        $cachedfilepath = null;
    }

    return $cachedfilepath;
}

function lightboxgallery_rss_feeds() {
    global $CFG;

    $status = true;

    if (! $CFG->enablerssfeeds) {
        debugging('DISABLED (admin variables)');
    } else if (! get_config('lightboxgallery', 'enablerssfeeds')) {
        debugging('DISABLED (module configuration)');
    } else {
        if ($galleries = $DB->get_records('lightboxgallery')) {
            foreach ($galleries as $gallery) {
                if ($gallery->rss && $status) {

                    $filename = rss_file_name('lightboxgallery', $gallery);

                    if (file_exists($filename)) {
                        if ($lastmodified = filemtime($filename)) {
                            if ($lastmodified > time() - HOURSECS) {
                                continue;
                            }
                        }
                    }

                    if (!instance_is_visible('lightboxgallery', $gallery)) {
                        if (file_exists($filename)) {
                            @unlink($filename);
                        }
                        continue;
                    }

                    mtrace('Updating RSS feed for ' . format_string($gallery->name, true) . ', ID: ' . $gallery->id);

                    $result = lightboxgallery_rss_feed($gallery);

                    if (! empty($result)) {
                        $status = rss_save_file('lightboxgallery', $gallery, $result);
                    }

                    if (debugging()) {
                        if (empty($result)) {
                            echo('ID: ' . $gallery->id . '-> (empty) ');
                        } else {
                            if (! empty($status)) {
                                echo('ID: ' . $gallery->id . '-> OK ');
                            } else {
                                echo('ID: ' . $gallery->id . '-> FAIL ');
                            }
                        }
                    }

                }
            }
        }
    }

    return $status;
}

function lightboxgallery_rss_get_sql($glossary, $time=0) {
    // Do we only want new items?
    if ($time) {
        $time = "AND e.timecreated > $time";
    } else {
        $time = "";
    }

    $sql = '';

    return $sql;
}

function lightboxgallery_rss_header($title = null, $link = null, $description = null) {
    global $CFG, $USER, $OUTPUT;

    $status = true;
    $result = "";

    $site = get_site();

    if ($status) {

        // Calculate title, link and description.
        if (empty($title)) {
            $title = format_string($site->fullname);
        }
        if (empty($link)) {
            $link = $CFG->wwwroot;
        }
        if (empty($description)) {
            $description = $site->summary;
        }

        // XML headers.
        $result .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $result .= "<rss version=\"2.0\" xmlns:media=\"http://search.yahoo.com/mrss\"".
                    "xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";

        // Open the channel.
        $result .= rss_start_tag('channel', 1, true);

        // Write channel info.
        $result .= rss_full_tag('title', 2, false, strip_tags($title));
        $result .= rss_full_tag('link', 2, false, $link);
        $result .= rss_full_tag('description', 2, false, $description);
        $result .= rss_full_tag('generator', 2, false, 'Moodle');
        if (!empty($USER->lang)) {
            $result .= rss_full_tag('language', 2, false, substr($USER->lang, 0, 2));
        }
        $today = getdate();
        $result .= rss_full_tag('copyright', 2, false, '&#169; '. $today['year'] .' '. format_string($site->fullname));

        // Write image info.
        $rsspix = $OUTPUT->image_url('i/rsssitelogo');

        // Write the info.
        $result .= rss_start_tag('image', 2, true);
        $result .= rss_full_tag('url', 3, false, $rsspix);
        $result .= rss_full_tag('title', 3, false, 'moodle');
        $result .= rss_full_tag('link', 3, false, $CFG->wwwroot);
        $result .= rss_full_tag('width', 3, false, '140');
        $result .= rss_full_tag('height', 3, false, '35');
        $result .= rss_end_tag('image', 2, true);
    }

    if (!$status) {
        return false;
    } else {
        return $result;
    }
}
