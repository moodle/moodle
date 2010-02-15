/**
 * Module for the general JavaScript chat module
 */
YUI.add('mod_chat_js', function(Y){
    /**
     * @namespace M.mod_chat
     */
    M.mod_chat = M.mod_chat || {};
    /**
     * @namespace M.mod_chat.js
     */
    M.mod_chat.js = {

        waitflag : false,       // True when a submission is in progress
        timer : null,           // Stores the timer object
        timeout : 1,            // The seconds between updates
        users : [],             // An array of users

        /**
         * Function that kicks everything off depending on what is available
         * within the page, this means we can call it from within each frame and it
         * will set up correctly
         *
         * @function
         * @this {M.mod_chat.js}
         * @param {YUI} Y
         * @param {Array|null} users
         */
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
        /**
         * Starts the update timeout
         *
         * @function
         * @this {M.mod_chat.js}
         */
        start : function() {
            this.timer = setTimeout(function(self){
                self.update();
            }, this.timeout*1000, this);
        },
        /**
         * Stops the update timeout
         * @function
         * @this {M.mod_chat.js}
         */
        stop : function() {
            clearTimeout(this.timer);
        },
        /**
         * Updates the user information
         * @function
         * @this {M.mod_chat.js}
         */
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
        /**
         * Redirects the frames parent
         */
        insert_redirect : function() {
            parent.jsupdate.location.href = parent.jsupdate.document.anchors[0].href;
        },
        /**
         * Enables the input form
         * @this {M.mod_chat.js}
         */
        enable_form : function() {
            var el = Y.one('#input_chat_message');
            this.waitflag = false;
            el.set('className','');
            el.focus();
        },
        /**
         * Submits the entered message
         * @param {Event} e
         */
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
            this.enable_form();
            return false;
        }

    }
}, '2.0.0', {requires:['base','node']});