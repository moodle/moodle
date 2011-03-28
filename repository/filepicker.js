// YUI3 File Picker module for moodle
// Author: Dongsheng Cai <dongsheng@moodle.com>

/**
 *
 * File Picker UI
 * =====
 * this.rendered, it tracks if YUI Panel rendered
 * this.api, stores the URL to make ajax request
 * this.mainui, YUI Panel
 * this.treeview, YUI Treeview
 * this.viewbar, a button group to switch view mode
 * this.viewmode, store current view mode
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
            var api = this.api + '?action='+args.action;
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
                        var panel_id = '#panel-'+client_id;
                        if (!o) {
                            alert('IO FATAL');
                            return;
                        }
                        var data = null;
                        try {
                            data = Y.JSON.parse(o.responseText);
                        } catch(e) {
                            scope.print_msg(M.str.repository.invalidjson, 'error');
                            Y.one(panel_id).set('innerHTML', 'ERROR: '+M.str.repository.invalidjson+'<pre>'+stripHTML(o.responseText)+'</pre>');
                            return;
                        }
                        // error checking
                        if (data && data.error) {
                            scope.print_msg(data.error, 'error');
                            scope.list();
                            return;
                        } else {
                            if (data.msg) {
                                scope.print_msg(data.msg, 'info');
                            }
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
            Y.io(api, cfg);
            if (redraw) {
                this.wait('load');
            }
        },
        print_msg: function(msg, type) {
            var client_id = this.options.client_id;
            var dlg_id = 'fp-msg-dlg-'+client_id;
            function handleYes() {
                this.hide();
            }
            var icon = YAHOO.widget.SimpleDialog.ICON_INFO;
            if (type=='error') {
                icon = YAHOO.widget.SimpleDialog.ICON_ALARM;
            }
            if (!this.msg_dlg) {
                this.msg_dlg = new YAHOO.widget.SimpleDialog(dlg_id,
                     { width: "300px",
                       fixedcenter: true,
                       visible: true,
                       draggable: true,
                       close: true,
                       text: msg,
                       modal: false,
                       icon: icon,
                       zindex: 9999992,
                       constraintoviewport: true,
                       buttons: [{ text:M.str.moodle.ok, handler:handleYes, isDefault:true }]
                     });
                this.msg_dlg.render(document.body);
            } else {
                this.msg_dlg.setBody(msg);
            }
            var header = M.str.moodle.info;
            if (type=='error') {
                header = M.str.moodle.error;
            }
            this.msg_dlg.setHeader(type);
            this.msg_dlg.show();
        },
        build_tree: function(node, level) {
            var client_id = this.options.client_id;
            var dynload = this.active_repo.dynload;
            if(node.children) {
                node.title = '<i><u>'+node.title+'</u></i>';
            }
            var info = {
                label:node.title,
                //title:fp_lang.date+' '+node.date+fp_lang.size+' '+node.size,
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
        view_files: function(page) {
            var p= page?page:null;
            if (this.active_repo.issearchresult) {
                // list view is desiged to display treeview
                // it is not working well with search result
                this.view_as_icons();
            } else {
                this.viewbar.set('disabled', false);
                if (this.viewmode == 1) {
                    this.view_as_icons();
                } else if (this.viewmode == 2) {
                    this.view_as_list(p);
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
                    obj.issearchresult = false;
                    var list = obj.list;
                    scope.viewbar.set('disabled', false);
                    scope.parse_repository_options(obj);
                    for(k in list) {
                        scope.build_tree(list[k], node);
                    }
                    cb();
                }
            }, false);
        },
        view_as_list: function(p) {
            var scope = this;
            var page = null;
            if (!p) {
                if (scope.active_repo.page) {
                    page = scope.active_repo.page;
                }
            } else {
                page = p;
            }
            scope.request({
                action:'list',
                client_id: scope.options.client_id,
                repository_id: scope.active_repo.id,
                path:'',
                page:page,
                callback: function(id, obj, args) {
                    scope.parse_repository_options(obj);
                    if (obj.login) {
                        scope.viewbar.set('disabled', true);
                        scope.print_login(obj);
                        return;
                    }
                    var client_id = scope.options.client_id;
                    var dynload = scope.active_repo.dynload;
                    var list = obj.list;
                    var panel_id = '#panel-'+client_id;
                    scope.viewmode = 2;
                    Y.one(panel_id).set('innerHTML', '');

                    scope.print_header();

                    var html = '<div class="fp-tree-panel" id="treeview-'+client_id+'">';
                    if (list && list.length==0) {
                        html += '<div class="fp-emptylist mdl-align">' +M.str.repository.nofilesavailable+'</div>';
                    }
                    html += '</div>';

                    var tree = Y.Node.create(html);
                    Y.one(panel_id).appendChild(tree);
                    if (!list || list.length==0) {
                        return;
                    }

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
                }
            }, true);
        },
        view_as_icons: function() {
            var scope = this;
            var client_id = this.options.client_id;
            var list = this.filelist;
            var panel_id = '#panel-'+client_id;
            this.viewmode = 1;
            Y.one(panel_id).set('innerHTML', '');

            this.print_header();

            var html = '<div class="fp-grid-panel" id="fp-grid-panel-'+client_id+'">';
            if (list && list.length==0) {
                html += '<div class="fp-emptylist mdl-align">' +M.str.repository.nofilesavailable+'</div>';
            }
            html += '</div>';

            var gridpanel = Y.Node.create(html);
            Y.one('#panel-'+client_id).appendChild(gridpanel);
            var count = 0;
            for(var k in list) {
                var node = list[k];
                var grid = document.createElement('DIV');
                grid.className='fp-grid';
                // the file name
                var title = document.createElement('DIV');
                title.id = 'grid-title-'+client_id+'-'+String(count);
                title.className = 'label';
                var filename = node.title;
                if (node.shorttitle) {
                    filename = node.shorttitle;
                }
                var filename_id = 'filname-link-'+client_id+'-'+String(count);
                title.innerHTML += '<a href="###" id="'+filename_id+'" title="'+node.title+'"><span>'+filename+"</span></a>";


                if(node.thumbnail_width){
                    grid.style.width = node.thumbnail_width+'px';
                    title.style.width = (node.thumbnail_width-10)+'px';
                } else {
                    grid.style.width = title.style.width = '90px';
                }
                var frame = document.createElement('DIV');
                frame.style.textAlign='center';
                if(node.thumbnail_height){
                    frame.style.height = node.thumbnail_height+'px';
                }
                var img = document.createElement('img');
                img.src = node.thumbnail;
                img.title = node.title;
                if(node.thumbnail_alt) {
                    img.alt = node.thumbnail_alt;
                }
                if(node.thumbnail_title) {
                    img.title = node.thumbnail_title;
                }

                var link = document.createElement('A');
                link.href='###';
                link.id = 'img-id-'+client_id+'-'+String(count);
                if(node.url) {
                    // hide
                    //grid.innerHTML += '<p><a target="_blank" href="'+node.url+'">'+M.str.repository.preview+'</a></p>';
                }
                link.appendChild(img);
                frame.appendChild(link);
                grid.appendChild(frame);
                grid.appendChild(title);
                gridpanel.appendChild(grid);

                var y_title = Y.one('#'+title.id);
                var y_file = Y.one('#'+link.id);
                var dynload = this.active_repo.dynload;
                if(node.children) {
                    y_file.on('click', function(e, p) {
                        if(dynload) {
                            var params = {'path':p.path};
                            scope.list(params);
                        }else{
                            this.filelist = p.children;
                            this.view_files();
                        }
                    }, this, node);
                    y_title.on('click', function(e, p, id){
                        var icon = Y.one(id);
                        icon.simulate('click');
                    }, this, node, '#'+link.id);
                } else {
                    var fileinfo = {};
                    fileinfo['title'] = list[k].title;
                    fileinfo['source'] = list[k].source;
                    fileinfo['thumbnail'] = list[k].thumbnail;
                    fileinfo['haslicense'] = list[k].haslicense?true:false;
                    fileinfo['hasauthor'] = list[k].hasauthor?true:false;
                    y_title.on('click', function(e, args) {
                        this.select_file(args);
                    }, this, fileinfo);
                    y_file.on('click', function(e, args) {
                        this.select_file(args);
                    }, this, fileinfo);
                }
                count++;
            }
        },
        select_file: function(args) {
            var client_id = this.options.client_id;
            var thumbnail = Y.one('#fp-grid-panel-'+client_id);
            if(thumbnail){
                thumbnail.setStyle('display', 'none');
            }
            var header = Y.one('#fp-header-'+client_id);
            if (header) {
                header.setStyle('display', 'none');
            }
            var footer = Y.one('#fp-footer-'+client_id);
            if (footer) {
                footer.setStyle('display', 'none');
            }
            var path = Y.one('#path-'+client_id);
            if(path){
                path.setStyle('display', 'none');
            }
            var panel = Y.one('#panel-'+client_id);
            var form_id = 'fp-rename-form-'+client_id;
            var html = '<div class="fp-rename-form" id="'+form_id+'">';
            html += '<p><img src="'+args.thumbnail+'" /></p>';
            html += '<table width="100%">';
            html += '<tr><td class="mdl-right"><label for="newname-'+client_id+'">'+M.str.repository.saveas+':</label></td>';
            html += '<td class="mdl-left"><input type="text" id="newname-'+client_id+'" value="'+args.title+'" /></td></tr>';

            var le_checked = '';
            var le_style = '';
            if (this.options.repositories[this.active_repo.id].return_types == 1) {
                // support external links only
                le_checked = 'checked';
                le_style = ' style="display:none;"';
            } else if(this.options.repositories[this.active_repo.id].return_types == 2) {
                // support internal files only
                le_style = ' style="display:none;"';
            }
            if ((this.options.externallink && this.options.env == 'editor' && this.options.return_types != 1)) {
                html += '<tr'+le_style+'><td></td><td class="mdl-left"><input type="checkbox" id="linkexternal-'+client_id+'" value="" '+le_checked+' />'+M.str.repository.linkexternal+'</td></tr>';
            }

            if (!args.hasauthor) {
                // the author of the file
                html += '<tr><td class="mdl-right"><label for="text-author">'+M.str.repository.author+' :</label></td>';
                html += '<td class="mdl-left"><input id="text-author-'+client_id+'" type="text" name="author" value="'+this.options.author+'" /></td>';
                html += '</tr>';
            }

            if (!args.haslicense) {
                // the license of the file
                var licenses = this.options.licenses;
                html += '<tr><td class="mdl-right"><label for="select-license-'+client_id+'">'+M.str.repository.chooselicense+' :</label></td>';
                html += '<td class="mdl-left"><select name="license" id="select-license-'+client_id+'">';
                var recentlicense = YAHOO.util.Cookie.get('recentlicense');
                if (recentlicense) {
                    this.options.defaultlicense=recentlicense;
                }
                for (var i in licenses) {
                    if (this.options.defaultlicense==licenses[i].shortname) {
                        var selected = ' selected';
                    } else {
                        var selected = '';
                    }
                    html += '<option value="'+licenses[i].shortname+'"'+selected+'>'+licenses[i].fullname+'</option>';
                }
                html += '</select></td></tr>';
            }
            html += '</table>';

            html += '<p><input type="hidden" id="filesource-'+client_id+'" value="'+args.source+'" />';
            html += '<input type="button" id="fp-confirm-'+client_id+'" value="'+M.str.repository.getfile+'" />';
            html += '<input type="button" id="fp-cancel-'+client_id+'" value="'+M.str.moodle.cancel+'" /></p>';
            html += '</div>';

            var getfile_form = Y.Node.create(html);
            panel.appendChild(getfile_form);

            var getfile = Y.one('#fp-confirm-'+client_id);
            getfile.on('click', function(e) {
                var client_id = this.options.client_id;
                var scope = this;
                var repository_id = this.active_repo.id;
                var title = Y.one('#newname-'+client_id).get('value');
                var filesource = Y.one('#filesource-'+client_id).get('value');
                var params = {'title':title, 'source':filesource, 'savepath': this.options.savepath};
                var license = Y.one('#select-license-'+client_id);
                if (license) {
                    params['license'] = license.get('value');
                    YAHOO.util.Cookie.set('recentlicense', license.get('value'));
                }
                var author = Y.one('#text-author-'+client_id);
                if (author){
                    params['author'] = author.get('value');
                }

                if (this.options.env == 'editor') {
                    // in editor, images are stored in '/' only
                    params.savepath = '/';
                    // when image or media button is clicked
                    if ( this.options.return_types != 1 ) {
                        var linkexternal = Y.one('#linkexternal-'+client_id).get('checked');
                        if (linkexternal) {
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

                this.wait('download', title);
                this.request({
                    action:'download',
                    client_id: client_id,
                    repository_id: repository_id,
                    'params': params,
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
                }, true);
            }, this);
            var elform = Y.one('#'+form_id);
            elform.on('keydown', function(e) {
                if (e.keyCode == 13) {
                    getfile.simulate('click');
                    e.preventDefault();
                }
            }, this);
            var cancel = Y.one('#fp-cancel-'+client_id);
            cancel.on('click', function(e) {
                this.view_files();
            }, this);
            var treeview = Y.one('#treeview-'+client_id);
            if (treeview){
                treeview.setStyle('display', 'none');
            }
        },
        wait: function(type) {
            var panel = Y.one('#panel-'+this.options.client_id);
            panel.set('innerHTML', '');
            var name = '';
            var str = '<div style="text-align:center">';
            if(type=='load') {
                str += '<img src="'+M.util.image_url('i/loading')+'" />';
                str += '<p>'+M.str.repository.loading+'</p>';
            }else{
                str += '<img src="'+M.util.image_url('i/progressbar')+'" />';
                str += '<p>'+M.str.repository.copying+' <strong>'+name+'</strong></p>';
            }
            str += '</div>';
            try {
                panel.set('innerHTML', str);
            } catch(e) {
                alert(e.toString());
            }
        },
        render: function() {
            var client_id = this.options.client_id;
            var scope = this;
            var filepicker_id = 'filepicker-'+client_id;
            var fpnode = Y.Node.create('<div class="file-picker" id="'+filepicker_id+'"></div>');
            Y.one(document.body).appendChild(fpnode);
            // render file picker panel
            this.mainui = new YAHOO.widget.Panel(filepicker_id, {
                draggable: true,
                close: true,
                underlay: 'none',
                zindex: 9999990,
                monitorresize: false,
                xy: [50, YAHOO.util.Dom.getDocumentScrollTop()+20]
            });
            var layout = null;
            this.mainui.beforeRenderEvent.subscribe(function() {
                YAHOO.util.Event.onAvailable('layout-'+client_id, function() {
                    layout = new YAHOO.widget.Layout('layout-'+client_id, {
                        height: 480, width: 700,
                        units: [
                        {position: 'top', height: 32, resize: false,
                        body:'<div class="yui-buttongroup fp-viewbar" id="fp-viewbar-'+client_id+'"></div><div class="fp-searchbar" id="search-div-'+client_id+'"></div>', gutter: '2'},
                        {position: 'left', width: 200, resize: true, scroll:true,
                        body:'<ul class="fp-list" id="fp-list-'+client_id+'"></ul>', gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
                        {position: 'center', body: '<div class="fp-panel" id="panel-'+client_id+'"></div>',
                        scroll: true, gutter: '0 2 0 0' }
                        ]
                    });
                    layout.render();
                    scope.show_recent_repository();
                });
            });

            this.mainui.setHeader(M.str.repository.filepicker);
            this.mainui.setBody('<div id="layout-'+client_id+'"></div>');
            this.mainui.render();
            this.rendered = true;

            var scope = this;
            // adding buttons
            var view_icons = {label: M.str.repository.iconview, value: 't', 'checked': true,
                onclick: {
                    fn: function(){
                        scope.view_as_icons();
                    }
                }
            };
            var view_listing = {label: M.str.repository.listview, value: 'l',
                onclick: {
                    fn: function(){
                        scope.view_as_list();
                    }
                }
            };
            this.viewbar = new YAHOO.widget.ButtonGroup({
                id: 'btngroup-'+client_id,
                name: 'buttons',
                disabled: true,
                container: 'fp-viewbar-'+client_id
            });
            this.viewbar.addButtons([view_icons, view_listing]);
            // processing repository listing
            var r = this.options.repositories;
            Y.on('contentready', function(el) {
                var list = Y.one(el);
                var count = 0;
                for (var i in r) {
                    var id = 'repository-'+client_id+'-'+r[i].id;
                    var link_id = id + '-link';
                    list.append('<li id="'+id+'"><a class="fp-repo-name" id="'+link_id+'" href="###">'+r[i].name+'</a></li>');
                    Y.one('#'+link_id).prepend('<img src="'+r[i].icon+'" width="16" height="16" />&nbsp;');
                    Y.one('#'+link_id).on('click', function(e, scope, repository_id) {
                        YAHOO.util.Cookie.set('recentrepository', repository_id);
                        scope.repository_id = repository_id;
                        this.list({'repo_id':repository_id});
                    }, this /*handler running scope*/, this/*second argument*/, r[i].id/*third argument of handler*/);
                    count++;
                }
                if (count==0) {
                    if (this.options.externallink) {
                        list.set('innerHTML', M.str.repository.norepositoriesexternalavailable);
                    } else {
                        list.set('innerHTML', M.str.repository.norepositoriesavailable);
                    }
                }
            }, '#fp-list-'+client_id, this /* handler running scope */, '#fp-list-'+client_id /*first argument of handler*/);
        },
        parse_repository_options: function(data) {
            this.filelist = data.list?data.list:null;
            this.filepath = data.path?data.path:null;
            this.active_repo = {};
            this.active_repo.issearchresult = Boolean(data.issearchresult);
            this.active_repo.dynload = data.dynload?data.dynload:false;
            this.active_repo.pages = Number(data.pages?data.pages:null);
            this.active_repo.page = Number(data.page?data.page:null);
            this.active_repo.id = data.repo_id?data.repo_id:null;
            this.active_repo.nosearch = data.nosearch?true:false;
            this.active_repo.norefresh = data.norefresh?true:false;
            this.active_repo.nologin = data.nologin?true:false;
            this.active_repo.logouttext = data.logouttext?data.logouttext:null;
            this.active_repo.help = data.help?data.help:null;
            this.active_repo.manage = data.manage?data.manage:null;
        },
        print_login: function(data) {
            this.parse_repository_options(data);
            var client_id = this.options.client_id;
            var repository_id = data.repo_id;
            var l = this.logindata = data.login;
            var loginurl = '';
            var panel = Y.one('#panel-'+client_id);
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
                    this.request({
                        'params': params,
                        'scope': scope,
                        'action':'signin',
                        'path': '',
                        'client_id': client_id,
                        'repository_id': repository_id,
                        'callback': function(id, o, args) {
                            scope.parse_repository_options(o);
                            scope.view_files();
                        }
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
                    this.request({
                            scope: scope,
                            action:'search',
                            client_id: client_id,
                            repository_id: repository_id,
                            form: {id: 'fp-form-'+scope.options.client_id,upload:false,useDisabled:true},
                            callback: function(id, o, args) {
                                o.issearchresult = true;
                                scope.parse_repository_options(o);
                                scope.view_files();
                            }
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
        search: function(args) {
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
            this.request({
                    scope: scope,
                    action:'search',
                    client_id: client_id,
                    repository_id: repository_id,
                    form: {id: 'fp-form-'+scope.options.client_id,upload:false,useDisabled:true},
                    callback: function(id, o, args) {
                        o.issearchresult = true;
                        scope.parse_repository_options(o);
                        scope.view_files();
                    }
            }, true);
        },
        list: function(args) {
            var scope = this;
            if (!args) {
                args = {};
            }
            if (!args.repo_id) {
                args.repo_id = scope.active_repo.id;
            }
            scope.request({
                action:'list',
                client_id: scope.options.client_id,
                repository_id: args.repo_id,
                path:args.path?args.path:'',
                page:args.page?args.page:'',
                callback: function(id, obj, args) {
                    Y.all('#fp-list-'+scope.options.client_id+' li a').setStyle('backgroundColor', 'transparent');
                    var el = Y.one('#repository-'+scope.options.client_id+'-'+obj.repo_id+'-link');
                    if (el) {
                        el.setStyle('backgroundColor', '#AACCEE');
                    }
                    if (obj.login) {
                        scope.viewbar.set('disabled', true);
                        scope.print_login(obj);
                    } else if (obj.upload) {
                        scope.viewbar.set('disabled', true);
                        scope.parse_repository_options(obj);
                        scope.create_upload_form(obj);

                    } else if (obj.iframe) {

                    } else if (obj.list) {
                        obj.issearchresult = false;
                        scope.viewbar.set('disabled', false);
                        scope.parse_repository_options(obj);
                        scope.view_files();
                    }
                }
            }, true);
        },
        create_upload_form: function(data) {
            var client_id = this.options.client_id;
            Y.one('#panel-'+client_id).set('innerHTML', '');
            var types = this.options.accepted_types;

            this.print_header();
            var id = data.upload.id+'_'+client_id;
            var str = '<div id="'+id+'_div" class="fp-upload-form mdl-align">';
            str += '<form id="'+id+'" method="POST">';
            str += '<table width="100%">';
            str += '<tr><td class="mdl-right">';
            str += '<label for="'+id+'_file">'+data.upload.label+': </label></td>';
            str += '<td class="mdl-left"><input type="file" id="'+id+'_file" name="repo_upload_file" />';
            str += '<tr><td class="mdl-right"><label for="newname-'+client_id+'">'+M.str.repository.saveas+':</label></td>';
            str += '<td class="mdl-left"><input type="text" name="title" id="newname-'+client_id+'" value="" /></td></tr>';
            str += '<input type="hidden" name="itemid" value="'+this.options.itemid+'" />';
            for (var i in types) {
                str += '<input type="hidden" name="accepted_types[]" value="'+types[i]+'" />';
            }
            str += '</td></tr><tr>';
            str += '<td class="mdl-right"><label>'+M.str.repository.author+': </label></td>';
            str += '<td class="mdl-left"><input type="text" name="author" value="'+this.options.author+'" /></td>';
            str += '</tr>';
            str += '<tr>';
            str += '<td class="mdl-right">'+M.str.repository.chooselicense+': </td>';
            str += '<td class="mdl-left">';
            var licenses = this.options.licenses;
            str += '<select name="license" id="select-license-'+client_id+'">';
            var recentlicense = YAHOO.util.Cookie.get('recentlicense');
            if (recentlicense) {
                this.options.defaultlicense=recentlicense;
            }
            for (var i in licenses) {
                if (this.options.defaultlicense==licenses[i].shortname) {
                    var selected = ' selected';
                } else {
                    var selected = '';
                }
                str += '<option value="'+licenses[i].shortname+'"'+selected+'>'+licenses[i].fullname+'</option>';
            }
            str += '</select>';
            str += '</td>';
            str += '</tr></table>';
            str += '</form>';
            str += '<div class="fp-upload-btn"><button id="'+id+'_action">'+M.str.repository.upload+'</button></div>';
            str += '</div>';
            var upload_form = Y.Node.create(str);
            Y.one('#panel-'+client_id).appendChild(upload_form);
            var scope = this;
            Y.one('#'+id+'_action').on('click', function(e) {
                e.preventDefault();
                var license = Y.one('#select-license-'+client_id).get('value');
                YAHOO.util.Cookie.set('recentlicense', license);
                if (!Y.one('#'+id+'_file').get('value')) {
                    scope.print_msg(M.str.repository.nofilesattached, 'error');
                    return false;
                }
                Y.use('io-upload-iframe', function() {
                    scope.request({
                            scope: scope,
                            action:'upload',
                            client_id: client_id,
                            params: {'savepath':scope.options.savepath},
                            repository_id: scope.active_repo.id,
                            form: {id: id, upload:true},
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
                });
            }, this);
        },
        print_header: function() {
            var r = this.active_repo;
            var scope = this;
            var client_id = this.options.client_id;
            var repository_id = this.active_repo.id;
            var panel = Y.one('#panel-'+client_id);
            var str = '<div id="fp-header-'+client_id+'">';
            str += '<div class="fp-toolbar" id="repo-tb-'+client_id+'"></div>';
            str += '</div>';
            var head = Y.Node.create(str);
            panel.appendChild(head);
            //if(this.active_repo.pages < 8){
                this.print_paging('header');
            //}


            var toolbar = Y.one('#repo-tb-'+client_id);

            if(!r.nosearch) {
                var html = '<a href="###"><img src="'+M.util.image_url('a/search')+'" /> '+M.str.repository.search+'</a>';
                var search = Y.Node.create(html);
                search.on('click', function() {
                    scope.request({
                        scope: scope,
                        action:'searchform',
                        repository_id: repository_id,
                        callback: function(id, obj, args) {
                            var scope = args.scope;
                            var client_id = scope.options.client_id;
                            var repository_id = scope.active_repo.id;
                            var container = document.getElementById('fp-search-dlg');
                            if(container) {
                                container.innerHTML = '';
                                container.parentNode.removeChild(container);
                            }
                            var container = document.createElement('DIV');
                            container.id = 'fp-search-dlg';

                            var dlg_title = document.createElement('DIV');
                            dlg_title.className = 'hd';
                            dlg_title.innerHTML = M.str.repository.search;

                            var dlg_body = document.createElement('DIV');
                            dlg_body.className = 'bd';

                            var sform = document.createElement('FORM');
                            sform.method = 'POST';
                            sform.id = "fp-search-form";
                            sform.innerHTML = obj.form;

                            dlg_body.appendChild(sform);
                            container.appendChild(dlg_title);
                            container.appendChild(dlg_body);
                            Y.one(document.body).appendChild(container);
                            var search_dialog= null;
                            function dialog_handler() {
                                scope.viewbar.set('disabled', false);
                                scope.request({
                                        scope: scope,
                                        action:'search',
                                        client_id: client_id,
                                        repository_id: repository_id,
                                        form: {id: 'fp-search-form',upload:false,useDisabled:true},
                                        callback: function(id, o, args) {
                                            scope.parse_repository_options(o);
                                            scope.view_files();
                                        }
                                }, true);
                                search_dialog.cancel();
                            }
                            Y.one('#fp-search-form').on('keydown', function(e){
                                if (e.keyCode == 13) {
                                    dialog_handler();
                                    e.preventDefault();
                                }
                            }, this);

                            search_dialog = new YAHOO.widget.Dialog("fp-search-dlg", {
                               postmethod: 'async',
                               draggable: true,
                               width : "30em",
                               modal: true,
                               fixedcenter : true,
                               zindex: 9999991,
                               visible : false,
                               constraintoviewport : true,
                               buttons: [
                               {
                                   text:M.str.repository.submit,
                                   handler:dialog_handler,
                                   isDefault:true
                               }, {
                                   text:M.str.moodle.cancel,
                                   handler:function(){
                                       this.destroy()
                                   }
                               }]
                            });
                            search_dialog.render();
                            search_dialog.show();
                        }
                    });
                },this);
                toolbar.appendChild(search);
            }
            // weather we use cache for this instance, this button will reload listing anyway
            if(!r.norefresh) {
                var html = '<a href="###"><img src="'+M.util.image_url('a/refresh')+'" /> '+M.str.repository.refresh+'</a>';
                var refresh = Y.Node.create(html);
                refresh.on('click', function() {
                    this.list();
                }, this);
                toolbar.appendChild(refresh);
            }
            if(!r.nologin) {
                var label = r.logouttext?r.logouttext:M.str.repository.logout;
                var html = '<a href="###"><img src="'+M.util.image_url('a/logout')+'" /> '+label+'</a>';
                var logout = Y.Node.create(html);
                logout.on('click', function() {
                    this.request({
                        action:'logout',
                        client_id: client_id,
                        repository_id: repository_id,
                        path:'',
                        callback: function(id, obj, args) {
                            scope.viewbar.set('disabled', true);
                            scope.print_login(obj);
                        }
                    }, true);
                }, this);
                toolbar.appendChild(logout);
            }

            if(r.manage) {
                var mgr = document.createElement('A');
                mgr.href = r.manage;
                mgr.target = "_blank";
                mgr.innerHTML = '<img src="'+M.util.image_url('a/setting')+'" /> '+M.str.repository.manageurl;
                toolbar.appendChild(mgr);
            }
            if(r.help) {
                var help = document.createElement('A');
                help.href = r.help;
                help.target = "_blank";
                help.innerHTML = '<img src="'+M.util.image_url('a/help')+'" /> '+M.str.repository.help;
                toolbar.appendChild(help);
            }

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
            var client_id = this.options.client_id;
            var scope = this;
            var r = this.active_repo;
            var str = '';
            var action = '';
            if(r.pages > 1) {
                str += '<div class="fp-paging" id="paging-'+html_id+'-'+client_id+'">';
                str += this.get_page_button(1)+'1</a> ';

                var span = 5;
                var ex = (span-1)/2;

                if (r.page+ex>=r.pages) {
                    var max = r.pages;
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
                if (max==r.pages) {
                    str += this.get_page_button(r.pages)+r.pages+'</a>';
                } else {
                    str += this.get_page_button(max)+max+'</a>';
                    str += ' ... '+this.get_page_button(r.pages)+r.pages+'</a>';
                }
                str += '</div>';
            }
            if (str) {
                var a = Y.Node.create(str);
                Y.one('#fp-header-'+client_id).appendChild(a);

                Y.all('#fp-header-'+client_id+' .fp-paging a').each(
                    function(node, id) {
                        node.on('click', function(e) {
                            var id = node.get('id');
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
                                        callback: function(id, o, args) {
                                            o.issearchresult = true;
                                            scope.parse_repository_options(o);
                                            scope.view_files(result[1]);
                                        }
                                }, true);

                            } else {
                                if (scope.viewmode == 2) {
                                    scope.view_as_list(result[1]);
                                } else {
                                    scope.list(args);
                                }
                            }
                        });
                    });
            }
        },
        print_path: function() {
            var client_id = this.options.client_id;
            var panel = Y.one('#panel-'+client_id);
            var p = this.filepath;
            if (p && p.length!=0) {
                var path = Y.Node.create('<div id="path-'+client_id+'" class="fp-pathbar"></div>');
                panel.appendChild(path);
                for(var i = 0; i < p.length; i++) {
                    var link_path = p[i].path;
                    var link = document.createElement('A');
                    link.href = "###";
                    link.innerHTML = p[i].name;
                    link.id = 'path-node-'+client_id+'-'+i;
                    var sep = Y.Node.create('<span>/</span>');
                    path.appendChild(link);
                    path.appendChild(sep);
                    Y.one('#'+link.id).on('click', function(Y, path){
                        this.list({'path':path});
                        }, this, link_path)
                }
            }
        },
        hide: function() {
            this.mainui.hide();
        },
        show: function() {
            if (this.rendered) {
                var panel = Y.one('#panel-'+this.options.client_id);
                panel.set('innerHTML', '');
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
            var repository_id = YAHOO.util.Cookie.get('recentrepository');
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
