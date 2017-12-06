/**
 * @package dataformfield
 * @subpackage duration
 * @copyright  2014 Itamar Tzadok
 */

/**
 * Required alert in entries form
 */
M.dataformfield_duration_required = {};

M.dataformfield_duration_required.init = function(Y, options) {
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
            var content = element.one('input').get('value');

            element.all('.felement.ftext.error').remove();
            if (content == '') {
                element.one('.fgrouplabel').insert('<div class="felement ftext error"><span class="error">' + err_message + '</span></div>', 'after');
            }
        }

        var selector = '#fitem_id_' + fieldname + '.fitem.required';

        if (!Y.one(selector)) {
            return;
        }

        Y.one(selector).on('change', required);
        required(null, Y.one(selector));
    });
};
