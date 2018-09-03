YUI.add('moodle-auth-passwordunmask', function(Y) {
    var PASSWORDUNMASK = function() {
        PASSWORDUNMASK.superclass.constructor.apply(this, arguments);
    }

    Y.extend(PASSWORDUNMASK, Y.Base, {
        // Initialize checkboxes.
        initializer : function(params) {
            this.add_checkboxes();
        },
        // Create checkboxes for all unmasking passwords.
        add_checkboxes : function() {
            Y.all('#authmenu input[type=password]').each(function(node) {
                var checkboxlabel = M.util.get_string('unmaskpassword', 'core_form');
                var elementid = node.get('id');
                var elementname = node.get('name');

                // Retain unmask div from previous implementation.
                var unmaskdiv = Y.Node.create('<div id="'+elementid+'unmaskdiv" class="unmask"></div>');

                // Add checkbox for unmasking to unmaskdiv.
                var unmaskchb = Y.Node.create('<input id="'+elementid+'unmask" type="checkbox" name="'+elementname+'unmask">');
                unmaskdiv.appendChild(unmaskchb);
                //Attach event using static javascript function for unmasking password.
                unmaskchb.on('click', function() {unmaskPassword(elementid);});

                // Add label for checkbox to unmaskdiv.
                var unmasklabel = Y.Node.create('<label for="'+elementid+'unmask">'+checkboxlabel+'</label>');
                unmaskdiv.appendChild(unmasklabel);

                // Insert unmask div in the same div as password input.
                node.get('parentNode').insert(unmaskdiv, node.get('lastNode'));
            });
            return;
        }
    });

    M.auth = M.auth || {};
    M.auth.passwordunmask = function(params) {
        return new PASSWORDUNMASK(params);
    }
}, '@VERSION@', {requires:['base', 'node']});
