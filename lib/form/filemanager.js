var selected_file = null;
var rm_cb = {
    success: function(o) {
        if(o.responseText){
            repository_client.files[o.responseText]--;
            selected_file.parentNode.removeChild(selected_file);
        }
    }
}
function rm_file(id, name, context) {
    if (confirm(filemanager.strdelete)) {
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            moodle_cfg.wwwroot+'/repository/ws.php?action=delete&itemid='+id,
                rm_cb,
                'title='+name+'&client_id='+filemanager.clientid
                );
        selected_file = context.parentNode;
    }
}
function fp_callback(obj) {
    var list = document.getElementById('draftfiles-'+obj.client_id);
    var html = '<li><a href="'+obj['url']+'"><img src="'+obj['icon']+'" class="icon" /> '+obj['file']+'</a> ';
    html += '<a href="###" onclick=\'rm_file('+obj['id']+', "'+obj['file']+'", this)\'><img src="'+filemanager.deleteicon+'" class="iconsmall" /></a>';
    html += '</li>';
    list.innerHTML += html;
}
function callpicker(el_id, client_id, itemid) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById(el_id);
    var params = {};
    params.env = 'filemanager';
    params.maxbytes = filemanager.maxbytes;
    params.maxfiles = filemanager.maxfiles;
    params.itemid = itemid;
    params.target = el;
    params.callback = fp_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}