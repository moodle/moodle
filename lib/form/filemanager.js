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
 *
 * File Manager UI
 * =====
 * this.api, stores the URL to make ajax request
 * this.currentpath
 * this.filepicker_options
 * this.movefile_dialog
 * this.mkdir_dialog
 * this.rename_dialog
 * this.client_id
 * this.filecount, how many files in this filemanager
 * this.maxfiles
 * this.maxbytes
 *
 * FileManager options:
 * =====
 * this.options.currentpath
 * this.options.itemid
 */


M.form_filemanager = {};

/**
 * This fucntion is called for each file picker on page.
 */
M.form_filemanager.init = function(Y, options) {
    var FileManagerHelper = function(options) {
        FileManagerHelper.superclass.constructor.apply(this, arguments);
    }
    FileManagerHelper.NAME = "FileManager";
    FileManagerHelper.ATTRS = {
        options: {},
        lang: {}
    };

    Y.extend(FileManagerHelper, Y.Base, {
        api: M.cfg.wwwroot+'/files/files_ajax.php',
        menus: {},
        initializer: function(options) {
            this.options = options;
            if (options.mainfile) {
                this.mainfile = options.mainfile;
            }
            this.client_id = options.client_id;
            this.currentpath = '/';
            this.maxfiles = options.maxfiles;
            this.maxbytes = options.maxbytes;

            this.filepicker_options = options.filepicker?options.filepicker:{};
            this.filepicker_options.client_id = this.client_id;
            this.filepicker_options.maxfiles = this.maxfiles;
            this.filepicker_options.maxbytes = this.maxbytes;
            this.filepicker_options.env = 'filemanager';
            this.filepicker_options.filearea = options.filearea;
            this.filepicker_options.itemid = options.itemid;

            this.filearea = options.filearea?options.filearea:'user_draft';
            if (options.filecount) {
                this.filecount = options.filecount;
            } else {
                this.filecount = 0;
            }
            this.setup_buttons();
            this.render();
        },

        request: function(args) {
            var api = this.api + '?action='+args.action;
            var params = {};
            var scope = this;
            if (args['scope']) {
                scope = args['scope'];
            }
            params['sesskey'] = M.cfg.sesskey;
            params['filearea'] = this.filearea;
            params['client_id'] = this.client_id;
            params['filepath'] = this.currentpath;
            params['itemid'] = this.options.itemid?this.options.itemid:0;
            if (args['params']) {
                for (i in args['params']) {
                    params[i] = args['params'][i];
                }
            }
            var cfg = {
                method: 'POST',
                on: {
                    complete: function(id,o,p) {
                        if (!o) {
                            alert('IO FATAL');
                            return;
                        }
                        var data = Y.JSON.parse(o.responseText);
                        args.callback(id,data,p);
                    }
                },
                arguments: {
                    scope: scope
                },
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'User-Agent': 'MoodleFileManager/3.0'
                },
                data: build_querystring(params)
            };
            if (args.form) {
                cfg.form = args.form;
            }
            Y.io(api, cfg);
        },
        filepicker_callback: function(obj) {
            var button_addfile  = Y.one("#btnadd-"+this.client_id);
            this.filecount++;
            if (this.filecount > 0) {
                Y.one("#btndwn-"+this.client_id).setStyle('display', 'inline');
            }
            if (this.filecount >= this.maxfiles && this.maxfiles!=-1) {
                button_addfile.setStyle('display', 'none');
            }
            this.refresh(this.currentpath);
        },
        refresh: function(filepath) {
            var scope = this;
            this.currentpath = filepath;
            if (!filepath) {
                filepath = this.currentpath;
            } else {
                this.currentpath = filepath;
            }
            this.request({
                action: 'list',
                scope: scope,
                params: {'filepath':filepath},
                callback: function(id, obj, args) {
                    scope.options = obj;
                    scope.render(obj);
                }
            });
        },
        setup_buttons: function() {
            var button_download = Y.one("#btndwn-"+this.client_id);
            var button_create   = Y.one("#btncrt-"+this.client_id);
            var button_addfile  = Y.one("#btnadd-"+this.client_id);

            // setup 'add file' button
            // if maxfiles == -1, the no limit
            if (this.filecount >= this.maxfiles
                    && this.maxfiles!=-1) {
                button_addfile.setStyle('display', 'none');
            } else {
                button_addfile.on('click', function(e) {
                    var options = this.filepicker_options;
                    options.formcallback = this.filepicker_callback;
                    // XXX: magic here, to let filepicker use filemanager scope
                    options.magicscope = this;
                    options.savepath = this.currentpath;
                    M.core_filepicker.show(Y, options);
                }, this);
            }

            // setup 'make a folder' button
            if (this.options.subdirs) {
                button_create.on('click',function(e) {
                    var scope = this;
                    // a function used to perform an ajax request
                    function perform_action(e) {
                        var foldername = Y.one('#fm-newname').get('value');
                        if (!foldername) {
                            return;
                        }
                        scope.request({
                            action:'mkdir',
                            params: {filepath:scope.currentpath, newdirname:foldername},
                            callback: function(id, obj, args) {
                                var filepath = obj.filepath;
                                scope.mkdir_dialog.hide();
                                scope.refresh(filepath);
                                Y.one('#fm-newname').set('value', '');
                            }
                        });
                    }
                    if (!Y.one('#fm-mkdir-dlg')) {
                        var dialog = Y.Node.create('<div id="fm-mkdir-dlg"><div class="hd">'+M.str.repository.entername+'</div><div class="bd"><input type="text" id="fm-newname" /></div></div>');
                        Y.one(document.body).appendChild(dialog);
                        this.mkdir_dialog = new YAHOO.widget.Dialog("fm-mkdir-dlg", {
                             width: "300px",
                             visible: true,
                             x:e.pageX,
                             y:e.pageY,
                             constraintoviewport : true
                             });

                    }
                    var buttons = [ { text:M.str.moodle.ok, handler:perform_action, isDefault:true },
                                  { text:M.str.moodle.cancel, handler:function(){this.cancel();}}];

                    this.mkdir_dialog.cfg.queueProperty("buttons", buttons);
                    this.mkdir_dialog.render();
                    this.mkdir_dialog.show();
                }, this);
            } else {
                button_create.setStyle('display', 'none');
            }

            // setup 'download this folder' button
            // NOTE: popup window must be enabled to perform download process
            button_download.on('click',function() {
                var scope = this;
                // perform downloaddir ajax request
                this.request({
                    action: 'downloaddir',
                    scope: scope,
                    callback: function(id, obj, args) {
                        if (obj) {
                            scope.refresh(obj.filepath);
                            var win = window.open(obj.fileurl, 'fm-download-folder');
                            if (!win) {
                                alert(M.str.repository.popupblockeddownload);
                            }
                        } else {
                            alert(M.str.repository.draftareanofiles);
                        }
                    }
                });
            }, this);
        },
        render: function() {
            var options = this.options;
            var path = this.options.path;
            var list = this.options.list;
            var breadcrumb = Y.one('#fm-path-'+this.client_id);
            // build breadcrumb
            if (path) {
                // empty breadcrumb
                breadcrumb.set('innerHTML', '');
                var count = 0;
                for(var p in path) {
                    var arrow = '';
                    if (count==0) {
                        arrow = Y.Node.create('<span>'+M.str.moodle.path + ': </span>');
                    } else {
                        arrow = Y.Node.create('<span> ▶ </span>');
                    }
                    count++;

                    var pathid  = 'fm-path-node-'+this.client_id;
                    pathid += ('-'+count);

                    var crumb = Y.Node.create('<a href="###" id="'+pathid+'">'+path[p].name+'</a>');
                    breadcrumb.appendChild(arrow);
                    breadcrumb.appendChild(crumb);

                    var args = {};
                    args.requestpath = path[p].path;
                    args.client_id = this.client_id;
                    Y.one('#'+pathid).on('click', function(e, args) {
                        var scope = this;
                        var params = {};
                        params['filepath'] = args.requestpath;
                        this.currentpath = args.requestpath;
                        this.request({
                            action: 'list',
                            scope: scope,
                            params: params,
                            callback: function(id, obj, args) {
                                scope.options = obj;
                                scope.render(obj);
                            }
                        });
                    }, this, args);
                }
            }
            var template = Y.one('#fm-template');
            var container = Y.one('#filemanager-' + this.client_id);
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
            file_data.client_id = folder_data.client_id = zip_data.client_id = this.client_id;

            var foldername_ids = [];
            if (!list || list.length == 0) {
                // hide file browser and breadcrumb
                container.setStyle('display', 'none');
                if (!path || path.length <= 1) {
                    breadcrumb.setStyle('display', 'none');
                }
                return;
            } else {
                container.setStyle('display', 'block');
                breadcrumb.setStyle('display', 'block');
            }

            var count = 0;
            for(var i in list) {
                count++;
                // the li html element
                var htmlid = 'fileitem-'+this.client_id+'-'+count;
                // link to file
                var fileid = 'filename-'+this.client_id+'-'+count;
                // file menu
                var action = 'action-'  +this.client_id+'-'+count;

                var html = template.get('innerHTML');

                html_ids.push('#'+htmlid);
                html_data[htmlid] = action;

                list[i].htmlid = htmlid;
                list[i].fileid = fileid;
                list[i].action = action;

                var url = "###";
                // check main file
                var ismainfile = false;
                //if (fm_cfg[this.client_id].mainfilename && (fm_cfg[this.client_id].mainfilename.toLowerCase() == list[i].fullname.toLowerCase())) {
                    //ismainfile = true;
                //}

                switch (list[i].type) {
                    case 'folder':
                        // click folder name
                        foldername_ids.push('#'+fileid);
                        // click folder menu
                        folder_ids.push('#'+action);
                        folder_data[action] = list[i];
                        folder_data[fileid] = list[i];
                        break;
                    case 'file':
                        file_ids.push('#'+action);
                        file_data[action] = list[i];
                        if (list[i].url) {
                            url = list[i].url;
                        }
                    break;
                    case 'zip':
                        zip_ids.push('#'+action);
                        zip_data[action] = list[i];
                        if (list[i].url) {
                            url = list[i].url;
                        }
                    break;
                }
                var fullname = list[i].fullname;

                // add green tick to main file
                //if (ismainfile) {
                    //fullname = "<strong>"+list[i].fullname+"</strong> <img src='"+M.cfg.wwwroot+"/pix/i/tick_green_small.gif"+"' />";
                //}

                html = html.replace('___fullname___', '<a href="'+url+'" id="'+fileid+'"><img src="'+list[i].icon+'" /> ' + fullname + '</a>');
                html = html.replace('___action___', '<a style="display:none" href="###" id="'+action+'"><img alt="▶" src="'+M.cfg.wwwroot+'/pix/i/settings.gif'+'" /></a>');
                html = '<li id="'+htmlid+'">'+html+'</li>';
                listhtml += html;
            }
            if (!Y.one('#draftfiles-'+this.client_id)) {
                var filelist = Y.Node.create('<ul id="draftfiles-'+this.client_id+'"></ul>');
                container.appendChild(filelist);
            }
            Y.one('#draftfiles-'+this.client_id).set('innerHTML', listhtml);

            // click normal file menu
            Y.on('click', this.create_filemenu,   file_ids, this, file_data);
            // click folder menu
            Y.on('click', this.create_foldermenu, folder_ids, this, folder_data);
            // click archievs menu
            Y.on('click', this.create_zipmenu,    zip_ids, this, zip_data);
            // when mouse moveover every menu
            function mouseover_menu(e, obj) {
                var node = e.currentTarget;
                node.setStyle('backgroundColor', '#0066EE');
                var menu = Y.one('#'+obj[node.get('id')]);
                menu.setStyle('display', 'inline');
            }
            function mouseout_menu(e, obj) {
                var node = e.currentTarget;
                node.setStyle('backgroundColor', 'transparent');
                var menu = Y.one('#'+obj[node.get('id')]);
                menu.setStyle('display', 'none');
            }
            Y.on('mouseover', mouseover_menu, html_ids, this, html_data);
            Y.on('mouseout', mouseout_menu, html_ids, this, html_data);
            // click folder name
            Y.on('click', this.enter_folder, foldername_ids, this, folder_data);
        },
        enter_folder: function(e, data) {
            var node = e.currentTarget;
            var file = data[node.get('id')];
            this.refresh(file.filepath);
        },
        create_filemenu: function(e, data) {
            var options = this.options;
            var node = e.currentTarget;
            var file = data[node.get('id')];

            var menuitems = [
                {text: M.str.moodle.download, url:file.url}
                ];
            function setmainfile(type, ev, obj) {
                var file = obj[node.get('id')];
                Y.one(mainid).set('value', file.filepath+file.filename);
            }
            if (this.mainfile) {
                var mainid = '#id_'+this.mainfile;
                var menu = {text: M.str.repository.setmainfile, onclick:{fn: setmainfile, obj:data, scope:this}};
                menuitems.push(menu);
            }
            this.create_menu(e, 'filemenu', menuitems, file, data);
        },
        create_foldermenu: function(e, data) {
            var scope = this;
            var node = e.currentTarget;
            var fileinfo = data[node.get('id')];
            // an extra menu item for folder to zip it
            function archive_folder(type,ev,obj) {
                var params = {};
                params['filepath']   = fileinfo.filepath;
                params['filename']   = '.';
                this.request({
                    action: 'zip',
                    scope: scope,
                    params: params,
                    callback: function(id, obj, args) {
                        scope.refresh(obj.filepath);
                    }
                });
            }
            var menuitems = [
                {text: M.str.editor.zip, onclick: {fn: archive_folder, obj: data, scope: this}},
                ];
            this.create_menu(e, 'foldermenu', menuitems, fileinfo, data);
        },
        create_zipmenu: function(e, data) {
            var scope = this;
            var node = e.currentTarget;
            var fileinfo = data[node.get('id')];

            function unzip(type, ev, obj) {
                var params = {};
                params['filepath'] = fileinfo.filepath;
                params['filename'] = fileinfo.fullname;
                this.request({
                    action: 'unzip',
                    scope: scope,
                    params: params,
                    callback: function(id, obj, args) {
                        scope.refresh(obj.filepath);
                    }
                });
            }
            var menuitems = [
                {text: M.str.moodle.download, url:fileinfo.url},
                {text: M.str.moodle.unzip, onclick: {fn: unzip, obj: data, scope: this}}
                ];
            this.create_menu(e, 'zipmenu', menuitems, fileinfo, data);
        },
        create_menu: function(ev, menuid, menuitems, fileinfo, options) {
            var position = [ev.pageX, ev.pageY];
            var scope = this;
            function remove(type, ev, obj) {
                var dialog_options = {};
                var params = {};
                dialog_options.message = M.str.repository.confirmdeletefile;
                dialog_options.scope = this;
                var filename = '';
                var filepath = '';
                if (fileinfo.type == 'folder') {
                    params.filename = '.';
                    params.filepath = fileinfo.fullname;
                } else {
                    params.filename = fileinfo.fullname;
                }
                dialog_options.callbackargs = [params];
                dialog_options.callback = function(params) {
                    this.request({
                        action: 'delete',
                        scope: this,
                        params: params,
                        callback: function(id, obj, args) {
                            scope.filecount--;
                            scope.refresh(obj.filepath);
                            if (scope.filecount < scope.maxfiles && scope.maxfiles!=-1) {
                                var button_addfile  = Y.one("#btnadd-"+scope.client_id);
                                button_addfile.setStyle('display', 'inline');
                                button_addfile.on('click', function(e) {
                                    var options = scope.filepicker_options;
                                    options.formcallback = scope.filepicker_callback;
                                    // XXX: magic here, to let filepicker use filemanager scope
                                    options.magicscope = scope;
                                    options.savepath = scope.currentpath;
                                    M.core_filepicker.show(Y, options);
                                }, this);
                            }
                        }
                    });
                }
                M.util.show_confirm_dialog(ev, dialog_options);
            }
            function rename (type, ev, obj) {
                var scope = this;
                var perform = function(e) {
                    var newfilename = Y.one('#fm-rename-input').get('value');
                    if (!newfilename) {
                        return;
                    }

                    var action = '';
                    var params = {};
                    if (fileinfo.type == 'folder') {
                        params['filepath']   = fileinfo.filepath;
                        params['filename']   = '.';
                        action = 'renamedir';
                    } else {
                        params['filepath']   = fileinfo.filepath;
                        params['filename']   = fileinfo.fullname;
                        action = 'rename';
                    }
                    params['newfilename'] = newfilename;
                    scope.request({
                        action: action,
                        scope: scope,
                        params: params,
                        callback: function(id, obj, args) {
                            scope.refresh(obj.filepath);
                            Y.one('#fm-rename-input').set('value', '');
                            scope.rename_dialog.hide();
                        }
                    });
                }

                var dialog = Y.one('#fm-rename-dlg');
                if (!dialog) {
                    dialog = Y.Node.create('<div id="fm-rename-dlg"><div class="hd">'+M.str.repository.enternewname+'</div><div class="bd"><input type="text" id="fm-rename-input" /></div></div>');
                    Y.one(document.body).appendChild(dialog);
                    this.rename_dialog = new YAHOO.widget.Dialog("fm-rename-dlg", {
                         width: "300px",
                         fixedcenter: true,
                         visible: true,
                         constraintoviewport : true
                         });

                }
                var buttons = [ { text:M.str.moodle.rename, handler:perform, isDefault:true},
                                  { text:M.str.moodle.cancel, handler:function(){this.cancel();}}];

                this.rename_dialog.cfg.queueProperty('buttons', buttons);
                this.rename_dialog.render();
                this.rename_dialog.show();
                //var k1 = new YAHOO.util.KeyListener(scope, {keys:13}, {fn:function(){perform();}, correctScope: true});
                //k1.enable();
                Y.one('#fm-rename-input').set('value', fileinfo.fullname);
            }
            function move(type, ev, obj) {
                var scope = this;
                var itemid = this.options.itemid;
                // setup move file dialog
                var dialog = null;
                if (!Y.one('#fm-move-dlg')) {
                    dialog = Y.Node.create('<div id="fm-move-dlg"></div>');
                    Y.one(document.body).appendChild(dialog);
                } else {
                    dialog = Y.one('#fm-move-dlg');
                }

                dialog.set('innerHTML', '<div class="hd">Moving</div><div class="bd"><div id="fm-move-div">'+M.str.repository.nopathselected+'</div><div id="fm-tree"></div></div>');

                this.movefile_dialog = new YAHOO.widget.Dialog("fm-move-dlg", {
                     width : "600px",
                     fixedcenter : true,
                     visible : false,
                     constraintoviewport : true
                     });

                var treeview = new YAHOO.widget.TreeView("fm-tree");

                var dialog = this.movefile_dialog;
                function _move(e) {
                    if (!treeview.targetpath) {
                        return;
                    }
                    var params = {};
                    if (fileinfo.type == 'folder') {
                        alert('Moving folder is not supported yet');
                        action = 'movedir';
                        return;
                    } else {
                        action = 'movefile';
                    }
                    params['filepath'] = fileinfo.filepath;
                    params['filename'] = fileinfo.fullname;
                    params['newfilepath'] = treeview.targetpath;
                    scope.request({
                        action: action,
                        scope: scope,
                        params: params,
                        callback: function(id, obj, args) {
                            var p = '/';
                            if (obj) {
                                p = obj.filepath;
                            }
                            dialog.cancel();
                            scope.refresh(p);
                        }
                    });
                }

                var buttons = [ { text:M.str.moodle.move, handler:_move, isDefault:true },
                                  { text:M.str.moodle.cancel, handler:function(){this.cancel();}}];

                this.movefile_dialog.cfg.queueProperty("buttons", buttons);
                this.movefile_dialog.render();

                treeview.subscribe("dblClickEvent", function(e) {
                    // update destidatoin folder
                    this.targetpath = e.node.data.path;
                    var title = Y.one('#fm-move-div');
                    title.set('innerHTML', '<strong>"' + this.targetpath + '"</strong> has been selected.');
                });

                function loadDataForNode(node, onCompleteCallback) {
                    var params = {};
                    params['filepath'] = node.data.path;
                    var obj = {
                        action: 'dir',
                        scope: scope,
                        params: params,
                        callback: function(id, obj, args) {
                            data = obj.children;
                            if (data.length == 0) {
                                // so it is empty
                            } else {
                                for (var i in data) {
                                    var textnode = {label: data[i].fullname, path: data[i].filepath, itemid: this.itemid};
                                    var tmpNode = new YAHOO.widget.TextNode(textnode, node, false);
                                }
                            }
                            this.oncomplete();
                        }
                    };
                    obj.oncomplete = onCompleteCallback;
                    scope.request(obj);
                }

                this.movefile_dialog.subscribe('show', function(){
                    var rootNode = treeview.getRoot();
                    treeview.setDynamicLoad(loadDataForNode);
                    treeview.removeChildren(rootNode);
                    var textnode = {label: "Files", path: '/'};
                    var tmpNode = new YAHOO.widget.TextNode(textnode, rootNode, true);
                    treeview.draw();
                }, this, true);

                this.movefile_dialog.show();
            }
            if (fileinfo.type!='folder') {
                var shared_items = [
                    {text: M.str.moodle.rename+'...', onclick: {fn: rename, obj: options, scope: this}},
                    {text: M.str.moodle.move+'...', onclick: {fn: move, obj: options, scope: this}}
                ];
            } else {
                var shared_items = [];
            }
            // delete is reserve word in Javascript
            shared_items.push({text: M.str.moodle['delete']+'...', onclick: {fn: remove, obj: options, scope: this}});
            var menu = new YAHOO.widget.Menu(menuid, {xy:position, clicktohide:true});
            menu.clearContent();
            menu.addItems(menuitems);
            menu.addItems(shared_items);
            menu.render(document.body);
            menu.subscribe('hide', function(){
                this.fireEvent('destroy');
            });
            menu.show();
        }
    });

    // finally init everything needed

    var item = document.getElementById('nonjs-filemanager-'+options.client_id);
    if (item) {
        item.parentNode.removeChild(item);
    }
    item = document.getElementById('filemanager-wrapper-'+options.client_id);
    if (item) {
        item.style.display = '';
    }

    new FileManagerHelper(options);
};
