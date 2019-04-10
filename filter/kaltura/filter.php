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
 * Kaltura filter script.
 *
 * @package    filter_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote-Learner.net Inc (http://www.remote-learner.net)
 */

class filter_kaltura extends moodle_text_filter {
    /** @var object $context The current page context. */
    public static $pagecontext = null;

    /** @var string $kafuri The KAF URI. */
    public static $kafuri = null;

    /** @var string $apiurl The URI used by the previous version (v3) of the plug-ins when embedding anchor tags. */
    public static $apiurl = null;

    /** @var string $module The module used to render part of the final URL. */
    public static $module = null;

    /** @var string $defaultheight The default height for the video. */
    public static $defaultheight = 280;

    /** @var string $defaultwidth The default width for the video. */
    public static $defaultwidth = 400;

    /**
     * This function runs once during a single page request and initialzies
     * some data.
     * @param object $page Moodle page object.
     * @param object $context Page context object.
     */
    public function setup($page, $context) {
        global $CFG;
        require_once($CFG->dirroot.'/local/kaltura/locallib.php');
        $configsettings = local_kaltura_get_config();

        self::$pagecontext = $this->get_course_context($context);

        $newuri = '';

        self::$kafuri = $configsettings->kaf_uri;

        if (!empty($configsettings->uri)) {
            self::$apiurl = $configsettings->uri;
        }

        self::$module = local_kaltura_get_endpoint(KAF_BROWSE_EMBED_MODULE);
    }

    /**
     * This function returns the course context where possible.
     * @param object $context A context object.
     * @return object A Moodle context object.
     */
    protected function get_course_context($context) {
        $coursecontext = null;

        if ($context instanceof context_course) {
            $coursecontext = $context;
        } else if ($context instanceof context_module) {
            $coursecontext = $context->get_course_context();
        } else {
            $coursecontext = context_system::instance();
        }

        return $coursecontext;
    }

    /**
     * This function does the work of converting text that matches a regular expression into
     * Kaltura video markup, so that links to Kaltura videos are displayed in the Kaltura
     * video player.
     * @param string $text Text that is to be displayed on the page.
     * @param array $options An array of additional options.
     * @return string The same text or modified text is returned.
     */
    public function filter($text, array $options = array()) {
        global $CFG;

        // Check if the the filter plug-in is enabled.
        if (empty($CFG->filter_kaltura_enable)) {
            return $text;
        }

        // Check either if the KAF URI or API URI has been set.  If neither has been set then return the text with no changes.
        if (is_null(self::$kafuri) && is_null(self::$apiurl)) {
            return $text;
        }

        // Performance shortcut.  All regexes bellow end with the </a> tag, if not present nothing can match.
        if (false  === stripos($text, '</a>')) {
            return $text;
        }

        // We need to return the original value if regex fails!
        $newtext = $text;

        // Search for v3 Kaltura embedded anchor tag format.
        $uri = self::$apiurl;
        $uri = rtrim($uri, '/');
        $uri = str_replace(array('.', '/', 'https'), array('\.', '\/', 'https?'), $uri);

        $oldsearch = '/<a\s[^>]*href="('.$uri.')\/index\.php\/kwidget\/wid\/_([0-9]+)\/uiconf_id\/([0-9]+)\/entry_id\/([\d]+_([a-z0-9]+))\/v\/flash"[^>]*>([^>]*)<\/a>/is';
        $newtext = preg_replace_callback($oldsearch, 'filter_kaltura_callback', $newtext);

        // Search for newer versoin of Kaltura embedded anchor tag format.
        $kafuri = self::$kafuri;
        $kafuri = rtrim($kafuri, '/');
        $kafuri = str_replace(array('http://', 'https://', '.', '/'), array('https?://', 'https?://', '\.', '\/'), $kafuri);

        $search = $search = '/<a\s[^>]*href="(((https?:\/\/'.KALTURA_URI_TOKEN.')|('.$kafuri.')))\/browseandembed\/index\/media\/entryid\/([\d]+_[a-z0-9]+)(\/([a-zA-Z0-9]+\/[a-zA-Z0-9]+\/)*)"[^>]*>([^>]*)<\/a>/is';
        $newtext = preg_replace_callback($search, 'filter_kaltura_callback', $newtext);

        if (empty($newtext) || $newtext === $text) {
            // Error or not filtered.
            unset($newtext);
            return $text;
        }

        return $newtext;
    }
}

/**
 * Change links to Kaltura into embedded Kaltura videos.
 * @param  array $link An array of elements matching the regular expression from class filter_kaltura - filter().
 * @return string Kaltura embed video markup.
 */
function filter_kaltura_callback($link) {
    $width = filter_kaltura::$defaultwidth;
    $height = filter_kaltura::$defaultheight;
    $source = '';

    // Convert KAF URI anchor tags into iframe markup.
    if (9 == count($link)) {
        // Get the height and width of the iframe.
        $properties = explode('||', $link[8]);

        $width = $properties[2];
        $height = $properties[3];

        if (4 != count($properties)) {
            return $link[0];
        }

        $source = filter_kaltura::$kafuri . '/browseandembed/index/media/entryid/' . $link[5] . $link[6];
    }

    // Convert v3 anchor tags into iframe markup.
    if (7 == count($link) && $link[1] == filter_kaltura::$apiurl) {
        $source = filter_kaltura::$kafuri.'/browseandembed/index/media/entryid/'.$link[4].'/playerSize/';
        $source .= filter_kaltura::$defaultwidth.'x'.filter_kaltura::$defaultheight.'/playerSkin/'.$link[3];
    }

    $params = array(
        'courseid' => filter_kaltura::$pagecontext->instanceid,
        'height' => $height,
        'width' => $width,
        'withblocks' => 0,
        'source' => $source

    );

    $url = new moodle_url('/filter/kaltura/lti_launch.php', $params);

    $iframe = html_writer::tag('iframe', '', array(
        'width' => $width,
        'height' => $height,
        'class' => 'kaltura-player-iframe',
        'allowfullscreen' => 'true',
        'allow' => 'autoplay *; fullscreen *; encrypted-media *; camera *; microphone *;',
        'src' => $url->out(false),
        'frameborder' => '0'
    ));

    $iframeContainer = html_writer::tag('div', $iframe, array(
        'class' => 'kaltura-player-container'
    ));

    return $iframeContainer;
}
