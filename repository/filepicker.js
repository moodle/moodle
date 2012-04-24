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
    /** help function to extract width/height style as a number, not as a string */
    Y.Node.prototype.getStylePx = function(attr) {
        var style = this.getStyle(attr);
        if (''+style == '0' || ''+style == '0px') {
            return 0;
        }
        var matches = style.match(/^([\d\.]+)px$/)
        if (matches && parseFloat(matches[1])) {
            return parseFloat(matches[1]);
        }
        return null;
    }

    /** if condition is met, the class is added to the node, otherwise - removed */
    Y.Node.prototype.addClassIf = function(className, condition) {
        if (condition) {
            this.addClass(className);
        } else {
            this.removeClass(className);
        }
        return this;
    }

    /** sets the width(height) of the node considering existing minWidth(minHeight) */
    Y.Node.prototype.setStyleAdv = function(stylename, value) {
        var stylenameCap = stylename.substr(0,1).toUpperCase() + stylename.substr(1, stylename.length-1).toLowerCase();
        this.setStyle(stylename, '' + Math.max(value, this.getStylePx('min'+stylenameCap)) + 'px')
        return this;
    }

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
            var api = (args.api?args.api:this.api) + '?action='+args.action;
            var params = {};
            var scope = args['scope']?args['scope']:this;
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
                                this.fpnode.one('.fp-content').setContent('');
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
            this.fpnode.one('.fp-select').removeClass('loading');
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
            var dynload = this.active_repo.dynload;
            // prepare file name with icon
            var el = Y.Node.create('<div/>').setContent(M.core_filepicker.templates.listfilename);
            el.one('.fp-filename').setContent(node.shorttitle ? node.shorttitle : node.title);
            // TODO add tooltip with node.title or node.thumbnail_title
            if (node.icon && !node.children) {
                el.one('.fp-icon').appendChild(Y.Node.create('<img/>').set('src', node.icon));
                if (node.realicon) {
                    this.lazyloading[el.one('.fp-icon img').generateID()] = node.realicon;
                }
            }
            // create node
            var tmpNode = new YAHOO.widget.HTMLNode(el.getContent(), level, false);
            if (node.dynamicLoadComplete) {
                tmpNode.dynamicLoadComplete = true;
            }
            tmpNode.fileinfo = node;
            tmpNode.isLeaf = node.children ? false : true;
            if (!tmpNode.isLeaf) {
                if(node.expanded) {
                    tmpNode.expand();
                }
                if (dynload) {
                    tmpNode.scope = this;
                }
                tmpNode.path = node.path ? node.path : '';
                for(var c in node.children) {
                    this.build_tree(node.children[c], tmpNode);
                }
            }
        },
        view_files: function(appenditems) {
            this.viewbar_set_enabled(true);
            this.print_path();
            if (this.viewmode == 2) {
                this.view_as_list(appenditems);
            } else if (this.viewmode == 3) {
                this.view_as_table(appenditems);
            } else {
                this.view_as_icons(appenditems);
            }
            // display/hide the link for requesting next page
            if (!appenditems && this.active_repo.hasmorepages) {
                if (!this.fpnode.one('.fp-content .fp-nextpage')) {
                    this.fpnode.one('.fp-content').append(M.core_filepicker.templates.nextpage);
                }
                this.fpnode.one('.fp-content .fp-nextpage').one('a,button').on('click', function(e) {
                    e.preventDefault();
                    this.fpnode.one('.fp-content .fp-nextpage').addClass('loading');
                    this.request_next_page();
                }, this);
            }
            if (!this.active_repo.hasmorepages && this.fpnode.one('.fp-content .fp-nextpage')) {
                this.fpnode.one('.fp-content .fp-nextpage').remove();
            }
            if (this.fpnode.one('.fp-content .fp-nextpage')) {
                this.fpnode.one('.fp-content .fp-nextpage').removeClass('loading');
            }
            this.content_scrolled();
        },
        content_scrolled: function(e) {
            setTimeout(Y.bind(function() {
                if (this.processingimages) {return;}
                this.processingimages = true;
                var scope = this,
                    fpcontent = this.fpnode.one('.fp-content'),
                    fpcontenty = fpcontent.getY(),
                    fpcontentheight = fpcontent.getStylePx('height'),
                    nextpage = fpcontent.one('.fp-nextpage'),
                    is_node_visible = function(node) {
                        var offset = node.getY()-fpcontenty;
                        if (offset <= fpcontentheight && (offset >=0 || offset+node.getStylePx('height')>=0)) {
                            return true;
                        }
                        return false;
                    };
                // automatically load next page when 'more' link becomes visible
                if (nextpage && !nextpage.hasClass('loading') && is_node_visible(nextpage)) {
                    nextpage.one('a,button').simulate('click');
                }
                // replace src for visible images that need to be lazy-loaded
                if (scope.lazyloading) {
                    fpcontent.all('img').each( function(node) {
                        if (node.get('id') && scope.lazyloading[node.get('id')] && is_node_visible(node)) {
                            node.set('src', scope.lazyloading[node.get('id')]);
                            delete scope.lazyloading[node.get('id')];
                        }
                    });
                }
                this.processingimages = false;
            }, this), 200)
        },
        treeview_dynload: function(node, cb) {
            var scope = node.scope;
            var client_id = scope.options.client_id;
            var repository_id = scope.active_repo.id;
            var retrieved_children = {};
            if (node.children) {
                for (var i in node.children) {
                    retrieved_children[node.children[i].path] = node.children[i];
                }
            }
            scope.request({
                action:'list',
                client_id: client_id,
                repository_id: repository_id,
                path:node.path?node.path:'',
                page:node.page?args.page:'',
                callback: function(id, obj, args) {
                    var list = obj.list;
                    // check that user did not leave the view mode before recieving this response
                    if (!(scope.active_repo.id == obj.repo_id && scope.viewmode == 2 && node && node.getChildrenEl())) {
                        return;
                    }
                    if (cb != null) { // (in manual mode do not update current path)
                        scope.viewbar_set_enabled(true);
                        scope.parse_repository_options(obj);
                        node.highlight(false);
                    }
                    node.origlist = obj.list?obj.list:null;
                    node.origpath = obj.path?obj.path:null;
                    node.children = [];
                    for(k in list) {
                        if (list[k].children && retrieved_children[list[k].path]) {
                            // if this child is a folder and has already been retrieved
                            node.children[node.children.length] = retrieved_children[list[k].path];
                        } else {
                            scope.build_tree(list[k], node);
                        }
                    }
                    if (cb == null) {
                        node.refresh();
                    } else {
                        // invoke callback requested by TreeView
                        cb();
                    }
                    scope.content_scrolled();
                }
            }, false);
        },
        /** displays list of files in tree (list) view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_list: function(appenditems) {
            var scope = this;
            var client_id = scope.options.client_id;
            var dynload = scope.active_repo.dynload;
            var list = this.filelist;
            scope.viewmode = 2;
            if (appenditems) {
                var parentnode = scope.treeview.getRoot();
                if (scope.treeview.getHighlightedNode()) {
                    parentnode = scope.treeview.getHighlightedNode();
                    if (parentnode.isLeaf) {parentnode = parentnode.parent;}
                }
                for (var k in appenditems) {
                    scope.build_tree(appenditems[k], parentnode);
                }
                scope.treeview.draw();
                return;
            }
            if (!list || list.length==0 && (!this.filepath || !this.filepath.length)) {
                this.display_error(M.str.repository.nofilesavailable, 'nofilesavailable');
                return;
            }

            var treeviewnode = Y.Node.create('<div/>').
                setAttrs({'class':'fp-treeview', id:'treeview-'+client_id});
            this.fpnode.one('.fp-content').setContent('').appendChild(treeviewnode);

            scope.treeview = new YAHOO.widget.TreeView('treeview-'+client_id);
            if (dynload) {
                scope.treeview.setDynamicLoad(scope.treeview_dynload, 1);
            }
            scope.treeview.singleNodeHighlight = true;
            if (scope.filepath && scope.filepath.length) {
                // we just jumped from icon/details view, we need to show all parents
                // we extract as much information as possible from filepath and filelist
                // and send additional requests to retrieve siblings for parent folders
                var mytree = {};
                var mytreeel = null;
                for (var i in scope.filepath) {
                    if (mytreeel == null) {
                        mytreeel = mytree;
                    } else {
                        mytreeel.children = [{}];
                        mytreeel = mytreeel.children[0];
                    }
                    var parent = scope.filepath[i];
                    mytreeel.path = parent.path;
                    mytreeel.title = parent.name;
                    mytreeel.dynamicLoadComplete = true; // we will call it manually
                    mytreeel.expanded = true;
                }
                mytreeel.children = scope.filelist
                scope.build_tree(mytree, scope.treeview.getRoot());
                // manually call dynload for parent elements in the tree so we can load other siblings
                if (dynload) {
                    var root = scope.treeview.getRoot();
                    while (root && root.children && root.children.length) {
                        root = root.children[0];
                        if (root.path == mytreeel.path) {
                            root.origpath = scope.filepath;
                            root.origlist = scope.filelist;
                        } else if (!root.isLeaf && root.expanded) {
                            scope.treeview_dynload(root, null);
                        }
                    }
                }
            } else {
                // there is no path information, just display all elements as a list, without hierarchy
                for(k in list) {
                    scope.build_tree(list[k], scope.treeview.getRoot());
                }
            }
            scope.treeview.subscribe('clickEvent', function(e){
                e.node.highlight(false);
                if(e.node.isLeaf){
                    if (e.node.parent && e.node.parent.origpath) {
                        // set the current path
                        scope.filepath = e.node.parent.origpath;
                        scope.filelist = e.node.parent.origlist;
                        scope.print_path();
                    }
                    scope.select_file(e.node.fileinfo);
                } else {
                    // save current path and filelist (in case we want to jump to other viewmode)
                    scope.filepath = e.node.origpath;
                    scope.filelist = e.node.origlist;
                    scope.print_path();
                    scope.content_scrolled();
                }
            });
            scope.treeview.draw();
        },
        /** displays list of files in icon view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_icons: function(appenditems) {
            var scope = this;
            this.viewmode = 1;
            var list = this.filelist, container, element_template;
            if (!appenditems || !this.filelist || !this.filelist.length) {
                if (!list || list.length==0) {
                    this.display_error(M.str.repository.nofilesavailable, 'nofilesavailable');
                    return;
                }
                this.fpnode.one('.fp-content').setContent(M.core_filepicker.templates.iconview);
                element_template = this.fpnode.one('.fp-content').one('.fp-file');
                container = element_template.get('parentNode');
                container.removeChild(element_template);
            } else {
                list = appenditems;
                element_template = Y.Node.create(M.core_filepicker.templates.iconview).one('.fp-file');
                container = this.fpnode.one('.fp-content').one('.fp-file').get('parentNode')
            }

            var count = 0;
            for(var k in list) {
                var node = list[k];
                var element = element_template.cloneNode(true);
                container.appendChild(element);
                var filename = node.shorttitle ? node.shorttitle : node.title;
                var filenamediv = element.one('.fp-filename');
                filenamediv.setContent(filename);
                var imgdiv = element.one('.fp-thumbnail');
                var width = node.thumbnail_width ? node.thumbnail_width : 90;
                var height = node.thumbnail_height ? node.thumbnail_height : 90;
                filenamediv.setStyleAdv('width', width);
                imgdiv.setStyleAdv('width', width).setStyleAdv('height', height);
                var img = Y.Node.create('<img/>').setAttrs({src:node.thumbnail,title:node.title});
                if(node.thumbnail_alt) {
                    img.set('alt', node.thumbnail_alt);
                }
                if(node.thumbnail_title) {
                    img.set('title', node.thumbnail_title);
                }
                img.setStyle('maxWidth', ''+width+'px').setStyle('maxHeight', ''+height+'px');
                if (node.realthumbnail) {
                    this.lazyloading[img.generateID()] = node.realthumbnail;
                }
                imgdiv.appendChild(img)

                var dynload = this.active_repo.dynload;
                if(node.children) {
                    element.on('click', function(e, p) {
                        e.preventDefault();
                        if(dynload) {
                            scope.list({'path':p.path});
                        }else{
                            this.filepath = p.path;
                            this.filelist = p.children;
                            this.view_files();
                        }
                    }, this, node);
                } else {
                    element.on('click', function(e, args) {
                        e.preventDefault();
                        this.select_file(args);
                    }, this, list[k]);
                }
                count++;
            }
        },
        /** displays list of files in table view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_table: function(appenditems) {
            var list = this.filelist, scope = this;
            var client_id = this.options.client_id;
            this.viewmode = 3;
            if (appenditems != null) {
                this.tableview.addRows(appenditems);
                this.tableview.sortable = !this.active_repo.hasmorepages;
                return;
            }

            if (!list || list.length==0) {
                this.display_error(M.str.repository.nofilesavailable, 'nofilesavailable');
                return;
            }
            var treeviewnode = Y.Node.create('<div/>').
                    setAttrs({'class':'fp-tableview', id:'tableview-'+client_id});
            this.fpnode.one('.fp-content').setContent('').appendChild(treeviewnode);
            var formatValue = function (o){
                if (o.data[''+o.column.key+'_f_s']) { return o.data[''+o.column.key+'_f_s']; }
                else if (o.data[''+o.column.key+'_f']) { return o.data[''+o.column.key+'_f']; }
                else if (o.value) { return o.value; }
                else { return ''; }
            };
            var formatTitle = function(o) {
                var el = Y.Node.create('<div/>').setContent(M.core_filepicker.templates.listfilename);
                el.one('.fp-filename').setContent(o.data['shorttitle'] ? o.data['shorttitle'] : o.value);
                el.one('.fp-icon').appendChild(Y.Node.create('<img/>').set('src', o.data['icon']));
                if (o.data['realicon']) {
                    scope.lazyloading[el.one('.fp-icon img').generateID()] = o.data['realicon'];
                }
                // TODO add tooltip with o.data['title'] (o.value) or o.data['thumbnail_title']
                return el.getContent();
            }
            var sortFoldersFirst = function(a, b, desc) {
                if (a.get('children') && !b.get('children')) {return -1;}
                if (!a.get('children') && b.get('children')) {return 1;}
                var aa = a.get(this.key), bb = b.get(this.key), dir = desc?-1:1;
                if (this.key == 'title' && a.get('shorttitle')) {aa=a.get('shorttitle');}
                if (this.key == 'title' && b.get('shorttitle')) {bb=b.get('shorttitle');}
                return (aa > bb) ? dir : ((aa < bb) ? -dir : 0);
            }

            var cols = [
                {key: "title", label: M.str.moodle.name, allowHTML: true, formatter: formatTitle,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "datemodified", label: M.str.moodle.lastmodified, allowHTML: true, formatter: formatValue,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "size", label: M.str.repository.size, allowHTML: true, formatter: formatValue,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "type", label: M.str.repository.type, allowHTML: true,
                    sortable: true, sortFn: sortFoldersFirst}
            ];
            this.tableview = new Y.DataTable({
                columns: cols,
                data: list,
                sortable: !this.active_repo.hasmorepages // allow sorting only if there are no more pages to load
            });

            this.tableview.render('#tableview-'+client_id);
            this.tableview.delegate('click', function (e) {
                var record = this.tableview.getRecord(e.currentTarget.get('id'));
                if (record) {
                    var data = record.getAttrs();
                    if (data.children) {
                        if (this.active_repo.dynload) {
                            this.list({'path':data.path});
                        } else {
                            this.filepath = data.path;
                            this.filelist = data.children;
                            this.view_files();
                        }
                    } else {
                        this.select_file(data);
                    }
                }
            }, 'tr', this);
        },
        /** If more than one page available, requests and displays the files from the next page */
        request_next_page: function() {
            if (!this.active_repo.hasmorepages || this.active_repo.nextpagerequested) {
                // nothing to load
                return;
            }
            this.active_repo.nextpagerequested = true;
            var nextpage = this.active_repo.page+1;
            var args = {page:nextpage, repo_id:this.active_repo.id, path:this.active_repo.path};
            var action = this.active_repo.issearchresult ? 'search' : 'list';
            this.request({
                scope: this,
                action: action,
                client_id: this.options.client_id,
                repository_id: args.repo_id,
                params: args,
                callback: function(id, obj, args) {
                    var scope = args.scope;
                    // check that we are still in the same repository and are expecting this page
                    if (scope.active_repo.hasmorepages && obj.list && obj.page &&
                            obj.repo_id == scope.active_repo.id &&
                            obj.page == scope.active_repo.page+1 && obj.path == scope.path) {
                        scope.parse_repository_options(obj, true);
                        scope.view_files(obj.list)
                    }
                }
            }, false);
        },
        select_file: function(args) {
            this.selectui.show();
            var client_id = this.options.client_id;
            var selectnode = this.fpnode.one('.fp-select');
            var return_types = this.options.repositories[this.active_repo.id].return_types;
            selectnode.removeClass('loading');
            selectnode.one('.fp-saveas input').set('value', args.title);
            selectnode.one('.fp-setauthor input').set('value', this.options.author);

            var imgnode = Y.Node.create('<img/>').
                set('src', args.realthumbnail ? args.realthumbnail : args.thumbnail).
                setStyle('maxHeight', ''+(args.thumbnail_height ? args.thumbnail_height : 90)+'px').
                setStyle('maxWidth', ''+(args.thumbnail_width ? args.thumbnail_width : 90)+'px');
            selectnode.one('.fp-thumbnail').setContent('').appendChild(imgnode);

            selectnode.one('.fp-linkexternal input').set('checked', ''); // default to unchecked
            if ((this.options.externallink && this.options.env == 'editor' && return_types == 3)) {
                // support both internal and external links, enable checkbox 'Link external'
                selectnode.one('.fp-linkexternal input').set('disabled', '');
                selectnode.all('.fp-linkexternal').removeClass('uneditable')
            } else {
                // disable checkbox 'Link external'
                selectnode.one('.fp-linkexternal input').set('disabled', 'disabled');
                selectnode.all('.fp-linkexternal').addClass('uneditable')
                if (return_types == 1) {
                    // support external links only
                    selectnode.one('.fp-linkexternal input').set('checked', 'checked');
                }
            }

            if (args.hasauthor) {
                selectnode.one('.fp-setauthor input').set('disabled', 'disabled');
                selectnode.all('.fp-setauthor').addClass('uneditable')
            } else {
                selectnode.one('.fp-setauthor input').set('disabled', '');
                selectnode.all('.fp-setauthor').removeClass('uneditable')
            }

            if (!args.haslicense) {
                // the license of the file
                selectnode.one('.fp-setlicense select').set('disabled', '');
                selectnode.one('.fp-setlicense').removeClass('uneditable');
            } else {
                selectnode.one('.fp-setlicense select').set('disabled', 'disabled');
                selectnode.one('.fp-setlicense').addClass('uneditable');
            }

            selectnode.one('form #filesource-'+client_id).set('value', args.source);

            // display static information about a file (when known)
            var attrs = ['datemodified','datecreated','size','license','author','dimensions'];
            for (var i in attrs) {
                if (selectnode.one('.fp-'+attrs[i])) {
                    var value = (args[attrs[i]+'_f']) ? args[attrs[i]+'_f'] : (args[attrs[i]] ? args[attrs[i]] : '');
                    selectnode.one('.fp-'+attrs[i]).addClassIf('fp-unknown', ''+value == '')
                        .one('.fp-value').setContent(value);
                }
            }
        },
        setup_select_file: function() {
            var client_id = this.options.client_id;
            var selectnode = this.fpnode.one('.fp-select');
            var getfile = selectnode.one('.fp-select-confirm');
            // bind labels with corresponding inputs
            selectnode.all('.fp-saveas,.fp-linkexternal,.fp-setauthor,.fp-setlicense').each(function (node) {
                node.all('label').set('for', node.one('input,select').generateID());
            });
            this.populate_licenses_select(selectnode.one('.fp-setlicense select'));
            // register event on clicking submit button
            getfile.on('click', function(e) {
                e.preventDefault();
                var client_id = this.options.client_id;
                var scope = this;
                var repository_id = this.active_repo.id;
                var title = selectnode.one('.fp-saveas input').get('value');
                var filesource = selectnode.one('form #filesource-'+client_id).get('value');
                var params = {'title':title, 'source':filesource, 'savepath': this.options.savepath};
                var license = selectnode.one('.fp-setlicense select');
                if (license) {
                    params['license'] = license.get('value');
                    Y.Cookie.set('recentlicense', license.get('value'));
                }
                params['author'] = selectnode.one('.fp-setauthor input').get('value');

                if (this.options.externallink && this.options.env == 'editor') {
                    // in editor, images are stored in '/' only
                    params.savepath = '/';
                    // when image or media button is clicked
                    var return_types = this.options.repositories[this.active_repo.id].return_types;
                    if ( return_types != 1 ) {
                        var linkexternal = selectnode.one('.fp-linkexternal input');
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

                selectnode.addClass('loading');
                this.request({
                    action:'download',
                    client_id: client_id,
                    repository_id: repository_id,
                    'params': params,
                    onerror: function(id, obj, args) {
                        selectnode.removeClass('loading');
                        scope.selectui.hide();
                    },
                    callback: function(id, obj, args) {
                        selectnode.removeClass('loading');
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
            elform.appendChild(Y.Node.create('<input/>').
                setAttrs({type:'hidden',id:'filesource-'+client_id}));
            elform.on('keydown', function(e) {
                if (e.keyCode == 13) {
                    getfile.simulate('click');
                    e.preventDefault();
                }
            }, this);
            var cancel = selectnode.one('.fp-select-cancel');
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
            this.fpnode = Y.Node.create(M.core_filepicker.templates.generallayout).
                set('id', 'filepicker-'+client_id);
            var fpselectnode = Y.Node.create(M.core_filepicker.templates.selectlayout);
            Y.one(document.body).appendChild(this.fpnode);
            this.fpnode.appendChild(fpselectnode);
            this.mainui = new Y.Panel({
                srcNode      : this.fpnode,
                headerContent: M.str.repository.filepicker,
                zIndex       : 500000,
                centered     : true,
                modal        : true,
                visible      : false,
                render       : true
            });
            // allow to move the panel dragging it by it's header:
            this.mainui.plug(Y.Plugin.Drag,{handles:['.yui3-widget-hd']});
            // Check if CSS for the node sets min-max width/height and therefore if panel shall be resizable:
            var resizeconstraints = {
              minWidth: this.fpnode.getStylePx('minWidth')?this.fpnode.getStylePx('minWidth'):this.fpnode.getStylePx('width'),
              minHeight: this.fpnode.getStylePx('minHeight')?this.fpnode.getStylePx('minHeight'):this.fpnode.getStylePx('height'),
              maxWidth: this.fpnode.getStylePx('maxWidth')?this.fpnode.getStylePx('maxWidth'):this.fpnode.getStylePx('width'),
              maxHeight: this.fpnode.getStylePx('maxHeight')?this.fpnode.getStylePx('maxHeight'):this.fpnode.getStylePx('height'),
              preserveRatio: false
            };
            if (resizeconstraints.minWidth < resizeconstraints.maxWidth ||
                    resizeconstraints.minHeight < resizeconstraints.maxHeight) {
                this.mainui.plug(Y.Plugin.Resize)
                this.mainui.resize.plug(Y.Plugin.ResizeConstrained, resizeconstraints);
            }
            this.mainui.show();
            // create panel for selecting a file (initially hidden)
            this.selectui = new Y.Panel({
                srcNode      : fpselectnode,
                zIndex       : 600000,
                centered     : true,
                modal        : true,
                close        : true,
                render       : true
            });
            this.selectui.hide();
            // event handler for lazy loading of thumbnails and next page
            this.fpnode.one('.fp-content').on(['scroll','resize'], this.content_scrolled, this);
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
            this.hide_header();

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
                this.display_error(M.str.repository.norepositoriesavailable, 'norepositoriesavailable')
            }
            // display repository that was used last time
            this.show_recent_repository();
        },
        parse_repository_options: function(data, appendtolist) {
            if (appendtolist) {
                if (data.list) {
                    if (!this.filelist) { this.filelist = []; }
                    for (var i in data.list) {
                        this.filelist[this.filelist.length] = data.list[i];
                    }
                }
            } else {
                this.filelist = data.list?data.list:null;
                this.lazyloading = {};
            }
            this.filepath = data.path?data.path:null;
            this.active_repo = {};
            this.active_repo.issearchresult = data.issearchresult?true:false;
            this.active_repo.dynload = data.dynload?data.dynload:false;
            this.active_repo.pages = Number(data.pages?data.pages:null);
            this.active_repo.page = Number(data.page?data.page:null);
            this.active_repo.hasmorepages = (this.active_repo.pages && this.active_repo.page && (this.active_repo.page < this.active_repo.pages || this.active_repo.pages == -1))
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
            this.parse_repository_options(data);
            var client_id = this.options.client_id;
            var repository_id = data.repo_id;
            var l = this.logindata = data.login;
            var loginurl = '';
            var action = data['login_btn_action'] ? data['login_btn_action'] : 'login';
            var form_id = 'fp-form-'+client_id;

            var loginform_node = Y.Node.create(M.core_filepicker.templates.loginform);
            loginform_node.one('form').set('id', form_id);
            this.fpnode.one('.fp-content').setContent('').appendChild(loginform_node);
            var templates = {
                'popup' : loginform_node.one('.fp-login-popup'),
                'textarea' : loginform_node.one('.fp-login-textarea'),
                'select' : loginform_node.one('.fp-login-select'),
                'text' : loginform_node.one('.fp-login-text'),
                'radio' : loginform_node.one('.fp-login-radiogroup'),
                'checkbox' : loginform_node.one('.fp-login-checkbox'),
                'input' : loginform_node.one('.fp-login-input')
            };
            var container;
            for (var i in templates) {
                if (templates[i]) {
                    container = templates[i].get('parentNode');
                    container.removeChild(templates[i])
                }
            }

            for(var k in l) {
                if (templates[l[k].type]) {
                    var node = templates[l[k].type].cloneNode(true);
                } else {
                    node = templates['input'].cloneNode(true);
                }
                if (l[k].type == 'popup') {
                    // submit button
                    loginurl = l[k].url;
                    var popupbutton = node.one('button');
                    popupbutton.on('click', function(e){
                        M.core_filepicker.active_filepicker = this;
                        window.open(loginurl, 'repo_auth', 'location=0,status=0,width=500,height=300,scrollbars=yes');
                        e.preventDefault();
                    }, this);
                    loginform_node.one('form').on('keydown', function(e) {
                        if (e.keyCode == 13) {
                            popupbutton.simulate('click');
                            e.preventDefault();
                        }
                    }, this);
                    loginform_node.all('.fp-login-submit').remove();
                    action = 'popup';
                }else if(l[k].type=='textarea') {
                    // textarea element
                    if (node.one('label')) { node.one('label').set('for', l[k].id).setContent(l[k].label) }
                    node.one('textarea').setAttrs({id:l[k].id, name:l[k].name});
                }else if(l[k].type=='select') {
                    // select element
                    if (node.one('label')) { node.one('label').set('for', l[k].id).setContent(l[k].label) }
                    node.one('select').setAttrs({id:l[k].id, name:l[k].name}).setContent('');
                    for (i in l[k].options) {
                        node.one('select').appendChild(
                            Y.Node.create('<option/>').
                                set('value', l[k].options[i].value).
                                setContent(l[k].options[i].label))
                    }
                }else if(l[k].type=='radio') {
                    // radio input element
                    node.all('label').setContent(l[k].label);
                    var list = l[k].value.split('|');
                    var labels = l[k].value_label.split('|');
                    var radionode = null;
                    for(var item in list) {
                        if (radionode == null) {
                            radionode = node.one('.fp-login-radio');
                            radionode.one('input').set('checked', 'checked');
                        } else {
                            var x = radionode.cloneNode(true);
                            radionode.insert(x, 'after');
                            radionode = x;
                            radionode.one('input').set('checked', '');
                        }
                        radionode.one('input').setAttrs({id:''+l[k].id+item, name:l[k].name,
                            type:l[k].type, value:list[item]});
                        radionode.all('label').setContent(labels[item]).set('for', ''+l[k].id+item)
                    }
                    if (radionode == null) {
                        node.one('.fp-login-radio').remove();
                    }
                }else {
                    // input element
                    if (node.one('label')) { node.one('label').set('for', l[k].id).setContent(l[k].label) }
                    node.one('input').
                        set('type', l[k].type).
                        set('id', l[k].id).
                        set('name', l[k].name).
                        set('value', l[k].value?l[k].value:'')
                }
                container.appendChild(node);
            }
            // custom label text for submit button
            if (data['login_btn_label']) {
                loginform_node.all('.fp-login-submit').setContent(data['login_btn_label'])
            }
            // register button action for login and search
            if (action == 'login' || action == 'search') {
                loginform_node.one('.fp-login-submit').on('click', function(e){
                    e.preventDefault();
                    this.hide_header();
                    this.request({
                        'scope': this,
                        'action':(action == 'search') ? 'search' : 'signin',
                        'path': '',
                        'client_id': client_id,
                        'repository_id': repository_id,
                        'form': {id:form_id, upload:false, useDisabled:true},
                        'callback': this.display_response
                    }, true);
                }, this);
            }
            // if 'Enter' is pressed in the form, simulate the button click
            if (loginform_node.one('.fp-login-submit')) {
                loginform_node.one('form').on('keydown', function(e) {
                    if (e.keyCode == 13) {
                        loginform_node.one('.fp-login-submit').simulate('click')
                        e.preventDefault();
                    }
                }, this);
            }
        },
        display_response: function(id, obj, args) {
            var scope = args.scope
            // highlight the current repository in repositories list
            scope.fpnode.all('.fp-repo.active').removeClass('active');
            scope.fpnode.all('#fp-repo-'+scope.options.client_id+'-'+obj.repo_id).addClass('active')
            // add class repository_REPTYPE to the filepicker (for repository-specific styles)
            for (var i in scope.options.repositories) {
                scope.fpnode.removeClass('repository_'+scope.options.repositories[i].type)
            }
            if (obj.repo_id && scope.options.repositories[obj.repo_id]) {
                scope.fpnode.addClass('repository_'+scope.options.repositories[obj.repo_id].type)
            }
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
            var content = this.fpnode.one('.fp-content');
            content.setContent(M.core_filepicker.templates.uploadform);

            content.all('.fp-file,.fp-saveas,.fp-setauthor,.fp-setlicense').each(function (node) {
                node.all('label').set('for', node.one('input,select').generateID());
            });
            content.one('form').set('id', id);
            content.one('.fp-file input').set('name', 'repo_upload_file');
            content.one('.fp-saveas input').set('name', 'title');
            content.one('.fp-setauthor input').setAttrs({name:'author', value:this.options.author});
            content.one('.fp-setlicense select').set('name', 'license');
            this.populate_licenses_select(content.one('.fp-setlicense select'))
            // append hidden inputs to the upload form
            content.one('form').appendChild(Y.Node.create('<input/>').
                setAttrs({type:'hidden',name:'itemid',value:this.options.itemid}));
            var types = this.options.accepted_types;
            for (var i in types) {
                content.one('form').appendChild(Y.Node.create('<input/>').
                    setAttrs({type:'hidden',name:'accepted_types[]',value:types[i]}));
            }

            var scope = this;
            content.one('.fp-upload-btn').on('click', function(e) {
                e.preventDefault();
                var license = content.one('.fp-setlicense select');
                Y.Cookie.set('recentlicense', license.get('value'));
                if (!content.one('.fp-file input').get('value')) {
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
            var toolbar = this.fpnode.one('.fp-toolbar');
            toolbar.one('.fp-tb-logout').one('a,button').on('click', function(e) {
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
            toolbar.one('.fp-tb-refresh').one('a,button').on('click', function(e) {
                e.preventDefault();
                if (!this.active_repo.norefresh) {
                    this.list();
                }
            }, this);
            toolbar.one('.fp-tb-search form').
                set('method', 'POST').
                set('id', 'fp-tb-search-'+client_id).
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

            // it does not matter what kind of element is .fp-tb-manage, we create a dummy <a>
            // element and use it to open url on click event
            var managelnk = Y.Node.create('<a/>').
                setAttrs({id:'fp-tb-manage-'+client_id+'-link', target:'_blank'}).
                setStyle('display', 'none');
            toolbar.append(managelnk);
            toolbar.one('.fp-tb-manage').one('a,button').
                on('click', function(e) {
                    e.preventDefault();
                    managelnk.simulate('click')
                });

            // same with .fp-tb-help
            var helplnk = Y.Node.create('<a/>').
                setAttrs({id:'fp-tb-help-'+client_id+'-link', target:'_blank'}).
                setStyle('display', 'none');
            toolbar.append(helplnk);
            toolbar.one('.fp-tb-manage').one('a,button').
                on('click', function(e) {
                    e.preventDefault();
                    helplnk.simulate('click')
                });
        },
        hide_header: function() {
            if (this.fpnode.one('.fp-toolbar')) {
                this.fpnode.one('.fp-toolbar').addClass('empty');
            }
            if (this.pathbar) {
                this.pathbar.setContent('').addClass('empty');
            }
        },
        print_header: function() {
            var r = this.active_repo;
            var scope = this;
            var client_id = this.options.client_id;
            this.hide_header();
            this.print_path();
            var toolbar = this.fpnode.one('.fp-toolbar');
            if (!toolbar) { return; }

            var enable_tb_control = function(node, enabled) {
                if (!node) { return; }
                node.addClassIf('disabled', !enabled).addClassIf('enabled', enabled)
                if (enabled) {
                    toolbar.removeClass('empty');
                }
            }

            // TODO 'back' permanently disabled for now. Note, flickr_public uses 'Logout' for it!
            enable_tb_control(toolbar.one('.fp-tb-back'), false);

            // search form
            enable_tb_control(toolbar.one('.fp-tb-search'), !r.nosearch);
            if(!r.nosearch) {
                var searchform = toolbar.one('.fp-tb-search form');
                searchform.setContent('');
                this.request({
                    scope: this,
                    action:'searchform',
                    repository_id: this.active_repo.id,
                    callback: function(id, obj, args) {
                        if (obj.repo_id == scope.active_repo.id && obj.form) {
                            // if we did not jump to another repository meanwhile
                            searchform.setContent(obj.form);
                        }
                    }
                }, false);
            }

            // refresh button
            // weather we use cache for this instance, this button will reload listing anyway
            enable_tb_control(toolbar.one('.fp-tb-refresh'), !r.norefresh);

            // login button
            enable_tb_control(toolbar.one('.fp-tb-logout'), !r.nologin);
            if(!r.nologin) {
                var label = r.logouttext?r.logouttext:M.str.repository.logout;
                toolbar.one('.fp-tb-logout').one('a,button').setContent(label)
            }

            // manage url
            enable_tb_control(toolbar.one('.fp-tb-manage'), r.manage);
            Y.one('#fp-tb-manage-'+client_id+'-link').set('href', r.manage);

            // help url
            enable_tb_control(toolbar.one('.fp-tb-help'), r.help);
            Y.one('#fp-tb-help-'+client_id+'-link').set('href', r.help);
        },
        print_path: function() {
            if (!this.pathbar) { return; }
            this.pathbar.setContent('').addClass('empty');
            var p = this.filepath;
            if (p && p.length!=0 && this.viewmode != 2) {
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
            if (this.viewmode != 2 && this.viewmode != 3) {
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
