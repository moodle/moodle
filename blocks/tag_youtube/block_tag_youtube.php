<?php // $id$

require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/magpie/rss_cache.inc');
require_once($CFG->libdir . '/phpxml/xml.php');

define('DEFAULT_NUMBER_OF_VIDEOS', 5);
define('YOUTUBE_CACHE_EXPIRATION', 1800);

class block_tag_youtube extends block_base {

    function init() {
        $this->title = get_string('blockname','block_tag_youtube');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('tag' => true);
    }

    function specialization() {
        $this->title = !empty($this->config->title) ? $this->config->title : get_string('blockname', 'block_tag_youtube');
        // Convert numeric categories (old YouTube API) to
        // textual ones (new Google Data API)
        $this->config->category = !empty($this->config->category) ? $this->category_map_old2new($this->config->category) : '0';
    }

    function instance_allow_multiple() {
        return true;
    }

    function preferred_width() {
        return 140;
    }

    function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

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

        $this->content = new stdClass;
        $this->content->text = $text;
        $this->content->footer = '';

        return $this->content;
    }

    function get_videos_by_playlist(){

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://gdata.youtube.com/feeds/api/playlists/' .
                   $this->config->playlist .
                   '?start-index=1&max-results=' .
                   $numberofvideos .
                   '&format=5';
        return $this->fetch_request($request);
    }

    function get_videos_by_tag(){

        $tagid = optional_param('id', 0, PARAM_INT);   // tag id - for backware compatibility
        $tag = optional_param('tag', '', PARAM_TAG); // tag

        if ($tag) {
            $tagobject = tag_get('name', $tag);
        } else if ($tagid) {
            $tagobject = tag_get('id', $tagid);
        }

        if (empty($tagobject)) {
            print_error('tagnotfound');
        }

        $querytag = urlencode($tagobject->name);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if ( !empty($this->config->numberofvideos) ) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://gdata.youtube.com/feeds/api/videos?vq=' .
                   $querytag .
                   '&start-index=1&max-results=' .
                   $numberofvideos .
                   '&format=5';

        return $this->fetch_request($request);
    }

    function get_videos_by_tag_and_category(){

        $tagid = optional_param('id', 0, PARAM_INT);   // tag id - for backware compatibility
        $tag = optional_param('tag', '', PARAM_TAG); // tag

        if ($tag) {
            $tagobject = tag_get('name', $tag);
        } else if ($tagid) {
            $tagobject = tag_get('id', $tagid);
        }

        if (empty($tagobject)) {
            print_error('tagnotfound');
        }

        $querytag = urlencode($tagobject->name);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://gdata.youtube.com/feeds/api/videos?category=' .
                   $this->category_map_old2new($this->config->category) .
                   '&vq=' .
                   $querytag .
                   '&start-index=1&max-results=' .
                   $numberofvideos .
                   '&format=5';

        return $this->fetch_request($request);
    }

    function fetch_request($request){

        global $CFG;

        make_upload_directory('/cache/youtube');

        $cache = new RSSCache($CFG->dataroot . '/cache/youtube',YOUTUBE_CACHE_EXPIRATION);
        $cache_status = $cache->check_cache( $request);

        if ( $cache_status == 'HIT' ) {
            $cached_response = $cache->get( $request );

            // TODO: Stop using phpxml, switching to DOM/Simple for this
            // TODO: Drop lib/phpxml if 0 uses in core/contrib
            $xmlobj = XML_unserialize($cached_response);
            return $this->render_video_list($xmlobj);
        }

        if ( $cache_status == 'STALE' ) {
            $cached_response = $cache->get( $request );
        }

        $response = download_file_content($request);

        if(empty($response)){
            $response = $cached_response;
        }
        else{
            $cache->set($request, $response);
        }

        // TODO: Stop using phpxml, switching to DOM/Simple for this
        // TODO: Drop lib/phpxml if 0 uses in core/contrib
        $xmlobj = XML_unserialize($response);
        return $this->render_video_list($xmlobj);
    }

    function render_video_list($xmlobj){

        $text = '';
        $text .= '<ul class="yt-video-entry unlist img-text">';
        $videos = $xmlobj['feed']['entry'];

        if (is_array($videos) ) {
            foreach($videos as $video){
                $url       = s($video['media:group']['media:player attr']['url']);
                $thumbnail = s($video['media:group']['media:thumbnail']['0 attr']['url']);
                $title     = s($video['media:group']['media:title']);
                $seconds   = $video['media:group']['yt:duration attr']['seconds'];

                $text .= '<li>';
                $text .= '<div class="clearfix">';
                $text .= '<a href="'. $url . '">';
                $text .= '<img alt="" class="youtube-thumb" src="'. $thumbnail .'" /></a>';
                $text .= '</div><span><a href="'. $url . '">'. $title .'</a></span>';
                $text .= '<div>';
                $text .= format_time($seconds);
                $text .= "</div></li>\n";
            }
        } else {
            // if youtube is offline, or for whatever reason the previous
            // call doesn't work...
            //add_to_log(SITEID, 'blocks/tag_youtube', 'problem in getting videos off youtube');
        }
        $text .= "</ul><div class=\"clearer\"></div>\n";
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
?>
