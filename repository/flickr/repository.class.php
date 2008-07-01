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
        global $SESSION, $action;
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

        if(!empty($SESSION->filckrmail)){
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
    public function print_login(){
        global $SESSION;
        if(empty($SESSION->flickrmail)) {
        echo <<<EOD
            <form action="picker.php">
            <label for="account">Flickr Account (Email)</lable>
            <input type='text' name='flickrmail' id='account' />
            <input type='hidden' name='id' value='$this->repositoryid' />
            <input type='checkbox' name='remember' value='true' /> Remember <br/>
            <input type='submit' value='Go' />
            </form>
EOD;
        } else {
            $this->print_listing();
        }
        //echo '<a href="?id='.$this->repositoryid.'&action=list">See flickr photos list</a>';
        return true;
    }
    public function get_listing($path = '0', $search = ''){
        global $SESSION;
        $people = $this->flickr->people_findByEmail($SESSION->flickrmail);
        $photos_url = $this->flickr->urls_getUserPhotos($people['nsid']);
        $photos = $this->flickr->people_getPublicPhotos($people['nsid'], null, 36, $this->page);
        $this->photos = array('a'=>$SESSION->flickrmail, 'u'=>$photos_url, 'p'=>$photos);
        return $this->photos;
    }
    public function print_listing(){
        if(empty($this->photos)){
            $this->get_listing();
        }
        echo '<h2>Account: <span>'.$this->photos['a'].'</span></h2>';
        echo '<a href="picker.php?id='.$this->repositoryid.'&reset=1">Change user</a>';
        echo '<hr/>';
        foreach ((array)$this->photos['p']['photo'] as $photo) {
            echo "<a href='".$this->photos['u'].$photo[id]."'>";
            echo "<img border='0' alt='$photo[title]' ".
                "src=" . $this->flickr->buildPhotoURL($photo, "Square") . ">";
            echo "</a>";
            $i++;
            // If it reaches the sixth photo, insert a line break
            if ($i % 6 == 0) {
                echo "<br/>";
            }
        }
        echo <<<EOD
<style type='text/css'>
#paging{margin-top: 10px; clear:both}
#paging a{padding: 4px; border: 1px solid gray}
</style>
EOD;
        echo '<div id="paging">';
        for($i=1; $i <= $this->photos['p']['pages']; $i++) {
            echo '<a href="picker.php?id='.$this->repositoryid.'&action=list&p='.$i.'">';
            echo $i;
            echo '</a> ';
        }
        echo '</div>';
    }
    public function print_search(){
        echo '<input type="text" name="Search" value="search terms..." size="40" class="right"/>';
        return true;
    }
}
?>
