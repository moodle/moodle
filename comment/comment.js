/**
 * Javascript for comments 2.0
 */

function cmt_replace(client_id,list,newcmt) {
    var ret = {};
    ret.ids = [];
    var template = document.getElementById('cmt-tmpl');
    var html = '';
    for(var i in list) {
        var htmlid = 'comment-'+list[i].id+'-'+client_id;
        var val = template.innerHTML;
        val = val.replace('___name___', list[i].username);
        if (list[i]['delete']||newcmt) {
            list[i].content = '<div class="comment-delete"><a href="###" title="'+mstr.moodle.deletecomment+'" onclick="delete_comment(\''+client_id+'\',\''+list[i].id+'\')"><img src="'+moodle_cfg.wwwroot+'/pix/t/delete.gif" /></a></div>' + list[i].content;
        }
        val = val.replace('___time___', list[i].time);
        val = val.replace('___picture___', list[i].avatar);
        val = val.replace('___content___', list[i].content);
        val = '<li id="'+htmlid+'">'+val+'</li>';
        ret.ids.push(htmlid);
        html = (val+html);
    }
    ret.html = html;
    return ret;
}
function cmt_load(cid) {
    var container = document.getElementById('comment-list-'+cid);
    container.innerHTML = '<div style="text-align:center"><img src="'+moodle_cfg.wwwroot+'/pix/i/loading.gif'+'" /></div>';
}
function get_comments(client_id, area, itemid, page) {
    var url = moodle_cfg.wwwroot + '/comment/comment_ajax.php';
    var data = {
        'courseid': comment_params.courseid,
        'contextid': comment_params.contextid,
        'area': area,
        'itemid': itemid,
        'page': page,
        'client_id': client_id,
        'sesskey': moodle_cfg.sesskey
    }
    this.cb = {
        success: function(o) {
            var ret = json_decode(o.responseText);
            if (!comment_check_response(ret)) {
                return;
            }
            var linktext = document.getElementById('comment-link-text-'+ret.client_id);
            linktext.innerHTML = mstr.moodle.comments + ' ('+ret.count+')';
            var container = document.getElementById('comment-list-'+ret.client_id);
            var pagination = document.getElementById('comment-pagination-'+ret.client_id);
            if (ret.pagination) {
                pagination.innerHTML = ret.pagination;
            } else {
                //empty paging bar
                pagination.innerHTML = '';
            }
            var result = cmt_replace(ret.client_id, ret.list);
            container.innerHTML = result.html;
        }
    }
    cmt_load(client_id);
    var trans = YAHOO.util.Connect.asyncRequest('POST',
        url+'?action=get', this.cb, build_querystring(data));
}
function post_comment(cid) {
    this.cb = {
        success: function(o) {
            var resp = json_decode(o.responseText);
            if (!comment_check_response(resp)) {
                return;
            }
            if(resp) {
                var cid = resp.client_id;
                var ta = document.getElementById('dlg-content-'+cid);
                ta.value = '';
                var container = document.getElementById('comment-list-'+cid);
                var result = cmt_replace(cid,[resp], true);
                container.innerHTML += result.html;
                var ids = result.ids;
                var linktext = document.getElementById('comment-link-text-'+resp.client_id);
                linktext.innerHTML = mstr.moodle.comments + ' ('+resp.count+')';
                for(var i in ids) {
                    var attributes = {
                        color: { to: '#06e' },
                        backgroundColor: { to: '#FFE390' }
                    };
                    var anim = new YAHOO.util.ColorAnim(ids[i], attributes);
                    anim.animate();
                }
            }
        }
    }
    var ta = document.getElementById('dlg-content-'+cid);
    if (ta.value && ta.value != mstr.moodle.addcomment) {
        var url = moodle_cfg.wwwroot + '/comment/comment_ajax.php';
        var formObject = document.getElementById('comment-form-'+cid);
        YAHOO.util.Connect.setForm(formObject);
        var trans = YAHOO.util.Connect.asyncRequest('POST', url+'?action=add', this.cb);
    } else {
        var attributes = {
            backgroundColor: { from: '#FFE390', to:'#FFFFFF' }
        };
        var anim = new YAHOO.util.ColorAnim('dlg-content-'+cid, attributes);
        anim.animate();
    }
}
function delete_comment(client_id, comment_id) {
    var url = moodle_cfg.wwwroot + '/comment/comment_ajax.php';
    var data = {
        'courseid': comment_params.courseid,
        'contextid': comment_params.contextid,
        'commentid': comment_id,
        'client_id': client_id,
        'sesskey': moodle_cfg.sesskey
    }
    this.cb = {
        success: function(o) {
            var resp = json_decode(o.responseText);
            if (!comment_check_response(resp)) {
                return;
            }
            var htmlid= 'comment-'+resp.commentid+'-'+resp.client_id;
            this.el = document.getElementById(htmlid);
            this.el.style.overflow = 'hidden';
            var attributes = {
                width:{to:0},
                height:{to:0}
            };
            var anim = new YAHOO.util.Anim(htmlid, attributes, 1, YAHOO.util.Easing.easeOut);
            anim.onComplete.subscribe(this.remove_dom, [], this);
            anim.animate();
        },
        remove_dom: function() {
            this.el.parentNode.removeChild(this.el);
        }
    }
    var trans = YAHOO.util.Connect.asyncRequest('POST',
        url+'?action=delete', this.cb, build_querystring(data));
}
function view_comments(client_id, area, itemid, page) {
    var container = document.getElementById('comment-ctrl-'+client_id);
    var ta = document.getElementById('dlg-content-'+client_id);
    var img = document.getElementById('comment-img-'+client_id);
    if (container.style.display=='none'||container.style.display=='') {
        // show
        get_comments(client_id, area, itemid, page);
        container.style.display = 'block';
        img.src=moodle_cfg.wwwroot+'/pix/t/expanded.png';
    } else {
        // hide
        container.style.display = 'none';
        img.src=moodle_cfg.wwwroot+'/pix/t/collapsed.png';
        ta.value = '';
    }
    toggle_textarea.apply(ta, [false]);
    // reset textarea size
    ta.onclick = function() {
        toggle_textarea.apply(this, [true]);
    }
    ta.onkeypress = function() {
        if (this.scrollHeight > this.clientHeight && !window.opera)
            this.rows += 1;
    }
    ta.onblur = function() {
        toggle_textarea.apply(this, [false]);
    }
    return false;
}
function comment_hide_link(cid) {
    var link = document.getElementById('comment-link-'+cid);
    if(link){
        link.style.display='none';
    } else {
    }
}
function toggle_textarea(focus) {
    if (focus) {
        if (this.value == mstr.moodle.addcomment) {
            this.value = '';
            this.style.color = 'black';
        }
    }else{
        if (this.value == '') {
            this.value = mstr.moodle.addcomment;
            this.style.color = 'grey';
            this.rows = 1;
        }
    }
}
function comment_check_response(data) {
    if (data.error) {
        alert(data.error);
        return false;
    }
    return true;
}
