// YUI3 File Picker module for moodle
// Author: Dongsheng Cai <dongsheng@moodle.com>

/**
 *
 * File Picker UI
 * =====
 * this.fpnode, contains reference to filepicker Node, non-empty if and only if rendered
 * this.api, stores the URL to make ajax request
 * this.mainui, YUI Panel
 * this.selectui, YUI Panel for selecting particular file
 * this.msg_dlg, YUI Panel for error or info message
 * this.process_dlg, YUI Panel for processing existing filename
 * this.treeview, YUI Treeview
 * this.viewmode, store current view mode
 * this.pathbar, reference to the Node with path bar
 * this.pathnode, a Node element representing one folder in a path bar (not attached anywhere, just used for template)
 *
 * Filepicker options:
 * =====
 * this.options.client_id, the instance id
 * this.options.contextid
 * this.options.itemid
 * this.options.repositories, stores all repositories displaied in file picker
 * this.options.formcallback
 *
 * Active repository options
 * =====
 * this.active_repo.id
 * this.active_repo.nosearch
 * this.active_repo.norefresh
 * this.active_repo.nologin
 * this.active_repo.help
 * this.active_repo.manage
 *
 * Server responses
 * =====
 * this.filelist, cached filelist
 * this.pages
 * this.page
 * this.filepath, current path
 * this.logindata, cached login form
 */

M.core_filepicker = M.core_filepicker || {};

/**
 * instances of file pickers used on page
 */
M.core_filepicker.instances = M.core_filepicker.instances || {};
M.core_filepicker.active_filepicker = null;

/**
 * HTML Templates to use in FilePicker
 */
M.core_filepicker.templates = M.core_filepicker.templates || {};

/**
 * Init and show file picker
 */
M.core_filepicker.show = function(Y, options) {
    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options);
    }
    M.core_filepicker.instances[options.client_id].show();
};

/**
 * Add new file picker to current instances
 */
M.core_filepicker.init = function(Y, options) {
    if (options.templates);
    for (var templid in options.templates) {
        this.templates[templid] = options.templates[templid];
    }

    var FilePickerHelper = function(options) {
        FilePickerHelper.superclass.constructor.apply(this, arguments);
    };

    FilePickerHelper.NAME = "FilePickerHelper";
    FilePickerHelper.ATTRS = {
        options: {},
        lang: {}
    };

    Y.extend(FilePickerHelper, Y.Base, {
        api: M.cfg.wwwroot+'/repository/repository_ajax.php',
        cached_responses: {},

        initializer: function(options) {
            this.options = options;
            if (!this.options.savepath) {
                this.options.savepath = '/';
            }
        },

        destructor: function() {
        },

        request: function(args, redraw) {
            var client_id = args.client_id;
            if (!args.api) {
                var api = this.api + '?action='+args.action;
            } else {
                var api = args.api + '?action='+args.action;
            }
            var params = {};
            var scope = this;
            if (args['scope']) {
                scope = args['scope'];
            }
            params['repo_id']=args.repository_id;
            params['p'] = args.path?args.path:'';
            params['page'] = args.page?args.page:'';
            params['env']=this.options.env;
            // the form element only accept certain file types
            params['accepted_types']=this.options.accepted_types;
            params['sesskey'] = M.cfg.sesskey;
            params['client_id'] = args.client_id;
            params['itemid'] = this.options.itemid?this.options.itemid:0;
            params['maxbytes'] = this.options.maxbytes?this.options.maxbytes:-1;
            if (this.options.context && this.options.context.id) {
                params['ctx_id'] = this.options.context.id;
            }
            if (args['params']) {
                for (i in args['params']) {
                    params[i] = args['params'][i];
                }
            }
            if (args.action == 'upload') {
                var list = [];
                for(var k in params) {
                    var value = params[k];
                    if(value instanceof Array) {
                        for(var i in value) {
                            list.push(k+'[]='+value[i]);
                        }
                    } else {
                        list.push(k+'='+value);
                    }
                }
                params = list.join('&');
            } else {
                params = build_querystring(params);
            }
            var cfg = {
                method: 'POST',
                on: {
                    complete: function(id,o,p) {
                        if (!o) {
                            // TODO
                            alert('IO FATAL');
                            return;
                        }
                        var data = null;
                        try {
                            data = Y.JSON.parse(o.responseText);
                        } catch(e) {
                            scope.print_msg(M.str.repository.invalidjson, 'error');
                            scope.display_error(M.str.repository.invalidjson+'<pre>'+stripHTML(o.responseText)+'</pre>', 'invalidjson')
                            return;
                        }
                        // error checking
                        if (data && data.error) {
                            scope.print_msg(data.error, 'error');
                            if (args.onerror) {
                                args.onerror(id,data,p);
                            } else {
                                Y.one(panel_id).set('innerHTML', '');
                            }
                            return;
                        } else if (data && data.event) {
                            switch (data.event) {
                                case 'fileexists':
                                    scope.process_existing_file(data);
                                    break;
                                default:
                                    break;
                            }
                        } else {
                            if (data.msg) {
                                scope.print_msg(data.msg, 'info');
                            }
                            // cache result if applicable
                            if (args.action != 'upload' && data.allowcaching) {
                                scope.cached_responses[params] = data;
                            }
                            // invoke callback
                            args.callback(id,data,p);
                        }
                    }
                },
                arguments: {
                    scope: scope
                },
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                data: params,
                context: this
            };
            if (args.form) {
                cfg.form = args.form;
            }
            // check if result of the same request has been already cached. If not, request it
            // (never applicable in case of form submission and/or upload action):
            if (!args.form && args.action != 'upload' && scope.cached_responses[params]) {
                args.callback(null, scope.cached_responses[params], {scope: scope})
            } else {
                Y.io(api, cfg);
                if (redraw) {
                    this.wait();
                }
            }
        },
        /** displays the dialog and processes rename/overwrite if there is a file with the same name in the same filearea*/
        process_existing_file: function(data) {
            var scope = this;
            var handleOverwrite = function(e) {
                // overwrite
                e.preventDefault();
                var data = this.process_dlg.dialogdata;
                var params = {}
                params['existingfilename'] = data.existingfile.filename;
                params['existingfilepath'] = data.existingfile.filepath;
                params['newfilename'] = data.newfile.filename;
                params['newfilepath'] = data.newfile.filepath;
                this.hide_header();
                this.request({
                    'params': params,
                    'scope': this,
                    'action':'overwrite',
                    'path': '',
                    'client_id': this.options.client_id,
                    'repository_id': this.active_repo.id,
                    'callback': function(id, o, args) {
                        scope.hide();
                        // editor needs to update url
                        // filemanager do nothing
                        if (scope.options.editor_target && scope.options.env == 'editor') {
                            scope.options.editor_target.value = data.existingfile.url;
                            scope.options.editor_target.onchange();
                        } else if (scope.options.env === 'filepicker') {
                            var fileinfo = {'client_id':scope.options.client_id,
                                    'url':data.existingfile.url,
                                    'file':data.existingfile.filename};
                            scope.options.formcallback.apply(scope, [fileinfo]);
                        }
                    }
                }, true);
            }
            var handleRename = function(e) {
                // inserts file with the new name
                e.preventDefault();
                var scope = this;
                var data = this.process_dlg.dialogdata;
                if (scope.options.editor_target && scope.options.env == 'editor') {
                    scope.options.editor_target.value = data.newfile.url;
                    scope.options.editor_target.onchange();
                }
                scope.hide();
                var formcallback_scope = null;
                if (scope.options.magicscope) {
                    formcallback_scope = scope.options.magicscope;
                } else {
                    formcallback_scope = scope;
                }
                var fileinfo = {'client_id':scope.options.client_id,
                                'url':data.newfile.url,
                                'file':data.newfile.filename};
                scope.options.formcallback.apply(formcallback_scope, [fileinfo]);
            }
            var handleCancel = function(e) {
                // Delete tmp file
                e.preventDefault();
                var params = {};
                params['newfilename'] = this.process_dlg.dialogdata.newfile.filename;
                params['newfilepath'] = this.process_dlg.dialogdata.newfile.filepath;
                this.request({
                    'params': params,
                    'scope': this,
                    'action':'deletetmpfile',
                    'path': '',
                    'client_id': this.options.client_id,
                    'repository_id': this.active_repo.id,
                    'callback': function(id, o, args) {
                        // let it be in background, from user point of view nothing is happenning
                    }
                }, false);
                this.process_dlg.hide();
                this.selectui.hide();
            }
            if (!this.process_dlg) {
                var node = Y.Node.create(M.core_filepicker.templates.processexistingfile);
                this.fpnode.appendChild(node);
                this.process_dlg = new Y.Panel({
                    srcNode      : node,
                    headerContent: M.str.repository.fileexistsdialogheader,
                    zIndex       : 800000,
                    centered     : true,
                    modal        : true,
                    visible      : false,
                    render       : true,
                    buttons      : {}
                });
                node.one('.fp-dlg-butoverwrite').on('click', handleOverwrite, this);
                node.one('.fp-dlg-butrename').on('click', handleRename, this);
                node.one('.fp-dlg-butcancel').on('click', handleCancel, this);
                if (this.options.env == 'editor') {
                    node.one('.fp-dlg-text').setContent(M.str.repository.fileexistsdialog_editor);
                } else {
                    node.one('.fp-dlg-text').setContent(M.str.repository.fileexistsdialog_filemanager);
                }
            }
            this.process_dlg.dialogdata = data;
            this.fpnode.one('.fp-dlg .fp-dlg-butrename').setContent(M.util.get_string('renameto', 'repository', data.newfile.filename));
            this.process_dlg.show();
        },
        /** displays error instead of filepicker contents */
        display_error: function(errortext, errorcode) {
            this.fpnode.one('.fp-content').setContent(M.core_filepicker.templates.error);
            this.fpnode.one('.fp-content .fp-error').
                addClass(errorcode).
                setContent(errortext);
        },
        /** displays message in a popup */
        print_msg: function(msg, type) {
            var header = M.str.moodle.error;
            if (type != 'error') {
                type = 'info'; // one of only two types excepted
                header = M.str.moodle.info;
            }
            if (!this.msg_dlg) {
                var node = Y.Node.create(M.core_filepicker.templates.message);
                this.fpnode.appendChild(node);

                this.msg_dlg = new Y.Panel({
                    srcNode      : node,
                    zIndex       : 800000,
                    centered     : true,
                    modal        : true,
                    visible      : false,
                    render       : true
                });
                node.one('.fp-msg-butok').on('click', function(e) {
                    e.preventDefault();
                    this.msg_dlg.hide();
                }, this);
            }

            this.msg_dlg.set('headerContent', header);
            this.fpnode.one('.fp-msg').removeClass('fp-msg-info').removeClass('fp-msg-error').addClass('fp-msg-'+type)
            this.fpnode.one('.fp-msg .fp-msg-text').setContent(msg);
            this.msg_dlg.show();
        },
        build_tree: function(node, level) {
            var client_id = this.options.client_id;
            var dynload = this.active_repo.dynload;
            var info = {
                label:node.title,
                filename:node.title,
                source:node.source?node.source:'',
                thumbnail:node.thumbnail,
                path:node.path?node.path:[]
            };
            var tmpNode = new YAHOO.widget.TextNode(info, level, false);
            if(node.repo_id) {
                tmpNode.repo_id=node.repo_id;
            }else{
                tmpNode.repo_id=this.active_repo.id;
            }
            if(node.children) {
                if(node.expanded) {
                    tmpNode.expand();
                }
                if (dynload) {
                    tmpNode.scope = this;
                }
                tmpNode.isLeaf = false;
                tmpNode.client_id = client_id;
                if (node.path) {
                    tmpNode.path = node.path;
                } else {
                    tmpNode.path = '';
                }
                for(var c in node.children) {
                    this.build_tree(node.children[c], tmpNode);
                }
            } else {
                tmpNode.isLeaf = true;
            }
        },
        view_files: function() {
            if (this.active_repo.issearchresult) {
                // list view is desiged to display treeview
                // it is not working well with search result
                this.view_as_icons();
            } else {
                this.viewbar_set_enabled(true);
                if (this.viewmode == 1) {
                    this.view_as_icons();
                } else if (this.viewmode == 2) {
                    this.view_as_list();
                } else {
                    this.view_as_icons();
                }
            }
        },
        treeview_dynload: function(node, cb) {
            var scope = node.scope;
            var client_id = scope.options.client_id;
            var repository_id = scope.active_repo.id;
            scope.request({
                action:'list',
                client_id: client_id,
                repository_id: repository_id,
                path:node.path?node.path:'',
                page:node.page?args.page:'',
                callback: function(id, obj, args) {
                    var list = obj.list;
                    scope.viewbar_set_enabled(true);
                    scope.parse_repository_options(obj);
                    for(k in list) {
                        scope.build_tree(list[k], node);
                    }
                    cb();
                }
            }, false);
        },
        view_as_list: function() {
            // TODO !!!!!!!!!!
            var scope = this;
            var client_id = scope.options.client_id;
            var dynload = scope.active_repo.dynload;
            var list = this.filelist;
            scope.viewmode = 2;
            if (list && list.length==0) {
                this.display_error(M.str.repository.nofilesavailable, 'nofilesavailable');
                return;
            }

            var html = '<div class="fp-tree-panel" id="treeview-'+client_id+'"></div>';
            this.fpnode.one('.fp-content').setContent(html);

            scope.treeview = new YAHOO.widget.TreeView('treeview-'+client_id);
            if (dynload) {
                scope.treeview.setDynamicLoad(scope.treeview_dynload, 1);
            }

            for(k in list) {
                scope.build_tree(list[k], scope.treeview.getRoot());
            }
            scope.treeview.subscribe('clickEvent', function(e){
                if(e.node.isLeaf){
                    var fileinfo = {};
                    fileinfo['title'] = e.node.data.filename;
                    fileinfo['source'] = e.node.data.source;
                    fileinfo['thumbnail'] = e.node.data.thumbnail;
                    scope.select_file(fileinfo);
                }
            });
            scope.treeview.draw();
        },
        view_as_icons: function() {
            var scope = this;
            var client_id = this.options.client_id;
            var list = this.filelist;
            this.viewmode = 1;

            if (list && list.length==0) {
                this.display_error(M.str.repository.nofilesavailable, 'nofilesavailable');
                return;
            }
            this.fpnode.one('.fp-content').setContent(M.core_filepicker.templates.iconview);

            var element_template = this.fpnode.one('.fp-content').one('.fp-file');
            var container = element_template.get('parentNode');
            container.removeChild(element_template);
            var count = 0;
            for(var k in list) {
                var node = list[k];
                var element = element_template.cloneNode(true);
                container.appendChild(element);
                /*html = M.core_filepicker.templates.gridelementtemplate.
                    replace(/\{GRIDELEMENTID\}/g, 'fp-grid-'+client_id+'-'+count).
                    replace(/\{IMGID\}/g, 'fp-img-'+client_id+'-'+count).
                    replace(/\{FILENAMEID\}/g, 'fp-filename-'+client_id+'-'+count);*/
                var filename = node.shorttitle ? node.shorttitle : node.title;
                var filenamediv = element.one('.fp-filename');
                filenamediv.setContent(filename);
                var imgdiv = element.one('.fp-thumbnail');
                var set_width = function(node, width) {
                    var widthmatches = node.getStyle('minWidth').match(/^(\d+)px$/)
                    if (widthmatches && parseInt(widthmatches[1])>width) {
                        width = parseInt(widthmatches[1]);
                    }
                    node.setStyle('width', '' + width + 'px')
                }
                var set_height = function(node, height) {
                    var heightmatches = node.getStyle('minHeight').match(/^(\d+)px$/)
                    if (heightmatches && parseInt(heightmatches[1])>height) {
                        height = parseInt(heightmatches[1]);
                    }
                    node.setStyle('height', '' + height + 'px')
                }
                var width = node.thumbnail_width ? node.thumbnail_width : 90;
                var height = node.thumbnail_height ? node.thumbnail_height : 90;
                set_width(filenamediv, width)
                set_width(imgdiv, width)
                set_height(imgdiv, height);
                var img = Y.Node.create('<img/>').
                    set('src', node.thumbnail).
                    set('title', node.title);
                if(node.thumbnail_alt) {
                    img.set('alt', node.thumbnail_alt);
                }
                if(node.thumbnail_title) {
                    img.set('title', node.thumbnail_title);
                }
                img.setStyle('maxWidth', ''+width+'px').setStyle('maxHeight', ''+height+'px');
                imgdiv.appendChild(img)

                var dynload = this.active_repo.dynload;
                if(node.children) {
                    element.on('click', function(e, p) {
                        e.preventDefault();
                        if(dynload) {
                            var params = {'path':p.path};
                            scope.list(params);
                        }else{
                            this.filelist = p.children;
                            this.view_files();
                        }
                    }, this, node);
                } else {
                    var fileinfo = {};
                    fileinfo['title'] = list[k].title;
                    fileinfo['source'] = list[k].source;
                    fileinfo['thumbnail'] = list[k].thumbnail;
                    fileinfo['haslicense'] = list[k].haslicense?true:false;
                    fileinfo['hasauthor'] = list[k].hasauthor?true:false;
                    element.on('click', function(e, args) {
                        e.preventDefault();
                        this.select_file(args);
                    }, this, fileinfo);
                }
                count++;
            }
        },
        select_file: function(args) {
            this.selectui.show();
            var client_id = this.options.client_id;
            var selectnode = this.fpnode.one('.fp-select');
            selectnode.one('#newname-'+client_id).set('value', args.title);
            selectnode.one('#text-author-'+client_id).set('value', this.options.author);

            var imgnode = Y.Node.create('<img/>').set('src', args.thumbnail)
            selectnode.one('#img-'+client_id).setContent('').appendChild(imgnode);

            selectnode.one('#linkexternal-'+client_id).set('checked', ''); // default to unchecked
            if ((this.options.externallink && this.options.env == 'editor' && this.options.return_types != 1)) {
                // enable checkbox 'Link external'
                selectnode.one('#linkexternal-'+client_id).set('disabled', '');
                selectnode.all('#linkexternal-'+client_id+',#wrap-linkexternal-'+client_id).removeClass('uneditable')
            } else {
                // disable checkbox 'Link external'
                selectnode.one('#linkexternal-'+client_id).set('disabled', 'disabled');
                selectnode.all('#linkexternal-'+client_id+',#wrap-linkexternal-'+client_id).addClass('uneditable')
                if (this.options.return_types == 1) {
                    // support external links only
                    selectnode.one('#linkexternal-'+client_id).set('checked', 'checked');
                }
            }

            if (args.hasauthor) {
                selectnode.one('#text-author-'+client_id).set('disabled', 'disabled');
                selectnode.all('#text-author-'+client_id+',#wrap-text-author-'+client_id).addClass('uneditable')
            } else {
                selectnode.one('#text-author-'+client_id).set('disabled', '');
                selectnode.all('#text-author-'+client_id+',#wrap-text-author-'+client_id).removeClass('uneditable')
            }

            if (!args.haslicense) {
                // the license of the file
                this.populate_licenses_select(selectnode.one('#select-license-'+client_id));
                selectnode.one('#wrap-select-license-'+client_id).set('disabled', '');
                selectnode.all('#select-license-'+client_id+'#wrap-select-license-'+client_id).removeClass('uneditable');
            } else {
                selectnode.one('#wrap-select-license-'+client_id).set('disabled', 'disabled');
                selectnode.all('#select-license-'+client_id+'#wrap-select-license-'+client_id).addClass('uneditable');
            }
            selectnode.one('form #filesource-'+client_id).set('value', args.source);
        },
        setup_select_file: function() {
            var client_id = this.options.client_id;
            var selectnode = this.fpnode.one('.fp-select');
            var getfile = selectnode.one('#fp-confirm-'+client_id);
            getfile.on('click', function(e) {
                e.preventDefault();
                var client_id = this.options.client_id;
                var scope = this;
                var repository_id = this.active_repo.id;
                var title = selectnode.one('#newname-'+client_id).get('value');
                var filesource = selectnode.one('form #filesource-'+client_id).get('value');
                var params = {'title':title, 'source':filesource, 'savepath': this.options.savepath};
                var license = selectnode.one('#select-license-'+client_id);
                if (license) {
                    params['license'] = license.get('value');
                    Y.Cookie.set('recentlicense', license.get('value'));
                }
                var author = selectnode.one('#text-author-'+client_id);
                if (author){
                    params['author'] = author.get('value');
                }

                if (this.options.externallink && this.options.env == 'editor') {
                    // in editor, images are stored in '/' only
                    params.savepath = '/';
                    // when image or media button is clicked
                    if ( this.options.return_types != 1 ) {
                        var linkexternal = selectnode.one('#linkexternal-'+client_id);
                        if (linkexternal && linkexternal.get('checked')) {
                            params['linkexternal'] = 'yes';
                        }
                    } else {
                        // when link button in editor clicked
                        params['linkexternal'] = 'yes';
                    }
                }

                if (this.options.env == 'url') {
                    params['linkexternal'] = 'yes';
                }

                // TODO show some waiting process here
                this.request({
                    action:'download',
                    client_id: client_id,
                    repository_id: repository_id,
                    'params': params,
                    onerror: function(id, obj, args) {
                        scope.selectui.hide();
                    },
                    callback: function(id, obj, args) {
                        if (scope.options.editor_target && scope.options.env=='editor') {
                            scope.options.editor_target.value=obj.url;
                            scope.options.editor_target.onchange();
                        }
                        scope.hide();
                        obj.client_id = client_id;
                        var formcallback_scope = null;
                        if (args.scope.options.magicscope) {
                            formcallback_scope = args.scope.options.magicscope;
                        } else {
                            formcallback_scope = args.scope;
                        }
                        scope.options.formcallback.apply(formcallback_scope, [obj]);
                    }
                }, false);
            }, this);
            var elform = selectnode.one('form');
            elform.appendChild(Y.Node.create('<input type="hidden"/>').set('id', 'filesource-'+client_id));
            elform.on('keydown', function(e) {
                if (e.keyCode == 13) {
                    getfile.simulate('click');
                    e.preventDefault();
                }
            }, this);
            var cancel = selectnode.one('#fp-cancel-'+client_id);
            cancel.on('click', function(e) {
                e.preventDefault();
                this.selectui.hide();
            }, this);
        },
        wait: function() {
            this.fpnode.one('.fp-content').setContent(M.core_filepicker.templates.loading);
        },
        viewbar_set_enabled: function(mode) {
            var viewbar = this.fpnode.one('.fp-viewbar')
            if (viewbar) {
                if (mode) {
                    viewbar.addClass('enabled').removeClass('disabled')
                } else {
                    viewbar.removeClass('enabled').addClass('disabled')
                }
            }
            this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').removeClass('checked')
            var modes = {1:'icons', 2:'tree', 3:'details'};
            this.fpnode.all('.fp-vb-'+modes[this.viewmode]).addClass('checked');
        },
        viewbar_clicked: function(e) {
            e.preventDefault();
            var viewbar = this.fpnode.one('.fp-viewbar')
            if (!viewbar || !viewbar.hasClass('disabled')) {
                if (e.currentTarget.hasClass('fp-vb-tree')) {
                    this.viewmode = 2;
                } else if (e.currentTarget.hasClass('fp-vb-details')) {
                    this.viewmode = 3;
                } else {
                    this.viewmode = 1;
                }
                this.viewbar_set_enabled(true)
                this.view_files();
                Y.Cookie.set('recentviewmode', this.viewmode);
            }
        },
        render: function() {
            var client_id = this.options.client_id;
            var nodecontent = M.core_filepicker.templates.generallayout.
                replace(/\{TOOLBARID}/g, 'fp-tb-'+client_id).
                replace(/\{TOOLBACKID}/g, 'fp-tb-back-'+client_id).
                replace(/\{TOOLSEARCHID}/g, 'fp-tb-search-'+client_id).
                replace(/\{TOOLREFRESHID}/g, 'fp-tb-refresh-'+client_id).
                replace(/\{TOOLLOGOUTID}/g, 'fp-tb-logout-'+client_id).
                replace(/\{TOOLMANAGEID}/g, 'fp-tb-manage-'+client_id).
                replace(/\{TOOLHELPID}/g, 'fp-tb-help-'+client_id);
            this.fpnode = Y.Node.create(nodecontent);
            this.fpnode.set('id', 'filepicker-'+client_id);
            var fpselectnode = Y.Node.create(M.core_filepicker.templates.selectlayout.
                replace(/\{IMGID}/g, 'img-'+client_id).
                replace(/\{NEWNAMEID}/g, 'newname-'+client_id).
                replace(/\{LINKEXTID}/g, 'linkexternal-'+client_id).
                replace(/\{AUTHORID}/g, 'text-author-'+client_id).
                replace(/\{LICENSEID}/g, 'select-license-'+client_id).
                replace(/\{BUTCONFIRMID}/g, 'fp-confirm-'+client_id).
                replace(/\{BUTCANCELID}/g, 'fp-cancel-'+client_id)
                );
            Y.one(document.body).appendChild(this.fpnode);
            this.fpnode.appendChild(fpselectnode);
            this.mainui = new Y.Panel({
                srcNode      : this.fpnode,
                headerContent: M.str.repository.filepicker,
                zIndex       : 500000,
                centered     : true,
                modal        : true,
                visible      : false,
                render       : true,
                plugins      : [Y.Plugin.Drag]
            });
            this.mainui.show();
            this.selectui = new Y.Panel({
                srcNode      : fpselectnode,
                zIndex       : 600000,
                centered     : true,
                modal        : true,
                close        : true,
                render       : true
            });
            this.selectui.hide();
            // save template for one path element and location of path bar
            if (this.fpnode.one('.fp-path-folder')) {
                this.pathnode = this.fpnode.one('.fp-path-folder');
                this.pathbar = this.pathnode.get('parentNode');
                this.pathbar.removeChild(this.pathnode);
            }
            // assign callbacks for view mode switch buttons
            this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').
                on('click', this.viewbar_clicked, this);
            // assign callbacks for toolbar links
            this.setup_toolbar();
            this.setup_select_file();

            // processing repository listing
            // Resort the repositories by sortorder
            var sorted_repositories = []
            for (var i in this.options.repositories) {
                sorted_repositories[i] = this.options.repositories[i]
            }
            sorted_repositories.sort(function(a,b){return a.sortorder-b.sortorder})
            // extract one repository template and repeat it for all repositories available,
            // set name and icon and assign callbacks
            var reponode = this.fpnode.one('.fp-repo');
            if (reponode) {
                var list = reponode.get('parentNode');
                list.removeChild(reponode);
                for (i in sorted_repositories) {
                    var repository = sorted_repositories[i]
                    var node = reponode.cloneNode(true);
                    list.appendChild(node);
                    node.
                        set('id', 'fp-repo-'+client_id+'-'+repository.id).
                        on('click', function(e, repository_id) {
                            e.preventDefault();
                            Y.Cookie.set('recentrepository', repository_id);
                            this.hide_header();
                            this.list({'repo_id':repository_id});
                        }, this /*handler running scope*/, repository.id/*second argument of handler*/);
                    node.one('.fp-repo-name').setContent(repository.name)
                    node.one('.fp-repo-icon').set('src', repository.icon)
                    if (i==0) {node.addClass('first');}
                    if (i==sorted_repositories.length-1) {node.addClass('last');}
                    if (i%2) {node.addClass('even');} else {node.addClass('odd');}
                }
            }
            // display error if no repositories found
            if (sorted_repositories.length==0) {
                if (this.options.externallink) {
                    list.set('innerHTML', M.str.repository.norepositoriesexternalavailable); // TODO as error
                } else {
                    list.set('innerHTML', M.str.repository.norepositoriesavailable); // TODO as error
                }
            }
            // display repository that was used last time
            this.show_recent_repository();
        },
        parse_repository_options: function(data) {
            this.filelist = data.list?data.list:null;
            this.filepath = data.path?data.path:null;
            this.active_repo = {};
            this.active_repo.issearchresult = data.issearchresult?true:false;
            this.active_repo.dynload = data.dynload?data.dynload:false;
            this.active_repo.pages = Number(data.pages?data.pages:null);
            this.active_repo.page = Number(data.page?data.page:null);
            this.active_repo.id = data.repo_id?data.repo_id:null;
            this.active_repo.nosearch = (data.login || data.nosearch); // this is either login form or 'nosearch' attribute set
            this.active_repo.norefresh = (data.login || data.norefresh); // this is either login form or 'norefresh' attribute set
            this.active_repo.nologin = (data.login || data.nologin); // this is either login form or 'nologin' attribute is set
            this.active_repo.logouttext = data.logouttext?data.logouttext:null;
            this.active_repo.help = data.help?data.help:null;
            this.active_repo.manage = data.manage?data.manage:null;
            this.print_header();
        },
        print_login: function(data) {
            // TODO !!!!
            this.parse_repository_options(data);
            var client_id = this.options.client_id;
            var repository_id = data.repo_id;
            var l = this.logindata = data.login;
            var loginurl = '';
            var panel = this.fpnode.one('.fp-content');
            var action = 'login';
            if (data['login_btn_action']) {
                action=data['login_btn_action'];
            }
            var form_id = 'fp-form-'+client_id;
            var download_button_id = 'fp-form-download-button-'+client_id;
            var search_button_id   = 'fp-form-search-button-'+client_id;
            var login_button_id    = 'fp-form-login-button-'+client_id;
            var popup_button_id    = 'fp-form-popup-button-'+client_id;

            var str = '<div class="fp-login-form">';
            str += '<form id="'+form_id+'">';
            var has_pop = false;
            str +='<table width="100%">';
            for(var k in l) {
                str +='<tr>';
                if(l[k].type=='popup') {
                    // pop element
                    loginurl = l[k].url;
                    str += '<td colspan="2"><p class="fp-popup">'+M.str.repository.popup+'</p>';
                    str += '<p class="fp-popup"><button id="'+popup_button_id+'">'+M.str.repository.login+'</button>';
                    str += '</p></td>';
                    action = 'popup';
                }else if(l[k].type=='textarea') {
                    // textarea element
                    str += '<td colspan="2"><p><textarea id="'+l[k].id+'" name="'+l[k].name+'"></textarea></p></td>';
                }else if(l[k].type=='select') {
                    // select element
                    str += '<td align="right"><label>'+l[k].label+':</label></td>';
                    str += '<td align="left"><select id="'+l[k].id+'" name="'+l[k].name+'">';
                    for (i in l[k].options) {
                        str += '<option value="'+l[k].options[i].value+'">'+l[k].options[i].label+'</option>';
                    }
                    str += '</select></td>';
                }else{
                    // input element
                    var label_id = '';
                    var field_id = '';
                    var field_value = '';
                    if(l[k].id) {
                        label_id = ' for="'+l[k].id+'"';
                        field_id = ' id="'+l[k].id+'"';
                    }
                    if (l[k].label) {
                        str += '<td align="right" width="30%" valign="center">';
                        str += '<label'+label_id+'>'+l[k].label+'</label> </td>';
                    } else {
                        str += '<td width="30%"></td>';
                    }
                    if(l[k].value) {
                        field_value = ' value="'+l[k].value+'"';
                    }
                    if(l[k].type=='radio'){
                        var list = l[k].value.split('|');
                        var labels = l[k].value_label.split('|');
                        str += '<td align="left">';
                        for(var item in list) {
                            str +='<input type="'+l[k].type+'"'+' name="'+l[k].name+'"'+
                                field_id+' value="'+list[item]+'" />'+labels[item]+'<br />';
                        }
                        str += '</td>';
                    }else{
                        str += '<td align="left">';
                        str += '<input type="'+l[k].type+'"'+' name="'+l[k].name+'"'+field_value+' '+field_id+' />';
                        str += '</td>';
                    }
                }
                str +='</tr>';
            }
            str +='</table>';
            str += '</form>';

            // custom lable text
            var btn_label = data['login_btn_label']?data['login_btn_label']:M.str.repository.submit;
            if (action != 'popup') {
                str += '<p><input type="button" id="';
                switch (action) {
                    case 'search':
                        str += search_button_id;
                        break;
                    case 'download':
                        str += download_button_id;
                        break;
                    default:
                        str += login_button_id;
                        break;
                }
                str += '" value="'+btn_label+'" /></p>';
            }

            str += '</div>';

            // insert login form
            try {
                panel.set('innerHTML', str);
            } catch(e) {
                alert(M.str.repository.xhtmlerror);
            }
            // register buttons
            // process login action
            var login_button = Y.one('#'+login_button_id);
            var scope = this;
            if (login_button) {
                login_button.on('click', function(){
                    // collect form data
                    var data = this.logindata;
                    var scope = this;
                    var params = {};
                    for (var k in data) {
                        if(data[k].type!='popup') {
                            var el = Y.one('[name='+data[k].name+']');
                            var type = el.get('type');
                            params[data[k].name] = '';
                            if(type == 'checkbox') {
                                params[data[k].name] = el.get('checked');
                            } else {
                                params[data[k].name] = el.get('value');
                            }
                        }
                    }
                    // start ajax request
                    this.hide_header();
                    this.request({
                        'params': params,
                        'scope': scope,
                        'action':'signin',
                        'path': '',
                        'client_id': client_id,
                        'repository_id': repository_id,
                        'callback': this.display_response
                    }, true);
                }, this);
            }
            var search_button = Y.one('#'+search_button_id);
            if (search_button) {
                search_button.on('click', function(){
                    var data = this.logindata;
                    var params = {};

                    for (var k in data) {
                        if(data[k].type!='popup') {
                            var el = document.getElementsByName(data[k].name)[0];
                            params[data[k].name] = '';
                            if(el.type == 'checkbox') {
                                params[data[k].name] = el.checked;
                            } else if(el.type == 'radio') {
                                var tmp = document.getElementsByName(data[k].name);
                                for(var i in tmp) {
                                    if (tmp[i].checked) {
                                        params[data[k].name] = tmp[i].value;
                                    }
                                }
                            } else {
                                params[data[k].name] = el.value;
                            }
                        }
                    }
                    this.hide_header();
                    this.request({
                            scope: scope,
                            action:'search',
                            client_id: client_id,
                            repository_id: repository_id,
                            form: {id: 'fp-form-'+scope.options.client_id,upload:false,useDisabled:true},
                            callback: scope.display_response
                    }, true);
                }, this);
            }
            var download_button = Y.one('#'+download_button_id);
            if (download_button) {
                download_button.on('click', function(){
                    alert('download');
                });
            }
            var popup_button = Y.one('#'+popup_button_id);
            if (popup_button) {
                popup_button.on('click', function(e){
                    M.core_filepicker.active_filepicker = this;
                    window.open(loginurl, 'repo_auth', 'location=0,status=0,width=500,height=300,scrollbars=yes');
                    e.preventDefault();
                }, this);
            }
            var elform = Y.one('#'+form_id);
            elform.on('keydown', function(e) {
                if (e.keyCode == 13) {
                    switch (action) {
                        case 'search':
                            search_button.simulate('click');
                            break;
                        default:
                            login_button.simulate('click');
                            break;
                    }
                    e.preventDefault();
                }
            }, this);

        },
        display_response: function(id, obj, args) {
            var scope = args.scope
            // highlight the current repository in repositories list
            scope.fpnode.all('.fp-repo.active').removeClass('active');
            scope.fpnode.all('#fp-repo-'+scope.options.client_id+'-'+obj.repo_id).addClass('active')
            // display response
            if (obj.login) {
                scope.viewbar_set_enabled(false);
                scope.print_login(obj);
            } else if (obj.upload) {
                scope.viewbar_set_enabled(false);
                scope.parse_repository_options(obj);
                scope.create_upload_form(obj);
            } else if (obj.iframe) {

            } else if (obj.list) {
                scope.viewbar_set_enabled(true);
                scope.parse_repository_options(obj);
                scope.view_files();
            }
        },
        list: function(args) {
            if (!args) {
                args = {};
            }
            if (!args.repo_id) {
                args.repo_id = this.active_repo.id;
            }
            this.request({
                action: 'list',
                client_id: this.options.client_id,
                repository_id: args.repo_id,
                path: args.path,
                page: args.page,
                scope: this,
                callback: this.display_response
            }, true);
        },
        populate_licenses_select: function(node) {
            if (!node) {
                return;
            }
            node.setContent('');
            var licenses = this.options.licenses;
            var recentlicense = Y.Cookie.get('recentlicense');
            if (recentlicense) {
                this.options.defaultlicense=recentlicense;
            }
            for (var i in licenses) {
                var option = Y.Node.create('<option/>').
                    set('selected', (this.options.defaultlicense==licenses[i].shortname)).
                    set('value', licenses[i].shortname).
                    setContent(licenses[i].fullname);
                node.appendChild(option)
            }
        },
        create_upload_form: function(data) {
            var client_id = this.options.client_id;
            var id = data.upload.id+'_'+client_id;
            var str = M.core_filepicker.templates.uploadform.
                replace(/\{UPLOADFORMID}/g, id).
                replace(/\{INPUTFILEID}/g, id+'_file').
                replace(/\{NEWNAMEID}/g, 'newname-'+client_id).
                replace(/\{AUTHORID}/g, 'author-'+client_id).
                replace(/\{LICENSEID}/g, 'license-'+client_id).
                replace(/\{BUTUPLOADID}/g, id+'_action');
            this.fpnode.one('.fp-content').setContent(str);

            Y.all('#'+id+'_file').set('name', 'repo_upload_file');
            Y.all('#'+'newname-'+client_id).set('name', 'title');
            Y.all('#'+'author-'+client_id).set('name', 'author');
            Y.all('#'+'author-'+client_id).set('value', this.options.author);
            Y.all('#'+'license-'+client_id).set('name', 'license');
            this.populate_licenses_select(Y.one('#'+'license-'+client_id))
            Y.one('#'+id).append('<input type="hidden" name="itemid" value="'+this.options.itemid+'" />'); // TODO nicer!
            var types = this.options.accepted_types;
            for (var i in types) {
                Y.one('#'+id).append('<input type="hidden" name="accepted_types[]" value="'+types[i]+'" />'); // TODO nicer!
            }

            var scope = this;
            Y.one('#'+id+'_action').on('click', function(e) {
                e.preventDefault();
                var license = Y.one('#license-'+client_id);
                Y.Cookie.set('recentlicense', license.get('value'));
                if (!Y.one('#'+id+'_file').get('value')) {
                    scope.print_msg(M.str.repository.nofilesattached, 'error');
                    return false;
                }
                this.hide_header();
                scope.request({
                        scope: scope,
                        action:'upload',
                        client_id: client_id,
                        params: {'savepath':scope.options.savepath},
                        repository_id: scope.active_repo.id,
                        form: {id: id, upload:true},
                        onerror: function(id, o, args) {
                            scope.create_upload_form(data);
                        },
                        callback: function(id, o, args) {
                            if (scope.options.editor_target&&scope.options.env=='editor') {
                                scope.options.editor_target.value=o.url;
                                scope.options.editor_target.onchange();
                            }
                            scope.hide();
                            o.client_id = client_id;
                            var formcallback_scope = null;
                            if (args.scope.options.magicscope) {
                                formcallback_scope = args.scope.options.magicscope;
                            } else {
                                formcallback_scope = args.scope;
                            }
                            scope.options.formcallback.apply(formcallback_scope, [o]);
                        }
                }, true);
            }, this);
        },
        /** setting handlers and labels for elements in toolbar. Called once during the initial render of filepicker */
        setup_toolbar: function() {
            var client_id = this.options.client_id;
            Y.one('#fp-tb-logout-'+client_id).on('click', function(e) {
                e.preventDefault();
                if (!this.active_repo.nologin) {
                    this.hide_header();
                    this.request({
                        action:'logout',
                        client_id: this.options.client_id,
                        repository_id: this.active_repo.id,
                        path:'',
                        callback: this.display_response
                    }, true);
                }
            }, this);
            Y.one('#fp-tb-refresh-'+client_id).on('click', function(e) {
                e.preventDefault();
                if (!this.active_repo.norefresh) {
                    this.list();
                }
            }, this);
            Y.one('#fp-tb-search-'+client_id).
                set('method', 'POST').
                on('submit', function(e) {
                    e.preventDefault();
                    if (!this.active_repo.nosearch) {
                        this.request({
                            scope: this,
                            action:'search',
                            client_id: this.options.client_id,
                            repository_id: this.active_repo.id,
                            form: {id: 'fp-tb-search-'+client_id, upload:false, useDisabled:true},
                            callback: this.display_response
                        }, true);
                    }
                }, this);

            // it does not matter what kind of element is {TOOLMANAGEID}, we create a dummy <a>
            // element and use it to open url on click event
            var managelnk = Y.Node.create('<a/>');
            managelnk.set('id', 'fp-tb-manage-'+client_id+'-link').set('target', '_blank').setStyle('display', 'none');
            Y.one('#fp-tb-'+client_id).append(managelnk);
            Y.one('#fp-tb-manage-'+client_id).on('click', function(e) {
                e.preventDefault();
                managelnk.simulate('click')
            });

            // same with {TOOLHELPID}
            var helplnk = Y.Node.create('<a/>');
            helplnk.set('id', 'fp-tb-help-'+client_id+'-link').set('target', '_blank').setStyle('display', 'none');
            Y.one('#fp-tb-'+client_id).append(helplnk);
            Y.one('#fp-tb-help-'+client_id).on('click', function(e) {
                e.preventDefault();
                helplnk.simulate('click')
            });
        },
        hide_header: function() {
            var client_id = this.options.client_id;
            if (Y.one('#fp-tb-'+client_id)) {
                Y.one('#fp-tb-'+client_id).addClass('empty');
            }
        },
        print_header: function() {
            var r = this.active_repo;
            var scope = this;
            var client_id = this.options.client_id;
            this.print_paging();

            this.hide_header();
            var enable_tb_control = function(elementid, enabled) {
                if (!enabled) {
                    Y.all('#'+elementid+',#wrap-'+elementid).addClass('disabled').removeClass('enabled')
                } else {
                    Y.all('#'+elementid+',#wrap-'+elementid).removeClass('disabled').addClass('enabled')
                    Y.one('#fp-tb-'+client_id).removeClass('empty');
                }
            }

            // TODO 'back' permanently disabled for now. Note, flickr_public uses 'Logout' for it!
            enable_tb_control('fp-tb-back-'+client_id, false);

            // search form
            enable_tb_control('fp-tb-search-'+client_id, !r.nosearch);
            if(!r.nosearch) {
                Y.all('#fp-tb-search-'+client_id).setContent('');
                this.request({
                    scope: this,
                    action:'searchform',
                    repository_id: this.active_repo.id,
                    callback: function(id, obj, args) {
                        if (obj.repo_id == scope.active_repo.id && obj.form) {
                            // if we did not jump to another repository meanwhile
                            Y.all('#fp-tb-search-'+scope.options.client_id).setContent(obj.form);
                        }
                    }
                }, false);
            }

            // refresh button
            // weather we use cache for this instance, this button will reload listing anyway
            enable_tb_control('fp-tb-refresh-'+client_id, !r.norefresh);

            // login button
            enable_tb_control('fp-tb-logout-'+client_id, !r.nologin);
            if(!r.nologin) {
                var label = r.logouttext?r.logouttext:M.str.repository.logout;
                Y.one('#fp-tb-logout-'+client_id).setContent(label)
            }

            // manage url
            enable_tb_control('fp-tb-manage-'+client_id, r.manage);
            Y.one('#fp-tb-manage-'+client_id+'-link').set('href', r.manage);

            // help url
            enable_tb_control('fp-tb-help-'+client_id, r.help);
            Y.one('#fp-tb-help-'+client_id+'-link').set('href', r.help);

            this.print_path();
        },
        get_page_button: function(page) {
            var r = this.active_repo;
            var css = '';
            if (page == r.page) {
                css = 'class="cur_page" ';
            }
            var str = '<a '+css+'href="###" id="repo-page-'+page+'">';
            return str;
        },
        print_paging: function(html_id) {
            // TODO !!!
            var client_id = this.options.client_id;
            var scope = this;
            var r = this.active_repo;
            var str = '';
            var action = '';
            var lastpage = r.pages;
            var lastpagetext = r.pages;
            if (r.pages == -1) {
                lastpage = r.page + 1;
                lastpagetext = M.str.moodle.next;
            }
            if (lastpage > 1) {
                str += this.get_page_button(1)+'1</a> ';

                var span = 5;
                var ex = (span-1)/2;

                if (r.page+ex>=lastpage) {
                    var max = lastpage;
                } else {
                    if (r.page<span) {
                        var max = span;
                    } else {
                        var max = r.page+ex;
                    }
                }

                // won't display upper boundary
                if (r.page >= span) {
                    str += ' ... ';
                    for(var i=r.page-ex; i<max; i++) {
                        str += this.get_page_button(i);
                        str += String(i);
                        str += '</a> ';
                    }
                } else {
                    // this very first elements
                    for(var i = 2; i < max; i++) {
                        str += this.get_page_button(i);
                        str += String(i);
                        str += '</a> ';
                    }
                }

                // won't display upper boundary
                if (max==lastpage) {
                    str += this.get_page_button(lastpage)+lastpagetext+'</a>';
                } else {
                    str += this.get_page_button(max)+max+'</a>';
                    str += ' ... '+this.get_page_button(lastpage)+lastpagetext+'</a>';
                }
            }
            this.fpnode.one('.fp-paging').setContent(str);
            this.fpnode.all('.fp-paging a').on('click', function(e) {
                            e.preventDefault();
                            var id = e.currentTarget.get('id');
                            var re = new RegExp("repo-page-(\\d+)", "i");
                            var result = id.match(re);
                            var args = {};
                            args.page = result[1];
                            if (scope.active_repo.issearchresult) {
                                scope.request({
                                        scope: scope,
                                        action:'search',
                                        client_id: client_id,
                                        repository_id: r.id,
                                        params: {'page':result[1]},
                                        callback: scope.display_response
                                }, true);

                            } else {
                                scope.list(args);
                            }
                        });
        },
        print_path: function() {
            if (!this.pathbar) { return; }
            this.pathbar.setContent('');
            var p = this.filepath;
            if (p && p.length!=0) {
                for(var i = 0; i < p.length; i++) {
                    var el = this.pathnode.cloneNode(true);
                    this.pathbar.appendChild(el);
                    if (i == 0) {el.addClass('first');}
                    if (i == p.length-1) {el.addClass('last');}
                    if (i%2) {el.addClass('even');} else {el.addClass('odd');}
                    el.all('.fp-path-folder-name').setContent(p[i].name);
                    el.on('click',
                            function(e, path) {
                                e.preventDefault();
                                this.list({'path':path});
                            },
                        this, p[i].path);
                }
                this.pathbar.removeClass('empty');
            } else {
                this.pathbar.addClass('empty');
            }
        },
        hide: function() {
            this.selectui.hide();
            if (this.process_dlg) {
                this.process_dlg.hide();
            }
            if (this.msg_dlg) {
                this.msg_dlg.hide();
            }
            this.mainui.hide();
        },
        show: function() {
            if (this.fpnode) {
                this.hide();
                this.mainui.show();
                this.show_recent_repository();
            } else {
                this.launch();
            }
        },
        launch: function() {
            this.render();
        },
        show_recent_repository: function() {
            this.hide_header();
            this.viewbar_set_enabled(false);
            var repository_id = Y.Cookie.get('recentrepository');
            this.viewmode = Y.Cookie.get('recentviewmode', Number);
            if (this.viewmode != 1 && this.viewmode != 2 && this.viewmode != 3) {
                this.viewmode = 1;
            }
            if (this.options.repositories[repository_id]) {
                this.list({'repo_id':repository_id});
            }
        }
    });
    var loading = Y.one('#filepicker-loading-'+options.client_id);
    if (loading) {
        loading.setStyle('display', 'none');
    }
    M.core_filepicker.instances[options.client_id] = new FilePickerHelper(options);
};
