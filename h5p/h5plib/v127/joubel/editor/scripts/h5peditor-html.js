/* global ns CKEDITOR */
/**
 * Adds a html text field to the form.
 *
 * @param {type} parent
 * @param {type} field
 * @param {type} params
 * @param {type} setValue
 * @returns {undefined}
 */
ns.Html = function (parent, field, params, setValue) {
  this.parent = parent;
  this.field = field;
  this.value = params;
  this.setValue = setValue;
  this.tags = ns.$.merge(['br'], (this.field.tags || this.defaultTags));
};
ns.Html.first = true;

ns.Html.prototype.defaultTags = ['strong', 'em', 'del', 'h2', 'h3', 'a', 'ul', 'ol', 'table', 'hr'];

// This should probably be named "hasTag()" instead...
// And might be more efficient if this.tags.contains() were used?
ns.Html.prototype.inTags = function (value) {
  return (ns.$.inArray(value.toLowerCase(), this.tags) >= 0);
};

/**
 * Check if the provided button is enabled by config.
 *
 * @param {string} value
 * @return {boolean}
 */
ns.Html.prototype.inButtons = function (button) {
  return (H5PIntegration.editor !== undefined && H5PIntegration.editor.wysiwygButtons !== undefined && H5PIntegration.editor.wysiwygButtons.indexOf(button) !== -1);
};

ns.Html.prototype.getCKEditorConfig = function () {
  const config = {
    updateSourceElementOnDestroy: true,
    plugins: ['Essentials', 'Paragraph'],
    alignment: { options: ["left", "center", "right"] },
    toolbar: [],
  };

  // Basic styles
  const basicstyles = [];
  const basicstylesPlugins = [];
  if (this.inTags("strong") || this.inTags("b")) {
    basicstyles.push('bold');
    basicstylesPlugins.push('Bold');
    // Might make "strong" duplicated in the tag lists. Which doesn't really
    // matter. Note: CKeditor will only make strongs.
    this.tags.push("strong");
  }
  if (this.inTags("em") || this.inTags("i")) {
    // Use <em> elements for italic text instead of CKE's default <i>
    // Has to be a plugin to work
    const ItalicAsEmPlugin = function (editor) {
      editor.conversion.for('downcast').attributeToElement({
        model: 'italic',
        view: 'em',
        converterPriority: 'high',
      });
    }

    basicstyles.push('italic');
    basicstylesPlugins.push('Italic', ItalicAsEmPlugin);
    this.tags.push("i");
  }
  if (this.inTags("u")) {
    basicstyles.push('underline');
    basicstylesPlugins.push('Underline');
    this.tags.push("u");
  }
  if (this.inTags("strike") || this.inTags("del") || this.inTags("s")) {
    basicstyles.push('strikethrough');
    basicstylesPlugins.push('Strikethrough');
    // Might make "strike" or "del" or both duplicated in the tag lists. Which
    // again doesn't really matter.
    this.tags.push("strike");
    this.tags.push("del");
    this.tags.push("s");
  }
  if (this.inTags("sub")) {
    basicstyles.push("subscript");
    basicstylesPlugins.push('Subscript');
  }
  if (this.inTags("sup")) {
    basicstyles.push("superscript");
    basicstylesPlugins.push('Superscript');
  }
  if (basicstyles.length > 0) {
    basicstyles.push('|', 'removeFormat');
    basicstylesPlugins.push('RemoveFormat');
    config.plugins.push(...basicstylesPlugins);
    config.toolbar.push(...basicstyles);
  }

  // Alignment is added to all wysiwygs
  config.plugins.push('Alignment');
  config.toolbar.push('|', 'alignment');

  // Paragraph styles
  const paragraph = [];
  const paragraphPlugins = [];
  if (this.inTags("ul") || this.inTags("ol")) {
    paragraphPlugins.push('List');
  }
  if (this.inTags("ul")) {
    paragraph.push("bulletedList");
    this.tags.push("li");
  }
  if (this.inTags("ol")) {
    paragraph.push("numberedList");
    this.tags.push("li");
  }
  if (this.inTags("blockquote")) {
    paragraph.push("blockquote");
    paragraphPlugins.push('BlockQuote');
  }
  if (this.inButtons('language')) {
    this.tags.push('span');
    paragraph.push('textPartLanguage');
    paragraphPlugins.push('TextPartLanguage');
  }
  if (paragraph.length > 0) {
    config.plugins.push(...paragraphPlugins);
    config.toolbar.push(...paragraph);
  }

  // Links.
  if (this.inTags("a")) {
    const items = ["link"];
    config.plugins.push('Link', 'AutoLink');
    config.toolbar.push("|", ...items);
    config.link = {
      // Automatically add target="_blank" and rel="noopener noreferrer" to all external links.
      addTargetToExternalLinks: true,
      // Automatically add protocol if not present
      defaultProtocol: 'http://',
    }
  }

  // Inserts
  const inserts = [];
  const insertsPlugins = [];
  if (this.inTags('img')) {
    // TODO: Include toolbar functionality to insert and edit images
    // For now, we just include the plugin to prevent data loss
    insertsPlugins.push('Image');
  }
  if (this.inTags("hr")) {
    inserts.push("horizontalLine");
    insertsPlugins.push('HorizontalLine');
  }
  if (this.inTags('code')) {
    if (this.inButtons('inlineCode')) {
      inserts.push('code');
      insertsPlugins.push('Code');
    }
    if (this.inTags('pre') && this.inButtons('codeSnippet')) {
      inserts.push('codeBlock');
      insertsPlugins.push('CodeBlock');
    }
  }
  if (inserts.length > 0) {
    config.toolbar.push("|", ...inserts);
  }
  if (insertsPlugins.length > 0) {
    config.plugins.push(...insertsPlugins);
  }

  if (this.inTags("table")) {
    config.toolbar.push("insertTable");
    config.plugins.push(
      'Table',
      'TableToolbar',
      'TableProperties',
      'TableCellProperties',
      'TableColumnResize',
      'TableCaption'
    );
    config.table = {
      contentToolbar: [
        'toggleTableCaption',
        'tableColumn',
        'tableRow',
        'mergeTableCells',
        'tableProperties',
        'tableCellProperties'
      ],
      tableProperties: {
        defaultProperties: {
          borderStyle: 'underline',
          borderWidth: '0.083em',
          borderColor: '#494949',
          padding: '0',
          alignment: 'left'
        }
      },
      tableCellProperties: {
        defaultProperties: {
          borderStyle: 'underline',
          borderWidth: '0.083em',
          borderColor: '#494949',
          padding: '1px'
        }
      }
    }
    ns.$.merge(this.tags, ["tr", "td", "th", "colgroup", "col", "thead", "tbody", "tfoot", "figure", "figcaption"]);
  }

  // Add dropdown to toolbar if formatters in tags (h1, h2, etc).
  const formats = [];
  for (let index = 1; index < 7; index++) {
    if (this.inTags('h' + index)) {
      formats.push({ model: 'heading' + index, view: 'h' + index, title: 'Heading ' + index, class: 'ck-heading_heading' + index });
    }
  }

  if (this.inTags('pre')) {
    formats.push({ model: 'formatted', view: 'pre', title: 'Formatted', class: 'ck-heading_formatted' });
  }

  // if (this.inTags("address")) formats.push("address"); // TODO: potential data loss
  if (formats.length > 0 || this.inTags('p') || this.inTags('div')) {
    // If the formats are shown, always have a paragraph
    formats.push({ model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' });
    this.tags.push("p");
    this.field.enterMode = "p";
    config.heading = {options: formats};
    config.plugins.push('Heading');
    config.toolbar.push('heading');
  }

  if (this.field.font !== undefined) {
    // Create wrapper for text styling options
    var styles = [];
    var colors = [];
    this.tags.push('span');

    /**
     * Help set specified values for property.
     *
     * @private
     * @param {Array} values list
     * @param {string} prop Property name
     */
    const setValues = function (values, prop) {
      options = [];
      for (let i = 0; i < values.length; i++) {
        options.push(values[i]);
      }
      config[prop] = { options: options };
    };

    // Font family chooser
    if (this.field.font.family) {
      styles.push('fontFamily');
      config.plugins.push('FontFamily');

      let fontFamilies = [
        'default',
        'Arial, Helvetica, sans-serif',
        'Comic Sans MS, Cursive, sans-serif',
        'Courier New, Courier, monospace',
        'Georgia, serif',
        'Lucida Sans Unicode, Lucida Grande, sans-serif',
        'Tahoma, Geneva, sans-serif',
        'Times New Roman, Times, serif',
        'Trebuchet MS, Helvetica, sans-serif',
        'Verdana, Geneva, sans-serif'
      ]

      // If custom fonts are set, use those
      if (this.field.font.family instanceof Array) {
        fontFamilies = ['default', ...this.field.font.family.map(font => (
          font.label + ', ' + font.css
        ))];
      }

      setValues(fontFamilies, 'fontFamily');
      config.fontFamily.supportAllValues = true;
    }

    // Font size chooser
    if (this.field.font.size) {
      styles.push('fontSize');
      config.plugins.push('FontSize');

      let fontSizes = [];
      const convertToEm = (percent) => parseFloat(percent) / 100 + 'em';

      if (this.field.font.size instanceof Array) {
        // Use specified sizes
        fontSizes = this.field.font.size.map(size => ({
          title: size.label,
          model: convertToEm(size.css)
        }));
      } else {
        // Standard font sizes that are available
        fontSizes = [
          'Default', '50%', '56.25%', '62.5%', '68.75%', '75%', '87.5%',
          '100%', '112.5%', '125%', '137.5%', '150%', '162.5%', '175%',
          '225%', '300%', '450%'
        ].map(percent => ({
          title: percent,
          model: percent === 'Default' ? '1em' : convertToEm(percent)
        }));
      }

      setValues(fontSizes, 'fontSize');
    }

    /**
     * Format an array of color objects for ckeditor
     * @param {Array} values
     * @returns {string}
     */
    const getColors = function (values) {
      const colors = [];
      for (let i = 0; i < values.length; i++) {
        const val = values[i];
        if (val.label && val.css) {
          // Check if valid color format
          const css = val.css.match(/^(#[a-f0-9]{3}[a-f0-9]{3}?|rgba?\([0-9, ]+\)|hsla?\([0-9,.% ]+\)) *;?$/i);

          // If invalid, skip
          if (!css) {
            continue;
          }

          colors.push({color: css[0], label: val.label});
        }
      }
      return colors;
    };

    // Text color chooser
    if (this.field.font.color) {
      colors.push('fontColor');
      config.plugins.push('FontColor');

      if (this.field.font.color instanceof Array) {
        config.fontColor = { colors: getColors(this.field.font.color) };
      }
    }

    // Text background color chooser
    if (this.field.font.background) {
      colors.push('fontBackgroundColor');
      config.plugins.push('FontBackgroundColor');

      if (this.field.font.background instanceof Array) {
        config.fontBackgroundColor = { colors: getColors(this.field.font.color) };
      }
    }

    // Add the text styling options
    if (styles.length) {
      config.toolbar.push(...styles);
    }
    if (colors.length) {
      config.toolbar.push(...colors);
    }
  }

  if (this.field.enterMode === 'p') {
    this.tags.push('p');
  }
  else {
    this.tags.push('div');

    // Without this, empty divs get deleted on init of cke
    config.plugins.push('GeneralHtmlSupport');
    config.htmlSupport = {
      allow: [{
        name: 'div',
        attributes: true,
        classes: true,
        styles: true
      }]
    };
  }

  return config;
};

/**
 * Append field to wrapper.
 *
 * @param {type} $wrapper
 * @returns {undefined}
 */
ns.Html.prototype.appendTo = function ($wrapper) {
  var that = this;

  this.$item = ns.$(this.createHtml()).appendTo($wrapper);
  this.$input = this.$item.children('.ckeditor');
  this.$errors = this.$item.children('.h5p-errors');

  ns.bindImportantDescriptionEvents(this, this.field.name, this.parent);

  this.ckEditorConfig = this.getCKEditorConfig();

  this.$input.focus(function () {
    // Blur is not fired on destroy. Therefore we need to keep track of it!
    var blurFired = false;

    // Remove placeholder
    that.$placeholder = that.$item.find('.h5peditor-ckeditor-placeholder').detach();

    if (ns.Html.first) {
      ClassicEditor.basePath = ns.basePath + '/ckeditor/';
      ns.Html.first = false;
    }

    if (ns.Html.current === that) {
      return;
    }
    // Remove existing CK instance.
    ns.Html.removeWysiwyg();

    that.inputWidth = that.$input[0].getBoundingClientRect().width;
    ns.Html.current = that;

    ClassicEditor
      .create(this, that.ckEditorConfig)
      .then(editor => {
        const getEditorHeight = () => {
          // Use the dimensions of the H5P iframe, not the editor iframe
          const { innerHeight, innerWidth } = window.parent;

          let ratio = 0.5;

          switch (true) {
            case innerHeight < 560:
              ratio = 0.2
              break;
            case innerHeight < 768 && innerWidth < 576:
              ratio = 0.25
              break;
            case innerHeight < 768:
              ratio = 0.3
              break;
            case innerHeight < 1024 && innerWidth < 576:
              ratio = 0.3;
              break;
            case innerHeight < 1024:
              ratio = 0.45;
              break;
            default:
              break;
          }

          return (ratio * innerHeight * 0.85) + 'px';
        }

        that.ckeditor = editor;
        const editable = editor.ui.view.editable;
        editorElement = editable.element;
        editor.ui.view.element.style.maxWidth = that.inputWidth + 'px';
        editorElement.style.maxHeight = getEditorHeight();

        // Readjust toolbar's grouped item dropdown panel,
        // since it can overflow the parent iframe element by using default positioning
        const dropdownPanel = editor.ui.view.toolbar._behavior.groupedItemsDropdown;
        dropdownPanel.panelPosition = 'auto';

        // Disable sticky toolbar, since it has problem within iframes
        editor.ui.view.stickyPanel.unbind('isActive');
        editor.ui.view.stickyPanel.isActive = false;

        // Remove overflow protection on startup
        let initialData = editor.getData();
        if (initialData.includes('table-overflow-protection')) {
          initialData = initialData.replace(/<div class=\"table-overflow-protection\">.*<\/div>/, '');
          editor.setData(initialData);
        }

        // Mimic old enter_mode behaviour if not specifically set to 'p'
        if (that.field.enterMode !== 'p') {
          // Use <div> elements instead of <p>
          editor.conversion.for('downcast').elementToElement({
            model: 'paragraph',
            view: 'div',
            converterPriority: 'high'
          });
        }

        editor.ui.on('update', () => {
          editorElement.style.maxHeight = getEditorHeight();
        });

        editor.editing.view.focus();

        editor.on('focus', function () {
          blurFired = false;
          editorElement.style.maxHeight = getEditorHeight();
        });

        editor.once('destroy', function () {
          // In some cases, the blur event is not fired. Need to be sure it is, so that
          // validation and saving is done
          if (!blurFired) {
            blur();
          }

          // Display placeholder if:
          // -- The value held by the field is empty AND
          // -- The value shown in the UI is empty AND
          // -- A placeholder is defined
          const value = editor.getData();
          if (that.$placeholder.length !== 0 && (value === undefined || value.length === 0) && (that.value === undefined || that.value.length === 0)) {
            that.$placeholder.appendTo(that.$item.find('.ckeditor'));
          }

          // Since validate() is not always run,
          // make sure tabe overflow protection is added always when editor is destroyed
          if (value.includes('table') && !value.includes('table-overflow-protection')) {
            that.value = value + '<div class="table-overflow-protection"></div>';
            that.setValue(that.field, that.value);
            that.$input.html(that.value).change();
          }
        });

        var blur = function () {
          blurFired = true;

          // Do not validate if the field has been hidden.
          if (that.$item.is(':visible')) {
            that.validate();
          }
        };

        editor.on('blur', blur);
      })
      .catch(error => {
        throw new Error('Error loading CKEditor: ' + error);
      });
    });

  // Always preload the first CKEditor field to avoid focus problems when the
  // editor is opened inside an iframe and focus has to be set by a human made
  // event (Safari).
  // if (this.$item.is(':visible') && !ns.Html.firstLoad) {
  //   handleFocus();
  //   ns.Html.firstLoad = true;
  // }
};

/**
 * Create HTML for the HTML field.
 */
ns.Html.prototype.createHtml = function () {
  const id = ns.getNextFieldId(this.field);
  var input = '<div id="' + id + '"';
  if (this.field.description !== undefined) {
    input += ' aria-describedby="' + ns.getDescriptionId(id) + '"';
  }
  input += ' class="ckeditor" tabindex="0" contenteditable="true">';
  if (this.value !== undefined) {
    input += this.value;
  }
  else if (this.field.placeholder !== undefined) {
    input += '<span class="h5peditor-ckeditor-placeholder">' + this.field.placeholder + '</span>';
  }
  // Add overflow protection if table
  if (this.field.tags.includes('table') && !input.includes('table-overflow-protection')) {
    input += '<div class="table-overflow-protection"></div>';
  }
  input += '</div>';

  return ns.createFieldMarkup(this.field, ns.createImportantDescription(this.field.important) + input, id);
};

/**
 * Validate the current text field.
 */
ns.Html.prototype.validate = function () {
  var that = this;

  if (that.$errors.children().length) {
    that.$errors.empty();
    this.$input.addClass('error');
  }

  // Get contents from editor
  // If there are more than one ckeditor, getData() might be undefined when ckeditor is not
  let value = ((this.ckeditor !== undefined && this.ckeditor.getData() !== undefined)
    ? this.ckeditor.getData()
    : this.$input.html());

  value = value
    // Remove placeholder text if any:
    .replace(/<span class="h5peditor-ckeditor-placeholder">.*<\/span>/, '')
    // Workaround for Microsoft browsers that otherwise can produce non-emtpy fields causing trouble
    .replace(/^<br>$/, '');

  var $value = ns.$('<div>' + value + '</div>');
  var textValue = $value.text();

  // Check if we have any text at all.
  if (!this.field.optional && !textValue.length) {
    // We can accept empty text, if there's an image instead.
    if (!(this.inTags("img") && $value.find('img').length > 0)) {
      this.$errors.append(ns.createError(ns.t('core', 'requiredProperty', { ':property': ns.t('core', 'textField') })));
    }
  }

  // Verify HTML tags.  Removes tags not in allowed tags.  Will replace with
  // the tag's content.  So if we get an unallowed container, the contents
  // will remain, without the container.
  $value.find('*').each(function () {
    if (!that.inTags(this.tagName)) {
      ns.$(this).replaceWith(ns.$(this).contents());
    }
  });

  // Add overflow protection if chance of aligned tables
  if(that.inTags('table') && !value.includes('table-overflow-protection')) {
    this.$input.append('<div class="table-overflow-protection"></div>');
    $value.append('<div class="table-overflow-protection"></div>');
  }

  value = $value.html();

  // Display errors and bail if set.
  if (that.$errors.children().length) {
    return false;
  }
  else {
    this.$input.removeClass('error');
  }

  this.value = value;
  this.setValue(this.field, value);
  this.$input.change(); // Trigger change event.

  return value;
};

/**
 * Destroy H5PEditor existing CK instance. If it exists.
 */
ns.Html.removeWysiwyg = function () {
  if (ns.Html.current !== undefined) {
    try {
      ns.Html.current.ckeditor.destroy();
    }
    catch (e) {
      // No-op, just stop error from propagating. This usually occurs if
      // the CKEditor DOM has been removed together with other DOM data.
    }
    ns.Html.current = undefined;
  }
};

/**
 * Remove this item.
 */
ns.Html.prototype.remove = function () {
  this.$item.remove();
};

/**
 * When someone from the outside wants to set a value.
 *
 * @param {string} value
 */
ns.Html.prototype.forceValue = function (value) {
  if (this.ckeditor === undefined) {
    this.$input.html(value);
  }
  else {
    this.ckeditor.setData(value);
  }
  this.validate();
};

ns.widgets.html = ns.Html;
