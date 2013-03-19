M.form_submit = {};

M.form_submit.init = function(Y, options) {
    Y.on('submit', function(e) {
        if (!containsErrors) {
            e.target.one('#'+options.submitid).setAttribute('disabled', 'true');
        }
    }, '#'+options.formid);
};