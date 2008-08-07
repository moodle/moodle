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
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(dirname(dirname(__FILE__)).'/lib/filelib.php');

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
    public function __construct($repositoryid, $contextid = SITEID, $options = array()){
        $this->repositoryid = $repositoryid;
        $this->context      = get_context_instance_by_id($contextid);
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
    public function get_file($url, $file = '') {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/repository/download')) {
            mkdir($CFG->dataroot.'/repository/download/', 0777, true);
        }
        if(is_dir($CFG->dataroot.'/repository/download')) {
            $dir = $CFG->dataroot.'/repository/download/';
        }
        if(empty($file)) {
            $file = uniqid('repo').'_'.time().'.tmp';
        }
        if(file_exists($dir.$file)){
            $file = uniqid('m').$file;
        }
        $fp = fopen($dir.$file, 'w');
        $c = new curl;
        $c->download(array(
            array('url'=>$url, 'file'=>$fp)
        ));
        return $dir.$file;
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
    public function store_login($username = '', $password = '', $userid = 1) {
        global $DB;

        $repository = new stdclass;
        if (!empty($this->repositoryid)) {
            $repository->id = $this->repositoryid;
        } else {
            $repository->userid         = $userid;
            $repository->repositorytype = $this->type;
            $repository->contextid      = $this->context->id;
        }
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
 * exception class for repository api
 *
 */

class repository_exception extends moodle_exception {
}

/**
 * Listing object describing a listing of files and directories
 */

abstract class repository_listing {
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

function repository_user_instances($context){
    global $DB, $CFG, $USER;
    $params = array();
    $sql = 'SELECT * FROM {repository} r WHERE ';
    $sql .= ' (r.userid = 0 or r.userid = ?) ';
    $params[] = $USER->id;
    if($context->id == SITEID) {
        $sql .= 'AND (r.contextid = ?)';
        $params[] = SITEID;
    } else {
        $sql .= 'AND (r.contextid = ? or r.contextid = ?)';
        $params[] = SITEID;
        $params[] = $context->id;
    }
    if(!$repos = $DB->get_records_sql($sql, $params)) {
        $repos = array();
    }
    return $repos;
}

function repository_instance($id){
    global $DB, $CFG;

    if (!$instance = $DB->get_record('repository', array('id' => $id))) {
        return false;
    }
    require_once($CFG->dirroot . '/repository/'. $instance->repositorytype 
        . '/repository.class.php');
    $classname = 'repository_' . $instance->repositorytype;
    return new $classname($instance->id, $instance->contextid);
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

function move_to_filepool($path, $name, $itemid) {
    global $DB, $CFG, $USER;
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $entry = new object();
    $entry->filearea  = 'user_draft';
    $entry->contextid = $context->id;
    $entry->filename  = $name;
    $entry->filepath  = '/';
    $entry->timecreated  = time();
    $entry->timemodified = time();
    if(is_numeric($itemid)) {
        $entry->itemid = $itemid;
    } else {
        $entry->itemid = 0;
    }
    $entry->mimetype     = mimeinfo('type', $path);
    $entry->userid       = $USER->id;
    $fs = get_file_storage();
    $browser = get_file_browser();
    if ($file = $fs->create_file_from_pathname($entry, $path)) {
        $id = json_encode($file->get_itemid());
        $ret = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        return array('url'=>$ret->get_url(),'id'=>$file->get_itemid());
    } else {
        return null;
    }
}

// TODO
// Need to pass contextid and contextlevel here
function get_repository_client($context){
    global $CFG, $USER;
    $suffix = uniqid();
    $strsubmit    = get_string('submit', 'repository');
    $strlistview  = get_string('listview', 'repository');
    $strthumbview = get_string('thumbview', 'repository');
    $strsearch    = get_string('search', 'repository');
    $strlogout    = get_string('logout', 'repository');
    $strloading   = get_string('loading', 'repository');
    $strtitle     = get_string('title', 'repository');
    $filename     = get_string('filename', 'repository');
    $strsync      = get_string('sync', 'repository');
    $strdownload  = get_string('download', 'repository');
    $strback      = get_string('back', 'repository');
    $strclose     = get_string('close', 'repository');

    $js = <<<EOD
    <style type="text/css">
    #list-$suffix{line-height: 1.5em}
    #list-$suffix a{ padding: 3px }
    #list-$suffix li a:hover{ background: gray; color:white; }
    #paging-$suffix{margin:10px 5px; clear:both}
    #paging-$suffix a{padding: 4px; border: 1px solid gray}
    #panel-$suffix{padding:0;margin:0; text-align:left;}
    .file_name{color:green;}
    .file_date{color:blue}
    .file_size{color:gray}
    .grid{width:80px; float:left;text-align:center;}
    .grid div{width: 80px; height: 36px; overflow: hidden}
    .repo-opt{font-size: 10px;color:red}
    </style>
    <style type="text/css">
    @import "$CFG->wwwroot/lib/yui/reset-fonts-grids/reset-fonts-grids.css";
    @import "$CFG->wwwroot/lib/yui/reset/reset-min.css";
    @import "$CFG->wwwroot/lib/yui/resize/assets/skins/sam/resize.css";
    @import "$CFG->wwwroot/lib/yui/container/assets/skins/sam/container.css";
    @import "$CFG->wwwroot/lib/yui/layout/assets/skins/sam/layout.css";
    @import "$CFG->wwwroot/lib/yui/button/assets/skins/sam/button.css";
    </style>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/element/element-beta-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/dragdrop/dragdrop-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/container/container-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/resize/resize-beta-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/layout/layout-beta-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/connection/connection-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/json/json-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/button/button-min.js"></script>
    <script type="text/javascript" src="$CFG->wwwroot/lib/yui/selector/selector-beta-min.js"></script>
    <script>
    var repository_client_$suffix = (function() {
        // private static field
        var dver = '1.0';
        // private static methods
        function alert_version(){
            alert(dver);
        }
        function _client(){
            // public varible
            this.name = 'repository_client_$suffix';
            // private varible
            var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event, layout = null, resize = null;
            var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");
            var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));
            var PANEL_BODY_PADDING = (10*2);
            var btn_list = {label: '$strlistview', value: 'l', checked: true, onclick: {fn: _client.viewlist}};
            var btn_thumb = {label: '$strthumbview', value: 't', onclick: {fn: _client.viewthumb}};
            var select = new YAHOO.util.Element('select');
            var list = null;
            var resize = null;
            var panel = new YAHOO.widget.Panel('file-picker-$suffix', {
                draggable: true,
                close: true,
                underlay: 'none',
                width: '510px',
                zindex: 666666,
                xy: [100, 100]
            });
            // construct code section
            {
                panel.setHeader('$strtitle');
                panel.setBody('<div id="layout-$suffix"></div>');
                panel.beforeRenderEvent.subscribe(function() {
                    Event.onAvailable('layout-$suffix', function() {
                        layout = new YAHOO.widget.Layout('layout-$suffix', {
                            height: 400, width: 490,
                            units: [
                                {position: 'top', height: 32, resize: false, 
                                body:'<div class="yui-buttongroup" id="repo-viewbar-$suffix"></div>', gutter: '2'},
                                {position: 'left', width: 150, resize: true, 
                                body:'<ul id="repo-list-$suffix"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                                {position: 'center', body: '<div id="panel-$suffix"></div>', 
                                scroll: true, gutter: '0 2 0 0' }
                            ]
                        });
                        layout.render();
                    });
                });
                resize = new YAHOO.util.Resize('file-picker-$suffix', {
                    handles: ['br'],
                    autoRatio: true,
                    status: true,
                    minWidth: 380,
                    minHeight: 400
                });
                resize.on('resize', function(args) {
                    var panelHeight = args.height;
                    var headerHeight = this.header.offsetHeight; // Content + Padding + Border
                    var bodyHeight = (panelHeight - headerHeight);
                    var bodyContentHeight = (IE_QUIRKS) ? bodyHeight : bodyHeight - PANEL_BODY_PADDING;
                    YAHOO.util.Dom.setStyle(this.body, 'height', bodyContentHeight + 'px');
                    if (IE_SYNC) {
                        this.sizeUnderlay();
                        this.syncIframe();
                    }
                    layout.set('height', bodyContentHeight);
                    layout.set('width', (args.width - PANEL_BODY_PADDING));
                    layout.resize();

                }, panel, true);
                _client.viewbar = new YAHOO.widget.ButtonGroup({
                    id: 'btngroup-$suffix',
                    name: 'buttons',
                    disabled: true,
                    container: 'repo-viewbar-$suffix'
                    });
            }
            // public method
            this.show = function(){
                panel.show();
            }
            this.hide = function(){
                panel.hide();
            }
            this.create_picker = function(){
                // display UI
                panel.render();
                _client.viewbar.addButtons([btn_list, btn_thumb]);
                // init repository list
                list = new YAHOO.util.Element('repo-list-$suffix');
                list.on('contentReady', function(e){
                    for(var i=0; i<_client.repos.length; i++) {
                        var repo = _client.repos[i];
                        li = document.createElement('ul');
                        li.innerHTML = '<a href="###" id="repo-call-$suffix-'+repo.id+'">'+
                            repo.repositoryname+'</a><br/>';
                        li.innerHTML += '<a href="###" class="repo-opt" onclick="repository_client_$suffix.search('+repo.id+')">$strsearch</a>';
                        li.innerHTML += '<a href="###" class="repo-opt" id="repo-logout-$suffix-'+repo.id+'">$strlogout</a>';
                        li.id = 'repo-$suffix-'+repo.id;
                        this.appendChild(li);
                        var e = new YAHOO.util.Element('repo-call-$suffix-'+repo.id);
                        e.on('click', function(e){
                            var re = /repo-call-$suffix-(\d+)/i;
                            var id = this.get('id').match(re);
                            repository_client_$suffix.req(id[1], 1, 0);
                            });
                        e = new YAHOO.util.Element('repo-logout-$suffix-'+repo.id);
                        e.on('click', function(e){
                            var re = /repo-logout-$suffix-(\d+)/i;
                            var id = this.get('id').match(re);
                            repository_client_$suffix.req(id[1], 1, 1);
                            });
                        repo = null;
                    }
                    });
            }
        }
        // public static varible
        _client.repos = [];
        _client.repositoryid = 0;
        _client.datasource, 
        _client.viewmode = 0;
        _client.viewbar =null;
        // public static mehtod
        _client.postdata = function(obj) {
            var str = '';
            for(k in obj) {
                if(obj[k] instanceof Array) {
                    for(i in obj[k]) {
                        str += (encodeURIComponent(k) +'[]='+encodeURIComponent(obj[k][i]));
                        str += '&';
                    }
                } else {
                    str += encodeURIComponent(k) +'='+encodeURIComponent(obj[k]);
                    str += '&';
                }
            }
            return str;
        }
        _client.loading = function(){
            var panel = new YAHOO.util.Element('panel-$suffix');
            panel.get('element').innerHTML = '<span>$strloading</span>';
        }
        _client.rename = function(oldname, url){
            var panel = new YAHOO.util.Element('panel-$suffix');
            var html = '<div>';
            html += '<label for="newname">$filename</label>';
            html += '<input type="text" id="newname-$suffix" value="'+oldname+'" /><br/>';
            html += '<label for="syncfile">$strsync</label>';
            html += '<input type="checkbox" id="syncfile-$suffix" /><br/>';
            html += '<input type="hidden" id="fileurl-$suffix" value="'+url+'" />';
            html += '<input type="button" onclick="repository_client_$suffix.download()" value="$strdownload" />';
            html += '<a href="###" onclick="repository_client_$suffix.viewfiles()">$strback</a>';
            html += '</div>';
            panel.get('element').innerHTML = html;
        }
        _client.print_login = function(){
            var panel = new YAHOO.util.Element('panel-$suffix');
            var data = _client.datasource.l;
            var str = '';
            for(var k in data){
                str += '<p>';
                var lable_id = '';
                var field_id = '';
                var field_value = '';
                if(data[k].id){
                    lable_id = ' for="'+data[k].id+'"';
                    field_id = ' id="'+data[k].id+'"';
                }
                if (data[k].label) {
                    str += '<label'+lable_id+'>'+data[k].label+'</label>';
                }
                if(data[k].value){
                    field_value = ' value="'+data[k].value+'"';
                }
                str += '<input type="'+data[k].type+'"'+' name="'+data[k].name+'"'+field_id+field_value+' />';
                str += '</p>';
            }
            str += '<p><input type="button" onclick="repository_client_$suffix.login()" value="$strsubmit" /></p>';
            panel.get('element').innerHTML = str;
        }

        _client.viewfiles = function(){
            if(_client.viewmode) {
                _client.viewthumb();
            } else {
                _client.viewlist();
            }
        }

        _client.viewthumb = function(){
            var panel = new YAHOO.util.Element('panel-$suffix');
            _client.viewbar.check(1);
            obj = _client.datasource.list;
            var str = '';
            str += _client.makepage();
            for(k in obj){
                str += '<div class="grid">';
                str += '<img title="'+obj[k].title+'" src="'+obj[k].thumbnail+'" />';
                str += '<div style="text-align:center">';
                str += ('<input type="radio" title="'+obj[k].title
                        +'" name="selected-files" value="'+obj[k].source
                        +'" onclick=\'repository_client_$suffix.rename("'+obj[k].title+'", "'
                        +obj[k].source+'")\' />');
                str += obj[k].title+'</div>';
                str += '</div>';
            }
            panel.get('element').innerHTML = str;
            _client.viewmode = 1;
            return str;
        }

        _client.viewlist = function(){
            var panel = new YAHOO.util.Element('panel-$suffix');
            var str = '';
            _client.viewbar.check(0);
            obj = _client.datasource.list;
            str += _client.makepage();
            var re = new RegExp();
            re.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
            for(k in obj){
                str += ('<input type="radio" title="'+obj[k].title+'" name="selected-files" value="'+obj[k].source+'" onclick=\'repository_client_$suffix.rename("'+obj[k].title+'", "'+obj[k].source+'")\' /> ');
                if(re.test(obj[k].source)) {
                    str += '<a class="file_name" href="'+obj[k].source+'">'+obj[k].title+'</a>';
                } else {
                    str += '<span class="file_name" >'+obj[k].title+'</span>';
                }
                str += '<br/>';
                str += '<label>Date: </label><span class="file_date">'+obj[k].date+'</span><br/>';
                str += '<label>Size: </label><span class="file_size">'+obj[k].size+'</span>';
                str += '<br/>';
            }
            panel.get('element').innerHTML = str;
            _client.viewmode = 0;
            return str;
        }
        // XXX: An ugly hack to show paging for flickr
        _client.makepage = function(){
            var str = '';
            if(_client.datasource.pages){
                str += '<div id="paging-$suffix">';
                for(var i = 1; i <= _client.datasource.pages; i++) {
                    str += '<a onclick="repository_client_$suffix.req('+_client.repositoryid+', '+i+', 0)" href="###">';
                    str += String(i);
                    str += '</a> ';
                }
                str += '</div>';
            }
            return str;
        }
        _client.download = function(){
            var title = document.getElementById('newname-$suffix').value;
            var file = document.getElementById('fileurl-$suffix').value;
            var itemid = 1;
            if(_client.itemid){
                itemid = _client.itemid;
            }
            _client.loading();
            var trans = YAHOO.util.Connect.asyncRequest('POST', 
                '$CFG->wwwroot/repository/ws.php?ctx_id=$context->id&repo_id='+_client.repositoryid+
                    '&action=download', 
                _client.dlfile, _client.postdata({'itemid': itemid, 'env':_client.env, 'file':file, 'title':title}));
        }
        _client.login = function(){
            var obj = {};
            var data = _client.datasource.l;
            for (var k in data) {
                var el = document.getElementsByName(data[k].name)[0];
                obj[data[k].name] = '';
                if(el.type == 'checkbox') {
                    obj[data[k].name] = el.checked;
                } else {
                    obj[data[k].name] = el.value;
                }
            }
            obj['env'] = _client.env;
            obj['ctx_id'] = $context->id;
            _client.loading();
            var trans = YAHOO.util.Connect.asyncRequest('POST', 
                '$CFG->wwwroot/repository/ws.php', _client.callback,
                _client.postdata(obj));
        }
        _client.end = function(str){
            _client.target.value = str;
            _client.formcallback();
            _client.instance.hide();
            _client.viewfiles();
        }
        _client.callback = {
            success: function(o) {
                var panel = new YAHOO.util.Element('panel-$suffix');
                try {
                    var ret = YAHOO.lang.JSON.parse(o.responseText);
                } catch(e) {
                    alert('Callback: Invalid JSON String'+o.responseText);
                };
                if(ret.e){
                    panel.get('element').innerHTML = ret.e;
                    return;
                }
                _client.datasource = ret;
                if(_client.datasource.l){
                    _client.print_login();
                } else if(_client.datasource.list) {
                    if(_client.viewmode) {
                        _client.viewthumb();
                    } else {
                        _client.viewlist();
                    }
                }
            }
        }
        _client.dlfile = {
            success: function(o) {
                var panel = new YAHOO.util.Element('panel-$suffix');
                try {
                    var ret = YAHOO.lang.JSON.parse(o.responseText);
                } catch(e) {
                    alert('Invalid JSON String'+o.responseText);
                }
                if(ret.e){
                    panel.get('element').innerHTML = ret.e;
                    return;
                }
                var html = '<h1>Download Successfully!</h1>';
                html += '<button onclick="repository_client_$suffix.end(\''+ret+'\')">Add!</button>';
                panel.get('element').innerHTML = html;
            }
        }
        // request file list or login
        _client.req = function(id, path, reset) {
            _client.viewbar.set('disabled', false);
            _client.loading();
            _client.repositoryid = id;
            var trans = YAHOO.util.Connect.asyncRequest('GET', '$CFG->wwwroot/repository/ws.php?ctx_id=$context->id&repo_id='+id+'&p='+path+'&reset='+reset+'&env='+_client.env, _client.callback);
        }
        _client.search = function(id){
            var data = window.prompt("What are you searching for?");
            if(data == null || data == '') {
                alert('nothing entered');
                return;
            }
            _client.viewbar.set('disabled', false);
            _client.loading();
            var trans = YAHOO.util.Connect.asyncRequest('GET', '$CFG->wwwroot/repository/ws.php?ctx_id=$context->id&repo_id='+id+'&s='+data+'&env='+_client.env, _client.callback);
        }
        return _client;
    })();
EOD;

    $repos = repository_user_instances($context);
    foreach($repos as $repo) {
        $js .= "\r\n";
        $js .= 'repository_client_'.$suffix.'.repos.push('.json_encode($repo).');'."\n";
    }
    $js .= "\r\n";

    $js .= <<<EOD
    function openpicker_$suffix(obj) {
        if(!repository_client_$suffix.instance) {
            repository_client_$suffix.env = obj.env;
            repository_client_$suffix.target = obj.target;
            repository_client_$suffix.instance = new repository_client_$suffix();
            repository_client_$suffix.instance.create_picker();
            if(obj.itemid){
                repository_client_$suffix.itemid = obj.itemid;
            }
            if(obj.callback){
                repository_client_$suffix.formcallback = obj.callback;
            } else {
                repository_client_$suffix.formcallback = function(){};
            }
        } else {
            repository_client_$suffix.instance.show();
        }
    }
    </script>
EOD;
    $html = <<<EOD
    <div class='yui-skin-sam'>
        <div id="file-picker-$suffix"></div>
    </div>
EOD;
    return array('html'=>$html, 'js'=>$js, 'suffix'=>$suffix);
}
