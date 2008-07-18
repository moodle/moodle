<?php
require_once('../config.php');
require_once('lib.php');
?>
<html>
<head>
<title> Ajax picker demo page </title>
<?php
/*******************************************************\

  This file is a demo page for ajax repository file
  picker.

\*******************************************************/
$itempic = $CFG->pixpath.'/i/item.gif';
$meta = <<<EOD
<link rel="stylesheet" type="text/css" href="../lib/yui/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/reset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/resize/assets/skins/sam/resize.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/layout/assets/skins/sam/layout.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/menu/assets/skins/sam/menu.css" />
<style type="text/css">
body {
  margin:0;
  padding:0;
  background: #FFF7C6;
}
#demo .yui-resize-handle-br {
    height: 11px;
    width: 11px;
    background-position: -20px -60px;
    background-color: transparent;
}
#panel{padding:0;margin:0; text-align:left;}
#list{line-height: 1.5em}
#list li{
background: url($itempic) no-repeat 0 2px;
padding-left: 16px
}
#list a{
padding: 3px
}
#list li a:hover{
background: gray;
color:white;
}
.t{width:80px; float:left;text-align:center;}
.t div{width: 80px; height: 36px; overflow: hidden}
.repo-opt{font-size: 10px;color:red}
img{margin:0;padding:0;border:0}
#paging{margin:10px 5px; clear:both}
#paging a{padding: 4px; border: 1px solid gray}
.file_name{color:green;}
.file_date{color:blue}
.file_size{color:gray}
</style>
<script type="text/javascript" src="../lib/yui/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="../lib/yui/event/event-min.js"></script>
<script type="text/javascript" src="../lib/yui/dom/dom-min.js"></script>
<script type="text/javascript" src="../lib/yui/element/element-beta-min.js"></script>
<script type="text/javascript" src="../lib/yui/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="../lib/yui/container/container-min.js"></script>
<script type="text/javascript" src="../lib/yui/resize/resize-beta-min.js"></script>
<script type="text/javascript" src="../lib/yui/animation/animation-min.js"></script>
<script type="text/javascript" src="../lib/yui/layout/layout-beta-min.js"></script>
<script type="text/javascript" src="../lib/yui/connection/connection.js"></script>
<script type="text/javascript" src="../lib/yui/json/json-min.js"></script>
<script type="text/javascript" src="../lib/yui/menu/menu-min.js"></script>
<script type="text/javascript" src="../lib/yui/button/button-min.js"></script>
<script type="text/javascript" src="../lib/yui/selector/selector-beta-min.js"></script>
EOD;
echo $meta;
?>
</head>
<body class=" yui-skin-sam">
<div id='control'>
    <input type="button" id="con1" onclick='openpicker()' value="Open File Picker" /> <br/>
    <textarea rows=12 cols=50 id="result">
    </textarea>
</div>
<div>
    <div id="file-picker"></div>
</div>

<script type="text/javascript">
var repositoryid = 0;
var datasource, Dom = YAHOO.util.Dom, Event = YAHOO.util.Event, layout = null, resize = null;
var viewbar  = null;
var viewmode = 0;
var repos = [];
<?php
$repos = repository_get_repositories();
foreach($repos as $repo) {
    echo 'repos.push('.json_encode($repo).')';
    echo "\n";
}
?>

/**
 * this function will create a file picker dialog, and resigister all the event
 * of component
 */
function openpicker(){
    // QUIRKS FLAG, FOR BOX MODEL
    var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");
    // UNDERLAY/IFRAME SYNC REQUIRED
    var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));
    // PADDING USED FOR BODY ELEMENT (Hardcoded for example)
    var PANEL_BODY_PADDING = (10*2);
    // 10px top/bottom padding applied to Panel body element. The top/bottom border width is 0
    var panel = new YAHOO.widget.Panel('file-picker', {
        draggable: true,
        close: false,
        underlay: 'none',
        width: '510px',
        xy: [100, 100]
    });
    panel.setHeader('Moodle Repository Picker');
    panel.setBody('<div id="layout"></div>');
    panel.beforeRenderEvent.subscribe(function() {
        Event.onAvailable('layout', function() {
            layout = new YAHOO.widget.Layout('layout', {
                height: 400,
                width: 490,
                units: [
                    {position: 'top', height: 32, resize: false, body:'<div class="yui-buttongroup" id="viewbar"></div>', gutter: '2'},
                    { position: 'left', width: 150, resize: true, body: '<ul id="list"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                    { position: 'center', body: '<div id="panel"></div>', scroll: true, gutter: '0 2 0 0' }
                ]
            });

            layout.render();
        });
    });
    panel.render();
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
    var list = new YAHOO.util.Element('list');
    list.on('contentReady', function(e){
            for(var i=0; i<repos.length; i++) {
                var repo = repos[i];
                li = document.createElement('li');
                li.innerHTML = '<a href="###" id="repo-call-'+repo.id+'">'+
                    repo.repositoryname+'</a><br/>';
                li.innerHTML += '<a href="###" class="repo-opt" onclick=\'dosearch('+repo.id+')\'>search</a>';
                li.innerHTML += '<a href="###" class="repo-opt" id="repo-logout-'+repo.id+'">Logout </a>';
                li.id = 'repo-'+repo.id;
                this.appendChild(li);
                var e = new YAHOO.util.Element('repo-call-'+repo.id);
                e.on('click', function(e){
                    var re = /repo-call-(\d+)/i;
                    var id = this.get('id').match(re);
                    cr(id[1], 1, 0);
                    });
                e = new YAHOO.util.Element('repo-logout-'+repo.id);
                e.on('click', function(e){
                    var re = /repo-logout-(\d+)/i;
                    var id = this.get('id').match(re);
                    cr(id[1], 1, 1);
                    });
                repo = null;
            }
        });
    viewbar = new YAHOO.widget.ButtonGroup({
            id: 'btngroup',
            name: 'buttons',
            disabled: true,
            container: 'viewbar'
            });
    var btn_list = {label: 'List', value: 'l', checked: true, onclick: {fn: viewlist}};
    var btn_thumb = {label: 'Thumbnail', value: 't', onclick: {fn: viewthumb}};
    viewbar.addButtons([btn_list, btn_thumb]);
    var select = new YAHOO.util.Element('select');
};

function postdata(obj) {
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

// XXX: A ugly hack to show paging for flickr
function makepage(){
    var str = '';
    if(datasource.pages){
        str += '<div id="paging">';
        for(var i = 1; i <= datasource.pages; i++) {
            str += '<a onclick="cr('+repositoryid+', '+i+', 0)" href="###">';
            str += String(i);
            str += '</a> ';
        }
        str += '</div>';
    }
    return str;
}

// display a loading picture
function loading(){
    var panel = new YAHOO.util.Element('panel');
    panel.get('element').innerHTML = '<img src="<?php echo $CFG->pixpath.'/i/loading.gif'?>" alt="loading..." />';
}

// name the file
function rename(oldname, url){
    var panel = new YAHOO.util.Element('panel');
    var html = '<div>';
    html += '<label for="newname">Name:</label>';
    html += '<input type="text" id="newname" value="'+oldname+'" /><br/>';
    html += '<input type="hidden" id="fileurl" value="'+url+'" />';
    html += '<input type="button" onclick="download()" value="Download" />';
    html += '<a href="###" onclick="viewfiles()">Back</a>';
    html += '</div>';
    panel.get('element').innerHTML = html;
}
function download(){
    var title = document.getElementById('newname').value;
    var file = document.getElementById('fileurl').value;
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('POST', 'ws.php?id='+repositoryid+'&action=download', loadfile, postdata({'file':file, 'title':title}));
}
// produce thumbnail view
function viewthumb(){
    viewbar.check(1);
    obj = datasource.list;
    if(!obj){
        return;
    }
    var panel = new YAHOO.util.Element('panel');
    var str = '';
    str += makepage();
    for(k in obj){
        str += '<div class="t">';
        str += '<img title="'+obj[k].title+'" src="'+obj[k].thumbnail+'" />';
        str += '<div style="text-align:center">';
        str += ('<input type="radio" title="'+obj[k].title+'" name="selected-files" value="'+obj[k].source+'" onclick=\'rename("'+obj[k].title+'", "'+obj[k].source+'")\' />');
        str += obj[k].title+'</div>';
        str += '</div>';
    }
    panel.get('element').innerHTML = str;
    viewmode = 1;
    return str;
}

function viewfiles(){
    if(viewmode) {
        viewthumb();
    } else {
        viewlist();
    }
}
// produce list view
function viewlist(){
    var str = '';
    viewbar.check(0);
    obj = datasource.list;
    if(!obj){
        return;
    }
    var panel = new YAHOO.util.Element('panel');
    str += makepage();
    for(k in obj){
        var re = new RegExp();
        re.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
        str += ('<input type="radio" title="'+obj[k].title+'" name="selected-files" value="'+obj[k].source+'" onclick=\'rename("'+obj[k].title+'", "'+obj[k].source+'")\' /> ');
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
    viewmode = 0;
    return str;
}

// produce login html
function print_login(){
    var panel = new YAHOO.util.Element('panel');
    var data = datasource.l;
    panel.get('element').innerHTML = data;
}

var callback = {
success: function(o) {
    try {
        var ret = YAHOO.lang.JSON.parse(o.responseText);
    } catch(e) {
        alert('Invalid JSON String\n'+o.responseText);
    }
    datasource = ret;
    if(datasource.l){
        print_login();
    } else if(datasource.list) {
        if(viewmode) {
            viewthumb();
        } else {
            viewlist();
        }
    }
  }
}
var loadfile = {
    success: function(o) {
        try {
            var ret = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert('Invalid JSON String\n'+o.responseText);
        }
        var panel = new YAHOO.util.Element('panel');
        var html = '<h1>Download Successfully!</h1>';
        html += '<a href="###" onclick="viewfiles()">Back</a>';
        panel.get('element').innerHTML = html;
    }
}

function cr(id, path, reset){
    viewbar.set('disabled', false);
    if(id != 0) {
        repositoryid = id;
    }
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('GET', 'ws.php?id='+id+'&p='+path+'&reset='+reset, callback);
}
function dosearch(id){
    var data = window.prompt("What are you searching for?");
    if(data == null && data == '') {
        alert('nothing entered');
        return;
    }
    viewbar.set('disabled', false);
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('GET', 'ws.php?id='+id+'&s='+data, callback);
}

function dologin(){
    YAHOO.util.Connect.setForm('moodle-repo-login');
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('POST', 'ws.php', callback);
}
</script>
</body>
</html>
