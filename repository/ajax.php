<?php
/*******************************************************\
  This file is a demo page for ajax repository file 
  picker.


\*******************************************************/
 
require_once('../config.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ajax picker demo page</title>
<style type="text/css">
body {
	margin:0;
	padding:0;
}
#demo .yui-resize-handle-br {
    height: 11px;
    width: 11px;
    background-position: -20px -60px;
    background-color: transparent;
}
#panel{padding:0;margin:0; text-align:left;}
#list{}
.t{width:80px; float:left;text-align:center;}
img{margin:0;padding:0;border:0}
</style>
<link rel="stylesheet" type="text/css" href="../lib/yui/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/reset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/resize/assets/skins/sam/resize.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/layout/assets/skins/sam/layout.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="../lib/yui/menu/assets/skins/sam/menu.css" />
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
</head>
<body class=" yui-skin-sam">
<div id="file-picker"></div>
<script type="text/javascript">
var repositoryid = 0;
var datasource;
(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        layout = null,
        resize = null;

    Event.onDOMReady(function() {
		// QUIRKS FLAG, FOR BOX MODEL
		var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");
		// UNDERLAY/IFRAME SYNC REQUIRED
		var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));
		// PADDING USED FOR BODY ELEMENT (Hardcoded for example)
		var PANEL_BODY_PADDING = (10*2);
        // 10px top/bottom padding applied to Panel body element. The top/bottom border width is 0
    
        var panel = new YAHOO.widget.Panel('file-picker', {
            draggable: true,
            modal: true,
            close: true,
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
                        { position: 'top', height: 32, resize: false, body: '<div class="yui-buttongroup" id="viewbar">'
                        +'<input type="radio" value="List" id="listview" checked />'
                        +'<input type="radio" value="Thumbnail" id="thumbview" />'
                        +'</div>', gutter: '2' },
                        { position: 'left', width: 150, resize: true, body: '<ul id="list"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                        { position: 'bottom', 
                        height: 30, 
                        body: '<div id="toolbar">'+
                        '<input type="button" value="Select" />'+
                        '<input type="button" id="search" value="Search" />'+
                        '<input type="button" id="logout" value="Logout" />'+
                        '</div>', 
                        gutter: '2'},
                        { position: 'center', body: '<div id="panel"></div>', scroll: true, gutter: '0 2 0 0' }
                    ]
                });

                layout.render();
                var btns = new YAHOO.widget.ButtonGroup('viewbar');
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
				// Keep the underlay and iframe size in sync.
				// You could also set the width property, to achieve the 
				// same results, if you wanted to keep the panel's internal
				// width property in sync with the DOM width. 
				this.sizeUnderlay();
				// Syncing the iframe can be expensive. Disable iframe if you
				// don't need it.
				this.syncIframe();
			}
            layout.set('height', bodyContentHeight);
            layout.set('width', (args.width - PANEL_BODY_PADDING));
            layout.resize();
            
        }, panel, true);
        var list = new YAHOO.util.Element('list');
        list.on('contentReady', function(e){
                var li = document.createElement('li');
                li.innerHTML = '<a href="###">Box.net</a>';
                li.id = 'repo-1';
                this.appendChild(li);
                var i = new YAHOO.util.Element('repo-1');
                i.on('click', function(e){
                    cr(1, 1, 0);
                    });
                li = document.createElement('li');
                li.innerHTML = '<a href="###">Flickr</a>';
                li.id = 'repo-2';
                this.appendChild(li);
                i = new YAHOO.util.Element('repo-2');
                i.on('click', function(e){
                    cr(2, 1, 0);
                    });
            });
        var listview = new YAHOO.util.Element('listview');
        listview.on('click', function(e){
                viewlist();
                })
        var thumbview = new YAHOO.util.Element('thumbview');
        thumbview.on('click', function(e){
                viewthumb();
                })
        var search = new YAHOO.util.Element('search');
        search.on('click', function(e){
                })
    });
})();
YAHOO.util.Event.addListener('logout', 'click', function(e){
        cr(repositoryid, 1, 1);
        });

function postdata(obj) {
    var str = '';
    for(k in obj) {
        if(str == ''){
            str += '?';
        } else {
            str += '&';
        }
        str += encodeURIComponent(k) +'='+encodeURIComponent(obj[k]);
    }
    return str;
}

function loading(){
    var panel = new YAHOO.util.Element('panel');
    panel.get('element').innerHTML = '<img src="<?php echo $CFG->pixpath.'/i/loading.gif'?>" alt="loading..." />';
}

function viewthumb(){
    obj = datasource.list;
    if(!obj){
        return;
    }
    var panel = new YAHOO.util.Element('panel');
    var str = '';
    for(k in obj){
        str += '<div class="t">';
        str += '<img title="'+obj[k].title+'" src="'+obj[k].thumbnail+'" />';
        str += '<div style="text-align:center">'+obj[k].title+'</div>';
        str += '</div>';
    }
    panel.get('element').innerHTML = str;
    return str;
}
function viewlist(){
    obj = datasource.list;
    if(!obj){
        return;
    }
    var panel = new YAHOO.util.Element('panel');
    var str = '';
    for(k in obj){
        str += '<input type="checkbox" value="'+obj[k].url+'" />';
        str += obj[k].title;
        str += '<br/>';
    }
    panel.get('element').innerHTML = str;
    return str;
}
function print_login(){
    var panel = new YAHOO.util.Element('panel');
    var data = datasource.l;
    panel.get('element').innerHTML = data;
}

var callback = {
success: function(o) {
    var ret = YAHOO.lang.JSON.parse(o.responseText);
    datasource = ret;
    if(datasource.l){
        print_login();
    } else if(datasource.list) {
        viewlist();
    }
  }
}

function cr(id, page, reset){
    if(id == 0) {
        repositoryid = id;
    }
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('GET', 'ws.php?id='+id+'&p='+page+'&reset='+reset, callback);
}

function dologin(){
    YAHOO.util.Connect.setForm('moodle-repo-login');
    loading();
    var trans = YAHOO.util.Connect.asyncRequest('POST', 'ws.php', callback);
}
</script>
</body>
</html>
