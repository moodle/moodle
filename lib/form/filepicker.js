function updatefile(client_id, obj) {
    document.getElementById('repo_info_'+client_id).innerHTML = obj['file'];
}
function callpicker(client_id, id) {
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
    params.callback = updatefile;
    open_filepicker(client_id, params);
    return false;
}
