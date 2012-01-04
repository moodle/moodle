// backup files tree
// Author: Dongsheng Cai <dongsheng@moodle.com>
M.core_backup_files_tree = {
    y3: null,
    api: M.cfg.wwwroot+'/files/filebrowser_ajax.php',
    request: function(url, node, cb) {
        var api = this.api + '?action=getfiletree';
        var params = [];
        params['contextid'] = this.get_param(url, 'contextid', -1);
        params['component'] = this.get_param(url, 'component', null);
        params['filearea']  = this.get_param(url, 'filearea', null);
        params['itemid']    = this.get_param(url, 'itemid', -1);
        params['filepath']  = this.get_param(url, 'filepath', null);
        params['filename']  = this.get_param(url, 'filename', null);
        if (params['filearea'] == 'backup' && params['component'] == 'user') {
            // XXX: the id in params['contextid'] is current context
            // request file list, so should be user context
            params['contextid'] = this.usercontextid;
        }
        var scope = this;
        params['sesskey']=M.cfg.sesskey;
        var cfg = {
            method: 'POST',
            on: {
                complete: function(id,o,p) {
                    try {
                        var data = this.y3.JSON.parse(o.responseText);
                    } catch(e) {
                        alert(e.toString());
                        return;
                    }
                    if (data && data.length==0) {
                        node.isLeaf = true;
                    } else {
                        for (i in data) {
                            if (data[i].isdir) {
                                var info = {label: data[i].filename, href: data[i].url};
                                var n = new YAHOO.widget.TextNode(info, node, false);
                                YAHOO.util.Event.addListener(n.labelElId, "click", function(e) {
                                    YAHOO.util.Event.preventDefault(e);
                                });
                                n.isLeaf = false;
                            } else {
                                var params = data[i].params;
                                if (params['filearea'] == 'backup' && params['component'] == 'user') {
                                    // XXX: display the restore link, so should be context id
                                    params['contextid'] = scope.currentcontextid;
                                }
                                params.action = 'choosebackupfile';
                                var restoreurl = M.cfg.wwwroot+'/backup/restorefile.php?'+build_querystring(params);
                                var info = {label: data[i].filename, 'href': data[i].url, 'restoreurl': restoreurl};
                                params['filename'] = data[i].filename;
                                var n = new YAHOO.widget.RestoreNode(info, node, false);
                                n.isLeaf = true;
                            }
                        }
                    }
                    cb();
                }
            },
            arguments: {
                scope: scope
            },
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            data: build_querystring(params),
            context: this
        };
        this.y3.io(api, cfg);
    },
    init : function(Y, options){
        var htmlid = options.htmlid;
        this.usercontextid = options.usercontextid;
        this.currentcontextid = options.currentcontextid;
        var tree = new YAHOO.widget.TreeView(htmlid);
        tree.setDynamicLoad(this.dynload);
        var root = tree.getRoot();
        var children = root.children;
        tree.subscribe("clickEvent", function(e) {
            if(!e.node.isLeaf){
                e.node.toggle();
            }
        });
        for (i in children) {
            var node = children[i];
            if (node.className == 'file-tree-folder') {
                node.isLeaf = false;
                // prevent link
                YAHOO.util.Event.addListener(node.labelElId, "click", function(e) {
                    YAHOO.util.Event.preventDefault(e);
                });
            } else {
                node.isLeaf = true;
            }
        }
        tree.render();
        this.y3 = Y;
    },
    dynload: function(node, oncompletecb) {
        M.core_backup_files_tree.request(node.href, node, oncompletecb);
    },
    get_param: function(url, name, val) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec(url);
        if( results == null ) {
            return val;
        } else {
            return unescape(results[1]);
        }
    }
}

YAHOO.widget.RestoreNode = function(oData, oParent, expanded) {

    if (oData) {
        if (YAHOO.lang.isString(oData)) {
            oData = { label: oData };
        }
        this.init(oData, oParent, expanded);
        this.setUpLabel(oData);
    }

};
YAHOO.extend(YAHOO.widget.RestoreNode, YAHOO.widget.TextNode, {
    labelStyle: "ygtvlabel",
    labelElId: null,
    label: null,
    title: null,
    href: null,
    target: "_blank",
    _type: "RestoreNode",
    setUpLabel: function(oData) {
        if (YAHOO.lang.isString(oData)) {
            oData = {
                label: oData
            };
        } else {
            if (oData.style) {
                this.labelStyle = oData.style;
            }
        }

        this.label = oData.label;
        this.restoreurl = oData.restoreurl;
        this.labelElId = "ygtvlabelel" + this.index;
    },
    getContentHtml: function() {
        var sb = [];
        sb[sb.length] = '<a';
        sb[sb.length] = ' id="' + this.labelElId + '"';
        sb[sb.length] = ' class="' + this.labelStyle  + '"';
        if (this.href) {
            sb[sb.length] = ' href="' + this.href + '"';
            sb[sb.length] = ' target="' + this.target + '"';
        }
        if (this.title) {
            sb[sb.length] = ' title="' + this.title + '"';
        }
        sb[sb.length] = ' >';
        sb[sb.length] = this.label;
        sb[sb.length] = '</a>';
        sb[sb.length] = ' <a href="'+this.restoreurl+'">'+M.str.moodle.restore+'</a>';
        return sb.join("");
    }
});
