// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Password Unmask functionality.
 *
 * @module     core_form/passwordunmask
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/templates'], function($, Template) {

    /**
     * Constructor for PasswordUnmask.
     *
     * @class core_form/passwordunmask
     * @param   {String}    elementid   The element to apply the PasswordUnmask to
     */
    var PasswordUnmask = function(elementid) {
        // Setup variables.
        this.wrapperSelector = '[data-passwordunmask="wrapper"][data-passwordunmaskid="' + elementid + '"]';
        this.wrapper = $(this.wrapperSelector);
        this.editorSpace = this.wrapper.find('[data-passwordunmask="editor"]');
        this.editLink = this.wrapper.find('a[data-passwordunmask="edit"]');
        this.editInstructions = this.wrapper.find('[data-passwordunmask="instructions"]');
        this.displayValue = this.wrapper.find('[data-passwordunmask="displayvalue"]');
        this.inputFieldLabel = $('label[for="' + elementid + '"]');

        this.inputField = this.editorSpace.find(document.getElementById(elementid));
        // Hide the field.
        this.inputField.addClass('d-none');
        this.inputField.removeClass('hiddenifjs');

        if (!this.editInstructions.attr('id')) {
            this.editInstructions.attr('id', elementid + '_instructions');
        }
        this.editInstructions.hide();

        this.setDisplayValue();

        // Add the listeners.
        this.addListeners();
    };

    /**
     * Add the event listeners required for PasswordUnmask.
     *
     * @method  addListeners
     * @return  {PasswordUnmask}
     * @chainable
     */
    PasswordUnmask.prototype.addListeners = function() {
        this.wrapper.on('click keypress', '[data-passwordunmask="edit"]', $.proxy(function(e) {
            if (e.type === 'keypress' && e.keyCode !== 13) {
                return;
            }
            e.stopImmediatePropagation();
            e.preventDefault();

            if (this.isEditing()) {
                // Only focus on the edit link if the event was not a click, and the new target is not an input field.
                if (e.type !== 'click' && !$(e.relatedTarget).is(':input')) {
                    this.turnEditingOff(true);
                } else {
                    this.turnEditingOff(false);
                }
            } else {
                this.turnEditingOn();
            }
        }, this));

        this.wrapper.on('click keypress', '[data-passwordunmask="unmask"]', $.proxy(function(e) {
            if (e.type === 'keypress' && e.keyCode !== 13) {
                return;
            }
            e.stopImmediatePropagation();
            e.preventDefault();

            // Toggle the data attribute.
            this.wrapper.data('unmasked', !this.wrapper.data('unmasked'));

            this.setDisplayValue();
        }, this));

        this.wrapper.on('keydown', 'input', $.proxy(function(e) {
            if (e.type === 'keydown' && e.keyCode !== 13) {
                return;
            }

            e.stopImmediatePropagation();
            e.preventDefault();

            this.turnEditingOff(true);
        }, this));

        this.inputFieldLabel.on('click', $.proxy(function(e) {
            e.preventDefault();

            this.turnEditingOn();
        }, this));

        return this;
    };

    /**
     * Check whether focus was lost from the PasswordUnmask and turn editing off if required.
     *
     * @method  checkFocusOut
     * @param   {EventFacade}   e       The EventFacade generating the suspsected Focus Out
     */
    PasswordUnmask.prototype.checkFocusOut = function(e) {
        if (!this.isEditing()) {
            // Ignore - not editing.
            return;
        }

        window.setTimeout($.proxy(function() {
            // Firefox does not have the focusout event. Instead jQuery falls back to the 'blur' event.
            // The blur event does not have a relatedTarget, so instead we use a timeout and the new activeElement.
            var relatedTarget = e.relatedTarget || document.activeElement;
            if (this.wrapper.has($(relatedTarget)).length) {
                // Ignore, some part of the element is still active.
                return;
            }

            // Only focus on the edit link if the new related target is not an input field or anchor.
            this.turnEditingOff(!$(relatedTarget).is(':input,a'));
        }, this), 100);
    };

    /**
     * Whether the password is currently visible (unmasked).
     *
     * @method  passwordVisible
     * @return  {Boolean}            True if the password is unmasked
     */
    PasswordUnmask.prototype.passwordVisible = function() {
        return !!this.wrapper.data('unmasked');
    };

    /**
     * Whether the user is currently editing the field.
     *
     * @method  isEditing
     * @return  {Boolean}            True if edit mode is enabled
     */
    PasswordUnmask.prototype.isEditing = function() {
        return this.inputField.hasClass('d-inline-block');
    };

    /**
     * Enable the editing functionality.
     *
     * @method  turnEditingOn
     * @return  {PasswordUnmask}
     * @chainable
     */
    PasswordUnmask.prototype.turnEditingOn = function() {
        var value = this.getDisplayValue();
        if (this.passwordVisible()) {
            this.inputField.attr('type', 'text');
        } else {
            this.inputField.attr('type', 'password');
        }
        this.inputField.val(value);
        this.inputField.attr('size', this.inputField.attr('data-size'));
        // Show the field.
        this.inputField.addClass('d-inline-block');

        if (this.editInstructions.length) {
            this.inputField.attr('aria-describedby', this.editInstructions.attr('id'));
            this.editInstructions.show();
        }

        this.wrapper.attr('data-passwordunmask-visible', 1);

        this.editLink.hide();
        this.inputField
            .focus()
            .select();

        // Note, this cannot be added as a delegated listener on init because Firefox does not support the FocusOut
        // event (https://bugzilla.mozilla.org/show_bug.cgi?id=687787) and the blur event does not identify the
        // relatedTarget.
        // The act of focusing the this.inputField means that in Firefox the focusout will be triggered on blur of the edit
        // link anchor.
        $('body').on('focusout', this.wrapperSelector, $.proxy(this.checkFocusOut, this));

        return this;
    };

    /**
     * Disable the editing functionality, optionally focusing on the edit link.
     *
     * @method  turnEditingOff
     * @param   {Boolean}       focusOnEditLink     Whether to focus on the edit link after disabling the editor
     * @return  {PasswordUnmask}
     * @chainable
     */
    PasswordUnmask.prototype.turnEditingOff = function(focusOnEditLink) {
        $('body').off('focusout', this.wrapperSelector, this.checkFocusOut);
        var value = this.getDisplayValue();
        this.inputField
            // Ensure that the aria-describedby is removed.
            .attr('aria-describedby', null);
        this.inputField.val(value);
        // Hide the field again.
        this.inputField.removeClass('d-inline-block');

        this.editInstructions.hide();

        // Remove the visible attr.
        this.wrapper.removeAttr('data-passwordunmask-visible');

        // Remove the size attr.
        this.inputField.removeAttr('size');

        this.editLink.show();
        this.setDisplayValue();

        if (focusOnEditLink) {
            this.editLink.focus();
        }

        return this;
    };

    /**
     * Get the currently value.
     *
     * @method  getDisplayValue
     * @return  {String}
     */
    PasswordUnmask.prototype.getDisplayValue = function() {
        return this.inputField.val();
    };

    /**
     * Set the currently value in the display, taking into account the current settings.
     *
     * @method  setDisplayValue
     * @return  {PasswordUnmask}
     * @chainable
     */
    PasswordUnmask.prototype.setDisplayValue = function() {
        var value = this.getDisplayValue();
        if (this.isEditing()) {
            if (this.wrapper.data('unmasked')) {
                this.inputField.attr('type', 'text');
            } else {
                this.inputField.attr('type', 'password');
            }
            this.inputField.val(value);
        }

        // Update the display value.
        // Note: This must always be updated.
        // The unmask value can be changed whilst editing and the editing can then be disabled.
        if (value && this.wrapper.data('unmasked')) {
            // There is a value, and we will show it.
            this.displayValue.text(value);
        } else {
            if (!value) {
                value = "";
            }
            // There is a value, but it will be disguised.
            // We use the passwordunmask-fill to allow modification of the fill and to ensure that the display does not
            // change as the page loads the JS.
            Template.render('core_form/element-passwordunmask-fill', {
                element: {
                    frozen:     this.inputField.is('[readonly]'),
                    value:      value,
                    valuechars: value.split(''),
                },
            }).done($.proxy(function(html, js) {
                this.displayValue.html(html);

                Template.runTemplateJS(js);
            }, this));
        }

        return this;
    };

    return PasswordUnmask;
});
