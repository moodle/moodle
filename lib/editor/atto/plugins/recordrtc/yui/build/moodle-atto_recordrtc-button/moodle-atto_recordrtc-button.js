YUI.add('moodle-atto_recordrtc-button', function (Y, NAME) {

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
 * Atto recordrtc library functions
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_recordrtc-button
 */

/**
 * Atto text editor recordrtc plugin.
 *
 * @namespace M.atto_recordrtc
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

// ESLint directives.
/* eslint-disable camelcase, spaced-comment */

// JSHint directives.
/*global M */
/*jshint onevar: false */

// Scrutinizer CI directives.
/** global: Y */
/** global: M */

var PLUGINNAME = 'atto_recordrtc',
    TEMPLATE = '' +
    '<div class="{{PLUGINNAME}} container-fluid">' +
      '<div class="{{bs_row}} hide">' +
        '<div class="{{bs_col}}12">' +
          '<div id="alert-danger" class="alert {{bs_al_dang}}">' +
            '<strong>{{insecurealert_title}}</strong> {{insecurealert}}' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="{{bs_row}} hide">' +
        '{{#if isAudio}}' +
          '<div class="{{bs_col}}1"></div>' +
          '<div class="{{bs_col}}10">' +
            '<audio id="player"></audio>' +
          '</div>' +
          '<div class="{{bs_col}}1"></div>' +
        '{{else}}' +
          '<div class="{{bs_col}}12">' +
            '<video id="player"></video>' +
          '</div>' +
        '{{/if}}' +
      '</div>' +
      '<div class="{{bs_row}}">' +
        '<div class="{{bs_col}}1"></div>' +
        '<div class="{{bs_col}}10">' +
          '<button id="start-stop" class="{{bs_ss_btn}}">{{startrecording}}</button>' +
        '</div>' +
        '<div class="{{bs_col}}1"></div>' +
      '</div>' +
      '<div class="{{bs_row}} hide">' +
        '<div class="{{bs_col}}3"></div>' +
        '<div class="{{bs_col}}6">' +
          '<button id="upload" class="btn btn-primary btn-block">{{attachrecording}}</button>' +
        '</div>' +
        '<div class="{{bs_col}}3"></div>' +
      '</div>' +
    '</div>';

Y.namespace('M.atto_recordrtc').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * The current language by default.
     */
    _lang: 'en',

    initializer: function() {
        if (this.get('host').canShowFilepicker('media')) {
            // Add audio and/or video buttons depending on the settings.
            var allowedtypes = this.get('allowedtypes');
            if (allowedtypes === 'both' || allowedtypes === 'audio') {
                this._addButton('audio', this._audio);
            }
            if (allowedtypes === 'both' || allowedtypes === 'video') {
                this._addButton('video', this._video);
            }

            // Initialize the dialogue box.
            var dialogue = this.getDialogue({
                width: 1000,
                focusAfterHide: null
            });

            // If dialogue is closed during recording, do the following.
            dialogue.after('visibleChange', function() {
                var closed = !dialogue.get('visible'),
                    m = M.atto_recordrtc.commonmodule;

                if (closed) {
                    window.clearInterval(m.countdownTicker);

                    if (m.mediaRecorder && m.mediaRecorder.state !== 'inactive') {
                        m.mediaRecorder.stop();
                    }

                    if (m.stream) {
                        m.stream.getTracks().forEach(function(track) {
                            if (track.readyState !== 'ended') {
                                track.stop();
                            }
                        });
                    }
                }

            });

            dialogue.on('click', function() {
                this.centered();
            });

            // Require adapter.js library.
            window.require(['core/adapter'], function(adapter) {
                window.adapter = adapter;
            });
        }
    },

    /**
     * Add the buttons to the Atto toolbar.
     *
     * @method _addButton
     * @param {string} type
     * @param {callback} callback
     * @private
     */
    _addButton: function(type, callback) {
        this.addButton({
            buttonName: type,
            icon: this.get(type + 'rtcicon'),
            iconComponent: PLUGINNAME,
            callback: callback,
            title: type + 'rtc',
            tags: type + 'rtc',
            tagMatchRequiresAll: false
        });
    },

    /**
     * Toggle audiortc and normal display mode
     *
     * @method _audio
     * @private
     */
    _audio: function() {
        var dialogue = this.getDialogue();

        dialogue.set('headerContent', M.util.get_string('audiortc', 'atto_recordrtc'));
        dialogue.set('bodyContent', this._createContent('audio'));

        dialogue.show();

        M.atto_recordrtc.audiomodule.init(this);
    },

    /**
     * Toggle videortc and normal display mode
     *
     * @method _video
     * @private
     */
    _video: function() {
        var dialogue = this.getDialogue();

        dialogue.set('headerContent', M.util.get_string('videortc', 'atto_recordrtc'));
        dialogue.set('bodyContent', this._createContent('video'));

        dialogue.show();

        M.atto_recordrtc.videomodule.init(this);
    },

    /**
     * Create the HTML to be displayed in the dialogue box
     *
     * @method _createContent
     * @param {string} type
     * @returns {Object}
     * @private
     */
    _createContent: function(type) {
        var isAudio = (type === 'audio'),
            bsRow = 'row',
            bsCol = 'col-xs-',
            bsAlDang = 'alert-danger',
            bsSsBtn = 'btn btn-lg btn-outline-danger btn-block';

        var bodyContent = Y.Handlebars.compile(TEMPLATE)({
            PLUGINNAME: PLUGINNAME,
            isAudio: isAudio,
            bs_row: bsRow,
            bs_col: bsCol,
            bs_al_dang: bsAlDang,
            bs_ss_btn: bsSsBtn,
            insecurealert_title: M.util.get_string('insecurealert_title', 'atto_recordrtc'),
            insecurealert: M.util.get_string('insecurealert', 'atto_recordrtc'),
            startrecording: M.util.get_string('startrecording', 'atto_recordrtc'),
            attachrecording: M.util.get_string('attachrecording', 'atto_recordrtc')
        });

        return bodyContent;
    },

    /**
     * Close the dialogue without further action.
     *
     * @method closeDialogue
     * @param {Object} scope The "this" context of the editor.
     */
    closeDialogue: function(scope) {
        scope.getDialogue().hide();

        scope.editor.focus();
    },

    /**
     * Insert the annotation link in the editor.
     *
     * @method setLink
     * @param {Object} scope The "this" context of the editor.
     * @param {string} annotation The HTML link to the recording.
     */
    setLink: function(scope, annotation) {
        scope.getDialogue().hide();

        scope.editor.focus();
        scope.get('host').insertContentAtFocusPoint(annotation);
        scope.markUpdated();
    }
}, {
    ATTRS: {
        /**
         * The contextid to use when generating this recordrtc.
         *
         * @attribute contextid
         * @type String
         */
        contextid: {
            value: null
        },

        /**
         * The sesskey to use when generating this recordrtc.
         *
         * @attribute sesskey
         * @type String
         */
        sesskey: {
            value: null
        },

        /**
         * The allowedtypes to use when generating this recordrtc.
         *
         * @attribute allowedtypes
         * @type String
         */
        allowedtypes: {
            value: null
        },

        /**
         * The audiobitrate to use when generating this recordrtc.
         *
         * @attribute audiobitrate
         * @type String
         */
        audiobitrate: {
            value: null
        },

        /**
         * The videobitrate to use when generating this recordrtc.
         *
         * @attribute videobitrate
         * @type String
         */
        videobitrate: {
            value: null
        },

        /**
         * The timelimit to use when generating this recordrtc.
         *
         * @attribute timelimit
         * @type String
         */
        timelimit: {
            value: null
        },

        /**
         * The audiortcicon to use when generating this recordrtc.
         *
         * @attribute audiortcicon
         * @type String
         */
        audiortcicon: {
            value: null
        },

        /**
         * The videortcicon to use when generating this recordrtc.
         *
         * @attribute videortcicon
         * @type String
         */
        videortcicon: {
            value: null
        },

        /**
         * Maximum upload size set on server, in bytes.
         *
         * @attribute maxrecsize
         * @type String
         */
        maxrecsize: {
            value: null
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "moodle-atto_recordrtc-recording"]});
