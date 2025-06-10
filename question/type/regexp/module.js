M.qtype_regexp = M.qtype_regexp || {};

M.qtype_regexp.showhidealternate = function(Y, buttonel, showhideel) {
    Y.one(buttonel).on('click', function(e) {
        if (Y.one(showhideel).getStyle('display') == 'none') {
            Y.one(showhideel).setStyle('display', 'block');
            Y.one(buttonel).set('value', M.util.get_string('hidealternate', 'qtype_regexp'));
        } else {
            Y.one(showhideel).setStyle('display', 'none');
            Y.one(buttonel).set('value', M.util.get_string('showalternate', 'qtype_regexp'));
        }
        e.halt();
    });
};
