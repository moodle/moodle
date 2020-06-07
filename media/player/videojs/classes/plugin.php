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
 * Main class for plugin 'media_videojs'
 *
 * @package   media_videojs
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Player that creates HTML5 <video> tag.
 *
 * @package   media_videojs
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_videojs_plugin extends core_media_player_native {
    /** @var array caches last moodle_page used to include AMD modules */
    protected $loadedonpage = [];
    /** @var string language file to use */
    protected $language = 'en';
    /** @var array caches supported extensions */
    protected $extensions = null;
    /** @var bool is this a youtube link */
    protected $youtube = false;

    /**
     * Generates code required to embed the player.
     *
     * @param moodle_url[] $urls
     * @param string $name
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     */
    public function embed($urls, $name, $width, $height, $options) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $sources = array();
        $mediamanager = core_media_manager::instance();
        $datasetup = [];

        $text = null;
        $isaudio = null;
        $hastracks = false;
        $hasposter = false;
        if (array_key_exists(core_media_manager::OPTION_ORIGINAL_TEXT, $options) &&
                preg_match('/^<(video|audio)\b/i', $options[core_media_manager::OPTION_ORIGINAL_TEXT], $matches)) {
            // Original text already had media tag - get some data from it.
            $text = $options[core_media_manager::OPTION_ORIGINAL_TEXT];
            $isaudio = strtolower($matches[1]) === 'audio';
            $hastracks = preg_match('/<track\b/i', $text);
            $hasposter = self::get_attribute($text, 'poster') !== null;
        }

        // Currently Flash in VideoJS does not support responsive layout. If Flash is enabled try to guess
        // if HTML5 player will be engaged for the user and then set it to responsive.
        $responsive = (get_config('media_videojs', 'useflash') && !$this->youtube) ? null : true;
        $flashtech = false;

        // Build list of source tags.
        foreach ($urls as $url) {
            $extension = $mediamanager->get_extension($url);
            $mimetype = $mediamanager->get_mimetype($url);
            if ($mimetype === 'video/quicktime' && (core_useragent::is_chrome() || core_useragent::is_edge())) {
                // Fix for VideoJS/Chrome bug https://github.com/videojs/video.js/issues/423 .
                $mimetype = 'video/mp4';
            }
            // If this is RTMP stream, adjust mimetype to those VideoJS suggests to use (either flash or mp4).
            if ($url->get_scheme() === 'rtmp') {
                if ($mimetype === 'video/x-flv') {
                    $mimetype = 'rtmp/flv';
                } else {
                    $mimetype = 'rtmp/mp4';
                }
            }
            $source = html_writer::empty_tag('source', array('src' => $url, 'type' => $mimetype));
            $sources[] = $source;
            if ($isaudio === null) {
                $isaudio = in_array('.' . $extension, file_get_typegroup('extension', 'audio'));
            }
            if ($responsive === null) {
                $responsive = core_useragent::supports_html5($extension);
            }
            if (($url->get_scheme() === 'rtmp' || !core_useragent::supports_html5($extension))
                    && get_config('media_videojs', 'useflash')) {
                $flashtech = true;
            }
        }
        $sources = implode("\n", $sources);

        // Find the title, prevent double escaping.
        $title = $this->get_name($name, $urls);
        $title = preg_replace(['/&amp;/', '/&gt;/', '/&lt;/'], ['&', '>', '<'], $title);

        if ($this->youtube) {
            $datasetup[] = '"techOrder": ["youtube"]';
            $datasetup[] = '"sources": [{"type": "video/youtube", "src":"' . $urls[0] . '"}]';

            // Check if we have a time parameter.
            if ($time = $urls[0]->get_param('t')) {
                $datasetup[] = '"youtube": {"start": "' . self::get_start_time($time) . '"}';
            }

            $sources = ''; // Do not specify <source> tags - it may confuse browser.
            $isaudio = false; // Just in case.
        } else if ($flashtech) {
            $datasetup[] = '"techOrder": ["flash", "html5"]';
        }

        // Add a language.
        if ($this->language) {
            $datasetup[] = '"language": "' . $this->language . '"';
        }

        // Set responsive option.
        if ($responsive) {
            $datasetup[] = '"fluid": true';
        }

        if ($isaudio && !$hastracks) {
            // We don't need a full screen toggle for the audios (except when tracks are present).
            $datasetup[] = '"controlBar": {"fullscreenToggle": false}';
        }

        if ($isaudio && !$height && !$hastracks && !$hasposter) {
            // Hide poster area for audios without tracks or poster.
            // See discussion on https://github.com/videojs/video.js/issues/2777 .
            // Maybe TODO: if there are only chapter tracks we still don't need poster area.
            $datasetup[] = '"aspectRatio": "1:0"';
        }

        // Attributes for the video/audio tag.
        // We use data-setup-lazy as the attribute name for the config instead of
        // data-setup because data-setup will cause video.js to load the player as soon as the library is loaded,
        // which is BEFORE we have a chance to load any additional libraries (youtube).
        // The data-setup-lazy is just a tag name that video.js does not recognise so we can manually initialise
        // it when we are sure the dependencies are loaded.
        static $playercounter = 1;
        $attributes = [
            'data-setup-lazy' => '{' . join(', ', $datasetup) . '}',
            'id' => 'id_videojs_' . uniqid() . '_' . $playercounter++,
            'class' => get_config('media_videojs', $isaudio ? 'audiocssclass' : 'videocssclass')
        ];

        if (!$responsive) {
            // Note we ignore limitsize setting if not responsive.
            parent::pick_video_size($width, $height);
            $attributes += ['width' => $width] + ($height ? ['height' => $height] : []);
        }

        if (core_useragent::is_ios(10)) {
            // Hides native controls and plays videos inline instead of fullscreen,
            // see https://github.com/videojs/video.js/issues/3761 and
            // https://github.com/videojs/video.js/issues/3762 .
            // iPhone with iOS 9 still displays double controls and plays fullscreen.
            // iPhone with iOS before 9 display only native controls.
            $attributes += ['playsinline' => 'true'];
        }

        if ($text !== null) {
            // Original text already had media tag - add necessary attributes and replace sources
            // with the supported URLs only.
            if (($class = self::get_attribute($text, 'class')) !== null) {
                $attributes['class'] .= ' ' . $class;
            }
            $text = self::remove_attributes($text, ['id', 'width', 'height', 'class']);
            if (self::get_attribute($text, 'title') === null) {
                $attributes['title'] = $title;
            }
            $text = self::add_attributes($text, $attributes);
            $text = self::replace_sources($text, $sources);
        } else {
            // Create <video> or <audio> tag with necessary attributes and all sources.
            // We don't want fallback to another player because list_supported_urls() is already smart.
            // Otherwise we could end up with nested <audio> or <video> tags. Fallback to link only.
            $attributes += ['preload' => 'auto', 'controls' => 'true', 'title' => $title];
            $text = html_writer::tag($isaudio ? 'audio' : 'video', $sources . self::LINKPLACEHOLDER, $attributes);
        }

        // Limit the width of the video if width is specified.
        // We do not do it in the width attributes of the video because it does not work well
        // together with responsive behavior.
        if ($responsive) {
            self::pick_video_size($width, $height);
            if ($width) {
                $text = html_writer::div($text, null, ['style' => 'max-width:' . $width . 'px;']);
            }
        }

        return html_writer::div($text, 'mediaplugin mediaplugin_videojs d-block');
    }

    /**
     * Utility function that sets width and height to defaults if not specified
     * as a parameter to the function (will be specified either if, (a) the calling
     * code passed it, or (b) the URL included it).
     * @param int $width Width passed to function (updated with final value)
     * @param int $height Height passed to function (updated with final value)
     */
    protected static function pick_video_size(&$width, &$height) {
        if (!get_config('media_videojs', 'limitsize')) {
            return;
        }
        parent::pick_video_size($width, $height);
    }

    /**
     * Method to convert Youtube time parameter string, which can contain human readable time
     * intervals such as '1h5m', '1m10s', etc or a numeric seconds value
     *
     * @param string $timestr
     * @return int
     */
    protected static function get_start_time(string $timestr): int {
        if (is_numeric($timestr)) {
            // We can return the time string itself if it's already numeric.
            return (int) $timestr;
        }

        try {
            // Parse the time string as an ISO 8601 time interval.
            $timeinterval = new DateInterval('PT' . core_text::strtoupper($timestr));

            return ($timeinterval->h * HOURSECS) + ($timeinterval->i * MINSECS) + $timeinterval->s;
        } catch (Exception $ex) {
            // Invalid time interval.
            return 0;
        }
    }

    public function get_supported_extensions() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        if ($this->extensions === null) {
            // Get extensions set by user in UI config.
            $filetypes = preg_split('/\s*,\s*/',
                strtolower(trim(get_config('media_videojs', 'videoextensions') . ',' .
                get_config('media_videojs', 'audioextensions'))));

            $this->extensions = file_get_typegroup('extension', $filetypes);
            if ($this->extensions && !get_config('media_videojs', 'useflash')) {
                // If Flash is disabled get extensions supported by player that don't rely on flash.
                $supportedextensions = array_merge(file_get_typegroup('extension', 'html_video'),
                    file_get_typegroup('extension', 'html_audio'), file_get_typegroup('extension', 'media_source'));
                $this->extensions = array_intersect($this->extensions, $supportedextensions);
            }
        }
        return $this->extensions;
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        $result = [];
        // Youtube.
        $this->youtube = false;
        if (count($urls) == 1 && get_config('media_videojs', 'youtube')) {
            $url = reset($urls);

            // Check against regex.
            if (preg_match($this->get_regex_youtube(), $url->out(false), $this->matches)) {
                $this->youtube = true;
                return array($url);
            }
        }

        $extensions = $this->get_supported_extensions();
        $rtmpallowed = get_config('media_videojs', 'rtmp') && get_config('media_videojs', 'useflash');
        foreach ($urls as $url) {
            // If RTMP support is disabled, skip the URL that is using RTMP (which
            // might have been picked to the list by its valid extension).
            if (!$rtmpallowed && ($url->get_scheme() === 'rtmp')) {
                continue;
            }

            // If RTMP support is allowed, URL with RTMP scheme is supported irrespective to extension.
            if ($rtmpallowed && ($url->get_scheme() === 'rtmp')) {
                $result[] = $url;
                continue;
            }

            $ext = '.' . core_media_manager::instance()->get_extension($url);
            // Handle HLS and MPEG-DASH if supported.
            $isstream = in_array($ext, file_get_typegroup('extension', 'media_source'));
            if ($isstream && in_array($ext, $extensions) && core_useragent::supports_media_source_extensions($ext)) {
                $result[] = $url;
                continue;
            }

            if (!get_config('media_videojs', 'useflash')) {
                return parent::list_supported_urls($urls, $options);
            } else {
                // If Flash fallback is enabled we can not check if/when browser supports flash.
                // We assume it will be able to handle any other extensions that player supports.
                if (in_array($ext, $extensions)) {
                    $result[] = $url;
                }
            }
        }
        return $result;
    }

    /**
     * Default rank
     * @return int
     */
    public function get_rank() {
        return 2000;
    }

    /**
     * Tries to match the current language to existing language files
     *
     * Matched language is stored in $this->language
     *
     * @return string JS code with a setting
     */
    protected function find_language() {
        global $CFG;
        $this->language = current_language();
        $basedir = $CFG->dirroot . '/media/player/videojs/videojs/lang/';
        $langfiles = get_directory_list($basedir);
        $candidates = [];
        foreach ($langfiles as $langfile) {
            if (strtolower(pathinfo($langfile, PATHINFO_EXTENSION)) !== 'js') {
                continue;
            }
            $lang = basename($langfile, '.js');
            if (strtolower($langfile) === $this->language . '.js') {
                // Found an exact match for the language.
                $js = file_get_contents($basedir . $langfile);
                break;
            }
            if (substr($this->language, 0, 2) === strtolower(substr($langfile, 0, 2))) {
                // Not an exact match but similar, for example "pt_br" is similar to "pt".
                $candidates[$lang] = $langfile;
            }
        }
        if (empty($js) && $candidates) {
            // Exact match was not found, take the first candidate.
            $this->language = key($candidates);
            $js = file_get_contents($basedir . $candidates[$this->language]);
        }
        // Add it as a language for Video.JS.
        if (!empty($js)) {
            return "$js\n";
        }

        // Could not match, use default language of video player (English).
        $this->language = null;
        return "";
    }

    public function supports($usedextensions = []) {
        $supports = parent::supports($usedextensions);
        if (get_config('media_videojs', 'youtube')) {
            $supports .= ($supports ? '<br>' : '') . get_string('youtube', 'media_videojs');
        }
        if (get_config('media_videojs', 'rtmp') && get_config('media_videojs', 'useflash')) {
            $supports .= ($supports ? '<br>' : '') . get_string('rtmp', 'media_videojs');
        }
        return $supports;
    }

    public function get_embeddable_markers() {
        $markers = parent::get_embeddable_markers();
        // Add YouTube support if enabled.
        if (get_config('media_videojs', 'youtube')) {
            $markers = array_merge($markers, array('youtube.com', 'youtube-nocookie.com', 'youtu.be', 'y2u.be'));
        }
        // Add RTMP support if enabled.
        if (get_config('media_videojs', 'rtmp') && get_config('media_videojs', 'useflash')) {
            $markers[] = 'rtmp://';
        }

        return $markers;
    }

    /**
     * Returns regular expression used to match URLs for single youtube video
     * @return string PHP regular expression e.g. '~^https?://example.org/~'
     */
    protected function get_regex_youtube() {
        // Regex for standard youtube link.
        $link = '(youtube(-nocookie)?\.com/(?:watch\?v=|v/))';
        // Regex for shortened youtube link.
        $shortlink = '((youtu|y2u)\.be/)';

        // Initial part of link.
        $start = '~^https?://(www\.)?(' . $link . '|' . $shortlink . ')';
        // Middle bit: Video key value.
        $middle = '([a-z0-9\-_]+)';
        return $start . $middle . core_media_player_external::END_LINK_REGEX_PART;
    }

    /**
     * Setup page requirements.
     *
     * @param moodle_page $page The page we are going to add requirements to.
     */
    public function setup($page) {

        // Load dynamic loader. It will scan page for videojs media and load necessary modules.
        // Loader will be loaded on absolutely every page, however the videojs will only be loaded
        // when video is present on the page or added later to it in AJAX.
        $path = new moodle_url('/media/player/videojs/videojs/video-js.swf');
        $contents = 'videojs.options.flash.swf = "' . $path . '";' . "\n";
        $contents .= $this->find_language(current_language());
        $page->requires->js_amd_inline(<<<EOT
require(["media_videojs/loader"], function(loader) {
    loader.setUp(function(videojs) {
        $contents
    });
});
EOT
        );
    }
}
