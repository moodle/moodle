var waitFlag = false;
function empty_field_and_submit() {
    if(waitFlag) {
        return false;
    }
    waitFlag = true;
    var input_chat_message = document.getElementById('input_chat_message');
    document.getElementById('sendForm').chat_message.value = input_chat_message.value;
    input_chat_message.value = '';
    input_chat_message.className = 'wait';
    document.getElementById('sendForm').submit();
    enableForm();
    return false;
}

function enableForm() {
    var input_chat_message = document.getElementById('input_chat_message');
    waitFlag = false;
    input_chat_message.className = '';
    input_chat_message.focus();
}