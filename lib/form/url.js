M.form_url = {};

M.form_url.init = function(Y, options) {
    options.formcallback = M.form_url.callback;
    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options);
    }
    Y.on('click', function(e, client_id) {
        e.preventDefault();
        M.core_filepicker.instances[client_id].show();
    }, '#filepicker-button-js-'+options.client_id, null, options.client_id);
};

M.form_url.callback = function (params) {
    // Get the first child of type 'input' within the wrapper div associated with the filepicker's id.
    var urlElement = document.getElementById('url-wrapper-' + params.client_id).getElementsByTagName('input')[0];
    urlElement.value = params.url;
    urlElement.focus();
};
