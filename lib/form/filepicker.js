
M.form_filepicker = {};


M.form_filepicker.callback = function(params) {
    var html = '<a href="'+params['url']+'">'+params['file']+'</a>';
    document.getElementById('file_info_'+params['client_id']).innerHTML = html;
};

/**
 * This fucntion is called for each file picker on page.
 */
M.form_filepicker.init = function(Y, options) {
    options.formcallback = M.form_filepicker.callback;
    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options); 
    }
    Y.on('click', function(e, client_id) {
        e.preventDefault();
        M.core_filepicker.instances[client_id].show();
    }, '#filepicker-button-'+options.client_id, null, options.client_id);

    var item = document.getElementById('nonjs-filepicker-'+options.client_id);
    if (item) {
        item.parentNode.removeChild(item);
    }
    item = document.getElementById('filepicker-wrapper-'+options.client_id);
    if (item) {
        item.style.display = '';
    }
};
