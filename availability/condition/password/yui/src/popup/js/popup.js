/**
 * Availability password - YUI code for password popup
 *
 * @module     moodle-availability_password-popup
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var SELECTORS = {
    MAINREGION: '#region-main',
    PASSWORDLINK: '.availability_password-popup',
    PASSWORDFIELD: '#availability_password_input',
    ERRORMESSAGE: '#availability_password_error',
    CMCONTAINER: '.activity',
    CMNAME: '.instancename',
    CMICON: '.activityicon'
};

M.availability_password = M.availability_password || {}; // eslint-disable-line camelcase
M.availability_password.popup = {
    api: M.cfg.wwwroot + '/availability/condition/password/ajax.php',

    init: function() {
        var main;

        main = Y.one(SELECTORS.MAINREGION);
        if (!main) {
            return;
        }
        main.delegate('click', this.showPopup, SELECTORS.PASSWORDLINK, this);
        main.delegate('click', this.checkShowPopup, SELECTORS.CMCONTAINER + ' ' + SELECTORS.CMNAME, this);
        main.delegate('click', this.checkShowPopup, SELECTORS.CMCONTAINER + ' ' + SELECTORS.CMICON, this);
        this.initActivityLinks();
    },

    showPopup: function(e) {
        var content, cmname, panel, url, cmid, cmcontainer, cmnameholder, submit;

        e.preventDefault();
        e.stopPropagation();

        url = e.currentTarget.get('href');
        cmid = url.match(/id=(\d+)/);
        if (!cmid) {
            return;
        }
        cmid = parseInt(cmid[1], 10);
        if (!cmid) {
            return;
        }

        cmname = '';
        cmcontainer = e.currentTarget.ancestor(SELECTORS.CMCONTAINER);
        if (cmcontainer) {
            cmnameholder = cmcontainer.one(SELECTORS.CMNAME);
            if (cmnameholder) {
                cmname = cmnameholder.getHTML();
            }
        }

        content = '';
        content += '<div id="availability_password_intro">' +
            M.util.get_string('passwordintro', 'availability_password', cmname) + '</div>';
        content += '<div>';
        content += '<label class="form-control-label" for="availability_password_input">' +
            M.util.get_string('enterpassword', 'availability_password') + '</label>';
        content += '<input id="availability_password_input" class="form-control" type="password" />';
        content += '<div id="availability_password_error" class="invalid-feedback" style="display: none; "></div>';
        content += '</div>';

        panel = new M.core.dialogue({
            headerContent: M.util.get_string('passwordprotection', 'availability_password', cmname),
            bodyContent: content,
            width: '350px',
            modal: true,
            extraClasses: ['availability_password_dialogue']
        }).show();
        panel.after('visibleChange', function() {
            if (!panel.get('visible')) {
                panel.destroy(true);
            }
        });

        submit = function(e) {
            var data, password;
            e.preventDefault();

            password = Y.one(SELECTORS.PASSWORDFIELD).get('value').trim();
            if (password.length === 0) {
                return; // Do nothing if the password is blank.
            }

            // Send the request back to the server.
            data = {
                sesskey: M.cfg.sesskey,
                id: cmid,
                password: password
            };
            Y.io(this.api, {
                data: data,
                on: {
                    // Handle the response from the server.
                    success: function(ignore, resp) {
                        var details;
                        try {
                            details = JSON.parse(resp.responseText);
                        } catch (ex) {
                            window.alert('Communication error'); // eslint-disable-line no-alert
                            return;
                        }
                        if (details.error) {
                            window.alert(details.error); // eslint-disable-line no-alert
                            return;
                        }
                        if (details.success) {
                            if (details.redirect !== undefined) {
                                document.location = details.redirect;
                            } else {
                                document.location.reload();
                            }
                        } else {
                            Y.one(SELECTORS.ERRORMESSAGE).setHTML(M.util.get_string('wrongpassword', 'availability_password'));
                            Y.one(SELECTORS.ERRORMESSAGE).show();
                            Y.one(SELECTORS.PASSWORDFIELD).addClass('is-invalid');
                            Y.one(SELECTORS.PASSWORDFIELD).focus();
                        }
                    }
                }
            });
        };

        panel.addButton({
            label: M.util.get_string('submit', 'core'),
            section: Y.WidgetStdMod.FOOTER,
            action: submit,
            context: this,
            classNames: 'btn btn-primary'
        });
        panel.addButton({
            label: M.util.get_string('cancel', 'core'),
            section: Y.WidgetStdMod.FOOTER,
            action: function(e) {
                e.preventDefault();
                panel.hide();
            },
            classNames: 'btn btn-secondary'
        });

        Y.one(SELECTORS.PASSWORDFIELD).focus().on('key', submit, 'enter', this);
    },

    /**
     * Check to see if the activity is unavailable, but has an associated password popup.
     * If so, popup the relevant password request, when the activity name or the activity icon is clicked on.
     * @param {Object} e The event object.
     */
    checkShowPopup: function(e) {
        var activityName, pwLink;

        activityName = e.currentTarget;
        if (activityName.ancestor('a')) {
            return; // The activity name is already linked - go with the default action.
        }

        pwLink = activityName.ancestor(SELECTORS.CMCONTAINER).one(SELECTORS.PASSWORDLINK);
        if (pwLink) {
            // Trigger the relevant password popup.
            e.preventDefault();
            e.stopPropagation();
            this.showPopup({
                currentTarget: pwLink,
                preventDefault: function() { /* Do nothing */
                },
                stopPropagation: function() { /* Do nothing */
                }
            });
        }
    },

    initActivityLinks: function() {
        var nameoricon = SELECTORS.CMCONTAINER + ' ' + SELECTORS.CMNAME + ', ' + SELECTORS.CMCONTAINER + ' ' + SELECTORS.CMICON;
        Y.one(SELECTORS.MAINREGION).all(nameoricon).each(function(activityName) {
            var pwLink;
            if (activityName.ancestor('a')) {
                return; // Already linked, nothing to do.
            }
            pwLink = activityName.ancestor(SELECTORS.CMCONTAINER).one(SELECTORS.PASSWORDLINK);
            if (pwLink) {
                activityName.setStyle('cursor', 'pointer');
            }
        });
    }
};
