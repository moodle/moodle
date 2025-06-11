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
 * @package     atto_panoptoltibutton
 * @copyright   2020 Panopto
 * @license     GPL v3
 */


/**
 * @module moodle-atto_panoptoltibutton-button
 */


/**
 * Atto text editor Panopto LTI plugin
 *
 * @namespace M.atto_panoptoltibutton
 * @class     Button
 * @extends    M.editor_atto.EditorPlugin
 */


require(["core/str"], function (str) {
    let errorMessage = null;
    const stringPromise = str.get_string(
        "erroroccurred",
        "atto_panoptoltibutton"
    );

    stringPromise
        .then(function (invalid) {
            errorMessage = invalid;
        })
        .catch(function (error) {
            console.error("Error loading string:", error);
        });

    document.CALLBACKS = {
        handleError: function (errors) {
            alert(errorMessage);
            for (let i = 0; i < errors.length; i++) {
                console.error(errors[i]);
            }
        },
    };
});


Y.namespace('M.atto_panoptoltibutton').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    _CONTENT_ITEM_SELECTION_URL: M.cfg.wwwroot + '/lib/editor/atto/plugins/panoptoltibutton/contentitem.php',

    _panel: null,

    _addTool: function (event, tool) {
        event.preventDefault();
        var resourceLinkId = this._createResourceLinkId(),
            host = this.get('host'),
            panel,
            courseid = this._course,
            isResponsive = this._isResponsive;

        document.CALLBACKS['f' + resourceLinkId] = function (contentItemData) {
            if (!contentItemData) {
                return;
            }

            for (var i = 0; i < contentItemData['@graph'].length; i++) {
                var item = contentItemData['@graph'][i];
                var strategyFactory = new Y.M.atto_panoptoltibutton.PlacementStrategyFactory();
                var strategy = strategyFactory.strategyFor(item, courseid, resourceLinkId, tool, isResponsive);
                var render = strategy.toHtml;
                host.insertContentAtFocusPoint(render(item));
            }
            host.saveSelection();
            host.updateOriginal();
            panel.hide();
            panel.destroy();
        };

        this._panel = new M.core.dialogue({
            bodyContent: '<iframe src="' + this._CONTENT_ITEM_SELECTION_URL +
                '?course=' + this._course.id +
                '&id=' + tool.id +
                '&callback=f' + resourceLinkId +
                '" width="100%" height="100%"></iframe>',
            headerContent: tool.name,
            width: '67%',
            height: '66%',
            draggable: false,
            visible: true,
            zindex: 100,
            modal: true,
            focusOnPreviousTargetAfterHide: true,
            render: true
        });

        this._panel.after('visibleChange', this._panel.destroy, this);
        panel = this._panel;

    },

    initializer: function (args) {
        // If we don't have tool or capability is disabled, just quit.
        if (!args.toolTypes || args.toolTypes.length === 0 || args.disabled) {
            return;
        }

        this._course = args.course;
        this._isResponsive = args.isResponsive;

        this._createResourceLinkId = (function (base) {
            return function () {
                return base + '_' + (new Date()).getTime();
            };
        }(args.resourcebase));

        if (args.toolTypes.length > 1) {
             this.addToolbarMenu({
                 icon: "ed/iconone",
                 iconComponent: "atto_panoptoltibutton",

                 globalItemConfig: {
                     callback: this._addTool,
                 },

                 items: args.toolTypes.map(function (args) {
                     return {
                         text: args.name,
                         callbackArgs: args,
                     };
                 }),
             });
        } else if (args.toolTypes.length === 1) {
            // Code to add a single button for one tool
            this.addButton({
                icon: "ed/iconone",
                iconComponent: "atto_panoptoltibutton",
                text: args.toolTypes[0].name ?? 'Panopto LTI',
                callback: this._addTool,
                callbackArgs: args.toolTypes[0],
            });
        }
    }
});