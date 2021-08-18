import Configuration from './configuration';
import EditorListener from './editorlistener';
import Listeners from './listeners';
import MathML from './mathml';
import Util from './util';

export default class ContentManager {
  /**
   * @classdesc
   * This class represents a modal dialog, managing the following:
   * - The insertion of content into the current instance of the {@link ModalDialog} class.
   * - The actions to be done once the modal object has been submitted
   *   (submitAction} method).
   * - The update of the content when the {@link ModalDialog} class is also updated,
   *   for example when ModalDialog is re-opened.
   * - The communication between the {@link ModalDialog} class and itself, if the content
   *   has been changed (hasChanges} method).
   * @constructs
   * @param {Object} contentManagerAttributes - Object containing all attributes needed to
   * create a new instance.
   */
  constructor(contentManagerAttributes) {
    /**
     * An object containing MathType editor parameters. See
     * http://docs.wiris.com/en/mathtype/mathtype_web/sdk-api/parameters for further information.
     * @type {Object}
     */
    this.editorAttributes = {};
    if ('editorAttributes' in contentManagerAttributes) {
      this.editorAttributes = contentManagerAttributes.editorAttributes;
    } else {
      throw new Error('ContentManager constructor error: editorAttributes property missed.');
    }

    /**
     * CustomEditors instance. Contains the custom editors.
     * @type {CustomEditors}
     */
    this.customEditors = null;
    if ('customEditors' in contentManagerAttributes) {
      this.customEditors = contentManagerAttributes.customEditors;
    }

    /**
* Environment properties. This object contains data about the integration platform.
* @type {Object}
* @property {String} editor - Editor name. Usually the HTML editor.
* @property {String} mode - Save mode. Xml by default.
* @property {String} version - Plugin version.
  */
    this.environment = {};
    if ('environment' in contentManagerAttributes) {
      this.environment = contentManagerAttributes.environment;
    } else {
      throw new Error('ContentManager constructor error: environment property missed');
    }

    /**
      * ContentManager language.
      * @type {String}
      */
    this.language = '';
    if ('language' in contentManagerAttributes) {
      this.language = contentManagerAttributes.language;
    } else {
      throw new Error('ContentManager constructor error: language property missed');
    }

    /**
    * {@link EditorListener} instance. Manages the changes inside the editor.
    * @type {EditorListener}
    */
    this.editorListener = new EditorListener();

    /**
     * MathType editor instance.
     * @type {JsEditor}
     */
    this.editor = null;

    /**
     * Navigator user agent.
     * @type {String}
     */
    this.ua = navigator.userAgent.toLowerCase();

    /**
     * Mobile device properties object
     * @type {DeviceProperties}
     */
    this.deviceProperties = {};
    this.deviceProperties.isAndroid = this.ua.indexOf('android') > -1;
    this.deviceProperties.isIOS = ((this.ua.indexOf('ipad') > -1) || (this.ua.indexOf('iphone') > -1));

    /**
     * Custom editor toolbar.
     * @type {String}
     */
    this.toolbar = null;

    /**
     * Instance of the {@link ModalDialog} class associated with the current
     * {@link ContentManager} instance.
     * @type {ModalDialog}
     */
    this.modalDialogInstance = null;

    /**
     * ContentManager listeners.
     * @type {Listeners}
     */
    this.listeners = new Listeners();

    /**
     * MathML associated to the ContentManager instance.
     * @type {String}
     */
    this.mathML = null;

    /**
     * Indicates if the edited element is a new one or not.
     * @type {Boolean}
     */
    this.isNewElement = true;

    /**
     * {@link IntegrationModel} instance. Needed to call wrapper methods.
     * @type {IntegrationModel}
     */
    this.integrationModel = null;

    /**
     * Indicates if the editor is loaded.
     * @type {Boolean}
     */
    this.isEditorLoaded = false;
  }

  /**
   * Adds a new listener to the current {@link ContentManager} instance.
   * @param {Object} listener - The listener to be added.
   */
  addListener(listener) {
    this.listeners.add(listener);
  }

  /**
   * Sets an instance of {@link IntegrationModel} class to the current {@link ContentManager}
   * instance.
   * @param {IntegrationModel} integrationModel - The {@link IntegrationModel} instance.
   */
  setIntegrationModel(integrationModel) {
    this.integrationModel = integrationModel;
  }

  /**
   * Sets the {@link ModalDialog} instance into the current {@link ContentManager} instance.
   * @param {ModalDialog} modalDialogInstance - The {@link ModalDialog} instance
   */
  setModalDialogInstance(modalDialogInstance) {
    this.modalDialogInstance = modalDialogInstance;
  }

  /**
   * Inserts the content into the current {@link ModalDialog} instance updating
   * the title and inserting the JavaScript editor.
   */
  insert() {
    // Before insert the editor we update the modal object title to avoid weird render display.
    this.updateTitle(this.modalDialogInstance);
    this.insertEditor(this.modalDialogInstance);
  }

  /**
   * Inserts MathType editor into the {@link ModalDialog.contentContainer}. It waits until
   * editor's JavaScript is loaded.
   */
  insertEditor() {
    // To know if editor JavaScript is loaded we need to wait until
    // window.com.wiris.jsEditor.JsEditor.newInstance is ready.
    if (window.com && window.com.wiris && window.com.wiris.jsEditor
        && window.com.wiris.jsEditor.JsEditor && window.com.wiris.jsEditor.JsEditor.newInstance) {
      this.editor = window.com.wiris.jsEditor.JsEditor.newInstance(this.editorAttributes);
      this.editor.insertInto(this.modalDialogInstance.contentContainer);
      this.editor.focus();
      if (this.modalDialogInstance.rtl) {
        this.editor.action('rtl');
      }
      // Setting div in rtl in case of it's activated.
      if (this.editor.getEditorModel().isRTL()) {
        this.editor.element.style.direction = 'rtl';
      }

      // Editor listener: this object manages the changes logic of editor.
      this.editor.getEditorModel().addEditorListener(this.editorListener);

      // iOS events.
      if (this.modalDialogInstance.deviceProperties.isIOS) {
        setTimeout(function hide() { this.modalDialogInstance.hideKeyboard(); }, 400);
        const formulaDisplayDiv = document.getElementsByClassName('wrs_formulaDisplay')[0];
        Util.addEvent(formulaDisplayDiv, 'focus', this.modalDialogInstance.handleOpenedIosSoftkeyboard);
        Util.addEvent(formulaDisplayDiv, 'blur', this.modalDialogInstance.handleClosedIosSoftkeyboard);
      }
      // Fire onLoad event. Necessary to set the MathML into the editor
      // after is loaded.
      this.listeners.fire('onLoad', {});
      this.isEditorLoaded = true;
    } else {
      setTimeout(ContentManager.prototype.insertEditor.bind(this), 100);
    }
  }

  /**
   * Initializes the current class by loading MathType script.
   */
  init() {
    const script = document.createElement('script');
    script.type = 'text/javascript';
    let editorUrl = Configuration.get('editorUrl');
    // We create an object url for parse url string and work more efficiently.
    const urlObject = document.createElement('a');
    urlObject.href = editorUrl;

    // Change to https if necessary.
    if (window.location.href.indexOf('https://') === 0) {
      // It check if browser is https and configuration is http.
      // If this is so, we will replace protocol.
      if (urlObject.protocol === 'http:') {
        urlObject.protocol = 'https:';
      }
    }

    // Check protocol and remove port if it's standard.
    if (urlObject.port === '80' || urlObject.port === '443') {
      editorUrl = `${urlObject.protocol}//${urlObject.hostname}/${urlObject.pathname}`;
    } else {
      editorUrl = `${urlObject.protocol}//${urlObject.hostname}:${urlObject.port}/${urlObject.pathname}`;
    }

    // Editor stats. Use environment property to set it.
    const stats = {};
    if ('editor' in this.environment) {
      stats.editor = this.environment.editor;
    } else {
      stats.editor = 'unknown';
    }

    if ('mode' in this.environment) {
      stats.mode = this.environment.mode;
    } else {
      stats.mode = Configuration.get('saveMode');
    }

    if ('version' in this.environment) {
      stats.version = this.environment.version;
    } else {
      stats.version = Configuration.get('version');
    }

    // Load editor URL. We add stats as GET params.
    script.src = `${editorUrl}?lang=${this.language}&stats-editor=${stats.editor}&stats-mode=${stats.mode}&stats-version=${stats.version}`;

    document.getElementsByTagName('head')[0].appendChild(script);
  }

  /**
  * Sets the {@link ContentManager.editor} initial content.
  */
  setInitialContent() {
    if (!this.isNewElement) {
      this.setMathML(this.mathML);
    }
  }

  /**
   * Sets a MathML into {@link ContentManager.editor} instance.
   * @param {String} mathml - MathML string.
   * @param {Boolean} focusDisabled - If true editor don't get focus after the MathML is set.
   * False by default.
   */
  setMathML(mathml, focusDisabled) {
    // By default focus is enabled.
    if (typeof focusDisabled === 'undefined') {
      focusDisabled = false;
    }
    // Using setMathML method is not a change produced by the user but for the API
    // so we set to false the contentChange property of editorListener.
    this.editor.setMathMLWithCallback(mathml, () => {
      this.editorListener.setWaitingForChanges(true);
    });

    // We need to wait a little until the callback finish.
    setTimeout(() => {
      this.editorListener.setIsContentChanged(false);
    }, 500);

    // In some scenarios - like closing modal object - editor mustn't be focused.
    if (!focusDisabled) {
      this.onFocus();
    }
  }

  /**
   * Sets the focus to the current instance of {@link ContentManager.editor}. Triggered by
   * {@link ModalDialog.focus}.
   */
  onFocus() {
    if (typeof this.editor !== 'undefined' && this.editor != null) {
      this.editor.focus();
    }
  }

  /**
   * Updates the edition area by calling {@link IntegrationModel.updateFormula}.
   * Triggered by {@link ModalDialog.submitAction}.
   */
  submitAction() {
    if (!this.editor.isFormulaEmpty()) {
      let mathML = this.editor.getMathMLWithSemantics();
      // Add class for custom editors.
      if (this.customEditors.getActiveEditor() !== null) {
        const { toolbar } = this.customEditors.getActiveEditor();
        mathML = MathML.addCustomEditorClassAttribute(mathML, toolbar);
      } else {
        // We need - if exists - the editor name from MathML
        // class attribute.
        Object.keys(this.customEditors.editors).forEach((key) => {
          mathML = MathML.removeCustomEditorClassAttribute(mathML, key);
        });
      }
      const mathmlEntitiesEncoded = MathML.mathMLEntities(mathML);
      this.integrationModel.updateFormula(mathmlEntitiesEncoded);
    } else {
      this.integrationModel.updateFormula(null);
    }

    this.customEditors.disable();
    this.integrationModel.notifyWindowClosed();

    // Set disabled focus to prevent lost focus.
    this.setEmptyMathML();
    this.customEditors.disable();
  }

  /**
   * Sets an empty MathML as {@link ContentManager.editor} content.
   */
  setEmptyMathML() {
    // As second argument we pass.
    if (this.deviceProperties.isAndroid || this.deviceProperties.isIOS) {
      // We need to set a empty annotation in order to maintain editor in Hand mode.
      // Adding dir rtl in case of it's activated.
      if (this.editor.getEditorModel().isRTL()) {
        this.setMathML('<math dir="rtl"><semantics><annotation encoding="application/json">[]</annotation></semantics></math>', true);
      } else {
        this.setMathML('<math><semantics><annotation encoding="application/json">[]</annotation></semantics></math>', true);
      }
    } else if (this.editor.getEditorModel().isRTL()) {
      this.setMathML('<math dir="rtl"/>', true);
    } else {
      this.setMathML('<math/>', true);
    }
  }

  /**
   * Open event. Triggered by {@link ModalDialog.open}. Does the following:
   * - Updates the {@link ContentManager.editor} content
   *   (with an empty MathML or an existing formula),
   * - Updates the {@link ContentManager.editor} toolbar.
   * - Recovers the the focus.
   */
  onOpen() {
    if (this.isNewElement) {
      this.setEmptyMathML();
    } else {
      this.setMathML(this.mathML);
    }
    this.updateToolbar();
    this.onFocus();
  }

  /**
   * Sets the correct toolbar depending if exist other custom toolbars
   * at the same time (e.g: Chemistry).
   */
  updateToolbar() {
    this.updateTitle(this.modalDialogInstance);
    const customEditor = this.customEditors.getActiveEditor();
    if (customEditor) {
      const toolbar = customEditor.toolbar
        ? customEditor.toolbar
        : _wrs_int_wirisProperties.toolbar;

      if (this.toolbar == null || this.toolbar !== toolbar) {
        this.setToolbar(toolbar);
      }
    } else {
      const toolbar = this.getToolbar();
      if (this.toolbar == null || this.toolbar !== toolbar) {
        this.setToolbar(toolbar);
        this.customEditors.disable();
      }
    }
  }

  /**
   * Updates the current {@link ModalDialog.title}. If a {@link CustomEditors} is enabled
   * sets the custom editor title. Otherwise sets the default title.
   */
  updateTitle() {
    const customEditor = this.customEditors.getActiveEditor();
    if (customEditor) {
      this.modalDialogInstance.setTitle(customEditor.title);
    } else {
      this.modalDialogInstance.setTitle('MathType');
    }
  }

  /**
   * Returns the editor toolbar, depending on the configuration local or server side.
   * @returns {String} - Toolbar identifier.
   */
  getToolbar() {
    let toolbar = 'general';
    if ('toolbar' in this.editorAttributes) {
      ({ toolbar } = this.editorAttributes.toolbar);
    }
    // TODO: Change global integration variable for integration custom toolbar.
    if (toolbar === 'general') {
      // eslint-disable-next-line camelcase
      toolbar = (typeof _wrs_int_wirisProperties === 'undefined' || typeof _wrs_int_wirisProperties.toolbar === 'undefined') ? 'general' : _wrs_int_wirisProperties.toolbar;
    }

    return toolbar;
  }

  /**
   * Sets the current {@link ContentManager.editor} instance toolbar.
   * @param {String} toolbar - The toolbar name.
   */
  setToolbar(toolbar) {
    this.toolbar = toolbar;
    this.editor.setParams({ toolbar: this.toolbar });
  }

  /**
   * Returns true if the content of the editor has been changed. The logic of the changes
   * is delegated to {@link EditorListener} class.
   * @returns {Boolean} True if the editor content has been changed. False otherwise.
   */
  hasChanges() {
    return (!this.editor.isFormulaEmpty() && this.editorListener.getIsContentChanged());
  }

  /**
   * Handle keyboard events detected in modal when elements of this class intervene.
   * @param {KeyboardEvent} keyboardEvent - The keyboard event.
   */
  onKeyDown(keyboardEvent) {
    if (keyboardEvent.key !== undefined && keyboardEvent.repeat === false) {
      // Code to detect Esc event.
      if (keyboardEvent.key === 'Escape' || keyboardEvent.key === 'Esc') {
        // There should be only one element with class name 'wrs_pressed' at the same time.
        let list = document.getElementsByClassName('wrs_expandButton wrs_expandButtonFor3RowsLayout wrs_pressed');
        if (list.length === 0) {
          list = document.getElementsByClassName('wrs_expandButton wrs_expandButtonFor2RowsLayout wrs_pressed');
          if (list.length === 0) {
            list = document.getElementsByClassName('wrs_select wrs_pressed');
            if (list.length === 0) {
              this.modalDialogInstance.cancelAction();
              keyboardEvent.stopPropagation();
              keyboardEvent.preventDefault();
            }
          }
        }
      } else if (keyboardEvent.shiftKey && keyboardEvent.key === 'Tab') { // Code to detect shift Tab event.
        if (document.activeElement === this.modalDialogInstance.submitButton) {
          this.editor.focus();
          keyboardEvent.stopPropagation();
          keyboardEvent.preventDefault();
        } else {
          // There should be more than one element with class name 'wrs_selected'.
          const list = document.getElementsByClassName('wrs_selected');
          let done = false;
          for (let i = 0; !done && i < list.length; i += 1) {
            if (list[i].getAttribute('role') === 'tab' && document.activeElement === list[i]) {
              this.modalDialogInstance.cancelButton.focus();
              done = true;
              keyboardEvent.stopPropagation();
              keyboardEvent.preventDefault();
            }
          }
        }
      } else if (keyboardEvent.key === 'Tab') { // Code to detect Tab event.
        if (document.activeElement === this.modalDialogInstance.cancelButton) {
          const list = document.getElementsByClassName('wrs_selected');
          let done = false;
          for (let i = 0; !done && i < list.length; i += 1) {
            if (list[i].getAttribute('role') === 'tab') {
              list[i].focus();
              done = true;
              keyboardEvent.stopPropagation();
              keyboardEvent.preventDefault();
            }
          }
        } else {
          // There should be only one element with class name 'wrs_formulaDisplay'.
          const element = document.getElementsByClassName('wrs_formulaDisplay')[0];
          if (element.getAttribute('class') === 'wrs_formulaDisplay wrs_focused') {
            this.modalDialogInstance.submitButton.focus();
            keyboardEvent.stopPropagation();
            keyboardEvent.preventDefault();
          }
        }
      }
    }
  }
}
