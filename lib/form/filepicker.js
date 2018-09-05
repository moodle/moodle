
M.form_filepicker = {};
M.form_filepicker.Y = null;
M.form_filepicker.instances = [];

M.form_filepicker.callback = function(params) {
    var html = '<a href="'+params['url']+'">'+params['file']+'</a>';
    html += '<div class="dndupload-progressbars"></div>';
    M.form_filepicker.Y.one('#file_info_'+params['client_id'] + ' .filepicker-filename').setContent(html);
    //When file is added then set status of global variable to true
    var elementname = M.core_filepicker.instances[params['client_id']].options.elementname;
    M.form_filepicker.instances[elementname].fileadded = true;
    //generate event to indicate changes which will be used by disable if or validation code
    M.form_filepicker.Y.one('#id_'+elementname).simulate('change');
};

/**
 * This fucntion is called for each file picker on page.
 */
M.form_filepicker.init = function(Y, options) {
    //Keep reference of YUI, so that it can be used in callback.
    M.form_filepicker.Y = Y;

    //For client side validation, initialize file status for this filepicker
    M.form_filepicker.instances[options.elementname] = {};
    M.form_filepicker.instances[options.elementname].fileadded = false;

    //Set filepicker callback
    options.formcallback = M.form_filepicker.callback;

    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options);
    }
    Y.on('click', function(e, client_id) {
        e.preventDefault();
        if (this.ancestor('.fitem.disabled') == null) {
            M.core_filepicker.instances[client_id].show();
        }
    }, '#filepicker-button-'+options.client_id, null, options.client_id);

    var item = document.getElementById('nonjs-filepicker-'+options.client_id);
    if (item) {
        item.parentNode.removeChild(item);
    }
    item = document.getElementById('filepicker-wrapper-'+options.client_id);
    if (item) {
        item.style.display = '';
    }

    var dndoptions = {
        clientid: options.client_id,
        acceptedtypes: options.accepted_types,
        author: options.author,
        maxfiles: -1,
        maxbytes: options.maxbytes,
        itemid: options.itemid,
        repositories: options.repositories,
        formcallback: options.formcallback,
        containerprefix: '#file_info_',
        containerid: 'file_info_'+options.client_id,
        contextid: options.context.id
    };
    M.form_dndupload.init(Y, dndoptions);
};
