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
 * This module allows to enhance the form elements MoodleQuickForm_filetypes
 *
 * @module     core_form/filetypes
 * @copyright  2017 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
define(['jquery', 'core/log', 'core/modal_events', 'core/modal_save_cancel', 'core/ajax',
        'core/templates', 'core/tree'],
    function($, Log, ModalEvents, ModalSaveCancel, Ajax, Templates, Tree) {

    "use strict";

    /**
     * Constructor of the FileTypes instances.
     *
     * @constructor
     * @param {String} elementId The id of the form element to enhance
     * @param {String} elementLabel The label of the form element used as the modal selector title
     * @param {String} onlyTypes Limit the list of offered types to this
     * @param {Bool} allowAll Allow presence of the "All file types" item
     */
    var FileTypes = function(elementId, elementLabel, onlyTypes, allowAll) {

        this.elementId = elementId;
        this.elementLabel = elementLabel;
        this.onlyTypes = onlyTypes;
        this.allowAll = allowAll;

        this.inputField = $('#' + elementId);
        this.wrapperBrowserTrigger = $('[data-filetypesbrowser="' + elementId + '"]');
        this.wrapperDescriptions = $('[data-filetypesdescriptions="' + elementId + '"]');

        if (!this.wrapperBrowserTrigger.length) {
            // This is a valid case. Most probably the element is frozen and
            // the filetypes browser should not be available.
            return;
        }

        if (!this.inputField.length || !this.wrapperDescriptions.length) {
            Log.error('core_form/filetypes: Unexpected DOM structure, unable to enhance filetypes field ' + elementId);
            return;
        }

        this.prepareBrowserTrigger()
            .then(function() {
                return this.prepareBrowserModal();
            }.bind(this))

            .then(function() {
                return this.prepareBrowserTree();
            }.bind(this));
    };

    /**
     * Create and set the browser trigger widget (this.browserTrigger).
     *
     * @method prepareBrowserTrigger
     * @returns {Promise}
     */
    FileTypes.prototype.prepareBrowserTrigger = function() {
        return Templates.render('core_form/filetypes-trigger', {})
            .then(function(html) {
                this.wrapperBrowserTrigger.html(html);
                this.browserTrigger = this.wrapperBrowserTrigger.find('[data-filetypeswidget="browsertrigger"]');
            }.bind(this));
    };

    /**
     * Create and set the modal for displaying the browser (this.browserModal).
     *
     * @method prepareBrowserModal
     * @returns {Promise}
     */
    FileTypes.prototype.prepareBrowserModal = function() {
        return ModalSaveCancel.create({
            title: this.elementLabel,
        })
        .then(function(modal) {
            this.browserModal = modal;
            return modal;
        }.bind(this))
        .then(function() {
            // Because we have custom conditional modal trigger, we need to
            // handle the focus after closing ourselves, too.
            this.browserModal.getRoot().on(ModalEvents.hidden, function() {
                this.browserTrigger.focus();
            }.bind(this));

            this.browserModal.getRoot().on(ModalEvents.save, function() {
                this.saveBrowserModal();
            }.bind(this));
        }.bind(this));

    };

    /**
     * Create and set the tree in the browser modal's body.
     *
     * @method prepareBrowserTree
     * @returns {Promise}
     */
    FileTypes.prototype.prepareBrowserTree = function() {

        this.browserTrigger.on('click', function(e) {
            e.preventDefault();

            // We want to display the browser modal only when the associated input
            // field is not frozen (disabled).
            if (this.inputField.is('[disabled]')) {
                return;
            }

            var bodyContent = this.loadBrowserModalBody();

            bodyContent.then(function() {

                // Turn the list of groups and extensions into the tree.
                this.browserTree = new Tree(this.browserModal.getBody());

                // Override the behaviour of the Enter and Space keys to toggle our checkbox,
                // rather than toggle the tree node expansion status.
                this.browserTree.handleKeyDown = function(item, e) {
                    if (e.keyCode == this.browserTree.keys.enter || e.keyCode == this.browserTree.keys.space) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleCheckbox(item.attr('data-filetypesbrowserkey'));
                    } else {
                        Tree.prototype.handleKeyDown.call(this.browserTree, item, e);
                    }
                }.bind(this);

                if (this.allowAll) {
                    // Hide all other items if "All file types" is enabled.
                    this.hideOrShowItemsDependingOnAllowAll(this.browserModal.getRoot()
                        .find('input[type="checkbox"][data-filetypesbrowserkey="*"]').first());
                    // And do the same whenever we click that checkbox.
                    this.browserModal.getRoot().on('change', 'input[type="checkbox"][data-filetypesbrowserkey="*"]', function(e) {
                        this.hideOrShowItemsDependingOnAllowAll($(e.currentTarget));
                    }.bind(this));
                }

                // Synchronize checked status if the file extension is present in multiple groups.
                this.browserModal.getRoot().on('change', 'input[type="checkbox"][data-filetypesbrowserkey]', function(e) {
                    var checkbox = $(e.currentTarget);
                    var key = checkbox.attr('data-filetypesbrowserkey');
                    this.browserModal.getRoot().find('input[type="checkbox"][data-filetypesbrowserkey="' + key + '"]')
                        .prop('checked', checkbox.prop('checked'));
                }.bind(this));

            }.bind(this))

            .then(function() {
                this.browserModal.show();
            }.bind(this));

            this.browserModal.setBody(bodyContent);

        }.bind(this));

        // Return a resolved promise.
        return $.when();
    };

    /**
     * Load the browser modal body contents.
     *
     * @returns {Promise}
     */
    FileTypes.prototype.loadBrowserModalBody = function() {

        var args = {
            onlytypes: this.onlyTypes.join(),
            allowall: this.allowAll,
            current: this.inputField.val()
        };

        return Ajax.call([{
            methodname: 'core_form_get_filetypes_browser_data',
            args: args

        }])[0].then(function(browserData) {
            return Templates.render('core_form/filetypes-browser', {
                elementid: this.elementId,
                groups: browserData.groups
            });
        }.bind(this));
    };

    /**
     * Change the checked status of the given file type (group or extension).
     *
     * @method toggleCheckbox
     * @param {String} key
     */
    FileTypes.prototype.toggleCheckbox = function(key) {

        var checkbox = this.browserModal.getRoot().find('input[type="checkbox"][data-filetypesbrowserkey="' + key + '"]').first();

        checkbox.prop('checked', !checkbox.prop('checked'));
    };

    /**
     * Update the associated input field with selected file types.
     *
     * @method saveBrowserModal
     */
    FileTypes.prototype.saveBrowserModal = function() {

        // Check the "All file types" first.
        if (this.allowAll) {
            var allcheckbox = this.browserModal.getRoot().find('input[type="checkbox"][data-filetypesbrowserkey="*"]');
            if (allcheckbox.length && allcheckbox.prop('checked')) {
                this.inputField.val('*');
                this.updateDescriptions(['*']);
                return;
            }
        }

        // Iterate over all checked boxes and populate the list.
        var newvalue = [];

        this.browserModal.getRoot().find('input[type="checkbox"]').each(/** @this represents the checkbox */ function() {
            var checkbox = $(this);
            var key = checkbox.attr('data-filetypesbrowserkey');

            if (checkbox.prop('checked')) {
                newvalue.push(key);
            }
        });

        // Remove duplicates (e.g. file types present in multiple groups).
        newvalue = newvalue.filter(function(x, i, a) {
            return a.indexOf(x) == i;
        });

        this.inputField.val(newvalue.join(' '));
        this.updateDescriptions(newvalue);
    };

    /**
     * Describe the selected filetypes in the form when saving the browser.
     *
     * @param {Array} keys List of keys to describe
     * @returns {Promise}
     */
    FileTypes.prototype.updateDescriptions = function(keys) {

        var descriptions = [];

        keys.forEach(function(key) {
            descriptions.push({
                description: this.browserModal.getRoot().find('[data-filetypesname="' + key + '"]').first().text().trim(),
                extensions: this.browserModal.getRoot().find('[data-filetypesextensions="' + key + '"]').first().text().trim()
            });
        }.bind(this));

        var templatedata = {
            hasdescriptions: (descriptions.length > 0),
            descriptions: descriptions
        };

        return Templates.render('core_form/filetypes-descriptions', templatedata)
            .then(function(html) {
                this.wrapperDescriptions.html(html);
            }.bind(this));
    };

    /**
     * If "All file types" is checked, all other browser items are made hidden, and vice versa.
     *
     * @param {jQuery} allcheckbox The "All file types" checkbox.
     */
    FileTypes.prototype.hideOrShowItemsDependingOnAllowAll = function(allcheckbox) {
        var others = this.browserModal.getRoot().find('[role="treeitem"][data-filetypesbrowserkey!="*"]');
        if (allcheckbox.prop('checked')) {
            others.hide();
        } else {
            others.show();
        }
    };

    return {
        init: function(elementId, elementLabel, onlyTypes, allowAll) {
            new FileTypes(elementId, elementLabel, onlyTypes, allowAll);
        }
    };
});
