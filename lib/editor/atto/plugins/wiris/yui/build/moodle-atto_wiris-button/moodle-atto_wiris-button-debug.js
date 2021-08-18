YUI.add('moodle-atto_wiris-button', function (Y, NAME) {

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

/*
 * @package    atto_atto_wiris
 * @copyright  2011, Maths for More S.L. http://www.wiris.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_atto_wiris-button
 */

/**
 * MathType for Atto plugin.
 *
 * @namespace M.atto_wiris
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */
Y.namespace('M.atto_wiris').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    initializer: function(config) {

        // Filter not enabled at course level so no continue.
        if (!config.filter_enabled) {
            return;
        }

        Y.Get.js(M.cfg.wwwroot + '/lib/editor/atto/plugins/wiris/core.js?v=' + config.version, function(err) {
            if (err) {
                Y.log('Could not load core.js');
            } else {
                // Once the core is loaded we can extend the IntegrationModel class.

                /**
                 * AttoIntegration constructor. Extends from IntegrationModel class.
                 * @param {object} integrationModelProperties - Integration model properties.
                 */
                AttoIntegration = function(integrationModelProperties) {
                    WirisPlugin.IntegrationModel.call(this, integrationModelProperties);
                    this.config = integrationModelProperties.config;
                };

                AttoIntegration.prototype = Object.create(WirisPlugin.IntegrationModel && WirisPlugin.IntegrationModel.prototype);


                /**
                 * Returns the absolute plugin path.
                 * @returns {string} - plugin path.
                 */
                AttoIntegration.prototype.getPath = function() {
                    return M.cfg.wwwroot + '/lib/editor/atto/plugins/wiris';
                };

                /**
                 * Returns the integration language, i.e, Atto language
                 * @returns {string} - integration language.
                 */
                AttoIntegration.prototype.getLanguage = function() {
                    return this.config.lang;
                };

                /**
                 * Handles a double click on the target element. In this integration
                 * we stop de event propagation to avoid Moodle opening image edit dialog.
                 * @param {object} element - DOM object target.
                 * @param {event} event - D¡double click event.
                 */
                AttoIntegration.prototype.doubleClickHandler = function(element, event) {
                    var isWirisformula = element.classList.contains('Wirisformula');
                    if (isWirisformula) {
                        event.stopPropagation();
                        WirisPlugin.IntegrationModel.prototype.doubleClickHandler.call(this, element, event);
                    }
                };

                /**
                 * Converts a MathML to an image and insert the image in the DOM object. Once the
                 * formula is updated the edit object is marked as updated.
                 * @param {string} mathml - target MathML.
                 */
                AttoIntegration.prototype.updateFormula = function(mathml) {
                    WirisPlugin.IntegrationModel.prototype.updateFormula.call(this, mathml);
                    var host = this.editorObject.get('host');
                    var html = host.textarea.get('value');
                    var value = this.convertSafeMathml(WirisPlugin.Parser.endParse(html, null, this.config.lang, true));
                    host.textarea.set('value', value);
                    this.editorObject.markUpdated();
                };

                /**
                 * Callback function. This function is called before 'onTargetReady' event
                 * is fired. We listen to form 'submit' event to un-parse the content.
                 */
                AttoIntegration.prototype.callbackFunction = function() {
                    WirisPlugin.IntegrationModel.prototype.callbackFunction.call(this);
                    this.parseContent();
                    // Adding submit event.
                    var form = this.editorObject.get('host').textarea.ancestor('form');

                    if (form) {
                        form.on('submit', this.submit, this);
                    }
                };

                /**
                 * Converts all MathML inside the editor object to img elements.
                 * **/
                AttoIntegration.prototype.parseContent = function() {
                    var host = this.editorObject.get('host');
                    var html = host.editor.get('innerHTML');
                    // html = this._convertSafeMath(html);
                    html = WirisPlugin.Parser.initParse(html, this.config.lang);
                    host.editor.set('innerHTML', html);
                    this.editorObject.markUpdated();
                };

                /**
                 * Converts all MathType images inside the editor object into MathML.
                 */
                AttoIntegration.prototype.unParseContent = function() {
                    var host = this.editorObject.get('host');
                    var html = host.textarea.get('value');
                    var value = this.convertSafeMathml(WirisPlugin.Parser.endParse(html, null, this.config.lang, true));
                    host.textarea.set('value', value);
                };

                /**
                 * This method is called once the form is submitted. Replaces the content of the
                 * editor textarea replacing MathType formulas for the correspondent MathML.
                 */
                AttoIntegration.prototype.submit = function() {
                    var host = this.editorObject.get('host');
                    // We get the HTML content (with the imnages) instead of the raw html content
                    // and convert images into data-mathml attribute.
                    var html = host.editor.get('innerHTML');
                    // Check if exist mathml tag for parse.
                    if (html.indexOf('math»') >= 0 || html.indexOf('math>') >= 0) {
                        host.textarea.set('value', WirisPlugin.Parser.endParse(html, null, this.config.lang, true));
                    }
                };

                /**
                 * Transform all occurrences of safeMatML in a text for MathML.
                 * @param {string} content - original content.
                 * @returns {string} - parsed original content.
                 */
                AttoIntegration.prototype.convertSafeMathml = function(content) {
                   var output = '';
                   var mathTagBegin = '«math';
                   var mathTagEnd = '«/math»';
                   var start = content.indexOf(mathTagBegin);
                   var end = 0;

                   while (start != -1) {
                       output += content.substring(end, start);
                       // Avoid WIRIS images to be parsed.
                       imageMathmlAttribute = content.indexOf(WirisPlugin.Configuration.get('imageMathmlAttribute'));
                       end = content.indexOf(mathTagEnd, start);

                       if (end == -1) {
                           end = content.length - 1;
                       } else if (imageMathmlAttribute != -1) {
                           // First close tag of img attribute
                           // If a mathmlAttribute exists should be inside a img tag.
                           end += content.indexOf("/>", start);
                       }
                       else {
                           end += mathTagEnd.length;
                       }

                       if (!WirisPlugin.MathML.isMathmlInAttribute(content, start) && imageMathmlAttribute == -1) {
                           var mathml = content.substring(start, end);
                           output += WirisPlugin.MathML.safeXmlDecode(mathml);
                       }
                       else {
                           output += content.substring(start, end);
                       }

                       start = content.indexOf(mathTagBegin, end);
                   }

                   output += content.substring(end, content.length);
                   return output;
                };

               /**
                * Integration model properties.
                * @type {object}
                * @property {string} configurationService - URL for configuration service.
                * @property {object} editorObject - editor object.
                * @property {object} target - integration DOM target.
                * @property {string} stringName - integration script name.
                * @property {object} config - Atto plugin config object.
                *
                */
                var integrationModelProperties = {};
                integrationModelProperties.configurationService = M.cfg.wwwroot + '/filter/wiris/integration/configurationjs.php';
                integrationModelProperties.editorObject = this;
                integrationModelProperties.target = this.get('host').editor.getDOMNode();
                integrationModelProperties.scriptName = '';
                integrationModelProperties.config = config;

                // Here we create a new instance of AttoIntegration.
                var attoIntegrationInstance = new AttoIntegration(integrationModelProperties);
                attoIntegrationInstance.init();
                // We don't need to wait for anything. The event 'onTargetReady' can be fired.
                attoIntegrationInstance.listeners.fire('onTargetReady', {});

                // Despite the number of Atto editors we only need a single instance.
                WirisPlugin.currentInstance = attoIntegrationInstance;
            }
        }.bind(this));

        this._addButtons(config);

        // Global events to host.
        var host = this.get('host');

        // It's needed to parse the content on selectionchanged event in order to recover properly
        // the content of the editor in drafts.
        // For more information view PLUGINS-1009
        host.on('atto:selectionchanged', function(e) {
            // This condition is satisfied when event is thrown by draft
            if (typeof e.event == 'undefined') {
                var html = host.editor.get('innerHTML');
                html = WirisPlugin.Parser.initParse(html, WirisPlugin.currentInstance.config.lang);
                host.editor.set('innerHTML', html);
            }
        });

        // Override updateFromTextArea to update the content editable element.
        host._wirisUpdateFromTextArea = host.updateFromTextArea;
        host.updateFromTextArea = function() {
            host._wirisUpdateFromTextArea();
            var html = host.editor.get('innerHTML');
            html = WirisPlugin.Parser.initParse(html, WirisPlugin.currentInstance.config.lang);
            host.editor.set('innerHTML', html);
        };
        // Override updateOriginal to update the content of the text area element.
        host._wirisupdateOriginal = host.updateOriginal;
        host.updateOriginal = function() {
            host._wirisupdateOriginal();
            var html = host.textarea.get('value');
            var value = WirisPlugin.Parser.endParse(html);
            value = _convertSafeMathML(value);
            host.textarea.set('value', value);
        };

        /**
         * Converts all the occurrences of a safeMathml
         * with standard MathML.
         * @type {string} - content content to be filtered.
         * @returns {string} the original content with MathML instead of safeMathML.
         */
        _convertSafeMathML = function(content) {
            var output = '';
            var mathTagBegin = '«math';
            var mathTagEnd = '«/math»';
            var start = content.indexOf(mathTagBegin);
            var end = 0;

            while (start != -1) {
                output += content.substring(end, start);
                // Avoid WIRIS images to be parsed.
                imageMathmlAttribute = content.indexOf(WirisPlugin.Configuration.get('imageMathmlAttribute'));
                end = content.indexOf(mathTagEnd, start);

                if (end == -1) {
                    end = content.length - 1;
                } else if (imageMathmlAttribute != -1) {
                    // First close tag of img attribute
                    // If a mathmlAttribute exists should be inside a img tag.
                    end += content.indexOf("/>", start);
                }
                else {
                    end += mathTagEnd.length;
                }

                if (!WirisPlugin.MathML.isMathmlInAttribute(content, start) && imageMathmlAttribute == -1) {
                    var mathml = content.substring(start, end);
                    output += WirisPlugin.MathML.safeXmlDecode(mathml);
                }
                else {
                    output += content.substring(start, end);
                }

                start = content.indexOf(mathTagBegin, end);
            }

            output += content.substring(end, content.length);
            return output;
        };
    },

    /**
     * Add MathType and ChemType buttons to toolbar.
     * @param {object} config - backend configuration object.
     */
    _addButtons: function(config) {
        if (parseInt(config.editor_is_active)) {
            this.addButton({
                title: 'wiris_editor_title',
                buttonName: 'wiris_editor',
                icon: 'formula',
                iconComponent: 'atto_wiris',
                callback: this._editorButton
            });
        }
        if (parseInt(config.chemistry_is_active)) {
            this.addButton({
                title: 'wiris_chem_editor_title',
                buttonName: 'wiris_chem_editor',
                icon: 'chem',
                iconComponent: 'atto_wiris',
                callback: this._chemButton
            });
        }
        // We add the button after the collapse plugin initially hide other
        // buttons. So we recall it here.
        var host = this.get('host');
        if (host.plugins.collapse) {
            host.plugins.collapse._setVisibility(host.plugins.collapse.buttons.collapse);
        }
    },
    /**
     * Callback for MathType button.
     */
    _editorButton: function() {
        WirisPlugin.currentInstance.editorObject = this;
        WirisPlugin.currentInstance.setTarget(this.get('host').editor.getDOMNode());
        WirisPlugin.currentInstance.core.getCustomEditors().disable();
        WirisPlugin.currentInstance.openNewFormulaEditor();
    },
    /**
     * Callback for ChemType button.
     */
    _chemButton: function() {
        WirisPlugin.currentInstance.editorObject = this;
        WirisPlugin.currentInstance.setTarget(this.get('host').editor.getDOMNode());
        WirisPlugin.currentInstance.getCore().getCustomEditors().enable('chemistry');
        WirisPlugin.currentInstance.openNewFormulaEditor();
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "get"]});
