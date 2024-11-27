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
 * Version information
 *
 * @package    local_fullpage
 * @copyright  Huseyin Yemen  - http://themesalmond.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Render page.
 * @param string $id
 */
function local_fullpage_render($id) {
    $templatecontext['pageenabled'] = true;
    // Advanced page.
    if (substr($id, 0, 1) == "a") {
        $templatecontext['pageenabled'] = get_config('local_fullpage', 'pageenabled');
        if (empty($templatecontext['pageenabled'])) {
            return $templatecontext;
        }
        $id = substr($id, 1, 2);
        $pagecount = get_config('local_fullpage', 'pagecount');
        if ( $id > $pagecount ) {
            $templatecontext['pageenabled'] = false;
            return $templatecontext;
        }
        if ($id > 0 && $id <= $pagecount) {
            $pagetitle = "pagetitle{$id}";
            $pagecss = "pagecss{$id}";
            $pagecsslink = "pagecsslink{$id}";
            $pagecap = "pagecap{$id}";
            $pagenavbar = "pagenavbar{$id}";
            $pageheader = "pageheader{$id}";
            $pagefooter = "pagefooter{$id}";
            $pageimglink = "pageimglink{$id}";
            if (!empty(get_config('local_fullpage', $pagetitle))) {
                $templatecontext['pagetitle'] = get_config('local_fullpage', $pagetitle);
            }
            if (!empty(get_config('local_fullpage', $pagecap))) {
                $templatecontext['pagecap'] = get_config('local_fullpage', $pagecap);
            } else {
                $templatecontext['pagecap'] = "<h1>".get_string('pagenotfound', 'local_fullpage')."</h1>";
            }
            if (!empty(get_config('local_fullpage', $pagecss))) {
                $templatecontext['pagecss'] = get_config('local_fullpage', $pagecss);
            }
            if (!empty(get_config('local_fullpage', $pagecsslink))) {
                $templatecontext['pagecsslink'] = get_config('local_fullpage', $pagecsslink);
            }
            $templatecontext['pageimglink'] = get_config('local_fullpage', $pageimglink);
            $templatecontext['pagenavbar'] = get_config('local_fullpage', $pagenavbar);
            $templatecontext['pageheader'] = get_config('local_fullpage', $pageheader);
            $templatecontext['pagefooter'] = get_config('local_fullpage', $pagefooter);
            $templatecontext['pageadvanced'] = true;
        } else {
            $templatecontext['pagecap'] = "<h1>".get_string('pagenotfound', 'local_fullpage')."</h1>";
        }
    } else if (substr($id, 0, 1) == "s") {
        global $CFG, $PAGE;
        $templatecontext['pageenabledsimple'] = get_config('local_fullpage', 'pageenabledsimple');
        $pagecount = get_config('local_fullpage', 'pagecountsimple');
        $id = substr($id, 1, 2);
        if ( $id > $pagecount ) {
            $templatecontext['pageenabled'] = false;
            return $templatecontext;
        }
        if (empty($templatecontext['pageenabledsimple'])) {
            return $templatecontext;
        }
        if ($id > 0 && $id <= $pagecount) {
            $pagetitle = "pagetitlesimple{$id}";
            $pagecap = "pagecapsimple{$id}";
            $pageheader = "pageheadersimple{$id}";
            $pagefooter = "pagefootersimple{$id}";
            $pageimage = "imgepagesimple{$id}";
            $pageimgposition = "pageimgpositionsimple{$id}";
            $syscontext = context_system::instance();
            $localplg = 'local_fullpage';
            if (!empty($pageimage)) {
                $localplgname = get_config('local_fullpage', $pageimage);
            }
            $itemid = theme_get_revision();
            $image = "";
            if (!empty($pageimage && $localplgname)) {
                $image = moodle_url::make_pluginfile_url($syscontext->id,
                            $localplg, $pageimage, $itemid, $localplgname, false, false);
            }
            if (!empty(get_config('local_fullpage', $pagetitle))) {
                $templatecontext['pagetitle'] = get_config('local_fullpage', $pagetitle);
            }
            if (!empty(get_config('local_fullpage', $pagecap))) {
                $templatecontext['pagecap'] = get_config('local_fullpage', $pagecap);
            } else {
                $templatecontext['pagecap'] = "<h1>".get_string('pagenotfound', 'local_fullpage')."</h1>";
            }

            $templatecontext['pageimg'] = $image;
            $templatecontext['pagenavbar'] = true;
            $templatecontext['pageheader'] = get_config('local_fullpage', $pageheader);
            $templatecontext['pagefooter'] = get_config('local_fullpage', $pagefooter);
            $templatecontext['pagesimple'] = true;
            $templatecontext['backgroundsimple'] = false;
            $templatecontext['topsimple'] = false;
            $templatecontext['leftsimple'] = false;
            $templatecontext['rightsimple'] = false;
            $templatecontext['fulltopsimple'] = false;
            if (get_config('local_fullpage', $pageimgposition) == "1") {
                $templatecontext['backgroundsimple'] = true;
            } else if (get_config('local_fullpage', $pageimgposition) == "2") {
                $templatecontext['topsimple'] = true;
            } else if (get_config('local_fullpage', $pageimgposition) == "21") {
                $templatecontext['fulltopsimple'] = true;
            } else if (get_config('local_fullpage', $pageimgposition) == "3") {
                $templatecontext['leftsimple'] = true;
            } else if (get_config('local_fullpage', $pageimgposition) == "4") {
                $templatecontext['rightsimple'] = true;
            }
        } else {
            $templatecontext['pagecap'] = "<h1>".get_string('pagenotfound', 'local_fullpage')."</h1>";
        }

    } else {
        $templatecontext['pageenabled'] = false;
        return $templatecontext;
    }
    return $templatecontext;
}
/**
 * Alert message.
 * @param string $message
 */
function local_fullpage_alertmessage ($message) {
    echo "
    <script>
        require(['core/notification’], function(notification) {
            notification.alert('Hello', 'Welcome to my site!', 'Continue');
        });
    </script>";
}
/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function local_fullpage_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && substr($filearea, 0, 3) === 'img') {
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return local_fullpage_setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        return false;
    }
}
/**
 * Serve the theme setting file.
 *
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool may terminate if file not found or donotdie not specified
 */
function local_fullpage_setting_file_serve($filearea, $args, $forcedownload, $options) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");
    $syscontext = context_system::instance();
    $component = 'local_fullpage';

    $revision = array_shift($args);
    if ($revision < 0) {
        $lifetime = 0;
    } else {
        $lifetime = 60 * 60 * 24 * 60;
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$syscontext->id}/{$component}/{$filearea}/0/{$relativepath}";
    $fullpath = rtrim($fullpath, '/');
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
        send_stored_file($file, $lifetime, 0, $forcedownload, $options);
        return true;
    } else {
        return false;
    }
}
