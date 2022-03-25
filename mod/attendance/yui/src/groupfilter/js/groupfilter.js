/* global M */
// eslint-disable-next-line camelcase
M.mod_attendance = M.mod_attendance || {};
M.mod_attendance.groupfilter = {
    groupmappings: null,

    init: function(opts) {
        "use strict";

        this.groupmappings = opts.groupmappings;
        Y.one('#id_group').after('change', this.update_user_list, this);
    },

    /**
     * Update the user list with those found in the selected group.
     */
    update_user_list: function() { // eslint-disable-line camelcase
        "use strict";
        var groupid, userlist, users, userid, opt;

        // Get the list of users in the current group.
        groupid = Y.one('#id_group').get('value');
        users = this.groupmappings[groupid];

        // Remove the options from the users select.
        userlist = Y.one('#id_users');
        userlist.get('options').remove();

        // Repopulate the users select with those users in the selected group (if any).
        if (users !== undefined) {
            for (userid in users) {
                if (users.hasOwnProperty(userid)) {
                    opt = Y.Node.create('<option></option>');
                    opt.set('value', userid);
                    opt.set('text', users[userid]);
                    userlist.appendChild(opt);
                }
            }
        }
    }
};
