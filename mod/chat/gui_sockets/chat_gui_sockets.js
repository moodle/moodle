scroll_active = true;
function empty_field_and_submit() {
    var cf   = document.getElementById('sendform');
    var inpf = document.getElementById('inputform');
    cf.chat_msgidnr.value = parseInt(cf.chat_msgidnr.value) + 1;
    cf.chat_message.value = inpf.chat_message.value;
    inpf.chat_message.value = '';
    cf.submit();
    inpf.chat_message.focus();
    return false;
}
function setfocus() {
    document.getElementsByName("chat_message")[0].focus();
}
