/**
 * Creates a widget to add author information to a form
 *
 * @param {object} semantics
 * @param {object} params
 * @param {object} group
 * @param {mixed} parent used in processSemanticsChunk()
 * @returns {ns.Coordinates}
 */
H5PEditor.metadataAuthorWidget = function (semantics, params, $wrapper, parent) {
  if (!params.authors) {
    params.authors = [];
  }

  const $ = H5PEditor.$;

  const widget = $('<div class="field h5p-metadata-author-widget"></div>');

  var $authorData = $('<div class="h5p-author-data"></div>');
  widget.append($authorData);

  H5PEditor.processSemanticsChunk(semantics, {}, $authorData, parent);

  // Get references to the fields
  var nameField = H5PEditor.findField('name', parent);
  var roleField = H5PEditor.findField('role', parent);

  var $button = $('<div class="field authorList">' +
    '<button type="button" class="h5p-metadata-button inverted h5p-save-author">' +
      H5PEditor.t('core', 'addAuthor') +
    '</button>' +
  '</div>').children('button').click(function (event) {

    // Temporarily set name as mandatory to get the error messages only when
    // clicking the Add Author button
    nameField.field.optional = false;
    var name = nameField.validate();
    nameField.field.optional = true;
    var role = roleField.validate();

    if (!name) {
      return;
    }

    // Don't add author if already in list with the same role
    const authorDuplicate = params.authors.some(function (author) {
      return author.name === name && author.role === role;
    });
    if (authorDuplicate) {
      resetForm();
      return;
    }

    addAuthor(name, role);
  }).end();
  $authorData.append($button);

  var authorListWrapper = $('<div class="h5p-author-list-wrapper"><ul class="h5p-author-list"></ul></div>');
  widget.append(authorListWrapper);
  renderAuthorList();

  widget.appendTo($wrapper);

  /**
   * Add an author to the list of authors
   * @param {string} [name]
   * @param {string} [role]
   */
  function addAuthor(name, role) {
    params.authors.push({
      name: name,
      role: role
    });

    renderAuthorList();
    resetForm();
  }

  /**
   * Add default/current author to list of authors
   *
   * @param {string} fallbackName Name to fallback to if there is no valid name chosen already
   * @param {string} fallbackRole Role to fallback to if there is no valid role chosen already
   */
  function addDefaultAuthor(fallbackName, fallbackRole) {
    var name = nameField.validate();

    if (!name) {
      name = fallbackName;
    }

    var role = roleField.validate();

    if (!role) {
      role = fallbackRole;
    }

    addAuthor(name, role);
  }

  /**
   * Resets the form
   */
  function resetForm() {
    nameField.$input.val('');
  }

  /**
   * Remove author from list.
   *
   * @param {object} author - Author to be removed.
   * @param {string} author.name - Author name.
   * @param {string} author.role - Author role.
   */
  function removeAuthor(author) {
    params.authors = params.authors.filter(function (e) {
      return (e !== author);
    });

    renderAuthorList();
  }

  function renderAuthorList() {
    var wrapper = widget.find('.h5p-author-list-wrapper');
    wrapper.empty();

    const authorList = $('<ul></ul>');
    params.authors.forEach(function (author) {
      // Name and role
      var listItem = $('<li>', {
        html: H5PEditor.htmlspecialchars(author.name),
        append: $('<span>', {
          'class': 'h5p-metadata-role',
          html: author.role
        })
      });

      // The delete-button
      $('<button>', {
        type: 'button',
        'class': 'h5p-metadata-icon-button',
        click: function () {
          if (confirm(H5PEditor.t('core', 'confirmRemoveAuthor'))) {
            removeAuthor(author);
          }
        }
      }).appendTo(listItem);

      authorList.append(listItem);
    });

    wrapper.append(authorList);
  }

  return {
    addAuthor: addAuthor,
    addDefaultAuthor: addDefaultAuthor
  };
};
