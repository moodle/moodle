var H5PEditor = H5PEditor || {};
var ns = H5PEditor;

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

ns.Html.prototype.createToolbar = function () {
  var basicstyles = [];
  var paragraph = [];
  var formats = [];
  var inserts = [];
  var toolbar = [];

  // Basic styles
  if (this.inTags("strong") || this.inTags("b")) {
    basicstyles.push('Bold');
    // Might make "strong" duplicated in the tag lists. Which doesn't really
    // matter. Note: CKeditor will only make strongs.
    this.tags.push("strong");
  }
  if (this.inTags("em") || this.inTags("i")) {
    basicstyles.push('Italic');
    // Might make "em" duplicated in the tag lists. Which again
    // doesn't really matter. Note: CKeditor will only make ems.
    this.tags.push("em");
  }
  if (this.inTags("u")) basicstyles.push('Underline');
  if (this.inTags("strike") || this.inTags("del") || this.inTags("s")) {
    basicstyles.push('Strike');
    // Might make "strike" or "del" or both duplicated in the tag lists. Which
    // again doesn't really matter.
    this.tags.push("strike");
    this.tags.push("del");
    this.tags.push("s");
  }
  if (this.inTags("sub")) basicstyles.push("Subscript");
  if (this.inTags("sup")) basicstyles.push("Superscript");
  if (basicstyles.length > 0) {
    basicstyles.push("-");
    basicstyles.push("RemoveFormat");
    toolbar.push({
      name: 'basicstyles',
      items: basicstyles
    });
  }

  // Alignment is added to all wysiwygs
  toolbar.push({
    name: "justify",
    items: ["JustifyLeft", "JustifyCenter", "JustifyRight"]
  });

  // Paragraph styles
  if (this.inTags("ul")) {
    paragraph.push("BulletedList");
    this.tags.push("li");
  }
  if (this.inTags("ol")) {
    paragraph.push("NumberedList");
    this.tags.push("li");
  }
  if (this.inTags("blockquote")) paragraph.push("Blockquote");
  if (paragraph.length > 0) {
    toolbar.push(paragraph);
  }

  // Links.
  if (this.inTags("a")) {
    var items = ["Link", "Unlink"];
    if (this.inTags("anchor")) {
      items.push("Anchor");
    }
    toolbar.push({
      name: "links",
      items: items
    });
  }

  // Inserts
  if (this.inTags("img")) inserts.push("Image");
  if (this.inTags("table")) {
    inserts.push("Table");
    ns.$.merge(this.tags, ["tr", "td", "th", "colgroup", "thead", "tbody", "tfoot"]);
  }
  if (this.inTags("hr")) inserts.push("HorizontalRule");
  if (inserts.length > 0) {
    toolbar.push({
      name: "insert",
      items: inserts
    });
  }

  // Create wrapper for text styling options
  var styles = {
    name: "styles",
    items: []
  };
  var colors = {
    name: "colors",
    items: []
  };

  // Add format group if formatters in tags (h1, h2, etc). Formats use their
  // own format_tags to filter available formats.
  if (this.inTags("h1")) formats.push("h1");
  if (this.inTags("h2")) formats.push("h2");
  if (this.inTags("h3")) formats.push("h3");
  if (this.inTags("h4")) formats.push("h4");
  if (this.inTags("h5")) formats.push("h5");
  if (this.inTags("h6")) formats.push("h6");
  if (this.inTags("address")) formats.push("address");
  if (this.inTags("pre")) formats.push("pre");
  if (formats.length > 0 || this.inTags('p') || this.inTags('div')) {
    formats.push("p");   // If the formats are shown, always have a paragraph..
    this.tags.push("p");
    styles.items.push('Format');
  }

  var ret = {
    toolbar: toolbar
  };

  if (this.field.font !== undefined) {
    this.tags.push('span');

    /**
     * Help set specified values for property.
     *
     * @private
     * @param {Array} values list
     * @param {string} prop Property
     * @param {string} [defProp] Default property name
     */
    var setValues = function (values, prop, defProp) {
      ret[prop] = '';
      for (var i = 0; i < values.length; i++) {
        var val = values[i];
        if (val.label && val.css) {
          // Add label and CSS
          ret[prop] += val.label + '/' + val.css + ';';

          // Check if default value
          if (defProp && val.default) {
            ret[defProp] = val.label;
          }
        }
      }
    };

    /**
     * @private
     * @param {Array} values
     * @returns {string}
     */
    var getColors = function (values) {
      var colors = '';
      for (var i = 0; i < values.length; i++) {
        var val = values[i];
        if (val.label && val.css) {
          var css = val.css.match(/^#?([a-f0-9]{3}[a-f0-9]{3}?)$/i);
          if (!css) {
            continue;
          }

          // Add label and CSS
          if (colors) {
            colors += ',';
          }
          colors += val.label + '/' + css[1];
        }
      }
      return colors;
    };

    if (this.field.font.family) {
      // Font family chooser
      styles.items.push('Font');

      if (this.field.font.family instanceof Array) {
        // Use specified families
        setValues(this.field.font.family, 'font_names', 'font_defaultLabel');
      }
    }

    if (this.field.font.size) {
      // Font size chooser
      styles.items.push('FontSize');

      ret.fontSize_sizes = '';
      if (this.field.font.size instanceof Array) {
        // Use specified sizes
        setValues(this.field.font.size, 'fontSize_sizes', 'fontSize_defaultLabel');
      }
      else {
        ret.fontSize_defaultLabel = '100%';

        // Standard font size that is used. (= 100%)
        var defaultFont = 16;

        // Standard font sizes that is available.
        var defaultAvailable = [8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72];
        for (var i = 0; i < defaultAvailable.length; i++) {
          // Calculate percentage of standard font size. This enables scaling
          // in content types without rounding errors across browsers.
          var em = defaultAvailable[i] / 16;
          ret.fontSize_sizes += (em * 100) + '%/' + em + 'em;';
        }

      }

    }

    if (this.field.font.color) {
      // Text color chooser
      colors.items.push('TextColor');

      if (this.field.font.color instanceof Array) {
        ret.colorButton_colors = getColors(this.field.font.color);
        ret.colorButton_enableMore = false;
      }
    }

    if (this.field.font.background) {
      // Text background color chooser
      colors.items.push('BGColor');

      if (this.field.font.background instanceof Array) {
        ret.colorButton_colors = getColors(this.field.font.color);
        ret.colorButton_enableMore = false;
      }
    }
  }

  // Add the text styling options
  if (styles.items.length) {
    toolbar.push(styles);
  }
  if (colors.items.length) {
    toolbar.push(colors);
  }

  // Set format_tags if not empty. CKeditor does not like empty format_tags.
  if (formats.length) {
    ret.format_tags = formats.join(';');
  }

  // Enable selection of enterMode in module semantics.
  if (this.field.enterMode === 'p' || formats.length > 0) {
    this.tags.push('p');
    ret.enterMode = CKEDITOR.ENTER_P;
  } else {
    // Default to DIV, not allowing BR at all.
    this.tags.push('div');
    ret.enterMode = CKEDITOR.ENTER_DIV;
  }

  return ret;
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

  var ckConfig = {
    extraPlugins: "",
    startupFocus: true,
    enterMode: CKEDITOR.ENTER_DIV,
    allowedContent: true, // Disables the ckeditor content filter, might consider using it later... Must make sure it doesn't remove math...
    protectedSource: []
  };
  ns.$.extend(ckConfig, this.createToolbar());

  // Look for additions in HtmlAddons
  if (ns.HtmlAddons) {
    for (var tag in ns.HtmlAddons) {
      if (that.inTags(tag)) {
        for (var provider in ns.HtmlAddons[tag]) {
          ns.HtmlAddons[tag][provider](ckConfig, that.tags);
        }
      }
    }
  }

  this.$item.children('.ckeditor').focus(function () {

    // Blur is not fired on destroy. Therefore we need to keep track of it!
    var blurFired = false;

    // Remove placeholder
    that.$placeholder = that.$item.find('.h5peditor-ckeditor-placeholder').detach();

    if (ns.Html.first) {
      CKEDITOR.basePath = ns.basePath + '/ckeditor/';
    }

    if (ns.Html.current === that) {
      return;
    }
    // Remove existing CK instance.
    ns.Html.removeWysiwyg();

    ns.Html.current = that;
    ckConfig.width = this.offsetWidth - 8; // Avoid miscalculations
    that.ckeditor = CKEDITOR.replace(this, ckConfig);

    that.ckeditor.on('focus', function () {
      blurFired = false;
    });

    that.ckeditor.once('destroy', function () {

      // In some cases, the blur event is not fired. Need to be sure it is, so that
      // validation and saving is done
      if (!blurFired) {
        blur();
      }

      // Display placeholder if:
      // -- The value held by the field is empty AND
      // -- The value shown in the UI is empty AND
      // -- A placeholder is defined
      var value = that.ckeditor !== undefined ? that.ckeditor.getData() : that.$input.html();
      if (that.$placeholder.length !== 0 && (value === undefined || value.length === 0) && (that.value === undefined || that.value.length === 0)) {
        that.$placeholder.appendTo(that.$item.find('.ckeditor'));
      }
    });

    var blur = function () {
      blurFired = true;
      // Do not validate if the field has been hidden.
      if (that.$item.is(':visible')) {
        that.validate();
      }
    };

    that.ckeditor.on('blur', blur);

    // Add events to ckeditor. It is beeing done here since we know it exists
    // at this point... Use case from commit message: "Make the default
    // linkTargetType blank for ckeditor" - STGW
    if (ns.Html.first) {
      CKEDITOR.on('dialogDefinition', function(e) {
        // Take the dialog name and its definition from the event data.
        var dialogName = e.data.name;
        var dialogDefinition = e.data.definition;

        // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
        if (dialogName === 'link') {
          // Get a reference to the "Link Info" tab.
          var targetTab = dialogDefinition.getContents('target');

          // Set the default value for the URL field.
          var urlField = targetTab.get('linkTargetType');
          urlField['default'] = '_blank';
        }

        // Override show event handler
        var onShow = dialogDefinition.onShow;
        dialogDefinition.onShow = function () {
          if (onShow !== undefined) {
            onShow.apply(this, arguments);
          }

          // Grab current item
          var $item = ns.Html.current.$item;

          // Position dialog above text field
          var itemPos = $item.offset();
          var itemWidth = $item.width();
          var itemHeight = $item.height();
          var dialogSize = this.getSize();

          var x = itemPos.left + (itemWidth / 2) - (dialogSize.width / 2);
          var y = itemPos.top + (itemHeight / 2) - (dialogSize.height / 2);

          this.move(x, y, true);
        };
      });
      ns.Html.first = false;
    }
  });
};

/**
 * Create HTML for the HTML field.
 */
ns.Html.prototype.createHtml = function () {
  var input = '<div class="ckeditor" tabindex="0" contenteditable="true">';
  if (this.value !== undefined) {
    input += this.value;
  }
  else if (this.field.placeholder !== undefined) {
    input += '<span class="h5peditor-ckeditor-placeholder">' + this.field.placeholder + '</span>';
  }
  input += '</div>';

  return ns.createFieldMarkup(this.field, ns.createImportantDescription(this.field.important) + input);
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
  var value = this.ckeditor !== undefined ? this.ckeditor.getData() : this.$input.html();

  // Remove placeholder text if any:
  value = value.replace(/<span class="h5peditor-ckeditor-placeholder">.*<\/span>/, '');

  var $value = ns.$('<div>' + value + '</div>');
  var textValue = $value.text();

  // Check if we have any text at all.
  if (!this.field.optional && !textValue.length) {
    // We can accept empty text, if there's an image instead.
    if (! (this.inTags("img") && $value.find('img').length > 0)) {
      this.$errors.append(ns.createError(ns.t('core', 'requiredProperty', {':property': ns.t('core', 'textField')})));
    }
  }

  // Verify HTML tags.  Removes tags not in allowed tags.  Will replace with
  // the tag's content.  So if we get an unallowed container, the contents
  // will remain, without the container.
  $value.find('*').each(function () {
    if (! that.inTags(this.tagName)) {
      ns.$(this).replaceWith(ns.$(this).contents());
    }
  });
  value = $value.html();

  // Display errors and bail if set.
  if (that.$errors.children().length) {
    return false;
  } else {
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

ns.widgets.html = ns.Html;
