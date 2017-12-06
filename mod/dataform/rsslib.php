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
 * This file contains all the common stuff to be used for RSS in the Dataform Plugin
 *
 * @package    mod_dataform
 * @category   rss
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generates the RSS Feed
 *
 * @param strClass $context the context the feed should be created under
 * @param array $args array of arguments to be used in the creation of the RSS Feed
 * @return NULL|string NULL if there is nothing to return, or the file path of the cached file if there is
 */
function dataform_rss_get_feed($context, $args) {
    global $CFG, $DB;

    // Check CFG->data_enablerssfeeds.
    if (empty($CFG->dataform_enablerssfeeds)) {
        debugging("DISABLED (module configuration)");
        return null;
    }

    // Validate context.
    $dataformid = clean_param($args[3], PARAM_INT);
    $cm = get_coursemodule_from_instance('dataform', $dataformid, 0, false, MUST_EXIST);
    if ($cm) {
        $modcontext = context_module::instance($cm->id);

        // Context id from db should match the submitted one.
        if ($context->id != $modcontext->id) {
            return null;
        }
    }

    // Check RSS enbabled for instance.
    $dataform = $DB->get_record('dataform', array('id' => $dataformid), '*', MUST_EXIST);
    if (!rss_enabled_for_mod('dataform', $dataform, false, false)) {
        return null;
    }

    // Get the target view.
    $viewid = clean_param($args[4], PARAM_INT);
    $viewdata = $DB->get_record('dataform_views', array('id' => $viewid), '*', MUST_EXIST);
    $viewman = mod_dataform_view_manager::instance($dataformid);
    $view = $viewman->get_view_by_id($viewid);
    // If (!($view instanceof 'mod_dataform\interfaces\rss')) {
        // return null;
    // }.

    // Get the cache file info
    // The cached file name is formatted dfid_viewid_contentstamp,
    // where contentstamp is provided by the view.
    $componentid = $dataformid. "_$viewid";
    $cachedfilepath = dataform_rss_get_cached_file_path($componentid);
    $contentstamp = $cachedfilepath ? dataform_rss_get_cached_content_stamp($cachedfilepath) : null;

    $newcontentstamp = $view->get_content_stamp();
    $hasnewcontent = ($newcontentstamp !== $contentstamp);

    // Neither existing nor new.
    if (!$cachedfilepath and !$hasnewcontent) {
        return null;
    }

    if ($cachedfilepath) {
        // Use cache under 60 seconds.
        $cachetime = filemtime($cachedfilepath);
        if ((time() - $cachetime) < 60) {
            return $cachedfilepath;
        }

        // Use cache if there is nothing new.
        if (!$hasnewcontent) {
            return $cachedfilepath;
        }

        // Cached file is outdated so delete it.
        $instance = (object) array('id' => $componentid);
        rss_delete_file('mod_dataform', $instance);
    }

    // Still here, fetch new content.
    // Each article is an stdclass {title, descrition, pubdate, entrylink}.
    if (!$items = $view->get_rss_items()) {
        return null;
    }

    // First all rss feeds common headers.
    $headertitle = $view->get_rss_header_title();
    $headerlink = $view->get_rss_header_link();
    $headerdescription = $view->get_rss_header_description();
    $header = rss_standard_header($headertitle, $headerlink, $headerdescription);
    if (empty($header)) {
        return null;
    }

    // Now all rss feeds common footers.
    $footer = rss_standard_footer();
    if (empty($footer)) {
        return null;
    }

    // All's good, save the content to file.
    $articles = rss_add_items($items);
    $rss = $header.$articles.$footer;
    $filename = $componentid. "_$newcontentstamp";
    $status = rss_save_file('mod_dataform', $filename, $rss);
    $dirpath = dataform_rss_get_cache_dir_path();
    return "$dirpath/$filename.xml";
}

/**
 * Gets the cached file path if file exists
 *
 * @param string $componentid the prefix identifier of the file name
 * @return NULL|string NULL if there is nothing to return, or the file path of the cached file if there is
 */
function dataform_rss_get_cache_dir_path() {
    global $CFG;

    return "$CFG->cachedir/rss/mod_dataform";
}

/**
 * Gets the cached file path if file exists
 *
 * @param string $componentid the prefix identifier of the file name
 * @return NULL|string NULL if there is nothing to return, or the file path of the cached file if there is
 */
function dataform_rss_get_cached_file_path($componentid) {
    $dirpath = dataform_rss_get_cache_dir_path();
    $files = glob("$dirpath/{$componentid}_*.xml");

    if (empty($files)) {
        return null;
    }

    // There should be only one. At any rate all cached files for the componentid are deleted
    // when the cache is refreshed.
    $cachefilename = reset($files);
    return $cachefilename;
}

/**
 * Gets the cached content stamp from the filename, that is the third _ delimited argument of
 * cached file name.
 *
 * @param string $cachedfilepath the full path of the cached file
 * @return NULL|string NULL if there is nothing to return, or the file path of the cached file if there is
 */
function dataform_rss_get_cached_content_stamp($cachedfilepath) {
    if (empty($cachedfilepath)) {
        return null;
    }
    $filename = pathinfo($cachedfilepath, PATHINFO_FILENAME);
    // Strip the .xml extension.
    if (substr($filename, -4) === '.xml') {
        $filename = substr($filename, 0, strlen($filename) - 4);
    }
    list(, , $contentstamp) = explode('_', $filename);

    return $contentstamp;
}
