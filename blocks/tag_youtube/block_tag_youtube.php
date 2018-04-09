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
 * Tag youtube block
 *
 * @package    block_tag_youtube
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('DEFAULT_NUMBER_OF_VIDEOS', 5);

class block_tag_youtube extends block_base {

    /**
     * @var Google_Service_Youtube
     */
    protected $service = null;

    function init() {
        $this->title = get_string('pluginname','block_tag_youtube');
        $this->config = new stdClass();
    }

    function applicable_formats() {
        return array('tag' => true);
    }

    /**
     * It can be configured.
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    function specialization() {
        $this->title = !empty($this->config->title) ? $this->config->title : get_string('pluginname', 'block_tag_youtube');
        // Convert numeric categories (old YouTube API) to
        // textual ones (new Google Data API)
        $this->config->category = !empty($this->config->category) ? $this->category_map_old2new($this->config->category) : '0';
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG;

        //note: do NOT include files at the top of this file
        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        if (!$this->get_service()) {
            $this->content->text = $this->get_error_message();
            return $this->content;
        }

        $text = '';
        if(!empty($this->config->playlist)){
            //videos from a playlist
            $text = $this->get_videos_by_playlist();
        }
        else{
            if(!empty($this->config->category)){
                //videos from category with tag
                $text = $this->get_videos_by_tag_and_category();
            }
            else {
                //videos with tag
                $text = $this->get_videos_by_tag();
            }
        }

        $this->content->text = $text;

        return $this->content;
    }

    function get_videos_by_playlist(){

        if (!$service = $this->get_service()) {
            return $this->get_error_message();
        }

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        try {
            $response = $service->playlistItems->listPlaylistItems('id,snippet', array(
                'playlistId' => $this->config->playlist,
                'maxResults' => $numberofvideos
            ));
        } catch (Google_Service_Exception $e) {
            debugging('Google service exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return $this->get_error_message(get_string('requesterror', 'block_tag_youtube'));
        }

        return $this->render_items($response);
    }

    function get_videos_by_tag(){

        if (!$service = $this->get_service()) {
            return $this->get_error_message();
        }

        $tagid = optional_param('id', 0, PARAM_INT);   // tag id - for backware compatibility
        $tag = optional_param('tag', '', PARAM_TAG); // tag
        $tc = optional_param('tc', 0, PARAM_INT); // Tag collection id.

        if ($tagid) {
            $tagobject = core_tag_tag::get($tagid);
        } else if ($tag) {
            $tagobject = core_tag_tag::get_by_name($tc, $tag);
        }

        if (empty($tagobject)) {
            return '';
        }

        $querytag = urlencode($tagobject->name);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if ( !empty($this->config->numberofvideos) ) {
            $numberofvideos = $this->config->numberofvideos;
        }

        try {
            $response = $service->search->listSearch('id,snippet', array(
                'q' => $querytag,
                'type' => 'video',
                'maxResults' => $numberofvideos
            ));
        } catch (Google_Service_Exception $e) {
            debugging('Google service exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return $this->get_error_message(get_string('requesterror', 'block_tag_youtube'));
        }

        return $this->render_items($response);
    }

    function get_videos_by_tag_and_category(){

        if (!$service = $this->get_service()) {
            return $this->get_error_message();
        }

        $tagid = optional_param('id', 0, PARAM_INT);   // tag id - for backware compatibility
        $tag = optional_param('tag', '', PARAM_TAG); // tag
        $tc = optional_param('tc', 0, PARAM_INT); // Tag collection id.

        if ($tagid) {
            $tagobject = core_tag_tag::get($tagid);
        } else if ($tag) {
            $tagobject = core_tag_tag::get_by_name($tc, $tag);
        }

        if (empty($tagobject)) {
            return '';
        }

        $querytag = urlencode($tagobject->name);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        try {
            $response = $service->search->listSearch('id,snippet', array(
                'q' => $querytag,
                'type' => 'video',
                'maxResults' => $numberofvideos,
                'videoCategoryId' => $this->config->category
            ));
        } catch (Google_Service_Exception $e) {
            debugging('Google service exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return $this->get_error_message(get_string('requesterror', 'block_tag_youtube'));
        }

        return $this->render_items($response);
    }

    /**
     * Sends a request to fetch data.
     *
     * @see block_tag_youtube::service
     * @deprecated since Moodle 2.8.8, 2.9.2 and 3.0 MDL-49085 - please do not use this function any more.
     * @param string $request
     * @throws coding_exception
     */
    public function fetch_request($request) {
        throw new coding_exception('Sorry, this function has been deprecated in Moodle 2.8.8, 2.9.2 and 3.0. Use block_tag_youtube::get_service instead.');

        $c = new curl(array('cache' => true, 'module_cache'=>'tag_youtube'));
        $c->setopt(array('CURLOPT_TIMEOUT' => 3, 'CURLOPT_CONNECTTIMEOUT' => 3));

        $response = $c->get($request);

        $xml = new SimpleXMLElement($response);
        return $this->render_video_list($xml);
    }

    /**
     * Renders the video list.
     *
     * @see block_tag_youtube::render_items
     * @deprecated since Moodle 2.8.8, 2.9.2 and 3.0 MDL-49085 - please do not use this function any more.
     * @param SimpleXMLElement $xml
     * @throws coding_exception
     */
    function render_video_list(SimpleXMLElement $xml){
        throw new coding_exception('Sorry, this function has been deprecated in Moodle 2.8.8, 2.9.2 and 3.0. Use block_tag_youtube::render_items instead.');
    }

    /**
     * Returns an error message.
     *
     * Useful when the block is not properly set or something goes wrong.
     *
     * @param string $message The message to display.
     * @return string HTML
     */
    protected function get_error_message($message = null) {
        global $OUTPUT;

        if (empty($message)) {
            $message = get_string('apierror', 'block_tag_youtube');
        }
        return $OUTPUT->notification($message);
    }

    /**
     * Gets the youtube service object.
     *
     * @return Google_Service_YouTube
     */
    protected function get_service() {
        global $CFG;

        if (!$apikey = get_config('block_tag_youtube', 'apikey')) {
            return false;
        }

        // Wrapped in an if in case we call different get_videos_* multiple times.
        if (!isset($this->service)) {
            require_once($CFG->libdir . '/google/lib.php');
            $client = get_google_client();
            $client->setDeveloperKey($apikey);
            $client->setScopes(array(Google_Service_YouTube::YOUTUBE_READONLY));
            $this->service = new Google_Service_YouTube($client);
        }

        return $this->service;
    }

    /**
     * Renders the list of items.
     *
     * @param array $videosdata
     * @return string HTML
     */
    protected function render_items($videosdata) {

        if (!$videosdata || empty($videosdata->items)) {
            if (!empty($videosdata->error)) {
                debugging('Error fetching data from youtube: ' . $videosdata->error->message, DEBUG_DEVELOPER);
            }
            return '';
        }

        // If we reach that point we already know that the API key is set.
        $service = $this->get_service();

        $text = html_writer::start_tag('ul', array('class' => 'yt-video-entry unlist img-text'));
        foreach ($videosdata->items as $video) {

            // Link to the video included in the playlist if listing a playlist.
            if (!empty($video->snippet->resourceId)) {
                $id = $video->snippet->resourceId->videoId;
                $playlist = '&list=' . $video->snippet->playlistId;
            } else {
                $id = $video->id->videoId;
                $playlist = '';
            }

            $thumbnail = $video->snippet->getThumbnails()->getDefault();
            $url = 'http://www.youtube.com/watch?v=' . $id . $playlist;

            $videodetails = $service->videos->listVideos('id,contentDetails', array('id' => $id));
            if ($videodetails && !empty($videodetails->items)) {

                // We fetch by id so we just use the first one.
                $details = $videodetails->items[0];
                $start = new DateTime('@0');
                $start->add(new DateInterval($details->contentDetails->duration));
                $seconds = $start->format('U');
            }

            $text .= html_writer::start_tag('li');

            $imgattrs = array('class' => 'youtube-thumb', 'src' => $thumbnail->url, 'alt' => $video->snippet->title);
            $thumbhtml = html_writer::empty_tag('img', $imgattrs);
            $link = html_writer::tag('a', $thumbhtml, array('href' => $url));
            $text .= html_writer::tag('div', $link, array('class' => 'clearfix'));

            $text .= html_writer::tag('span', html_writer::tag('a', $video->snippet->title, array('href' => $url)));

            if (!empty($seconds)) {
                $text .= html_writer::tag('div', format_time($seconds));
            }
            $text .= html_writer::end_tag('li');
        }
        $text .= html_writer::end_tag('ul');

        return $text;
    }

    function get_categories() {
        // TODO: Right now using sticky categories from
        // http://gdata.youtube.com/schemas/2007/categories.cat
        // This should be performed from time to time by the block insead
        // and cached somewhere, avoiding deprecated ones and observing regions
        return array (
            '0' => get_string('anycategory', 'block_tag_youtube'),
            'Film'  => get_string('filmsanimation', 'block_tag_youtube'),
            'Autos' => get_string('autosvehicles', 'block_tag_youtube'),
            'Music' => get_string('music', 'block_tag_youtube'),
            'Animals'=> get_string('petsanimals', 'block_tag_youtube'),
            'Sports' => get_string('sports', 'block_tag_youtube'),
            'Travel' => get_string('travel', 'block_tag_youtube'),
            'Games'  => get_string('gadgetsgames', 'block_tag_youtube'),
            'Comedy' => get_string('comedy', 'block_tag_youtube'),
            'People' => get_string('peopleblogs', 'block_tag_youtube'),
            'News'   => get_string('newspolitics', 'block_tag_youtube'),
            'Entertainment' => get_string('entertainment', 'block_tag_youtube'),
            'Education' => get_string('education', 'block_tag_youtube'),
            'Howto'  => get_string('howtodiy', 'block_tag_youtube'),
            'Tech'   => get_string('scienceandtech', 'block_tag_youtube')
        );
    }

    /**
     * Provide conversion from old numeric categories available in youtube API
     * to the new ones available in the Google API
     *
     * @param int $oldcat old category code
     * @return mixed new category code or 0 (if no match found)
     *
     * TODO: Someday this should be applied on upgrade for all the existing
     * block instances so we won't need the mapping any more. That would imply
     * to implement restore handling to perform the conversion of old blocks.
     */
    function category_map_old2new($oldcat) {
        $oldoptions = array (
            0  => '0',
            1  => 'Film',
            2  => 'Autos',
            23 => 'Comedy',
            24 => 'Entertainment',
            10 => 'Music',
            25 => 'News',
            22 => 'People',
            15 => 'Animals',
            26 => 'Howto',
            17 => 'Sports',
            19 => 'Travel',
            20 => 'Games'
        );
        if (array_key_exists($oldcat, $oldoptions)) {
            return $oldoptions[$oldcat];
        } else {
            return $oldcat;
        }
    }
}

