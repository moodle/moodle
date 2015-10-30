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

    function init() {
        $this->title = get_string('pluginname','block_tag_youtube');
    }

    function applicable_formats() {
        return array('tag' => true);
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

    function preferred_width() {
        return 140;
    }

    function get_content() {
        global $CFG;

        //note: do NOT include files at the top of this file
        require_once($CFG->dirroot.'/tag/lib.php');
        require_once($CFG->libdir . '/filelib.php');

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
            return '';
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
            return '';
        }

        $querytag = urlencode($tagobject->name);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://gdata.youtube.com/feeds/api/videos?category=' .
                   $this->config->category .
                   '&vq=' .
                   $querytag .
                   '&start-index=1&max-results=' .
                   $numberofvideos .
                   '&format=5';


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

        foreach($xml->entry as $entry){
            $media = $entry->children('http://search.yahoo.com/mrss/');
            $playerattrs = $media->group->player->attributes();
            $url = s($playerattrs['url']);
            $thumbattrs = $media->group->thumbnail[0]->attributes();
            $thumbnail = s($thumbattrs['url']);
            $title = s($media->group->title);
            $yt = $media->children('http://gdata.youtube.com/schemas/2007');
            $secattrs = $yt->duration->attributes();
            $seconds = $secattrs['seconds'];

            $text .= '<li>';
            $text .= '<div class="clearfix">';
            $text .= '<a href="'. $url . '">';
            $text .= '<img alt="" class="youtube-thumb" src="'. $thumbnail .'" /></a>';
            $text .= '</div><span><a href="'. $url . '">'. $title .'</a></span>';
            $text .= '<div>';
            $text .= format_time($seconds);
            $text .= "</div></li>\n";
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

