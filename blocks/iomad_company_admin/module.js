M.blocks_iomad_company_admin = {};

M.blocks_eldms_company_admin.init = YUI().use('node', function(Y) {

    var submitButton = Y.one('#id_submitbutton');
    var cancelButton = Y.one('#id_cancel');

    submitButton.setStyle('display','none');
    cancelButton.setStyle('display','none');
});
