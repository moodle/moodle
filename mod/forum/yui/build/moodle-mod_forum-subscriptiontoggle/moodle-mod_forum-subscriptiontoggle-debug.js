YUI.add('moodle-mod_forum-subscriptiontoggle', function (Y, NAME) {

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

/**
 * A utility to check whether the connection to the Moodle server is still
 * active.
 *
 * @module     moodle-core-subscriptiontoggle
 * @package    mod_forum
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-mod_forum-subscriptiontoggle
 */

/**
 * @namespace M.mod_forum
 * @class subscriptiontoggle
 */

function SubscriptionToggle() {
    SubscriptionToggle.superclass.constructor.apply(this, arguments);
}

var LOGNAME = 'moodle-mod_forum-subscriptiontoggle';

Y.extend(SubscriptionToggle, Y.Base, {
    initializer: function() {
        Y.delegate('click', this._toggleSubscription, Y.config.doc.body, '.discussionsubscription .discussiontoggle', this);
    },
    _toggleSubscription: function(e) {
        var clickedLink = e.currentTarget;

        Y.io(this.get('uri'), {
            data: {
                sesskey: M.cfg.sesskey,
                forumid: clickedLink.getData('forumid'),
                discussionid: clickedLink.getData('discussionid'),
                includetext: clickedLink.getData('includetext')
            },
            context: this,
            'arguments': {
                clickedLink: clickedLink
            },
            on: {
                complete: this._handleCompletion
            }
        });

        // Prevent the standard browser behaviour now.
        e.preventDefault();
    },

    _handleCompletion: function(tid, response, args) {
        var responseObject;
        // Attempt to parse the response into an object.
        try {
            responseObject = Y.JSON.parse(response.response);
            if (responseObject.error) {
                Y.use('moodle-core-notification-ajaxexception', function() {
                    return new M.core.ajaxException(responseObject);
                });
                return this;
            }
        } catch (error) {
            Y.use('moodle-core-notification-exception', function() {
                return new M.core.exception(error);
            });
            return this;
        }

        if (!responseObject.icon) {
            Y.log('No icon received. Skipping the current value replacement', 'warn', LOGNAME);
            return;
        }

        var container = args.clickedLink.ancestor('.discussionsubscription');
        if (container) {
            // We should just receive the new value for the table.
            // Note: We do not need to escape the HTML here as it should be provided sanitised from the JS response already.
            container.set('innerHTML', responseObject.icon);
        }
    }
}, {
    NAME: 'subscriptionToggle',
    ATTRS: {
        /**
         * The AJAX endpoint which controls the subscription toggle.
         *
         * @attribute uri
         * @type String
         * @default M.cfg.wwwroot + '/mod/forum/subscribe_ajax.php'
         */
        uri: {
            value: M.cfg.wwwroot + '/mod/forum/subscribe_ajax.php'
        }
    }
});

var NS = Y.namespace('M.mod_forum.subscriptiontoggle');
NS.init = function(config) {
    return new SubscriptionToggle(config);
};


}, '@VERSION@', {"requires": ["base-base", "io-base"]});
