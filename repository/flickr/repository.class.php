<?php
/**
 * repository_flickr class
 * This is a subclass of repository class
 *
 * @author Dongsheng Cai
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/repository/lib.php');
require_once($CFG->dirroot.'/repository/flickr/'.'phpFlickr.php');

class repository_flickr extends repository{
    var $flickr;
    var $photos;
    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        $options['page']    = optional_param('p', 1, PARAM_INT);
        $options['api_key'] = 'bf85ae2b5b105a2c645f32a32cd6ad59';
        $options['secret']  = '7cb2f9d7cf70aebe';
        parent::__construct($repositoryid, $context, $options);
        $this->flickr = new phpFlickr($this->options['api_key'], $this->options['secret']);

        $reset = optional_param('reset', 0, PARAM_INT);
        if(!empty($reset)) {
            unset($SESSION->flickrmail);
            set_user_preference('flickrmail', '');
        }

        if(!empty($SESSION->flickrmail)) {
            $action = 'list';
        } else {
            $options['flickrmail'] = optional_param('flickrmail', '', PARAM_RAW);
            if(!empty($options['flickrmail'])) {
                $people = $this->flickr->people_findByEmail($options['flickrmail']);
                if(!empty($people)) {
                    $remember = optional_param('remember', '', PARAM_RAW);
                    if(!empty($remember)) {
                        set_user_preference('flickrmail', $options['flickrmail']);
                    }
                    $SESSION->flickrmail = $options['flickrmail'];
                    $action = 'list';
                }
            } else {
                if($account = get_user_preferences('flickrmail', '')){
                    $SESSION->flickrmail = $account;
                    $action = 'list';
                }
            }
        }
    }
    public function print_login($ajax = true){
        global $SESSION;
        if(empty($SESSION->flickrmail)) {
        $str =<<<EOD
            <form id="moodle-repo-login">
            <label for="account">Flickr Account (Email)</label>
            <input type='text' name='flickrmail' id='account' />
            <input type='hidden' name='id' value='$this->repositoryid' /><br/>
            <input type='checkbox' name='remember' value='true' /> Remember <br/>
            <a href="###" onclick="dologin()">Go</a>
            </form>
EOD;
            if($ajax){
                $ret = array();
                $ret['l'] = $str;
                return $ret;
            }else{
                echo $str;
            }
        } else {
            return $this->get_listing();
        }
    }
    public function get_listing($path = '1', $search = ''){
        global $SESSION;
        $people = $this->flickr->people_findByEmail($SESSION->flickrmail);
        $photos_url = $this->flickr->urls_getUserPhotos($people['nsid']);

        if(!empty($search)) {
            // do searching, if $path is not empty, ignore it.
            $photos = $this->flickr->photos_search(array('user_id'=>$people['nsid'], 'text'=>$search));
        } elseif(!empty($path) && empty($search)) {
            $photos = $this->flickr->people_getPublicPhotos($people['nsid'], null, 36, $path);
        }

        $ret = new stdclass;
        $ret->url   = $photos_url;
        $ret->list  = array();
        $ret->pages = $photos['pages'];
        if(is_int($path) && $path <= $ret->pages) {
            $ret->page = $path;
        } else {
            $ret->page = 1;
        }
        foreach ($photos['photo'] as $p) {
            if(empty($p['title'])) {
                $p['title'] = get_string('notitle', 'repository_flickr');
            }
            $ret->list[] =
                array('title'=>$p['title'],'source'=>'http://farm2.static.flickr.com/'.$p['server'].'/'.$p['id'].'_'.$p['secret'].'_b.jpg','id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, "Square"));
        }
        return $ret;
    }
    public function print_listing(){
        if(empty($this->photos)){
            $this->get_listing();
        }
        $str = '';
        $str .= '<h2>Account: <span>'.$this->photos['a'].'</span></h2>';
        foreach ((array)$this->photos['photo'] as $photo) {
            $str .= "<a href='".$this->photos['url'].$photo[id]."'>";
            $str .= "<img border='0' alt='$photo[title]' ".
                "src=" . $photo['thumbnail'] . ">";
            $str .= "</a>";
            $i++;

            if ($i % 4 == 0) {
                $str .= "<br/>";
            }
        }
        $str .= <<<EOD
<style type='text/css'>
#paging{margin-top: 10px; clear:both}
#paging a{padding: 4px; border: 1px solid gray}
</style>
EOD;
        $str .= '<div id="paging">';
        for($i=1; $i <= $this->photos['pages']; $i++) {
            $str .= '<a href="###" onclick="cr('.$this->repositoryid.', '.$i.', 0)">';
            $str .= $i;
            $str .= '</a> ';
        }
        $str .= '</div>';
        echo $str;
    }
    public function print_search(){
        echo '<input type="text" name="Search" value="search terms..." size="40" class="right"/>';
        return true;
    }
}
?>
