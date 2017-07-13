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
 * @module     moodle-core-checknet
 * @package    core
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-core-checknet
 */

/**
 * @namespace M.core
 * @class checknet
 */

function CheckNet() {
    CheckNet.superclass.constructor.apply(this, arguments);
}

Y.extend(CheckNet, Y.Base, {
    /**
     * A link to the warning dialogue.
     *
     * @property _alertDialogue
     * @type M.core.dialogue
     * @private
     * @default null
     */
    _alertDialogue: null,

    /**
     * Setup the checking mechanism.
     *
     * @method initializer
     */
    initializer: function() {
        // Perform our first check.
        this._scheduleCheck();
    },

    /**
     * Schedule a check of the checknet file.
     *
     * @method _scheduleCheck
     * @chainable
     * @private
     */
    _scheduleCheck: function() {
        // Schedule the next check after five seconds.
        Y.later(this.get('frequency'), this, this._performCheck);

        return this;
    },

    /**
     * Perform an immediate check of the checknet file.
     *
     * @method _performCheck
     * @private
     */
    _performCheck: function() {
        Y.io(this.get('uri'), {
            data: {
                // Add the session key.
                sesskey: M.cfg.sesskey,
                // Add a query string to prevent older versions of IE from using the cache.
                time: new Date().getTime()
            },
            timeout: this.get('timeout'),
            headers: {
                'Cache-Control': 'no-cache',
                'Expires': '-1'
            },
            context: this,
            on: {
                complete: function(tid, response) {
                    // Check for failure conditions.
                    // We check for a valid status here because if the user is moving away from the page at the time we
                    // run this callback we do not want to display the error.
                    if (response && typeof response.status !== "undefined") {
                        var code = parseInt(response.status, 10);

                        if (code === 200) {
                            // This is a valid attempt - clear any existing warning dialogue and destroy it.
                            if (this._alertDialogue) {
                                this._alertDialogue.destroy();
                                this._alertDialogue = null;
                            }
                        } else if (code >= 300 && code <= 399) {
                            // This is a cached status - warn developers, but otherwise ignore.
                            Y.log("A cached copy of the checknet status file was returned so it's reliablity cannot be guaranteed",
                                'warn',
                                'moodle-mod_scorm-checknet');
                        } else {
                            if (this._alertDialogue === null || this._alertDialogue.get('destroyed')) {
                                // Only create a new dialogue if it isn't already displayed.
                                this._alertDialogue = new M.core.alert({
                                    message: M.util.get_string.apply(this, this.get('message'))
                                });
                            } else {
                                this._alertDialogue.show();
                            }
                        }
                    }

                    // Start the next check.
                    this._scheduleCheck();
                }
            }
        });
    }
}, {
    NAME: 'checkNet',
    ATTRS: {
        /**
         * The file to check access against.
         *
         * @attribute uri
         * @type String
         * @default M.cfg.wwwroot + '/lib/yui/build/moodle-core-checknet/assets/checknet.txt'
         */
        uri: {
            value: M.cfg.wwwroot + '/lib/yui/build/moodle-core-checknet/assets/checknet.txt'
        },

        /**
         * The timeout (in milliseconds) before the checker should give up and display a warning.
         *
         * @attribute timeout
         * @type Number
         * @value 4000
         */
        timeout: {
            value: 4000
        },

        /**
         * The frequency (in milliseconds) that checks should be run.
         * A new check is not begun until the previous check has completed.
         *
         * @attribute frequency
         * @writeOnce
         * @type Number
         * @value 10000
         */
        frequency: {
            value: 10000
        },

        /**
         * The message which should be displayed upon a test failure.
         *
         * The array values are passed directly to M.util.get_string() and arguments should match accordingly.
         *
         * @attribute message
         * @type Array
         * @value [
         *  'networkdropped',
         *  'moodle'
         * ]
         */
        message: {
            value: [
                'networkdropped',
                'moodle'
            ]
        }
    }
});

M.core = M.core || {};
M.core.checknet = M.core.checknet || {};
M.core.checknet.init = function(config) {
    return new CheckNet(config);
};
