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

var timer = null
var f = 1; //seconds
function stop() {
    clearTimeout(timer)
}

function start() {
    timer = setTimeout("update()", f*1000);
    YAHOO.util.Event.addListener(document.body, 'unload', stop);
}

function update() {
    for(i=0; i<uidles.length; i++) {
        el = document.getElementById(uidles[i]);
        if (el != null) {
            parts = el.innerHTML.split(":");
            time = f + (parseInt(parts[0], 10)*60) + parseInt(parts[1], 10);
            min = Math.floor(time/60);
            sec = time % 60;
            el.innerHTML = ((min < 10) ? "0" : "") + min + ":" + ((sec < 10) ? "0" : "") + sec;
        }
    }
    timer = setTimeout("update()", f*1000);
}