<?php

require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir . '/filelib.php');

define('YOUTUBE_DEV_KEY', 'Dlp6qqRbI28');
define('DEFAULT_NUMBER_OF_VIDEOS', 5);

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
            return '';
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
            return '';
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
        $c = new curl(array('cache' => true, 'module_cache'=>'tag_youtube'));
        $c->setopt(array('CURLOPT_TIMEOUT' => 3, 'CURLOPT_CONNECTTIMEOUT' => 3));

        $response = $c->get($request);

        $xml = new SimpleXMLElement($response);
        return $this->render_video_list($xml);
    }

    function render_video_list(SimpleXMLElement $xml){

        $text = '';
        $text .= '<ul class="yt-video-entry unlist img-text">';

        foreach($xml->video_list->video as $video){
            $text .= '<li>';
            $text .= '<div class="clearfix">';
            $text .= '<a href="'. s($video->url) . '">';
            $text .= '<img alt="" class="youtube-thumb" src="'. $video->thumbnail_url .'" /></a>';
            $text .= '</div><span><a href="'. s($video->url) . '">'.s($video->title).'</a></span>';
            $text .= '<div>';
            $text .= format_time($video->length_seconds);
            $text .= "</div></li>\n";
        }
        $text .= "</ul><div class=\"clearer\"></div>\n";
        return $text;
    }
}

