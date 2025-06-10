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
 * @package    atto_vidgridbutton
 * @copyright  Panopto 2009 - 2016 With contributions from Joseph Malmsten (joseph.malmsten@gmail.com)
 * @copyright  ilos 2017
 * @copyright  VidGrid 2018 - 2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_vidgridbutton-button
 */

/**
 * Atto text editor vidgridbutton plugin.
 *
 * @namespace M.atto_vidgridbutton
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var _serverPath = 'https://app.vidgrid.com/lti/embedSsoAutoLogin';
var _iframeId = 'moodleLtiIframe';

var COMPONENTNAME = 'atto_vidgridbutton',
    SELECTALIGN = 'float:left; display:none',
    TEMPLATE = '<iframe src="{{src}}" id="{{id}}" height="{{height}}" width="{{width}}" scrolling="auto"></iframe>';

    Y.namespace('M.atto_vidgridbutton').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

        /**
         * Initialize the button
         *
         * @method Initializer
         */
        initializer: function () {
            // If we don't have the capability to view then give up.
            if (this.get('disabled')) {
                return;
            }

            var $icon = 'iconone';

            // Add the vidgridbutton icon/buttons.
            this.addButton({
                icon: 'ed/' + $icon,
                iconComponent: 'atto_vidgridbutton',
                buttonName: $icon,
                callback: this._displayDialogue,
                callbackArgs: $icon
            });
        },

        /**
         * Display the vidgridbutton Dialogue
         *
         * @method _displayDialogue
         * @private
         */
        _displayDialogue: function (e, $clickedicon) {
            var $width = 900,
                $height = 700,
                $dialogue = this.getDialogue({
                    headerContent: M.util.get_string('dialogtitle', COMPONENTNAME),
                    width: $width + 'px',
                    height: $height + 'px',
                    focusAfterHide: $clickedicon
                });

            e.preventDefault();

            // When dialog becomes invisible, reset it. This fixes problems with multiple editors per page.
            $dialogue.after('visibleChange', function() {
                var $attributes = $dialogue.getAttrs();

                if($attributes.visible === false) {
                    setTimeout(function() {
                        $dialogue.reset();
                    }, 5);
                }
            });

            // Dialog doesn't detect changes in width without this.
            // If you reuse the dialog, this seems necessary.
            if ($dialogue.width !== $width + 'px') {
                $dialogue.set('width', $width + 'px');
            }

            if ($dialogue.height !== $height + 'px') {
                $dialogue.set('height', $height + 'px');
            }

            $dialogue.set('bodyContent', this._getFormContent($clickedicon));

            $dialogue.show();

            this._doInsert(this);
        },

        /**
         * Return the dialogue content for the tool, attaching any required
         * events.
         *
         * @method _getDialogueContent
         * @return {Node} The content to place in the dialogue.
         * @private
         */
        _getFormContent: function ($clickedicon) {

            var $sessKey =  this.get('sessKey');
            var $returnUrl = this.get('webRoot')+'/mod/lti/return.php?course='+this.get('courseId')+'&sesskey='+$sessKey;

            var $orgApiKey =  this.get('orgApiKey');

            var $launchUrl = _serverPath+'?oauth_consumer_key='+$orgApiKey+'&launch_presentation_return_url='
                + encodeURIComponent($returnUrl)
                + "&tool_consumer_info_product_family_code=moodle";

            var $template = Y.Handlebars.compile(TEMPLATE),
                $content = Y.Node.create($template({
                    elementid: this.get('host').get('elementid'),
                    component: COMPONENTNAME,
                    clickedicon: $clickedicon,
                    src: $launchUrl,
                    height: 650,
                    width: 850,
                    id: _iframeId,
                    selectalign: SELECTALIGN
                }));

            this._form = $content;
            return $content;
        },

        /**
         * Inserts the users input onto the page
         * @method _getDialogueContent
         * @private
         */
        _doInsert: function ($parent) {

            var $iframeEl = document.getElementById( _iframeId );

            $iframeEl.onload= function() {

                var $innerIframe = $iframeEl.contentDocument;

                if(!$innerIframe)
                {
                    return;
                }

                var $innerElement = $innerIframe.getElementById("page-content").querySelector('[role="main"]');
                var $url = $innerElement.innerText;
                var $search = $url.search("https://");
                $url = $url.substr($search);

                if ($url.indexOf("vidgrid") <= 0)
                {
                    return;
                }

                var $iframe = '<iframe allowfullscreen="" frameborder="0" height="315"'
                    + ' src="'+$url+'" width="560"></iframe>';

                $parent.getDialogue({ focusAfterHide: null }).hide();
                $parent.editor.focus();
                $parent.get('host').insertContentAtFocusPoint($iframe);
                $parent.markUpdated();

            };
        }
    }, {
        ATTRS: {
            disabled: {
                value: false
            },
            courseId: {
                value: null
            },
            orgApiKey: {
                value: null
            },
            webRoot: {
                value: null
            },
            sessKey: {
                value: null
            }
        }
    });
