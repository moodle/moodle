import Parser from './parser';
import Util from './util';
import StringManager from './stringmanager';
import ContentManager from './contentmanager';
import Latex from './latex';
import MathML from './mathml';
import CustomEditors from './customeditors';
import Configuration from './configuration';
import jsProperties from './jsvariables';
import Event from './event';
import Listeners from './listeners';
import Image from './image';
import ServiceProvider from './serviceprovider';
import ModalDialog from './modal';
// import { ServiceProviderProperties, ServiceProvider } from './serviceprovider';
import '../styles/styles.css';

/**
 * @typedef {Object} CoreProperties
 * @property {ServiceProviderProperties} serviceProviderProperties
 * - The ServiceProvider class properties. *
 */
export default class Core {
  /**
   * @classdesc
   * This class represents MathType integration Core, managing the following:
   * - Integration initialization.
   * - Event managing.
   * - Insertion of formulas into the edit area.
   * ```js
   *       let core = new Core();
   *       core.addListener(listener);
   *       core.language = 'en';
   *
   *       // Initializing Core class.
   *       core.init(configurationService);
   * ```
   * @constructs
   * Core constructor.
   * @param {CoreProperties}
   */
  constructor(coreProperties) {
    /**
     * Language. Needed for accessibility and locales. 'en' by default.
     * @type {String}
     */
    this.language = 'en';

    /**
     * Edit mode, 'images' by default. Admits the following values:
     * - images
     * - latex
     * @type {String}
     */
    this.editMode = 'images';

    /**
     * Modal dialog instance.
     * @type {ModalDialog}
     */
    this.modalDialog = null;

    /**
     * The instance of {@link CustomEditors}. By default
     * the only custom editor is the Chemistry editor.
     * @type {CustomEditors}
     */
    this.customEditors = new CustomEditors();

    /**
     * Chemistry editor.
     * @type {CustomEditor}
     */
    const chemEditorParams = {
      name: 'Chemistry',
      toolbar: 'chemistry',
      icon: 'chem.png',
      confVariable: 'chemEnabled',
      title: 'ChemType',
      tooltip: 'Insert a chemistry formula - ChemType', // TODO: Localize tooltip.
    };

    this.customEditors.addEditor('chemistry', chemEditorParams);

    /**
     * Environment properties. This object contains data about the integration platform.
     * @typedef IntegrationEnvironment
     * @property {String} IntegrationEnvironment.editor - Editor name. For example the HTML editor.
     * @property {String} IntegrationEnvironment.mode - Integration save mode.
     * @property {String} IntegrationEnvironment.version - Integration version.
     *
     */

    /**
     * The environment properties object.
     * @type {IntegrationEnvironment}
     */
    this.environment = {};

    /**
     * @typedef EditionProperties
     * @property {Boolean} editionProperties.isNewElement - True if the formula is a new one.
     * False otherwise.
     * @property {HTMLImageElement} editionProperties.temporalImage- The image element.
     * Null if the formula is new.
     * @property {Range} editionProperties.latexRange - Tha range that contains the LaTeX formula.
     * @property {Range} editionProperties.range - The range that contains the image element.
     * @property {String} editionProperties.editMode - The edition mode. 'images' by default.
     */

    /**
     * The properties of the current edition process.
     * @type {EditionProperties}
     */
    this.editionProperties = {};

    this.editionProperties.isNewElement = true;
    this.editionProperties.temporalImage = null;
    this.editionProperties.latexRange = null;
    this.editionProperties.range = null;

    /**
     * The {@link IntegrationModel} instance.
     * @type {IntegrationModel}
     */
    this.integrationModel = null;

    /**
     * The {@link ContentManager} instance.
     * @type {ContentManager}
     */
    this.contentManager = null;

    /**
     * The current browser.
     * @type {String}
     */
    this.browser = (() => {
      const ua = navigator.userAgent;
      let browser = 'none';
      if (ua.search('Edge/') >= 0) {
        browser = 'EDGE';
      } else if (ua.search('Chrome/') >= 0) {
        browser = 'CHROME';
      } else if (ua.search('Trident/') >= 0) {
        browser = 'IE';
      } else if (ua.search('Firefox/') >= 0) {
        browser = 'FIREFOX';
      } else if (ua.search('Safari/') >= 0) {
        browser = 'SAFARI';
      }
      return browser;
    }
    )();

    /**
     * Plugin listeners.
     * @type {Array.<Object>}
     */
    this.listeners = new Listeners();

    /**
     * Service provider properties.
     * @type {ServiceProviderProperties}
     */
    this.serviceProviderProperties = {};
    if ('serviceProviderProperties' in coreProperties) {
      this.serviceProviderProperties = coreProperties.serviceProviderProperties;
    } else {
      throw new Error('serviceProviderProperties property missing.');
    }
  }

  /**
   * Static property.
   * Core listeners.
   * @private
   * @type {Listeners}
   */
  static get globalListeners() {
    return Core._globalListeners;
  }

  /**
   * Static property setter.
   * Set core listeners.
   * @param {Listeners} value - The property value.
   * @ignore
   */
  static set globalListeners(value) {
    Core._globalListeners = value;
  }

  /**
   * Core state. Says if it was loaded previously.
   * True when Core.init was called. Otherwise, false.
   * @private
   * @type {Boolean}
   */
  static get initialized() {
    return Core._initialized;
  }

  /**
   * Core state. Says if it was loaded previously.
   * @param {Boolean} value - True to say that Core.init was called. Otherwise, false.
   * @ignore
   */
  static set initialized(value) {
    Core._initialized = value;
  }

  /**
   * Sets the {@link Core.integrationModel} property.
   * @param {IntegrationModel} integrationModel - The {@link IntegrationModel} property.
   */
  setIntegrationModel(integrationModel) {
    this.integrationModel = integrationModel;
  }

  /**
   * Sets the {@link Core.environment} property.
   * @param {IntegrationEnvironment} integrationEnvironment -
   * The {@link IntegrationEnvironment} object.
   */
  setEnvironment(integrationEnvironment) {
    if ('editor' in integrationEnvironment) {
      this.environment.editor = integrationEnvironment.editor;
    }
    if ('mode' in integrationEnvironment) {
      this.environment.mode = integrationEnvironment.mode;
    }
    if ('version' in integrationEnvironment) {
      this.environment.version = integrationEnvironment.version;
    }
  }

  /**
   * Returns the current {@link ModalDialog} instance.
   * @returns {ModalDialog} The current {@link ModalDialog} instance.
   */
  getModalDialog() {
    return this.modalDialog;
  }

  /**
   * Inits the {@link Core} class, doing the following:
   * - Calls asynchronously configuration service, retrieving the backend configuration in a JSON.
   * - Updates {@link Configuration} class with the previous configuration properties.
   * - Updates the {@link ServiceProvider} class using the configuration service path as reference.
   * - Loads language strings.
   * - Fires onLoad event.
   * @param {Object} serviceParameters - Service parameters.
   */
  init() {
    if (!Core.initialized) {
      const serviceProviderListener = Listeners.newListener('onInit', () => {
        const jsConfiguration = ServiceProvider.getService('configurationjs', '', 'get');
        const jsonConfiguration = JSON.parse(jsConfiguration);
        Configuration.addConfiguration(jsonConfiguration);
        // Adding JavaScript (not backend) configuration variables.
        Configuration.addConfiguration(jsProperties);
        // Fire 'onLoad' event:
        // All integration must listen this event in order to know if the plugin
        // has been properly loaded.
        StringManager.language = this.language;
        this.listeners.fire('onLoad', {});
      });

      ServiceProvider.addListener(serviceProviderListener);
      ServiceProvider.init(this.serviceProviderProperties);

      Core.initialized = true;
    } else {
      // Case when there are more than two editor instances.
      // After the first editor all the other editors don't need to load any file or service.
      this.listeners.fire('onLoad', {});
    }
  }

  /**
   * Adds a {@link Listener} to the current instance of the {@link Core} class.
   * @param {Listener} listener - The listener object.
   */
  addListener(listener) {
    this.listeners.add(listener);
  }

  /**
   * Adds the global {@link Listener} instance to {@link Core} class.
   * @param {Listener} listener - The event listener to be added.
   * @static
   */
  static addGlobalListener(listener) {
    Core.globalListeners.add(listener);
  }

  beforeUpdateFormula(mathml, wirisProperties) {
    /**
     * This event is fired before updating the formula.
     * @type {Object}
     * @property {String} mathml - MathML to be transformed.
     * @property {String} editMode - Edit mode.
     * @property {Object} wirisProperties - Extra attributes for the formula.
     * @property {String} language - Formula language.
     */
    const beforeUpdateEvent = new Event();

    beforeUpdateEvent.mathml = mathml;

    // Cloning wirisProperties object
    // We don't want wirisProperties object modified.
    beforeUpdateEvent.wirisProperties = {};

    if (wirisProperties != null) {
      Object.keys(wirisProperties).forEach((attr) => {
        beforeUpdateEvent.wirisProperties[attr] = wirisProperties[attr];
      });
    }


    // Read only.
    beforeUpdateEvent.language = this.language;
    beforeUpdateEvent.editMode = this.editMode;

    if (this.listeners.fire('onBeforeFormulaInsertion', beforeUpdateEvent)) {
      return {};
    }

    if (Core.globalListeners.fire('onBeforeFormulaInsertion', beforeUpdateEvent)) {
      return {};
    }

    return {
      mathml: beforeUpdateEvent.mathml,
      wirisProperties: beforeUpdateEvent.wirisProperties,
    };
  }

  /**
   * Converts a MathML into it's correspondent image and inserts the image is
   * inserted in a HTMLElement target by creating
   * a new image or updating an existing one.
   * @param {HTMLElement} focusElement - The HTMLElement to be focused after the insertion.
   * @param {Window} windowTarget - The window element where the editable content is.
   * @param {String} mathml - The MathML.
   * @param {Array.<Object>} wirisProperties - The extra attributes for the formula.
   */
  insertFormula(focusElement, windowTarget, mathml, wirisProperties) {
    const returnObject = {};

    if (!mathml) {
      this.insertElementOnSelection(null, focusElement, windowTarget);
    } else if (this.editMode === 'latex') {
      returnObject.latex = Latex.getLatexFromMathML(mathml);
      // this.integrationModel.getNonLatexNode is an integration wrapper
      // to have special behaviours for nonLatex.
      // Not all the integrations have special behaviours for nonLatex.
      if (!!this.integrationModel.fillNonLatexNode && !returnObject.latex) {
        const afterUpdateEvent = new Event();
        afterUpdateEvent.editMode = this.editMode;
        afterUpdateEvent.windowTarget = windowTarget;
        afterUpdateEvent.focusElement = focusElement;
        afterUpdateEvent.latex = returnObject.latex;
        this.integrationModel.fillNonLatexNode(afterUpdateEvent, windowTarget, mathml);
      } else {
        returnObject.node = windowTarget.document.createTextNode(`$$${returnObject.latex}$$`);
      }
      this.insertElementOnSelection(returnObject.node, focusElement, windowTarget);
    } else {
      returnObject.node = Parser.mathmlToImgObject(windowTarget.document,
        mathml,
        wirisProperties, this.language);

      this.insertElementOnSelection(returnObject.node, focusElement, windowTarget);
    }

    return returnObject;
  }

  afterUpdateFormula(focusElement, windowTarget, node, latex) {
    /**
     * This event is fired after update the formula.
     * @type {Event}
     * @param {String} editMode - edit mode.
     * @param {Object} windowTarget - target window.
     * @param {Object} focusElement - target element to be focused after update.
     * @param {String} latex - LaTeX generated by the formula (editMode=latex).
     * @param {Object} node - node generated after update the formula (text if LaTeX img otherwise).
     */
    const afterUpdateEvent = new Event();
    afterUpdateEvent.editMode = this.editMode;
    afterUpdateEvent.windowTarget = windowTarget;
    afterUpdateEvent.focusElement = focusElement;
    afterUpdateEvent.node = node;
    afterUpdateEvent.latex = latex;

    if (this.listeners.fire('onAfterFormulaInsertion', afterUpdateEvent)) {
      return {};
    }

    if (Core.globalListeners.fire('onAfterFormulaInsertion', afterUpdateEvent)) {
      return {};
    }

    return {};
  }

  /**
   * Sets the caret after a given Node and set the focus to the owner document.
   * @param {Node} node - The Node element.
   */
  placeCaretAfterNode(node) {
    this.integrationModel.getSelection();
    const nodeDocument = node.ownerDocument;
    if (typeof nodeDocument.getSelection !== 'undefined' && !!node.parentElement) {
      const range = nodeDocument.createRange();
      range.setStartAfter(node);
      range.collapse(true);
      const selection = nodeDocument.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
      nodeDocument.body.focus();
    }
  }

  /**
   * Replaces a Selection object with an HTMLElement.
   * @param {HTMLElement} element - The HTMLElement to replace the selection.
   * @param {HTMLElement} focusElement - The HTMLElement to be focused after the replace.
   * @param {Window} windowTarget - The window target.
   */
  insertElementOnSelection(element, focusElement, windowTarget) {
    if (this.editionProperties.isNewElement) {
      if (element) {
        if (focusElement.type === 'textarea') {
          Util.updateTextArea(focusElement, element.textContent);
        } else if (document.selection && document.getSelection === 0) {
          let range = windowTarget.document.selection.createRange();
          windowTarget.document.execCommand('InsertImage', false, element.src);

          if (!('parentElement' in range)) {
            windowTarget.document.execCommand('delete', false);
            range = windowTarget.document.selection.createRange();
            windowTarget.document.execCommand('InsertImage', false, element.src);
          }

          if ('parentElement' in range) {
            const temporalObject = range.parentElement();

            if (temporalObject.nodeName.toUpperCase() === 'IMG') {
              temporalObject.parentNode.replaceChild(element, temporalObject);
            } else {
              // IE9 fix: parentNode() does not return the IMG node,
              // returns the parent DIV node. In IE < 9, pasteHTML does not work well.
              range.pasteHTML(Util.createObjectCode(element));
            }
          }
        } else {
          const editorSelection = this.integrationModel.getSelection();
          let range = null;
          // In IE is needed keep the range due to after focus the modal window
          // it can't be retrieved the last selection.
          if (this.editionProperties.range) {
            ({ range } = this.editionProperties);
            this.editionProperties.range = null;
          } else {
            range = editorSelection.getRangeAt(0);
          }

          // Delete if something was surrounded.
          range.deleteContents();

          let node = range.startContainer;
          const position = range.startOffset;

          if (node.nodeType === 3) { // TEXT_NODE.
            node = node.splitText(position);
            node.parentNode.insertBefore(element, node);
          } else if (node.nodeType === 1) { // ELEMENT_NODE.
            node.insertBefore(element, node.childNodes[position]);
          }

          this.placeCaretAfterNode(element);
        }
      } else if (focusElement.type === 'textarea') {
        focusElement.focus();
      } else {
        const editorSelection = this.integrationModel.getSelection();
        editorSelection.removeAllRanges();

        if (this.editionProperties.range) {
          const { range } = this.editionProperties;
          this.editionProperties.range = null;
          editorSelection.addRange(range);
        }
      }
    } else if (this.editionProperties.latexRange) {
      if (document.selection && document.getSelection === 0) {
        this.editionProperties.isNewElement = true;
        this.editionProperties.latexRange.select();
        this.insertElementOnSelection(element, focusElement, windowTarget);
      } else {
        this.editionProperties.latexRange.deleteContents();
        this.editionProperties.latexRange.insertNode(element);
        this.placeCaretAfterNode(element);
      }
    } else if (focusElement.type === 'textarea') {
      let item;
      // Wrapper for some integrations that can have special behaviours to show latex.
      if (typeof this.integrationModel.getSelectedItem !== 'undefined') {
        item = this.integrationModel.getSelectedItem(focusElement, false);
      } else {
        item = Util.getSelectedItemOnTextarea(focusElement);
      }
      Util.updateExistingTextOnTextarea(focusElement,
        element.textContent,
        item.startPosition,
        item.endPosition);
    } else {
      if (element && element.nodeName.toLowerCase() === 'img') { // Editor empty, formula has been erased on edit.
        // Clone is needed to maintain event references to temporalImage.
        Image.clone(element, this.editionProperties.temporalImage);
      } else {
        this.editionProperties.temporalImage.remove();
      }
      this.placeCaretAfterNode(this.editionProperties.temporalImage);
    }
  }


  /**
   * Opens a modal dialog containing MathType editor..
   * @param {HTMLElement} target - The target HTMLElement where formulas should be inserted.
   * @param {Boolean} isIframe - True if the target HTMLElement is an iframe. False otherwise.
   */
  openModalDialog(target, isIframe) {
    // Textarea elements don't have normal document ranges. It only accepts latex edit.
    this.editMode = 'images';

    // In IE is needed keep the range due to after focus the modal window
    // it can't be retrieved the last selection.
    try {
      if (isIframe) {
        // Is needed focus the target first.
        target.contentWindow.focus();
        const selection = target.contentWindow.getSelection();
        this.editionProperties.range = selection.getRangeAt(0);
      } else {
        // Is needed focus the target first.
        target.focus();
        const selection = getSelection();
        this.editionProperties.range = selection.getRangeAt(0);
      }
    } catch (e) {
      this.editionProperties.range = null;
    }

    if (isIframe === undefined) {
      isIframe = true;
    }

    this.editionProperties.latexRange = null;

    if (target) {
      let selectedItem;
      if (typeof this.integrationModel.getSelectedItem !== 'undefined') {
        selectedItem = this.integrationModel.getSelectedItem(target, isIframe);
      } else {
        selectedItem = Util.getSelectedItem(target, isIframe);
      }

      // Check LaTeX if and only if the node is a text node (nodeType==3).
      if (selectedItem) {
        // Case when image was selected and button pressed.
        if (!selectedItem.caretPosition && Util.containsClass(selectedItem.node, Configuration.get('imageClassName'))) {
          this.editionProperties.temporalImage = selectedItem.node;
          this.editionProperties.isNewElement = false;
        } else if (selectedItem.node.nodeType === 3) {
          // If it's a text node means that editor is working with LaTeX.
          if (this.integrationModel.getMathmlFromTextNode) {
            // If integration has this function it isn't set range due to we don't
            // know if it will be put into a textarea as a text or image.
            const mathml = this.integrationModel.getMathmlFromTextNode(
              selectedItem.node,
              selectedItem.caretPosition,
            );
            if (mathml) {
              this.editMode = 'latex';
              this.editionProperties.isNewElement = false;
              this.editionProperties.temporalImage = document.createElement('img');
              this.editionProperties.temporalImage.setAttribute(
                Configuration.get('imageMathmlAttribute'),
                MathML.safeXmlEncode(mathml),
              );
            }
          } else {
            const latexResult = Latex.getLatexFromTextNode(
              selectedItem.node,
              selectedItem.caretPosition,
            );
            if (latexResult) {
              const mathml = Latex.getMathMLFromLatex(latexResult.latex);
              this.editMode = 'latex';
              this.editionProperties.isNewElement = false;
              this.editionProperties.temporalImage = document.createElement('img');
              this.editionProperties.temporalImage.setAttribute(
                Configuration.get('imageMathmlAttribute'),
                MathML.safeXmlEncode(mathml),
              );
              const windowTarget = isIframe ? target.contentWindow : window;

              if (target.tagName.toLowerCase() !== 'textarea') {
                if (document.selection) {
                  let leftOffset = 0;
                  let previousNode = latexResult.startNode.previousSibling;

                  while (previousNode) {
                    leftOffset += Util.getNodeLength(previousNode);
                    previousNode = previousNode.previousSibling;
                  }

                  this.editionProperties.latexRange = windowTarget.document.selection.createRange();
                  this.editionProperties.latexRange.moveToElementText(
                    latexResult.startNode.parentNode,
                  );
                  this.editionProperties.latexRange.move(
                    'character',
                    leftOffset + latexResult.startPosition,
                  );
                  this.editionProperties.latexRange.moveEnd(
                    'character',
                    latexResult.latex.length + 4,
                  ); // Plus 4 for the '$$' characters.
                } else {
                  this.editionProperties.latexRange = windowTarget.document.createRange();
                  this.editionProperties.latexRange.setStart(
                    latexResult.startNode,
                    latexResult.startPosition,
                  );
                  this.editionProperties.latexRange.setEnd(
                    latexResult.endNode,
                    latexResult.endPosition,
                  );
                }
              }
            }
          }
        }
      } else if (target.tagName.toLowerCase() === 'textarea') {
        // By default editMode is 'images', but when target is a textarea it needs to be 'latex'.
        this.editMode = 'latex';
      }
    }

    // Setting an object with the editor parameters.
    // Editor parameters can be customized in several ways:
    // 1 - editorAttributes: Contains the default editor attributes,
    //  usually the metrics in a comma separated string. Always exists.
    // 2 - editorParameters: Object containing custom editor parameters.
    // These parameters are defined in the backend. So they affects all integration instances.

    // The backend send the default editor attributes in a coma separated
    // with the following structure: key1=value1,key2=value2...
    const defaultEditorAttributesArray = Configuration.get('editorAttributes').split(', ');
    const defaultEditorAttributes = {};
    for (let i = 0, len = defaultEditorAttributesArray.length; i < len; i += 1) {
      const tempAttribute = defaultEditorAttributesArray[i].split('=');
      const key = tempAttribute[0];
      const value = tempAttribute[1];
      defaultEditorAttributes[key] = value;
    }
    // Custom editor parameters.
    const editorAttributes = {};
    Object.assign(editorAttributes, defaultEditorAttributes, Configuration.get('editorParameters'));
    editorAttributes.language = this.language;
    editorAttributes.rtl = this.integrationModel.rtl;

    const contentManagerAttributes = {};
    contentManagerAttributes.editorAttributes = editorAttributes;
    contentManagerAttributes.language = this.language;
    contentManagerAttributes.customEditors = this.customEditors;
    contentManagerAttributes.environment = this.environment;

    if (this.modalDialog == null) {
      this.modalDialog = new ModalDialog(editorAttributes);
      this.contentManager = new ContentManager(contentManagerAttributes);
      // When an instance of ContentManager is created we need to wait until
      // the ContentManager is ready by listening 'onLoad' event.
      const listener = Listeners.newListener('onLoad', () => {
        this.contentManager.isNewElement = this.editionProperties.isNewElement;
        if (this.editionProperties.temporalImage != null) {
          const mathML = MathML.safeXmlDecode(this.editionProperties.temporalImage.getAttribute(Configuration.get('imageMathmlAttribute')));
          this.contentManager.mathML = mathML;
        }
      });
      this.contentManager.addListener(listener);
      this.contentManager.init();
      this.modalDialog.setContentManager(this.contentManager);
      this.contentManager.setModalDialogInstance(this.modalDialog);
    } else {
      this.contentManager.isNewElement = this.editionProperties.isNewElement;
      if (this.editionProperties.temporalImage != null) {
        const mathML = MathML.safeXmlDecode(this.editionProperties.temporalImage.getAttribute(Configuration.get('imageMathmlAttribute')));
        this.contentManager.mathML = mathML;
      }
    }
    this.contentManager.setIntegrationModel(this.integrationModel);
    this.modalDialog.open();
  }

  /**
   * Returns the {@link CustomEditors} instance.
   * @return {CustomEditors} The current {@link CustomEditors} instance.
   */
  getCustomEditors() {
    return this.customEditors;
  }
}

/**
 * Core static listeners.
 * @type {Listeners}
 * @private
 */
Core._globalListeners = new Listeners();

/**
 * Resources state. Says if they were loaded or not.
 * @type {Boolean}
 * @private
 */
Core._initialized = false;
