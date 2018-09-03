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
//

/**
 * Atto recordrtc library functions for function abstractions
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ESLint directives.
/* eslint-disable camelcase */

// Scrutinizer CI directives.
/** global: M */
/** global: Y */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to module namespaces.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule;

M.atto_recordrtc.abstractmodule = {
    // A helper for making a Moodle alert appear.
    // Subject is the content of the alert (which error ther alert is for).
    // Possibility to add on-alert-close event.
    show_alert: function(subject, onCloseEvent) {
        Y.use('moodle-core-notification-alert', function() {
            var dialogue = new M.core.alert({
                title: M.util.get_string(subject + '_title', 'atto_recordrtc'),
                message: M.util.get_string(subject, 'atto_recordrtc')
            });

            if (onCloseEvent) {
                dialogue.after('complete', onCloseEvent);
            }
        });
    },

    // Handle getUserMedia errors.
    handle_gum_errors: function(error, commonConfig) {
        var btnLabel = M.util.get_string('recordingfailed', 'atto_recordrtc'),
            treatAsStopped = function() {
                commonConfig.onMediaStopped(btnLabel);
            };

        // Changes 'CertainError' -> 'gumcertain' to match language string names.
        var stringName = 'gum' + error.name.replace('Error', '').toLowerCase();

        // After alert, proceed to treat as stopped recording, or close dialogue.
        if (stringName !== 'gumsecurity') {
            am.show_alert(stringName, treatAsStopped);
        } else {
            am.show_alert(stringName, function() {
                cm.editorScope.closeDialogue(cm.editorScope);
            });
        }
    },

    // Select best options for the recording codec.
    select_rec_options: function(recType) {
        var types, options;

        if (recType === 'audio') {
            types = [
                'audio/webm;codecs=opus',
                'audio/ogg;codecs=opus'
            ];
            options = {
                audioBitsPerSecond: window.parseInt(cm.editorScope.get('audiobitrate'))
            };
        } else {
            types = [
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=h264,opus',
                'video/webm;codecs=vp8,opus'
            ];
            options = {
                audioBitsPerSecond: window.parseInt(cm.editorScope.get('audiobitrate')),
                videoBitsPerSecond: window.parseInt(cm.editorScope.get('videobitrate'))
            };
        }

        var compatTypes = types.filter(function(type) {
            return window.MediaRecorder.isTypeSupported(type);
        });

        if (compatTypes.length !== 0) {
            options.mimeType = compatTypes[0];
        }

        return options;
    }
};
