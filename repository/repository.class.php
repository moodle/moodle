<?php
/**
 * repository.class.php
 * This is the base class of repository class
 *
 * To use repository plugin, you need to create a new folder under repository/, named as the remote 
 * repository, the subclass must be defined in  the name

 *
 * class repository is an abstract class, some functions must be implemented in subclass.
 *
 * See an example of use of this library in repository/box/repository.class.php
 *
 * A few notes :
 *   // options are stored as serialized format in database 
 *   $options = array('api_key'=>'dmls97d8j3i9tn7av8y71m9eb55vrtj4', 
 *                  'auth_token'=>'', 'path_root'=>'/');
 *   $repo    = new repository_xxx($options);
 *   // print login page or a link to redirect to another page
 *   $repo->print_login();
 *   // call get_listing, and print result
 *   $repo->print_listing();
 *   // print a search box
 *   $repo->print_search();
 *
 * @version 1.0 dev
 * @package repository_api
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

abstract class repository{
    protected $options = array();
    private $name    = 'repository_base';

    /**
     * Take an array as a parameter, which contains necessary information
     * of repository.
     *
     * @param string $parent The parent path, this parameter must
     * not be the folder name, it may be a identification of folder
     * @param string $search The text will be searched.
     * @return array the list of files, including meta infomation
     */
    public function __construct($options = array()){
        if(is_array($options)){
            foreach($options as $n=>$v) {
                $this->options[$n] = $v;
            }
        }
    }

    public function __set($name, $value){
        $this->options[$name] = $value;
    }

    public function __get($name){
        if (array_key_exists($name, $this->options)){
            return $this->options[$name];
        }
        trigger_error('Undefined property: '.$name, E_USER_NOTICE);
        return null;
    }

    public function __isset($name){
        return isset($this->options[$name]);
    }

    public function __toString(){
        return 'Repository class: '.__CLASS__;
    }
    // Given a URL, get a file from there.
    public function get_file($url){
        return null;
    }

    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * @param string $parent The parent path, this parameter can
     * a folder name, or a identification of folder
     * @param string $search The text will be searched.
     * @return array the list of files, including meta infomation
     */
    abstract public function get_listing($parent = '/', $search = '');

    /**
     * Print a list or return string
     *
     * @param string $list
     * $list = array(
     *            array('name'=>'moodle.txt', 'size'=>12, 'path'=>'', 'date'=>''),
     *            array('name'=>'repository.txt', 'size'=>32, 'path'=>'', 'date'=>''),
     *            array('name'=>'forum.txt', 'size'=>82, 'path'=>'', 'date'=>''),
     *         );
     *
     * @param boolean $print if printing the listing directly
     *
     */
    public function print_listing($listing = array(), $print=true){
        if(empty($listing)){
            return false;
        }
        $count = 0;
        $str = '';
        $str = '<table>';
        foreach ($listing as $v){
            echo '<tr id="entry_'.$count.'">';
            echo '<td><input type="checkbox" /></td>';
            echo '<td>'.$v['name'].'</td>';
            echo '<td>'.$v['size'].'</td>';
            echo '<td>'.$v['date'].'</td>';
            echo '</tr>';
            $count++;
        }
        $str = '</table>';
        if($print){
            echo $str;
            return null;
        } else {
            return $str;
        }
        
    }
    
    /**
     * Show the login screen, if required
     * This is an abstract function, it must be overriden.
     * The specific plug-in need to specify authentication types in database
     * options field
     * Imagine following cases:
     * 1. no need of authentication
     * 2. Use username and password to authenticate
     * 3. Redirect to authentication page, in this case, the repository
     * will callback moodle with following common parameters:
     *    (1) boolean callback To tell moodle this is a callback
     *    (2) int     id       Specify repository ID 
     * The callback page need to use these parameters to init
     * the repository plug-ins correctly. Also, auth_token or ticket may
     * attach in the callback url, these must be taken into account too.
     *
     */
    abstract public function print_login();

    /**
     * Show the search screen, if required
     *
     * @return null
     */
    abstract public function print_search();

    /**
     * Cache login details for repositories
     *
     * @param string $username
     * @param string $password
     * @param string $userid The id of specific user
     * @return array the list of files, including meta infomation
     */
    public function store_login($username = '', $password = '', 
        $userid = -1, $contextid = SITEID) {
            global $DB;
            $repostory = new stdclass;
            $repostory->userid         = $userid;
            $repostory->repositorytype = $this->name;
            $repostory->contextid      = $contextid;
            if ($entry = $DB->get_record('repository', $repository)) {
                $repository->id = $entry->id;
                $DB->update_record('repository', $repository);
                $repository->username = $username;
                $repository->password = $password;
                return $repository->id;
            } else {
                $repository->username = $username;
                $repository->password = $password;
                $id = $DB->insert_record('repository', $repository);
                return $id;
            }
            return false;
    }

    /**
     * Defines operations that happen occasionally on cron
     *
     */
    public function cron(){
        return true;
    }
}

?>
