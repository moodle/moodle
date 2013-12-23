var PASSWORDUNMASK = function() {
    PASSWORDUNMASK.superclass.constructor.apply(this, arguments);
};

Y.extend(PASSWORDUNMASK, Y.Base, {
    // Initialize checkbox if id is passed.
    initializer : function(params) {
        if (params && params.formid) {
            this.add_checkbox(params.formid, params.checkboxlabel, params.checkboxname);
        }
    },

    // Create checkbox for unmasking password.
    add_checkbox : function(elementid, checkboxlabel, checkboxname) {
        var node = Y.one('#'+elementid);

        // Retaining unmask div from previous implementation.
        var unmaskdiv = Y.Node.create('<div id="'+elementid+'unmaskdiv" class="unmask"></div>');

        // Add checkbox for unmasking to unmaskdiv.
        var unmaskchb = Y.Node.create('<input id="'+elementid+'unmask" type="checkbox" name="'+
            checkboxname+'unmask">');
        unmaskdiv.appendChild(unmaskchb);
        // Attach event using static javascript function for unmasking password.
        unmaskchb.on('click', function() {unmaskPassword(elementid);});

        // Add label for checkbox to unmaskdiv.
        var unmasklabel = Y.Node.create('<label for="'+elementid+'unmask">'+checkboxlabel+'</label>');
        unmaskdiv.appendChild(unmasklabel);

        // Insert unmask div in the same div as password input.
        node.get('parentNode').insert(unmaskdiv, node.get('lastNode'));
    }
});

M.form = M.form || {};
M.form.passwordunmask = function(params) {
    return new PASSWORDUNMASK(params);
};
