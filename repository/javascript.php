<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
//       Don't modify this file unless you know how it works             //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
/**
 * Return javascript to create file picker to browse repositories
 * @global object $CFG
 * @global object $USER
 * @param object $context the context
 * @return array
 */
function repository_get_client($context) {
    global $CFG, $USER;
    $suffix = uniqid();
    $sesskey = sesskey();
    // language string
    $stradd       = get_string('add', 'repository');
    $strback      = get_string('back', 'repository');
    $strcancel    = get_string('cancel');
    $strclose     = get_string('close', 'repository');
    $strccache    = get_string('cleancache', 'repository');
    $strcopying   = get_string('copying', 'repository');
    $strdownbtn   = get_string('getfile', 'repository');
    $strdownload  = get_string('downloadsucc', 'repository');
    $strdate      = get_string('date', 'repository').': ';
    $strerror     = get_string('error', 'repository');
    $strfilenotnull = get_string('filenotnull', 'repository');
    $strrefresh   = get_string('refresh', 'repository');
    $strinvalidjson = get_string('invalidjson', 'repository');
    $strlistview  = get_string('listview', 'repository');
    $strlogout    = get_string('logout', 'repository');
    $strloading   = get_string('loading', 'repository');
    $strthumbview = get_string('thumbview', 'repository');
    $strtitle     = get_string('title', 'repository');
    $strnoresult  = get_string('noresult', 'repository');
    $strmgr       = get_string('manageurl', 'repository');
    $strnoenter   = get_string('noenter', 'repository');
    $strsave      = get_string('save', 'repository');
    $strsaveas    = get_string('saveas', 'repository').': ';
    $strsaved     = get_string('saved', 'repository');
    $strsaving    = get_string('saving', 'repository');
    $strsize      = get_string('size', 'repository').': ';
    $strsync      = get_string('sync', 'repository');
    $strsearch    = get_string('search', 'repository');
    $strsearching = get_string('searching', 'repository');
    $strsubmit    = get_string('submit', 'repository');
    $strpreview   = get_string('preview', 'repository');
    $strpopup     = get_string('popup', 'repository');
    $strupload    = get_string('upload', 'repository');
    $struploading = get_string('uploading', 'repository');
    $css = '';
    if (!isset($CFG->repo_yui_loaded)) {
        $css .= <<<EOD
<style type="text/css">
@import "$CFG->httpswwwroot/lib/yui/resize/assets/skins/sam/resize.css";
@import "$CFG->httpswwwroot/lib/yui/container/assets/skins/sam/container.css";
@import "$CFG->httpswwwroot/lib/yui/layout/assets/skins/sam/layout.css";
@import "$CFG->httpswwwroot/lib/yui/button/assets/skins/sam/button.css";
@import "$CFG->httpswwwroot/lib/yui/assets/skins/sam/treeview.css";
</style>
<style type="text/css">
.file-picker{font-size:12px;}
.file-picker strong{background:#FFFFCC}
.file-picker a{color: #336699}
.file-picker a:hover{background:#003366;color:white}
.fp-panel{padding:0;margin:0; text-align:left;}
.fp-searchbar{float:right}
.fp-viewbar{width:300px;float:left}
.fp-toolbar{padding: .8em;background: #FFFFCC;color:white;text-align:center}
.fp-toolbar a{padding: 0 .5em}
.fp-list{list-style-type:none;padding:0}
.fp-list li{border-bottom:1px dotted gray;margin-bottom: 1em;}
.fp-repo-name{display:block;padding: .5em;margin-bottom: .5em}
.fp-pathbar{margin: .4em;border-bottom: 1px dotted gray;}
.fp-pathbar a{padding: .4em;}
.fp-rename-form{text-align:center}
.fp-rename-form p{margin: 1em;}
.fp-upload-form{margin: 2em 0;text-align:center}
.fp-upload-btn a{font-size: 1.5em;background: #ccc;color:white;padding: .5em}
.fp-upload-btn a:hover {background: grey;color:white}
.fp-paging{margin:1em .5em; clear:both;text-align:center;line-height: 2.5em;}
.fp-paging a{padding: .5em;border: 1px solid #CCC}
.fp-popup{text-align:center}
.fp-popup a{font-size: 3em}
.fp-grid{width:80px; float:left;text-align:center;}
.fp-grid div{width: 80px; overflow: hidden}
.fp-grid p{margin:0;padding:0;background: #FFFFCC}
.fp-grid .label{height:48px}
.fp-grid span{background: #EEF9EB;color:gray}
</style>
EOD;

        $js = <<<EOD
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/element/element-beta-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/treeview/treeview-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/container/container-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/resize/resize-beta-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/layout/layout-beta-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/connection/connection-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/json/json-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/button/button-min.js"></script>
<script type="text/javascript" src="$CFG->httpswwwroot/lib/yui/selector/selector-beta-min.js"></script>
EOD;
        $CFG->repo_yui_loaded = true;
    } else {
        $js = '';
    }

    $js .= <<<EOD
<script type="text/javascript">
//<![CDATA[
var active_instance = null;
function repository_callback(id) {
    active_instance.req(id, '', 0);
}
var repository_client_$suffix = (function() {
// private static field
var dver = '1.0';
// private static methods
function alert_version() {
    alert(dver);
}
function _client() {
// public varible
this.name = 'repository_client_$suffix';
// private varible
var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event, layout = null, resize = null;
var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");
var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));
var PANEL_BODY_PADDING = (10*2);
var btn_list = {label: '$strlistview', value: 'l', checked: true, onclick: {fn: _client.viewlist}};
var btn_thumb = {label: '$strthumbview', value: 't', onclick: {fn: _client.viewthumb}};
var repo_list = null;
var resize = null;
var filepicker = new YAHOO.widget.Panel('file-picker-$suffix', {
    draggable: true,
    close: true,
    modal: true,
    underlay: 'none',
    zindex: 666666,
    xy: [50, Dom.getDocumentScrollTop()+20]
});
// construct code section
{
    filepicker.setHeader('$strtitle');
    filepicker.setBody('<div id="layout-$suffix"></div>');
    filepicker.beforeRenderEvent.subscribe(function() {
        Event.onAvailable('layout-$suffix', function() {
            layout = new YAHOO.widget.Layout('layout-$suffix', {
                height: 480, width: 630,
                units: [
                {position: 'top', height: 32, resize: false,
                body:'<div class="yui-buttongroup fp-viewbar" id="repo-viewbar-$suffix"></div><div class="fp-searchbar" id="search-div-$suffix"></div>', gutter: '2'},
                {position: 'left', width: 200, resize: true,
                body:'<ul class="fp-list" id="repo-list-$suffix"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                {position: 'center', body: '<div class="fp-panel" id="panel-$suffix"></div>',
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
        Dom.setStyle(this.body, 'height', bodyContentHeight + 'px');
        if (IE_SYNC) {
            this.sizeUnderlay();
            this.syncIframe();
        }
        layout.set('height', bodyContentHeight);
        layout.set('width', (args.width - PANEL_BODY_PADDING));
        layout.resize();

    }, filepicker, true);
    _client.viewbar = new YAHOO.widget.ButtonGroup({
        id: 'btngroup-$suffix',
        name: 'buttons',
        disabled: true,
        container: 'repo-viewbar-$suffix'
    });
}
// public method
this.show = function() {
    filepicker.show();
}
this.hide = function() {
    filepicker.hide();
}
this.create_picker = function() {
    // display UI
    filepicker.render();
    _client.viewbar.addButtons([btn_list, btn_thumb]);
    // init repository list
    repo_list = new YAHOO.util.Element('repo-list-$suffix');
    repo_list.on('contentReady', function(e) {
        var searchbar = new YAHOO.util.Element('search-div-$suffix');
        searchbar.get('element').innerHTML = '<input id="search-input-$suffix" /><button id="search-btn-$suffix">$strsearch</button>';
        var searchbtn = new YAHOO.util.Element('search-btn-$suffix');
        searchbtn.callback={
            success: function(o) {
                var panel = new YAHOO.util.Element('panel-$suffix');
                if(!o.responseText) {
                    panel.get('element').innerHTML = '$strnoresult';
                    return;
                }
                try {
                    var json = YAHOO.lang.JSON.parse(o.responseText);
                } catch(e) {
                    alert('$strinvalidjson - |search_cb| -'+o.responseText);
                    return;
                }
                _client.ds={};
                if(!json.list || json.list.length<1){
                    panel.get('element').innerHTML = '$strnoresult';
                    return;
                }
                _client.ds.list = json.list;
                if(_client.ds.list) {
                    if(_client.viewmode) {
                        _client.viewthumb();
                    } else {
                        _client.viewlist();
                    }
                    var input_ctl = new YAHOO.util.Element('search-input-$suffix');
                    input_ctl.get('element').value='';
                }
            }
        }
        searchbtn.input_ctl = new YAHOO.util.Element('search-input-$suffix');
        searchbtn.on('click', function(e) {
            var keyword = this.input_ctl.get('value');
            var params = [];
            params['s'] = keyword;
            params['env']=_client.env;
            params['action']='gsearch';
            params['sesskey']='$sesskey';
            params['ctx_id']=$context->id;
            _client.loading('load');
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                '$CFG->httpswwwroot/repository/ws.php?action=gsearch', this.callback, _client.postdata(params));
        });
        for(var i=0; i<_client.repos.length; i++) {
            var repo = _client.repos[i];
            var li = document.createElement('li');
            li.id = 'repo-$suffix-'+repo.id;
            var icon = document.createElement('img');
            icon.src = repo.icon;
            icon.width = '16';
            icon.height = '16';
            var link = document.createElement('a');
            link.href = '###';
            link.id = 'repo-call-$suffix-'+repo.id;
            link.appendChild(icon);
            link.className = 'fp-repo-name';
            link.onclick = function() {
                var re = /repo-call-$suffix-(\d+)/i;
                var id = this.id.match(re);
                repository_client_$suffix.req(id[1], '', 0);
            }
            link.innerHTML += ' '+repo.name;
            li.appendChild(link);
            this.appendChild(li);
            repo = null;
        }
    });
}
}

// public static varible
_client.repos = [];
_client.repositoryid = 0;
// _client.ds save all data received from server side
_client.ds = null;
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
_client.loading = function(type, name) {
    var panel = new YAHOO.util.Element('panel-$suffix');
    panel.get('element').innerHTML = '';
    var content = document.createElement('div');
    content.style.textAlign='center';
    var para = document.createElement('P');
    var img = document.createElement('IMG');
    if(type=='load') {
        img.src = '$CFG->pixpath/i/loading.gif';
        para.innerHTML = '$strloading';
    }else{
        img.src = '$CFG->pixpath/i/progressbar.gif';
        para.innerHTML = '$strcopying <strong>'+name+'</strong>';
    }
    content.appendChild(para);
    content.appendChild(img);
    //content.innerHTML = '';
    panel.get('element').appendChild(content);
}
_client.rename = function(oldname, url, icon, repo_id) {
    var panel = new YAHOO.util.Element('panel-$suffix');
    var html = '<div class="fp-rename-form">';
    _client.repositoryid=repo_id;
    html += '<p><img src="'+icon+'" /></p>';
    html += '<p><label for="newname-$suffix">$strsaveas</label>';
    html += '<input type="text" id="newname-$suffix" value="'+oldname+'" /></p>';
    /**
    html += '<p><label for="syncfile-$suffix">$strsync</label> ';
    html += '<input type="checkbox" id="syncfile-$suffix" /></p>';
    */
    html += '<p><input type="hidden" id="fileurl-$suffix" value="'+url+'" />';
    html += '<a href="###" onclick="repository_client_$suffix.viewfiles()">$strback</a> ';
    html += '<input type="button" onclick="repository_client_$suffix.download()" value="$strdownbtn" />';
    html += '<input type="button" onclick="repository_client_$suffix.hide()" value="$strcancel" /></p>';
    html += '</div>';
    panel.get('element').innerHTML = html;
}
_client.popup = function(url) {
    active_instance = repository_client_$suffix;
    _client.win = window.open(url,'repo_auth', 'location=0,status=0,scrollbars=0,width=500,height=300');
    return false;
}
_client.print_login = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    var data = _client.ds.login;
    var str = '';
    var has_pop = false;
    for(var k in data) {
        if(data[k].type=='popup') {
            str += '<p class="fp-popup"><a href="###" onclick="repository_client_$suffix.popup(\''+data[k].url+'\')">$strpopup</a></p>';
            has_pop = true;
        }else if(data[k].type=='textarea') {
            str += '<p><textarea id="'+data[k].id+'" name="'+data[k].name+'"></textarea></p>';
        }else{
            str += '<p>';
            var lable_id = '';
            var field_id = '';
            var field_value = '';
            if(data[k].id) {
                lable_id = ' for="'+data[k].id+'"';
                field_id = ' id="'+data[k].id+'"';
            }
            if (data[k].label) {
                str += '<label'+lable_id+'>'+data[k].label+'</label><br/>';
            }
            if(data[k].value) {
                field_value = ' value="'+data[k].value+'"';
            }
            str += '<input type="'+data[k].type+'"'+' name="'+data[k].name+'"'+field_id+field_value+' />';
            str += '</p>';
        }
    }
    if(!has_pop) {
        str += '<p><input type="button" onclick="repository_client_$suffix.login()" value="$strsubmit" /></p>';
    }
    panel.get('element').innerHTML = str;
}

_client.viewfiles = function() {
    if(_client.viewmode) {
        _client.viewthumb();
    } else {
        _client.viewlist();
    }
}
_client.print_header = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    var str = '';
    str += '<div class="fp-toolbar" id="repo-tb-$suffix"></div>';
    panel.set('innerHTML', str);
    _client.makepath();
}
_client.print_footer = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    panel.get('element').innerHTML += _client.uploadcontrol();
    panel.get('element').innerHTML += _client.makepage();
    var oDiv = document.getElementById('repo-tb-$suffix');
    if(!_client.ds.nosearch) {
        var search = document.createElement('A');
        search.href = '###';
        search.innerHTML = '<img src="$CFG->pixpath/a/search.png" /> $strsearch';
        oDiv.appendChild(search);
        search.onclick = function() {
            repository_client_$suffix.search(repository_client_$suffix.repositoryid);
        }
    }
    // weather we use cache for this instance, this button will reload listing anyway
    var ccache = document.createElement('A');
    ccache.href = '###';
    ccache.innerHTML = '<img src="$CFG->pixpath/a/refresh.png" /> $strrefresh';
    oDiv.appendChild(ccache);
    ccache.onclick = function() {
        var params = [];
        params['env']=_client.env;
        params['sesskey']='$sesskey';
        params['ctx_id']=$context->id;
        params['repo_id']=repository_client_$suffix.repositoryid;
        _client.loading('load');
        var trans = YAHOO.util.Connect.asyncRequest('POST',
                '$CFG->httpswwwroot/repository/ws.php?action=ccache', repository_client_$suffix.req_cb, _client.postdata(params));
    }
    if(_client.ds.manage) {
        var mgr = document.createElement('A');
        mgr.innerHTML = '<img src="$CFG->pixpath/a/setting.png" /> $strmgr';
        mgr.href = _client.ds.manage;
        mgr.target = "_blank";
        oDiv.appendChild(mgr);
    }
    if(!_client.ds.nologin) {
        var logout = document.createElement('A');
        logout.href = '###';
        logout.innerHTML = '<img src="$CFG->pixpath/a/logout.png" /> $strlogout';
        oDiv.appendChild(logout);
        logout.onclick = function() {
            repository_client_$suffix.req(repository_client_$suffix.repositoryid, 1, 1);
        }
    }
}
_client.viewthumb = function(ds) {
    _client.viewmode = 1;
    var panel = new YAHOO.util.Element('panel-$suffix');
    _client.viewbar.check(1);
    var list = null;
    var args = arguments.length;
    if(args == 1) {
        list = ds;
    } else {
        // from button
        list = _client.ds.list;
    }
    _client.print_header();
    var count = 0;
    for(k in list) {
        var el = document.createElement('div');
        el.className='fp-grid';
        var frame = document.createElement('DIV');
        frame.style.textAlign='center';
        var img = document.createElement('img');
        img.src = list[k].thumbnail;
        var link = document.createElement('A');
        link.href='###';
        link.id = 'img-id-'+String(count);
        link.appendChild(img);
        frame.appendChild(link);
        var title = document.createElement('div');
        if(list[k].children) {
            title.innerHTML = '<i><u>'+list[k].title+'</i></u>';
        } else {
            if(list[k].url)
                title.innerHTML = '<p><a target="_blank" href="'+list[k].url+'">$strpreview</a></p>';
            title.innerHTML += '<span>'+list[k].title+"</span>";
        }
        title.className = 'label';
        el.appendChild(frame);
        el.appendChild(title);
        panel.get('element').appendChild(el);
        if(list[k].children) {
            var folder = new YAHOO.util.Element(link.id);
            folder.ds = list[k].children;
            folder.path = list[k].path;
            folder.on('contentReady', function() {
                this.on('click', function() {
                    if(_client.ds.dynload) {
                        var params = [];
                        params['p'] = this.path;
                        params['env'] = _client.env;
                        params['repo_id'] = _client.repositoryid;
                        params['ctx_id'] = $context->id;
                        params['sesskey']= '$sesskey';
                        _client.loading('load');
                        var trans = YAHOO.util.Connect.asyncRequest('POST',
                                '$CFG->httpswwwroot/repository/ws.php?action=list', _client.req_cb, _client.postdata(params));
                    }else{
                        _client.viewthumb(this.ds);
                    }
                });
            });
        } else {
            var file = new YAHOO.util.Element(link.id);
            file.title = list[k].title;
            file.value = list[k].source;
            file.icon  = list[k].thumbnail;
            if(list[k].repo_id) {
                file.repo_id = list[k].repo_id;
            }else{
                file.repo_id = _client.repositoryid;
            }
            file.on('contentReady', function() {
                this.on('click', function() {
                    repository_client_$suffix.rename(this.title, this.value, this.icon, this.repo_id);
                });
            });
        }
        count++;
    }
    _client.print_footer();
}
_client.buildtree = function(node, level) {
    if(node.children) {
        node.title = '<i><u>'+node.title+'</u></i>';
    }
    var info = {label:node.title, title:"$strdate"+node.date+' '+'$strsize'+node.size};
    var tmpNode = new YAHOO.widget.TextNode(info, level, false);
    var tooltip = new YAHOO.widget.Tooltip(tmpNode.labelElId, {
        context:tmpNode.labelElId, text:info.title});
    if(node.repo_id) {
        tmpNode.repo_id=node.repo_id;
    }else{
        tmpNode.repo_id=_client.repositoryid;
    }
    tmpNode.filename = node.title;
    tmpNode.value  = node.source;
    tmpNode.icon = node.thumbnail;
    tmpNode.path = node.path;
    if(node.children) {
        if(node.expanded) {
            tmpNode.expand();
        }
        tmpNode.isLeaf = false;
        if (node.path) {
            tmpNode.path = node.path;
        } else {
            tmpNode.path = '';
        }
        for(var c in node.children) {
            _client.buildtree(node.children[c], tmpNode);
        }
    } else {
        tmpNode.isLeaf = true;
        tmpNode.onLabelClick = function() {
            repository_client_$suffix.rename(this.filename, this.value, this.icon, this.repo_id);
        }
    }
}
_client.dynload = function (node, fnLoadComplete) {
    var callback = {
        success: function(o) {
             try {
                 var json = YAHOO.lang.JSON.parse(o.responseText);
             } catch(e) {
                 alert('$strinvalidjson - |dynload| -'+o.responseText);
                 return;
             }
             for(k in json.list) {
                 _client.buildtree(json.list[k], node);
             }
             o.argument.fnLoadComplete();
        },
        failure:function(oResponse) {
            alert('$strerror - |dynload| -');
            oResponse.argument.fnLoadComplete();
        },
        argument:{"node":node, "fnLoadComplete": fnLoadComplete}
    }
    var params = [];
    params['p']=node.path;
    params['env']=_client.env;
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=_client.repositoryid;
    var trans = YAHOO.util.Connect.asyncRequest('POST',
            '$CFG->httpswwwroot/repository/ws.php?action=list', callback, _client.postdata(params));
}
_client.viewlist = function() {
    _client.viewmode = 0;
    var panel = new YAHOO.util.Element('panel-$suffix');
    _client.viewbar.check(0);
    list = _client.ds.list;
    _client.print_header();
    panel.get('element').innerHTML += '<div id="treediv-$suffix"></div>';
    var tree = new YAHOO.widget.TreeView('treediv-$suffix');
    if(_client.ds.dynload) {
        tree.setDynamicLoad(_client.dynload, 1);
    } else {
    }
    for(k in list) {
        _client.buildtree(list[k], tree.getRoot());
    }
    tree.draw();
    _client.print_footer();
}
_client.upload = function() {
    var u = _client.ds.upload;
    var aform = document.getElementById(u.id);
    var parent = document.getElementById(u.id+'_div');
    var d = document.getElementById(_client.ds.upload.id+'-file');
    if(d.value!='' && d.value!=null) {
        var container = document.createElement('DIV');
        container.id = u.id+'_loading';
        container.style.textAlign='center';
        var img = document.createElement('IMG');
        img.src = '$CFG->pixpath/i/progressbar.gif';
        var para = document.createElement('p');
        para.innerHTML = '$struploading';
        container.appendChild(para);
        container.appendChild(img);
        parent.appendChild(container);
        YAHOO.util.Connect.setForm(aform, true, true);
        var trans = YAHOO.util.Connect.asyncRequest('POST',
                '$CFG->httpswwwroot/repository/ws.php?action=upload&sesskey=$sesskey&ctx_id=$context->id&repo_id='
                +_client.repositoryid,
                _client.upload_cb);
    }else{
        alert('$strfilenotnull');
    }
}
_client.upload_cb = {
upload: function(o) {
        try {
            var ret = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert('$strinvalidjson - |upload| -'+o.responseText);
        }
        if(ret && ret.e) {
            var panel = new YAHOO.util.Element('panel-$suffix');
            panel.get('element').innerHTML = ret.e;
            return;
        }
        if(ret) {
            alert('$strsaved');
            repository_client_$suffix.end(ret);
        }
    }
}
_client.uploadcontrol = function() {
    var str = '';
    if(_client.ds.upload) {
        str += '<div id="'+_client.ds.upload.id+'_div" class="fp-upload-form">';
        str += '<form id="'+_client.ds.upload.id+'" onsubmit="return false">';
        str += '<label for="'+_client.ds.upload.id+'-file">'+_client.ds.upload.label+'</label>';
        str += '<input type="file" id="'+_client.ds.upload.id+'-file" name="repo_upload_file" />';
        str += '<p class="fp-upload-btn"><a href="###" onclick="return repository_client_$suffix.upload();">$strupload</a></p>';
        str += '</form>';
        str += '</div>';
    }
    return str;
}
_client.makepage = function() {
    var str = '';
    if(_client.ds.pages) {
        str += '<div class="fp-paging" id="paging-$suffix">';
        for(var i = 1; i <= _client.ds.pages; i++) {
            str += '<a onclick="repository_client_$suffix.req('+_client.repositoryid+', '+i+', 0)" href="###">';
            str += String(i);
            str += '</a> ';
        }
        str += '</div>';
    }
    return str;
}
_client.makepath = function() {
    if(_client.viewmode == 0) {
        return;
    }
    var panel = new YAHOO.util.Element('panel-$suffix');
    var p = _client.ds.path;
    if(p && p.length!=0) {
        var oDiv = document.createElement('DIV');
        oDiv.id = "path-$suffix";
        oDiv.className = "fp-pathbar";
        panel.get('element').appendChild(oDiv);
        for(var i = 0; i < _client.ds.path.length; i++) {
            var link = document.createElement('A');
            link.href = "###";
            link.innerHTML = _client.ds.path[i].name;
            link.id = 'path-'+i+'-el';
            var sep = document.createElement('SPAN');
            sep.innerHTML = '/';
            oDiv.appendChild(link);
            oDiv.appendChild(sep);
            var el = new YAHOO.util.Element(link.id);
            el.id = _client.repositoryid;
            el.path = _client.ds.path[i].path;
            el.on('contentReady', function() {
                this.on('click', function() {
                    repository_client_$suffix.req(this.id, this.path, 0);
                })
            });
        }
    }
}
// send download request
_client.download = function() {
    var title = document.getElementById('newname-$suffix').value;
    var file = document.getElementById('fileurl-$suffix').value;
    _client.loading('download', title);
    var params = [];
    params['env']=_client.env;
    params['file']=file;
    params['title']=title;
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=_client.repositoryid;
    var trans = YAHOO.util.Connect.asyncRequest('POST',
            '$CFG->httpswwwroot/repository/ws.php?action=download', _client.download_cb, _client.postdata(params));
}
// send login request
_client.login = function() {
    var params = [];
    var data = _client.ds.login;
    for (var k in data) {
        if(data[k].type!='popup') {
            var el = document.getElementsByName(data[k].name)[0];
            params[data[k].name] = '';
            if(el.type == 'checkbox') {
                params[data[k].name] = el.checked;
            } else {
                params[data[k].name] = el.value;
            }
        }
    }
    params['env'] = _client.env;
    params['repo_id'] = _client.repositoryid;
    params['ctx_id'] = $context->id;
    params['sesskey']= '$sesskey';
    _client.loading('load');
    var trans = YAHOO.util.Connect.asyncRequest('POST',
            '$CFG->httpswwwroot/repository/ws.php?action=sign', _client.req_cb, _client.postdata(params));
}
_client.end = function(str) {
    if(_client.env=='form') {
        _client.target.value = str['id'];
    }else{
        _client.target.value = str['url'];
        _client.target.onchange();
    }
    _client.formcallback(str['file']);
    _client.instance.hide();
    _client.viewfiles();
}
_client.hide = function() {
    _client.instance.hide();
    _client.viewfiles();
}
// request file list or login
_client.req = function(id, path, logout) {
    _client.viewbar.set('disabled', false);
    _client.loading('load');
    _client.repositoryid = id;
    if (logout == 1) {
        action = 'logout';
    } else {
        action = 'list';
    }
    var params = [];
    params['p'] = path;
    params['env']=_client.env;
    params['action']=action;
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=id;
    var trans = YAHOO.util.Connect.asyncRequest('POST', '$CFG->httpswwwroot/repository/ws.php?action='+action, _client.req_cb, _client.postdata(params));
}
_client.search_form_cb = {
success: function(o) {
     var el = document.getElementById('fp-search-dlg');
     if(el) {
         el.innerHTML = '';
     } else {
         var el = document.createElement('DIV');
         el.id = 'fp-search-dlg';
     }
     var div1 = document.createElement('DIV');
     div1.className = 'hd';
     div1.innerHTML = "$strsearching";
     var div2 = document.createElement('DIV');
     div2.className = 'bd';
     var sform = document.createElement('FORM');
     sform.method = 'POST';
     sform.id = "fp-search-form";
     sform.action = '$CFG->wwwroot/repository/ws.php?action=search';
     sform.innerHTML = o.responseText;
     div2.appendChild(sform);
     el.appendChild(div1);
     el.appendChild(div2);
     document.body.appendChild(el);
     var dlg = new YAHOO.widget.Dialog("fp-search-dlg",{
        postmethod: 'async',
        width : "30em",
        fixedcenter : true,
        zindex: 766667,
        visible : false, 
        constraintoviewport : true,
        buttons : [ 
            { text:"Submit",handler: function() {
                _client.viewbar.set('disabled', false); _client.loading('load');
                YAHOO.util.Connect.setForm('fp-search-form', false, false);
                this.cancel();
                var trans = YAHOO.util.Connect.asyncRequest('POST',
                    '$CFG->httpswwwroot/repository/ws.php?action=search&env='+_client.env, _client.req_cb);
                },isDefault:true 
            }, 
            {text:"Cancel",handler:function() {this.cancel()}}
        ]
    });
    dlg.render();
    dlg.show();
}
}
_client.search = function(id) {
    var params = [];
    params['env']=_client.env;
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=id;
    var trans = YAHOO.util.Connect.asyncRequest('POST', '$CFG->httpswwwroot/repository/ws.php?action=searchform', _client.search_form_cb, _client.postdata(params));
}
_client.req_cb = {
success: function(o) {
     var panel = new YAHOO.util.Element('panel-$suffix');
     try {
         var ret = YAHOO.lang.JSON.parse(o.responseText);
     } catch(e) {
         alert('$strinvalidjson - |req_cb| -'+o.responseText);
     };
     if(ret && ret.e) {
         panel.get('element').innerHTML = ret.e;
         return;
     }
     _client.ds = ret;
     if(!_client.ds) {
         return;
     }else if(_client.ds && _client.ds.login) {
         _client.print_login();
     } else if(_client.ds.list) {
         if(_client.viewmode) {
             _client.viewthumb();
         } else {
             _client.viewlist();
         }
     }
}
}
_client.download_cb = {
success: function(o) {
     var panel = new YAHOO.util.Element('panel-$suffix');
     try {
         var ret = YAHOO.lang.JSON.parse(o.responseText);
     } catch(e) {
         alert('$strinvalidjson - |download_cb| -'+o.responseText);
     }
     if(ret && ret.e) {
         panel.get('element').innerHTML = ret.e;
         return;
     }
     if(ret) {
         repository_client_$suffix.end(ret);
     }
}
}

return _client;
})();
EOD;

$repos = repository_get_instances(array($context,get_system_context()));
foreach ($repos as $repo) {
    $js .= "\r\n";
    $js .= 'repository_client_'.$suffix.'.repos.push('.json_encode($repo->ajax_info()).');'."\n";
}
$js .= "\r\n";

$js .= <<<EOD
function openpicker_$suffix(params) {
    if(!repository_client_$suffix.instance) {
        repository_client_$suffix.env = params.env;
        repository_client_$suffix.target = params.target;
        if(params.type) {
            repository_client_$suffix.filetype = params.filetype;
        } else {
            repository_client_$suffix.filetype = 'all';
        }
        repository_client_$suffix.instance = new repository_client_$suffix();
        repository_client_$suffix.instance.create_picker();
        if(params.callback) {
            repository_client_$suffix.formcallback = params.callback;
        } else {
            repository_client_$suffix.formcallback = function() {};
        }
    } else {
        repository_client_$suffix.instance.show();
    }
}
//]]>
</script>
EOD;
return array('css'=>$css, 'js'=>$js, 'suffix'=>$suffix);
}
