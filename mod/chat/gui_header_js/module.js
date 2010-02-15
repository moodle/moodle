YUI.add('mod_chat_js', function(Y){

    M.mod_chat_js = {

        waitflag : false,
        timer : null,
        timeout : 1,
        users : [],

        init : function(Y, users) {
            if (users) {
                this.users = users;
                this.start();
                Y.one(document.body).on('unload', this.stop, this);
            }
            var inputform = Y.one('#inputForm');
            if (inputform) {
                inputform.on('submit', this.submit, this);
            }
        },

        start : function() {
            this.timer = setTimeout(function(self){
                self.update();
            }, this.timeout*1000, this);
        },

        stop : function() {
            clearTimeout(this.timer);
        },

        update : function() {
            for (var i in this.users) {
                var el  = Y.one('#uidle'+this.users[i]);
                if (el) {
                    var parts = el.get('innerHTML').split(':');
                    var time = this.timeout + (parseInt(parts[0], 10)*60) + parseInt(parts[1], 10);
                    var min = Math.floor(time/60);
                    var sec = time % 60;
                    el.set('innerHTML', ((min < 10) ? "0" : "") + min + ":" + ((sec < 10) ? "0" : "") + sec);
                }
            }
            this.start();
        },

        insert_redirect : function() {
            parent.jsupdate.location.href = parent.jsupdate.document.anchors[0].href;
        },

        enable_form : function(el) {
            this.waitflag = false;
            el.set('className','');
            el.focus();
        },

        submit : function(e) {
            e.halt();
            if(this.waitflag) {
                return false;
            }
            this.waitflag = true;
            var inputchatmessage = Y.one('#input_chat_message');
            Y.one('#insert_chat_message').set('value', inputchatmessage.get('value'));
            inputchatmessage.set('value', '');
            inputchatmessage.addClass('wait');
            Y.one('#sendForm').submit();
            this.enable_form(inputchatmessage);
            return false;
        }

    }

}, '2.0.0', {requires:['base','node']});