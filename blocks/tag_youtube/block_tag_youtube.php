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
     * @param stdClass $videosdata
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

    /**
     * Method that returns an array containing all relevant video categories obtained through an API call, where the
     * array index represents the category ID and the array value represents the category name.
     *
     * @return array The array containing the relevant video categories
     * @throws moodle_exception If the API key is not set
     * @throws Google_Service_Exception If an error occurs while obtaining the categories through the API call
     */
    public function get_categories() {
        // Get the default categories and it's translations.
        $categorytranslations = $this->category_map_translation();

        if ($service = $this->get_service()) {
            // Call the API to fetch the youtube video categories.
            // This API call requires the regionCode parameter which instructs the API to return the list of video
            // categories available in the specified country. Currently 'us' is hardcoded as the returned categories
            // for this region correspond to the previously used (legacy) hardcoded list of categories.
            // TODO: We should improve this in the future and avoid hardcoding this value.
            $response = $service->videoCategories->listVideoCategories('snippet', ['regionCode' => 'us']);
            $categoryitems = $response['modelData']['items'];

            // Return an array with the relevant categories.
            return array_reduce($categoryitems, function($categories, $category) use ($categorytranslations) {
                $categoryid = $category['id'];
                $categoryname = $category['snippet']['title'];
                // Videos can be associated with this category.
                if ($category['snippet']['assignable']) {
                    // If the category name can be mapped with a translation, add it to the categories array.
                    if (array_key_exists($categoryname, $categorytranslations)) {
                        $categories[$categoryid] = $categorytranslations[$categoryname];
                    } else { // Otherwise, display the untranslated category name and show a debugging message.
                        $categories[$categoryid] = $categoryname;
                        debugging("The category '{$categoryname}' does not have a translatable language string.");
                    }
                }
                return $categories;
            }, []);
        } else {
            throw new \moodle_exception('apierror', 'block_tag_youtube');
        }
    }

    /**
     * Method that provides mapping between the video category names and their translations.
     *
     * @return array The array that maps the video category names with their translations
     */
    private function category_map_translation() {
        return [
            'Film & Animation' => get_string('filmsanimation', 'block_tag_youtube'),
            'Autos & Vehicles' => get_string('autosvehicles', 'block_tag_youtube'),
            'Music' => get_string('music', 'block_tag_youtube'),
            'Pets & Animals' => get_string('petsanimals', 'block_tag_youtube'),
            'Sports' => get_string('sports', 'block_tag_youtube'),
            'Travel & Events' => get_string('travel', 'block_tag_youtube'),
            'Gaming' => get_string('gadgetsgames', 'block_tag_youtube'),
            'People & Blogs' => get_string('peopleblogs', 'block_tag_youtube'),
            'Comedy' => get_string('comedy', 'block_tag_youtube'),
            'Entertainment' => get_string('entertainment', 'block_tag_youtube'),
            'News & Politics' => get_string('newspolitics', 'block_tag_youtube'),
            'Howto & Style'  => get_string('howtodiy', 'block_tag_youtube'),
            'Education' => get_string('education', 'block_tag_youtube'),
            'Science & Technology' => get_string('scienceandtech', 'block_tag_youtube'),
            'Nonprofits & Activism' => get_string('nonprofitactivism', 'block_tag_youtube'),
        ];
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // There is a private key, only admins can see it.
        $pluginconfigs = get_config('block_tag_youtube');
        if (!has_capability('moodle/site:config', context_system::instance())) {
            unset($pluginconfigs->apikey);
        }
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();

        return (object) [
            'instance' => $instanceconfigs,
            'plugin' => $pluginconfigs,
        ];
    }
}

