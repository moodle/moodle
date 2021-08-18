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
 * Main class for 'media_jwplayer'.
 *
 * @package   media_jwplayer
 * @copyright 2017 Ruslan Kabalin, Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

/**
 *  JW Player media plugin.
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_jwplayer_plugin extends core_media_player {
    /** @var bool is this called from mobile app. */
    protected $ismobileapp = false;

    /** Player width in responsive mode. */
    const VIDEO_WIDTH_RESPONSIVE = '100%';
    /** Default aspect ratio in responsive mode. */
    const VIDEO_ASPECTRATIO = '16:9';
    /** Player height required to enable audio mode. */
    const AUDIO_HEIGHT = '40';

    /**
     * Generates code required to embed the player.
     *
     * @param array $urls Moodle URLs of media files
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    public function embed($urls, $name, $width, $height, $options) {
        global $CFG;

        // Process tag and populate options.
        $playeroptions = ['globalattributes' => []];
        if (!empty($options[core_media_manager::OPTION_ORIGINAL_TEXT])) {
            // Determine the type of media tag.
            preg_match('/^<(video|audio|a)\b/i', $options[core_media_manager::OPTION_ORIGINAL_TEXT], $matches);
            $tagtype = $matches[1];

            if ($tagtype === 'video' || $tagtype === 'audio') {
                // This is HTML5 media tag.
                $playeroptions = $this->get_options_from_media_tag($options[core_media_manager::OPTION_ORIGINAL_TEXT]);
                // Using <audio> tag will result in audio player mode irrspective to source mime type.
                $playeroptions['isaudio'] = ($tagtype === 'audio');
            } else if ($tagtype === 'a') {
                // This is <a> tag.
                // Create attribute options if we don't already have them.
                if (empty($options['htmlattributes'])) {
                    $xml = new SimpleXMLElement($options[core_media_manager::OPTION_ORIGINAL_TEXT]);
                    foreach ($xml->attributes() as $attrib => $atval) {
                        $attrib = clean_param($attrib, PARAM_ALPHAEXT);
                        $atval = clean_param(htmlspecialchars_decode($atval), PARAM_RAW);
                        $options['htmlattributes'][$attrib] = $atval;
                    }
                }
                // Process tag attributes.
                $playeroptions = $this->get_options_from_a_tag_attributes($options['htmlattributes']);
            }
        } else {
            // We don't have original text if $mediamanager->embed_url called directly
            // (e.g. this happens when media URL is embedded in mod_url).
            // In this case treat it as <a href=...> tag.
            $tagtype = 'a';
        }

        if ($this->ismobileapp) {
            // We can't use JWPlayer in Moodle mobile app as we are not able to
            // make JS modules initialised. Check if we can fallback to html5 video/audio.
            require_once($CFG->libdir . '/filelib.php');
            $supportedextensions = file_get_typegroup('extension', ['html_video', 'html_audio']);
            $manager = core_media_manager::instance();
            $sources = [];
            $isaudio = $playeroptions['isaudio'] ?? null;
            // Check URLs if they can be used for html5. Even if we had html5 video source,
            // we go through links anyway to add mimetype.
            foreach ($urls as $url) {
                // Get extension and mimetype.
                $ext = $manager->get_extension($url);
                $mimetype = $manager->get_mimetype($url);

                if ($ext === 'm3u8') {
                    // HLS. Only reason we do mimetype overriding here is because setting
                    // $CFG->customfiletypes temporarly won't change file_get_typegroup()
                    // output presumably because of caching.
                    $mimetype = 'application/x-mpegURL';
                } else if (!in_array('.' . $ext, $supportedextensions)) {
                    // Extension is not supported for use in html5 video/audio.
                    continue;
                }

                if ($isaudio === null) {
                    // Flag is we deal with audio.
                    $isaudio = in_array('.' . $ext, file_get_typegroup('extension', 'html_audio'));
                }
                $source = html_writer::empty_tag('source', ['src' => $url, 'type' => $mimetype]);
                $sources[] = $source;
            }
            if (count($sources)) {
                // Setup poster image.
                $poster = '';
                if (isset($playeroptions['image']) && $playeroptions['image'] instanceof moodle_url) {
                    $poster = urldecode($playeroptions['image']->out(false));
                } else if ($poster = get_config('media_jwplayer', 'defaultposter')) {
                    $syscontext = context_system::instance();
                    $poster = moodle_url::make_pluginfile_url(
                        $syscontext->id, 'media_jwplayer', 'defaultposter', 0, null, $poster)->out(true);
                }

                if ($tagtype === 'a') {
                    // Faling back to html5 media.
                    $attributes = [];
                    // Set Title from title attribute of a tag if it has one if not default to filename.
                    if (isset($playeroptions['globalattributes']['title'])) {
                        $attributes['title'] = (string) $playeroptions['globalattributes']['title'];
                    } else if (!get_config('media_jwplayer', 'emptytitle')) {
                        $attributes['title'] = $this->get_name($name, $urls);
                    }

                    // Set size.
                    if (get_config('media_jwplayer', 'displaymode') !== 'responsive') {
                        parent::pick_video_size($width, $height);
                        $attributes += ['width' => $width] + ($height ? ['height' => $height] : []);
                    }

                    // Set poster.
                    if ($poster) {
                        $attributes += ['poster' => $poster];
                    }

                    // Output html5 player.
                    $attributes += ['preload' => 'metadata', 'controls' => 'true'];
                    $sources = implode("\n", $sources);
                    return html_writer::tag($isaudio ? 'audio' : 'video', $sources . self::LINKPLACEHOLDER, $attributes);
                } else if ($tagtype === 'video' || $tagtype === 'audio') {
                    // Faling back to original html5 media.
                    // We replace sources in original tag, as they might have been modified by filter.
                    $sources = implode("\n", $sources);
                    $output = core_media_player_native::replace_sources(
                        $options[core_media_manager::OPTION_ORIGINAL_TEXT], $sources);

                    // And we set poster.
                    if ($poster) {
                        $output = core_media_player_native::add_attributes(
                            $options[core_media_manager::OPTION_ORIGINAL_TEXT], ['poster' => $poster]);
                    }
                    return $output;
                }
            }
            // If we can't fallback to html5 video/audio, just output link instead.
            return self::LINKPLACEHOLDER;
        }

        // Embeding JWPlayer.
        return $this->embed_jwplayer($urls, $name, $width, $height, $playeroptions);
    }

    /**
     * Parse media tag.
     *
     * This function is parsing media tag and populate as simple array of player
     * options used for player setup. Valid global attributes located in the tag
     * are also determined and presented in a separate 'globalattributes' key.
     *
     * @param string $originalhtml Original HTML snippet.
     * @return array Player options.
     */
    private function get_options_from_media_tag($originalhtml): array {

        $playeroptions = ['globalattributes' => []];
        $globalattributes = self::get_global_attributes();

        // Determine media type.
        preg_match('/^<(video|audio)\b/i', $originalhtml, $matches);
        $type = $matches[1];

        // Get attributes.
        $attributes = [];
        $tag = $originalhtml;
        while (preg_match('/^(<[^>]*\b)(\w+)="(.*?)"(.*)$/is', $tag, $matches)) {
            // Attribute with value, e.g. width="500".
            $tag = $matches[1] . $matches[4];
            $attributes[clean_param($matches[2], PARAM_ALPHAEXT)] = clean_param(htmlspecialchars_decode($matches[3]), PARAM_RAW);
        }
        while (preg_match('~^(<[^>]*\b)(\w+)([ />].*)$~is', $tag, $matches)) {
            // Some attributes may not have value, e.g. <video controls>.
            $tag = $matches[1] . $matches[3];
            $attributes[clean_param($matches[2], PARAM_ALPHAEXT)] = '';
        }
        // We have got media tag itself counted as "attribute with no value". Remove it from array.
        unset($attributes[$type]);

        // Populate global attributes.
        foreach ($attributes as $attrib => $atval) {
            if (in_array($attrib, $globalattributes) || strpos($attrib, 'data-') === 0) {
                $playeroptions['globalattributes'][$attrib] = $atval;
            }
        }

        // Populate media type specific attributes as options.
        // We only take those we can use for player setup.
        $mappingfunction = 'get_' . $type . '_tag_options_mapping';
        $mediattributes = self::$mappingfunction();
        foreach ($mediattributes as $attrib => $option) {
            if (isset($attributes[$attrib])) {
                $playeroptions[$option] = $attributes[$attrib] ? $attributes[$attrib] : "true";
            }
        }
        // Image is expected to be instance of moodle_url.
        if (isset($playeroptions['image'])) {
            $playeroptions['image'] = new moodle_url(clean_param($playeroptions['image'], PARAM_URL));
        }

        // Parse tracks.
        if (preg_match_all('~</?track\b[^>]*>~im', $originalhtml, $matches)) {
            $tracks = [];
            foreach ($matches[0] as $trackhtml) {
                // Determine track attributes.
                $trackattributes = [];
                while (preg_match('/^(<[^>]*\b)(\w+)="(.*?)"(.*)$/is', $trackhtml, $matches)) {
                    // Attribute with value, e.g. width="500".
                    $trackhtml = $matches[1] . $matches[4];
                    $key = clean_param($matches[2], PARAM_ALPHAEXT);
                    $value = clean_param(htmlspecialchars_decode($matches[3]), PARAM_RAW);
                    if (!empty($key) && !empty($value)) {
                        $trackattributes[$key] = $value;
                    }
                }
                while (preg_match('~^(<[^>]*\b)(\w+)([ />].*)$~is', $trackhtml, $matches)) {
                    // Some attributes may not have value, e.g. <track default>.
                    $trackhtml = $matches[1] . $matches[3];
                    $key = clean_param($matches[2], PARAM_ALPHAEXT);
                    if (!empty($key)) {
                        $trackattributes[$key] = '';
                    }
                }
                // We have got track tag itself counted as "attribute with no value". Remove it from array.
                unset($trackattributes['track']);

                // We popluate track data according to JW Player API requirements.
                $validkinds = ['subtitles', 'captions', 'chapters'];
                if ($trackattributes['src'] && (empty($trackattributes['kind']) ||
                        in_array($trackattributes['kind'], $validkinds))) {
                    // Track file.
                    $track = ['file' => clean_param($trackattributes['src'], PARAM_URL)];

                    // Labels.
                    if (isset($trackattributes['label'])) {
                        $track['label'] = $trackattributes['label'];
                    }
                    if (isset($trackattributes['srclang'])) {
                        $track['label'] = isset($track['label']) ?
                            $track['label'] . ' (' . $trackattributes['srclang'] . ')' : $trackattributes['srclang'];
                    }

                    // Kind of track.
                    if (empty($trackattributes['kind']) || $trackattributes['kind'] === 'subtitles' ||
                            $trackattributes['kind'] === 'captions') {
                        $track['kind'] = 'captions';
                    } else if ($trackattributes['kind'] === 'chapters') {
                        $track['kind'] = 'chapters';
                    }

                    // Default track (only applicable to 'captions').
                    if (isset($trackattributes['default']) && $track['kind'] = 'captions') {
                        $track['default'] = true;
                    }

                    // Add track data to the list of tracks.
                    $tracks[] = $track;
                }
            }

            // Define subtitles option.
            if (count($tracks)) {
                $playeroptions['subtitles'] = $tracks;
            }
        }
        return $playeroptions;
    }

    /**
     * Parse <a> tag attributes.
     *
     * This function is separating data-jwplayer-* attributes, format them and
     * populate as simple array of player options used for player setup. Valid
     * global attributes located in the tag are also determined and presented
     * in a separate 'globalattributes' key.
     *
     * @param array $attributes Array of attributes in name => (str)value format.
     * @return array Player options.
     */
    private function get_options_from_a_tag_attributes($attributes): array {

        $playeroptions = ['globalattributes' => []];
        $globalattributes = self::get_global_attributes();

        foreach ($attributes as $attrib => $atval) {
            // Process data-jwplayer-* attributes.
            if (strpos($attrib, 'data-jwplayer-') === 0) {
                // Treat attributes starting data-jwplayer as options.
                $opt = preg_replace('~^data-jwplayer-~', '', $attrib);
                $atval = trim((string) $atval);
                if ($opt === 'subtitles') {
                    // For subtitles, we need to parse attribute content and build array of tracks.
                    // Split into tracks.
                    $atvalarray = preg_split('~[,;] ~', $atval);
                    $tracks = [];
                    foreach ($atvalarray as $trackdata) {
                        // Track can be specified in two formats, with label ('English: https://URL')
                        // or just URL.
                        $trackdata = explode(': ', $trackdata, 2);
                        if (count($trackdata) === 2) {
                            // Label has been provided.
                            $tracks[] = [
                                'label' => trim($trackdata[0]),
                                'file' => clean_param($trackdata[1], PARAM_URL),
                            ];
                        } else {
                            $tracks[] = [
                                'file' => clean_param($trackdata[0], PARAM_URL),
                            ];
                        }
                    }
                    $atval = $tracks;
                } else if (filter_var($atval, FILTER_VALIDATE_URL)) {
                    // If value is a URL convert to moodle_url.
                    $atval = new moodle_url(clean_param($atval, PARAM_URL));
                }
                $playeroptions[$opt] = $atval;
            } else {
                // Pass any other global HTML attributes to the player span tag.
                if (in_array($attrib, $globalattributes) || strpos($attrib, 'data-') === 0) {
                    $playeroptions['globalattributes'][$attrib] = $atval;
                }
            }
        }
        return $playeroptions;
    }

    /**
     * Returns mapping of video tag attributes to matching JWPlayer setup options.
     *
     * We ignore height and width attributes, as they are determined by filter
     * and passed to plugin in embed call. We also ignore attributes that to not
     * have a corresponding option, e.g. 'crossorigin'
     *
     * @return array Mapping of tag attribute => player setup option.
     */
    private static function get_video_tag_options_mapping(): array {
        return [
            'autoplay' => 'autostart',
            'controls' => 'controls',
            'loop'     => 'repeat',
            'muted'    => 'mute',
            'poster'   => 'image',
            'preload'  => 'preload',
        ];
    }

    /**
     * Returns mapping of audio tag attributes to matching JWPlayer setup options.
     *
     * We ignore attributes that to not have a corresponding option, e.g. 'crossorigin'
     *
     * @return array Mapping of tag attribute => player setup option.
     */
    private static function get_audio_tag_options_mapping(): array {
        return [
            'autoplay' => 'autostart',
            'controls' => 'controls',
            'loop'     => 'repeat',
            'muted'    => 'mute',
            'preload'  => 'preload',
        ];
    }

    /**
     * Returns the list of valid global attributes.
     *
     * @return array Global attributes.
     */
    private static function get_global_attributes(): array {
        // List of valid global attributes.
        // https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes.
        return [
            'accesskey',
            'autocapitalize',
            'class',
            'contenteditable',
            'contextmenu',
            'dir',
            'draggable',
            'dropzone',
            'hidden',
            'id',
            'is',
            'itemid',
            'itemprop',
            'itempref',
            'itemscope',
            'itemtype',
            'lang',
            'part',
            'slot',
            'spellcheck',
            'style',
            'tabindex',
            'title',
            'translate',
        ];
    }

    /**
     * Generates code required to embed JWPlayer.
     *
     * @param array $urls Moodle URLs of media files
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Player options array
     * @return string HTML code for embed
     */
    private function embed_jwplayer($urls, $name, $width, $height, $options) {
        global $PAGE, $CFG;
        $mediamanager = core_media_manager::instance();
        $sources = [];
        $isstream = null;
        $isaudio = $options['isaudio'] ?? null;
        foreach ($urls as $url) {
            // Add the details for this source.
            $source = ['file' => urldecode($url->out(false))];
            // Help to determine the type of mov.
            $ext = $mediamanager->get_extension($url);
            if ($ext === 'mov') {
                $source['type'] = 'mp4';
            }
            // Check if this is a stream.
            if ($isstream === null) {
                $isstream = in_array($ext, ['m3u8', 'mpd', 'ts', 'fmp4']);
            }
            // Check if this is audio if we don't know that already from media tag.
            if ($isaudio === null) {
                $isaudio = in_array('.' . $ext, file_get_typegroup('extension', 'html_audio'));
            }

            $sources[] = $source;
        }

        // Make sure that stream URLs are at the start of the list and set up playlist.
        $playlistitem = ['sources' => $sources];

        // Set Title from title attribute of a tag if it has one if not default to filename.
        if (isset($options['globalattributes']['title'])) {
            $playlistitem['title'] = (string) $options['globalattributes']['title'];
            // Remove title from global attributes.
            unset ($options['globalattributes']['title']);
        } else if (!get_config('media_jwplayer', 'emptytitle')) {
            $playlistitem['title'] = $this->get_name($name, $urls);
        }

        // Setup video description.
        if (isset($options['description'])) {
            $playlistitem['description'] = $options['description'];
        }

        // Setup video mediaid.
        if (isset($options['mediaid']) && strlen(trim($options['mediaid']))) {
            $playlistitem['mediaid'] = $options['mediaid'];
        }

        // Setup poster image.
        if (isset($options['image']) && $options['image'] instanceof moodle_url) {
            $playlistitem['image'] = urldecode($options['image']->out(false));
        } else if ($poster = get_config('media_jwplayer', 'defaultposter')) {
            $syscontext = context_system::instance();
            $playlistitem['image'] = moodle_url::make_pluginfile_url(
                $syscontext->id, 'media_jwplayer', 'defaultposter', 0, null, $poster)->out(true);
        }

        // Setup subtitle tracks.
        if (isset($options['subtitles']) && count($options['subtitles'])) {
            $playlistitem['tracks'] = $options['subtitles'];
        }

        $playersetupdata = ['playlist' => [$playlistitem]];

        // Setup player size.
        // If we are dealing with audio, show just the control bar of default width.
        if ($isaudio) {
            parent::pick_video_size($width, $height);
            $height = self::AUDIO_HEIGHT;
        }

        // Unless we have size defined in element containing media URL, use settings.
        if (!$width) {
            // Use responsive width if choosen in settings otherwise default to fixed size.
            if (get_config('media_jwplayer', 'displaymode') === 'responsive') {
                $width = self::VIDEO_WIDTH_RESPONSIVE;
                $playersetupdata['aspectratio'] = $options['aspectratio'] ?? get_config('media_jwplayer', 'aspectratio');
            } else if (get_config('media_jwplayer', 'displaymode') === 'fixedwidth') {
                $width = $CFG->media_default_width;
                $height = 0;
            } else {
                // Fixed width and height.
                parent::pick_video_size($width, $height);
            }
        }
        $playersetupdata['width'] = $width;
        if (empty($playersetupdata['aspectratio'])) {
            // Height needs to be defined only if we don't have aspectratio (i.e. not in responsive mode).
            if ($height) {
                // Height is known.
                $playersetupdata['height'] = $height;
            } else {
                // We are either in 'fixedwidth' display mode or source media has only width defined.
                // Calculate height using aspectratio proportion.
                $aspectratio = $options['aspectratio'] ?? get_config('media_jwplayer', 'aspectratio');
                list($x, $y) = explode(':', $aspectratio);
                $playersetupdata['height'] = round((int) $width * (int) $y / (int) $x);
            }
        }

        // Set additional player options: autostart, mute, controls, repeat.
        if (isset($options['autostart'])) {
            $playersetupdata['autostart'] = $options['autostart'];
        }
        if (isset($options['mute'])) {
            $playersetupdata['mute'] = $options['mute'];
        }
        if (isset($options['controls'])) {
            $playersetupdata['controls'] = $options['controls'];
        }
        if (isset($options['repeat'])) {
            $playersetupdata['repeat'] = $options['repeat'];
        }

        // Load skin.
        if ($customskinname = get_config('media_jwplayer', 'customskinname')) {
            $playersetupdata['skin'] = ['name' => $customskinname];
        }

        // Playback rate.
        $playbackrates = get_config('media_jwplayer', 'playbackrates');
        if (!empty($playbackrates) && $playbackrates !== '1') {
            // Convert settings value to array of float numbers.
            $playbackrates = array_map(function($param) {
                return (float) $param;
            }, explode(',', $playbackrates));
            $playersetupdata['playbackRateControls'] = $playbackrates;
        }

        // Google Analytics settings.
        if (get_config('media_jwplayer', 'googleanalytics')) {
            $playersetupdata['ga'] = [];
            $galabel = $options['galabel'] ?? get_config('media_jwplayer', 'galabel');
            if ($galabel) {
                $playersetupdata['ga']['label'] = $galabel;
            }
        }

        $playersetup = new stdClass();
        $playersetup->setupdata = $playersetupdata;
        $playersetup->events = $this->get_enabled_events();
        $playersetup->logerrors = (bool) get_config('media_jwplayer', 'logerrors');
        // Add download button if required and supported.
        $playersetup->showdownloadbtn = get_config('media_jwplayer', 'downloadbutton') && !$isstream;

        // Set up the player.
        $playerid = 'media_jwplayer_' . html_writer::random_id('');
        $PAGE->requires->js_call_amd('media_jwplayer/jwplayer', 'setupPlayer', [$playersetup, $playerid, $PAGE->context->id]);
        $playerdiv = html_writer::div(self::LINKPLACEHOLDER, '', ['id' => $playerid]);
        return html_writer::div($playerdiv, 'mediaplugin mediaplugin_jwplayer d-block', $options['globalattributes']);
    }

    /**
     * Gets the list of file extensions supported (enabled) by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function get_supported_extensions() {
        return explode(',', get_config('media_jwplayer', 'enabledextensions'));
    }

    /**
     * Gets the list of events supported (enabled) by this media player.
     *
     * @return array Array of strings
     */
    private function get_enabled_events() {
        return explode(',', get_config('media_jwplayer', 'enabledevents'));
    }

    /**
     * Generates the list of file extensions supported by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public static function list_supported_extensions() {
        $video = ['mp4', 'm4v', 'mov', 'webm', 'ogv'];
        $audio = ['aac', 'm4a', 'mp3', 'ogg', 'oga'];
        $streaming = ['m3u8', 'mpd', 'ts', 'fmp4'];
        return array_merge($video, $audio, $streaming);
    }

    /**
     * Generates the list of supported events that can be traced and logged.
     *
     * @return array Array of strings
     */
    public static function list_supported_events() {
        $events = [
            'started',
            'paused',
            'seeked',
            'resumed',
            'completed',
        ];
        return $events;
    }

    /**
     * Gets the ranking of this player.
     *
     * See parent class function for more details.
     *
     * @return int Rank
     */
    public function get_rank() {
        return 1;
    }

    /**
     * Checks if player has configuration sufficient for its using.
     *
     * @return bool True if player is configured.
     */
    private function is_configured() {
        $hostingmethod = get_config('media_jwplayer', 'hostingmethod');
        $libraryurl = get_config('media_jwplayer', 'libraryurl');
        if (!$hostingmethod || (($hostingmethod === 'cloud') && empty($libraryurl))) {
            return false;
        }
        return true;
    }

    /**
     * Setup page requirements.
     *
     * @param moodle_page $page the page we are going to add requirements to.
     * @return void
     */
    public function setup($page) {
        global $CFG;

        if (!$this->is_configured()) {
            // Not configured properly.
            return;
        }

        if (self::is_mobile_app_ws_request()) {
            // Nothing to setup here, it is webservice call. Set the flag to fallback
            // using <video> tag later.
            $this->ismobileapp = true;
            return;
        }

        $hostingmethod = get_config('media_jwplayer', 'hostingmethod');
        if ($hostingmethod === 'cloud') {
            $libraryurl = preg_replace('/\.js$/', '', get_config('media_jwplayer', 'libraryurl'));
            $jwplayer = new moodle_url($libraryurl);
        } else if ($hostingmethod === 'self') {
            $jwplayer = new moodle_url($CFG->httpswwwroot.'/media/player/jwplayer/jwplayer/jwplayer');

            // Set license key.
            $config = ['config' => ['media_jwplayer/jwplayer' => ['licensekey' => get_config('media_jwplayer', 'licensekey')]]];
            $licensejs = 'require.config(' . json_encode($config) . ')';
            $page->requires->js_amd_inline($licensejs);
        }

        // Define jwplayer module.
        $config = ['paths' => ['jwplayer' => $jwplayer->out()]];
        $requirejs = 'require.config(' . json_encode($config) . ')';
        $page->requires->js_amd_inline($requirejs);
    }

    /**
     * Returns human-readable string of supported file/link types for the "Manage media players" page
     *
     * @param array $usedextensions extensions that should NOT be highlighted
     * @return string
     */
    public function supports($usedextensions = []) {
        $supports = '';
        if (!$this->is_configured()) {
            // Configuration is incomplete, display warning.
            $supports .= html_writer::span(get_string('errornotconfigured', 'media_jwplayer'), 'error') . '<br>';
        }
        $supports .= parent::supports($usedextensions);

        return $supports;
    }

    /**
     * Check if embedding is requested by webservice call from mobile app.
     *
     * We need to do more detailed check here, as WS_SERVER is not sufficient.
     * Code snippet by courtesy of Open University.
     *
     * @return bool
     */
    private static function is_mobile_app_ws_request(): bool {
        global $DB;
        $ismobileapp = false;
        $wstoken = optional_param('wstoken', null, PARAM_ALPHANUM);
        if ($wstoken) {
            list($insql, $params) = $DB->get_in_or_equal(
                    [MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'], SQL_PARAMS_NAMED);
            $params['token'] = $wstoken;
            $sql = "SELECT *
                      FROM {external_tokens} t
                      JOIN {external_services} s ON t.externalserviceid = s.id
                     WHERE t.token = :token AND s.shortname $insql";
            $ismobileapp = $DB->record_exists_sql($sql, $params);
        }
        return $ismobileapp;
    }
}
