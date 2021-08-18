import Core from './core.src';
import Image from './image';
import Listeners from './listeners';
import Util from './util';
import Configuration from './configuration';
import ServiceProvider from './serviceprovider';

/**
 * @typedef {Object} IntegrationModelProperties
 * @property {string} configurationService - Configuration service path.
 * This parameter is needed to determine all services paths.
 * @property {HTMLElement} integrationModelProperties.target - HTML target.
 * @property {string} integrationModelProperties.scriptName - Integration script name.
 * Usually the name of the integration script.
 * @property {Object} integrationModelProperties.environment - integration environment properties.
 * @property {Object} [integrationModelProperties.callbackMethodArguments] - object containing
 * callback method arguments.
 * @property {string} [integrationModelProperties.version] - integration version number.
 * @property {Object} [integrationModelProperties.editorObject] - object containing
 * the integration editor instance.
 * @property {boolean} [integrationModelProperties.rtl] - true if the editor is in RTL mode.
 * false otherwise.
 * @property {ServiceProviderProperties} [integrationModelProperties.serviceProviderProperties]
 * - The service parameters.
 * @property {Object} [integrationModelProperties.integrationParameters]
 * - Overwritten integration parameters.
 */

export default class IntegrationModel {
  /**
   * @classdesc
   * This class represents an integration model, allowing the integration script to
   * communicate with Core class. Each integration must extend this class.
   * @constructs
   * @param {IntegrationModelProperties} integrationModelProperties
   */
  constructor(integrationModelProperties) {
    /**
     * Language. Needed for accessibility and locales. English by default.
     */
    this.language = 'en';

    /**
     * Service parameters
     * @type {ServiceProviderProperties}
     */
    this.serviceProviderProperties = {};
    if ('serviceProviderProperties' in integrationModelProperties) {
      this.serviceProviderProperties = integrationModelProperties.serviceProviderProperties;
    }

    /**
     * Configuration service path. The integration service is needed by Core class to
     * load all the backend configuration into the frontend and also to create the paths
     * of all services (all services lives in the same route). Mandatory property.
     */
    this.configurationService = '';
    if ('configurationService' in integrationModelProperties) {
      this.serviceProviderProperties.URI = integrationModelProperties.configurationService;
      console.warn('Deprecated property configurationService. Use serviceParameters on instead.',
        [integrationModelProperties.configurationService]);
    }

    /**
     * Plugin version. Needed to stats and caching.
     * @type {string}
     */
    this.version = ('version' in integrationModelProperties ? integrationModelProperties.version : '');

    /**
     * DOM target in which the plugin works. Needed to associate events, insert formulas, etc.
     * Mandatory property.
     */
    this.target = null;
    if ('target' in integrationModelProperties) {
      this.target = integrationModelProperties.target;
    } else {
      throw new Error('IntegrationModel constructor error: target property missed.');
    }

    /**
     * Integration script name. Needed to know the plugin path.
     */
    if ('scriptName' in integrationModelProperties) {
      this.scriptName = integrationModelProperties.scriptName;
    }

    /**
     * Object containing the arguments needed by the callback function.
     */
    this.callbackMethodArguments = {};
    if ('callbackMethodArguments' in integrationModelProperties) {
      this.callbackMethodArguments = integrationModelProperties.callbackMethodArguments;
    }

    /**
     * Contains information about the integration environment:
     * like the name of the editor, the version, etc.
     */
    this.environment = {};
    if ('environment' in integrationModelProperties) {
      this.environment = integrationModelProperties.environment;
    }

    /**
     * Indicates if the DOM target is - or not - and iframe.
     */
    this.isIframe = false;
    if (this.target != null) {
      this.isIframe = (this.target.tagName.toUpperCase() === 'IFRAME');
    }

    /**
     * Instance of the integration editor object. Usually the entry point to access the API
     * of a HTML editor.
     */
    this.editorObject = null;
    if ('editorObject' in integrationModelProperties) {
      this.editorObject = integrationModelProperties.editorObject;
    }

    /**
     * Specifies if the direction of the text is RTL. false by default.
     */
    this.rtl = false;
    if ('rtl' in integrationModelProperties) {
      this.rtl = integrationModelProperties.rtl;
    }

    /**
     * Specifies if the integration model exposes the locale strings. false by default.
     */
    this.managesLanguage = false;
    if ('managesLanguage' in integrationModelProperties) {
      this.managesLanguage = integrationModelProperties.managesLanguage;
    }

    /**
     * Indicates if an image is selected. Needed to resize the image to the original size in case
     * the image is resized.
     * @type {boolean}
     */
    this.temporalImageResizing = false;

    /**
     * The Core class instance associated to the integration model.
     * @type {Core}
     */
    this.core = null;

    /**
     * Integration model listeners.
     * @type {Listeners}
     */
    this.listeners = new Listeners();

    // Parameters overwrite.
    if ('integrationParameters' in integrationModelProperties) {
      IntegrationModel.integrationParameters.forEach((parameter) => {
        if (parameter in integrationModelProperties.integrationParameters) {
          this[parameter] = integrationModelProperties.integrationParameters[parameter];
        }
      });
    }
  }

  /**
   * Init function. Usually called from the integration side once the core.js file is loaded.
   */
  init() {
    this.language = this.getLanguage();
    // We need to wait until Core class is loaded ('onLoad' event) before
    // call the callback method.
    const listener = Listeners.newListener('onLoad', () => {
      this.callbackFunction(this.callbackMethodArguments);
    });

    // Backwards compatibility.
    if (this.serviceProviderProperties.URI.indexOf('configuration') !== -1) {
      const uri = this.serviceProviderProperties.URI;
      const server = ServiceProvider.getServerLanguageFromService(uri);
      this.serviceProviderProperties.server = server;
      const configurationIndex = this.serviceProviderProperties.URI.indexOf('configuration');
      const subsTring = this.serviceProviderProperties.URI.substring(0, configurationIndex);
      this.serviceProviderProperties.URI = subsTring;
    }

    let serviceParametersURI = this.serviceProviderProperties.URI;
    serviceParametersURI = serviceParametersURI.indexOf('/') === 0 || serviceParametersURI.indexOf('http') === 0
      ? serviceParametersURI
      : Util.concatenateUrl(this.getPath(), serviceParametersURI);

    this.serviceProviderProperties.URI = serviceParametersURI;

    const coreProperties = {};
    coreProperties.serviceProviderProperties = this.serviceProviderProperties;
    this.setCore(new Core(coreProperties));
    this.core.addListener(listener);
    this.core.language = this.language;

    // Initializing Core class.
    this.core.init();
    // TODO: Move to Core constructor.
    this.core.setEnvironment(this.environment);
  }

  /**
   * Returns the absolute path of the integration script.
   * @return {string} - Absolute path for the integration script.
   */
  getPath() {
    if (typeof this.scriptName === 'undefined') {
      throw new Error('scriptName property needed for getPath.');
    }
    const col = document.getElementsByTagName('script');
    let path = '';
    for (let i = 0; i < col.length; i += 1) {
      const j = col[i].src.lastIndexOf(this.scriptName);
      if (j >= 0) {
        path = col[i].src.substr(0, j - 1);
      }
    }
    return path;
  }

  /**
   * Sets the language property.
   * @param {string} language - language code.
   */
  setLanguage(language) {
    this.language = language;
  }

  /**
   * Sets a Core instance.
   * @param {Core} core - instance of Core class.
   */
  setCore(core) {
    this.core = core;
    core.setIntegrationModel(this);
  }

  /**
   * Returns the Core instance.
   * @returns {Core} instance of Core class.
   */
  getCore() {
    return this.core;
  }

  /**
   * Sets the object target and updates the iframe property.
   * @param {HTMLElement} target - target object.
   */
  setTarget(target) {
    this.target = target;
    this.isIframe = (this.target.tagName.toUpperCase() === 'IFRAME');
  }

  /**
   * Sets the editor object.
   * @param {Object} editorObject - The editor object.
   */
  setEditorObject(editorObject) {
    this.editorObject = editorObject;
  }

  /**
   * Opens formula editor to editing a new formula. Can be overwritten in order to make some
   * actions from integration part before the formula is edited.
   */
  openNewFormulaEditor() {
    this.core.editionProperties.isNewElement = true;
    this.core.openModalDialog(this.target, this.isIframe);
  }

  /**
   * Opens formula editor to editing an existing formula. Can be overwritten in order to make some
   * actions from integration part before the formula is edited.
   */
  openExistingFormulaEditor() {
    this.core.editionProperties.isNewElement = false;
    this.core.openModalDialog(this.target, this.isIframe);
  }

  /**
   * Wrapper to Core.updateFormula method.
   * Transform a MathML into a image formula.
   * Then the image formula is inserted in the specified target, creating a new image (new formula)
   * or updating an existing one.
   * @param {string} mathml - MathML to generate the formula.
   * @param {string} editMode - Edit Mode (LaTeX or images).
   */
  updateFormula(mathml) {
    let focusElement;
    let windowTarget;
    const wirisProperties = null;

    if (this.isIframe) {
      focusElement = this.target.contentWindow;
      windowTarget = this.target.contentWindow;
    } else {
      focusElement = this.target;
      windowTarget = window;
    }

    let obj = this.core.beforeUpdateFormula(mathml, wirisProperties);

    if (!obj) {
      return '';
    }

    obj = this.insertFormula(focusElement, windowTarget, obj.mathml, obj.wirisProperties);

    if (!obj) {
      return '';
    }

    return this.core.afterUpdateFormula(obj.focusElement, obj.windowTarget, obj.node, obj.latex);
  }

  /**
   * Wrapper to Core.insertFormula method.
   * Inserts the formula in the specified target, creating
   * a new image (new formula) or updating an existing one.
   * @param {string} mathml - MathML to generate the formula.
   * @param {string} editMode - Edit Mode (LaTeX or images).
   */
  insertFormula(focusElement, windowTarget, mathml, wirisProperties) {
    return this.core.insertFormula(focusElement, windowTarget, mathml, wirisProperties);
  }

  /**
   * Returns the target selection.
   * @returns {Selection} target selection.
   */
  getSelection() {
    if (this.isIframe) {
      this.target.contentWindow.focus();
      return this.target.contentWindow.getSelection();
    }
    this.target.focus();
    return window.getSelection();
  }

  /**
   * Add events to formulas in the DOM target. The events added are the following:
   * - doubleClickHandler: handles double click event on formulas by opening an editor
   * to edit them.
   * - mouseDownHandler: handles mouse down event on formulas by saving the size of the formula
   * in case the the formula is resized.
   * - mouseUpHandler: handles mouse up event on formulas by restoring the saved formula size
   * in case the formula is resized.
   */
  addEvents() {
    const eventTarget = this.isIframe ? this.target.contentWindow.document : this.target;
    Util.addElementEvents(
      eventTarget,
      (element, event) => {
        this.doubleClickHandler(element, event);
      },
      (element, event) => {
        this.mousedownHandler(element, event);
      },
      (element, event) => {
        this.mouseupHandler(element, event);
      },
    );
  }

  /**
   * Handles a double click on the target element. Opens an editor
   * to re-edit the double-clicked formula.
   * @param {HTMLElement} element - DOM object target.
   */
  doubleClickHandler(element) {
    if (element.nodeName.toLowerCase() === 'img') {
      this.core.getCustomEditors().disable();
      const customEditorAttributeName = Configuration.get('imageCustomEditorName');
      if (element.hasAttribute(customEditorAttributeName)) {
        const customEditor = element.getAttribute(customEditorAttributeName);
        this.core.getCustomEditors().enable(customEditor);
      }
      if (Util.containsClass(element, Configuration.get('imageClassName'))) {
        this.core.editionProperties.temporalImage = element;
        this.core.editionProperties.isNewElement = true;
        this.openExistingFormulaEditor();
      }
    }
  }

  /**
   * Handles a mouse up event on the target element. Restores the image size to avoid
   * resizing formulas.
   */
  mouseupHandler() {
    if (this.temporalImageResizing) {
      setTimeout(() => {
        Image.fixAfterResize(this.temporalImageResizing);
      }, 10);
    }
  }

  /**
   * Handles a mouse down event on the target element. Saves the formula size to avoid
   * resizing formulas.
   * @param {HTMLElement} element - target element.
   */
  mousedownHandler(element) {
    if (element.nodeName.toLowerCase() === 'img') {
      if (Util.containsClass(element, Configuration.get('imageClassName'))) {
        this.temporalImageResizing = element;
      }
    }
  }

  /**
   * Returns the integration language. By default the browser agent. This method
   * should be overwritten to obtain the integration language, for example using the
   * plugin API of an HTML editor.
   * @returns {string} integration language.
   */
  getLanguage() {
    return this.getBrowserLanguage();
  }

  /**
   * Returns the browser language.
   * @returns {string} the browser language.
   */
  // eslint-disable-next-line class-methods-use-this
  getBrowserLanguage() {
    let language = 'en';
    if (navigator.userLanguage) {
      language = navigator.userLanguage.substring(0, 2);
    } else if (navigator.language) {
      language = navigator.language.substring(0, 2);
    } else {
      language = 'en';
    }
    return language;
  }

  /**
   * This function is called once the {@link Core} is loaded. IntegrationModel class
   * will fire this method when {@link Core} 'onLoad' event is fired.
   * This method should content all the logic to init
   * the integration.
   */
  callbackFunction() {
    // It's needed to wait until the integration target is ready. The event is fired
    // from the integration side.
    const listener = Listeners.newListener('onTargetReady', () => {
      this.addEvents(this.target);
    });
    this.listeners.add(listener);
  }

  /**
   * Function called when the content submits an action.
   */
  // eslint-disable-next-line class-methods-use-this
  notifyWindowClosed() {
    // Nothing.
  }

  /**
   * Wrapper.
   * Extracts mathml of a determined text node. This function is used as a wrapper inside core.js
   * in order to get mathml from a text node that can contain normal LaTeX or other chosen text.
   * @param {string} textNode - text node to extract the MathML.
   * @param {int} caretPosition - caret position inside the text node.
   * @returns {string} MathML inside the text node.
   */

  // eslint-disable-next-line class-methods-use-this, no-unused-vars
  getMathmlFromTextNode(textNode, caretPosition) {}

  /**
   * Wrapper
   * It fills wrs event object of nonLatex with the desired data.
   * @param {Object} event - event object.
   * @param {Object} window dom window object.
   * @param {string} mathml valid mathml.
   */
  // eslint-disable-next-line class-methods-use-this, no-unused-vars
  fillNonLatexNode(event, window, mathml) {}

  /**
    Wrapper.
   * Returns selected item from the target.
   * @param {HTMLElement} target - target element
   * @param {boolean} iframe
   */
  // eslint-disable-next-line class-methods-use-this, no-unused-vars
  getSelectedItem(target, isIframe) {}
}

// To know if the integration that extends this class implements
// wrapper methods, they are set as undefined.
IntegrationModel.prototype.getMathmlFromTextNode = undefined;
IntegrationModel.prototype.fillNonLatexNode = undefined;
IntegrationModel.prototype.getSelectedItem = undefined;

/**
 * An object containing a list with the overwritable class constructor properties.
 * @type {Object}
 */
IntegrationModel.integrationParameters = ['serviceProviderProperties', 'editorParameters'];
