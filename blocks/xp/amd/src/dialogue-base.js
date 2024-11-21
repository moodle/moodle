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
 * Generic dialogue base.
 *
 * This is originally a copy of block_stash/dialogue-base.
 *
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/yui', 'core/str'], function($, Y, Str) {
    /**
     * Constructor.
     *
     * Override it to inject your arguments.
     */
    function DialogueBase() {
        this._eventNode = $('<div>');
        var deferred = $.Deferred();
        this._ready = deferred.promise();
        Y.use(
            'moodle-core-notification',
            function() {
                this._init().then(function() {
                    deferred.resolve();
                });
            }.bind(this)
        );
    }

    DialogueBase.prototype._dialogue = null;
    DialogueBase.prototype._eventNode = null;
    DialogueBase.prototype._ready = null;
    DialogueBase.prototype._title = '&hellip;';

    /**
     * Center the dialogue on screen.
     */
    DialogueBase.prototype.center = function() {
        this._dialogue.centerDialogue();
    };

    /**
     * Initialise the things.
     *
     * @return {Void}
     */
    DialogueBase.prototype._init = function() {
        var deferred = $.Deferred(),
            loading = Y.Node.create('<p style="text-align: center;"><img src="' + M.util.image_url('y/loading') + '" alt=""></p>'),
            d;

        // New dialogue.
        d = new M.core.dialogue({
            draggable: true,
            modal: true,
            width: '600px'
        });
        this._dialogue = d;

        // Destroy on hide.
        var origHide = d.hide;
        d.hide = function() {
            origHide.apply(d, arguments);
            this.destroy();
        }.bind(d);

        // Set content.
        d.getStdModNode(Y.WidgetStdMod.HEADER).prepend(Y.Node.create('<h1>'));
        this._updateDialogueTitle();
        this._setDialogueContent(loading);
        deferred.resolve();

        // Render the things.
        this._render().fail(
            function() {
                Str.get_string('error', 'core')
                    .then(function(a) {
                        return a;
                    })
                    .fail(function() {
                        return '';
                    })
                    .always(
                        function(txt) {
                            this._setDialogueContent(txt);
                        }.bind(this)
                    );
            }.bind(this)
        );

        // Return the promise.
        return deferred.promise();
    };

    DialogueBase.prototype.close = function() {
        this.trigger('dialogue-closed');
        this._dialogue.destroy();
        this._eventNode = null;
    };

    /**
     * Find a node in this Dialog.
     *
     * @param {String} selector The selector.
     * @return {jQuery}
     */
    DialogueBase.prototype.find = function(selector) {
        return $(this._dialogue.getStdModNode(Y.WidgetStdMod.BODY).getDOMNode()).find(selector);
    };

    /**
     * Registers an event listener.
     *
     * @param {String} type The event type.
     * @param {Function} callback The callback, receives Event and extraArgs.
     */
    DialogueBase.prototype.on = function(type, callback) {
        this._eventNode.on(type, callback);
    };

    /**
     * Render mechanics.
     *
     * This is the method to override to set the content of the dialogue.
     * It must be non-blocking. Also do not forget to center the dialogue.
     */
    DialogueBase.prototype._render = function() {
        // Nothing here.
    };

    /**
     * Initialise the things.
     *
     * @param {Event} e The event.
     */
    DialogueBase.prototype.show = function(e) {
        this._ready.then(
            function() {
                this._dialogue.show(e);
            }.bind(this)
        );
    };

    /**
     * Set the dialogue content.
     *
     * @param {String} content The HTML content.
     */
    DialogueBase.prototype._setDialogueContent = function(content) {
        this._dialogue.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
    };

    /**
     * Set the dialogue title.
     *
     * @param {String} title The title.
     */
    DialogueBase.prototype.setTitle = function(title) {
        this._title = title;
        this._updateDialogueTitle();
    };

    /**
     * Triggers an event.
     *
     * @param {String} type The event type.
     * @param {Mixed} extraArgs The extra argument.
     */
    DialogueBase.prototype.trigger = function(type, extraArgs) {
        this._eventNode.trigger(type, extraArgs);
    };

    /**
     * Update the dialogue title.
     */
    DialogueBase.prototype._updateDialogueTitle = function() {
        if (!this._dialogue) {
            return;
        }

        this._dialogue
            .getStdModNode(Y.WidgetStdMod.HEADER)
            .one('h1')
            .setHTML(this._title);
    };

    return DialogueBase;
});
