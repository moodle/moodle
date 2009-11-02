function url_callback(params) {
}
function url_launch_filepicker(id, client_id, itemid) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById(id);
    var params = {};
    params.env = 'url';
    params.itemid = itemid;
    params.maxbytes = -1;
    params.maxfiles = -1;
    params.savepath = '/';
    params.target = el;
    params.callback = url_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}
