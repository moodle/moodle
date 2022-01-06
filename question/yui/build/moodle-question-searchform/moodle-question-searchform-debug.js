YUI.add('moodle-question-searchform', function (Y, NAME) {


    var SELECTORS = {
            OPTIONS: '.searchoptions'
        },
        NS;

    M.question = M.question || {};
    NS = M.question.searchform = {};

    NS.init = function() {
        Y.delegate('change', this.option_changed, Y.config.doc, SELECTORS.OPTIONS, this);
    };

    NS.option_changed = function(e) {
            e.target.getDOMNode().form.submit();
    };



}, '@VERSION@', {"requires": ["base", "node"]});
