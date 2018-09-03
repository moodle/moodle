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
 * Essential is a clean and customizable theme.
 *
 * Analytics
 *
 * This module provides extensive analytics on a platform of choice,
 * currently support Google Analytics and Piwik.
 *
 * @package    theme_essential
 * @copyright  2016 Gareth J Barnard
 * @copyright  2014 David Bezemer <info@davidbezemer.nl>, www.davidbezemer.nl
 * @author     David Bezemer <info@davidbezemer.nl>, Bas Brands <bmbrands@gmail.com>, Gavin Henrick <gavin@lts.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function theme_essential_uv_analytics_trackurl() {
    global $DB, $PAGE;
    $pageinfo = get_context_info_array($PAGE->context->id);
    // Adds page title.
    $trackurl = "'";

    if ((isset($pageinfo[1]->category)) || (isset($pageinfo[1]->fullname)) || (isset($pageinfo[2]->name))) {
        // Adds course category name.
        if (isset($pageinfo[1]->category)) {
            if ($category = $DB->get_record('course_categories', array('id' => $pageinfo[1]->category))) {
                $cats = explode("/", $category->path);
                foreach (array_filter($cats) as $cat) {
                    if ($categorydepth = $DB->get_record("course_categories", array("id" => $cat))) {
                        $trackurl .= $categorydepth->name . '/';
                    }
                }
            }
        }

        // Adds course full name.
        if (isset($pageinfo[1]->fullname)) {
            if (isset($pageinfo[2]->name)) {
                $trackurl .= $pageinfo[1]->fullname . '/';
            } else if ($PAGE->user_is_editing()) {
                $trackurl .= $pageinfo[1]->fullname . '/' . get_string('edit');
            } else {
                $trackurl .= $pageinfo[1]->fullname . '/' . get_string('view');
            }
        }

        // Adds activity name.
        if (isset($pageinfo[2]->name)) {
            $trackurl .= $pageinfo[2]->modname . '/' . $pageinfo[2]->name;
        }
    } else {
        $trackurl .= $PAGE->title;
    }

    $trackurl .= "'";
    return $trackurl;
}

function theme_essential_uv_insert_analytics_tracking() {
    $siteurl = \theme_essential_uv\toolbox::get_setting('analyticssiteurl');
    $tracking = '';

    if (!empty($siteurl)) {
        $imagetrack = \theme_essential_uv\toolbox::get_setting('analyticsimagetrack');
        $siteid = \theme_essential_uv\toolbox::get_setting('analyticssiteid');
        $trackadmin = \theme_essential_uv\toolbox::get_setting('analyticstrackadmin');
        $useuserid = \theme_essential_uv\toolbox::get_setting('analyticsuseuserid');
        $cleanurl = \theme_essential_uv\toolbox::get_setting('analyticscleanurl');

        if ($imagetrack) {
            $addition = '<noscript><p><img src="//'.$siteurl.'/piwik.php?idsite='.$siteid;
            $addition .= '" style="border:0" alt="" /></p></noscript>';
        } else {
            $addition = '';
        }

        if ($useuserid) {
            global $USER;
            if ($USER->id) {
                $userid = "".PHP_EOL."_paq.push(['setUserId', '".$USER->id."']);";
            } else {
                $userid = "";
            }
        } else {
            $userid = "";
        }

        if ($cleanurl) {
            $doctitle = "".PHP_EOL."_paq.push(['setDocumentTitle', " . theme_essential_uv_analytics_trackurl() . "]);";
        } else {
            $doctitle = "";
        }

        if (!is_siteadmin() || $trackadmin) {
            $tracking = "
<script type='text/javascript'>
var _paq = _paq || [];".$doctitle."
_paq.push(['enableLinkTracking']);".$userid."
_paq.push(['trackPageView']);
(function(){
    var u=(('https:' == document.location.protocol) ? 'https' : 'http') + '://" . $siteurl . "/';
    _paq.push(['setSiteId', " . $siteid . "]);
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    var d=document,
        g=d.createElement('script'),
        s=d.getElementsByTagName('script')[0];
    g.type='text/javascript';
    g.defer=true;
    g.async=true;
    g.src=u+'piwik.js';
    s.parentNode.insertBefore(g,s);
})();
</script>".$addition;
        }
    }
    return $tracking;
}

echo theme_essential_uv_insert_analytics_tracking();