/*global M*/
M.mod_realtimequiz = {};

M.mod_realtimequiz.init_editpage = function (Y) {
    "use strict";
    var realtimequizhelper = {
        lastradio: null,

        highlight_correct: function () {
            Y.all('.realtimequiz_answerradio').each(function (radiobtn) {
                var textbox, lastradio;
                if (radiobtn.get('checked')) {
                    textbox = Y.one('#id_answertext_' + radiobtn.get('value'));
                    if (textbox && textbox.get('value') === '' && this.lastradio) {
                        lastradio = Y.one('#' + this.lastradio);
                        if (lastradio) {
                            lastradio.set('checked', true);
                            lastradio.ancestor().ancestor().addClass('realtimequiz_highlight_correct');
                        }
                    } else {
                        radiobtn.ancestor().ancestor().addClass('realtimequiz_highlight_correct');
                        this.lastradio = radiobtn.get('id');
                    }
                } else {
                    radiobtn.ancestor().ancestor().removeClass('realtimequiz_highlight_correct');
                }
            }, this);
        },

        init: function () {
            Y.all('.realtimequiz_answerradio').on('click', this.highlight_correct, this);
            this.highlight_correct();
        }
    };

    realtimequizhelper.init();
};
