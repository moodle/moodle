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
 * This plugin is used to access youtube videos
 *
 * @since Moodle 2.0
 * @package    repository_youtube
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_youtube class
 *
 * @since Moodle 2.0
 * @package    repository_youtube
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_youtube extends repository {
    /** @var int maximum number of thumbs per page */
    const YOUTUBE_THUMBS_PER_PAGE = 27;

    /**
     * API key for using the YouTube Data API.
     * @var mixed
     */
    private $apikey;

    /**
     * Google Client.
     * @var Google_Client
     */
    private $client = null;

    /**
     * YouTube Service.
     * @var Google_Service_YouTube
     */
    private $service = null;

    /**
     * Search keyword text.
     * @var string
     */
    protected $keyword;

    /**
     * Youtube plugin constructor
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);

        $this->apikey = $this->get_option('apikey');

        // Without an API key, don't show this repo to users as its useless without it.
        if (empty($this->apikey)) {
            $this->disabled = true;
        }
    }

    /**
     * Init all the youtube client service stuff.
     *
     * Instead of instantiating the service in the constructor, we delay
     * it until really neeed because it's really memory hungry (2MB). That
     * way the editor or any other artifact requiring repository instantiation
     * can do it in a cheap way. Sort of lazy loading the plugin.
     */
    private function init_youtube_service() {
        global $CFG;

        if (!isset($this->service)) {
            require_once($CFG->libdir . '/google/lib.php');
            $this->client = get_google_client();
            $this->client->setDeveloperKey($this->apikey);
            $this->client->setScopes(array(Google_Service_YouTube::YOUTUBE_READONLY));
            $this->service = new Google_Service_YouTube($this->client);
        }
    }

    /**
     * Save apikey in config table.
     * @param array $options
     * @return boolean
     */
    public function set_option($options = array()) {
        if (!empty($options['apikey'])) {
            set_config('apikey', trim($options['apikey']), 'youtube');
        }
        unset($options['apikey']);
        return parent::set_option($options);
    }

    /**
     * Get apikey from config table.
     *
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config === 'apikey') {
            return trim(get_config('youtube', 'apikey'));
        } else {
            $options['apikey'] = trim(get_config('youtube', 'apikey'));
        }
        return parent::get_option($config);
    }

    public function check_login() {
        return !empty($this->keyword);
    }

    /**
     * Return search results
     * @param string $search_text
     * @return array
     */
    public function search($search_text, $page = 0) {
        global $SESSION;
        $sort = optional_param('youtube_sort', '', PARAM_TEXT);
        $sess_keyword = 'youtube_'.$this->id.'_keyword';
        $sess_sort = 'youtube_'.$this->id.'_sort';

        // This is the request of another page for the last search, retrieve the cached keyword and sort
        if ($page && !$search_text && isset($SESSION->{$sess_keyword})) {
            $search_text = $SESSION->{$sess_keyword};
        }
        if ($page && !$sort && isset($SESSION->{$sess_sort})) {
            $sort = $SESSION->{$sess_sort};
        }
        if (!$sort) {
            $sort = 'relevance'; // default
        }

        // Save this search in session
        $SESSION->{$sess_keyword} = $search_text;
        $SESSION->{$sess_sort} = $sort;

        $this->keyword = $search_text;
        $ret  = array();
        $ret['nologin'] = true;
        $ret['page'] = (int)$page;
        if ($ret['page'] < 1) {
            $ret['page'] = 1;
        }
        $start = ($ret['page'] - 1) * self::YOUTUBE_THUMBS_PER_PAGE + 1;
        $max = self::YOUTUBE_THUMBS_PER_PAGE;
        $ret['list'] = $this->_get_collection($search_text, $start, $max, $sort);
        $ret['norefresh'] = true;
        $ret['nosearch'] = true;
        // If the number of results is smaller than $max, it means we reached the last page.
        $ret['pages'] = (count($ret['list']) < $max) ? $ret['page'] : -1;
        return $ret;
    }

    /**
     * Private method to get youtube search results
     * @param string $keyword
     * @param int $start
     * @param int $max max results
     * @param string $sort
     * @throws moodle_exception If the google API returns an error.
     * @return array
     */
    private function _get_collection($keyword, $start, $max, $sort) {
        global $SESSION;

        // The new API doesn't use "page" numbers for browsing through results.
        // It uses a prev and next token in each set that you need to use to
        // request the next page of results.
        $sesspagetoken = 'youtube_'.$this->id.'_nextpagetoken';
        $pagetoken = '';
        if ($start > 1 && isset($SESSION->{$sesspagetoken})) {
            $pagetoken = $SESSION->{$sesspagetoken};
        }

        $list = array();
        $error = null;
        try {
            $this->init_youtube_service(); // About to use the service, ensure it's loaded.
            $response = $this->service->search->listSearch('id,snippet', array(
                'q' => $keyword,
                'maxResults' => $max,
                'order' => $sort,
                'pageToken' => $pagetoken,
                'type' => 'video',
                'videoEmbeddable' => 'true',
            ));

            // Track the next page token for the next request (when a user
            // scrolls down in the file picker for more videos).
            $SESSION->{$sesspagetoken} = $response['nextPageToken'];

            foreach ($response['items'] as $result) {
                $title = $result->snippet->title;
                $source = 'https://www.youtube.com/watch?v=' . $result->id->videoId . '#' . $title;
                $thumb = $result->snippet->getThumbnails()->getDefault();

                $list[] = array(
                    'shorttitle' => $title,
                    'thumbnail_title' => $result->snippet->description,
                    'title' => $title.'.avi', // This is a hack so we accept this file by extension.
                    'thumbnail' => $thumb->url,
                    'thumbnail_width' => (int)$thumb->width,
                    'thumbnail_height' => (int)$thumb->height,
                    'size' => '',
                    'date' => '',
                    'source' => $source,
                );
            }
        } catch (Google_Service_Exception $e) {
            // If we throw the google exception as-is, we may expose the apikey
            // to end users. The full message in the google exception includes
            // the apikey param, so we take just the part pertaining to the
            // actual error.
            $error = $e->getErrors()[0]['message'];
            throw new moodle_exception('apierror', 'repository_youtube', '', $error);
        }

        return $list;
    }

    /**
     * Youtube plugin doesn't support global search
     */
    public function global_search() {
        return false;
    }

    public function get_listing($path='', $page = '') {
        return array();
    }

    /**
     * Generate search form
     */
    public function print_login($ajax = true) {
        $ret = array();
        $search = new stdClass();
        $search->type = 'text';
        $search->id   = 'youtube_search';
        $search->name = 's';
        $search->label = get_string('search', 'repository_youtube').': ';
        $sort = new stdClass();
        $sort->type = 'select';
        $sort->options = array(
            (object)array(
                'value' => 'relevance',
                'label' => get_string('sortrelevance', 'repository_youtube')
            ),
            (object)array(
                'value' => 'date',
                'label' => get_string('sortpublished', 'repository_youtube')
            ),
            (object)array(
                'value' => 'rating',
                'label' => get_string('sortrating', 'repository_youtube')
            ),
            (object)array(
                'value' => 'viewCount',
                'label' => get_string('sortviewcount', 'repository_youtube')
            )
        );
        $sort->id = 'youtube_sort';
        $sort->name = 'youtube_sort';
        $sort->label = get_string('sortby', 'repository_youtube').': ';
        $ret['login'] = array($search, $sort);
        $ret['login_btn_label'] = get_string('search');
        $ret['login_btn_action'] = 'search';
        $ret['allowcaching'] = true; // indicates that login form can be cached in filepicker.js
        return $ret;
    }

    /**
     * file types supported by youtube plugin
     * @return array
     */
    public function supported_filetypes() {
        return array('video');
    }

    /**
     * Youtube plugin only return external links
     * @return int
     */
    public function supported_returntypes() {
        return FILE_EXTERNAL;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }

    /**
     * Add plugin settings input to Moodle form.
     * @param object $mform
     * @param string $classname
     */
    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform, $classname);
        $apikey = get_config('youtube', 'apikey');
        if (empty($apikey)) {
            $apikey = '';
        }

        $mform->addElement('text', 'apikey', get_string('apikey', 'repository_youtube'), array('value' => $apikey, 'size' => '40'));
        $mform->setType('apikey', PARAM_RAW_TRIMMED);
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');

        $mform->addElement('static', null, '',  get_string('information', 'repository_youtube'));
    }

    /**
     * Names of the plugin settings
     * @return array
     */
    public static function get_type_option_names() {
        return array('apikey', 'pluginname');
    }
}
