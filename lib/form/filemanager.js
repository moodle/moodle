/**
 * This file is part of Moodle - http://moodle.org/
 * File manager
 * @copyright  1999 onwards Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var fm_cfg = {};
var fm_move_dlg = null;
var fm_rename_dlg = null;
var fm_mkdir_dlg  = null;

// initialize file manager
var filemanager = (function(){
    function _filemanager() {
        this.init = function(client_id, options) {
            this.client_id = client_id;
            html_compiler(client_id, options);
        }
    }
    return _filemanager;
})();

filemanager.url = moodle_cfg.wwwroot + '/files/files_ajax.php';
filemanager.fileicon = moodle_cfg.wwwroot + '/pix/i/settings.gif';

// callback function for file picker
function filemanager_callback(obj) {
    refresh_filemanager(obj.filepath, fm_cfg[obj.client_id]);
    fm_cfg[obj.client_id].currentfiles++;

    if (fm_cfg[obj.client_id].currentfiles>=fm_cfg[obj.client_id].maxfiles && fm_cfg[obj.client_id].maxfiles!=-1) {
        var btn = document.getElementById('btnadd-'+obj.client_id);
        if (btn)
            btn.style.display = 'none';
    }
}

// setup options for file picker
function fm_launch_filepicker(el_id, options) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+options.client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById(el_id);
    var params = {};
    params.env = 'filemanager';
    params.itemid = options.itemid;
    params.maxfiles = options.maxfiles;
    params.maxbytes = options.maxbytes;
    params.savepath = options.savepath;
    params.target = el;
    params.callback = filemanager_callback;
    var fp = open_filepicker(options.client_id, params);
    return false;
}

// create a new folder in draft area
function mkdir(e, client_id, itemid) {
    var mkdir_cb = {
        success: function(o) {
            var result = json_decode(o.responseText);
            fm_mkdir_dlg.hide();
            refresh_filemanager(result.filepath, fm_cfg[client_id]);
        }
    }
    var perform = function(e) {
        var foldername = document.getElementById('fm-newname').value;
        if (!foldername) {
            return;
        }
        var params = [];
        params['itemid'] = itemid;
        params['newdirname'] = foldername;
        params['sesskey'] = moodle_cfg.sesskey;
        params['filepath'] = fm_cfg[client_id].currentpath;
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            filemanager.url+'?action=mkdir', mkdir_cb, build_querystring(params));
        YAHOO.util.Event.preventDefault(e);
    }
    if (!document.getElementById('fm-mkdir-dlg')) {
        var el = document.createElement('DIV');
        el.id = 'fm-mkdir-dlg';
        el.innerHTML = '<div class="hd">'+mstr.repository.entername+'</div><div class="bd"><input type="text" id="fm-newname" /></div>';
        document.body.appendChild(el);
        var x = YAHOO.util.Event.getPageX(e);
        var y = YAHOO.util.Event.getPageY(e);
        fm_mkdir_dlg = new YAHOO.widget.Dialog("fm-mkdir-dlg", {
             width: "300px",
             visible: true,
             x:y,
             y:y,
             constraintoviewport : true
             });

    }
    var buttons = [ { text:mstr.moodle.ok, handler:perform, isDefault:true },
                      { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

    fm_mkdir_dlg.cfg.queueProperty("buttons", buttons);
    fm_mkdir_dlg.render();
    fm_mkdir_dlg.show();

    var k1 = new YAHOO.util.KeyListener(document.getElementById('fm-mkdir-dlg'), {keys:13}, {fn:function(){perform();}, correctScope: true});
    k1.enable();

    document.getElementById('fm-newname').value = '';
}

// generate html
function html_compiler(client_id, options) {
    var list = options.list;
    var breadcrumb = document.getElementById('fm-path-'+client_id);
    var count = 0;
    if (options.path) {
        breadcrumb.innerHTML = '';
        var count = 0;
        for(var p in options.path) {
            var sep = document.createElement('SPAN');
            sep.innerHTML = ' ▶ ';
            if (count==0) {
                sep.innerHTML = mstr.moodle.path + ': ';
            } else {
                sep.innerHTML = ' ▶ ';
            }
            count++;
            var pathid  = 'fm-path-node-'+client_id;
            pathid += ('-'+count);

            var el = document.createElement('A');
            el.id = pathid;
            el.innerHTML = options.path[p].name;
            el.href = '###';
            breadcrumb.appendChild(sep);
            breadcrumb.appendChild(el);

            var args = {};
            args.itemid = options.itemid;
            args.requestpath = options.path[p].path;
            args.client_id = client_id;

            YAHOO.util.Event.addListener(pathid, 'click', click_breadcrumb, args);
        }
    }
    var template = document.getElementById('fm-tmpl');
    var container = document.getElementById('filemanager-' + client_id);
    var listhtml = '<ul id="draftfiles-'+client_id+'">';

    var folder_ids = [];

    var file_ids   = [];
    var file_data  = {};
    var folder_data = {};
    var html_ids = [];
    var html_data = {};
    var zip_data = {};
    file_data.itemid = folder_data.itemid = zip_data.itemid = options.itemid;
    file_data.client_id = folder_data.client_id = zip_data.client_id = options.client_id;

    var zip_ids    = [];
    var foldername_ids = [];
    if (list.length == 0) {
        // hide file browser and breadcrumb
        container.style.display='none';
        if (options.path.length <= 1) {
            breadcrumb.style.display='none';
        }
        return;
    } else {
        container.style.display='block';
        breadcrumb.style.display='block';
    }
    count = 0;
    for(var i in list) {
        count++;
        var htmlid = 'fileitem-'+client_id+'-'+count;
        var fileid = 'filename-'+client_id+'-'+count;
        var action = 'action-'  +client_id+'-'+count;
        var html = template.innerHTML;

        html_ids.push(htmlid);
        html_data[htmlid] = action;

        list[i].htmlid = htmlid;
        list[i].fileid = fileid;
        list[i].action = action;
        var url = "###";
        var ismainfile = false;
        if (fm_cfg[client_id].mainfilename && (fm_cfg[client_id].mainfilename.toLowerCase() == list[i].fullname.toLowerCase())) {
            ismainfile = true;
        }
        switch (list[i].type) {
            case 'folder':
                foldername_ids.push(fileid);
                folder_ids.push(action);
                folder_data[action] = list[i];
                folder_data[fileid] = list[i];
                break;
            case 'file':
                file_ids.push(action);
                file_data[action] = list[i];
                if (list[i].url) {
                    url = list[i].url;
                }
            break;
            case 'zip':
                zip_ids.push(action);
                zip_data[action] = list[i];
                if (list[i].url) {
                    url = list[i].url;
                }
            break;
        }
        var fullname = list[i].fullname;
        if (ismainfile) {
            fullname = "<strong>"+list[i].fullname+"</strong> <img src='"+moodle_cfg.wwwroot+"/pix/i/tick_green_small.gif"+"' />";
        }
        html = html.replace('___fullname___', '<a href="'+url+'" id="'+fileid+'"><img src="'+list[i].icon+'" /> ' + fullname + '</a>');
        html = html.replace('___action___', '<a style="display:none" href="###" id="'+action+'"><img alt="▶" src="'+filemanager.fileicon+'" /></a>');
        html = '<li id="'+htmlid+'">'+html+'</li>';
        listhtml += html;
    }
    container.innerHTML = (listhtml+'</ul>');

    options.client_id=client_id;

    YAHOO.util.Event.addListener(file_ids,   'click', create_filemenu, file_data);
    YAHOO.util.Event.addListener(folder_ids, 'click', create_foldermenu, folder_data);
    YAHOO.util.Event.addListener(zip_ids,    'click', create_zipmenu, zip_data);
    YAHOO.util.Event.addListener(html_ids,   'mouseover', fm_mouseover_menu, html_data);
    YAHOO.util.Event.addListener(html_ids,   'mouseout', fm_mouseout_menu, html_data);

    YAHOO.util.Event.addListener(foldername_ids,'click', click_folder, folder_data);
}

function fm_mouseover_menu(ev, args) {
    this.style.backgroundColor = '#0066EE';
    var menu = args[this.id];
    menu = document.getElementById(menu);
    menu.style.display = 'inline';
}

function fm_mouseout_menu(ev, args) {
    this.style.backgroundColor = 'transparent';
    var menu = args[this.id];
    menu = document.getElementById(menu);
    menu.style.display = 'none';
}

function click_breadcrumb(ev, args) {
    var params = [];
    params['itemid'] = args.itemid;
    params['sesskey'] = moodle_cfg.sesskey;
    params['filepath'] = args.requestpath;
    this.cb = {
        success: function(o) {
            var data = json_decode(o.responseText);
            for(var key in data) {
                this.options[key] = data[key];
            }
            html_compiler(this.client_id, this.options);
        }
    }
    this.cb.options = args;
    this.cb.client_id = args.client_id;

    fm_cfg[args.client_id].currentpath = args.requestpath;
    fm_loading('filemanager-'+args.client_id, 'fm-prgressbar');
    var trans = YAHOO.util.Connect.asyncRequest('POST',
        filemanager.url+'?action=list', this.cb, build_querystring(params));
}

function click_folder(ev, args) {
    var file = args[this.id];
    refresh_filemanager(file.filepath, args);
}

function refresh_filemanager(path, args) {
    var params = [];
    params['itemid'] = args.itemid;
    params['filepath'] = path;
    params['sesskey'] = moodle_cfg.sesskey;
    this.cb = {
        success: function(o) {
            var data = json_decode(o.responseText);
            for(var key in data) {
                this.options[key] = data[key];
            }
            html_compiler(this.client_id, this.options);
        }
    }
    this.cb.options = args;
    this.cb.client_id = args.client_id;

    fm_cfg[args.client_id].currentpath = params['filepath'];
    fm_loading('filemanager-'+args.client_id, 'fm-prgressbar');
    var trans = YAHOO.util.Connect.asyncRequest('POST',
        filemanager.url+'?action=list', this.cb, build_querystring(params));
}

function create_foldermenu(e, data) {
    var file = data[this.id];
    this.zip = function(type, ev, obj) {
        this.cb = {
            success: function(o) {
                 var result = json_decode(o.responseText);
                 if (result) {
                     refresh_filemanager(result.filepath, fm_cfg[this.client_id]);
                 }
            }
        }
        this.cb.client_id = obj.client_id;
        this.cb.file = this.file;
        var params = [];
        params['itemid'] = obj.itemid;
        params['filepath']   = this.file.filepath;
        params['filename']   = '.';
        params['sesskey'] = moodle_cfg.sesskey;
        fm_loading('filemanager-'+obj.client_id, 'fm-prgressbar');
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            filemanager.url+'?action=zip', this.cb, build_querystring(params));
    }
    this.zip.file = file;
    var menuitems = [
        {text: mstr.editor.zip, onclick: {fn: this.zip, obj: data, scope: this.zip}},
        ];
    create_menu(e, 'foldermenu', menuitems, file, data);
}

function create_filemenu(e, data) {
    var file = data[this.id];

    var menuitems = [
        {text: mstr.moodle.download, url:file.url}
        ];
    create_menu(e, 'filemenu', menuitems, file, data);
}

function create_zipmenu(e, data) {
    var file = data[this.id];
    this.unzip = function(type, ev, obj) {
        this.cb = {
            success:function(o) {
                var result = json_decode(o.responseText);
                if (result) {
                    refresh_filemanager(result.filepath, fm_cfg[this.client_id]);
                }
            }
        }
        this.cb.client_id = obj.client_id;
        var params = [];
        params['itemid'] = obj.itemid;
        params['filepath'] = this.file.filepath;
        params['filename'] = this.file.fullname;
        params['sesskey'] = moodle_cfg.sesskey;
        fm_loading('filemanager-'+obj.client_id, 'fm-prgressbar');
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            filemanager.url+'?action=unzip', this.cb, build_querystring(params));
    }
    this.unzip.file = file;

    var menuitems = [
        {text: mstr.moodle.download, url:file.url},
        {text: mstr.moodle.unzip, onclick: {fn: this.unzip, obj: data, scope: this.unzip}}
        ];
    create_menu(e, 'zipmenu', menuitems, file, data);
}

function create_menu(ev, menuid, menuitems, file, options) {
    var position = YAHOO.util.Event.getXY(ev);
    var el = document.getElementById(menuid);
    var menu = new YAHOO.widget.Menu(menuid, {xy:position});

    this.remove = function(type, ev, obj) {
        var args = {};
        args.message = mstr.repository.confirmdeletefile;
        args.callback = function() {
            var params = {};
            if (this.file.type == 'folder') {
                params['filename'] = '.';
                params['filepath'] = this.file.fullname;
            } else {
                params['filename'] = this.file.fullname;
                params['filepath'] = fm_cfg[this.client_id].currentpath;
            }
            params['itemid'] = this.itemid;
            params['sesskey'] = moodle_cfg.sesskey;
            fm_loading('filemanager-'+this.client_id, 'fm-prgressbar');
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                filemanager.url+'?action=delete', this.cb, build_querystring(params));
        }
        var dlg = confirm_dialog(ev, args);
        dlg.file = file;
        dlg.client_id = obj.client_id;
        dlg.itemid    = obj.itemid;
        dlg.cb = {
            success: function(o) {
                var result = json_decode(o.responseText);
                if (!result) {
                    alert(mstr.error.cannotdeletefile);
                }
                fm_cfg[this.client_id].currentfiles--;
                if (fm_cfg[this.client_id].currentfiles<fm_cfg[this.client_id].maxfiles) {
                    var btn = document.getElementById('btnadd-'+this.client_id);
                    btn.style.display = 'inline';
                }
                refresh_filemanager(result.filepath, fm_cfg[this.client_id]);
            }
        }
        dlg.cb.file = this.file;
        dlg.cb.client_id = obj.client_id;
    }
    this.remove.file = file;

    this.rename = function(type, ev, obj) {
        var file = this.file;
        var rename_cb = {
            success: function(o) {
                var result = json_decode(o.responseText);
                if (result) {
                    var el = document.getElementById(file.fileid);
                    el.innerHTML = this.newfilename;
                    // update filename
                    file.fullname = this.newfilename;
                    file.filepath = result.filepath;
                    fm_rename_dlg.hide();
                }
            }
        }
        var perform = function(e) {
            var newfilename = document.getElementById('fm-rename-input').value;
            if (!newfilename) {
                return;
            }

            var action = '';
            var params = [];
            params['itemid'] = obj.itemid;
            if (file.type == 'folder') {
                params['filepath']   = file.filepath;
                params['filename']   = '.';
                action = 'renamedir';
            } else {
                params['filepath']   = file.filepath;
                params['filename']   = file.fullname;
                action = 'rename';
            }
            params['newfilename'] = newfilename;

            params['sesskey'] = moodle_cfg.sesskey;
            rename_cb.newfilename = newfilename;
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                filemanager.url+'?action='+action, rename_cb, build_querystring(params));
        }

        var scope = document.getElementById('fm-rename-dlg');
        if (!scope) {
            var el = document.createElement('DIV');
            el.id = 'fm-rename-dlg';
            el.innerHTML = '<div class="hd">'+mstr.repository.enternewname+'</div><div class="bd"><input type="text" id="fm-rename-input" /></div>';
            document.body.appendChild(el);
            fm_rename_dlg = new YAHOO.widget.Dialog("fm-rename-dlg", {
                 width: "300px",
                 fixedcenter: true,
                 visible: true,
                 constraintoviewport : true
                 });

        }
        var buttons = [ { text:mstr.moodle.rename, handler:perform, isDefault:true },
                          { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

        fm_rename_dlg.cfg.queueProperty("buttons", buttons);
        fm_rename_dlg.render();
        fm_rename_dlg.show();

        var k1 = new YAHOO.util.KeyListener(scope, {keys:13}, {fn:function(){perform();}, correctScope: true});
        k1.enable();

        document.getElementById('fm-rename-input').value = file.fullname;
    }
    this.rename.file = file;

    this.move = function(type, ev, obj) {
        var tree = new YAHOO.widget.TreeView("fm-tree");
        var file = this.file;

        this.asyncMove = function(e) {
            if (!tree.targetpath) {
                return;
            }
            var cb = {
                success : function(o) {
                    var result = json_decode(o.responseText);
                    var p = '/';
                    if (result) {
                        p = result.filepath;
                    }
                    refresh_filemanager(result.filepath, fm_cfg[obj.client_id]);
                    this.dlg.cancel();
                }
            }
            cb.dlg = this;
            var params = {};
            if (file.type == 'folder') {
                alert('Moving folder is not supported yet');
                return;
                action = 'movedir';
            } else {
                action = 'movefile';
            }
            params['filepath'] = file.filepath;
            params['filename'] = file.fullname;
            params['itemid'] = obj.itemid;
            params['sesskey'] = moodle_cfg.sesskey;
            params['newfilepath'] = tree.targetpath;
            fm_loading('filemanager-'+obj.client_id, 'fm-prgressbar');
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                filemanager.url+'?action='+action, cb, build_querystring(params));
        }

        var buttons = [ { text:mstr.moodle.move, handler:this.asyncMove, isDefault:true },
                          { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

        fm_move_dlg.cfg.queueProperty("buttons", buttons);


        tree.subscribe("dblClickEvent", function(e) {
            // update destidatoin folder
            this.targetpath = e.node.data.path;
            var el = document.getElementById('fm-move-div');
            el.innerHTML = '<strong>"' + this.targetpath + '"</strong> has been selected.';
            YAHOO.util.Event.preventDefault(e);
        });

        this.loadDataForNode = function(node, onCompleteCallback) {
            this.cb = {
                success: function(o) {
                    var data = json_decode(o.responseText);
                    data = data.children;
                    if (data.length == 0) {
                        // so it is empty
                    } else {
                        for (var i in data) {
                            var textnode = {label: data[i].fullname, path: data[i].filepath, itemid: this.itemid};
                            var tmpNode = new YAHOO.widget.TextNode(textnode, node, false);
                        }
                    }
                    this.complete();
                }
            }
            var params = {};
            params['itemid'] = node.data.itemid;
            params['filepath'] = node.data.path;
            params['sesskey'] = moodle_cfg.sesskey;
            var trans = YAHOO.util.Connect.asyncRequest('POST',
                filemanager.url+'?action=dir', this.cb, build_querystring(params));
            this.cb.complete = onCompleteCallback;
            this.cb.itemid = node.data.itemid;
        }
        this.loadDataForNode.itemid = obj.itemid;

        fm_move_dlg.subscribe('show', function(){

            var el = document.getElementById('fm-move-div');
            el.innerHTML = '<div class="hd"></div><div class="bd"><div id="fm-move-div">'+mstr.repository.nopathselected+'</div><div id="fm-tree"></div></div>';

            var rootNode = tree.getRoot();
            tree.setDynamicLoad(this.loadDataForNode);
            tree.removeChildren(rootNode);
            var textnode = {label: "Files", path: '/', itemid: obj.itemid};
            var tmpNode = new YAHOO.widget.TextNode(textnode, rootNode, true);
            tree.draw();

        }, this, true);

        fm_move_dlg.render();
        fm_move_dlg.show();
    }
    this.move.file = file;
    var shared_items = [
        {text: mstr.moodle.rename+'...', onclick: {fn: this.rename, obj: options, scope: this.rename}},
        {text: mstr.moodle.move+'...', onclick: {fn: this.move, obj: options, scope: this.move}},
        {text: mstr.moodle['delete']+'...', onclick: {fn: this.remove, obj: options, scope: this.remove}}
        ];
    menu.addItems(menuitems);
    menu.addItems(shared_items);
    if (fm_cfg[options.client_id].mainfile && (file.type!='folder')) {
        this.set_mainfile = function(type, ev, obj) {
            if (fm_cfg[obj.client_id].mainfile) {
                var mainfile = document.getElementById(fm_cfg[obj.client_id].mainfile+'-id');
                mainfile.value = this.file.filepath+this.file.fullname;
                document.getElementById(fm_cfg[obj.client_id].mainfile+'-label').innerHTML = mainfile.value;
            }
            fm_cfg[obj.client_id].mainfilename = this.file.fullname;
            refresh_filemanager(fm_cfg[obj.client_id].currentpath, fm_cfg[obj.client_id]);

        }
        this.set_mainfile.file = file;
        menu.addItem({text: mstr.resource.setmainfile, onclick: {fn: this.set_mainfile, obj: options, scope: this.set_mainfile}});
    }
    menu.render(document.body);
    menu.show();
    menu.subscribe('hide', function(){
        this.destroy();
    });
}

function setup_filebrowser(client_id, options) {
    if (!options) {
        options = {};
    }
    fm_cfg[client_id] = {};
    fm_cfg[client_id] = options;
    fm_cfg[client_id].mainfile = options.mainfile;
    fm_cfg[client_id].currentpath = '/';
    fm_cfg[client_id].currentfiles = 0;
    // XXX: When editing existing folder, currentfiles shouldn't
    // be 0
    fm_cfg[client_id].maxfiles = options.maxfiles;
    if (options.mainfile) {
        var mainfilename = document.getElementById(options.mainfile+'-id');
        if (mainfilename.value) {
            var re = new RegExp(".*\/(.*)$", "i");
            var result = mainfilename.value.match(re);
            document.getElementById(options.mainfile+'-label').innerHTML = mainfilename.value;
            fm_cfg[client_id].mainfilename = result[1];
        } else {
            fm_cfg[client_id].mainfilename = '';
        }
    }
    var fm = new filemanager();
    fm.init(client_id, options);
    setup_buttons(client_id, options);
}

function setup_buttons(client_id, options) {
    //var fileadd = new YAHOO.widget.Button("btnadd-"+client_id);
    var fileadd = document.getElementById("btnadd-"+client_id);;
    var foldercreate = document.getElementById("btncrt-"+client_id);
    var folderdownload = document.getElementById("btndwn-"+client_id);

    var el = null;
    if (!fm_move_dlg) {
        el = document.createElement('DIV');
        el.id = 'fm-move-dlg';
        document.body.appendChild(el);
        fm_move_dlg = new YAHOO.widget.Dialog("fm-move-dlg", {
             width : "600px",
             fixedcenter : true,
             visible : false,
             constraintoviewport : true
             });
    } else {
        el = document.getElementById('fm-move-div');
    }

    el.innerHTML = '<div class="hd"></div><div class="bd"><div id="fm-move-div">'+mstr.repository.nopathselected+'</div><div id="fm-tree"></div></div>';

    fm_move_dlg.render();

    // if maxfiles == -1, the no limit
    if (fm_cfg[client_id].filecount >= fm_cfg[client_id].maxfiles && fm_cfg[client_id].maxfiles!=-1) {
        fileadd.style.display = 'none';
    } else {
        fm_cfg[client_id].currentfiles = fm_cfg[client_id].filecount;
        fileadd.onclick = function(e) {
            this.options.savepath = this.options.currentpath;
            fm_launch_filepicker(this.options.target, this.options);
        }
        fileadd.options = fm_cfg[client_id];
    }
    if (fm_cfg[client_id].subdirs) {
        foldercreate.onclick = function(e) {
            mkdir(e, this.options.client_id, this.options.itemid);
        }
        foldercreate.options = fm_cfg[client_id];
    } else {
        foldercreate.style.display = 'none';
    }
    folderdownload.onclick = function() {
        var cb = {
            success:function(o) {
                var result = json_decode(o.responseText);
                refresh_filemanager(result.filepath, fm_cfg[this.client_id]);
                var win = window.open(result.fileurl, 'fm-download-folder');
                if (!win) {
                    alert(mstr.repository.popupblockeddownload);
                }
            }
        };
        cb.client_id = this.options.client_id;
        var params = [];
        params['itemid'] = this.options.itemid;
        params['sesskey'] = moodle_cfg.sesskey;
        params['filepath'] = this.options.currentpath;
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            filemanager.url+'?action=downloaddir', cb, build_querystring(params));
    }
    folderdownload.options = fm_cfg[client_id];
}

function fm_loading(container, id) {

    if (!document.getElementById(id)) {
        var el = document.createElement('DIV');
        el.id = id;
        el.style.backgroundColor = "white";
        var container = document.getElementById(container);
        container.innerHTML = '';
        container.appendChild(el);
    }

    var loading = new YAHOO.widget.Module(id, {visible:false});
    loading.setBody('<div style="text-align:center"><img alt="'+mstr.repository.loading+'" src="'+moodle_cfg.wwwroot+'/pix/i/progressbar.gif" /></div>');
    loading.render();
    loading.show();

    return loading;
}
