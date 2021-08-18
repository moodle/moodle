import IntegrationModel from '@wiris/mathtype-integration-js-dev/src/integrationmodel.js';
import Configuration from '@wiris/mathtype-integration-js-dev/src/configuration.js';
import Parser from '@wiris/mathtype-integration-js-dev/src/parser.js';
import Util from '@wiris/mathtype-integration-js-dev/src/util';
import Listeners from '@wiris/mathtype-integration-js-dev/src/listeners';

/**
 * TinyMCE integration class. This class extends IntegrationModel class.
 */
export class TinyMceIntegration extends IntegrationModel {

    constructor(integrationModelProperties) {
        super(integrationModelProperties);
        /**
         * Indicates if the content of the TinyMCe editor has
         * been parsed.
         * @type {Boolean}
         */
        this.initParsed = integrationModelProperties.initParsed;
        /**
         * Indicates if the TinyMCE is integrated in Moodle.
         * @type {Boolean}
         */
        this.isMoodle = integrationModelProperties.isMoodle;
        /**
         * Indicates if the plugin is loaded as an external plugin by TinyMCE.
         * @type {Boolean}
         */
        this.isExternal = integrationModelProperties.isExternal;
    }


    /**
     * Returns the absolute path of the integration script. Depends on
     * TinyMCE integration (Moodle or standard).
     * @returns {Boolean} - Absolute path for the integration script.
     */
    getPath() {
        if (this.isMoodle) {
            var search = 'lib/editor/tinymce';
            var pos = tinymce.baseURL.indexOf(search);
            var baseURL = tinymce.baseURL.substr(0, pos + search.length);
            return baseURL + '/plugins/tiny_mce_wiris/tinymce/';
        } else if (this.isExternal) {
            var externalUrl = this.editorObject.getParam('external_plugins')['tiny_mce_wiris'];
            return externalUrl.substring(0, externalUrl.lastIndexOf('/')+1)

        } else {
            return tinymce.baseURL + '/plugins/tiny_mce_wiris/';
        }
    }

    /**
     * Returns the absolute path of plugin icons. A set of different
     * icons is needed for TinyMCE and Moodle 2.5-
     * @returns {String} - Absolute path of the icons folder.
     */
    getIconsPath() {
        if (this.isMoodle && Configuration.get('versionPlatform') < 2013111800) {
            return this.getPath() + 'icons/tinymce3/';
        } else {
            return this.getPath() + 'icons/';
        }

    }

    /**
     * Returns the integration language. TinyMCE language is inherited.
     * When no language is set, TinyMCE sets the toolbar to english.
     * @returns {String} - Integration language.
     */
    getLanguage() {
        if (this.editorObject.settings['wirisformulaeditorlang']) {
            return editor.settings['wirisformulaeditorlang'];
        }
        const langParam = this.editorObject.getParam('language');
        return langParam ? langParam : 'en';
    }

    /**
     * Callback function called before 'onTargetLoad' is fired. All the logic here is to
     * avoid TinyMCE change MathType formulas.
     */
    callbackFunction() {
        var dataImgFiltered = [];
        super.callbackFunction();

        // Avoid to change class of image formulas.
        var imageClassName = Configuration.get('imageClassName');
        if (this.isIframe) {
            // Attaching observers to wiris images.
            if (typeof Parser.observer != 'undefined') {
                Array.prototype.forEach.call(this.target.contentDocument.getElementsByClassName(imageClassName), function(wirisImages) {
                    Parser.observer.observe(wirisImages);
                });
            }
        } else { // Inline.
            // Attaching observers to wiris images.
            Array.prototype.forEach.call(document.getElementsByClassName(imageClassName), function(wirisImages) {
                Parser.observer.observe(wirisImages);
            });
        }

        // When a formula is updated TinyMCE 'Change' event must be fired.
        // See https://www.tiny.cloud/docs/advanced/events/#change for further information.
        var listener = Listeners.newListener('onAfterFormulaInsertion', function() {
            if (typeof this.editorObject.fire != 'undefined') {
                this.editorObject.fire('Change');
            }
        }.bind(this));
        this.getCore().addListener(listener);

        // Avoid filter formulas with performance enabled.
        dataImgFiltered[this.editorObject.id] = this.editorObject.settings.images_dataimg_filter;
        this.editorObject.settings.images_dataimg_filter = function(img) {
            if (img.hasAttribute('class') && img.getAttribute('class').indexOf(Configuration.get('imageClassName')) != -1) {
                return img.hasAttribute('internal-blob');
            }
            else {
                // If the client put an image data filter, run. Otherwise default behaviour (put blob).
                if (typeof dataImgFiltered[this.editorObject.id] != 'undefined') {
                    return dataImgFiltered[this.editorObject.id](img);
                }
                return true;
            }
        }
    }

    /**
     * Fires the event ExecCommand and transform a MathML into an image formula.
     * @param {string} mathml - MathML to generate the formula and can be caught with the event.
     */
    updateFormula(mathml) {
        if (typeof this.editorObject.fire != 'undefined') {
            this.editorObject.fire('ExecCommand',{command: 'updateFormula', value: mathml});
        }
        super.updateFormula(mathml);
    }
}

/**
 * Object containing all TinyMCE integration instances. One for each TinyMCE editor.
 * @type {Object}
 */
export var instances = {};
/**
 * TinyMCE integration current instance. The current instance
 * is the instance related with the focused editor.
 * @type {TinyMceIntegration}
 */
export var currentInstance = null;

/* Plugin integration */
(function () {
    tinymce.create('tinymce.plugins.tiny_mce_wiris', {
        init: function (editor, url) {

            // Array with MathML valid alements.
            var validMathML = [
                            'math[*]',
                            'maction[*]]',
                            'malignmark[*]',
                            'maligngroup[*]',
                            'menclose[*]',
                            'merror[*]',
                            'mfenced[*]',
                            'mfrac[*]',
                            'mglyph[*]',
                            'mi[*]',
                            'mlabeledtr[*]',
                            'mlongdiv[*]',
                            'mmultiscripts[*]',
                            'mn[*]',
                            'mo[*]',
                            'mover[*]',
                            'mpadded[*]',
                            'mphantom[*]',
                            'mprescripts[*]',
                            'mroot[*]',
                            'mrow[*]',
                            'ms[*]',
                            'mscarries[*]',
                            'mscarry[*]',
                            'msgroup[*]',
                            'msline[*]',
                            'mspace[*]',
                            'msqrt[*]',
                            'msrow[*]',
                            'mstack[*]',
                            'mstyle[*]',
                            'msub[*]',
                            'msubsup[*]',
                            'msup[*]',
                            'mtable[*]',
                            'mtd[*]',
                            'mtext[*]',
                            'mtr[*]',
                            'munder[*]',
                            'munderover[*]',
                            'semantics[*]',
                            'annotation[*]',
                           ];

            editor.settings.extended_valid_elements += ',' + validMathML.join();

            var callbackMethodArguments = {};

            /**
             * Integration model properties
             * @type {Object}
             * @property {Object} target - Integration DOM target.
             * @property {String} configurationService - Configuration integration service.
             * @property {String} version - Plugin version.
             * @property {String} scriptName - Integration script name.
             * @property {Object} environment - Integration environment properties.
             * @property {String} editor - Editor name.
             */
            var integrationModelProperties = {};
            integrationModelProperties.serviceProviderProperties= {};
            integrationModelProperties.serviceProviderProperties.URI = '' + M.cfg.wwwroot + '/filter/wiris/integration/';
            integrationModelProperties.serviceProviderProperties.server = 'php';
            integrationModelProperties.version = '7.12.0.1419';
            integrationModelProperties.isMoodle = true;
            if (typeof(editor.getParam('wiriscontextpath')) !== 'undefined') {
                integrationModelProperties.configurationService = Util.concatenateUrl(editor.getParam('wiriscontextpath'), integrationModelProperties.configurationService);
                editor.getParam('wiriscontextpath') + '/' + integrationModelProperties.configurationService;
                console.warn('Deprecated property wiriscontextpath. Use mathTypeParameters on instead.', editor.opts.wiriscontextpath)
            }

            // Overriding MathType integration parameters.
            if (typeof(editor.getParam('mathTypeParameters')) !== 'undefined') {
                integrationModelProperties.integrationParameters = editor.getParam('mathTypeParameters');
            }

            integrationModelProperties.scriptName = "plugin.min.js";
            integrationModelProperties.environment = {};

            let editorVersion = '4';
            if (tinyMCE.majorVersion === '5') {
                editorVersion = '5';
            }
            integrationModelProperties.environment.editor = 'TinyMCE ' + editorVersion + '.x';

            integrationModelProperties.callbackMethodArguments = callbackMethodArguments;
            integrationModelProperties.editorObject = editor;
            integrationModelProperties.initParsed = false;
            // We need to create the instance before TinyMce initialization in order to register commands.
            // However, as TinyMCE is not initialized at this point the HTML target is not created.
            // Here we create the target as null and onInit object the target is updated.
            integrationModelProperties.target = null;
            var isExternalPlugin = typeof(editor.getParam('external_plugins')) !== 'undefined' && 'tiny_mce_wiris' in editor.getParam('external_plugins');
            integrationModelProperties.isExternal = isExternalPlugin;
            integrationModelProperties.rtl = (editor.getParam('directionality') === 'rtl');


            // GenericIntegration instance.
            var tinyMceIntegrationInstance = new TinyMceIntegration(integrationModelProperties);
            tinyMceIntegrationInstance.init();
            WirisPlugin.instances[tinyMceIntegrationInstance.editorObject.id] = tinyMceIntegrationInstance;
            WirisPlugin.currentInstance = tinyMceIntegrationInstance;

            var onInit = function (editor) {
                var integrationInstance = WirisPlugin.instances[tinyMceIntegrationInstance.editorObject.id];
                if (!editor.inline) {
                    integrationInstance.setTarget(editor.getContentAreaContainer().firstChild);
                } else {
                    integrationInstance.setTarget(editor.getElement());
                }
                integrationInstance.setEditorObject(editor);
                integrationInstance.listeners.fire('onTargetReady', {});
                if ('wiriseditorparameters' in editor.settings) {
                    Configuration.update('editorParameters', editor.settings.wiriseditorparameters);
                }

                // Prevent TinyMCE attributes insertion.
                // TinyMCE insert attributes only when a new node is inserted.
                // For this reason, the mutation observer only acts on addedNodes.
                const mutationInstance = new MutationObserver(function onMutations(editor, mutations) {
                    Array.prototype.forEach.call(mutations,function(editor, mutation) {
                        Array.prototype.forEach.call(mutation.addedNodes,function(editor, node) {
                            if (node.nodeType == 1) {
                                // Act only in our own formulas.
                                Array.prototype.forEach.call(node.querySelectorAll(`.${WirisPlugin.Configuration.get('imageClassName')}`), function(editor, image) {
                                    // This only is executed due to init parse.
                                    image.removeAttribute('data-mce-src');
                                    image.removeAttribute('data-mce-style');
                                    // Be careful. The next line clear the changes queue
                                    // because is only executed these lines on init.
                                    editor.undoManager.clear();
                                }.bind(this, editor));
                            }
                        }.bind(this, editor));
                    }.bind(this, editor));
                }.bind(this, editor));
                mutationInstance.observe(editor.getBody(), {
                    attributes: true,
                    childList: true,
                    characterData: true,
                    subtree: true
                });

                const content = editor.getContent();

                // Bug fix: In Moodle2.x when TinyMCE is set to full screen
                // the content doesn't need to be filtered.
                // if (!editor.getParam('fullscreen_is_enabled') && content !== "") {
                    // We set content in html because other tiny plugins need data-mce
                    // and this is not possible with raw format.
                    editor.setContent(Parser.initParse(content, editor.getParam('language')), {format: "html"});
                    // This clean undoQueue for prevent onChange and Dirty state.
                    editor.undoManager.clear();
                // }

                // Init parsing OK. If a setContent method is called
                // wrs_initParse is called again.
                // Now if source code is edited the returned code is parsed.
                // PLUGINS-1070: We set this variable out of condition to parse content after.
                WirisPlugin.instances[editor.id].initParsed = true;
            }

            if ('onInit' in editor) {
                editor.onInit.add(onInit);
            }
            else {
                editor.on('init', function () {
                    onInit(editor);
                });
            }

            if ('onActivate' in editor) {
                editor.onActivate.add( function (editor) {
                    WirisPlugin.currentInstance = WirisPlugin.instances[tinymce.activeEditor.id];
                });
            }
            else {
                editor.on('focus', function (event) {
                    WirisPlugin.currentInstance = WirisPlugin.instances[tinymce.activeEditor.id];
                });
            }

            var onSave = function (editor, params) {
                params.content = Parser.endParse(params.content, editor.getParam('language'));
            }

            if ('onSaveContent' in editor) {
                editor.onSaveContent.add(onSave);
            }
            else {
                editor.on('saveContent', function (params) {
                    onSave(editor, params);
                });
            }

            if ('onGetContent' in editor) {
                editor.onGetContent.add(onSave);
            } else {
                editor.on('getContent', function(params) {
                    onSave(editor, params);
                });
            }

            if ('onBeforeSetContent' in editor) {
                editor.onBeforeSetContent.add(function(e,params) {
                    if (WirisPlugin.instances[editor.id].initParsed) {
                        params.content = Parser.initParse(params.content, editor.getParam('language'));
                    }
                });
            } else {
                editor.on('beforeSetContent', function(params) {
                    if (WirisPlugin.instances[editor.id].initParsed) {
                        params.content = Parser.initParse(params.content, editor.getParam('language'));
                    }
                });
            }

            function openFormulaEditorFunction() {
                const tinyMceIntegrationInstance = WirisPlugin.instances[editor.id];
                // Disable previous custom editors.
                tinyMceIntegrationInstance.core.getCustomEditors().disable();
                tinyMceIntegrationInstance.openNewFormulaEditor();
            }

            let commonEditor;
            if (tinyMCE.majorVersion === '5') {
                commonEditor = editor.ui.registry;
            }
            else {
                commonEditor = editor;
                commonEditor.addCommand('tiny_mce_wiris_openFormulaEditor',openFormulaEditorFunction);
            }

            const mathTypeIcon = '<svg width="16" height="16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 300 261.7" style="enable-background:new 0 0 300 261.7;" xml:space="preserve"><g id=icon-wirisformula stroke="none" stroke-width="1" fill-rule="evenodd"><g><path class="st1" d="M90.2,257.7c-11.4,0-21.9-6.4-27-16.7l-60-119.9c-7.5-14.9-1.4-33.1,13.5-40.5c14.9-7.5,33.1-1.4,40.5,13.5l27.3,54.7L121.1,39c5.3-15.8,22.4-24.4,38.2-19.1c15.8,5.3,24.4,22.4,19.1,38.2l-59.6,179c-3.9,11.6-14.3,19.7-26.5,20.6C91.6,257.7,90.9,257.7,90.2,257.7"/></g></g><g><g><path class="st2" d="M300,32.8c0-16.4-13.4-29.7-29.9-29.7c-2.9,0-7.2,0.8-7.2,0.8c-37.9,9.1-71.3,14-112,14c-0.3,0-0.6,0-1,0c-16.5,0-29.9,13.3-29.9,29.7c0,16.4,13.4,29.7,29.9,29.7l0,0c45.3,0,83.1-5.3,125.3-15.3h0C289.3,59.5,300,47.4,300,32.8"/></g></g></svg>';

            // MathType button.
            // Cmd Parameter is needed in TinyMCE4 and onAction parameter is needed in TinyMCE5.
            // For more details see TinyMCE migration page: https://www.tiny.cloud/docs-preview/migration-from-4.x/
            commonEditor.addButton('tiny_mce_wiris_formulaEditor', {
                tooltip: 'Insert a math equation - MathType', //TinyMCE3
                title: 'Insert a math equation - MathType',
                cmd: 'tiny_mce_wiris_openFormulaEditor',
                image: WirisPlugin.instances[editor.id].getIconsPath() + 'formula.png',
                onAction: openFormulaEditorFunction,
                icon: mathTypeIcon
            });

            const chemTypeIcon = '<svg width="16" height="16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve">  <image id="image0" width="16" height="16" x="0" y="0"href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfiChwSIwERK9BGAAABfklEQVQoz12RvUtbcRiFn/uRXKNJNCJSokFFqoKDIjHEqKA4WRAc/AsUKl2Ck5ODIKKDm4v+BbqIEr8WQ+hgQSgOElGJgkPTih+3tUabeL3353A1CX3Hcx7OC+dIAIL3CwXEmJU6XBHWuyIVgIjrOcoUblCTrolE/D+gc0QsUJePEtrGh+jajzcg1GQu0W87ldl7zZQAtPP9Dh5lADNm25rx+Xb7ZdWMCAARyHXhVQD8iwAD+qLV61B++U4Hb5pKS9Tx3w05Umr+K3Mlsk6SfY7x9oX7OvDiwlEEoLPLNkdc4yRJG9WkuCoCxBV7JMhg8cgRl7h44m9xwhM3PLyVYnBni3LBX64p4MHGYLSzOV9UWDd8AO608iW+2eP5NyVN4JR/7rRU2T0MzVRmATI197FIInsmTeIErcwTxqcAxHPDFdLHE4cJmPV4AAKZ+XStyrkCMC00K8Qnj+6+kAFKjfE/sw6/zAXf7bFUqmill+5v7es+vzVqlhukOWCLr6/ZMH/PRaOKYwAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxOC0xMC0yOVQwMTozNTowMS0wNzowMOC+eEAAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMTgtMTAtMjlUMDE6MzU6MDEtMDc6MDCR48D8AAAAAElFTkSuQmCC" /></svg>';

            // Dynamic customEditors buttons.
            var customEditors = WirisPlugin.instances[editor.id].getCore().getCustomEditors();
            for (var customEditor in customEditors.editors) {
                if (customEditors.editors[customEditor].confVariable) {
                    var cmd = 'tiny_mce_wiris_openFormulaEditor' + customEditors.editors[customEditor].name;
                    function commandFunction() {
                        customEditors.enable(customEditor);
                        WirisPlugin.instances[editor.id].openNewFormulaEditor();
                    };
                    editor.addCommand(cmd, commandFunction);
                    // Cmd Parameter is needed in TinyMCE4 and onAction parameter is needed in TinyMCE5.
                    // For more details see TinyMCE migration page: https://www.tiny.cloud/docs-preview/migration-from-4.x/
                    commonEditor.addButton('tiny_mce_wiris_formulaEditor' + customEditors.editors[customEditor].name, {
                        title: customEditors.editors[customEditor].tooltip, // TinyMCE3
                        tooltip: customEditors.editors[customEditor].tooltip,
                        onAction: commandFunction,
                        cmd: cmd,
                        icon: chemTypeIcon, // At the moment only chemTypeIcon because of the provisional solution for TinyMCE5.
                        image: WirisPlugin.instances[editor.id].getIconsPath() + customEditors.editors[customEditor].icon
                    });
                }
            }
        },

        // All versions.
        getInfo: function () {
            return {
                longname : 'tiny_mce_wiris',
                author : 'Maths for More',
                authorurl : 'http://www.wiris.com',
                infourl : 'http://www.wiris.com',
                version : '7.12.0.1419'
            };
        }
    });

    tinymce.PluginManager.add('tiny_mce_wiris', tinymce.plugins.tiny_mce_wiris);
})();


