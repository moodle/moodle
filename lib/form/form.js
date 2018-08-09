/**
 * This file contains JS functionality required by mforms and is included automatically
 * when required.
 */

// Namespace for the form bits and bobs
M.form = M.form || {};

if (typeof M.form.dependencyManager === 'undefined') {
    var dependencyManager = function() {
        dependencyManager.superclass.constructor.apply(this, arguments);
    };
    Y.extend(dependencyManager, Y.Base, {
        _locks: null,
        _hides: null,
        _dirty: null,
        _nameCollections: null,
        _fileinputs: null,

        initializer: function() {
            // Setup initial values for complex properties.
            this._locks = {};
            this._hides = {};
            this._dirty = {};

            // Setup event handlers.
            Y.Object.each(this.get('dependencies'), function(value, i) {
                var elements = this.elementsByName(i);
                elements.each(function(node) {
                    var nodeName = node.get('nodeName').toUpperCase();
                    if (nodeName == 'INPUT') {
                        if (node.getAttribute('type').match(/^(button|submit|radio|checkbox)$/)) {
                            node.on('click', this.updateEventDependencies, this);
                        } else {
                            node.on('blur', this.updateEventDependencies, this);
                        }
                        node.on('change', this.updateEventDependencies, this);
                    } else if (nodeName == 'SELECT') {
                        node.on('change', this.updateEventDependencies, this);
                    } else {
                        node.on('click', this.updateEventDependencies, this);
                        node.on('blur', this.updateEventDependencies, this);
                        node.on('change', this.updateEventDependencies, this);
                    }
                }, this);
            }, this);

            // Handle the reset button.
            this.get('form').get('elements').each(function(input) {
                if (input.getAttribute('type') == 'reset') {
                    input.on('click', function() {
                        this.get('form').reset();
                        this.updateAllDependencies();
                    }, this);
                }
            }, this);

            this.updateAllDependencies();
        },

        /**
         * Initializes the mapping from element name to YUI NodeList
         */
        initElementsByName: function() {
            var names = {}; // Form elements with a given name.
            var allnames = {}; // Form elements AND outer elements for groups with a given name.

            // Collect element names.
            Y.Object.each(this.get('dependencies'), function(conditions, i) {
                names[i] = new Y.NodeList();
                allnames[i] = new Y.NodeList();
                for (var condition in conditions) {
                    for (var value in conditions[condition]) {
                        for (var hide in conditions[condition][value]) {
                            for (var ei in conditions[condition][value][hide]) {
                                names[conditions[condition][value][hide][ei]] = new Y.NodeList();
                                allnames[conditions[condition][value][hide][ei]] = new Y.NodeList();
                            }
                        }
                    }
                }
            });

            // Locate elements for each name.
            this.get('form').get('elements').each(function(node) {
                var name = node.getAttribute('name');
                if (({}).hasOwnProperty.call(names, name)) {
                    names[name].push(node);
                    allnames[name].push(node);
                }
            });
            // Locate any groups with the given name.
            this.get('form').all('.fitem').each(function(node) {
                var name = node.getData('groupname');
                if (name && ({}).hasOwnProperty.call(allnames, name)) {
                    allnames[name].push(node);
                }
            });
            this._nameCollections = {names: names, allnames: allnames};
        },

        /**
         * Gets all elements in the form by their name and returns
         * a YUI NodeList
         *
         * @param {String} name The form element name.
         * @param {Boolean} includeGroups (optional - default false) Should the outer element for groups be included?
         * @return {Y.NodeList}
         */
        elementsByName: function(name, includeGroups) {
            if (includeGroups === undefined) {
                includeGroups = false;
            }
            var collection = (includeGroups ? 'allnames' : 'names');

            if (!this._nameCollections) {
                this.initElementsByName();
            }
            if (!({}).hasOwnProperty.call(this._nameCollections[collection], name)) {
                return new Y.NodeList();
            }
            return this._nameCollections[collection][name];
        },

        /**
         * Checks the dependencies the form has an makes any changes to the
         * form that are required.
         *
         * Changes are made by functions title _dependency{Dependencytype}
         * and more can easily be introduced by defining further functions.
         *
         * @param {EventFacade | null} e The event, if any.
         * @param {String} dependon The form element name to check dependencies against.
         * @return {Boolean}
         */
        checkDependencies: function(e, dependon) {
            var dependencies = this.get('dependencies'),
                tohide = {},
                tolock = {},
                condition, value, isHide, lock, hide,
                checkfunction, result, elements;
            if (!({}).hasOwnProperty.call(dependencies, dependon)) {
                return true;
            }
            elements = this.elementsByName(dependon);
            for (condition in dependencies[dependon]) {
                for (value in dependencies[dependon][condition]) {
                    for (isHide in dependencies[dependon][condition][value]) {
                        checkfunction = '_dependency' + condition[0].toUpperCase() + condition.slice(1);
                        if (Y.Lang.isFunction(this[checkfunction])) {
                            result = this[checkfunction].apply(this, [elements, value, (isHide === "1"), e]);
                        } else {
                            result = this._dependencyDefault(elements, value, (isHide === "1"), e);
                        }
                        lock = result.lock || false;
                        hide = result.hide || false;
                        for (var ei in dependencies[dependon][condition][value][isHide]) {
                            var eltolock = dependencies[dependon][condition][value][isHide][ei];
                            if (({}).hasOwnProperty.call(tohide, eltolock)) {
                                tohide[eltolock] = tohide[eltolock] || hide;
                            } else {
                                tohide[eltolock] = hide;
                            }

                            if (({}).hasOwnProperty.call(tolock, eltolock)) {
                                tolock[eltolock] = tolock[eltolock] || lock;
                            } else {
                                tolock[eltolock] = lock;
                            }
                        }
                    }
                }
            }

            for (var el in tolock) {
                var needsupdate = false;
                if (!({}).hasOwnProperty.call(this._locks, el)) {
                    this._locks[el] = {};
                }
                if (({}).hasOwnProperty.call(tolock, el) && tolock[el]) {
                    if (!({}).hasOwnProperty.call(this._locks[el], dependon) || this._locks[el][dependon]) {
                        this._locks[el][dependon] = true;
                        needsupdate = true;
                    }
                } else if (({}).hasOwnProperty.call(this._locks[el], dependon) && this._locks[el][dependon]) {
                    delete this._locks[el][dependon];
                    needsupdate = true;
                }

                if (!({}).hasOwnProperty.call(this._hides, el)) {
                    this._hides[el] = {};
                }
                if (({}).hasOwnProperty.call(tohide, el) && tohide[el]) {
                    if (!({}).hasOwnProperty.call(this._hides[el], dependon) || this._hides[el][dependon]) {
                        this._hides[el][dependon] = true;
                        needsupdate = true;
                    }
                } else if (({}).hasOwnProperty.call(this._hides[el], dependon) && this._hides[el][dependon]) {
                    delete this._hides[el][dependon];
                    needsupdate = true;
                }

                if (needsupdate) {
                    this._dirty[el] = true;
                }
            }

            return true;
        },
        /**
         * Update all dependencies in form
         */
        updateAllDependencies: function() {
            Y.Object.each(this.get('dependencies'), function(value, name) {
                this.checkDependencies(null, name);
            }, this);

            this.updateForm();
        },
        /**
         * Update dependencies associated with event
         *
         * @param {Event} e The event.
         */
        updateEventDependencies: function(e) {
            var el = e.target.getAttribute('name');
            this.checkDependencies(e, el);
            this.updateForm();
        },
        /**
         * Flush pending changes to the form
         */
        updateForm: function() {
            var el;
            for (el in this._dirty) {
                if (({}).hasOwnProperty.call(this._locks, el)) {
                    this._disableElement(el, !Y.Object.isEmpty(this._locks[el]));
                }
                if (({}).hasOwnProperty.call(this._hides, el)) {
                    this._hideElement(el, !Y.Object.isEmpty(this._hides[el]));
                }
            }

            this._dirty = {};
        },
        /**
         * Disables or enables all form elements with the given name
         *
         * @param {String} name The form element name.
         * @param {Boolean} disabled True to disable, false to enable.
         */
        _disableElement: function(name, disabled) {
            var els = this.elementsByName(name),
                filepicker = this.isFilePicker(name),
                editors = this.get('form').all('.fitem [data-fieldtype="editor"] textarea[name="' + name + '[text]"]');

            els.each(function(node) {
                if (disabled) {
                    node.setAttribute('disabled', 'disabled');
                } else {
                    node.removeAttribute('disabled');
                }

                // Extra code to disable filepicker or filemanager form elements
                if (filepicker) {
                    var fitem = node.ancestor('.fitem');
                    if (fitem) {
                        if (disabled) {
                            fitem.addClass('disabled');
                        } else {
                            fitem.removeClass('disabled');
                        }
                    }
                }
            });
            editors.each(function(editor) {
                if (disabled) {
                    editor.setAttribute('readonly', 'readonly');
                } else {
                    editor.removeAttribute('readonly', 'readonly');
                }
                editor.getDOMNode().dispatchEvent(new Event('form:editorUpdated'));
            });
        },
        /**
         * Hides or shows all form elements with the given name.
         *
         * @param {String} name The form element name.
         * @param {Boolean} hidden True to hide, false to show.
         */
        _hideElement: function(name, hidden) {
            var els = this.elementsByName(name, true);
            els.each(function(node) {
                var e = node.ancestor('.fitem', true);
                var label = null,
                    id = null;
                if (e) {
                    // Cope with differences between clean and boost themes.
                    if (e.hasClass('fitem_fgroup')) {
                        // Items within groups are not wrapped in div.fitem in theme_clean, so
                        // we need to hide the input, not the div.fitem.
                        e = node;
                    }

                    if (hidden) {
                        e.setAttribute('hidden', 'hidden');
                    } else {
                        e.removeAttribute('hidden');
                    }
                    e.setStyles({
                        display: (hidden) ? 'none' : ''
                    });

                    // Hide/unhide the label as well.
                    id = node.get('id');
                    if (id) {
                        label = Y.all('label[for="' + id + '"]');
                        if (label) {
                            if (hidden) {
                                label.setAttribute('hidden', 'hidden');
                            } else {
                                label.removeAttribute('hidden');
                            }
                            label.setStyles({
                                display: (hidden) ? 'none' : ''
                            });
                        }
                    }
                }
            });
        },
        /**
         * Is the form element inside a filepicker or filemanager?
         *
         * @param {String} el The form element name.
         * @return {Boolean}
         */
        isFilePicker: function(el) {
            if (!this._fileinputs) {
                var fileinputs = {};
                var selector = '.fitem [data-fieldtype="filepicker"] input,.fitem [data-fieldtype="filemanager"] input';
                var els = this.get('form').all(selector);
                els.each(function(node) {
                    fileinputs[node.getAttribute('name')] = true;
                });
                this._fileinputs = fileinputs;
            }

            if (({}).hasOwnProperty.call(this._fileinputs, el)) {
                return this._fileinputs[el] || false;
            }

            return false;
        },
        _dependencyNotchecked: function(elements, value, isHide) {
            var lock = false;
            elements.each(function() {
                if (this.getAttribute('type').toLowerCase() == 'hidden' &&
                        !this.siblings('input[type=checkbox][name="' + this.get('name') + '"]').isEmpty()) {
                    // This is the hidden input that is part of an advcheckbox.
                    return;
                }
                if (this.getAttribute('type').toLowerCase() == 'radio' && this.get('value') != value) {
                    return;
                }
                lock = lock || !Y.Node.getDOMNode(this).checked;
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        },
        _dependencyChecked: function(elements, value, isHide) {
            var lock = false;
            elements.each(function() {
                if (this.getAttribute('type').toLowerCase() == 'hidden' &&
                        !this.siblings('input[type=checkbox][name="' + this.get('name') + '"]').isEmpty()) {
                    // This is the hidden input that is part of an advcheckbox.
                    return;
                }
                if (this.getAttribute('type').toLowerCase() == 'radio' && this.get('value') != value) {
                    return;
                }
                lock = lock || Y.Node.getDOMNode(this).checked;
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        },
        _dependencyNoitemselected: function(elements, value, isHide) {
            var lock = false;
            elements.each(function() {
                lock = lock || this.get('selectedIndex') == -1;
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        },
        _dependencyEq: function(elements, value, isHide) {
            var lock = false;
            var hiddenVal = false;
            var options, v, selected, values;
            elements.each(function() {
                if (this.getAttribute('type').toLowerCase() == 'radio' && !Y.Node.getDOMNode(this).checked) {
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'hidden' &&
                        !this.siblings('input[type=checkbox][name="' + this.get('name') + '"]').isEmpty()) {
                    // This is the hidden input that is part of an advcheckbox.
                    hiddenVal = (this.get('value') == value);
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'checkbox' && !Y.Node.getDOMNode(this).checked) {
                    lock = lock || hiddenVal;
                    return;
                }
                if (this.getAttribute('class').toLowerCase() == 'filepickerhidden') {
                    // Check for filepicker status.
                    var elementname = this.getAttribute('name');
                    if (elementname && M.form_filepicker.instances[elementname].fileadded) {
                        lock = false;
                    } else {
                        lock = true;
                    }
                } else if (this.get('nodeName').toUpperCase() === 'SELECT' && this.get('multiple') === true) {
                    // Multiple selects can have one or more value assigned. A pipe (|) is used as a value separator
                    // when multiple values have to be selected at the same time.
                    values = value.split('|');
                    selected = [];
                    options = this.get('options');
                    options.each(function() {
                        if (this.get('selected')) {
                            selected[selected.length] = this.get('value');
                        }
                    });
                    if (selected.length > 0 && selected.length === values.length) {
                        for (var i in selected) {
                            v = selected[i];
                            if (values.indexOf(v) > -1) {
                                lock = true;
                            } else {
                                lock = false;
                                return;
                            }
                        }
                    } else {
                        lock = false;
                    }
                } else {
                    lock = lock || this.get('value') == value;
                }
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        },
        /**
         * Lock the given field if the field value is in the given set of values.
         *
         * @param {Array} elements
         * @param {String} values Single value or pipe (|) separated values when multiple
         * @returns {{lock: boolean, hide: boolean}}
         * @private
         */
        _dependencyIn: function(elements, values, isHide) {
            // A pipe (|) is used as a value separator
            // when multiple values have to be passed on at the same time.
            values = values.split('|');
            var lock = false;
            var hiddenVal = false;
            var options, v, selected, value;
            elements.each(function() {
                if (this.getAttribute('type').toLowerCase() == 'radio' && !Y.Node.getDOMNode(this).checked) {
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'hidden' &&
                        !this.siblings('input[type=checkbox][name="' + this.get('name') + '"]').isEmpty()) {
                    // This is the hidden input that is part of an advcheckbox.
                    hiddenVal = (values.indexOf(this.get('value')) > -1);
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'checkbox' && !Y.Node.getDOMNode(this).checked) {
                    lock = lock || hiddenVal;
                    return;
                }
                if (this.getAttribute('class').toLowerCase() == 'filepickerhidden') {
                    // Check for filepicker status.
                    var elementname = this.getAttribute('name');
                    if (elementname && M.form_filepicker.instances[elementname].fileadded) {
                        lock = false;
                    } else {
                        lock = true;
                    }
                } else if (this.get('nodeName').toUpperCase() === 'SELECT' && this.get('multiple') === true) {
                    // Multiple selects can have one or more value assigned.
                    selected = [];
                    options = this.get('options');
                    options.each(function() {
                        if (this.get('selected')) {
                            selected[selected.length] = this.get('value');
                        }
                    });
                    if (selected.length > 0 && selected.length === values.length) {
                        for (var i in selected) {
                            v = selected[i];
                            if (values.indexOf(v) > -1) {
                                lock = true;
                            } else {
                                lock = false;
                                return;
                            }
                        }
                    } else {
                        lock = false;
                    }
                } else {
                    value = this.get('value');
                    lock = lock || (values.indexOf(value) > -1);
                }
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        },
        _dependencyHide: function(elements, value) {
            return {
                lock: false,
                hide: true
            };
        },
        _dependencyDefault: function(elements, value, isHide) {
            var lock = false,
                hiddenVal = false,
                values
                ;
            elements.each(function() {
                var selected;
                if (this.getAttribute('type').toLowerCase() == 'radio' && !Y.Node.getDOMNode(this).checked) {
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'hidden' &&
                        !this.siblings('input[type=checkbox][name="' + this.get('name') + '"]').isEmpty()) {
                    // This is the hidden input that is part of an advcheckbox.
                    hiddenVal = (this.get('value') != value);
                    return;
                } else if (this.getAttribute('type').toLowerCase() == 'checkbox' && !Y.Node.getDOMNode(this).checked) {
                    lock = lock || hiddenVal;
                    return;
                }
                // Check for filepicker status.
                if (this.getAttribute('class').toLowerCase() == 'filepickerhidden') {
                    var elementname = this.getAttribute('name');
                    if (elementname && M.form_filepicker.instances[elementname].fileadded) {
                        lock = true;
                    } else {
                        lock = false;
                    }
                } else if (this.get('nodeName').toUpperCase() === 'SELECT' && this.get('multiple') === true) {
                    // Multiple selects can have one or more value assigned. A pipe (|) is used as a value separator
                    // when multiple values have to be selected at the same time.
                    values = value.split('|');
                    selected = [];
                    this.get('options').each(function() {
                        if (this.get('selected')) {
                            selected[selected.length] = this.get('value');
                        }
                    });
                    if (selected.length > 0 && selected.length === values.length) {
                        for (var i in selected) {
                            if (values.indexOf(selected[i]) > -1) {
                                lock = false;
                            } else {
                                lock = true;
                                return;
                            }
                        }
                    } else {
                        lock = true;
                    }
                } else {
                    lock = lock || this.get('value') != value;
                }
            });
            return {
                lock: lock,
                hide: isHide ? lock : false
            };
        }
    }, {
        NAME: 'mform-dependency-manager',
        ATTRS: {
            form: {
                setter: function(value) {
                    return Y.one('#' + value);
                },
                value: null
            },

            dependencies: {
                value: {}
            }
        }
    });

    M.form.dependencyManager = dependencyManager;
}

/**
 * Stores a list of the dependencyManager for each form on the page.
 */
M.form.dependencyManagers = {};

/**
 * Initialises a manager for a forms dependencies.
 * This should happen once per form.
 *
 * @param {YUI} Y YUI3 instance
 * @param {String} formid ID of the form
 * @param {Array} dependencies array
 * @return {M.form.dependencyManager}
 */
M.form.initFormDependencies = function(Y, formid, dependencies) {

    // If the dependencies isn't an array or object we don't want to
    // know about it
    if (!Y.Lang.isArray(dependencies) && !Y.Lang.isObject(dependencies)) {
        return false;
    }

    /**
     * Fixes an issue with YUI's processing method of form.elements property
     * in Internet Explorer.
     *     http://yuilibrary.com/projects/yui3/ticket/2528030
     */
    Y.Node.ATTRS.elements = {
        getter: function() {
            return Y.all(new Y.Array(this._node.elements, 0, true));
        }
    };

    M.form.dependencyManagers[formid] = new M.form.dependencyManager({form: formid, dependencies: dependencies});
    return M.form.dependencyManagers[formid];
};

/**
 * Update the state of a form. You need to call this after, for example, changing
 * the state of some of the form input elements in your own code, in order that
 * things like the disableIf state of elements can be updated.
 *
 * @param {String} formid ID of the form
 */
M.form.updateFormState = function(formid) {
    if (formid in M.form.dependencyManagers) {
        M.form.dependencyManagers[formid].updateAllDependencies();
    }
};
