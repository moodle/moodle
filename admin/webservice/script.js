capability_service = {
    select: null,
    input: null,
    button: null,

    cap_filter_init: function(strsearch) {
        // Find the form controls.
        capability_service.select = document.getElementById('menucapability');
        capability_service.button = document.getElementById('settingssubmit');

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.id = 'capabilitysearchui';

        // Find the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'capabilitysearch';
        capability_service.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        capability_service.select.parentNode.insertBefore(div, capability_service.select);
        YAHOO.util.Event.addListener(input, 'keyup', capability_service.cap_filter_change);
        YAHOO.util.Event.addListener(capability_service.select, 'change', capability_service.validate);
        capability_service.select.options[0].style.display = 'none';
        capability_service.validate();
    },

    cap_filter_change: function() {
        var filtertext = capability_service.input.value;
        var options = capability_service.select.options;
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
            capability_service.input.className = "error";
        } else {
            capability_service.input.className = "";
        }
        
        capability_service.validate();
    },

    validate: function() {
       capabilityname = document.getElementById('capabilityname');
       capabilityname.value = capability_service.select.value;


    }
}