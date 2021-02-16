// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/*
 * @package    core
 * @class      permissionmanager
 * @copyright  2015 Martin Mastny <mastnym@vscht.cz>
 * @since      3.0
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module admin/permissionmanager
  */
define(['jquery', 'core/config', 'core/notification', 'core/templates', 'core/yui'],
    function($, config, notification, templates, Y) {

     /**
      * Used CSS selectors
      * @access private
      */
    var SELECTORS = {
        ADDROLE: 'a.allowlink, a.prohibitlink',
        REMOVEROLE: 'a.preventlink, a.unprohibitlink',
        UNPROHIBIT: 'a.unprohibitlink'
        };
    var rolesloadedevent = $.Event('rolesloaded');
    var contextid;
    var contextname;
    var adminurl;
    var overideableroles;
    var panel = null;

    /**
     * Load all possible roles, which could be assigned from server
     *
     * @access private
     * @method loadOverideableRoles
     */
    var loadOverideableRoles = function() {
        var params = {
            contextid: contextid,
            getroles: 1,
            sesskey: config.sesskey
        };

        // Need to tell jQuery to expect JSON as the content type may not be correct (MDL-55041).
        $.post(adminurl + 'roles/ajax.php', params, null, 'json')
            .done(function(data) {
              try {
                  overideableroles = data;
                  loadOverideableRoles = function() {
                      $('body').trigger(rolesloadedevent);
                  };
                  loadOverideableRoles();
              } catch (err) {
                  notification.exception(err);
              }
            })
            .fail(function(jqXHR, status, error) {
                notification.exception(error);
            });
    };

    /**
     * Perform the UI changes after server change
     *
     * @access private
     * @method changePermissions
     * @param {JQuery} row
     * @param {int} roleid
     * @param {string} action
     */
    var changePermissions = function(row, roleid, action) {
        var params = {
            contextid: contextid,
            roleid: roleid,
            sesskey: M.cfg.sesskey,
            action: action,
            capability: row.data('name')
        };
        $.post(adminurl + 'roles/ajax.php', params, null, 'json')
        .done(function(data) {
            var action = data;
            try {
                var templatedata = {rolename: overideableroles[roleid],
                                    roleid: roleid,
                                    adminurl: adminurl,
                                    imageurl: M.util.image_url('t/delete', 'moodle')
                                    };
                switch (action) {
                    case 'allow':
                        templatedata.spanclass = 'allowed';
                        templatedata.linkclass = 'preventlink';
                        templatedata.action = 'prevent';
                        templatedata.icon = 't/delete';
                        templatedata.iconalt = M.util.get_string('deletexrole', 'core_role', overideableroles[roleid]);
                        break;
                    case 'prohibit':
                        templatedata.spanclass = 'forbidden';
                        templatedata.linkclass = 'unprohibitlink';
                        templatedata.action = 'unprohibit';
                        templatedata.icon = 't/delete';
                        templatedata.iconalt = M.util.get_string('deletexrole', 'core_role', overideableroles[roleid]);
                        break;
                    case 'prevent':
                        row.find('a[data-role-id="' + roleid + '"]').first().closest('.allowed').remove();
                        return;
                    case 'unprohibit':
                        row.find('a[data-role-id="' + roleid + '"]').first().closest('.forbidden').remove();
                        return;
                    default:
                        return;
                }
                templates.render('core/permissionmanager_role', templatedata)
                .done(function(content) {
                    if (action == 'allow') {
                        $(content).insertBefore(row.find('.allowmore').first());
                    } else if (action == 'prohibit') {
                        $(content).insertBefore(row.find('.prohibitmore').first());
                        // Remove allowed link
                        var allowedLink = row.find('.allowedroles').first().find('a[data-role-id="' + roleid + '"]');
                        if (allowedLink) {
                            allowedLink.first().closest('.allowed').remove();
                        }
                    }
                    panel.hide();
                })
                .fail(notification.exception);
            } catch (err) {
                notification.exception(err);
            }
        })
        .fail(function(jqXHR, status, error) {
            notification.exception(error);
        });
    };

    /**
     * Prompts user for selecting a role which is permitted
     *
     * @access private
     * @method handleAddRole
     * @param {event} e
     */
    var handleAddRole = function(e) {
        e.preventDefault();

        var link = $(e.currentTarget);

        // TODO: MDL-57778 Convert to core/modal.
        $('body').one('rolesloaded', function() {
            Y.use('moodle-core-notification-dialogue', function() {
                var action = link.data('action');
                var row = link.closest('tr.rolecap');
                var confirmationDetails = {
                    cap: row.data('humanname'),
                    context: contextname
                };
                var message = M.util.get_string('role' + action + 'info', 'core_role', confirmationDetails);
                if (panel === null) {
                    panel = new M.core.dialogue({
                        draggable: true,
                        modal: true,
                        closeButton: true,
                        width: '450px'
                    });
                }
                panel.set('headerContent', M.util.get_string('role' + action + 'header', 'core_role'));

                var i, existingrolelinks;

                var roles = [];
                switch (action) {
                    case 'allow':
                        existingrolelinks = row.find(SELECTORS.REMOVEROLE);
                        break;
                    case 'prohibit':
                        existingrolelinks = row.find(SELECTORS.UNPROHIBIT);
                        break;
                }
                for (i in overideableroles) {
                    var disabled = '';
                    var disable = existingrolelinks.filter("[data-role-id='" + i + "']").length;
                    if (disable) {
                        disabled = 'disabled';
                    }
                    var roledetails = {roleid: i, rolename: overideableroles[i], disabled: disabled};
                    roles.push(roledetails);
                }

                templates.render('core/permissionmanager_panelcontent', {message: message, roles: roles})
                .done(function(content) {
                    panel.set('bodyContent', content);
                    panel.show();
                    $('div.role_buttons').on('click', 'input', function(e) {
                        var roleid = $(e.currentTarget).data('role-id');
                        changePermissions(row, roleid, action);
                    });
                })
                .fail(notification.exception);

            });
        });
        loadOverideableRoles();
    };

    /**
     * Prompts user when removing permission
     *
     * @access private
     * @method handleRemoveRole
     * @param {event} e
     */
    var handleRemoveRole = function(e) {
        e.preventDefault();
        var link = $(e.currentTarget);
        $('body').one('rolesloaded', function() {
            var action = link.data('action');
            var roleid = link.data('role-id');
            var row = link.closest('tr.rolecap');
            var questionDetails = {
                role: overideableroles[roleid],
                cap: row.data('humanname'),
                context: contextname
            };

            notification.confirm(M.util.get_string('confirmunassigntitle', 'core_role'),
                M.util.get_string('confirmrole' + action, 'core_role', questionDetails),
                M.util.get_string('confirmunassignyes', 'core_role'),
                M.util.get_string('confirmunassignno', 'core_role'),
                function() {
                   changePermissions(row, roleid, action);
                }
            );
         });
        loadOverideableRoles();
    };

    return /** @alias module:core/permissionmanager */ {
        /**
         * Initialize permissionmanager
         * @access public
         * @param {Object} args
         */
        initialize: function(args) {
            contextid = args.contextid;
            contextname = args.contextname;
            adminurl = args.adminurl;
            var body = $('body');
            body.on('click', SELECTORS.ADDROLE, handleAddRole);
            body.on('click', SELECTORS.REMOVEROLE, handleRemoveRole);
        }
    };
});
