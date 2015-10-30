
/*
 * NOTE: the /mod/chat/gui_header_js/ is not a real plugin,
 * ideally this code should be in /mod/chat/module.js
 */

/**
 * @namespace M.mod_chat_header
 */
M.mod_chat_header = M.mod_chat_ajax || {};

/**
 * Init header based Chat UI - frame input
 *
 * @namespace M.mod_chat_header
 * @function
 * @param {YUI} Y
 * @param {Boolean} forcerefreshasap refresh users frame asap
 */
M.mod_chat_header.init_insert = function(Y, forcerefreshasap) {
    if (forcerefreshasap) {
        parent.jsupdate.location.href = parent.jsupdate.document.anchors[0].href;
    }
    parent.input.enableForm();
};

/**
 * Init header based Chat UI - frame input
 *
 * @namespace M.mod_chat_header
 * @function
 * @param {YUI} Y
 */
M.mod_chat_header.init_input = function(Y) {

    var inputframe = {

        waitflag : false,       // True when a submission is in progress

        /**
         * Initialises the input frame
         *
         * @function
         */
        init : function() {
            Y.one('#inputForm').on('submit', this.submit, this);
        },
        /**
         * Enables the input form
         * @this {M.mod_chat.js}
         */
        enable_form : function() {
            var el = Y.one('#input_chat_message');
            this.waitflag = false;
            el.set('className', '');
            el.focus();
        },
        /**
         * Submits the entered message
         * @param {Event} e
         */
        submit : function(e) {
            e.halt();
            if (this.waitflag) {
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

    };

    inputframe.init();
};

/**
 * Init header based Chat UI - frame users
 *
 * @namespace M.mod_chat_header
 * @function
 * @param {YUI} Y
 * @param {Array} users
 */
M.mod_chat_header.init_users = function(Y, users) {

    var usersframe = {

        timer : null,           // Stores the timer object
        timeout : 1,            // The seconds between updates
        users : [],             // An array of users

        /**
         * Initialises the frame with list of users
         *
         * @function
         * @this
         * @param {Array|null} users
         */
        init : function(users) {
            this.users = users;
            this.start();
            Y.one(document.body).on('unload', this.stop, this);
        },
        /**
         * Starts the update timeout
         *
         * @function
         * @this
         */
        start : function() {
            this.timer = setTimeout(function(self) {
                self.update();
            }, this.timeout*1000, this);
        },
        /**
         * Stops the update timeout
         * @function
         * @this
         */
        stop : function() {
            clearTimeout(this.timer);
        },
        /**
         * Updates the user information
         *
         * @function
         * @this
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
        }
    };

    usersframe.init(users);
};
