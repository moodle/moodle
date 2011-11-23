/**
 * This file contains JS functionality required by mforms and is included automatically
 * when required.
 */

// Namespace for the form bits and bobs
M.form = M.form || {};

/**
 * Initialises the show advanced functionality and events.
 * This should only ever happen ONCE per page.
 *
 * @param {YUI} Y
 * @param {object} config
 */
M.form.initShowAdvanced = function(Y, config) {
    if (M.form.showAdvanced) {
        return M.form.showAdvanced;
    }
    var showAdvanced = function(config) {
        showAdvanced.superclass.constructor.apply(this, arguments);
    };
    showAdvanced.prototype = {
        _advButtons : [],
        _advAreas : [],
        _stateInput : null,
        initializer : function() {
            this._advAreas = Y.all('form .advanced');
            this._advButtons = Y.all('.showadvancedbtn');
            if (this._advButtons.size() > 0) {
                this._stateInput = new Y.NodeList(document.getElementsByName('mform_showadvanced_last'));
                this._advButtons.on('click', this.switchState, this);
                this._advButtons.set('type', 'button');
            }
        },
        /**
         * Toggles between showing advanced items and hiding them.
         * Should be fired by an event.
         */
        switchState : function(e) {
            e.preventDefault();
            if (this._stateInput.get('value')=='1') {
                this._stateInput.set('value', '0');
                this._advButtons.setAttribute('value', M.str.form.showadvanced);
                this._advAreas.addClass('hide');
            } else {
                this._stateInput.set('value', '1');
                this._advButtons.setAttribute('value', M.str.form.hideadvanced);
                this._advAreas.removeClass('hide');
            }
        }
    };
    // Extend it with the YUI widget fw.
    Y.extend(showAdvanced, Y.Base, showAdvanced.prototype, {
        NAME : 'mform-showAdvanced'
    });
    M.form.showAdvanced = new showAdvanced(config);
    return M.form.showAdvanced;
};

/**
 * Initialises a manager for a forms dependencies.
 * This should happen once per form.
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

    // Define the dependency manager if it hasn't already been defined.
    M.form.dependencyManager = M.form.dependencyManager || (function(){
        var dependencyManager = function(config) {
            dependencyManager.superclass.constructor.apply(this, arguments);
        };
        dependencyManager.prototype = {
            _form : null,
            _depElements : [],
            _nameCollections : [],
            initializer : function(config) {
                var i = 0, nodeName;
                this._form = Y.one('#'+formid);
                for (i in dependencies) {
                    this._depElements[i] = this.elementsByName(i);
                    if (this._depElements[i].size() == 0) {
                        continue;
                    }
                    this._depElements[i].each(function(node){
                        nodeName = node.get('nodeName').toUpperCase();
                        if (nodeName == 'INPUT') {
                            if (node.getAttribute('type').match(/^(button|submit|radio|checkbox)$/)) {
                                node.on('click', this.checkDependencies, this);
                            } else {
                                node.on('blur', this.checkDependencies, this);
                            }
                            node.on('change', this.checkDependencies, this);
                        } else if (nodeName == 'SELECT') {
                            node.on('change', this.checkDependencies, this);
                        } else {
                            node.on('click', this.checkDependencies, this);
                            node.on('blur', this.checkDependencies, this);
                            node.on('change', this.checkDependencies, this);
                        }
                    }, this);
                }
                this._form.get('elements').each(function(input){
                    if (input.getAttribute('type')=='reset') {
                        input.on('click', function(){
                            this._form.reset();
                            this.checkDependencies();
                        }, this);
                    }
                }, this);

                return this.checkDependencies(null);
            },
            /**
             * Gets all elements in the form by thier name and returns
             * a YUI NodeList
             * @return Y.NodeList
             */
            elementsByName : function(name) {
                if (!this._nameCollections[name]) {
                    var elements = [];
                    this._form.get('elements').each(function(){
                        if (this.getAttribute('name') == name) {
                            elements.push(this);
                        }
                    });
                    this._nameCollections[name] = new Y.NodeList(elements);
                }
                return this._nameCollections[name];
            },
            /**
             * Checks the dependencies the form has an makes any changes to the
             * form that are required.
             *
             * Changes are made by functions title _dependency_{dependencytype}
             * and more can easily be introduced by defining further functions.
             */
            checkDependencies : function(e) {
                var tolock = [],
                    tohide = [],
                    dependon, condition, value,
                    lock, hide, checkfunction, result;
                for (dependon in dependencies) {
                    if (this._depElements[dependon].size() == 0) {
                        continue;
                    }
                    for (condition in dependencies[dependon]) {
                        for (value in dependencies[dependon][condition]) {
                            lock = false;
                            hide = false;
                            checkfunction = '_dependency_'+condition;
                            if (Y.Lang.isFunction(this[checkfunction])) {
                                result = this[checkfunction].apply(this, [this._depElements[dependon], value, e]);
                            } else {
                                result = this._dependency_default(this._depElements[dependon], value, e);
                            }
                            lock = result.lock || false;
                            hide = result.hide || false;
                            for (var ei in dependencies[dependon][condition][value]) {
                                var eltolock = dependencies[dependon][condition][value][ei];
                                if (hide) {
                                    tohide[eltolock] = true;
                                }
                                if (tolock[eltolock] != null) {
                                    tolock[eltolock] = lock || tolock[eltolock];
                                } else {
                                    tolock[eltolock] = lock;
                                }
                            }
                        }
                    }
                }
                for (var el in tolock) {
                    this._disableElement(el, tolock[el]);
                    if (tohide.propertyIsEnumerable(el)) {
                        this._hideElement(el, tohide[el]);
                    }
                }
                return true;
            },
            /**
             * Disabled all form elements with the given name
             */
            _disableElement : function(name, disabled) {
                var els = this.elementsByName(name);
                var form = this;
                els.each(function(){
                    if (disabled) {
                        this.setAttribute('disabled', 'disabled');
                    } else {
                        this.removeAttribute('disabled');
                    }

                    // Extra code to disable a filepicker
                    if (this.getAttribute('class') == 'filepickerhidden'){
                        var pickerbuttons = form.elementsByName(name + 'choose');
                        pickerbuttons.each(function(){
                            if (disabled){
                                this.setAttribute('disabled','disabled');
                            } else {
                                this.removeAttribute('disabled');
                            }
                        });
                    }
                })
            },
            /**
             * Hides all elements with the given name.
             */
            _hideElement : function(name, hidden) {
                var els = this.elementsByName(name);
                els.each(function(){
                    var e = els.ancestor('.fitem');
                    if (e) {
                        e.setStyles({
                            display : (hidden)?'none':''
                        })
                    }
                });
            },
            _dependency_notchecked : function(elements, value) {
                var lock = false;
                elements.each(function(){
                    if (this.getAttribute('type').toLowerCase()=='hidden' && Y.Node.getDOMNode(this).ancestor('div.felement.fcheckbox')) {
                        // This is the hidden input that is part of an advcheckbox.
                        return;
                    }
                    if (this.getAttribute('type').toLowerCase()=='radio' && this.get('value') != value) {
                        return;
                    }
                    lock = lock || !Y.Node.getDOMNode(this).checked;
                });
                return {
                    lock : lock,
                    hide : false
                }
            },
            _dependency_checked : function(elements, value) {
                var lock = false;
                elements.each(function(){
                    if (this.getAttribute('type').toLowerCase()=='hidden' && Y.Node.getDOMNode(this).ancestor('div.felement.fcheckbox')) {
                        // This is the hidden input that is part of an advcheckbox.
                        return;
                    }
                    if (this.getAttribute('type').toLowerCase()=='radio' && this.get('value') != value) {
                        return;
                    }
                    lock = lock || Y.Node.getDOMNode(this).checked;
                });
                return {
                    lock : lock,
                    hide : false
                }
            },
            _dependency_noitemselected : function(elements, value) {
                var lock = false;
                elements.each(function(){
                    lock = lock || this.get('selectedIndex') == -1;
                });
                return {
                    lock : lock,
                    hide : false
                }
            },
            _dependency_eq : function(elements, value) {
                var lock = false;
                var hidden_val = false;
                elements.each(function(){
                    if (this.getAttribute('type').toLowerCase()=='radio' && !Y.Node.getDOMNode(this).checked) {
                        return;
                    } else if (this.getAttribute('type').toLowerCase() == 'hidden' && Y.Node.getDOMNode(this).ancestor('div.felement.fcheckbox')) {
                        // This is the hidden input that is part of an advcheckbox.
                        hidden_val = (this.get('value') == value);
                        return;
                    } else if (this.getAttribute('type').toLowerCase() == 'checkbox' && !Y.Node.getDOMNode(this).checked) {
                        lock = lock || hidden_val;
                        return;
                    }
                    //check for filepicker status
                    if (this.getAttribute('class').toLowerCase() == 'filepickerhidden') {
                        var elementname = this.getAttribute('name');
                        if (elementname && M.form_filepicker.instances[elementname].fileadded) {
                            lock = false;
                        } else {
                            lock = true;
                        }
                    } else {
                        lock = lock || this.get('value') == value;
                    }
                });
                return {
                    lock : lock,
                    hide : false
                }
            },
            _dependency_hide : function(elements, value) {
                return {
                    lock : false,
                    hide : true
                }
            },
            _dependency_default : function(elements, value, ev) {
                var lock = false;
                var hidden_val = false;
                elements.each(function(){
                    if (this.getAttribute('type').toLowerCase()=='radio' && !Y.Node.getDOMNode(this).checked) {
                        return;
                    } else if (this.getAttribute('type').toLowerCase() == 'hidden' && Y.Node.getDOMNode(this).ancestor('div.felement.fcheckbox')) {
                        // This is the hidden input that is part of an advcheckbox.
                        hidden_val = (this.get('value') != value);
                        return;
                    } else if (this.getAttribute('type').toLowerCase() == 'checkbox' && !Y.Node.getDOMNode(this).checked) {
                        lock = lock || hidden_val;
                        return;
                    }
                    //check for filepicker status
                    if (this.getAttribute('class').toLowerCase() == 'filepickerhidden') {
                        var elementname = this.getAttribute('name');
                        if (elementname && M.form_filepicker.instances[elementname].fileadded) {
                            lock = true;
                        } else {
                            lock = false;
                        }
                    } else {
                        lock = lock || this.get('value') != value;
                    }
                });
                return {
                    lock : lock,
                    hide : false
                }
            }
        };
        Y.extend(dependencyManager, Y.Base, dependencyManager.prototype, {
            NAME : 'mform-dependency-manager'
        });

        return dependencyManager;
    })();

    return new M.form.dependencyManager();
};