function filepicker_callback(params) {
    alert('test');
    var html = '<div class="mdl-left"><a href="'+params['url']+'">'+params['file']+'</a>';
    html += '<a href="###" onclick=\'rm_file('+params['id']+', "'+params['file']+'", this)\'>ii</a>';
    html += '</div>';
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
    params.target = el;
    params.callback = filepicker_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}
