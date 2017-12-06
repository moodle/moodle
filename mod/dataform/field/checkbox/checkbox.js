/**
 * @package dataformfield
 * @subpackage checkbox
 * @copyright  2013 Itamar Tzadok
 */

/**
 * Required alert in entries form
 */
M.dataformfield_checkbox_required = {};

M.dataformfield_checkbox_required.init = function(Y, options) {
    Y.use('node', function (Y) {
        var fieldname = options.fieldname;
        var err_message = options.message;

        var required = function (e, element) {
            if (e) {
                element = this;
            }

            if (!element) {
                return;
            }

            var empty = true;
            element.all('.checkboxgroup' + fieldname + '_selected').each(function(check) {
                if (check.get('checked')) {
                    empty = false;
                }
            });
            element.all('.felement.ftext.error').remove();
            if (empty) {
                element.one('.fgrouplabel').insert('<div class="felement ftext error"><span class="error">' + err_message + '</span></div>', 'after');
            }
        }

        var selector = '#fgroup_id_' + fieldname + '.fitem.required';

        if (!Y.one(selector)) {
            return;
        }

        Y.one(selector).on('click', required);
        required(null, Y.one(selector));
    });
};
