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
 * Flickr tag block.
 *
 * @package    block_tag_flickr
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
define('FLICKR_DEV_KEY', '4fddbdd7ff2376beec54d7f6afad425e');
define('DEFAULT_NUMBER_OF_PHOTOS', 6);

require_once("{$CFG->libdir}/flickrclient.php");

class block_tag_flickr extends block_base {

    function init() {
        $this->title = get_string('pluginname','block_tag_flickr');
    }

    function applicable_formats() {
        return array('tag' => true);
    }

    function specialization() {
        $this->title = !empty($this->config->title) ? $this->config->title : get_string('pluginname', 'block_tag_flickr');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG, $USER;

        //note: do NOT include files at the top of this file
        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
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
            $this->content = new stdClass;
            $this->content->text = '';
            $this->content->footer = '';
            return $this->content;
        }

        //include related tags in the photo query ?
        $tagscsv = $tagobject->name;
        if (!empty($this->config->includerelatedtags)) {
            foreach ($tagobject->get_related_tags() as $t) {
                $tagscsv .= ',' . $t->get_display_name(false);
            }
        }
        $tagscsv = urlencode($tagscsv);

        //number of photos to display
        $numberofphotos = DEFAULT_NUMBER_OF_PHOTOS;
        if( !empty($this->config->numberofphotos)) {
            $numberofphotos = $this->config->numberofphotos;
        }

        //sort search results by
        $sortby = 'relevance';
        if( !empty($this->config->sortby)) {
            $sortby = $this->config->sortby;
        }

        //pull photos from a specific photoset
        if(!empty($this->config->photoset)){

            $request = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos';
            $request .= '&api_key='.FLICKR_DEV_KEY;
            $request .= '&photoset_id='.$this->config->photoset;
            $request .= '&per_page='.$numberofphotos;
            $request .= '&format=php_serial';

            $response = $this->fetch_request($request);

            $search = @unserialize($response);
            if ($search === false && $search != serialize(false)) {
                // The response didn't appear to be anything serialized, exit...
                return;
            }

            foreach ($search['photoset']['photo'] as $p){
                $p['owner'] = $search['photoset']['owner'];
            }

            $photos = array_values($search['photoset']['photo']);

        }
        //search for photos tagged with $tagscsv
        else{

            $request = 'https://api.flickr.com/services/rest/?method=flickr.photos.search';
            $request .= '&api_key='.FLICKR_DEV_KEY;
            $request .= '&tags='.$tagscsv;
            $request .= '&per_page='.$numberofphotos;
            $request .= '&sort='.$sortby;
            $request .= '&format=php_serial';

            $response = $this->fetch_request($request);

            $search = @unserialize($response);
            if ($search === false && $search != serialize(false)) {
                // The response didn't appear to be anything serialized, exit...
                return;
            }
            $photos = array_values($search['photos']['photo']);
        }


        if(strcmp($search['stat'], 'ok') != 0) return; //if no results were returned, exit...

        //Accessibility: render the list of photos
        $text = '<ul class="inline-list">';
         foreach ($photos as $photo) {
            $text .= '<li><a href="http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/" title="'.s($photo['title']).'">';
            $text .= '<img alt="'.s($photo['title']).'" class="flickr-photos" src="'. $this->build_photo_url($photo, 'square') ."\" /></a></li>\n";
         }
        $text .= "</ul>\n";

        $this->content = new stdClass;
        $this->content->text = $text;
        $this->content->footer = '';

        return $this->content;
    }

    function fetch_request($request){
        $c =  new curl(array('cache' => true, 'module_cache'=> 'tag_flickr'));
        // Set custom user agent as Flickr blocks our "MoodleBot" agent string.
        $c->setopt([
            'CURLOPT_USERAGENT' => flickr_client::user_agent(),
        ]);

        $response = $c->get($request);

        return $response;
    }

    function build_photo_url ($photo, $size='medium') {
        //receives an array (can use the individual photo data returned
        //from an API call) and returns a URL (doesn't mean that the
        //file size exists)
        $sizes = array(
            'square' => '_s',
            'thumbnail' => '_t',
            'small' => '_m',
            'medium' => '',
            'large' => '_b',
            'original' => '_o'
        );

        $size = strtolower($size);
        if (!array_key_exists($size, $sizes)) {
            $size = 'medium';
        }

        if ($size == 'original') {
            $url = 'http://farm' . $photo['farm'] . '.static.flickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['originalsecret'] . '_o' . '.' . $photo['originalformat'];
        } else {
            $url = 'http://farm' . $photo['farm'] . '.static.flickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . $sizes[$size] . '.jpg';
        }
        return $url;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = !empty($this->config) ? $this->config : new stdClass();

        return (object) [
            'instance' => $configs,
            'plugin' => new stdClass(),
        ];
    }
}


