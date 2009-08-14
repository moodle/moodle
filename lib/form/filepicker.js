function fp_callback(params) {
    document.getElementById('file_info_'+params['client_id']).innerHTML = '<div class="mdl-left"><a href="'+params['url']+'">'+params['file']+'</a></div>';
}
function callpicker(id, client_id, itemid) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById(id);
    var params = {};
    params.env = 'filepicker';
    params.itemid = itemid;
    params.maxbytes = filepicker.maxbytes;
    params.maxfiles = filepicker.maxfiles;
    params.target = el;
    params.callback = fp_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}
