// YUI3 File Picker module for moodle
// Author: Dongsheng Cai <dongsheng@moodle.com>

/**
 *
 * File Picker UI
 * =====
 * this.fpnode, contains reference to filepicker Node, non-empty if and only if rendered
 * this.api, stores the URL to make ajax request
 * this.mainui, YUI Panel
 * this.selectnode, contains reference to select-file Node
 * this.selectui, YUI Panel for selecting particular file
 * this.msg_dlg, YUI Panel for error or info message
 * this.process_dlg, YUI Panel for processing existing filename
 * this.treeview, YUI Treeview
 * this.viewmode, store current view mode
 * this.pathbar, reference to the Node with path bar
 * this.pathnode, a Node element representing one folder in a path bar (not attached anywhere, just used for template)
 * this.currentpath, the current path in the repository (or last requested path)
 *
 * Filepicker options:
 * =====
 * this.options.client_id, the instance id
 * this.options.contextid
 * this.options.itemid
 * this.options.repositories, stores all repositories displayed in file picker
 * this.options.formcallback
 *
 * Active repository options
 * =====
 * this.active_repo.id
 * this.active_repo.defaultreturntype
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
 * this.filepath, current path (each element of the array is a part of the breadcrumb)
 * this.logindata, cached login form
 */

YUI.add('moodle-core_filepicker', function(Y) {
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

    /** set image source to src, if there is preview, remember it in lazyloading.
     *  If there is a preview and it was already loaded, use it. */
    Y.Node.prototype.setImgSrc = function(src, realsrc, lazyloading) {
        if (realsrc) {
            if (M.core_filepicker.loadedpreviews[realsrc]) {
                this.set('src', realsrc).addClass('realpreview');
                return this;
            } else {
                if (!this.get('id')) {
                    this.generateID();
                }
                lazyloading[this.get('id')] = realsrc;
            }
        }
        this.set('src', src);
        return this;
    }

    /**
     * Replaces the image source with preview. If the image is inside the treeview, we need
     * also to update the html property of corresponding YAHOO.widget.HTMLNode
     * @param array lazyloading array containing associations of imgnodeid->realsrc
     */
    Y.Node.prototype.setImgRealSrc = function(lazyloading) {
        if (this.get('id') && lazyloading[this.get('id')]) {
            var newsrc = lazyloading[this.get('id')];
            M.core_filepicker.loadedpreviews[newsrc] = true;
            this.set('src', newsrc).addClass('realpreview');
            delete lazyloading[this.get('id')];
            var treenode = this.ancestor('.fp-treeview')
            if (treenode && treenode.get('parentNode').treeview) {
                treenode.get('parentNode').treeview.getRoot().refreshPreviews(this.get('id'), newsrc);
            }
        }
        return this;
    }

    /** scan TreeView to find which node contains image with id=imgid and replace it's html
     * with the new image source. */
    Y.YUI2.widget.Node.prototype.refreshPreviews = function(imgid, newsrc, regex) {
        if (!regex) {
            regex = new RegExp("<img\\s[^>]*id=\""+imgid+"\"[^>]*?(/?)>", "im");
        }
        if (this.expanded || this.isLeaf) {
            var html = this.getContentHtml();
            if (html && this.setHtml && regex.test(html)) {
                var newhtml = this.html.replace(regex, "<img id=\""+imgid+"\" src=\""+newsrc+"\" class=\"realpreview\"$1>", html);
                this.setHtml(newhtml);
                return true;
            }
            if (!this.isLeaf && this.children) {
                for(var c in this.children) {
                    if (this.children[c].refreshPreviews(imgid, newsrc, regex)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Displays a list of files (used by filepicker, filemanager) inside the Node
     *
     * @param array options
     *   viewmode : 1 - icons, 2 - tree, 3 - table
     *   appendonly : whether fileslist need to be appended instead of replacing the existing content
     *   filenode : Node element that contains template for displaying one file
     *   callback : On click callback. The element of the fileslist array will be passed as argument
     *   rightclickcallback : On right click callback (optional).
     *   callbackcontext : context where callbacks are executed
     *   sortable : whether content may be sortable (in table mode)
     *   dynload : allow dynamic load for tree view
     *   filepath : for pre-building of tree view - the path to the current directory in filepicker format
     *   treeview_dynload : callback to function to dynamically load the folder in tree view
     *   classnamecallback : callback to function that returns the class name for an element
     * @param array fileslist array of files to show, each array element may have attributes:
     *   title or fullname : file name
     *   shorttitle (optional) : display file name
     *   thumbnail : url of image
     *   icon : url of icon image
     *   thumbnail_width : width of thumbnail, default 90
     *   thumbnail_height : height of thumbnail, default 90
     *   thumbnail_alt : TODO not needed!
     *   description or thumbnail_title : alt text
     * @param array lazyloading : reference to the array with lazy loading images
     */
    Y.Node.prototype.fp_display_filelist = function(options, fileslist, lazyloading) {
        var viewmodeclassnames = {1:'fp-iconview', 2:'fp-treeview', 3:'fp-tableview'};
        var classname = viewmodeclassnames[options.viewmode];
        var scope = this;
        /** return whether file is a folder (different attributes in FileManager and FilePicker) */
        var file_is_folder = function(node) {
            if (node.children) {return true;}
            if (node.type && node.type == 'folder') {return true;}
            return false;
        };
        /** return the name of the file (different attributes in FileManager and FilePicker) */
        var file_get_filename = function(node) {
            return node.title ? node.title : node.fullname;
        };
        /** return display name of the file (different attributes in FileManager and FilePicker) */
        var file_get_displayname = function(node) {
            var displayname = node.shorttitle ? node.shorttitle : file_get_filename(node);
            return Y.Escape.html(displayname);
        };
        /** return file description (different attributes in FileManager and FilePicker) */
        var file_get_description = function(node) {
            var description = '';
            if (node.description) {
                description = node.description;
            } else if (node.thumbnail_title) {
                description = node.thumbnail_title;
            } else {
                description = file_get_filename(node);
            }
            return Y.Escape.html(description);
        };
        /** help funciton for tree view */
        var build_tree = function(node, level) {
            // prepare file name with icon
            var el = Y.Node.create('<div/>');
            el.appendChild(options.filenode.cloneNode(true));

            el.one('.fp-filename').setContent(file_get_displayname(node));
            // TODO add tooltip with node.title or node.thumbnail_title
            var tmpnodedata = {className:options.classnamecallback(node)};
            el.get('children').addClass(tmpnodedata.className);
            if (node.icon) {
                el.one('.fp-icon').appendChild(Y.Node.create('<img/>'));
                el.one('.fp-icon img').setImgSrc(node.icon, node.realicon, lazyloading);
            }
            // create node
            tmpnodedata.html = el.getContent();
            var tmpNode = new Y.YUI2.widget.HTMLNode(tmpnodedata, level, false);
            if (node.dynamicLoadComplete) {
                tmpNode.dynamicLoadComplete = true;
            }
            tmpNode.fileinfo = node;
            tmpNode.isLeaf = !file_is_folder(node);
            if (!tmpNode.isLeaf) {
                if(node.expanded) {
                    tmpNode.expand();
                }
                tmpNode.path = node.path ? node.path : (node.filepath ? node.filepath : '');
                for(var c in node.children) {
                    build_tree(node.children[c], tmpNode);
                }
            }
        };
        /** initialize tree view */
        var initialize_tree_view = function() {
            var parentid = scope.one('.'+classname).get('id');
            // TODO MDL-32736 use YUI3 gallery TreeView
            scope.treeview = new Y.YUI2.widget.TreeView(parentid);
            if (options.dynload) {
                scope.treeview.setDynamicLoad(Y.bind(options.treeview_dynload, options.callbackcontext), 1);
            }
            scope.treeview.singleNodeHighlight = true;
            if (options.filepath && options.filepath.length) {
                // we just jumped from icon/details view, we need to show all parents
                // we extract as much information as possible from filepath and filelist
                // and send additional requests to retrieve siblings for parent folders
                var mytree = {};
                var mytreeel = null;
                for (var i in options.filepath) {
                    if (mytreeel == null) {
                        mytreeel = mytree;
                    } else {
                        mytreeel.children = [{}];
                        mytreeel = mytreeel.children[0];
                    }
                    var pathelement = options.filepath[i];
                    mytreeel.path = pathelement.path;
                    mytreeel.title = pathelement.name;
                    mytreeel.icon = pathelement.icon;
                    mytreeel.dynamicLoadComplete = true; // we will call it manually
                    mytreeel.expanded = true;
                }
                mytreeel.children = fileslist;
                build_tree(mytree, scope.treeview.getRoot());
                // manually call dynload for parent elements in the tree so we can load other siblings
                if (options.dynload) {
                    var root = scope.treeview.getRoot();
                    // Whether search results are currently displayed in the active repository in the filepicker.
                    // We do not want to load siblings of parent elements when displaying search tree results.
                    var isSearchResult = typeof options.callbackcontext.active_repo !== 'undefined' &&
                        options.callbackcontext.active_repo.issearchresult;
                    while (root && root.children && root.children.length) {
                        root = root.children[0];
                        if (root.path == mytreeel.path) {
                            root.origpath = options.filepath;
                            root.origlist = fileslist;
                        } else if (!root.isLeaf && root.expanded && !isSearchResult) {
                            Y.bind(options.treeview_dynload, options.callbackcontext)(root, null);
                        }
                    }
                }
            } else {
                // there is no path information, just display all elements as a list, without hierarchy
                for(k in fileslist) {
                    build_tree(fileslist[k], scope.treeview.getRoot());
                }
            }
            scope.treeview.subscribe('clickEvent', function(e){
                e.node.highlight(false);
                var callback = options.callback;
                if (options.rightclickcallback && e.event.target &&
                        Y.Node(e.event.target).ancestor('.fp-treeview .fp-contextmenu', true)) {
                    callback = options.rightclickcallback;
                }
                Y.bind(callback, options.callbackcontext)(e, e.node.fileinfo);
                Y.YUI2.util.Event.stopEvent(e.event)
            });
            // Simulate click on file not folder.
            scope.treeview.subscribe('enterKeyPressed', function(node) {
                if (node.children.length === 0) {
                    Y.one(node.getContentEl()).one('a').simulate('click');
                }
            });
            // TODO MDL-32736 support right click
            /*if (options.rightclickcallback) {
                scope.treeview.subscribe('dblClickEvent', function(e){
                    e.node.highlight(false);
                    Y.bind(options.rightclickcallback, options.callbackcontext)(e, e.node.fileinfo);
                });
            }*/
            scope.treeview.draw();
        };
        /** formatting function for table view */
        var formatValue = function (o){
            if (o.data[''+o.column.key+'_f_s']) {return o.data[''+o.column.key+'_f_s'];}
            else if (o.data[''+o.column.key+'_f']) {return o.data[''+o.column.key+'_f'];}
            else if (o.value) {return o.value;}
            else {return '';}
        };
        /** formatting function for table view */
        var formatTitle = function(o) {
            var el = Y.Node.create('<div/>');
            el.appendChild(options.filenode.cloneNode(true)); // TODO not node but string!
            el.get('children').addClass(o.data['classname']);
            el.one('.fp-filename').setContent(o.value);
            if (o.data['icon']) {
                el.one('.fp-icon').appendChild(Y.Node.create('<img/>'));
                el.one('.fp-icon img').setImgSrc(o.data['icon'], o.data['realicon'], lazyloading);
            }
            if (options.rightclickcallback) {
                el.get('children').addClass('fp-hascontextmenu');
            }
            // TODO add tooltip with o.data['title'] (o.value) or o.data['thumbnail_title']
            return el.getContent();
        }

        /**
         * Generate slave checkboxes based on toggleall's specification
         * @param {object} o An object reprsenting the record for the current row.
         * @return {html} The checkbox html
         */
        var formatCheckbox = function(o) {
            var el = Y.Node.create('<div/>');
            var parentid = scope.one('.' + classname).get('id');
            var checkbox = Y.Node.create('<input/>')
                .setAttribute('type', 'checkbox')
                .setAttribute('data-fieldtype', 'checkbox')
                .setAttribute('data-fullname', o.data.fullname)
                .setAttribute('data-action', 'toggle')
                .setAttribute('data-toggle', 'slave')
                .setAttribute('data-togglegroup', 'file-selections-' + parentid);

            var checkboxLabel = Y.Node.create('<label>')
                .setHTML("Select file '" + o.data.fullname + "'")
                .addClass('visually-hidden')
                .setAttrs({
                    for: checkbox.generateID(),
                });

            el.appendChild(checkbox);
            el.appendChild(checkboxLabel);
            return el.getContent();
        };
        /** sorting function for table view */
        var sortFoldersFirst = function(a, b, desc) {
            if (a.get('isfolder') && !b.get('isfolder')) {
                return -1;
            }
            if (!a.get('isfolder') && b.get('isfolder')) {
                return 1;
            }
            var aa = a.get(this.key), bb = b.get(this.key), dir = desc ? -1 : 1;
            return (aa > bb) ? dir : ((aa < bb) ? -dir : 0);
        }
        /** initialize table view */
        var initialize_table_view = function() {
            var cols = [
                {key: "displayname", label: M.util.get_string('name', 'moodle'), allowHTML: true, formatter: formatTitle,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "datemodified", label: M.util.get_string('lastmodified', 'moodle'), allowHTML: true, formatter: formatValue,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "size", label: M.util.get_string('size', 'repository'), allowHTML: true, formatter: formatValue,
                    sortable: true, sortFn: sortFoldersFirst},
                {key: "mimetype", label: M.util.get_string('type', 'repository'), allowHTML: true,
                    sortable: true, sortFn: sortFoldersFirst}
            ];

            // Generate a checkbox based on toggleall's specification
            var div = Y.Node.create('<div/>');
            var parentid = scope.one('.' + classname).get('id');
            var checkbox = Y.Node.create('<input/>')
                .setAttribute('type', 'checkbox')
                // .setAttribute('title', M.util.get_string('selectallornone', 'form'))
                .setAttribute('data-action', 'toggle')
                .setAttribute('data-toggle', 'master')
                .setAttribute('data-togglegroup', 'file-selections-' + parentid);

            var checkboxLabel = Y.Node.create('<label>')
                .setHTML(M.util.get_string('selectallornone', 'form'))
                .addClass('visually-hidden')
                .setAttrs({
                    for: checkbox.generateID(),
                });

            div.appendChild(checkboxLabel);
            div.appendChild(checkbox);

            // Define the selector for the click event handler.
            var clickEventSelector = 'tr';
            // Enable the selectable checkboxes
            if (options.disablecheckboxes != undefined && !options.disablecheckboxes) {
                clickEventSelector = 'tr td:not(:first-child)';
                cols.unshift({
                    key: "",
                    label: div.getContent(),
                    allowHTML: true,
                    formatter: formatCheckbox,
                    sortable: false
                });
            }
            scope.tableview = new Y.DataTable({columns: cols, data: fileslist});
            scope.tableview.delegate('click', function (e, tableview) {
                var record = tableview.getRecord(e.currentTarget.get('id'));
                if (record) {
                    var callback = options.callback;
                    if (options.rightclickcallback && e.target.ancestor('.fp-tableview .fp-contextmenu', true)) {
                        callback = options.rightclickcallback;
                    }
                    Y.bind(callback, this)(e, record.getAttrs());
                }
            }, clickEventSelector, options.callbackcontext, scope.tableview);

            if (options.rightclickcallback) {
                scope.tableview.delegate('contextmenu', function (e, tableview) {
                    var record = tableview.getRecord(e.currentTarget.get('id'));
                    if (record) { Y.bind(options.rightclickcallback, this)(e, record.getAttrs()); }
                }, 'tr', options.callbackcontext, scope.tableview);
            }
        }
        /** append items in table view mode */
        var append_files_table = function() {
            if (options.appendonly) {
                fileslist.forEach(function(el) {
                    this.tableview.data.add(el);
                },scope);
            }
            scope.tableview.render(scope.one('.'+classname));
            scope.tableview.sortable = options.sortable ? true : false;
        };
        /** append items in tree view mode */
        var append_files_tree = function() {
            if (options.appendonly) {
                var parentnode = scope.treeview.getRoot();
                if (scope.treeview.getHighlightedNode()) {
                    parentnode = scope.treeview.getHighlightedNode();
                    if (parentnode.isLeaf) {parentnode = parentnode.parent;}
                }
                for (var k in fileslist) {
                    build_tree(fileslist[k], parentnode);
                }
                scope.treeview.draw();
            } else {
                // otherwise files were already added in initialize_tree_view()
            }
        }
        /** append items in icon view mode */
        var append_files_icons = function() {
            parent = scope.one('.'+classname);
            for (var k in fileslist) {
                var node = fileslist[k];
                var element = options.filenode.cloneNode(true);
                parent.appendChild(element);
                element.addClass(options.classnamecallback(node));
                var filenamediv = element.one('.fp-filename');
                filenamediv.setContent(file_get_displayname(node));
                var imgdiv = element.one('.fp-thumbnail'), width, height, src;
                if (node.thumbnail) {
                    width = node.thumbnail_width ? node.thumbnail_width : 90;
                    height = node.thumbnail_height ? node.thumbnail_height : 90;
                    src = node.thumbnail;
                } else {
                    width = 16;
                    height = 16;
                    src = node.icon;
                }
                filenamediv.setStyleAdv('width', width);
                imgdiv.setStyleAdv('width', width).setStyleAdv('height', height);
                var img = Y.Node.create('<img/>').setAttrs({
                        title: file_get_description(node),
                        alt: Y.Escape.html(node.thumbnail_alt ? node.thumbnail_alt : file_get_filename(node))}).
                    setStyle('maxWidth', ''+width+'px').
                    setStyle('maxHeight', ''+height+'px');
                img.setImgSrc(src, node.realthumbnail, lazyloading);
                imgdiv.appendChild(img);
                element.on('click', function(e, nd) {
                    if (options.rightclickcallback && e.target.ancestor('.fp-iconview .fp-contextmenu', true)) {
                        Y.bind(options.rightclickcallback, this)(e, nd);
                    } else {
                        Y.bind(options.callback, this)(e, nd);
                    }
                }, options.callbackcontext, node);
                if (options.rightclickcallback) {
                    element.on('contextmenu', options.rightclickcallback, options.callbackcontext, node);
                }
            }
        }

        // Notify the user if any of the files has a problem status.
        var problemFiles = [];
        fileslist.forEach(function(file) {
            if (!file_is_folder(file) && file.hasOwnProperty('status') && file.status != 0) {
                problemFiles.push(file);
            }
        });
        if (problemFiles.length > 0) {
            require(["core/notification", "core/str"], function(Notification, Str) {
                problemFiles.forEach(function(problemFile) {
                    Str.get_string('storedfilecannotreadfile', 'error', problemFile.fullname).then(function(string) {
                        Notification.addNotification({
                            message: string,
                            type: "error"
                        });
                        return;
                    }).catch(Notification.exception);
                });
            });
        }

        // If table view, need some additional properties
        // before passing fileslist to the YUI tableview
        if (options.viewmode == 3) {
            fileslist.forEach(function(el) {
                el.displayname = file_get_displayname(el);
                el.isfolder = file_is_folder(el);
                el.classname = options.classnamecallback(el);
            }, scope);
        }

        // initialize files view
        if (!options.appendonly) {
            var parent = Y.Node.create('<div/>').addClass(classname);
            this.setContent('').appendChild(parent);
            parent.generateID();
            if (options.viewmode == 2) {
                initialize_tree_view();
            } else if (options.viewmode == 3) {
                initialize_table_view();
            } else {
                // nothing to initialize for icon view
            }
        }

        // append files to the list
        if (options.viewmode == 2) {
            append_files_tree();
        } else if (options.viewmode == 3) {
            append_files_table();
        } else {
            append_files_icons();
        }

    }
}, '@VERSION@', {
    requires:['base', 'node', 'yui2-treeview', 'panel', 'cookie', 'datatable', 'datatable-sort']
});

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
 * Array of image sources for real previews (realicon or realthumbnail) that are already loaded
 */
M.core_filepicker.loadedpreviews = M.core_filepicker.loadedpreviews || {};

/**
* Set selected file info
*
* @param object file info
*/
M.core_filepicker.select_file = function(file) {
    M.core_filepicker.active_filepicker.select_file(file);
}

/**
 * Init and show file picker
 */
M.core_filepicker.show = function(Y, options) {
    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options);
    }
    M.core_filepicker.instances[options.client_id].options.formcallback = options.formcallback;
    M.core_filepicker.instances[options.client_id].show();
};

M.core_filepicker.set_templates = function(Y, templates) {
    for (var templid in templates) {
        M.core_filepicker.templates[templid] = templates[templid];
    }
}

/**
 * Add new file picker to current instances
 */
M.core_filepicker.init = function(Y, options) {
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
        waitinterval : null, // When the loading template is being displayed and its animation is running this will be an interval instance.
        initializer: function(options) {
            this.options = options;
            if (!this.options.savepath) {
                this.options.savepath = '/';
            }
        },

        destructor: function() {
        },

        request: function(args, redraw) {
            var api = (args.api ? args.api : this.api) + '?action='+args.action;
            var params = {};
            var scope = args['scope'] ? args['scope'] : this;
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
            // The unlimited value of areamaxbytes is -1, it is defined by FILE_AREA_MAX_BYTES_UNLIMITED.
            params['areamaxbytes'] = this.options.areamaxbytes ? this.options.areamaxbytes : -1;
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
                        var data = null;
                        try {
                            data = Y.JSON.parse(o.responseText);
                        } catch(e) {
                            if (o && o.status && o.status > 0) {
                                Y.use('moodle-core-notification-exception', function() {
                                    return new M.core.exception(e);
                                });
                                return;
                            }
                        }
                        // error checking
                        if (data && data.error) {
                            if (data.errorcode === 'invalidfiletype') {
                                // File type errors are not really errors, so report them less scarily.
                                Y.use('moodle-core-notification-alert', function() {
                                    return new M.core.alert({
                                        title: M.util.get_string('error', 'moodle'),
                                        message: data.error,
                                    });
                                });
                            } else {
                                Y.use('moodle-core-notification-ajaxexception', function() {
                                    return new M.core.ajaxException(data);
                                });
                            }
                            if (args.onerror) {
                                args.onerror(id, data, p);
                            } else {
                                // Don't know what to do, so blank the dialogue to ensure it is not left in an inconsistent state.
                                // This is not great. The user needs to re-click 'Upload file' to reset the display.
                                this.fpnode.one('.fp-content').setContent('');
                            }
                            return;
                        } else {
                            if (data.msg) {
                                // As far as I can tell, msg will never be set by any PHP code. -- Tim Oct 2024.
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
                        // Add an arbitrary parameter to the URL to force browsers to re-load the new image even
                        // if the file name has not changed.
                        var urlimage = data.existingfile.url + "?time=" + (new Date()).getTime();
                        if (scope.options.editor_target && scope.options.env == 'editor') {
                            // editor needs to update url
                            scope.options.editor_target.value = urlimage;
                            scope.options.editor_target.dispatchEvent(new Event('change'), {'bubbles': true});
                        }
                        var fileinfo = {'client_id':scope.options.client_id,
                            'url': urlimage,
                            'file': data.existingfile.filename};
                        var formcallback_scope = scope.options.magicscope ? scope.options.magicscope : scope;
                        scope.options.formcallback.apply(formcallback_scope, [fileinfo]);
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
                    scope.options.editor_target.dispatchEvent(new Event('change'), {'bubbles': true});
                }
                scope.hide();
                var formcallback_scope = scope.options.magicscope ? scope.options.magicscope : scope;
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
                this.process_dlg_node = Y.Node.create(M.core_filepicker.templates.processexistingfile);
                var node = this.process_dlg_node;
                node.generateID();
                this.process_dlg = new M.core.dialogue({
                    draggable    : true,
                    bodyContent  : node,
                    headerContent: M.util.get_string('fileexistsdialogheader', 'repository'),
                    centered     : true,
                    modal        : true,
                    visible      : false,
                    zIndex       : this.options.zIndex
                });
                node.one('.fp-dlg-butoverwrite').on('click', handleOverwrite, this);
                node.one('.fp-dlg-butrename').on('click', handleRename, this);
                node.one('.fp-dlg-butcancel').on('click', handleCancel, this);
                if (this.options.env == 'editor') {
                    node.one('.fp-dlg-text').setContent(M.util.get_string('fileexistsdialog_editor', 'repository'));
                } else {
                    node.one('.fp-dlg-text').setContent(M.util.get_string('fileexistsdialog_filemanager', 'repository'));
                }
            }
            this.selectnode.removeClass('loading');
            this.process_dlg.dialogdata = data;
            this.process_dlg_node.one('.fp-dlg-butrename').setContent(M.util.get_string('renameto', 'repository', data.newfile.filename));
            this.process_dlg.show();
        },
        /** displays error instead of filepicker contents */
        display_error: function(errortext, errorcode) {
            this.fpnode.one('.fp-content').setContent(M.core_filepicker.templates.error);
            this.fpnode.one('.fp-content .fp-error').
                addClass(errorcode).
                setContent(Y.Escape.html(errortext));
        },
        /** displays message in a popup */
        print_msg: function(msg, type) {
            var header = M.util.get_string('error', 'moodle');
            if (type != 'error') {
                type = 'info'; // one of only two types excepted
                header = M.util.get_string('info', 'moodle');
            }
            if (!this.msg_dlg) {
                this.msg_dlg_node = Y.Node.create(M.core_filepicker.templates.message);
                this.msg_dlg_node.generateID();

                this.msg_dlg = new M.core.dialogue({
                    draggable    : true,
                    bodyContent  : this.msg_dlg_node,
                    centered     : true,
                    modal        : true,
                    visible      : false,
                    zIndex       : this.options.zIndex
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
        view_files: function(appenditems) {
            this.viewbar_set_enabled(true);
            this.print_path();
            /*if ((appenditems == null) && (!this.filelist || !this.filelist.length) && !this.active_repo.hasmorepages) {
             // TODO do it via classes and adjust for each view mode!
                // If there are no items and no next page, just display status message and quit
                this.display_error(M.util.get_string('nofilesavailable', 'repository'), 'nofilesavailable');
                return;
            }*/
            if (this.viewmode == 2) {
                this.view_as_list(appenditems);
            } else if (this.viewmode == 3) {
                this.view_as_table(appenditems);
            } else {
                this.view_as_icons(appenditems);
            }
            this.fpnode.one('.fp-content').setAttribute('tabindex', '0');
            this.fpnode.one('.fp-content').focus();
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
                if (this.processingimages) {
                    return;
                }
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
                            node.setImgRealSrc(scope.lazyloading);
                        }
                    });
                }
                this.processingimages = false;
            }, this), 200)
        },
        treeview_dynload: function(node, cb) {
            var retrieved_children = {};
            if (node.children) {
                for (var i in node.children) {
                    retrieved_children[node.children[i].path] = node.children[i];
                }
            }
            this.request({
                action:'list',
                client_id: this.options.client_id,
                repository_id: this.active_repo.id,
                path:node.path?node.path:'',
                page:node.page?args.page:'',
                scope:this,
                callback: function(id, obj, args) {
                    var list = obj.list;
                    var scope = args.scope;
                    // check that user did not leave the view mode before recieving this response
                    if (!(scope.active_repo.id == obj.repo_id && scope.viewmode == 2 && node && node.getChildrenEl())) {
                        return;
                    }
                    if (cb != null) { // (in manual mode do not update current path)
                        scope.viewbar_set_enabled(true);
                        scope.parse_repository_options(obj);
                    }
                    node.highlight(false);
                    node.origlist = obj.list ? obj.list : null;
                    node.origpath = obj.path ? obj.path : null;
                    node.children = [];
                    for(k in list) {
                        if (list[k].children && retrieved_children[list[k].path]) {
                            // if this child is a folder and has already been retrieved
                            node.children[node.children.length] = retrieved_children[list[k].path];
                        } else {
                            // append new file to the list
                            scope.view_as_list([list[k]]);
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
       classnamecallback : function(node) {
            var classname = '';
            if (node.children) {
                classname = classname + ' fp-folder';
            }
            if (node.isref) {
                classname = classname + ' fp-isreference';
            }
            if (node.iscontrolledlink) {
                classname = classname + ' fp-iscontrolledlink';
            }
            if (node.refcount) {
                classname = classname + ' fp-hasreferences';
            }
            if (node.originalmissing) {
                classname = classname + ' fp-originalmissing';
            }
            return Y.Lang.trim(classname);
        },
        /** displays list of files in tree (list) view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_list: function(appenditems) {
            var list = (appenditems != null) ? appenditems : this.filelist;
            this.viewmode = 2;
            if (!this.filelist || this.filelist.length==0 && (!this.filepath || !this.filepath.length)) {
                this.display_error(M.util.get_string('nofilesavailable', 'repository'), 'nofilesavailable');
                return;
            }

            var element_template = Y.Node.create(M.core_filepicker.templates.listfilename);
            var options = {
                viewmode : this.viewmode,
                appendonly : (appenditems != null),
                filenode : element_template,
                callbackcontext : this,
                callback : function(e, node) {
                    // TODO MDL-32736 e is not an event here but an object with properties 'event' and 'node'
                    if (!node.children) {
                        if (e.node.parent && e.node.parent.origpath) {
                            // set the current path
                            this.filepath = e.node.parent.origpath;
                            this.filelist = e.node.parent.origlist;
                            this.print_path();
                        }
                        this.select_file(node);
                    } else {
                        // save current path and filelist (in case we want to jump to other viewmode)
                        this.filepath = e.node.origpath;
                        this.filelist = e.node.origlist;
                        this.currentpath = e.node.path;
                        this.print_path();
                        this.content_scrolled();
                    }
                },
                classnamecallback : this.classnamecallback,
                dynload : this.active_repo.dynload,
                filepath : this.filepath,
                treeview_dynload : this.treeview_dynload
            };
            this.fpnode.one('.fp-content').fp_display_filelist(options, list, this.lazyloading);
        },
        /** displays list of files in icon view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_icons: function(appenditems) {
            this.viewmode = 1;
            var list = (appenditems != null) ? appenditems : this.filelist;
            var element_template = Y.Node.create(M.core_filepicker.templates.iconfilename);
            if ((appenditems == null) && (!this.filelist || !this.filelist.length)) {
                this.display_error(M.util.get_string('nofilesavailable', 'repository'), 'nofilesavailable');
                return;
            }
            var options = {
                viewmode : this.viewmode,
                appendonly : (appenditems != null),
                filenode : element_template,
                callbackcontext : this,
                callback : function(e, node) {
                    if (e.preventDefault) {
                        e.preventDefault();
                    }
                    if(node.children) {
                        if (this.active_repo.dynload) {
                            this.list({'path':node.path});
                        } else {
                            this.filelist = node.children;
                            this.view_files();
                        }
                    } else {
                        this.select_file(node);
                    }
                },
                classnamecallback : this.classnamecallback
            };
            this.fpnode.one('.fp-content').fp_display_filelist(options, list, this.lazyloading);
        },
        /** displays list of files in table view mode. If param appenditems is specified,
         * appends those items to the end of the list. Otherwise (default behaviour)
         * clears the contents and displays the items from this.filelist */
        view_as_table: function(appenditems) {
            this.viewmode = 3;
            var list = (appenditems != null) ? appenditems : this.filelist;
            if (!appenditems && (!this.filelist || this.filelist.length==0) && !this.active_repo.hasmorepages) {
                this.display_error(M.util.get_string('nofilesavailable', 'repository'), 'nofilesavailable');
                return;
            }
            var element_template = Y.Node.create(M.core_filepicker.templates.listfilename);
            var options = {
                viewmode : this.viewmode,
                appendonly : (appenditems != null),
                filenode : element_template,
                callbackcontext : this,
                sortable : !this.active_repo.hasmorepages,
                callback : function(e, node) {
                    if (e.preventDefault) {e.preventDefault();}
                    if (node.children) {
                        if (this.active_repo.dynload) {
                            this.list({'path':node.path});
                        } else {
                            this.filelist = node.children;
                            this.view_files();
                        }
                    } else {
                        this.select_file(node);
                    }
                },
                classnamecallback : this.classnamecallback
            };
            this.fpnode.one('.fp-content').fp_display_filelist(options, list, this.lazyloading);
        },
        /** If more than one page available, requests and displays the files from the next page */
        request_next_page: function() {
            if (!this.active_repo.hasmorepages || this.active_repo.nextpagerequested) {
                // nothing to load
                return;
            }
            this.active_repo.nextpagerequested = true;
            var nextpage = this.active_repo.page+1;
            var args = {
                page: nextpage,
                repo_id: this.active_repo.id
            };
            var action = this.active_repo.issearchresult ? 'search' : 'list';
            this.request({
                path: this.currentpath,
                scope: this,
                action: action,
                client_id: this.options.client_id,
                repository_id: args.repo_id,
                params: args,
                callback: function(id, obj, args) {
                    var scope = args.scope;
                    // Check that we are still in the same repository and are expecting this page. We have no way
                    // to compare the requested page and the one returned, so we assume that if the last chunk
                    // of the breadcrumb is similar, then we probably are on the same page.
                    var samepage = true;
                    if (obj.path && scope.filepath) {
                        var pathbefore = scope.filepath[scope.filepath.length-1];
                        var pathafter = obj.path[obj.path.length-1];
                        if (pathbefore.path != pathafter.path) {
                            samepage = false;
                        }
                    }
                    if (scope.active_repo.hasmorepages && obj.list && obj.page &&
                            obj.repo_id == scope.active_repo.id &&
                            obj.page == scope.active_repo.page+1 && samepage) {
                        scope.parse_repository_options(obj, true);
                        scope.view_files(obj.list)
                    }
                }
            }, false);
        },
        select_file: function(args) {
            var argstitle = args.shorttitle ? args.shorttitle : args.title;
            // Limit the string length so it fits nicely on mobile devices
            var titlelength = 30;
            if (argstitle.length > titlelength) {
                argstitle = argstitle.substring(0, titlelength) + '...';
            }
            Y.one('#fp-file_label_'+this.options.client_id).setContent(Y.Escape.html(M.util.get_string('select', 'repository')+' '+argstitle));
            this.selectui.show();
            Y.one('#'+this.selectnode.get('id')).focus();
            var client_id = this.options.client_id;
            var selectnode = this.selectnode;
            var return_types = this.options.repositories[this.active_repo.id].return_types;
            selectnode.removeClass('loading');
            selectnode.one('.fp-saveas input').set('value', args.title);

            var imgnode = Y.Node.create('<img/>').
                set('src', args.realthumbnail ? args.realthumbnail : args.thumbnail).
                setStyle('maxHeight', ''+(args.thumbnail_height ? args.thumbnail_height : 90)+'px').
                setStyle('maxWidth', ''+(args.thumbnail_width ? args.thumbnail_width : 90)+'px');
            selectnode.one('.fp-thumbnail').setContent('').appendChild(imgnode);

            // filelink is the array of file-link-types available for this repository in this env
            var filelinktypes = [2/*FILE_INTERNAL*/,1/*FILE_EXTERNAL*/,4/*FILE_REFERENCE*/,8/*FILE_CONTROLLED_LINK*/];
            var filelink = {}, firstfilelink = null, filelinkcount = 0;
            for (var i in filelinktypes) {
                var allowed = (return_types & filelinktypes[i]) &&
                    (this.options.return_types & filelinktypes[i]);
                if (filelinktypes[i] == 1/*FILE_EXTERNAL*/ && !this.options.externallink && this.options.env == 'editor') {
                    // special configuration setting 'repositoryallowexternallinks' may prevent
                    // using external links in editor environment
                    allowed = false;
                }
                filelink[filelinktypes[i]] = allowed;
                firstfilelink = (firstfilelink==null && allowed) ? filelinktypes[i] : firstfilelink;
                filelinkcount += allowed ? 1 : 0;
            }
            var defaultreturntype = this.options.repositories[this.active_repo.id].defaultreturntype;
            if (defaultreturntype) {
                if (filelink[defaultreturntype]) {
                    firstfilelink = defaultreturntype;
                }
            }
            // make radio buttons enabled if this file-link-type is available and only if there are more than one file-link-type option
            // check the first available file-link-type option
            for (var linktype in filelink) {
                var el = selectnode.one('.fp-linktype-'+linktype);
                el.addClassIf('uneditable', !(filelink[linktype] && filelinkcount>1));
                el.one('input').set('checked', (firstfilelink == linktype) ? 'checked' : '').simulate('change');
            }

            // TODO MDL-32532: attributes 'hasauthor' and 'haslicense' need to be obsolete,
            selectnode.one('.fp-setauthor input').set('value', args.author ? args.author : this.options.author);
            this.populateLicensesSelect(selectnode.one('.fp-setlicense select'), args);
            selectnode.one('form #filesource-'+client_id).set('value', args.source);
            selectnode.one('form #filesourcekey-'+client_id).set('value', args.sourcekey);

            // display static information about a file (when known)
            var attrs = ['datemodified','datecreated','size','license','author','dimensions'];
            for (var i in attrs) {
                if (selectnode.one('.fp-'+attrs[i])) {
                    var value = (args[attrs[i]+'_f']) ? args[attrs[i]+'_f'] : (args[attrs[i]] ? args[attrs[i]] : '');
                    selectnode.one('.fp-'+attrs[i]).addClassIf('fp-unknown', ''+value == '')
                        .one('.fp-value').setContent(Y.Escape.html(value));
                }
            }
            // Load popover for the filepicker content.
            var filepickerContent = Y.one('.file-picker.fp-select');
            require(['theme_boost/bootstrap/popover'], function(Popover) {
                var popoverTriggerList = filepickerContent.getDOMNode().querySelectorAll('[data-bs-toggle="popover"]');
                popoverTriggerList.forEach((popoverTriggerEl) => {
                    new Popover(popoverTriggerEl);
                });
            });
        },
        setup_select_file: function() {
            var client_id = this.options.client_id;
            var selectnode = this.selectnode;
            var getfile = selectnode.one('.fp-select-confirm');
            var filePickerHelper = this;
            // bind labels with corresponding inputs
            selectnode.all('.fp-saveas,.fp-linktype-2,.fp-linktype-1,.fp-linktype-4,fp-linktype-8,.fp-setauthor,.fp-setlicense').each(function (node) {
                node.all('label').set('for', node.one('input,select').generateID());
            });
            selectnode.one('.fp-linktype-2 input').setAttrs({value: 2, name: 'linktype'});
            selectnode.one('.fp-linktype-1 input').setAttrs({value: 1, name: 'linktype'});
            selectnode.one('.fp-linktype-4 input').setAttrs({value: 4, name: 'linktype'});
            selectnode.one('.fp-linktype-8 input').setAttrs({value: 8, name: 'linktype'});
            var changelinktype = function(e) {
                if (e.currentTarget.get('checked')) {
                    var allowinputs = e.currentTarget.get('value') != 1/*FILE_EXTERNAL*/;
                    selectnode.all('.fp-setauthor,.fp-setlicense,.fp-saveas').each(function(node){
                        node.addClassIf('uneditable', !allowinputs);
                        node.all('input,select').set('disabled', allowinputs?'':'disabled');
                    });

                    // If the link to the file is selected, only then.
                    // Remember: this is not to be done for all repos.
                    // Only for those repos where the filereferencewarning is set.
                    // The value 4 represents FILE_REFERENCE here.
                    if (e.currentTarget.get('value') === '4') {
                        var filereferencewarning = filePickerHelper.active_repo.filereferencewarning;
                        if (filereferencewarning) {
                            var fileReferenceNode = e.currentTarget.ancestor('.fp-linktype-4');
                            var fileReferenceWarningNode = Y.Node.create('<div/>').
                                addClass('alert alert-warning px-3 py-1 my-1 small').
                                setAttrs({role: 'alert'}).
                                setContent(filereferencewarning);
                            fileReferenceNode.append(fileReferenceWarningNode);
                        }
                    } else {
                        var fileReferenceInput = selectnode.one('.fp-linktype-4 input');
                        var fileReferenceWarningNode = fileReferenceInput.ancestor('.fp-linktype-4').one('.alert-warning');
                        if (fileReferenceWarningNode) {
                            fileReferenceWarningNode.remove();
                        }
                    }
                }
            };
            selectnode.all('.fp-linktype-2,.fp-linktype-1,.fp-linktype-4,.fp-linktype-8').each(function (node) {
                node.one('input').on('change', changelinktype, this);
            });
            // register event on clicking submit button
            getfile.on('click', function(e) {
                e.preventDefault();
                var client_id = this.options.client_id;
                var scope = this;
                var repository_id = this.active_repo.id;
                var title = selectnode.one('.fp-saveas input').get('value');
                var filesource = selectnode.one('form #filesource-'+client_id).get('value');
                var filesourcekey = selectnode.one('form #filesourcekey-'+client_id).get('value');
                var params = {
                    'title': title,
                    'source': filesource,
                    'savepath': this.options.savepath || '/',
                    'sourcekey': filesourcekey,
                };
                var license = selectnode.one('.fp-setlicense select');
                if (license) {
                    params['license'] = license.get('value');
                    var origlicense = selectnode.one('.fp-license .fp-value');
                    if (origlicense) {
                        origlicense = origlicense.getContent();
                    }
                    if (this.options.rememberuserlicensepref) {
                        this.set_preference('recentlicense', license.get('value'));
                    }
                }
                params['author'] = selectnode.one('.fp-setauthor input').get('value');

                var return_types = this.options.repositories[this.active_repo.id].return_types;
                if (this.options.env == 'editor') {
                    // in editor, images are stored in '/' only
                    params.savepath = '/';
                }
                if ((this.options.externallink || this.options.env != 'editor') &&
                            (return_types & 1/*FILE_EXTERNAL*/) &&
                            (this.options.return_types & 1/*FILE_EXTERNAL*/) &&
                            selectnode.one('.fp-linktype-1 input').get('checked')) {
                    params['linkexternal'] = 'yes';
                } else if ((return_types & 4/*FILE_REFERENCE*/) &&
                        (this.options.return_types & 4/*FILE_REFERENCE*/) &&
                        selectnode.one('.fp-linktype-4 input').get('checked')) {
                    params['usefilereference'] = '1';
                } else if ((return_types & 8/*FILE_CONTROLLED_LINK*/) &&
                        (this.options.return_types & 8/*FILE_CONTROLLED_LINK*/) &&
                        selectnode.one('.fp-linktype-8 input').get('checked')) {
                    params['usecontrolledlink'] = '1';
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
                        if (obj.event == 'fileexists') {
                            scope.process_existing_file(obj);
                            return;
                        }
                        if (scope.options.editor_target && scope.options.env=='editor') {
                            scope.options.editor_target.value=obj.url;
                            scope.options.editor_target.dispatchEvent(new Event('change'), {'bubbles': true});
                        }
                        scope.hide();
                        obj.client_id = client_id;
                        var formcallback_scope = args.scope.options.magicscope ? args.scope.options.magicscope : args.scope;
                        scope.options.formcallback.apply(formcallback_scope, [obj]);
                    }
                }, false);
            }, this);
            var elform = selectnode.one('form');
            elform.appendChild(Y.Node.create('<input/>').
                setAttrs({type:'hidden',id:'filesource-'+client_id}));
            elform.appendChild(Y.Node.create('<input/>').
                setAttrs({type:'hidden',id:'filesourcekey-'+client_id}));
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
            // First check there isn't already an interval in play, and if there is kill it now.
            if (this.waitinterval != null) {
                clearInterval(this.waitinterval);
            }
            // Prepare the root node we will set content for and the loading template we want to display as a YUI node.
            var root = this.fpnode.one('.fp-content');
            var content = Y.Node.create(M.core_filepicker.templates.loading).addClass('fp-content-hidden').setStyle('opacity', 0);
            var count = 0;
            // Initiate an interval, we will have a count which will increment every 100 milliseconds.
            // Count 0 - the loading icon will have visibility set to hidden (invisible) and have an opacity of 0 (invisible also)
            // Count 5 - the visiblity will be switched to visible but opacity will still be at 0 (inivisible)
            // Counts 6 - 15 opacity will be increased by 0.1 making the loading icon visible over the period of a second
            // Count 16 - The interval will be cancelled.
            var interval = setInterval(function(){
                if (!content || !root.contains(content) || count >= 15) {
                    clearInterval(interval);
                    return true;
                }
                if (count == 5) {
                    content.removeClass('fp-content-hidden');
                } else if (count > 5) {
                    var opacity = parseFloat(content.getStyle('opacity'));
                    content.setStyle('opacity', opacity + 0.1);
                }
                count++;
                return false;
            }, 100);
            // Store the wait interval so that we can check it in the future.
            this.waitinterval = interval;
            // Set the content to the loading template.
            root.setContent(content);
        },
        viewbar_set_enabled: function(mode) {
            var viewbar = this.fpnode.one('.fp-viewbar')
            if (viewbar) {
                if (mode) {
                    viewbar.addClass('enabled').removeClass('disabled');
                    this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').setAttribute("aria-disabled", "false");
                    this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').setAttribute("tabindex", "");
                } else {
                    viewbar.removeClass('enabled').addClass('disabled');
                    this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').setAttribute("aria-disabled", "true");
                    this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').setAttribute("tabindex", "-1");
                }
            }
            this.fpnode.all('.fp-vb-icons,.fp-vb-tree,.fp-vb-details').removeClass('checked');
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
                this.set_preference('recentviewmode', this.viewmode);
            }
        },
        render: function() {
            var client_id = this.options.client_id;
            var fpid = "filepicker-"+ client_id;
            var labelid = 'fp-dialog-label_'+ client_id;
            var width = 873;
            var draggable = true;
            this.fpnode = Y.Node.create(M.core_filepicker.templates.generallayout).
                set('id', 'filepicker-'+client_id).set('aria-labelledby', labelid);

            if (this.in_iframe()) {
                width = Math.floor(window.innerWidth * 0.95);
                draggable = false;
            }

            this.mainui = new M.core.dialogue({
                extraClasses : ['filepicker'],
                draggable    : draggable,
                bodyContent  : this.fpnode,
                headerContent: '<h3 id="'+ labelid +'">'+ M.util.get_string('filepicker', 'repository') +'</h3>',
                centered     : true,
                modal        : true,
                visible      : false,
                width        : width+'px',
                responsiveWidth : 768,
                height       : '558px',
                zIndex       : this.options.zIndex,
                focusOnPreviousTargetAfterHide: true,
                focusAfterHide: this.options.previousActiveElement
            });

            // create panel for selecting a file (initially hidden)
            this.selectnode = Y.Node.create(M.core_filepicker.templates.selectlayout).
                set('id', 'filepicker-select-'+client_id).
                set('aria-live', 'assertive').
                set('role', 'dialog');

            var fplabel = 'fp-file_label_'+ client_id;
            this.selectui = new M.core.dialogue({
                headerContent: '<h3 id="' + fplabel +'">'+M.util.get_string('select', 'repository')+'</h3>',
                draggable    : true,
                width        : '450px',
                bodyContent  : this.selectnode,
                centered     : true,
                modal        : true,
                visible      : false,
                zIndex       : this.options.zIndex
            });
            Y.one('#'+this.selectnode.get('id')).setAttribute('aria-labelledby', fplabel);
            // event handler for lazy loading of thumbnails and next page
            this.fpnode.one('.fp-content').on(['scroll','resize'], this.content_scrolled, this);
            // save template for one path element and location of path bar
            if (this.fpnode.one('.fp-path-folder')) {
                this.pathnode = this.fpnode.one('.fp-path-folder');
                this.pathbar = this.pathnode.get('parentNode');
                this.pathbar.removeChild(this.pathnode);
            }
            // assign callbacks for view mode switch buttons
            this.fpnode.one('.fp-vb-icons').on('click', this.viewbar_clicked, this);
            this.fpnode.one('.fp-vb-tree').on('click', this.viewbar_clicked, this);
            this.fpnode.one('.fp-vb-details').on('click', this.viewbar_clicked, this);

            // assign callbacks for toolbar links
            this.setup_toolbar();
            this.setup_select_file();
            this.hide_header();

            // processing repository listing
            // Resort the repositories by sortorder
            var sorted_repositories = [];
            var i;
            for (i in this.options.repositories) {
                sorted_repositories[i] = this.options.repositories[i];
            }
            sorted_repositories.sort(function(a,b){return a.sortorder-b.sortorder});
            // extract one repository template and repeat it for all repositories available,
            // set name and icon and assign callbacks
            var reponode = this.fpnode.one('.fp-repo');
            if (reponode) {
                var list = reponode.get('parentNode');
                list.removeChild(reponode);
                for (i in sorted_repositories) {
                    var repository = sorted_repositories[i];
                    var h = (parseInt(i) == 0) ? parseInt(i) : parseInt(i) - 1,
                        j = (parseInt(i) == Object.keys(sorted_repositories).length - 1) ? parseInt(i) : parseInt(i) + 1;
                    var previousrepository = sorted_repositories[h];
                    var nextrepository = sorted_repositories[j];
                    var node = reponode.cloneNode(true);
                    list.appendChild(node);
                    node.
                        set('id', 'fp-repo-'+client_id+'-'+repository.id).
                        on('click', function(e, repository_id) {
                            e.preventDefault();
                            this.set_preference('recentrepository', repository_id);
                            this.hide_header();
                            this.list({'repo_id':repository_id});
                        }, this /*handler running scope*/, repository.id/*second argument of handler*/);
                    node.on('key', function(e, previousrepositoryid, nextrepositoryid, clientid, repositoryid) {
                        this.changeHighlightedRepository(e, clientid, repositoryid, previousrepositoryid, nextrepositoryid);
                    }, 'down:38,40', this, previousrepository.id, nextrepository.id, client_id, repository.id);
                    node.on('key', function(e, repositoryid) {
                        e.preventDefault();
                        this.set_preference('recentrepository', repositoryid);
                        this.hide_header();
                        this.list({'repo_id': repositoryid});
                    }, 'enter', this, repository.id);
                    node.one('.fp-repo-name').setContent(Y.Escape.html(repository.name));
                    node.one('.fp-repo-icon').set('src', repository.icon);
                    if (i==0) {
                        node.addClass('first');
                    }
                    if (i==sorted_repositories.length-1) {
                        node.addClass('last');
                    }
                    if (i%2) {
                        node.addClass('even');
                    } else {
                        node.addClass('odd');
                    }
                }
            }
            // display error if no repositories found
            if (sorted_repositories.length==0) {
                this.display_error(M.util.get_string('norepositoriesavailable', 'repository'), 'norepositoriesavailable')
            }
            // display repository that was used last time
            this.mainui.show();
            this.show_recent_repository();
        },
        /**
         * Change the highlighted repository to a new one.
         *
         * @param  {object} event The key event
         * @param  {integer} clientid The client id to identify the repo class.
         * @param  {integer} oldrepositoryid The repository id that we are removing the highlight for
         * @param  {integer} previousrepositoryid The previous repository id.
         * @param  {integer} nextrepositoryid The next repository id.
         */
        changeHighlightedRepository: function(event, clientid, oldrepositoryid, previousrepositoryid, nextrepositoryid) {
            event.preventDefault();
            var newrepositoryid = (event.keyCode == '40') ? nextrepositoryid : previousrepositoryid;
            this.fpnode.one('#fp-repo-' + clientid + '-' + oldrepositoryid).setAttribute('tabindex', '-1');
            this.fpnode.one('#fp-repo-' + clientid + '-' + newrepositoryid)
                    .setAttribute('tabindex', '0')
                    .focus();
        },
        parse_repository_options: function(data, appendtolist) {
            if (appendtolist) {
                if (data.list) {
                    if (!this.filelist) {
                        this.filelist = [];
                    }
                    for (var i in data.list) {
                        this.filelist[this.filelist.length] = data.list[i];
                    }
                }
            } else {
                this.filelist = data.list?data.list:null;
                this.lazyloading = {};
            }
            this.filepath = data.path?data.path:null;
            this.objecttag = data.object?data.object:null;
            this.active_repo = {};
            this.active_repo.issearchresult = data.issearchresult ? true : false;
            this.active_repo.defaultreturntype = data.defaultreturntype?data.defaultreturntype:null;
            this.active_repo.dynload = data.dynload?data.dynload:false;
            this.active_repo.pages = Number(data.pages?data.pages:null);
            this.active_repo.page = Number(data.page?data.page:null);
            this.active_repo.hasmorepages = (this.active_repo.pages && this.active_repo.page && (this.active_repo.page < this.active_repo.pages || this.active_repo.pages == -1))
            this.active_repo.id = data.repo_id?data.repo_id:null;
            this.active_repo.nosearch = (data.login || data.nosearch); // this is either login form or 'nosearch' attribute set
            this.active_repo.norefresh = (data.login || data.norefresh); // this is either login form or 'norefresh' attribute set
            this.active_repo.nologin = (data.login || data.nologin); // this is either login form or 'nologin' attribute is set
            this.active_repo.logouttext = data.logouttext?data.logouttext:null;
            this.active_repo.logouturl = (data.logouturl || '');
            this.active_repo.message = (data.message || '');
            this.active_repo.help = data.help?data.help:null;
            this.active_repo.manage = data.manage?data.manage:null;
            // Warning message related to the file reference option, if applicable to the given repository.
            this.active_repo.filereferencewarning = data.filereferencewarning ? data.filereferencewarning : null;
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
                    container.removeChild(templates[i]);
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
                } else if(l[k].type=='textarea') {
                    // textarea element
                    if (node.one('label')) {
                        node.one('label').set('for', l[k].id).setContent(l[k].label);
                    }
                    node.one('textarea').setAttrs({id:l[k].id, name:l[k].name});
                } else if(l[k].type=='select') {
                    // select element
                    if (node.one('label')) {
                        node.one('label').set('for', l[k].id).setContent(l[k].label);
                    }
                    node.one('select').setAttrs({id:l[k].id, name:l[k].name}).setContent('');
                    for (i in l[k].options) {
                        node.one('select').appendChild(
                            Y.Node.create('<option/>').
                                set('value', l[k].options[i].value).
                                setContent(l[k].options[i].label));
                    }
                } else if(l[k].type=='radio') {
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
                } else {
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
            var scope = args.scope;
            // highlight the current repository in repositories list
            scope.fpnode.all('.fp-repo.active')
                    .removeClass('active')
                    .setAttribute('aria-selected', 'false')
                    .setAttribute('tabindex', '-1');
            scope.fpnode.all('.nav-link')
                    .removeClass('active')
                    .setAttribute('aria-selected', 'false')
                    .setAttribute('tabindex', '-1');
            var activenode = scope.fpnode.one('#fp-repo-' + scope.options.client_id + '-' + obj.repo_id);
            activenode.addClass('active')
                    .setAttribute('aria-selected', 'true')
                    .setAttribute('tabindex', '0');
            activenode.all('.nav-link').addClass('active');
            // add class repository_REPTYPE to the filepicker (for repository-specific styles)
            for (var i in scope.options.repositories) {
                scope.fpnode.removeClass('repository_'+scope.options.repositories[i].type)
            }
            if (obj.repo_id && scope.options.repositories[obj.repo_id]) {
                scope.fpnode.addClass('repository_'+scope.options.repositories[obj.repo_id].type)
            }
            var filepickerContent = Y.one('.file-picker .fp-repo-items');
            filepickerContent.focus();
            // Load popover for the filepicker content.
            require(['theme_boost/bootstrap/popover'], function(Popover) {
                var popoverTriggerList = filepickerContent.getDOMNode().querySelectorAll('[data-bs-toggle="popover"]');
                popoverTriggerList.forEach((popoverTriggerEl) => {
                    new Popover(popoverTriggerEl);
                });
            });

            // display response
            if (obj.login) {
                scope.viewbar_set_enabled(false);
                scope.print_login(obj);
            } else if (obj.upload) {
                scope.viewbar_set_enabled(false);
                scope.parse_repository_options(obj);
                scope.create_upload_form(obj);
            } else if (obj.object) {
                M.core_filepicker.active_filepicker = scope;
                scope.viewbar_set_enabled(false);
                scope.parse_repository_options(obj);
                scope.create_object_container(obj.object);
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
            if (!args.path) {
                args.path = '';
            }
            this.currentpath = args.path;
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
        populateLicensesSelect: function(licensenode, filenode) {
            if (!licensenode) {
                return;
            }
            licensenode.setContent('');
            var selectedlicense = this.options.defaultlicense;
            if (filenode) {
                // File has a license already, use it.
                selectedlicense = filenode.license;
            } else if (this.options.rememberuserlicensepref && this.get_preference('recentlicense')) {
                // When 'Remember user licence preference' is enabled use the last license selected by the user, if any.
                selectedlicense = this.get_preference('recentlicense');
            }
            var licenses = this.options.licenses;
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
        create_object_container: function(data) {
            var content = this.fpnode.one('.fp-content');
            content.setContent('');
            //var str = '<object data="'+data.src+'" type="'+data.type+'" width="98%" height="98%" id="container_object" class="fp-object-container mdl-align"></object>';
            var container = Y.Node.create('<object/>').
                setAttrs({data:data.src, type:data.type, id:'container_object'}).
                addClass('fp-object-container');
            content.setContent('').appendChild(container);
        },
        create_upload_form: function(data) {
            var client_id = this.options.client_id;
            var id = data.upload.id+'_'+client_id;
            var content = this.fpnode.one('.fp-content');
            var template_name = 'uploadform_'+this.options.repositories[data.repo_id].type;
            var template = M.core_filepicker.templates[template_name] || M.core_filepicker.templates['uploadform'];
            content.setContent(template);

            content.all('.fp-file,.fp-saveas,.fp-setauthor,.fp-setlicense').each(function (node) {
                node.all('label').set('for', node.one('input,select').generateID());
            });
            content.one('form').set('id', id);
            content.one('.fp-file input').set('name', 'repo_upload_file');
            if (data.upload.label && content.one('.fp-file label')) {
                content.one('.fp-file label').setContent(data.upload.label);
            }
            content.one('.fp-saveas input').set('name', 'title');
            content.one('.fp-setauthor input').setAttrs({name:'author', value:this.options.author});
            content.one('.fp-setlicense select').set('name', 'license');
            this.populateLicensesSelect(content.one('.fp-setlicense select'));
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

                if (this.options.rememberuserlicensepref) {
                    this.set_preference('recentlicense', license.get('value'));
                }
                if (!content.one('.fp-file input').get('value')) {
                    scope.print_msg(M.util.get_string('nofilesattached', 'repository'), 'error');
                    return false;
                }
                this.hide_header();
                scope.request({
                        scope: scope,
                        action:'upload',
                        client_id: client_id,
                        params: {'savepath': scope.options.savepath || '/'},
                        repository_id: scope.active_repo.id,
                        form: {id: id, upload:true},
                        onerror: function(id, o, args) {
                            scope.create_upload_form(data);
                        },
                        callback: function(id, o, args) {
                            if (o.event == 'fileexists') {
                                scope.create_upload_form(data);
                                scope.process_existing_file(o);
                                return;
                            }
                            if (scope.options.editor_target&&scope.options.env=='editor') {
                                scope.options.editor_target.value=o.url;
                                scope.options.editor_target.dispatchEvent(new Event('change'), {'bubbles': true});
                            }
                            scope.hide();
                            o.client_id = client_id;
                            var formcallback_scope = args.scope.options.magicscope ? args.scope.options.magicscope : args.scope;
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
                if (this.active_repo.logouturl) {
                    window.open(this.active_repo.logouturl, 'repo_auth', 'location=0,status=0,width=500,height=300,scrollbars=yes');
                }
            }, this);
            toolbar.one('.fp-tb-refresh').one('a,button').on('click', function(e) {
                e.preventDefault();
                if (!this.active_repo.norefresh) {
                    this.list({ path: this.currentpath });
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
            toolbar.one('.fp-tb-help').one('a,button').
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
                            // Highlight search text when user click for search.
                            var searchnode = searchform.one('input[name="s"]');
                            if (searchnode) {
                                searchnode.once('click', function(e) {
                                    e.preventDefault();
                                    this.select();
                                });
                            }
                        }
                    }
                }, false);
            }

            // refresh button
            // weather we use cache for this instance, this button will reload listing anyway
            enable_tb_control(toolbar.one('.fp-tb-refresh'), !r.norefresh);

            // login button
            enable_tb_control(toolbar.one('.fp-tb-logout'), !r.nologin);

            // manage url
            enable_tb_control(toolbar.one('.fp-tb-manage'), r.manage);
            Y.one('#fp-tb-manage-'+client_id+'-link').set('href', r.manage);

            // help url
            enable_tb_control(toolbar.one('.fp-tb-help'), r.help);
            Y.one('#fp-tb-help-'+client_id+'-link').set('href', r.help);

            // message
            enable_tb_control(toolbar.one('.fp-tb-message'), r.message);
            toolbar.one('.fp-tb-message').setContent(r.message);
        },
        print_path: function() {
            if (!this.pathbar) {
                return;
            }
            this.pathbar.setContent('').addClass('empty');
            var p = this.filepath;
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
                    el.all('.fp-path-folder-name').setContent(Y.Escape.html(p[i].name));
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
            var repository_id = this.get_preference('recentrepository');
            this.viewmode = this.get_preference('recentviewmode');
            if (this.viewmode != 2 && this.viewmode != 3) {
                this.viewmode = 1;
            }
            if (this.options.repositories[repository_id]) {
                this.list({'repo_id':repository_id});
            }
        },
        get_preference: function (name) {
            if (this.options.userprefs[name]) {
                return this.options.userprefs[name];
            } else {
                return false;
            }
        },
        set_preference: function(name, value) {
            if (this.options.userprefs[name] != value) {
                require(['core_user/repository'], function(UserRepository) {
                    UserRepository.setUserPreference('filepicker_' + name, value);
                    this.options.userprefs[name] = value;
                }.bind(this));
            }
        },
        in_iframe: function () {
            // If we're not the top window then we're in an iFrame
            return window.self !== window.top;
        }
    });
    var loading = Y.one('#filepicker-loading-'+options.client_id);
    if (loading) {
        loading.setStyle('display', 'none');
    }
    M.core_filepicker.instances[options.client_id] = new FilePickerHelper(options);
};
