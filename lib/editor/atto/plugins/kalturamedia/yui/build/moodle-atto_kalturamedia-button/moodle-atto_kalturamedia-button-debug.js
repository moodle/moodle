YUI.add('moodle-atto_kalturamedia-button', function (Y, NAME) {

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
 * @package    atto_kalturamedia
 * @copyright  2Kaltura
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_kalturamedia-button
 */

/**
 * Atto text editor kalturamedia plugin.
 *
 * @namespace M.atto_kalturamedia
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_kalturamedia',
    CSS = {
        URLINPUT: 'atto_kalturamedia_urlentry',
        NAMEINPUT: 'atto_kalturamedia_nameentry'
    },
    SELECTORS = {
        URLINPUT: '.' + CSS.URLINPUT,
        NAMEINPUT: '.' + CSS.NAMEINPUT
    };

Y.namespace('M.atto_kalturamedia').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
        _currentSelection: null,
        embedWindow: null,

        initializer: function() {
            this.addButton({
                icon: 'icon',
                iconComponent: COMPONENTNAME,
                callback: this._kalturamedia
            });
        },
        _kalturamedia: function() {
            this._currentSelection = this.get('host').getSelection();
            if (this._currentSelection === false) {
                return;
            }

            var w = 1200;
            var h = 700;
            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var left = ((width / 2) - (w / 2)) + dualScreenLeft;
            var top = ((height / 2) - (h / 2)) + dualScreenTop;
            var newWindow = window.open(this._getIframeURL(), M.util.get_string("browse_and_embed", COMPONENTNAME), 'scrollbars=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

            window.buttonJs = this;

            if (window.focus) {
                newWindow.focus();
            }

            this.embedWindow = newWindow;
        },

        _getIframeURL: function() {

            var args = Y.mix({
                    elementid: this.get('host').get('elementid'),
                    contextid: this.get('contextid'),
                    height: '600px',
                    width: '1112px'
                },
                this.get('area'));
            return M.cfg.wwwroot + '/lib/editor/atto/plugins/kalturamedia/ltibrowse_container.php?' +
                Y.QueryString.stringify(args);
        },

        _getCourseId: function() {
            var courseId;
            var bodyClasses = document.getElementsByTagName('body')[0].className;
            var classes = bodyClasses.split(' ');
            for(i in classes)
            {
                if(classes[i].indexOf('course-') > -1)
                {
                    var parts = classes[i].split('-');
                    courseId = parts[1];
                }
            }

            return courseId;
        },

        _removeProtocolFromUrl: function(fullUrl) {
            return fullUrl.replace(/^https?:\/\//,'');
        },

        embedItem: function(what, data) {
            var sourceUrl = data.url;
            var url = this._removeProtocolFromUrl(sourceUrl);
            var parser = document.createElement('a');
            parser.href = sourceUrl;
            url += parser.search;

            var content = '<a href="http://'+url+'">tinymce-kalturamedia-embed||'+data.title+'||'+data.width+'||'+data.height+'</a>';

            host = this.get('host');
            host.setSelection(this._currentSelection);
            host.insertContentAtFocusPoint(content);
            this.markUpdated();
            this.embedWindow.close();
        }

    } , {
        ATTRS: {
            /**
             * The contextid to use when generating this preview.
             *
             * @attribute contextid
             * @type String
             */
            contextid: {
                value: null
            },

            /**
             * The KAF URI, as configured in Kaltura's plugin settings.
             */
            kafuri: {
                value: null
            }
        }}
);


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
