// File Tree Viewer
// Author: Dongsheng Cai <dongsheng@moodle.com>
M.core_filetree = {
    y3: null,
    api: M.cfg.wwwroot+'/files/filebrowser_ajax.php',
    request: function(url, node, cb) {
        var api = this.api + '?action=getfiletree';
        var params = [];
        params['contextid'] = this.get_param(url, 'contextid', -1);
        params['component'] = this.get_param(url, 'component', null);
        params['filearea'] = this.get_param(url, 'filearea', null);
        params['itemid'] = this.get_param(url, 'itemid', -1);
        params['filepath'] = this.get_param(url, 'filepath', null);
        params['filename'] = this.get_param(url, 'filename', null);
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
                            var mynode = {
                                label: data[i].filename,
                                href: data[i].url
                            };
                            var tmp = new YAHOO.widget.TextNode(mynode, node, false);
                            if (data[i].isdir) {
                                tmp.isLeaf = false;
                            } else {
                                tmp.isLeaf = true;
                                tmp.target = '_blank';
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
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            data: build_querystring(params),
            context: this
        };
        this.y3.io(api, cfg);
    },
    init : function(Y){
        var tree = new YAHOO.widget.TreeView('course-file-tree-view');
        tree.setDynamicLoad(this.dynload);
        tree.subscribe("clickEvent", this.onclick);
        var root = tree.getRoot();
        var children = root.children;
        for (i in children) {
            if (children[i].className == 'file-tree-folder') {
                children[i].isLeaf = false;
            } else {
                children[i].isLeaf = true;
            }
        }
        tree.render();
        this.y3 = Y;
    }, 
    dynload: function(node, oncompletecb) {
        M.core_filetree.request(node.href, node, oncompletecb);
    },
    onclick: function(e) {
        YAHOO.util.Event.preventDefault(e); 
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
