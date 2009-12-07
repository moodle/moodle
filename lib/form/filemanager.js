// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains javascript code used to manage files in draft area
 *
 * @since 2.0
 * @package filemanager
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Namespaces used by filemanager
 */
YAHOO.namespace('moodle.filemanager');

// three dialog box we will used later
YAHOO.moodle.filemanager.movefile_dialog = null;
YAHOO.moodle.filemanager.rename_dialog = null;
YAHOO.moodle.filemanager.mkdir_dialog  = null;


// an object used to record filemanager instances' data,
// we use it quite often
var fm_cfg = {};
fm_cfg.api = moodle_cfg.wwwroot + '/files/files_ajax.php';

// initialize file manager
var filemanager = (function(){
    function _filemanager() {
        this.init = function(client_id, options) {
            this.client_id = client_id;

            // setup move file dialog
            var dialog = null;
            if (!YAHOO.moodle.filemanager.movefile_dialog) {
                dialog = document.createElement('DIV');
                dialog.id = 'fm-move-dlg';
                document.body.appendChild(dialog);
                YAHOO.moodle.filemanager.movefile_dialog = new YAHOO.widget.Dialog("fm-move-dlg", {
                     width : "600px",
                     fixedcenter : true,
                     visible : false,
                     constraintoviewport : true
                     });
            } else {
                dialog = document.getElementById('fm-move-div');
            }

            dialog.innerHTML = '<div class="hd"></div><div class="bd"><div id="fm-move-div">'+mstr.repository.nopathselected+'</div><div id="fm-tree"></div></div>';

            YAHOO.moodle.filemanager.movefile_dialog.render();
            // generate filemanager html
            html_compiler(client_id, options);
        }
    }
    return _filemanager;
})();


/**
 * This function will be called by filepicker once it got the file successfully
 */
function filemanager_callback(params) {
    var client_id = params.client_id;
    fm_refresh(params.filepath, fm_cfg[client_id]);
    fm_cfg[client_id].currentfiles++;
    console.info(fm_cfg[client_id].currentfiles);

    if (fm_cfg[client_id].currentfiles>=fm_cfg[client_id].maxfiles
            && fm_cfg[client_id].maxfiles!=-1) {
        var addfilebutton = document.getElementById('btnadd-'+client_id);
        if (addfilebutton) {
            addfilebutton.style.display = 'none';
        }
    }
}

/**
 * Setup options to launch file picker.
 * Fired by add file button.
 */
function fm_launch_filepicker(target, options) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+options.client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);

    var target=document.getElementById(target);
    var params = {};
    params.env = 'filemanager';
    params.itemid = options.itemid;
    params.maxfiles = options.maxfiles;
    params.maxbytes = options.maxbytes;
    params.savepath = options.savepath;
    params.target = target;
    // setup filemanager callback
    params.callback = filemanager_callback;
    var fp = open_filepicker(options.client_id, params);
    return false;
}

/**
 * This function will create a dialog of creating new folder
 * Fired by 'make a folder' button
 */
function fm_create_folder(e, client_id, itemid) {
    // deal with ajax response
    var mkdir_ajax_callback = {
        success: function(o) {
            var result = json_decode(o.responseText);
            YAHOO.moodle.filemanager.mkdir_dialog.hide();
            fm_refresh(result.filepath, fm_cfg[client_id]);
        }
    }
    // a function used to perform an ajax request
    var perform_ajax_mkdir = function(e) {
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
            fm_cfg.api+'?action=mkdir', mkdir_ajax_callback, build_querystring(params));
        YAHOO.util.Event.preventDefault(e);
    }
    // create dialog html element
    if (!document.getElementById('fm-mkdir-dlg')) {
        var el = document.createElement('DIV');
        el.id = 'fm-mkdir-dlg';
        el.innerHTML = '<div class="hd">'+mstr.repository.entername+'</div><div class="bd"><input type="text" id="fm-newname" /></div>';
        document.body.appendChild(el);
        var x = YAHOO.util.Event.getPageX(e);
        var y = YAHOO.util.Event.getPageY(e);
        YAHOO.moodle.filemanager.mkdir_dialog = new YAHOO.widget.Dialog("fm-mkdir-dlg", {
             width: "300px",
             visible: true,
             x:y,
             y:y,
             constraintoviewport : true
             });

    }
    var buttons = [ { text:mstr.moodle.ok, handler:perform_ajax_mkdir, isDefault:true },
                      { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

    YAHOO.moodle.filemanager.mkdir_dialog.cfg.queueProperty("buttons", buttons);
    YAHOO.moodle.filemanager.mkdir_dialog.render();
    YAHOO.moodle.filemanager.mkdir_dialog.show();

    // presss 'enter' key to perform ajax request
    var k1 = new YAHOO.util.KeyListener(document.getElementById('fm-mkdir-dlg'), {keys:13}, {fn:function(){perform_ajax_mkdir();}, correctScope: true});
    k1.enable();

    document.getElementById('fm-newname').value = '';
}

// generate html
function html_compiler(client_id, options) {
    var list = options.list;
    var breadcrumb = document.getElementById('fm-path-'+client_id);
    var count = 0;
    // build breadcrumb
    if (options.path) {
        // empty breadcrumb
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

            var pathnode = document.createElement('A');
            pathnode.id = pathid;
            pathnode.innerHTML = options.path[p].name;
            pathnode.href = '###';
            breadcrumb.appendChild(sep);
            breadcrumb.appendChild(pathnode);

            var args = {};
            args.itemid = options.itemid;
            args.requestpath = options.path[p].path;
            args.client_id = client_id;

            YAHOO.util.Event.addListener(pathid, 'click', fm_click_breadcrumb, args);
        }
    }
    var template = document.getElementById('fm-template');
    var container = document.getElementById('filemanager-' + client_id);
    var listhtml = '';

    // folder list items
    var folder_ids = [];
    var folder_data = {};

    // normal file list items
    var file_ids   = [];
    var file_data  = {};

    // archives list items
    var zip_ids    = [];
    var zip_data = {};

    var html_ids = [];
    var html_data = {};

    file_data.itemid = folder_data.itemid = zip_data.itemid = options.itemid;
    file_data.client_id = folder_data.client_id = zip_data.client_id = options.client_id;

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

    var count = 0;
    for(var i in list) {
        count++;
        // the li html element
        var htmlid = 'fileitem-'+client_id+'-'+count;
        // link to file
        var fileid = 'filename-'+client_id+'-'+count;
        // file menu
        var action = 'action-'  +client_id+'-'+count;

        var html = template.innerHTML;

        html_ids.push(htmlid);
        html_data[htmlid] = action;

        list[i].htmlid = htmlid;
        list[i].fileid = fileid;
        list[i].action = action;

        var url = "###";
        // check main file
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

        // add green tick to main file
        if (ismainfile) {
            fullname = "<strong>"+list[i].fullname+"</strong> <img src='"+moodle_cfg.wwwroot+"/pix/i/tick_green_small.gif"+"' />";
        }

        html = html.replace('___fullname___', '<a href="'+url+'" id="'+fileid+'"><img src="'+list[i].icon+'" /> ' + fullname + '</a>');
        html = html.replace('___action___', '<a style="display:none" href="###" id="'+action+'"><img alt="▶" src="'+moodle_cfg.wwwroot+'/pix/i/settings.gif'+'" /></a>');
        html = '<li id="'+htmlid+'">'+html+'</li>';
        listhtml += html;
    }

    container.innerHTML = '<ul id="draftfiles-'+client_id+'">' + listhtml + '</ul>';

    // click normal file menu
    YAHOO.util.Event.addListener(file_ids,   'click', fm_create_filemenu, file_data);
    // click folder menu
    YAHOO.util.Event.addListener(folder_ids, 'click', fm_create_foldermenu, folder_data);
    // click archievs menu
    YAHOO.util.Event.addListener(zip_ids,    'click', fm_create_zipmenu, zip_data);
    // when mouse moveover every menu
    YAHOO.util.Event.addListener(html_ids,   'mouseover', fm_mouseover_menu, html_data);
    YAHOO.util.Event.addListener(html_ids,   'mouseout', fm_mouseout_menu, html_data);
    // click folder name
    YAHOO.util.Event.addListener(foldername_ids,'click', fm_click_folder, folder_data);
}

function fm_refresh(path, args) {
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
        fm_cfg.api+'?action=list', this.cb, build_querystring(params));
}

// display menu when mouse over
function fm_mouseover_menu(ev, args) {
    this.style.backgroundColor = '#0066EE';
    var menu = args[this.id];
    menu = document.getElementById(menu);
    menu.style.display = 'inline';
}

// hide menu when mouse over
function fm_mouseout_menu(ev, args) {
    this.style.backgroundColor = 'transparent';
    var menu = args[this.id];
    menu = document.getElementById(menu);
    menu.style.display = 'none';
}

function fm_click_breadcrumb(ev, args) {
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
        fm_cfg.api+'?action=list', this.cb, build_querystring(params));
}

function fm_click_folder(ev, args) {
    var file = args[this.id];
    fm_refresh(file.filepath, args);
}

function fm_create_foldermenu(e, data) {
    var file = data[this.id];
    // an extra menu item for folder to zip it
    this.zip = function(type, ev, obj) {
        this.cb = {
            success: function(o) {
                 var result = json_decode(o.responseText);
                 if (result) {
                     fm_refresh(result.filepath, fm_cfg[this.client_id]);
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
            fm_cfg.api+'?action=zip', this.cb, build_querystring(params));
    }
    this.zip.file = file;
    var menuitems = [
        {text: mstr.editor.zip, onclick: {fn: this.zip, obj: data, scope: this.zip}},
        ];
    fm_create_menu(e, 'foldermenu', menuitems, file, data);
}

function fm_create_filemenu(e, data) {
    var file = data[this.id];

    var menuitems = [
        {text: mstr.moodle.download, url:file.url}
        ];
    fm_create_menu(e, 'filemenu', menuitems, file, data);
}

function fm_create_zipmenu(e, data) {
    var file = data[this.id];
    this.unzip = function(type, ev, obj) {
        this.cb = {
            success:function(o) {
                var result = json_decode(o.responseText);
                if (result) {
                    fm_refresh(result.filepath, fm_cfg[this.client_id]);
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
            fm_cfg.api+'?action=unzip', this.cb, build_querystring(params));
    }
    this.unzip.file = file;

    var menuitems = [
        {text: mstr.moodle.download, url:file.url},
        {text: mstr.moodle.unzip, onclick: {fn: this.unzip, obj: data, scope: this.unzip}}
        ];
    fm_create_menu(e, 'zipmenu', menuitems, file, data);
}

function fm_create_menu(ev, menuid, menuitems, file, options) {
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
                fm_cfg.api+'?action=delete', this.cb, build_querystring(params));
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
                console.info('delete callback: '+fm_cfg[this.client_id].currentfiles);
                if (fm_cfg[this.client_id].currentfiles<fm_cfg[this.client_id].maxfiles) {
                    var btn = document.getElementById('btnadd-'+this.client_id);
                    btn.style.display = 'inline';
                    btn.onclick = function(e) {
                        this.options.savepath = this.options.currentpath;
                        fm_launch_filepicker(this.options.target, this.options);
                    }
                    btn.options = fm_cfg[this.client_id];
                }
                fm_refresh(result.filepath, fm_cfg[this.client_id]);
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
                    YAHOO.moodle.filemanager.rename_dialog.hide();
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
                fm_cfg.api+'?action='+action, rename_cb, build_querystring(params));
        }

        var scope = document.getElementById('fm-rename-dlg');
        if (!scope) {
            var el = document.createElement('DIV');
            el.id = 'fm-rename-dlg';
            el.innerHTML = '<div class="hd">'+mstr.repository.enternewname+'</div><div class="bd"><input type="text" id="fm-rename-input" /></div>';
            document.body.appendChild(el);
            YAHOO.moodle.filemanager.rename_dialog = new YAHOO.widget.Dialog("fm-rename-dlg", {
                 width: "300px",
                 fixedcenter: true,
                 visible: true,
                 constraintoviewport : true
                 });

        }
        var buttons = [ { text:mstr.moodle.rename, handler:perform, isDefault:true },
                          { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

        YAHOO.moodle.filemanager.rename_dialog.cfg.queueProperty("buttons", buttons);
        YAHOO.moodle.filemanager.rename_dialog.render();
        YAHOO.moodle.filemanager.rename_dialog.show();

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
                    fm_refresh(result.filepath, fm_cfg[obj.client_id]);
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
                fm_cfg.api+'?action='+action, cb, build_querystring(params));
        }

        var buttons = [ { text:mstr.moodle.move, handler:this.asyncMove, isDefault:true },
                          { text:mstr.moodle.cancel, handler:function(){this.cancel();}}];

        YAHOO.moodle.filemanager.movefile_dialog.cfg.queueProperty("buttons", buttons);


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
                fm_cfg.api+'?action=dir', this.cb, build_querystring(params));
            this.cb.complete = onCompleteCallback;
            this.cb.itemid = node.data.itemid;
        }
        this.loadDataForNode.itemid = obj.itemid;

        YAHOO.moodle.filemanager.movefile_dialog.subscribe('show', function(){

            var el = document.getElementById('fm-move-div');
            el.innerHTML = '<div class="hd"></div><div class="bd"><div id="fm-move-div">'+mstr.repository.nopathselected+'</div><div id="fm-tree"></div></div>';

            var rootNode = tree.getRoot();
            tree.setDynamicLoad(this.loadDataForNode);
            tree.removeChildren(rootNode);
            var textnode = {label: "Files", path: '/', itemid: obj.itemid};
            var tmpNode = new YAHOO.widget.TextNode(textnode, rootNode, true);
            tree.draw();

        }, this, true);

        YAHOO.moodle.filemanager.movefile_dialog.render();
        YAHOO.moodle.filemanager.movefile_dialog.show();
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
            fm_refresh(fm_cfg[obj.client_id].currentpath, fm_cfg[obj.client_id]);

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

/**
 * setup file manager options and initialize filemanger itself
 */
function launch_filemanager(client_id, options) {
    // setup options & parameters
    if (!options) {
        options = {};
    }
    fm_cfg[client_id] = {};
    fm_cfg[client_id] = options;
    fm_cfg[client_id].currentpath = '/';
    if (fm_cfg[client_id].filecount) {
        fm_cfg[client_id].currentfiles = fm_cfg[client_id].filecount;
    } else {
        fm_cfg[client_id].currentfiles = 0;
    }
    //
    console.info(fm_cfg[client_id].currentfiles);
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
    // setup filemanager
    var fm = new filemanager();
    fm.init(client_id, options);
    // setup toolbar
    fm_setup_buttons(client_id, options);
}

/**
 * Set up buttons
 */
function fm_setup_buttons(client_id, options) {
    var button_download = document.getElementById("btndwn-"+client_id);
    var button_create   = document.getElementById("btncrt-"+client_id);
    var button_addfile  = document.getElementById("btnadd-"+client_id);

    // setup 'add file' button
    // if maxfiles == -1, the no limit
    if (fm_cfg[client_id].filecount >= fm_cfg[client_id].maxfiles
            && fm_cfg[client_id].maxfiles!=-1) {
        button_addfile.style.display = 'none';
    } else {
        button_addfile.onclick = function(e) {
            this.options.savepath = this.options.currentpath;
            fm_launch_filepicker(this.options.target, this.options);
        }
        button_addfile.options = fm_cfg[client_id];
    }

    // setup 'make a folder' button
    if (fm_cfg[client_id].subdirs) {
        button_create.onclick = function(e) {
            fm_create_folder(e, this.options.client_id, this.options.itemid);
        }
        button_create.options = fm_cfg[client_id];
    } else {
        button_create.style.display = 'none';
    }

    // setup 'download this folder' button
    // NOTE: popup window must be enabled to perform download process
    button_download.onclick = function() {
        var downloaddir_callback = {
            success:function(o) {
                var result = json_decode(o.responseText);
                fm_refresh(result.filepath, fm_cfg[this.client_id]);
                var win = window.open(result.fileurl, 'fm-download-folder');
                if (!win) {
                    alert(mstr.repository.popupblockeddownload);
                }
            }
        };
        downloaddir_callback.client_id = this.options.client_id;
        var params = [];
        params['itemid'] = this.options.itemid;
        params['sesskey'] = moodle_cfg.sesskey;
        params['filepath'] = this.options.currentpath;
        // perform downloaddir ajax request
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            fm_cfg.api+'?action=downloaddir', downloaddir_callback, build_querystring(params));
    }
    button_download.options = fm_cfg[client_id];
}

/**
 * Print a progress bar
 */
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
