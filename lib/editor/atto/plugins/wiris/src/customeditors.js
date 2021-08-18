/**
 * A custom editor is MathType editor with a different
 * @typedef {Object} CustomEditor
 * @property {String} CustomEditor.name - Custom editor name.
 * @property {String} CustomEditor.toolbar - Custom editor toolbar.
 * @property {String} CustomEditor.icon - Custom editor icon.
 * @property {String} CustomEditor.confVariable - Configuration property to manage
 * the availability of the custom editor.
 * @property {String} CustomEditor.title - Custom editor modal dialog title.
 * @property {String} CustomEditor.tooltip - Custom editor icon tooltip.
 */

export default class CustomEditors {
  /**
   * @classdesc
   * This class represents the MathType custom editors manager.
   * A custom editor is MathType editor with a custom  toolbar.
   * This class associates a {@link CustomEditor} to:
   * - It's own formulas
   * - A custom toolbar
   * - An icon to open it from a HTML editor.
   * - A tooltip for the icon.
   * - A global variable to enable or disable it globally.
   * @constructs
   */
  constructor() {
    /**
     * The custom editors.
     * @type {Array.<CustomEditor>}
     */

    this.editors = [];
    /**
     * The active editor name.
     * @type {String}
     */
    this.activeEditor = 'default';
  }

  /**
   * Adds a {@link CustomEditor} to editors array.
   * @param {String} editorName - The editor name.
   * @param {CustomEditor} editorParams - The custom editor parameters.
   */
  addEditor(editorName, editorParams) {
    const customEditor = {};
    customEditor.name = editorParams.name;
    customEditor.toolbar = editorParams.toolbar;
    customEditor.icon = editorParams.icon;
    customEditor.confVariable = editorParams.confVariable;
    customEditor.title = editorParams.title;
    customEditor.tooltip = editorParams.tooltip;
    this.editors[editorName] = customEditor;
  }

  /**
   * Enables a {@link CustomEditor}.
   * @param {String} customEditorName - The custom editor name.
   */
  enable(customEditorName) {
    this.activeEditor = customEditorName;
  }

  /**
   * Disables a {@link CustomEditor}.
   */
  disable() {
    this.activeEditor = 'default';
  }

  /**
   * Returns the active editor.
   * @return {CustomEditor} - A {@link CustomEditor} if a custom editor is enabled. Null otherwise.
   */
  getActiveEditor() {
    if (this.activeEditor !== 'default') {
      return this.editors[this.activeEditor];
    }
    return null;
  }
}
