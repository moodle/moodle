
M.tool_capability = {
    select: null,
    input: null,
    button: null,

    init: function(Y, strsearch) {
        var context = M.tool_capability;

        // Find the form controls.
        context.select = document.getElementById('menucapability');
        context.button = document.getElementById('settingssubmit');

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.id = 'capabilitysearchui';

        // Find the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'capabilitysearch';
        context.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        context.select.parentNode.insertBefore(div, context.select);
        Y.on('keyup', context.typed, input);
        Y.on('change', context.validate, context.select);
        context.select.options[0].style.display = 'none';
        context.validate();
    },

    typed: function() {
        var context = M.tool_capability;

        var filtertext = context.input.value;
        var options = context.select.options;
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
            context.input.className = "error";
        } else {
            context.input.className = "";
        }
        context.validate();
    },

    validate: function() {
        var context = M.tool_capability;
        context.button.disabled = (context.select.value == '');
    }
}