
M.form_filepicker = {};


M.form_filepicker.callback = function(params) {
    var html = '<a href="'+params['url']+'">'+params['file']+'</a>';
    document.getElementById('file_info_'+params['client_id']).innerHTML = html;
    //When file is added then set draftid for validation
    var elementname = M.core_filepicker.instances[params['client_id']].options.elementname;
    var itemid = M.core_filepicker.instances[params['client_id']].options.itemid;
    M.form_filepicker.YUI.one('#id_'+elementname).set('value', itemid);
    //generate event to indicate changes which will be used by disable if code.
    M.form_filepicker.YUI.one('#id_'+elementname).simulate('change');
};

/**
 * This fucntion is called for each file picker on page.
 */
M.form_filepicker.init = function(Y, options) {
    //For client side validation, remove hidden draft_id
    M.form_filepicker.YUI = Y;
    Y.one('#id_'+options.elementname).set('value', '');
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
