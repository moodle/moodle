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

    this.strategyFor = function (item, course, resourceLinkId, tool) {

        var StrategyClass = Y.M.atto_panoptoltibutton.EmbeddedContentRenderingStrategy;

        if (item.mediaType === 'application/vnd.ims.lti.v1.ltilink'
                || item.placementAdvice) {
            StrategyClass = Y.M.atto_panoptoltibutton.IframeRenderingStrategy;

            if (item.placementAdvice) {

                switch (item.placementAdvice.presentationDocumentTarget) {
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

        var strategy = new StrategyClass(item, course, resourceLinkId, tool);

        return strategy;
    };
};

Y.namespace('M.atto_panoptoltibutton').EmbeddedContentRenderingStrategy = function (item,
        course, resourceLinkId, tool) {

    var mimeTypePieces = item.mediaType.split('/'),
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
    }


    TEMPLATES = {
        ltiLink: Y.Handlebars.compile('<iframe src="/lib/editor/atto/plugins/panoptoltibutton/view.php?custom={{custom}}&'
            + 'course={{course.id}}&ltitypeid={{toolid}}&resourcelinkid={{resourcelinkid}}'
            + '{{#if item.url}}&contenturl={{item.url}}{{/if}}'
            + '" '
            + '{{#if item.placementAdvice.width}} width="{{item.placementAdvice.displayWidth}}"{{/if}} '
            + '{{#if item.placementAdvice.height}} height="{{item.placementAdvice.displayHeight}}"{{/if}} '
            + '/>'
        ),
        link: Y.Handlebars.compile('<div style="' 
                    + (item.displayWidth ? 'width:{{item.displayWidth}};' : '')
                    + 'height:{{titleHeight}};">'
                    + '<a href="/lib/editor/atto/plugins/panoptoltibutton/view.php?custom={{custom}}&'
                    + 'course={{course.id}}&ltitypeid={{toolid}}&resourcelinkid={{resourcelinkid}}'
                    + '{{#if item.url}}&contenturl={{item.url}}{{/if}}'
                    + '" '
                    + '{{#if item.placementAdvice.windowTarget}}target="{{item.placementAdvice.windowTarget}}" {{/if}}'
                    + '>'
                        + '{{#if item.thumbnail}}'
                        + '<img src={{item.thumbnail.id}} alt="content thumbnail"'
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

    switch (mimeTypeType) {
        case 'application':
            if (mimeTypePieces[1] === 'vnd.ims.lti.v1.ltilink') {

                content = TEMPLATES.ltiLink({
                    item: item,
                    toolid: tool.id,
                    resourcelinkid: resourceLinkId,
                    course: course,
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
                titleWidth: titleWidth
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
        resourceLinkId, tool) {

    var template;

    // If the item URL is the same as the LTI Launch URL (or Content-Item request), we assume we need
    // to make an LTI Launch request.
    if (item.url !== tool.baseurl && item.url !== tool.config.
            toolurl_ContentItemSelectionRequest) {
        item.useCustomUrl = true;
    }

    template = Y.Handlebars.compile('<iframe src="/lib/editor/atto/plugins/panoptoltibutton/view.php?course={{courseId}}'
            + '&ltitypeid={{ltiTypeId}}&custom={{custom}}'
            + '{{#if item.useCustomUrl}}&contenturl={{item.url}}{{/if}}'
            + '&resourcelinkid={{resourcelinkid}}" '
            + ' {{#if item.placementAdvice.displayWidth}}width="{{item.placementAdvice.displayWidth}}" {{/if}}'
            + ' {{#if item.placementAdvice.displayHeight}}height="{{item.placementAdvice.displayHeight}}" {{/if}}'
            + '></iframe>'
            );

    this.toHtml = function () {
        return template({
            item: item,
            custom: JSON.stringify(item.custom),
            courseId: course.id,
            resourcelinkid: resourceLinkId,
            ltiTypeId: tool.id
        });
    };

};
