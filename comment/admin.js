YAHOO.util.Event.onDOMReady(init);
function init() {
    var select_all = document.getElementById('comment_select_all');
    select_all.onclick = function() {
        var comments = document.getElementsByName('comments');
        var checked = false;
        for (var i in comments) {
            if (comments[i].checked) {
                checked=true;
            }
        }
        for (var i in comments) {
            comments[i].checked = !checked;
        }
        this.checked = !checked;
    }
    var comments_delete = document.getElementById('comments_delete');
    comments_delete.onclick = function() {
        delete_comments();
    }
}
function delete_comments() {
    var url = moodle_cfg.wwwroot + '/comment/index.php';
    var cb = {
        success:function(o) {
            if (o.responseText == 'yes') {
                location.reload();
            }
        }
    }
    var comments = document.getElementsByName('comments');
    var list = '';
    for (var i in comments) {
        if (comments[i].checked) {
            list += (comments[i].value + '-');
        }
    }
    var data = {
        'commentids': list,
        'sesskey': moodle_cfg.sesskey
    }
    var trans = YAHOO.util.Connect.asyncRequest('POST',
        url+'?action=delete', cb, build_querystring(data));
}
