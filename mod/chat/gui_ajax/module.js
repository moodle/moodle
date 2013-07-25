
/*
 * NOTE: the /mod/chat/gui_header_js/ is not a real plugin,
 * ideally this code should be in /mod/chat/module.js
 */

/**
 * @namespace M.mod_chat_ajax
 */
M.mod_chat_ajax = M.mod_chat_ajax || {};

/**
 * Init ajax based Chat UI.
 * @namespace M.mod_chat_ajax
 * @function
 * @param {YUI} Y
 * @param {Object} cfg configuration data
 */
M.mod_chat_ajax.init = function(Y, cfg) {

    var gui_ajax = {

        // Properties
        api : M.cfg.wwwroot+'/mod/chat/chat_ajax.php?sesskey='+M.cfg.sesskey,  // The path to the ajax callback script
        cfg : {},                                       // A configuration variable
        interval : null,                                // The interval object for refreshes
        layout : null,                                  // A reference to the layout used in this module
        messages : [],                                  // An array of messages
        scrollable : true,                              // True is scrolling should occur
        thememenu : null,                               // A reference to the menu for changing themes

        // Elements
        messageinput : null,
        sendbutton : null,
        messagebox : null,

        init : function(cfg) {
            this.cfg = cfg;
            this.cfg.req_count = this.cfg.req_count || 0;
            this.layout = new Y.YUI2.widget.Layout({
                units : [
                     {position: 'right', width: 180, resize: true, gutter: '5px', scroll: true, body: 'chat-userlist', animate: false},
                     {position: 'bottom', height: 42, resize: false, body: 'chat-input-area', gutter: '5px', collapse: false, resize: false},
                     {position: 'center', body: 'chat-messages', gutter: '5px', scroll: true}
                ]
            });

            this.layout.on('render', function() {
                var unit = this.getUnitByPosition('right');
                if (unit) {
                    unit.on('close', function() {
                        closeLeft();
                    });
                }
            }, this.layout);
            this.layout.render();

            // Gather the general elements
            this.messageinput = Y.one('#input-message');
            this.sendbutton = Y.one('#button-send');
            this.messagebox = Y.one('#chat-messages');

            // Set aria attributes to messagebox and chat-userlist
            this.messagebox.set('role', 'log');
            this.messagebox.set('aria-live', 'polite');
            var userlist = Y.one('#chat-userlist');
            userlist.set('aria-live', 'polite');
            userlist.set('aria-relevant', 'all');

            // Attach the default events for this module
            this.sendbutton.on('click', this.send, this);
            this.messagebox.on('mouseenter', function() {
                this.scrollable = false;
            }, this);
            this.messagebox.on('mouseleave', function() {
                this.scrollable = true;
            }, this);

            // Send the message when the enter key is pressed
            Y.on('key', this.send, this.messageinput,  'press:13', this);

            document.title = this.cfg.chatroom_name;

            // Prepare and execute the first AJAX request of information
            Y.io(this.api,{
                method : 'POST',
                data :  build_querystring({
                    action : 'init',
                    chat_init : 1,
                    chat_sid : this.cfg.sid,
                    theme : this.theme
                }),
                on : {
                    success : function(tid, outcome) {
                        this.messageinput.removeAttribute('disabled');
                        this.messageinput.set('value', '');
                        this.messageinput.focus();
                        try {
                            var data = Y.JSON.parse(outcome.responseText);
                        } catch (ex) {
                            return;
                        }
                        this.update_users(data.users);
                    }
                },
                context : this
            });

            var scope = this;
            this.interval = setInterval(function() {
                scope.update_messages();
            }, this.cfg.timer, this);

            // Create and initalise theme changing menu
            /*
            this.thememenu = new Y.Overlay({
                bodyContent : '<div class="menuitem"><a href="'+this.cfg.chaturl+'&theme=bubble">Bubble</a></div><div class="menuitem"><a href="'+this.cfg.chaturl+'&theme=compact">Compact</a></div>',
                visible : false,
                zIndex : 2,
                align : {
                    node : '#choosetheme',
                    points : [Y.WidgetPositionExt.BL, Y.WidgetPositionExt.BR]
                }
            });
            this.thememenu.render(document.body);
            Y.one('#choosetheme').on('click', function(e){
                this.show();
                this.get('boundingBox').setStyle('visibility', 'visible');
            }, this.thememenu);

            return;
            */
            this.thememenu = new Y.YUI2.widget.Menu('basicmenu', {xy:[0,0]});
            this.thememenu.addItems([
                {text: "Bubble", url: this.cfg.chaturl+'&theme=bubble'},
                {text: "Compact", url: this.cfg.chaturl+'&theme=compact'}
            ]);
            this.thememenu.render(document.body);
            Y.one('#choosetheme').on('click', function(e){
                this.moveTo((e.pageX-20), (e.pageY-20));
                this.show();
            }, this.thememenu);
        },

        append_message : function(key, message, row) {
            var item = Y.Node.create('<li id="mdl-chat-entry-'+key+'">'+message.message+'</li>');
            item.addClass((message.mymessage)?'mdl-chat-my-entry':'mdl-chat-entry');
            Y.one('#messages-list').append(item);
            if (message.type && message.type == 'beep') {
                Y.one('#chat-notify').setContent('<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />');
            }
        },

        send : function(e, beep) {
            this.sendbutton.set('value', M.str.chat.sending);

            var data = {
                chat_message : (!beep)?this.messageinput.get('value'):'',
                chat_sid : this.cfg.sid,
                theme : this.cfg.theme
            };
            if (beep) {
                data.beep = beep
            }
            data.action = 'chat';

            Y.io(this.api, {
                method : 'POST',
                data : build_querystring(data),
                on : {
                    success : this.send_callback
                },
                context : this
            });
        },

        send_callback : function(tid, outcome, args) {
            try {
                var data = Y.JSON.parse(outcome.responseText);
            } catch (ex) {
                return;
            }
            this.sendbutton.set('value', M.str.chat.send);
            this.messageinput.set('value', '');
            clearInterval(this.interval);
            this.update_messages();
            var scope = this;
            this.interval = setInterval(function() {
                scope.update_messages();
            }, this.cfg.timer, this);
        },

        talkto: function (e, name) {
            this.messageinput.set('value', "To "+name+": ");
            this.messageinput.focus();
        },

        update_messages : function() {
            this.cfg.req_count++;
            Y.io(this.api, {
                method : 'POST',
                data : build_querystring({
                    action: 'update',
                    chat_lastrow : this.cfg.chat_lastrow || false,
                    chat_lasttime : this.cfg.chat_lasttime,
                    chat_sid : this.cfg.sid,
                    theme : this.cfg.theme
                }),
                on : {
                    success : this.update_messages_callback
                },
                context : this
            });
        },

        update_messages_callback : function(tid, outcome) {
            try {
                var data = Y.JSON.parse(outcome.responseText);
            } catch (ex) {
                return;
            }
            if (data.error) {
                clearInterval(this.interval);
                alert(data.error);
                window.location = this.cfg.home;
            }
            this.cfg.chat_lasttime = data.lasttime;
            this.cfg.chat_lastrow  = data.lastrow;
            // Update messages
            for (var key in data.msgs){
                if (!M.util.in_array(key, this.messages)) {
                    this.messages.push(key);
                    this.append_message(key, data.msgs[key], data.lastrow);
                }
            }
            // Update users
            this.update_users(data.users);
            // Scroll to the bottom of the message list
            if (this.scrollable) {
                Y.Node.getDOMNode(this.messagebox).parentNode.scrollTop+=500;
            }
            this.messageinput.focus();
        },

        update_users : function(users) {
            if (!users) {
                return;
            }
            var list = Y.one('#users-list');
            list.get('children').remove();
            for (var i in users) {
                var li = Y.Node.create('<li><table><tr><td>'+users[i].picture+'</td><td></td></tr></table></li>');
                if (users[i].id == this.cfg.userid) {
                    li.all('td').item(1).append(Y.Node.create('<strong><a target="_blank" href="'+users[i].url+'">'+ users[i].name+'</a></strong>'));
                } else {
                    li.all('td').item(1).append(Y.Node.create('<div><a target="_blank" href="'+users[i].url+'">'+users[i].name+'</a></div>'));
                    var talk = Y.Node.create('<a href="###">'+M.str.chat.talk+'</a>');
                    talk.on('click', this.talkto, this, users[i].name);
                    var beep = Y.Node.create('<a href="###">'+M.str.chat.beep+'</a>');
                    beep.on('click', this.send, this, users[i].id);
                    li.all('td').item(1).append(Y.Node.create('<div></div>').append(talk).append('&nbsp;').append(beep));
                }
                list.append(li);
            }
        }

    };

    gui_ajax.init(cfg);
};
