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
                            var tmp = new YAHOO.widget.HTMLNode('<div>'+data[i].icon+'&nbsp;<a href="'+data[i].url+'">'+data[i].filename+'</a></div>', node, false);
                            if (data[i].isdir) {
                                tmp.isLeaf = false;
                                tmp.isDir = true;
                            } else {
                                tmp.isLeaf = true;
                                tmp.isFile = true;
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
                children[i].isDir = true;
            } else {
                children[i].isLeaf = true;
                children[i].isFile = true;
            }
        }
        tree.render();
        this.y3 = Y;
    },
    dynload: function(node, oncompletecb) {
        var tmp = document.createElement('p');
        tmp.innerHTML = node.html;
        var links = tmp.getElementsByTagName('a');
        var link = links[0].href;
        M.core_filetree.request(link, node, oncompletecb);
    },
    onclick: function(e) {
        YAHOO.util.Event.preventDefault(e);
        if (e.node.isFile) {
            var tmp = document.createElement('p');
            tmp.innerHTML = e.node.html;
            var links = tmp.getElementsByTagName('a');
            var link = links[0].href;
            window.location = link;
        }
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
};
