function select_all() {
    var inputs = document.getElementsByTagName('INPUT');
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox') {
            inputs[i].checked = 'checked';
        }
    }
}

function deselect_all() {
    var inputs = document.getElementsByTagName('INPUT');
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox') {
            inputs[i].checked = '';
        }
    }
}

function confirm_if(expr, message) {
    if(!expr) {
        return true;
    }
    return confirm(message);
}
