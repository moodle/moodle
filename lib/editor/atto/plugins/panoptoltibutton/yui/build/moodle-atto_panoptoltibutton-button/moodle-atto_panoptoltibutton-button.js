YUI.add('moodle-atto_panoptoltibutton-button', function (Y, NAME) {

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
 * Atto text editor LTI activities plugin
 *
 * @namespace M.atto_panoptoltibutton
 * @class     Button
 * @extends   M.editor_atto.EditorPlugin
 */


Y.namespace('M.atto_panoptoltibutton').PlacementStrategyFactory = function () {

    this.strategyFor = function (item, course, resourceLinkId, tool, isResponsive) {

        var StrategyClass = Y.M.atto_panoptoltibutton.EmbeddedContentRenderingStrategy;

        if (   item.mediaType === 'application/vnd.ims.lti.v1.ltilink'
               || item.mediaType === 'application\\/vnd.ims.lti.v1.ltilink'
               || item.placementAdvice) {
            StrategyClass = Y.M.atto_panoptoltibutton.IframeRenderingStrategy;

            if (item.placementAdvice || item.iframe) {
                let presentationTarget = item.placementAdvice?.presentationDocumentTarget
                    ? item.placementAdvice.presentationDocumentTarget
                    : item.thumbnail
                        ? "frame"
                        : "iframe";

                switch (presentationTarget) {
                    case 'iframe':
                        StrategyClass = Y.M.atto_panoptoltibutton.IframeRenderingStrategy;
                        break;
                    case 'embed':
                    case 'frame':
                    case 'window':
                    case 'popup':
                    case 'overlay':
                        StrategyClass = Y.M.atto_panoptoltibutton.EmbeddedContentRenderingStrategy;
                        break;
                    default:
                        alert('Unsupported presentation target: '
                                + item.placementAdvice.presentationDocumentTarget);
                        break;
                }
            }
        }

        var strategy = new StrategyClass(item, course, resourceLinkId, tool, isResponsive);

        return strategy;
    };
};

Y.namespace('M.atto_panoptoltibutton').EmbeddedContentRenderingStrategy = function (item,
        course, resourceLinkId, tool, isResponsive) {

    var mimeTypePieces = item.mediaType.split("/"),
        mimeTypeType = mimeTypePieces[0],
        defaultHeight = "250px",
        defaultThumbnailWidth = 128,
        defaultThumbnailHeight = 72,
        titleHeight = defaultThumbnailHeight,
        titleWidth = null,
        textHeight = (parseInt(defaultHeight) - parseInt(defaultThumbnailHeight)) + "px",
        TEMPLATES,
        content;

    // In this case there is no text/html being sent, just a title and possible thumbnail, reduce height.
    if (!item.text || !item.text.length) {
        defaultHeight = defaultThumbnailHeight + "px";
    }

    if (!item.displayHeight) {
        item.displayHeight = defaultHeight;
    }

    var thumbnailId = '';
    if (item.thumbnail) {
        if (!item.thumbnail.width) {
            item.thumbnail.width = defaultThumbnailWidth;
        }

        if (!item.thumbnail.height) {
            item.thumbnail.height = defaultThumbnailHeight;
        }

        if (item.displayWidth) {
            // The extra 5px is for a margin to the right of the thumbnail
            titleWidth = (parseInt(item.displayWidth) - parseInt(item.thumbnail.width) - 5) + "px";
        }

        titleHeight = parseInt(item.thumbnail.height) + "px";

        // LTI 1.3 sends thumbnail id as @id.
        thumbnailId = item.thumbnail.id ? item.thumbnail.id : item.thumbnail["@id"];
    }


    TEMPLATES = {
        ltiLink: Y.Handlebars.compile(`
            <iframe src="${M.cfg.wwwroot}/lib/editor/atto/plugins/panoptoltibutton/view.php?custom={{custom}}
                &course={{course.id}}&ltitypeid={{toolid}}&resourcelinkid={{resourcelinkid}}
                {{#if item.url}}&contenturl={{item.url}}{{/if}}"
                {{#if isResponsive}}
                style="width: 100%; height: auto; aspect-ratio: 16 / 9;"
                {{else}}
                {{#if item.placementAdvice.width}} width="{{item.placementAdvice.displayWidth}}"{{/if}}
                {{#if item.placementAdvice.height}} height="{{item.placementAdvice.displayHeight}}"{{/if}}
                {{/if}}
                allowfullscreen="true">
            </iframe>
        `),
        link: Y.Handlebars.compile('<div style="'
                    + (item.displayWidth ? 'width:{{item.displayWidth}};' : '')
                    + 'height:{{titleHeight}};">'
                    + '<a href="' + M.cfg.wwwroot + '/lib/editor/atto/plugins/panoptoltibutton/view.php?custom={{custom}}&'
                    + 'course={{course.id}}&ltitypeid={{toolid}}&resourcelinkid={{resourcelinkid}}'
                    + '{{#if item.url}}&contenturl={{item.url}}{{/if}}'
                    + '" '
                    + '{{#if item.placementAdvice.windowTarget}}target="{{item.placementAdvice.windowTarget}}" {{/if}}'
                    + '>'
                        + '{{#if item.thumbnail}}'
                        + '<img src={{thumbnailId}} alt="content thumbnail"'
                        + 'style="float:left;margin-right:5px;'
                        + 'width:{{item.thumbnail.width}}px;'
                        + 'height:{{item.thumbnail.height}}px;'
                        + '" /> '
                        + '{{/if}}'
                        + '<div style="float:left;font-size:20px;font-weight:bold;'
                        + (item.titleWidth ? 'width:{{titleWidth}};' : '')
                        + 'height:{{titleHeight}};line-height:{{titleHeight}};">'
                        + '{{item.title}}'
                        + '</div>'
                    + '</a>'
                + '</div>'
                + '{{#if item.text}}'
                + '<div style="'
                + (item.displayWidth ? 'width:{{item.displayWidth}};' : '')
                + 'min-height:{{textHeight}};"></span>{{item.text}}</span></div>'
                + '{{/if}}'
        )
    };

    // Remove backslashes for the LTI 1.3
    mimeTypeType = mimeTypeType.replace(/\\/g, "");
    switch (mimeTypeType) {
        case 'application':
            if (mimeTypePieces[1] === 'vnd.ims.lti.v1.ltilink') {

                content = TEMPLATES.ltiLink({
                    item: item,
                    toolid: tool.id,
                    resourcelinkid: resourceLinkId,
                    course: course,
                    isResponsive: isResponsive,
                });
            }
            else {
                alert('Unsupported application subtype');
            }
            break;
        case 'text':
            content = TEMPLATES.link({
                item: item,
                custom: encodeURIComponent(JSON.stringify(item.custom)),
                course: course,
                toolid: tool.id,
                resourcelnkid: resourceLinkId,
                textHeight: textHeight,
                titleHeight: titleHeight,
                titleWidth: titleWidth,
                thumbnailId: thumbnailId
            });
            break;
        default:
            alert('Unsupported type');
    }

    this.toHtml = function () {
        return content;
    };

};

Y.namespace('M.atto_panoptoltibutton').IframeRenderingStrategy = function (item, course,
        resourceLinkId, tool, isResponsive) {

    var template;

    // If the item URL is the same as the LTI Launch URL (or Content-Item request), we assume we need
    // to make an LTI Launch request.
    if (item.url !== tool.baseurl && item.url !== tool.config.
            toolurl_ContentItemSelectionRequest) {
        item.useCustomUrl = true;
    }

    let displayWidth = item.placementAdvice?.displayWidth
        ? item.placementAdvice.displayWidth
        : item.iframe?.width;

    let displayHeight = item.placementAdvice?.displayHeight
        ? item.placementAdvice.displayHeight
        : item.iframe?.height;

    template = Y.Handlebars.compile(`
        <iframe src="${M.cfg.wwwroot}/lib/editor/atto/plugins/panoptoltibutton/view.php?course={{courseId}}
            &ltitypeid={{ltiTypeId}}&custom={{custom}}
            {{#if item.useCustomUrl}}&contenturl={{item.url}}{{/if}}
            &resourcelinkid={{resourcelinkid}}"
            {{#if isResponsive}}
            style="width: 100%; height: auto; aspect-ratio: 16 / 9;"
            {{else}}
            style="{{#if displayWidth}}width: {{displayWidth}}px;{{/if}}{{#if displayHeight}} height: {{displayHeight}}px;{{/if}}"
            {{/if}}
            allowfullscreen="true">
        </iframe>
    `);

    this.toHtml = function () {
        return template({
            item: item,
            custom: JSON.stringify(item.custom),
            courseId: course.id,
            resourcelinkid: resourceLinkId,
            ltiTypeId: tool.id,
            displayHeight: displayHeight,
            displayWidth: displayWidth,
            isResponsive: isResponsive,
        });
    };
};
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

}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
