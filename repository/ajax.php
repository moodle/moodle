<?php
require_once('../config.php');
require_once('lib.php');
if(!empty($_GET['create'])) {
    $result = true;
    $entry = new stdclass;
    $entry->repositoryname = 'Box.net';
    $entry->repositorytype = 'boxnet';
    $entry->contextid = SITEID;
    $entry->userid = $USER->id;
    $entry->timecreated = time();
    $entry->timemodified = time();
    $result = $result && $DB->insert_record('repository', $entry);
    $entry->repositoryname = 'Flickr!';
    $entry->repositorytype = 'flickr';
    $entry->contextid = SITEID;
    $entry->userid = $USER->id;
    $entry->timecreated = time();
    $entry->timemodified = time();
    $result = $result && $DB->insert_record('repository', $entry);
    if($result){
        die('200');
    } else {
        die('403');
    }
}
?>
<html>
<head>
<title> Ajax picker demo page </title>
<?php
/*******************************************************\

  This file is a demo page for ajax repository file
  picker.

\*******************************************************/
$meta = <<<EOD
<link rel="stylesheet" type="text/css" href="../lib/yui/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/reset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/resize/assets/skins/sam/resize.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/layout/assets/skins/sam/layout.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/menu/assets/skins/sam/menu.css" />
<style type="text/css">
body {margin:0; padding:0; background: #FFF7C6;}
img{margin:0;padding:0;border:0}
h1{font-size: 36px}
#list{line-height: 1.5em}
#list a{ padding: 3px }
#list li a:hover{ background: gray; color:white; }
#paging{margin:10px 5px; clear:both}
#paging a{padding: 4px; border: 1px solid gray}
#panel{padding:0;margin:0; text-align:left;}
.file_name{color:green;}
.file_date{color:blue}
.file_size{color:gray}
.grid{width:80px; float:left;text-align:center;}
.grid div{width: 80px; height: 36px; overflow: hidden}
.repo-opt{font-size: 10px;color:red}
</style>
<script type="text/javascript" src="../lib/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="../lib/yui/event/event.js"></script>
<script type="text/javascript" src="../lib/yui/dom/dom.js"></script>
<script type="text/javascript" src="../lib/yui/element/element-beta.js"></script>
<script type="text/javascript" src="../lib/yui/dragdrop/dragdrop.js"></script>
<script type="text/javascript" src="../lib/yui/container/container.js"></script>
<script type="text/javascript" src="../lib/yui/resize/resize-beta.js"></script>
<script type="text/javascript" src="../lib/yui/animation/animation.js"></script>
<script type="text/javascript" src="../lib/yui/layout/layout-beta.js"></script>
<script type="text/javascript" src="../lib/yui/connection/connection.js"></script>
<script type="text/javascript" src="../lib/yui/json/json.js"></script>
<script type="text/javascript" src="../lib/yui/menu/menu.js"></script>
<script type="text/javascript" src="../lib/yui/button/button-debug.js"></script>
<script type="text/javascript" src="../lib/yui/selector/selector-beta.js"></script>
<script type="text/javascript" src="../lib/yui/logger/logger.js"></script>
EOD;
echo $meta;
?>
</head>
<body class=" yui-skin-sam">
<div id='control'>
    <h1>Open the picker</h1>
    <input type="button" id="con1" onclick='openpicker()' value="Open File Picker" style="font-size: 24px;padding: 1em" /> <br/>
    <input type='hidden' id="result">
</div>
<div>
    <div id="file-picker"></div>
</div>
<hr />

<div>
    <h1>Create Repository Instance</h1>
    <input type='button' id="create-repo" value="Create!" style="font-size: 24px;padding: 1em" />
<script type="text/javascript">
btn = document.getElementById('create-repo');
var create_cb = {
    success: function(o) {
        try{
            var ret = o.responseText;
        } catch(e) {
            alert(e);
        }
        if(ret == 200) {
            alert('Create Repository Instances successfully!');
            btn.value='Done';
        } else {
            alert('Failed to create repository instances.');
            btn.value='Created';
            btn.disabled = false;
        }
    }
}
if(btn){
    btn.onclick = function(){
        btn.value = 'waiting...';
        btn.disabled = true;
        var trans = YAHOO.util.Connect.asyncRequest('GET', 'ajax.php?create=true', create_cb);
    }
}
</script>
</div>
<script type="text/javascript">
var repository_client = (function() {
    // private static field
    var dver = '1.0';
    // private static methods
    function alert_version(){
        alert(dver);
    }
    function _client(){
        // public varible
        this.name = 'repository_client';
        // private varible
        var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event, layout = null, resize = null;
        var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");
        var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));
        var PANEL_BODY_PADDING = (10*2);
        var btn_list = {label: 'List', value: 'l', checked: true, onclick: {fn: _client.viewlist}};
        var btn_thumb = {label: 'Thumbnail', value: 't', onclick: {fn: _client.viewthumb}};
        var select = new YAHOO.util.Element('select');
        var list = null;
        var resize = null;
        var panel = new YAHOO.widget.Panel('file-picker', {
            draggable: true,
            close: false,
            underlay: 'none',
            width: '510px',
            xy: [100, 100]
        });
        // construct code section
        {
            panel.setHeader('Moodle Repository Picker');
            panel.setBody('<div id="layout"></div>');
            panel.beforeRenderEvent.subscribe(function() {
                Event.onAvailable('layout', function() {
                    layout = new YAHOO.widget.Layout('layout', {
                        height: 400, width: 490,
                        units: [
                            {position: 'top', height: 32, resize: false, 
                            body:'<div class="yui-buttongroup" id="repo-viewbar"></div>', gutter: '2'},
                            {position: 'left', width: 150, resize: true, 
                            body:'<ul id="repo-list"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                            {position: 'center', body: '<div id="panel"></div>', 
                            scroll: true, gutter: '0 2 0 0' }
                        ]
                    });
                    layout.render();
                });
            });
            resize = new YAHOO.util.Resize('file-picker', {
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
                id: 'btngroup',
                name: 'buttons',
                disabled: true,
                container: 'repo-viewbar'
                });
        }
        // public method
        this.create_picker = function(){
            // display UI
            panel.render();
            _client.viewbar.addButtons([btn_list, btn_thumb]);
            // init repository list
            list = new YAHOO.util.Element('repo-list');
            list.on('contentReady', function(e){
                for(var i=0; i<_client.repos.length; i++) {
                    var repo = _client.repos[i];
                    li = document.createElement('ul');
                    li.innerHTML = '<a href="###" id="repo-call-'+repo.id+'">'+
                        repo.repositoryname+'</a><br/>';
                    li.innerHTML += '<a href="###" class="repo-opt" onclick="repository_client.search('+repo.id+')">Search</a>';
                    li.innerHTML += '<a href="###" class="repo-opt" id="repo-logout-'+repo.id+'">Logout</a>';
                    li.id = 'repo-'+repo.id;
                    this.appendChild(li);
                    var e = new YAHOO.util.Element('repo-call-'+repo.id);
                    e.on('click', function(e){
                        var re = /repo-call-(\d+)/i;
                        var id = this.get('id').match(re);
                        repository_client.req(id[1], 1, 0);
                        });
                    e = new YAHOO.util.Element('repo-logout-'+repo.id);
                    e.on('click', function(e){
                        var re = /repo-logout-(\d+)/i;
                        var id = this.get('id').match(re);
                        repository_client.req(id[1], 1, 1);
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
        var panel = new YAHOO.util.Element('panel');
        panel.get('element').innerHTML = '<img src="<?php echo $CFG->pixpath.'/i/loading.gif'?>" alt="loading..." />';
    }
    _client.rename = function(oldname, url){
        var panel = new YAHOO.util.Element('panel');
        var html = '<div>';
        html += '<label for="newname">Name:</label>';
        html += '<input type="text" id="newname" value="'+oldname+'" /><br/>';
        html += '<label for="syncfile">Sync?</label>';
        html += '<input type="checkbox" id="syncfile" /><br/>';
        html += '<input type="hidden" id="fileurl" value="'+url+'" />';
        html += '<input type="button" onclick="repository_client.download()" value="Download" />';
        html += '<a href="###" onclick="repository_client.viewfiles()">Back</a>';
        html += '</div>';
        panel.get('element').innerHTML = html;
    }
    _client.print_login = function(){
        var panel = new YAHOO.util.Element('panel');
        var data = _client.datasource.l;
        panel.get('element').innerHTML = data;
    }

    _client.viewfiles = function(){
        if(_client.viewmode) {
            _client.viewthumb();
        } else {
            _client.viewlist();
        }
    }

    _client.viewthumb = function(){
        var panel = new YAHOO.util.Element('panel');
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
                    +'" onclick=\'repository_client.rename("'+obj[k].title+'", "'
                    +obj[k].source+'")\' />');
            str += obj[k].title+'</div>';
            str += '</div>';
        }
        panel.get('element').innerHTML = str;
        _client.viewmode = 1;
        return str;
    }

    _client.viewlist = function(){
        var panel = new YAHOO.util.Element('panel');
        var str = '';
        _client.viewbar.check(0);
        obj = _client.datasource.list;
        str += _client.makepage();
        var re = new RegExp();
        re.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
        for(k in obj){
            str += ('<input type="radio" title="'+obj[k].title+'" name="selected-files" value="'+obj[k].source+'" onclick=\'repository_client.rename("'+obj[k].title+'", "'+obj[k].source+'")\' /> ');
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
    // XXX: A ugly hack to show paging for flickr
    _client.makepage = function(){
        var str = '';
        if(_client.datasource.pages){
            str += '<div id="paging">';
            for(var i = 1; i <= _client.datasource.pages; i++) {
                str += '<a onclick="repository_client.req('+_client.repositoryid+', '+i+', 0)" href="###">';
                str += String(i);
                str += '</a> ';
            }
            str += '</div>';
        }
        return str;
    }
    _client.download = function(){
        var title = document.getElementById('newname').value;
        var file = document.getElementById('fileurl').value;
        _client.loading();
        var trans = YAHOO.util.Connect.asyncRequest('POST', 
            'ws.php?id='+_client.repositoryid+'&action=download', _client.dlfile, 
            _client.postdata({'file':file, 'title':title}));
    }
    _client.login = function(){
        YAHOO.util.Connect.setForm('moodle-repo-login');
        _client.loading();
        var trans = YAHOO.util.Connect.asyncRequest('POST', 'ws.php', _client.callback);
    }
    _client.callback = {
    success: function(o) {
        var panel = new YAHOO.util.Element('panel');
        try {
            var ret = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert('Invalid JSON String\n'+o.responseText);
        }
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
        var panel = new YAHOO.util.Element('panel');
        try {
            var ret = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert('Invalid JSON String\n'+o.responseText);
        }
        if(ret.e){
            panel.get('element').innerHTML = ret.e;
            return;
        }
        var html = '<h1>Download Successfully!</h1>';
        html += '<a href="###" onclick="repository_client.viewfiles()">Back</a>';
        panel.get('element').innerHTML = html;
      }
    }
    // request file list or login
    _client.req = function(id, path, reset) {
        _client.viewbar.set('disabled', false);
        _client.loading();
        _client.repositoryid = id;
        var trans = YAHOO.util.Connect.asyncRequest('GET', 'ws.php?id='+id+'&p='+path+'&reset='+reset, _client.callback);
    }
    _client.search = function(id){
        var data = window.prompt("What are you searching for?");
        if(data == null || data == '') {
            alert('nothing entered');
            return;
        }
        _client.viewbar.set('disabled', false);
        _client.loading();
        var trans = YAHOO.util.Connect.asyncRequest('GET', 'ws.php?id='+id+'&s='+data, _client.callback);
    }
    return _client;
})();
<?php
$repos = repository_instances();
foreach($repos as $repo) {
    echo 'repository_client.repos.push('.json_encode($repo).');'."\n";
    echo "\n";
}
?>
function openpicker(){
    var r = new repository_client();
    r.create_picker();
}
</script>
</body>
</html>
