
M.report_capability = {
    select: null,
    input: null,
    button: null,

    init: function(Y, strsearch) {
        // Find the form controls.
        M.report_capability.select = document.getElementById('menucapability');
        M.report_capability.button = document.getElementById('settingssubmit');

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.id = 'capabilitysearchui';

        // Find the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'capabilitysearch';
        M.report_capability.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        M.report_capability.select.parentNode.insertBefore(div, M.report_capability.select);
        Y.on('keyup', M.report_capability.typed, input);
        Y.on('change', M.report_capability.validate, M.report_capability.select);
        M.report_capability.select.options[0].style.display = 'none';
        M.report_capability.validate();
    },

    typed: function() {
        var filtertext = M.report_capability.input.value;
        var options = M.report_capability.select.options;
        var onlycapability = -1;
        for (var i = 1; i < options.length; i++) {
            if (options[i].text.indexOf(filtertext) >= 0) {
                options[i].disabled = false;
                options[i].style.display = 'block';
                if (onlycapability == -1) {
                    onlycapability = i;
                } else {
                    onlycapability = -2;
                }
            } else {
                options[i].disabled = true;
                options[i].selected = false;
                options[i].style.display = 'none';
            }
        }
        if (onlycapability >= 0) {
            options[onlycapability].selected = true;
        }
        if (onlycapability == -1) {
            M.report_capability.input.className = "error";
        } else {
            M.report_capability.input.className = "";
        }
        M.report_capability.validate();
    },

    validate: function() {
        M.report_capability.button.disabled = (M.report_capability.select.value == '');
    }
}