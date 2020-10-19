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
 * this.areamaxbytes, the maximum size of the area
 * this.filemanager, contains reference to filemanager Node
 * this.selectnode, contains referenct to select-file Node
 * this.selectui, M.core.dialogue to select the file
 * this.viewmode, contains current view mode - icons, tree or details
 *
 * FileManager options:
 * =====
 * this.options.currentpath
 * this.options.itemid
 */


M.form_filemanager = {templates:{}};

M.form_filemanager.set_templates = function(Y, templates) {
    M.form_filemanager.templates = templates;
}

/**
 * This fucntion is called for each file picker on page.
 */
M.form_filemanager.init = function(Y, options) {
    var FileManagerHelper = function(options) {
        FileManagerHelper.superclass.constructor.apply(this, arguments);
    };
    FileManagerHelper.NAME = "FileManager";
    FileManagerHelper.ATTRS = {
        options: {},
        lang: {}
    };

    Y.extend(FileManagerHelper, Y.Base, {
        api: M.cfg.wwwroot+'/repository/draftfiles_ajax.php',
        menus: {},
        initializer: function(options) {
            this.options = options;
            if (options.mainfile) {
                this.enablemainfile = options.mainfile;
            }
            this.client_id = options.client_id;
            this.currentpath = '/';
            this.maxfiles = options.maxfiles;
            this.maxbytes = options.maxbytes;
            this.areamaxbytes = options.areamaxbytes;
            this.userprefs = options.userprefs;
            this.emptycallback = null; // Used by drag and drop upload

            this.filepicker_options = options.filepicker?options.filepicker:{};
            this.filepicker_options.client_id = this.client_id;
            this.filepicker_options.context = options.context;
            this.filepicker_options.maxfiles = this.maxfiles;
            this.filepicker_options.maxbytes = this.maxbytes;
            this.filepicker_options.areamaxbytes = this.areamaxbytes;
            this.filepicker_options.env = 'filemanager';
            this.filepicker_options.itemid = options.itemid;

            if (options.filecount) {
                this.filecount = options.filecount;
            } else {
                this.filecount = 0;
            }
            // prepare filemanager for drag-and-drop upload
            this.filemanager = Y.one('#filemanager-'+options.client_id);
            if (this.filemanager.hasClass('filemanager-container') || !this.filemanager.one('.filemanager-container')) {
                this.dndcontainer = this.filemanager;
            } else  {
                this.dndcontainer = this.filemanager.one('.filemanager-container');
                if (!this.dndcontainer.get('id')) {
                    this.dndcontainer.generateID();
                }
            }
            // save template for one path element and location of path bar
            if (this.filemanager.one('.fp-path-folder')) {
                this.pathnode = this.filemanager.one('.fp-path-folder');
                this.pathbar = this.pathnode.get('parentNode');
                this.pathbar.removeChild(this.pathnode);
            }
            // initialize 'select file' panel
            this.selectnode = Y.Node.create(M.form_filemanager.templates.fileselectlayout);
            this.selectnode.setAttribute('aria-live', 'assertive');
            this.selectnode.setAttribute('role', 'dialog');
            this.selectnode.generateID();

            var labelid = 'fm-dialog-label_'+ this.selectnode.get('id');
            this.selectui = new M.core.dialogue({
                draggable    : true,
                headerContent: '<h3 id="' + labelid +'">' + M.util.get_string('edit', 'moodle') + '</h3>',
                bodyContent  : this.selectnode,
                centered     : true,
                width        : '480px',
                modal        : true,
                visible      : false
            });
            Y.one('#'+this.selectnode.get('id')).setAttribute('aria-labelledby', labelid);
            this.selectui.hide();
            this.setup_select_file();
            // setup buttons onclick events
            this.setup_buttons();
            // set event handler for lazy loading of thumbnails
            this.filemanager.one('.fp-content').on(['scroll','resize'], this.content_scrolled, this);
            // display files
            this.viewmode = this.get_preference("recentviewmode");
            if (this.viewmode != 2 && this.viewmode != 3) {
                this.viewmode = 1;
            }
            var viewmodeselectors = {'1': '.fp-vb-icons', '2': '.fp-vb-tree', '3': '.fp-vb-details'};
            this.filemanager.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').removeClass('checked');
            this.filemanager.all(viewmodeselectors[this.viewmode]).addClass('checked');
            this.refresh(this.currentpath); // MDL-31113 get latest list from server
        },

        wait: function() {
           this.filemanager.addClass('fm-updating');
        },
        request: function(args, redraw) {
            var api = this.api + '?action='+args.action;
            var params = {};
            var scope = this;
            if (args['scope']) {
                scope = args['scope'];
            }
            params['sesskey'] = M.cfg.sesskey;
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
                        var data = null;
                        try {
                            data = Y.JSON.parse(o.responseText);
                        } catch(e) {
                            scope.print_msg(M.util.get_string('invalidjson', 'repository'), 'error');
                            Y.error(M.util.get_string('invalidjson', 'repository')+":\n"+o.responseText);
                            return;
                        }
                        if (data && data.tree && scope.set_current_tree) {
                            scope.set_current_tree(data.tree);
                        }
                        args.callback(id,data,p);
                    }
                },
                arguments: {
                    scope: scope
                },
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                data: build_querystring(params)
            };
            if (args.form) {
                cfg.form = args.form;
            }
            Y.io(api, cfg);
            if (redraw) {
                this.wait();
            }
        },
        filepicker_callback: function(obj) {
            this.filecount++;
            this.check_buttons();
            this.refresh(this.currentpath);
            if (typeof M.core_formchangechecker != 'undefined') {
                M.core_formchangechecker.set_form_changed();
            }
        },
        check_buttons: function() {
            if (this.filecount>0) {
                this.filemanager.removeClass('fm-nofiles');
            } else {
                this.filemanager.addClass('fm-nofiles');
            }
            if (this.filecount >= this.maxfiles && this.maxfiles!=-1) {
                this.filemanager.addClass('fm-maxfiles');
            }
            else {
                this.filemanager.removeClass('fm-maxfiles');
            }
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
                    scope.filecount = obj.filecount;
                    scope.options = obj;
                    scope.lazyloading = {};
                    scope.check_buttons();
                    scope.render(obj);
                }
            }, true);
        },
        /** displays message in a popup */
        print_msg: function(msg, type) {
            var header = M.util.get_string('error', 'moodle');
            if (type != 'error') {
                type = 'info'; // one of only two types excepted
                header = M.util.get_string('info', 'moodle');
            }
            if (!this.msg_dlg) {
                this.msg_dlg_node = Y.Node.create(M.form_filemanager.templates.message);
                var nodeid = this.msg_dlg_node.generateID();

                this.msg_dlg = new M.core.dialogue({
                    draggable    : true,
                    bodyContent  : this.msg_dlg_node,
                    centered     : true,
                    modal        : true,
                    visible      : false,
                });
                this.msg_dlg_node.one('.fp-msg-butok').on('click', function(e) {
                    e.preventDefault();
                    this.msg_dlg.hide();
                }, this);
            }

            this.msg_dlg.set('headerContent', header);
            this.msg_dlg_node.removeClass('fp-msg-info').removeClass('fp-msg-error').addClass('fp-msg-'+type)
            this.msg_dlg_node.one('.fp-msg-text').setContent(Y.Escape.html(msg));
            this.msg_dlg.show();
        },
        is_disabled: function() {
            return this.filemanager.ancestor('.fitem.disabled') != null;
        },
        getSelectedFiles: function() {
            var markedFiles = this.filemanager.all('[data-togglegroup=file-selections]:checked');
            var filenames = [];
            markedFiles.each(function(item) {
                var fileinfo = this.options.list.find(function(element) {
                    return item.getData().fullname == element.fullname;
                });
                if (fileinfo && fileinfo != undefined) {
                    filenames.push({
                        filepath: fileinfo.filepath,
                        filename: fileinfo.filename
                    });
                }
            }, this);

            return filenames;
        },
        setup_buttons: function() {
            var button_download = this.filemanager.one('.fp-btn-download');
            var button_create   = this.filemanager.one('.fp-btn-mkdir');
            var button_addfile  = this.filemanager.one('.fp-btn-add');
            var buttonDeleteFile = this.filemanager.one('.fp-btn-delete');

            // setup 'add file' button
            button_addfile.on('click', this.show_filepicker, this);

            var dndarrow = this.filemanager.one('.dndupload-arrow');
            if (dndarrow) {
                dndarrow.on('click', this.show_filepicker, this);
            }

            // setup 'make a folder' button
            if (this.options.subdirs) {
                button_create.on('click',function(e) {
                    e.preventDefault();
                    if (this.is_disabled()) {
                        return;
                    }
                    var scope = this;
                    // a function used to perform an ajax request
                    var perform_action = function(e) {
                        e.preventDefault();
                        var foldername = Y.one('#fm-newname-'+scope.client_id).get('value');
                        if (!foldername) {
                            scope.mkdir_dialog.hide();
                            return;
                        }
                        scope.request({
                            action:'mkdir',
                            params: {filepath:scope.currentpath, newdirname:foldername},
                            callback: function(id, obj, args) {
                                var filepath = obj.filepath;
                                scope.mkdir_dialog.hide();
                                scope.refresh(filepath);
                                Y.one('#fm-newname-'+scope.client_id).set('value', '');
                                if (typeof M.core_formchangechecker != 'undefined') {
                                    M.core_formchangechecker.set_form_changed();
                                }
                            }
                        });
                    };
                    var validate_folder_name = function() {
                        var valid = false;
                        var foldername = Y.one('#fm-newname-'+scope.client_id).get('value');
                        if (foldername.length > 0) {
                            valid = true;
                        }
                        var btn = Y.one('#fm-mkdir-butcreate-'+scope.client_id);
                        if (btn) {
                            btn.set('disabled', !valid);
                        }
                        return valid;
                    };
                    if (!this.mkdir_dialog) {
                        var node = Y.Node.create(M.form_filemanager.templates.mkdir);
                        this.mkdir_dialog = new M.core.dialogue({
                            draggable    : true,
                            bodyContent  : node,
                            centered     : true,
                            modal        : true,
                            visible      : false,
                        });
                        node.one('.fp-dlg-butcreate').set('id', 'fm-mkdir-butcreate-'+this.client_id).on('click',
                                perform_action, this);
                        node.one('input').set('id', 'fm-newname-'+this.client_id).on('keydown', function(e) {
                            var valid = Y.bind(validate_folder_name, this)();
                            if (valid && e.keyCode === 13) {
                                Y.bind(perform_action, this)(e);
                            }
                        }, this);
                        node.one('#fm-newname-'+this.client_id).on(['keyup', 'change'], function(e) {
                            Y.bind(validate_folder_name, this)();
                        }, this);

                        node.one('label').set('for', 'fm-newname-' + this.client_id);
                        node.all('.fp-dlg-butcancel').on('click', function(e){e.preventDefault();this.mkdir_dialog.hide();}, this);
                        node.all('.fp-dlg-curpath').set('id', 'fm-curpath-'+this.client_id);
                    }
                    this.mkdir_dialog.show();

                    // Default folder name:
                    var foldername = M.util.get_string('newfolder', 'repository');
                    while (this.has_folder(foldername)) {
                        foldername = increment_filename(foldername, true);
                    }
                    Y.one('#fm-newname-'+scope.client_id).set('value', foldername);
                    Y.bind(validate_folder_name, this)();
                    Y.one('#fm-newname-'+scope.client_id).focus().select();
                    Y.all('#fm-curpath-'+scope.client_id).setContent(this.currentpath);
                }, this);
            } else {
                this.filemanager.addClass('fm-nomkdir');
            }

            // setup 'download this folder' button
            button_download.on('click',function(e) {
                e.preventDefault();
                if (this.is_disabled()) {
                    return;
                }
                var scope = this;

                var image_downloading = this.filemanager.one('.fp-img-downloading');
                if (image_downloading.getStyle('display') == 'inline') {
                    return;
                }
                image_downloading.setStyle('display', 'inline');
                var filenames = this.getSelectedFiles();

                // perform downloaddir ajax request
                this.request({
                    action: 'downloadselected',
                    scope: scope,
                    params: {selected: Y.JSON.stringify(filenames)},
                    callback: function(id, obj, args) {
                        var image_downloading = scope.filemanager.one('.fp-img-downloading');
                        image_downloading.setStyle('display', 'none');

                        if (obj) {
                            scope.refresh(obj.filepath);
                            node = Y.Node.create('<iframe></iframe>').setStyles({
                                visibility : 'hidden',
                                width : '1px',
                                height : '1px'
                            });
                            node.set('src', obj.fileurl);
                            Y.one('body').appendChild(node);
                        } else {
                            scope.print_msg(M.util.get_string('draftareanofiles', 'repository'), 'error');
                        }
                    }
                });
            }, this);

            buttonDeleteFile.on('click', function(e) {
                e.preventDefault();
                var dialogOptions = {};
                var filenames = this.getSelectedFiles();

                if (!filenames.length) {
                    this.print_msg(M.util.get_string('nofilesselected', 'repository'), 'error');
                    return;
                }

                dialogOptions.scope = this;
                var params = {
                    selected: Y.JSON.stringify(filenames)
                };
                dialogOptions.header = M.util.get_string('confirm', 'moodle');
                dialogOptions.message = M.util.get_string('confirmdeleteselectedfile', 'repository', filenames.length);
                dialogOptions.callbackargs = [params];
                dialogOptions.callback = function(params) {
                    this.request({
                        action: 'deleteselected',
                        scope: this,
                        params: params,
                        callback: function(id, obj, args) {
                            // Do something here
                            args.scope.filecount -= params.length;
                            if (obj && obj.length) {
                                args.scope.refresh(obj[0]);
                            }
                            if (typeof M.core_formchangechecker != 'undefined') {
                                M.core_formchangechecker.set_form_changed();
                            }
                        }
                    });
                };
                this.show_confirm_dialog(dialogOptions);
            }, this);

            this.filemanager.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').
                on('click', function(e) {
                    e.preventDefault();
                    var viewbar = this.filemanager.one('.fp-viewbar')
                    if (!this.is_disabled() && (!viewbar || !viewbar.hasClass('disabled'))) {
                        this.filemanager.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').removeClass('checked')
                        if (e.currentTarget.hasClass('fp-vb-tree')) {
                            this.viewmode = 2;
                        } else if (e.currentTarget.hasClass('fp-vb-details')) {
                            this.viewmode = 3;
                        } else {
                            this.viewmode = 1;
                        }
                        e.currentTarget.addClass('checked')
                        this.render();
                        this.filemanager.one('.fp-content').setAttribute('tabIndex', '0');
                        this.filemanager.one('.fp-content').focus();
                        this.set_preference('recentviewmode', this.viewmode);
                    }
                }, this);
        },

        show_filepicker: function (e) {
            // if maxfiles == -1, the no limit
            e.preventDefault();
            if (this.is_disabled()) {
                return;
            }
            var options = this.filepicker_options;
            options.formcallback = this.filepicker_callback;
            // XXX: magic here, to let filepicker use filemanager scope
            options.magicscope = this;
            options.savepath = this.currentpath;
            options.previousActiveElement = e.target.ancestor('a', true);
            M.core_filepicker.show(Y, options);
        },

        print_path: function() {
            var p = this.options.path;
            this.pathbar.setContent('').addClass('empty');
            if (p && p.length!=0 && this.viewmode != 2) {
                for(var i = 0; i < p.length; i++) {
                    var el = this.pathnode.cloneNode(true);
                    this.pathbar.appendChild(el);

                    if (i == 0) {
                        el.addClass('first');
                    }
                    if (i == p.length-1) {
                        el.addClass('last');
                    }

                    if (i%2) {
                        el.addClass('even');
                    } else {
                        el.addClass('odd');
                    }
                    el.one('.fp-path-folder-name').setContent(Y.Escape.html(p[i].name)).
                        on('click', function(e, path) {
                            e.preventDefault();
                            if (!this.is_disabled()) {
                                this.refresh(path);
                            }
                        }, this, p[i].path);
                }
                this.pathbar.removeClass('empty');
            }
        },
        get_filepath: function(obj) {
            if (obj.path && obj.path.length) {
                return obj.path[obj.path.length-1].path;
            }
            return '';
        },
        treeview_dynload: function(node, cb) {
            var retrieved_children = {};
            if (node.children) {
                for (var i in node.children) {
                    retrieved_children[node.children[i].path] = node.children[i];
                }
            }
            if (!node.path || node.path == '/') {
                // this is a root pseudo folder
                node.fileinfo.filepath = '/';
                node.fileinfo.type = 'folder';
                node.fileinfo.fullname = node.fileinfo.title;
                node.fileinfo.filename = '.';
            }
            this.request({
                action:'list',
                params: {filepath:node.path?node.path:''},
                scope:this,
                callback: function(id, obj, args) {
                    var list = obj.list;
                    var scope = args.scope;
                    // check that user did not leave the view mode before recieving this response
                    if (!(scope.viewmode == 2 && node && node.getChildrenEl())) {
                        return;
                    }
                    if (cb != null) { // (in manual mode do not update current path)
                        scope.options = obj;
                        scope.currentpath = node.path?node.path:'/';
                    }
                    node.highlight(false);
                    node.origlist = obj.list ? obj.list : null;
                    node.origpath = obj.path ? obj.path : null;
                    node.children = [];
                    for(k in list) {
                        if (list[k].type == 'folder' && retrieved_children[list[k].filepath]) {
                            // if this child is a folder and has already been retrieved
                            retrieved_children[list[k].filepath].fileinfo = list[k];
                            node.children[node.children.length] = retrieved_children[list[k].filepath];
                        } else {
                            // append new file to the list
                            scope.view_files([list[k]]);
                        }
                    }
                    if (cb == null) {
                        node.refresh();
                    } else {
                        // invoke callback requested by TreeView component
                        cb();
                    }
                    scope.content_scrolled();
                }
            }, false);
        },
        content_scrolled: function(e) {
            setTimeout(Y.bind(function() {
                if (this.processingimages) {return;}
                this.processingimages = true;
                var scope = this,
                    fpcontent = this.filemanager.one('.fp-content'),
                    fpcontenty = fpcontent.getY(),
                    fpcontentheight = fpcontent.getStylePx('height'),
                    is_node_visible = function(node) {
                        var offset = node.getY()-fpcontenty;
                        if (offset <= fpcontentheight && (offset >=0 || offset+node.getStylePx('height')>=0)) {
                            return true;
                        }
                        return false;
                    };
                // replace src for visible images that need to be lazy-loaded
                if (scope.lazyloading) {
                    fpcontent.all('img').each( function(node) {
                        if (node.get('id') && scope.lazyloading[node.get('id')] && is_node_visible(node)) {
                            node.setImgRealSrc(scope.lazyloading);
                        }
                    });
                }
                this.processingimages = false;
            }, this), 200)
        },
        view_files: function(appendfiles) {
            this.filemanager.removeClass('fm-updating').removeClass('fm-noitems');
            if ((appendfiles == null) && (!this.options.list || this.options.list.length == 0) && this.viewmode != 2) {
                this.filemanager.addClass('fm-noitems');
                return;
            }
            var list = (appendfiles != null) ? appendfiles : this.options.list;
            var element_template;
            if (this.viewmode == 2 || this.viewmode == 3) {
                element_template = Y.Node.create(M.form_filemanager.templates.listfilename);
            } else {
                this.viewmode = 1;
                element_template = Y.Node.create(M.form_filemanager.templates.iconfilename);
            }

            if (this.viewmode == 1 || this.viewmode == 2) {
                this.filemanager.one('.fp-btn-delete').addClass('d-none');
            } else {
                this.filemanager.one('.fp-btn-delete').removeClass('d-none');
            }
            var options = {
                viewmode : this.viewmode,
                appendonly : appendfiles != null,
                filenode : element_template,
                disablecheckboxes: false,
                callbackcontext : this,
                callback : function(e, node) {
                    if (e.preventDefault) { e.preventDefault(); }
                    if (node.type == 'folder') {
                        this.refresh(node.filepath);
                    } else {
                        this.select_file(node);
                    }
                },
                rightclickcallback : function(e, node) {
                    if (e.preventDefault) { e.preventDefault(); }
                    this.select_file(node);
                },
                classnamecallback : function(node) {
                    var classname = '';
                    if (node.type == 'folder' || (!node.type && !node.filename)) {
                        classname = classname + ' fp-folder';
                    }
                    if (node.filename || node.filepath || (node.path && node.path != '/')) {
                        classname = classname + ' fp-hascontextmenu';
                    }
                    if (node.isref) {
                        classname = classname + ' fp-isreference';
                    }
                    if (node.refcount) {
                        classname = classname + ' fp-hasreferences';
                    }
                    if (node.originalmissing) {
                        classname = classname + ' fp-originalmissing';
                    }
                    if (node.sortorder == 1) { classname = classname + ' fp-mainfile';}
                    return Y.Lang.trim(classname);
                }
            };
            if (this.viewmode == 2) {
                options.dynload = true;
                options.filepath = this.options.path;
                options.treeview_dynload = this.treeview_dynload;
                options.norootrightclick = true;
                options.callback = function(e, node) {
                    // TODO MDL-32736 e is not an event here but an object with properties 'event' and 'node'
                    if (!node.fullname) {return;}
                    if (node.type != 'folder') {
                        if (e.node.parent && e.node.parent.origpath) {
                            // set the current path
                            this.options.path = e.node.parent.origpath;
                            this.options.list = e.node.parent.origlist;
                            this.print_path();
                        }
                        this.currentpath = node.filepath;
                        this.select_file(node);
                    } else {
                        // save current path and filelist (in case we want to jump to other viewmode)
                        this.options.path = e.node.origpath;
                        this.options.list = e.node.origlist;
                        this.currentpath = node.filepath;
                        this.print_path();
                        //this.content_scrolled();
                    }
                };
            }
            if (!this.lazyloading) {
                this.lazyloading={};
            }
            this.filemanager.one('.fp-content').fp_display_filelist(options, list, this.lazyloading);
            this.content_scrolled();
        },
        populateLicensesSelect: function(licensenode, filenode) {
            if (!licensenode) {
                return;
            }
            licensenode.setContent('');
            var selectedlicense = this.filepicker_options.defaultlicense;
            if (filenode) {
                // File has a license already, use it.
                selectedlicense = filenode.license;
            } else if (this.filepicker_options.rememberuserlicensepref) {
                selectedlicense = this.get_preference('recentlicense');
            }
            var licenses = this.filepicker_options.licenses;
            for (var i in licenses) {
                // Include the file's current license, even if not enabled, to prevent displaying
                // misleading information about which license the file currently has assigned to it.
                if (licenses[i].enabled == true || (filenode !== undefined && licenses[i].shortname === filenode.license)) {
                    var option = Y.Node.create('<option/>').
                    set('selected', (licenses[i].shortname == selectedlicense)).
                    set('value', licenses[i].shortname).
                    setContent(Y.Escape.html(licenses[i].fullname));
                    licensenode.appendChild(option);
                }
            }
        },
        set_current_tree: function(tree) {
            var appendfilepaths = function(list, node) {
                if (!node || !node.children || !node.children.length) {return;}
                for (var i in node.children) {
                    list[list.length] = node.children[i].filepath;
                    appendfilepaths(list, node.children[i]);
                }
            }
            var list = ['/'];
            appendfilepaths(list, tree);
            var selectnode = this.selectnode;
            node = selectnode.one('.fp-path select');
            node.setContent('');
            for (var i in list) {
                node.appendChild(Y.Node.create('<option/>').
                    set('value', list[i]).setContent(Y.Escape.html(list[i])));
            }
        },
        update_file: function(confirmed) {
            var selectnode = this.selectnode;
            var fileinfo = this.selectui.fileinfo;

            var newfilename = Y.Lang.trim(selectnode.one('.fp-saveas input').get('value'));
            var filenamechanged = (newfilename && newfilename != fileinfo.fullname);
            var pathselect = selectnode.one('.fp-path select'),
                    pathindex = pathselect.get('selectedIndex'),
                    targetpath = pathselect.get("options").item(pathindex).get('value');
            var filepathchanged = (targetpath != this.get_parent_folder_name(fileinfo));
            var newauthor = Y.Lang.trim(selectnode.one('.fp-author input').get('value'));
            var authorchanged = (newauthor != Y.Lang.trim(fileinfo.author));
            var licenseselect = selectnode.one('.fp-license select'),
                    licenseindex = licenseselect.get('selectedIndex'),
                    newlicense = licenseselect.get("options").item(licenseindex).get('value');
            var licensechanged = (newlicense != fileinfo.license);

            var params, action;
            var dialog_options = {callback:this.update_file, callbackargs:[true], scope:this};
            if (fileinfo.type == 'folder') {
                if (!newfilename) {
                    this.print_msg(M.util.get_string('entername', 'repository'), 'error');
                    return;
                }
                if (filenamechanged || filepathchanged) {
                    if (!confirmed) {
                        dialog_options.message = M.util.get_string('confirmrenamefolder', 'repository');
                        this.show_confirm_dialog(dialog_options);
                        return;
                    }
                    params = {filepath:fileinfo.filepath, newdirname:newfilename, newfilepath:targetpath};
                    action = 'updatedir';
                }
            } else {
                if (!newfilename) {
                    this.print_msg(M.util.get_string('enternewname', 'repository'), 'error');
                    return;
                }

                if ((filenamechanged || filepathchanged) && !confirmed) {
                    var warnings = '';
                    var originalfilenamearr = fileinfo.fullname.split('.');
                    var originalextension = (originalfilenamearr.length > 1) ? originalfilenamearr.pop() : "";
                    var newfilenamearr = newfilename.split('.');
                    var newextension = (newfilenamearr.length > 1) ? newfilenamearr.pop() : "";

                    if (newextension !== originalextension) {
                        if (newextension === "") {
                            var string = M.util.get_string('originalextensionremove', 'repository', originalextension);
                        } else {
                            var stringvars = {
                                originalextension: originalextension,
                                newextension: newextension
                            }
                            string = M.util.get_string('originalextensionchange', 'repository', stringvars);
                        }
                        warnings = warnings.concat('<li>', string, '</li>');
                    }
                    if (fileinfo.refcount) {
                        var string = M.util.get_string('aliaseschange', 'repository', fileinfo.refcount);
                        warnings = warnings.concat('<li>', string, '</li>');
                    }
                    if (warnings.length > 0) {
                        var message = '';
                        var confirmmsg = M.util.get_string('confirmrenamefile', 'repository', fileinfo.refcount);
                        dialog_options.message = message.concat('<p>', confirmmsg, '</p>',
                            '<ul class="p-x-2">', warnings, '</ul>');
                        this.show_confirm_dialog(dialog_options);
                        return;
                    }
                }
                if (filenamechanged || filepathchanged || licensechanged || authorchanged) {
                    params = {filepath:fileinfo.filepath, filename:fileinfo.fullname,
                        newfilename:newfilename, newfilepath:targetpath,
                        newlicense:newlicense, newauthor:newauthor};
                    action = 'updatefile';
                }
            }
            if (!action) {
                // no changes
                this.selectui.hide();
                return;
            }
            selectnode.addClass('loading');
            this.request({
                action: action,
                scope: this,
                params: params,
                callback: function(id, obj, args) {
                    if (obj.error) {
                        selectnode.removeClass('loading');
                        args.scope.print_msg(obj.error, 'error');
                    } else {
                        args.scope.selectui.hide();
                        args.scope.refresh((obj && obj.filepath) ? obj.filepath : '/');
                        if (typeof M.core_formchangechecker != 'undefined') {
                            M.core_formchangechecker.set_form_changed();
                        }
                    }
                }
            });
        },
        /**
         * Displays a confirmation dialog
         * Expected attributes in dialog_options: message, callback, callbackargs(optional), scope(optional)
         */
        show_confirm_dialog: function(dialog_options) {
            // instead of M.util.show_confirm_dialog(e, dialog_options);
            if (!this.confirm_dlg) {
                this.confirm_dlg_node = Y.Node.create(M.form_filemanager.templates.confirmdialog);
                var node = this.confirm_dlg_node;
                node.generateID();
                this.confirm_dlg = new M.core.dialogue({
                    draggable    : true,
                    bodyContent  : node,
                    centered     : true,
                    modal        : true,
                    visible      : false,
                    buttons      : {}
                });
                var handle_confirm = function(ev) {
                    var dlgopt = this.confirm_dlg.dlgopt;
                    ev.preventDefault();
                    this.confirm_dlg.hide();
                    if (dlgopt.callback) {
                        if (dlgopt.callbackargs) {
                            dlgopt.callback.apply(dlgopt.scope || this, dlgopt.callbackargs);
                        } else {
                            dlgopt.callback.apply(dlgopt.scope || this);
                        }
                    }
                }
                var handle_cancel = function(ev) {
                    ev.preventDefault();
                    this.confirm_dlg.hide();
                }
                node.one('.fp-dlg-butconfirm').on('click', handle_confirm, this);
                node.one('.fp-dlg-butcancel').on('click', handle_cancel, this);
            }
            this.confirm_dlg.dlgopt = dialog_options;
            if (typeof dialog_options.header != 'undefined') {
                this.confirm_dlg.set('headerContent', dialog_options.header);
            }
            this.confirm_dlg_node.one('.fp-dlg-text').setContent(dialog_options.message);
            this.confirm_dlg.show();
        },
        setup_select_file: function() {
            var selectnode = this.selectnode;
            var scope = this;
            // bind labels with corresponding inputs
            selectnode.all('.fp-saveas,.fp-path,.fp-author,.fp-license').each(function (node) {
                node.all('label').set('for', node.one('input,select').generateID());
            });
            // register event on clicking buttons
            selectnode.one('.fp-file-update').on('click', function(e) {
                e.preventDefault();
                this.update_file();
            }, this);
            selectnode.all('form input').on('key', function(e) {
                e.preventDefault();
                scope.update_file();
            }, 'enter');
            selectnode.one('.fp-file-download').on('click', function(e) {
                e.preventDefault();
                if (this.selectui.fileinfo.type != 'folder') {
                    node = Y.Node.create('<iframe></iframe>').setStyles({
                        visibility : 'hidden',
                        width : '1px',
                        height : '1px'
                    });
                    node.set('src', this.selectui.fileinfo.url);
                    Y.one('body').appendChild(node);
                }
            }, this);
            selectnode.one('.fp-file-delete').on('click', function(e) {
                e.preventDefault();
                var dialog_options = {
                    scope: this,
                    header: M.util.get_string('confirm', 'moodle'),
                };
                var params = {};
                var fileinfo = this.selectui.fileinfo;
                params.filepath = fileinfo.filepath;
                if (fileinfo.type == 'folder') {
                    params.filename = '.';
                    dialog_options.message = M.util.get_string('confirmdeletefolder', 'repository');
                } else {
                    params.filename = fileinfo.fullname;
                    if (fileinfo.refcount) {
                        dialog_options.message = M.util.get_string('confirmdeletefilewithhref', 'repository', fileinfo.refcount);
                    } else {
                        dialog_options.message = M.util.get_string('confirmdeletefile', 'repository');
                    }
                }
                dialog_options.callbackargs = [params];
                dialog_options.callback = function(params) {
                    //selectnode.addClass('loading');
                    this.request({
                        action: 'delete',
                        scope: this,
                        params: params,
                        callback: function(id, obj, args) {
                            //args.scope.selectui.hide();
                            args.scope.filecount--;
                            args.scope.refresh(obj.filepath);
                            if (typeof M.core_formchangechecker != 'undefined') {
                                M.core_formchangechecker.set_form_changed();
                            }
                        }
                    });
                };
                this.selectui.hide(); // TODO remove this after confirm dialog is replaced with YUI3
                this.show_confirm_dialog(dialog_options);
            }, this);
            selectnode.one('.fp-file-zip').on('click', function(e) {
                e.preventDefault();
                var params = {};
                var fileinfo = this.selectui.fileinfo;
                if (fileinfo.type != 'folder') {
                    // this button should not even be shown
                    return;
                }
                params['filepath']   = fileinfo.filepath;
                params['filename']   = '.';
                selectnode.addClass('loading');
                this.request({
                    action: 'zip',
                    scope: this,
                    params: params,
                    callback: function(id, obj, args) {
                        args.scope.selectui.hide();
                        args.scope.refresh(obj.filepath);
                    }
                });
            }, this);
            selectnode.one('.fp-file-unzip').on('click', function(e) {
                e.preventDefault();
                var params = {};
                var fileinfo = this.selectui.fileinfo;
                if (fileinfo.type != 'zip') {
                    // this button should not even be shown
                    return;
                }
                params['filepath'] = fileinfo.filepath;
                params['filename'] = fileinfo.fullname;
                // The unlimited value of areamaxbytes is -1, it is defined by FILE_AREA_MAX_BYTES_UNLIMITED.
                params['areamaxbytes'] = this.areamaxbytes ? this.areamaxbytes : -1;
                selectnode.addClass('loading');
                this.request({
                    action: 'unzip',
                    scope: this,
                    params: params,
                    callback: function(id, obj, args) {
                        if (obj.error) {
                            selectnode.removeClass('loading');
                            args.scope.print_msg(obj.error, 'error');
                        } else {
                            args.scope.selectui.hide();
                            args.scope.refresh(obj.filepath);
                        }
                    }
                });
            }, this);
            selectnode.one('.fp-file-setmain').on('click', function(e) {
                e.preventDefault();
                var params = {};
                var fileinfo = this.selectui.fileinfo;
                if (!this.enablemainfile || fileinfo.type == 'folder') {
                    // this button should not even be shown for folders or when mainfile is disabled
                    return;
                }
                params['filepath'] = fileinfo.filepath;
                params['filename'] = fileinfo.fullname;
                selectnode.addClass('loading');
                this.request({
                    action: 'setmainfile',
                    scope: this,
                    params: params,
                    callback: function(id, obj, args) {
                        args.scope.selectui.hide();
                        args.scope.refresh(fileinfo.filepath);
                    }
                });
            }, this);
            selectnode.all('.fp-file-cancel').on('click', function(e) {
                e.preventDefault();
                // TODO if changed asked to confirm, the same with close button
                this.selectui.hide();
            }, this);
            selectnode.all('.fp-file-update, .fp-file-download, .fp-file-delete, .fp-file-zip, .fp-file-unzip, ' +
                '.fp-file-setmain, .fp-file-cancel').on('key', function(e) {
                    e.preventDefault();
                    this.simulate('click');
            }, 'enter');
        },
        get_parent_folder_name: function(node) {
            if (node.type != 'folder' || node.filepath.length < node.fullname.length+1) {
                return node.filepath;
            }
            var basedir = node.filepath.substr(0, node.filepath.length - node.fullname.length - 1);
            var lastdir = node.filepath.substr(node.filepath.length - node.fullname.length - 2);
            if (lastdir == '/' + node.fullname + '/') {
                return basedir;
            }
            return node.filepath;
        },
        select_file: function(node) {
            if (this.is_disabled()) {
                return;
            }
            var selectnode = this.selectnode;
            selectnode.removeClass('loading').removeClass('fp-folder').
                removeClass('fp-file').removeClass('fp-zip').removeClass('fp-cansetmain');
            if (node.type == 'folder' || node.type == 'zip') {
                selectnode.addClass('fp-'+node.type);
            } else {
                selectnode.addClass('fp-file');
            }
            if (this.enablemainfile && (node.sortorder != 1) && node.type == 'file') {
                selectnode.addClass('fp-cansetmain');
            }
            this.selectui.fileinfo = node;
            selectnode.one('.fp-saveas input').set('value', node.fullname);
            var foldername = this.get_parent_folder_name(node);
            selectnode.all('.fp-author input').set('value', node.author ? node.author : '');
            this.populateLicensesSelect(selectnode.one('.fp-license select'), node);
            selectnode.all('.fp-path select option[selected]').set('selected', false);
            selectnode.all('.fp-path select option').each(function(el){
                if (el.get('value') == foldername) {
                    el.set('selected', true);
                }
            });
            selectnode.all('.fp-author input, .fp-license select').set('disabled',(node.type == 'folder')?'disabled':'');
            // display static information about a file (when known)
            var attrs = ['datemodified','datecreated','size','dimensions','original','reflist'];
            for (var i in attrs) {
                if (selectnode.one('.fp-'+attrs[i])) {
                    var value = (node[attrs[i]+'_f']) ? node[attrs[i]+'_f'] : (node[attrs[i]] ? node[attrs[i]] : '');
                    // Escape if the attribute being evaluated is not for the list of reference files.
                    if (attrs[i] !== 'reflist') {
                        value = Y.Escape.html(value);
                    }
                    selectnode.one('.fp-'+attrs[i]).addClassIf('fp-unknown', ''+value == '')
                        .one('.fp-value').setContent(value);
                }
            }
            // display thumbnail
            var imgnode = Y.Node.create('<img/>').
                set('src', node.realthumbnail ? node.realthumbnail : node.thumbnail).
                setStyle('maxHeight', ''+(node.thumbnail_height ? node.thumbnail_height : 90)+'px').
                setStyle('maxWidth', ''+(node.thumbnail_width ? node.thumbnail_width : 90)+'px');
            selectnode.one('.fp-thumbnail').setContent('').appendChild(imgnode);
            // load original location if applicable
            if (node.isref && !node.original) {
                selectnode.one('.fp-original').removeClass('fp-unknown').addClass('fp-loading');
                this.request({
                    action: 'getoriginal',
                    scope: this,
                    params: {'filepath':node.filepath,'filename':node.fullname},
                    callback: function(id, obj, args) {
                        // check if we did not select another file meanwhile
                        var scope = args.scope;
                        if (scope.selectui.fileinfo && node &&
                                scope.selectui.fileinfo.filepath == node.filepath &&
                                scope.selectui.fileinfo.fullname == node.fullname) {
                            selectnode.one('.fp-original').removeClass('fp-loading');
                            if (obj.original) {
                                node.original = obj.original;
                                selectnode.one('.fp-original .fp-value').setContent(Y.Escape.html(node.original));
                            } else {
                                selectnode.one('.fp-original .fp-value').setContent(M.util.get_string('unknownsource', 'repository'));
                            }
                        }
                    }
                }, false);
            }
            // load references list if applicable
            selectnode.one('.fp-refcount').setContent(node.refcount ? M.util.get_string('referencesexist', 'repository', node.refcount) : '');
            if (node.refcount && !node.reflist) {
                selectnode.one('.fp-reflist').removeClass('fp-unknown').addClass('fp-loading');
                this.request({
                    action: 'getreferences',
                    scope: this,
                    params: {'filepath':node.filepath,'filename':node.fullname},
                    callback: function(id, obj, args) {
                        // check if we did not select another file meanwhile
                        var scope = args.scope;
                        if (scope.selectui.fileinfo && node &&
                                scope.selectui.fileinfo.filepath == node.filepath &&
                                scope.selectui.fileinfo.fullname == node.fullname) {
                            selectnode.one('.fp-reflist').removeClass('fp-loading');
                            if (obj.references) {
                                node.reflist = '';
                                for (var i in obj.references) {
                                    node.reflist += '<li>'+Y.Escape.html(obj.references[i])+'</li>';
                                }
                                selectnode.one('.fp-reflist .fp-value').setContent(node.reflist);
                            } else {
                                selectnode.one('.fp-reflist .fp-value').setContent('');
                            }
                        }
                    }
                }, false);
            }
            // update dialog header
            var nodename = node.fullname;
            // Limit the string length so it fits nicely on mobile devices
            var namelength = 50;
            if (nodename.length > namelength) {
                nodename = nodename.substring(0, namelength) + '...';
            }
            Y.one('#fm-dialog-label_'+selectnode.get('id')).setContent(Y.Escape.html(M.util.get_string('edit', 'moodle')+' '+nodename));
            // show panel
            this.selectui.show();
            Y.one('#'+selectnode.get('id')).focus();
        },
        render: function() {
            this.print_path();
            this.view_files();
        },
        has_folder: function(foldername) {
            var element;
            for (var i in this.options.list) {
                element = this.options.list[i];
                if (element.type == 'folder' && element.fullname == foldername) {
                    return true;
                }
            }
            return false;
        },
        get_preference: function(name) {
            if (this.userprefs[name]) {
                return this.userprefs[name];
            } else {
                return false;
            }
        },
        set_preference: function(name, value) {
            if (this.userprefs[name] != value) {
                M.util.set_user_preference('filemanager_' + name, value);
                this.userprefs[name] = value;
            }
        },
    });

    // finally init everything needed
    // hide loading picture, display filemanager interface
    var filemanager = Y.one('#filemanager-'+options.client_id);
    filemanager.removeClass('fm-loading').addClass('fm-loaded');

    var manager = new FileManagerHelper(options);
    var dndoptions = {
        filemanager: manager,
        acceptedtypes: options.filepicker.accepted_types,
        clientid: options.client_id,
        author: options.author,
        maxfiles: options.maxfiles,
        maxbytes: options.maxbytes,
        areamaxbytes: options.areamaxbytes,
        itemid: options.itemid,
        repositories: manager.filepicker_options.repositories,
        containerid: manager.dndcontainer.get('id'),
        contextid: options.context.id
    };
    M.form_dndupload.init(Y, dndoptions);
};
