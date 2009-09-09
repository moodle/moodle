function filepicker_callback(params) {
    var html = '<a href="'+params['url']+'">'+params['file']+'</a>';
    // TODO: support delete the draft file
    document.getElementById('file_info_'+params['client_id']).innerHTML = html;
}

// launch file picker from filepicker element
function launch_filepicker(id, client_id, itemid) {
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
    params.savepath = '/';
    params.target = el;
    params.callback = filepicker_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}
