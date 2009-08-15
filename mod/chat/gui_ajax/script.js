// record msg IDs
var msgs = [];
var interval = null;
var scrollable = true;

(function() {
var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event;
// window.onload
Event.onDOMReady(function() {
    // build layout
    var layout = new YAHOO.widget.Layout({
        units: [
        { position: 'top', height: 60, body: 'chat_header', header: chat_cfg.header_title, gutter: '5px', collapse: true, resize: false },
        { position: 'right', header: chat_lang.userlist, width: 180, resize: true, gutter: '5px', footer: null, collapse: true, scroll: true, body: 'chat_user_list', animate: false },
        { position: 'bottom', header: chat_lang.inputarea, height: 60, resize: true, body: 'chat_input', gutter: '5px', collapse: false, resize: false },
        //{ position: 'left', header: 'Options', width: 200, resize: true, body: 'chat_options', gutter: '5px', collapse: true, close: true, collapseSize: 50, scroll: true, animate: false },
        { position: 'center', body: 'chat_panel', gutter: '5px', scroll: true }
        ]
    });
    layout.on('render', function() {
        layout.getUnitByPosition('left').on('close', function() {
            closeLeft();
        });
    });
    layout.render();
    Event.on('btn_send', 'click', function(ev) {
        Event.stopEvent(ev);
        send_message();
    });
    Event.on('chat_panel', 'mouseover', function(ev) {
        Event.stopEvent(ev);
        scrollable = false;
    });
    Event.on('chat_panel', 'mouseout', function(ev) {
        Event.stopEvent(ev);
        scrollable = true;
    });
    var key_send = new YAHOO.util.KeyListener(document, { keys:13 }, {fn:send_message, correctScope:true });
    key_send.enable();
    document.getElementById('input_msgbox').focus();
    document.title = chat_cfg.chatroom_name;


    this.cb = {
        success: function(o){
            if(o.responseText){
                var data = YAHOO.lang.JSON.parse(o.responseText);
            } else {
                return;
            }
            if (data.users) {
                update_users(data.users);
            }
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('POST', "update.php?chat_sid="+chat_cfg.sid+"&chat_init=1", this.cb, null);
    interval = setInterval(function(){
        update_messages();
    }, chat_cfg.timer);
});
})();

function in_array(f, t){
    var a = false;
    for( var i = 0; i<t.length; i++){
        if(f==t[i]){
            a=true;
            break;
        }
    }
    return a;
}

function talkto(name) {
    var msg = document.getElementById('input_msgbox');
    msg.value = "To "+name+": ";
    msg.focus();
}

function send_message() {
    var msg = document.getElementById('input_msgbox').value;
    var el_send = document.getElementById('btn_send');
    if (!msg) {
        alert('Empty message not allowed');
        return;
    }
    var url = 'post.php?chat_sid='+chat_cfg.sid;
    el_send.value = chat_lang.sending;
    var trans = YAHOO.util.Connect.asyncRequest('POST', url, send_cb, "chat_message="+msg);
}
function send_beep(id){
    var url = 'post.php?chat_sid='+chat_cfg.sid;
    var trans = YAHOO.util.Connect.asyncRequest('POST', url, send_cb, "beep="+id);
}

var send_cb = {
    success: function(o) {
        if(o.responseText == 200){
            document.getElementById('btn_send').value = chat_lang.send;
            document.getElementById('input_msgbox').value = '';
        }
        clearInterval(interval)
        update_messages();
        interval = setInterval(function(){
            update_messages();
        }, chat_cfg.timer);
        document.getElementById('input_msgbox').focus();
    }
}

function update_users(users) {
    if(!users){
        return;
    }
    var list = document.getElementById('listing');
    list.innerHTML = '';
    var html = '';
    for(var i in users){
        var el = document.createElement('li');
        html += '<table><tr><td>' + users[i].picture + '</td><td>'
        html += '<a target="_blank" href="'+users[i].url+'">'+ users[i].name+'<br/>';
        html += '<a href="###" onclick="talkto(\''+users[i].name+'\')">Talk</a> ';
        html += '<a href="###" onclick="send_beep('+users[i].id+')">Beep</a>';
        html += '</td></tr></table>';
        el.innerHTML = html;
        list.appendChild(el);
    }
}
function update_messages() {
    if(!chat_cfg.req_count){
        chat_cfg.req_count = 1;
    } else {
        chat_cfg.req_count++;
    }
    console.info('Update count: '+chat_cfg.req_count);
    var url = "update.php?chat_sid="+chat_cfg.sid+"&chat_lasttime="+chat_cfg.chat_lasttime;
    if(chat_cfg.chat_lastrow != null){
        url += "&chat_lastrow="+chat_cfg.chat_lastrow;
    }
    var trans = YAHOO.util.Connect.asyncRequest('POST', url, update_cb, null);
}
function append_msg(msg) {
    var list = document.getElementById('messageslist');
    var item = document.createElement('li');
    console.info('New message:'+msg.msg);
    item.innerHTML = msg.msg;
    if(msg.type && msg.type == 'beep'){
        document.getElementById('notify').innerHTML = '<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />';
    }
    list.appendChild(item);
}
var update_cb = {
success: function(o){
    try {
        if(o.responseText){
            var data = YAHOO.lang.JSON.parse(o.responseText);
        } else {
            return;
        }
    } catch(e) {
        alert('json invalid');
        alert(o.responseText);
        return;
    }
    if(data.error) {
        if(data.error.level == 'ERROR'){
            clearInterval(interval);
            window.location = chat_cfg.home;
        }
    }
    if(!data)
         return false;
    chat_cfg.chat_lasttime = data['lasttime'];
    chat_cfg.chat_lastrow  = data['lastrow'];
    // update messages
    for (key in data['msgs']){
        if(!in_array(key, msgs)){
            msgs.push(key);
            append_msg(data['msgs'][key]);
        }
    }
    // update users
    update_users(data['users']);
    // scroll to the bottom of the message list
    if(scrollable){
        document.getElementById('chat_panel').parentNode.scrollTop+=500;
    }
}
}

// debug code
if(!console){
    var console = {
        info: function(){
        },
        log: function(){
        }
    }
}
