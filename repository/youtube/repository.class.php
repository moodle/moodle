<?php
/**
 * repository_youtube class
 *
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_youtube extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $SESSION;
        $options['keyword'] = optional_param('youtube_keyword', '', PARAM_RAW);
        parent::__construct($repositoryid, $context, $options);
        $this->session_name = "youtube_keyword_".$this->id;
        if (!empty($this->keyword)) {
            $SESSION->{$this->session_name} = $this->keyword;
        }
    }

    public function check_login() {
        global $SESSION;
        return !empty($SESSION->{$this->session_name});
    }

    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->session_name});
        return $this->print_login();
    }

    public function search($search_text) {
    }

    private function _get_collection($keyword, $start, $max, $sort) {
        $list = array();
        $this->feed_url = 'http://gdata.youtube.com/feeds/api/videos?vq=' . $keyword . '&format=5&start-index=' . $start . '&max-results=' .$max . '&orderby=' . $sort;
        $c = new curl(array('cache'=>true));
        $content = $c->get($this->feed_url);
		$xml = simplexml_load_string($content);
        $media = $xml->entry->children('http://search.yahoo.com/mrss/');
	    $links = $xml->children('http://www.w3.org/2005/Atom');
        foreach ($xml->entry as $entry) {
            $media = $entry->children('http://search.yahoo.com/mrss/');
            $title = $media->group->title;
            $attrs = $media->group->thumbnail->attributes();
            $thumbnail = $attrs['url'];
            $arr = explode('/', $entry->id);
            $id = $arr[count($arr)-1];
            $source = 'http://www.youtube.com/v/'.$id;
            $list[] = array(
                'title'=>(string)$title,
                'thumbnail'=>(string)$attrs['url'],
                'size'=>'',
                'date'=>'',
                'source'=>$source
            );
        } 
        return $list;
    }

    public function get_file($url, $title) {
        return $url;
    }
    public function global_search() {
        return false;
    }
    public function get_listing($path='') {
        global $CFG, $SESSION;
        $start = 1;
        $max   = 25;
        $sort  = "relevance";
        $list = array();
        $ret  = array();
        $ret['path'] = array(array('name'=>'Root', 'path'=>0));
        $ret['list'] = $this->_get_collection($SESSION->{$this->session_name}, $start, $max, $sort);
        $file = 'ts.txt';
        return $ret;
    }

    public function print_login($ajax = true) {
        if ($ajax) {
            $ret = array();
            $search = new stdclass;
            $search->type = 'text';
            $search->id   = 'youtube_search';
            $search->name = 'youtube_keyword';
            $search->label = get_string('search', 'repository_youtube').': ';
            $ret['login'] = array($search);
            return $ret;
        }
    }
}
