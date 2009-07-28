function checksubmit(form) {
    var destination = form.formaction.options[form.formaction.selectedIndex].value;
    if (destination == "" || !checkchecked(form)) {
        form.formaction.selectedIndex = 0;
        return false;
    } else {
        return true;
    }
}

function checkchecked(form) {
    var inputs = document.getElementsByTagName('INPUT');
    var checked = false;
    inputs = filterByParent(inputs, function() {return form;});
    for(var i = 0; i < inputs.length; ++i) {
        if (inputs[i].type == 'checkbox' && inputs[i].checked) {
            checked = true;
        }
    }
    return checked;
}

function conditionalsubmit(event, args) {
    var form = document.getElementById(args.formid);
    if (checksubmit(form)) {
        form.submit();
    }
}
