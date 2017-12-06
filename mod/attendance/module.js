M.mod_attendance = {}; // eslint-disable-line camelcase

M.mod_attendance.init_manage = function(Y) { // eslint-disable-line camelcase

    Y.on('click', function(e) {
        if (e.target.get('checked')) {
            Y.all('input.attendancesesscheckbox').each(function() {
                this.set('checked', 'checked');
            });
        } else {
            Y.all('input.attendancesesscheckbox').each(function() {
                this.set('checked', '');
            });
        }
    }, '#cb_selector');
};

M.mod_attendance.set_preferences_action = function(action) { // eslint-disable-line camelcase
    var item = document.getElementById('preferencesaction');
    if (item) {
        item.setAttribute('value', action);
    } else {
        item = document.getElementById('preferencesform');
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", "action");
        input.setAttribute("value", action);
        item.appendChild(input);
    }
};