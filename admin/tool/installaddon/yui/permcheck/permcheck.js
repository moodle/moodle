/**
 * Check for write permission for the selected plugin type
 *
 * @module      moodle-tool_installaddon-permcheck
 * @author      David Mudrak <david@moodle.com>
 */
YUI.add('moodle-tool_installaddon-permcheck', function(Y) {

    M.tool_installaddon = M.tool_installaddon || {};

    /**
     * @class permcheck
     * @static
     */
    M.tool_installaddon.permcheck = {

        /**
         * @method init
         * @param {Object} config Configuration passed from the PHP
         */
        init : function(config) {
            this.config = config;
            var plugintypesel = Y.one('#tool_installaddon_installfromzip_plugintype');
            if (plugintypesel) {
                plugintypesel.on('change', this.check_for_permission, this);
            }
        },

        /**
         * @method check_for_permission
         * @param {Event} e
         */
        check_for_permission : function(e) {
            var plugintype = e.currentTarget.get('value');
            if (plugintype == '') {
                return;
            }
            Y.log('Selected plugin type: ' + plugintype, 'debug', 'moodle-tool_installaddon-permcheck');
            Y.io(this.config.permcheckurl, {
                'method' : 'GET',
                'data' : {
                    'sesskey' : M.cfg.sesskey,
                    'plugintype' : plugintype
                },
                'arguments' : {
                    'plugintypeselector' : e.currentTarget,
                    'showresult' : function(msg, status) {
                        var resultline = Y.one('#tool_installaddon_installfromzip_permcheck');
                        if (resultline) {
                            if (status === 'success') {
                                resultline.setContent('<span class="success"><img src="' + M.util.image_url('i/valid') + '" /> ' +
                                    msg + '</span>');
                            } else if (status === 'progress') {
                                resultline.setContent('<span class="progress"><img src="' + M.cfg.loadingicon + '" /> ' +
                                    msg + '</span>');
                            } else {
                                resultline.setContent('<span class="error"><img src="' + M.util.image_url('i/invalid') + '" /> ' +
                                    msg + '</span>');
                            }
                        }
                    }
                },
                'on' : {
                    'start' : function(transid, args) {
                        args.showresult(M.util.get_string('permcheckprogress', 'tool_installaddon'), 'progress');
                    },
                    'success': function(transid, outcome, args) {
                        var response;
                        try {
                            response = Y.JSON.parse(outcome.responseText);
                            if (response.error) {
                                Y.log(response.error, 'error', 'moodle-tool_installaddon-permcheck');
                                args.showresult(M.util.get_string('permcheckerror', 'tool_installaddon', response), 'error');
                            } else if (response.path && response.writable == 1) {
                                args.showresult(M.util.get_string('permcheckresultyes', 'tool_installaddon', response), 'success');
                            } else if (response.path && response.writable == 0) {
                                args.showresult(M.util.get_string('permcheckresultno', 'tool_installaddon', response), 'error');
                            } else {
                                Y.log(response, 'debug', 'moodle-tool_installaddon-permcheck');
                                args.showresult(M.util.get_string('permcheckerror', 'tool_installaddon', response), 'error');
                            }

                        } catch (e) {
                            Y.log(e, 'error', 'moodle-tool_installaddon-permcheck');
                            args.showresult(M.util.get_string('permcheckerror', 'tool_installaddon'), 'error');
                        }
                    },
                    'failure': function(transid, outcome, args) {
                        Y.log(outcome.statusText, 'error', 'moodle-tool_installaddon-permcheck');
                        args.showresult(M.util.get_string('permcheckerror', 'tool_installaddon'));
                    }
                }
            });
        },

        /**
         * @property
         * @type {Object}
         */
        config : null
    };

}, '@VERSION@', {
    requires:['node', 'event', 'io-base']
});
