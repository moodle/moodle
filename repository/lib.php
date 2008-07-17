<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2008 onwards  Moodle Pty Ltd   http://moodle.com        //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * This is the base class of the repository class
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
 * @package repository
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once('../config.php');

abstract class repository {
    protected $options;
    public    $name;
    public    $context;
    public    $repositoryid;
    public    $listing;

    /**
     * Take an array as a parameter, which contains necessary information
     * of repository.
     *
     * @param string $parent The parent path, this parameter must
     * not be the folder name, it may be a identification of folder
     * @param string $search The text will be searched.
     * @return array the list of files, including meta infomation
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()){
        $this->name         = 'repository_base';
        $this->context      = $context;
        $this->repositoryid = $repositoryid;
        $this->options      = array();
        if (is_array($options)) {
            foreach ($options as $n => $v) {
                $this->options[$n] = $v;
            }
        }
    }

    public function __set($name, $value) {
        $this->options[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->options)){
            return $this->options[$name];
        }
        trigger_error('Undefined property: '.$name, E_USER_NOTICE);
        return null;
    }

    public function __isset($name) {
        return isset($this->options[$name]);
    }

    public function __toString() {
        return 'Repository class: '.__CLASS__;
    }
    /**
     * Given a URL, get a file from there.
     * @param string $url the url of file
     * @param string $file save location
     */
    public function get_file($url) {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/repository/download')) {
            mkdir($CFG->dataroot.'/repository/download/', 0777, true);
        }
        if(is_dir($CFG->dataroot.'/repository/download')) {
            $dir = $CFG->dataroot.'/repository/download/';
        }
        if(file_exists($CFG->dirroot.'/repository/curl.class.php')) {
            $file = uniqid('repo').'_'.time().'.tmp';
            $fp = fopen($dir.$file, 'w');
            require_once($CFG->dirroot.'/repository/curl.class.php');
            $c = new curl;
            $c->download(array(
                array('url'=>$url, 'file'=>$fp)
            ));
            return $dir.$file;
        } else {
            return null;
        }
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
    public function print_listing($listing = array(), $print=true) {
        if(empty($listing)){
            $listing = $this->get_listing();
        }
        if (empty($listing)) {
            $str = '';
        } else {
            $count = 0;
            $str = '<table>';
            foreach ($listing as $v){
                $str .= '<tr id="entry_'.$count.'">';
                $str .= '<td><input type="checkbox" /></td>';
                $str .= '<td>'.$v['name'].'</td>';
                $str .= '<td>'.$v['size'].'</td>';
                $str .= '<td>'.$v['date'].'</td>';
                $str .= '</tr>';
                $count++;
            }
            $str .= '</table>';
        }
        if ($print){
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
    public function store_login($username = '', $password = '', $userid = -1, $contextid = SITEID) {
        global $DB;

        $repository = new stdclass;
        $repository->userid         = $userid;
        $repository->repositorytype = $this->name;
        $repository->contextid      = $contextid;
        if ($entry = $DB->get_record('repository', $repository)) {
            $repository->id = $entry->id;
            $repository->username = $username;
            $repository->password = $password;
            return $DB->update_record('repository', $repository);
        } else {
            $repository->username = $username;
            $repository->password = $password;
            return $DB->insert_record('repository', $repository);
        }
        return false;
    }

    /**
     * Defines operations that happen occasionally on cron
     *
     */
    public function cron() {
        return true;
    }
}


/**
 * Listing object describing a listing of files and directories
 */

abstract class repository_listing {
}
/**
 * This class is used by cURL class, use case:
 *
 * $CFG->repository_cache_expire = 120;
 * $c = new curl(array('cache'=>true));
 * $ret = $c->get('http://www.google.com');
 *
 */
class repository_cache {
    public $dir = '';
    function __construct(){
        global $CFG;
        if (!file_exists($CFG->dataroot.'/repository/cache')) {
            mkdir($CFG->dataroot.'/repository/cache/', 0777, true);
        }
        if(is_dir($CFG->dataroot.'/repository/cache')) {
            $this->dir = $CFG->dataroot.'/repository/cache/';
        }
    }
    public function get($param){
        global $CFG;
        $filename = md5(serialize($param));
        if(file_exists($this->dir.$filename)) {
            $lasttime = filemtime($this->dir.$filename);
            if(time()-$lasttime > $CFG->repository_cache_expire) {
                return false;
            } else {
                $fp = fopen($this->dir.$filename, 'r');
                $size = filesize($this->dir.$filename);
                $content = fread($fp, $size);
                return unserialize($content);
            }
        }
        return false;
    }
    public function set($param, $val){
        $filename = md5(serialize($param));
        $fp = fopen($this->dir.$filename, 'w');
        fwrite($fp, serialize($val));
        fclose($fp);
    }
    public function cleanup($expire){
        if($dir = opendir($this->dir)){
            while (false !== ($file = readdir($dir))) {
                if(!is_dir($file) && $file != '.' && $file != '..') {
                    $lasttime = @filemtime($this->dir.$file);
                    if(time() - $lasttime > $expire){
                        @unlink($this->dir.$file);
                    }
                }
            }
        }
    }
}

function repository_set_option($id, $position, $config = array()){
    global $DB;
    $repository = new stdclass;
    $position = (int)$position;
    $config   = serialize($config);
    if( $position < 1 || $position > 5){
        print_error('invalidoption', 'repository', '', $position);
    }
    if ($entry = $DB->get_record('repository', array('id'=>$id))) {
        $option = 'option'.$position;
        $repository->id = $entry->id;
        $repository->$option = $config;
        return $DB->update_record('repository', $repository);
    }
    return false;
}
function repository_get_option($id, $position){
    global $DB;
    $entry = $DB->get_record('repository', array('id'=>$id));
    $option = 'option'.$position;
    $ret = (array)unserialize($entry->$option);
    return $ret;
}
function repository_get_plugins(){
    global $CFG;
    $repo = $CFG->dirroot.'/repository/';
    $ret = array();
    if($dir = opendir($repo)){
        while (false !== ($file = readdir($dir))) {
            if(is_dir($file) && $file != '.' && $file != '..'
                && file_exists($repo.$file.'/repository.class.php')){
                require_once($repo.$file.'/version.php');
                $ret[] = array('name'=>$plugin->name,
                        'version'=>$plugin->version,
                        'path'=>$repo.$file,
                        'settings'=>file_exists($repo.$file.'/settings.php'));
            }
        }
    }
    return $ret;
}
