capability_report = {
    select: null,
    input: null,
    button: null,

    cap_filter_init: function(strsearch) {
        // Find the form controls.
        capability_report.select = document.getElementById('menucapability');
        capability_report.button = document.getElementById('settingssubmit');

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.id = 'capabilitysearchui';

        // Find the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'capabilitysearch';
        capability_report.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        capability_report.select.parentNode.insertBefore(div, capability_report.select);
        YAHOO.util.Event.addListener(input, 'keyup', capability_report.cap_filter_change);
        YAHOO.util.Event.addListener(capability_report.select, 'change', capability_report.validate);
        capability_report.select.options[0].style.display = 'none';
        capability_report.validate();
    },

    cap_filter_change: function() {
        var filtertext = capability_report.input.value;
        var options = capability_report.select.options;
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
            capability_report.input.className = "error";
        } else {
            capability_report.input.className = "";
        }
        capability_report.validate();
    },

    validate: function() {
        capability_report.button.disabled = (capability_report.select.value == '');
    }
}