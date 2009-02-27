<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
//       Don't modify this file unless you know how it works             //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once(dirname(dirname(__FILE__)).'/config.php');
$yui     = optional_param('yui', 0, PARAM_RAW);              // page or path
if (!empty($yui)) {
    repository_get_yui();
}
function repository_get_yui() {
    global $CFG;

    $lifetime = '86400';

    @header('Content-type: text/javascript'); 
    @header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    @header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
    @header('Cache-control: max-age='.$lifetime);
    @header('Pragma: ');

    $jslist = array(
        'yahoo-dom-event/yahoo-dom-event.js',
        'element/element-beta-min.js',
        'treeview/treeview-min.js',
        'dragdrop/dragdrop-min.js',
        'container/container-min.js',
        'resize/resize-min.js',
        'layout/layout-min.js',
        'connection/connection-min.js',
        'json/json-min.js',
        'button/button-min.js',
        'selector/selector-beta-min.js'
        );
    foreach ($jslist as $js) {
        echo "/* Included from lib/yui/$js */\n";
        readfile($CFG->dirroot.'/lib/yui/'.$js);
        echo "\n\n";
    }
    exit();
}
/**
 * Return javascript to create file picker to browse repositories
 * @global object $CFG
 * @global object $USER
 * @param object $context the context
 * @return array
 */
function repository_get_client($context, $accepted_filetypes = '*', $returnvalue = '*') {
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
    $strfederatedsearch = get_string('federatedsearch', 'repository');
    $strhelp      = get_string('help');
    $strrefresh   = get_string('refresh', 'repository');
    $strinvalidjson = get_string('invalidjson', 'repository');
    $strlistview  = get_string('listview', 'repository');
    $strlogin     = get_string('login', 'repository');
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
    $strupload    = get_string('upload', 'repository').'...';
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
.fp-login-form{text-align:center}
.fp-searchbar{float:right}
.fp-viewbar{width:300px;float:left}
.fp-toolbar{padding: .8em;background: #FFFFCC;color:white;text-align:center}
.fp-toolbar a{padding: 0 .5em}
.fp-list{list-style-type:none;padding:0;float:left;width:100%;margin:0;}
.fp-list li{border-bottom:1px dotted gray;margin-bottom: 1em;}
.fp-repo-name{display:block;padding: .5em;margin-bottom: .5em}
.fp-pathbar{margin: .4em;border-bottom: 1px dotted gray;}
.fp-pathbar a{padding: .4em;}
.fp-rename-form{text-align:center}
.fp-rename-form p{margin: 1em;}
.fp-upload-form{margin: 2em 0;text-align:center}
.fp-upload-btn a{cursor: default;background: white;border:1px solid gray;color:black;padding: .5em}
.fp-upload-btn a:hover {background: grey;color:white}
.fp-paging{margin:1em .5em; clear:both;text-align:center;line-height: 2.5em;}
.fp-paging a{padding: .5em;border: 1px solid #CCC}
.fp-paging a.cur_page{border: 1px solid blue}
.fp-popup{text-align:center}
.fp-grid{float:left;text-align:center;}
.fp-grid div{overflow: hidden}
.fp-grid p{margin:0;padding:0;background: #FFFFCC}
.fp-grid .label{height:48px;text-align:center}
.fp-grid span{color:gray}
</style>

<!--[if IE 6]>
    <style type="text/css">
    /* Fix for IE6 */
    .yui-skin-sam .yui-panel .hd{

    }
    </style>
<![endif]-->
EOD;

        $js = '<script type="text/javascript" src="'.$CFG->httpswwwroot.'/repository/javascript.php?yui=1"></script>';
        $CFG->repo_yui_loaded = true;
    } else {
        $js = '';
    }

    $js .= <<<EOD
<script type="text/javascript">
//<![CDATA[
//
var mdl_in_array = function(el, arr) {
    for(var i = 0, l = arr.length; i < l; i++) {
        if(arr[i] == el) {
            return true;
        }
    }
    return false;
}

var active_instance = null;
function repository_callback(id) {
    active_instance.req(id, '');
}
var repository_client_$suffix = (function() {
// private static field
var dver = '1.0';
// private static methods
function version() {
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
                height: 480, width: 700,
                units: [
                {position: 'top', height: 32, resize: false,
                body:'<div class="yui-buttongroup fp-viewbar" id="repo-viewbar-$suffix"></div><div class="fp-searchbar" id="search-div-$suffix"></div>', gutter: '2'},
                {position: 'left', width: 200, resize: true, scroll:true,
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
        minWidth: 680,
        minHeight: 400
    });
    if(YAHOO.env.ua.ie == 6){
        var fp_title = document.getElementById('file-picker-$suffix');
        fp_title.style.width = '680px';
    }
    resize.on('resize', function(args) {
        var panelHeight = args.height;
        var headerHeight = this.header.offsetHeight; // Content + Padding + Border
        var bodyHeight = (panelHeight - headerHeight);
        var bodyContentHeight = (IE_QUIRKS) ? bodyHeight : bodyHeight - PANEL_BODY_PADDING;
        Dom.setStyle(this.body, 'height', bodyContentHeight + 'px');
        if(YAHOO.env.ua.ie == 6){
            var fp_title = document.getElementById('file-picker-$suffix');
            fp_title.style.width = args.width;
        }
        if (IE_SYNC) {
            this.sizeUnderlay();
            this.syncIframe();
        }
        layout.set('height', bodyContentHeight);
        layout.set('width', (args.width - PANEL_BODY_PADDING));
        layout.resize();

    }, filepicker, true);
    filepicker.update_instances = function(){
        _client.print_instances();
    }
    _client.viewbar = new YAHOO.widget.ButtonGroup({
        id: 'btngroup-$suffix',
        name: 'buttons',
        disabled: true,
        container: 'repo-viewbar-$suffix'
    });
}
// public method
this.show = function() {
    filepicker.update_instances();
    var panel = new YAHOO.util.Element('panel-$suffix');
    panel.get('element').innerHTML = '';
    filepicker.show();
}
this.hide = function() {
    filepicker.hide();
}
this.create_picker = function() {
    // display UI
    filepicker.render();
    _client.viewbar.addButtons([btn_thumb, btn_list]);
    // init repository list
    repo_list = new YAHOO.util.Element('repo-list-$suffix');
    repo_list.on('contentReady', function(e) {
        var searchbar = new YAHOO.util.Element('search-div-$suffix');
        searchbar.get('element').innerHTML = '<input id="search-input-$suffix" /><button id="search-btn-$suffix">$strfederatedsearch</button>';
        var btn_search = new YAHOO.util.Element('search-btn-$suffix');
        var input_keyword = new YAHOO.util.Element('search-input-$suffix');
        btn_search.fnSearch = function(e) {
            var el = new YAHOO.util.Element('search-input-$suffix')
            var keyword = el.get('value');
            var params = [];
            params['s'] = keyword;
            params['env']=_client.env;
            params['action']='gsearch';
            params['accepted_types'] = _client.accepted_types;
            params['sesskey']='$sesskey';
            params['ctx_id']=$context->id;
            _client.loading('load');
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                '$CFG->httpswwwroot/repository/ws.php?action=gsearch', this.global_search_cb, _client.postdata(params));
        }
        btn_search.global_search_cb={
            success: function(o) {
                var panel = new YAHOO.util.Element('panel-$suffix');
                if(!o.responseText) {
                    panel.get('element').innerHTML = '$strnoresult';
                    return;
                }
                try {
                    var data = YAHOO.lang.JSON.parse(o.responseText);
                } catch(e) {
                    alert('$strinvalidjson - |global_search_cb| -'+_client.stripHTML(o.responseText));
                    return;
                }
                _client.ds={};
                if(!data.list || data.list.length<1){
                    panel.get('element').innerHTML = '$strnoresult';
                    return;
                }
                _client.ds.list = data.list;
                if(_client.viewmode) {
                    _client.viewlist();
                } else {
                    _client.viewthumb();
                }
                var el = new YAHOO.util.Element('search-input-$suffix')
                el.set('value', '');
            }
        }
        btn_search.on('contentReady', function() {
            btn_search.on('click', this.fnSearch, this.input_keyword);
        });
        input_keyword.on('contentReady', function() {
            var scope = document.getElementById('search-input-$suffix');
            var k1 = new YAHOO.util.KeyListener(scope, {keys:13}, {fn:function(){this.fnSearch()},scope:btn_search, correctScope: true});
            k1.enable();
        });
        _client.print_instances();
    });
}
}

// public static varible
_client.repos = [];
_client.repositoryid = 0;
// _client.ds save all data received from server side
_client.ds = null;
_client.viewmode = 0;
_client.viewbar = null;
_client.print_instances = function() {
    var container = new YAHOO.util.Element('repo-list-$suffix');
    container.set('innerHTML', '');
    for(var i in _client.repos) {
        var repo = _client.repos[i];
        var support = false;
        if(repository_client_$suffix.env=='editor' && _client.accepted_types != '*'){
            if(repo.supported_types!='*'){
                for (var j in repo.supported_types){
                    if(mdl_in_array(repo.supported_types[j], _client.accepted_types)){
                        support = true;
                    }
                }
            }
        }else{
            support = true;
        }
        if(repo.supported_types == '*' || support){
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
                // highlight active repo
                for(var cc in _client.repos){
                    var tmp_id = 'repo-call-$suffix-'+ _client.repos[cc].id;
                    var el = document.getElementById(tmp_id);
                    if(el){
                        el.style.background = 'transparent';
                    }
                }
                this.style.background = '#CCC';
                var re = /repo-call-$suffix-(\d+)/i;
                var id = this.id.match(re);
                repository_client_$suffix.req(id[1], '');
            }
            link.innerHTML += ' '+repo.name;
            li.appendChild(link);
            container.appendChild(li);
            repo = null;
        }
    }
}
_client.stripHTML = function(str){
    var re= /<\S[^><]*>/g
    var ret = str.replace(re, "")
    return ret;
}

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
    var thumbnail = document.getElementById('fp-grid-panel-$suffix');
    if(thumbnail){
        thumbnail.style.display = 'none';
    }
    var header = document.getElementById('fp-header-$suffix');
    header.style.display = 'none';
    var footer = document.getElementById('fp-footer-$suffix');
    footer.style.display = 'none';
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
    html += '<input type="button" onclick="repository_client_$suffix.download()" value="$strdownbtn" />';
    html += '<input type="button" onclick="repository_client_$suffix.viewfiles()" value="$strcancel" /></p>';
    html += '</div>';
    panel.get('element').innerHTML += html;
    var tree = document.getElementById('treediv-$suffix');
    if(tree){
        tree.style.display = 'none';
    }
}
_client.popup = function(url) {
    active_instance = repository_client_$suffix;
    _client.win = window.open(url,'repo_auth', 'location=0,status=0,scrollbars=0,width=500,height=300');
    return false;
}
_client.print_login = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    var data = _client.ds.login;
    var str = '<div class="fp-login-form">';
    var has_pop = false;
    for(var k in data) {
        if(data[k].type=='popup') {
            str += '<p class="fp-popup">$strpopup</p>';
            str += '<p class="fp-popup"><button onclick="repository_client_$suffix.popup(\''+data[k].url+'\')">$strlogin</button>';
            str += '</p>';
            has_pop = true;
        }else if(data[k].type=='textarea') {
            str += '<p><textarea id="'+data[k].id+'" name="'+data[k].name+'"></textarea></p>';
        }else{
            str += '<p>';
            var label_id = '';
            var field_id = '';
            var field_value = '';
            if(data[k].id) {
                label_id = ' for="'+data[k].id+'"';
                field_id = ' id="'+data[k].id+'"';
            }
            if (data[k].label) {
                str += '<label'+label_id+'>'+data[k].label+'</label>&nbsp;';
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
    str += '</div>';
    panel.get('element').innerHTML = str;
}

_client.viewfiles = function() {
    if(_client.viewmode) {
        _client.viewlist();
    } else {
        _client.viewthumb();
    }
}
_client.print_msg = function(msg) {
    _client.print_header();
    var panel = new YAHOO.util.Element('panel-$suffix');
    panel.get('element').innerHTML += msg;
    _client.print_footer();
}
_client.print_header = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    var str = '<div id="fp-header-$suffix">';
    str += '<div class="fp-toolbar" id="repo-tb-$suffix"></div>';
    if(_client.ds.pages < 8){
        str += _client.makepage('header');
    }
    str += '</div>';
    panel.set('innerHTML', str);
    _client.makepath();
}
_client.print_footer = function() {
    var panel = document.getElementById('panel-$suffix');
    var footer = document.createElement('DIV');
    footer.id = 'fp-footer-$suffix';
    footer.innerHTML += _client.uploadcontrol();
    footer.innerHTML += _client.makepage('footer');
    panel.appendChild(footer);
    // add repository manage buttons here
    var oDiv = document.getElementById('repo-tb-$suffix');
    if(!_client.ds.nosearch) {
        var search = document.createElement('A');
        search.href = '###';
        search.innerHTML = '<img src="$CFG->pixpath/a/search.png" /> $strsearch';
        oDiv.appendChild(search);
        search.onclick = function() {
            repository_client_$suffix.search_form(repository_client_$suffix.repositoryid);
        }
    }
    // weather we use cache for this instance, this button will reload listing anyway
    if(!_client.ds.norefresh) {
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
            repository_client_$suffix.logout(repository_client_$suffix.repositoryid, 1);
        }
    }
    if(_client.ds.help) {
        var help = document.createElement('A');
        help.href = _client.ds.help;
        help.target = "_blank";
        help.innerHTML = '<img src="$CFG->pixpath/a/help.png" /> $strhelp';
        oDiv.appendChild(help);
    }
}
_client.viewthumb = function(ds) {
    _client.viewmode = 0;
    _client.viewbar.check(0);
    var container = document.getElementById('panel-$suffix');
    var panel = document.createElement('DIV');
    panel.id = 'fp-grid-panel-$suffix';
    var list = null;
    if(arguments.length == 1) {
        list = ds;
    } else {
        // from button
        list = _client.ds.list;
    }
    _client.print_header();
    var count = 0;
    for(k in list) {
        // the container
        var el = document.createElement('div');
        el.className='fp-grid';
        // the file name
        var title = document.createElement('div');
        title.id = 'grid-title-'+String(count);
        title.className = 'label';
        if (list[k].shorttitle) {
            list[k].title = list[k].shorttitle;
        }
        title.innerHTML += '<a href="###"><span>'+list[k].title+"</span></a>";
        if(list[k].thumbnail_width){
            el.style.width = list[k].thumbnail_width+'px';
            title.style.width = (list[k].thumbnail_width-10)+'px';
        } else {
            el.style.width = title.style.width = '80px';
        }
        var frame = document.createElement('DIV');
        frame.style.textAlign='center';
        if(list[k].thumbnail_height){
            frame.style.height = list[k].thumbnail_height+'px';
        }
        var img = document.createElement('img');
        img.src = list[k].thumbnail;
        if(list[k].thumbnail_alt) {
            img.alt = list[k].thumbnail_alt;
        }
        if(list[k].thumbnail_title) {
            img.title = list[k].thumbnail_title;
        }
        var link = document.createElement('A');
        link.href='###';
        link.id = 'img-id-'+String(count);
        if(list[k].url) {
            el.innerHTML += '<p><a target="_blank" href="'+list[k].url+'">$strpreview</a></p>';
        }
        link.appendChild(img);
        frame.appendChild(link);
        el.appendChild(frame);
        el.appendChild(title);
        
        panel.appendChild(el);
        if(list[k].children) {
            var folder = new YAHOO.util.Element(link.id);
            folder.ds = list[k].children;
            folder.path = list[k].path;
            var el_title = new YAHOO.util.Element(title.id);
            folder.on('contentReady', function() {
                this.on('click', function() {
                    if(_client.ds.dynload) {
                        var params = [];
                        params['p'] = this.path;
                        params['env'] = _client.env;
                        params['repo_id'] = _client.repositoryid;
                        params['ctx_id'] = $context->id;
                        params['sesskey']= '$sesskey';
                        params['accepted_types'] = _client.accepted_types;
                        _client.loading('load');
                        var trans = YAHOO.util.Connect.asyncRequest('POST',
                                '$CFG->httpswwwroot/repository/ws.php?action=list', _client.req_cb, _client.postdata(params));
                    }else{
                        _client.viewthumb(this.ds);
                    }
                });
            });
            el_title.on('contentReady', function() {
                this.on('click', function(){
                    folder.fireEvent('click');
                });
            });    
        } else {
            var el_title = new YAHOO.util.Element(title.id);
            var file = new YAHOO.util.Element(link.id);
            el_title.title = file.title = list[k].title;
            el_title.value = file.value = list[k].source;
            el_title.icon = file.icon  = list[k].thumbnail;
            if(list[k].repo_id) {
                el_title.repo_id = file.repo_id = list[k].repo_id;
            }else{
                el_title.repo_id = file.repo_id = _client.repositoryid;
            }
            file.on('contentReady', function() {
                this.on('click', function() {
                    repository_client_$suffix.rename(this.title, this.value, this.icon, this.repo_id);
                });
            });
            el_title.on('contentReady', function() {
                this.on('click', function() {
                    repository_client_$suffix.rename(this.title, this.value, this.icon, this.repo_id);
                });
            });
        }
        count++;
    }
    container.appendChild(panel);
    _client.print_footer();
}
_client.buildtree = function(node, level) {
    if(node.children) {
        node.title = '<i><u>'+node.title+'</u></i>';
    }
    var info = {
        label:node.title,
        title:"$strdate"+node.date+' $strsize'+node.size,
        filename:node.title,
        value:node.source,
        icon:node.thumbnail,
        path:node.path
    };
    var tmpNode = new YAHOO.widget.TextNode(info, level, false);
    var tooltip = new YAHOO.widget.Tooltip(tmpNode.labelElId, {
        context:tmpNode.labelElId, text:info.title});
    if(node.repo_id) {
        tmpNode.repo_id=node.repo_id;
    }else{
        tmpNode.repo_id=_client.repositoryid;
    }
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
    }
}
_client.dynload = function (node, fnLoadComplete) {
    var callback = {
        success: function(o) {
             try {
                 var json = YAHOO.lang.JSON.parse(o.responseText);
             } catch(e) {
                 alert('$strinvalidjson - |dynload| -'+_client.stripHTML(o.responseText));
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
    params['accepted_types'] = _client.accepted_types;
    var trans = YAHOO.util.Connect.asyncRequest('POST',
            '$CFG->httpswwwroot/repository/ws.php?action=list', callback, _client.postdata(params));
}
_client.viewiframe = function() {
    var panel = new YAHOO.util.Element('panel-$suffix');
    panel.get('element').innerHTML = "<iframe frameborder=\"0\" width=\"98%\" height=\"400px\" src=\""+_client.ds.iframe+"\" />";
}
_client.viewlist = function() {
    _client.viewmode = 1;
    var panel = new YAHOO.util.Element('panel-$suffix');
    _client.viewbar.check(1);
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
    tree.subscribe('clickEvent', function(e){
        if(e.node.isLeaf){
            repository_client_$suffix.rename(e.node.data.filename, e.node.data.value, e.node.data.icon, e.node.repo_id);
        }
    });
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
            alert('$strinvalidjson - |upload| -'+_client.stripHTML(o.responseText));
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
        str += '<label for="'+_client.ds.upload.id+'-file">'+_client.ds.upload.label+': </label>';
        str += '<input type="file" id="'+_client.ds.upload.id+'-file" name="repo_upload_file" />';
        str += '<p class="fp-upload-btn"><a href="###" onclick="return repository_client_$suffix.upload();">$strupload</a></p>';
        str += '</form>';
        str += '</div>';
    }
    return str;
}
_client.paging_node = function(type, page) {
    if (page == _client.ds.page) {
        str = '<a class="cur_page" onclick="repository_client_$suffix.'+type+'('+_client.repositoryid+', '+page+', '+page+')" href="###">';
    } else {
        str = '<a onclick="repository_client_$suffix.'+type+'('+_client.repositoryid+', '+page+', '+page+')" href="###">';
    }
    return str;
}
_client.makepage = function(id) {
    var str = '';
    if(_client.ds.pages) {
        str += '<div class="fp-paging" id="paging-'+id+'-$suffix">';
        if(!_client.ds.search_result){
            var action = 'req';
        } else {
            var action = 'search_paging';
        }
        str += _client.paging_node(action, 1)+'1</a>';

        if (_client.ds.page+2>=_client.ds.pages) {
            var max = _client.ds.pages;
        } else {
            var max = _client.ds.page+2;
        }
        if (_client.ds.page-2 >= 3) {
            str += ' ... ';
            for(var i = _client.ds.page-2; i < max; i++) {
                str += _client.paging_node(action, i);
                str += String(i);
                str += '</a> ';
            }
        } else {
            for(var i = 2; i < max; i++) {
                str += _client.paging_node(action, i);
                str += String(i);
                str += '</a> ';
            }
        }
        if (max==_client.ds.pages) {
            str += _client.paging_node(action, _client.ds.pages)+_client.ds.pages+'</a>';
        } else {
            str += _client.paging_node(action, max)+max+'</a>';
            str += ' ... '+_client.paging_node(action, _client.ds.pages)+_client.ds.pages+'</a>';
        }
        str += '</div>';
    }
    return str;
}
_client.search_paging = function(id, path, page) {
    _client.viewbar.set('disabled', false);
    _client.loading('load');
    _client.repositoryid = id;
    var params = [];
    params['p'] = path;
    params['page'] = page;
    params['env']=_client.env;
    params['accepted_types'] = _client.accepted_types;
    params['action']='search';
    params['search_paging']='true';
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=id;
    var trans = YAHOO.util.Connect.asyncRequest('POST', '$CFG->httpswwwroot/repository/ws.php?action='+action, _client.req_cb, _client.postdata(params));
}
_client.makepath = function() {
    if(_client.viewmode == 1) {
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
                var path_link = document.getElementById(link.id);
                path_link.id = this.id;
                path_link.path = this.path
                path_link.onclick = function() {
                    repository_client_$suffix.req(this.id, this.path);
                }
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
    if(_client.itemid){
        params['itemid']=_client.itemid;
    }
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
    params['accepted_types'] = _client.accepted_types;
    _client.loading('load');
    var trans = YAHOO.util.Connect.asyncRequest('POST',
            '$CFG->httpswwwroot/repository/ws.php?action=sign', _client.req_cb, _client.postdata(params));
}
_client.end = function(obj) {
    if(_client.env=='filepicker') {
        _client.target.value = obj['id'];
    }else if(_client.env=='editor'){
        _client.target.value = obj['url'];
        _client.target.onchange();
    }
    _client.formcallback(obj);
    _client.instance.hide();
    _client.viewfiles();
}
_client.hide = function() {
    _client.instance.hide();
    _client.viewfiles();
}
// request file list or login
_client.req = function(id, path, page) {
    _client.viewbar.set('disabled', false);
    _client.loading('load');
    _client.repositoryid = id;
    var params = [];
    params['p'] = path;
    params['env']=_client.env;
    params['sesskey']='$sesskey';
    params['ctx_id']=$context->id;
    params['repo_id']=id;
    if (page) {
        params['page']=page;
    }
    params['accepted_types'] = _client.accepted_types;
    var trans = YAHOO.util.Connect.asyncRequest('POST', '$CFG->httpswwwroot/repository/ws.php?action=list', _client.req_cb, _client.postdata(params));
}
_client.logout = function(id) {
    _client.repositoryid = id;
    var params = [];
    params['repo_id'] = id;
    var trans = YAHOO.util.Connect.asyncRequest('POST', '$CFG->httpswwwroot/repository/ws.php?action=logout', _client.req_cb, _client.postdata(params));
}
_client.search_form_cb = {
success: function(o) {
     var el = document.getElementById('fp-search-dlg');
     var _r = repository_client_$suffix;
     if(el) {
         el.innerHTML = '';
     } else {
         var el = document.createElement('DIV');
         el.id = 'fp-search-dlg';
     }
     var div1 = document.createElement('DIV');
     div1.className = 'hd';
     div1.innerHTML = "$strsearching\"" + _r.repos[_r.repositoryid].name + '"';
     var div2 = document.createElement('DIV');
     div2.className = 'bd';
     var sform = document.createElement('FORM');
     sform.method = 'POST';
     sform.id = "fp-search-form";
     sform.action = '$CFG->httpswwwroot/repository/ws.php?action=search';
     sform.innerHTML = o.responseText;
     div2.appendChild(sform);
     el.appendChild(div1);
     el.appendChild(div2);
     document.body.appendChild(el);
     var dlg = new YAHOO.widget.Dialog("fp-search-dlg",{
        postmethod: 'async',
        draggable: true,
        width : "30em",
        fixedcenter : true,
        zindex: 766667,
        visible : false,
        constraintoviewport : true,
        buttons : [
            { text:"$strsubmit",handler: function() {
                _client.viewbar.set('disabled', false); _client.loading('load');
                YAHOO.util.Connect.setForm('fp-search-form', false, false);
                this.cancel();
                var trans = YAHOO.util.Connect.asyncRequest('POST',
                    '$CFG->httpswwwroot/repository/ws.php?action=search&env='+_client.env, _client.req_cb);
                },isDefault:true},
            {text:"$strcancel",handler:function() {this.cancel()}}
        ]
    });
    dlg.render();
    dlg.show();
}
}
_client.search_form = function(id) {
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
         var data = YAHOO.lang.JSON.parse(o.responseText);
     } catch(e) {
         alert('$strinvalidjson - |req_cb| -'+_client.stripHTML(o.responseText));
     };
     if(data && data.e) {
         panel.get('element').innerHTML = data.e;
         return;
     }
     _client.ds = data;
     if(!data) {
         return;
     }else if(data.msg){
         _client.print_msg(data.msg);
     }else if(data.iframe) {
         _client.viewiframe();
     }else if(data.login) {
         _client.print_login();
     }else if(data.list) {
         if(_client.viewmode) {
             _client.viewlist();
         } else {
             _client.viewthumb();
         }
     }
}
}
_client.download_cb = {
success: function(o) {
     var panel = new YAHOO.util.Element('panel-$suffix');
     try {
         var data = YAHOO.lang.JSON.parse(o.responseText);
     } catch(e) {
         alert('$strinvalidjson - |download_cb| -'+_client.stripHTML(o.responseText));
     }
     if(data && data.e) {
         panel.get('element').innerHTML = data.e;
         return;
     }
     if(data) {
         repository_client_$suffix.end(data);
     }
}
}

return _client;
})();
EOD;

$user_context = get_context_instance(CONTEXT_USER, $USER->id);
if (is_array($accepted_filetypes) && in_array('*', $accepted_filetypes)) {
    $accepted_filetypes = '*';
}
$repos = repository::get_instances(array($user_context, $context, get_system_context()), null, true, null, $accepted_filetypes, $returnvalue);
$js .= "\r\n".'repository_client_'.$suffix.'.repos=[];'."\r\n";
foreach ($repos as $repo) {
    $info = $repo->ajax_info();
    $js .= "\r\n";
    $js .= 'repository_client_'.$suffix.'.repos['.$info->id.']='.json_encode($repo->ajax_info()).';'."\n";
}
$js .= "\r\n";

$ft = new file_type_to_ext();
$image_file_ext = json_encode($ft->get_file_ext(array('image')));
$video_file_ext = json_encode($ft->get_file_ext(array('video')));
$accepted_file_ext = json_encode($ft->get_file_ext($accepted_filetypes));
$js .= <<<EOD
function openpicker_$suffix(params) {
    if(params.filetype) {
        if(params.filetype == 'image') {
            repository_client_$suffix.accepted_types = $image_file_ext;
        } else if(params.filetype == 'video' || params.filetype== 'media') {
            repository_client_$suffix.accepted_types = $video_file_ext;
        } else if(params.filetype == 'file') {
            repository_client_$suffix.accepted_types = '*';
        }
    } else {
        repository_client_$suffix.accepted_types = $accepted_file_ext;
    }
    if(!repository_client_$suffix.instance) {
        repository_client_$suffix.env = params.env;
        repository_client_$suffix.target = params.target;
        if(params.itemid){
            repository_client_$suffix.itemid = params.itemid;
        } else if(tinyMCE && id2itemid[tinyMCE.selectedInstance.editorId]){
            repository_client_$suffix.itemid = id2itemid[tinyMCE.selectedInstance.editorId];
        }
        repository_client_$suffix.instance = new repository_client_$suffix();
        repository_client_$suffix.instance.create_picker();
        if(params.callback) {
            repository_client_$suffix.formcallback = params.callback;
        } else {
            repository_client_$suffix.formcallback = function() {};
        }
    } else {
        repository_client_$suffix.target = params.target;
        repository_client_$suffix.instance.show();
    }
}
//]]>
</script>
EOD;
return array('css'=>$css, 'js'=>$js, 'suffix'=>$suffix);
}
