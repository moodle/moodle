<?php // $id$

require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/magpie/rss_cache.inc');
require_once($CFG->libdir . '/phpxml/xml.php');

define('YOUTUBE_DEV_KEY', 'Dlp6qqRbI28');
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

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_playlist';
        $request .= '&dev_id=' . YOUTUBE_DEV_KEY;
        $request .= "&id={$this->config->playlist}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

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

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_tag';
        $request .= '&dev_id='. YOUTUBE_DEV_KEY;
        $request .= "&tag={$querytag}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

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

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_category_and_tag';
        $request .= '&category_id='.$this->config->category;
        $request .= '&dev_id=' . YOUTUBE_DEV_KEY;
        $request .= "&tag={$querytag}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

        return $this->fetch_request($request);
    }

    function fetch_request($request){

        global $CFG;

        make_upload_directory('/cache/youtube');

        $cache = new RSSCache($CFG->dataroot . '/cache/youtube',YOUTUBE_CACHE_EXPIRATION);
        $cache_status = $cache->check_cache( $request);

        if ( $cache_status == 'HIT' ) {
            $cached_response = $cache->get( $request );

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

        $xmlobj = XML_unserialize($response);
        return $this->render_video_list($xmlobj);
    }

    function render_video_list($xmlobj){

        $text = '';
        $text .= '<ul class="yt-video-entry unlist img-text">';
        $videos = $xmlobj['ut_response']['video_list']['video'];

        if (is_array($videos) ) {
            foreach($videos as $video){
                $text .= '<li>';
                $text .= '<div class="clearfix">';
                $text .= '<a href="'. s($video['url']) . '">';
                $text .= '<img alt="" class="youtube-thumb" src="'. $video['thumbnail_url'] .'" /></a>';
                $text .= '</div><span><a href="'. s($video['url']) . '">'.s($video['title']).'</a></span>';
                $text .= '<div>';
                $text .= format_time($video['length_seconds']);
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
}
?>
