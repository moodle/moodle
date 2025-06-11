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
 * AMD module for displaying an LTI launch within a AMD panel.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2024
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    "jquery",
    "core/notification",
    "core/modal_factory",
    "core/str",
], ($, notification, ModalFactory, str) =>
{
    let courseId = null;

    var init = (params) =>
    {
        if (   "0" === params.addvidbtnid
            || "0" === params.ltilaunchurl
            || 0 === params.courseid
            || 0 === params.height
            || 0 === params.width)
        {
            return;
        }

        courseId = params.courseid;

        let addVideoBtn = $("#" + params.addvidbtnid);
        addVideoBtn.on("click", () =>
        {
            open_panopto_window_callback(
                params.ltilaunchurl,
                params.height,
                params.width
            );
        });
    };

    var open_panopto_window_callback = (url, height, width) =>
    {
        // Modal custom size class.
        let modalClass = "mod-panoptosubmission-modal-custom-size";

        // Ensure unique class names for dynamic styling.
        let modalDialogClass = "mod-panoptosubmission-modal-dialog-custom";
        let modalContentClass = "mod-panoptosubmission-modal-content-custom";
        let modalBodyClass = "mod-panoptosubmission-modal-body-custom";
        let iframeClass = "mod-panoptosubmission-iframe-custom";

        Promise.all([
            str.get_string("select_submission", "panoptosubmission"),
            ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                body: `<iframe class="${iframeClass}" src="${url}" frameborder="0"></iframe>`,
            }),
        ])
            .then(([selectText, modal]) =>
            {
                modal.setTitle(selectText);
                modal.getRoot().addClass(modalClass);
                modal
                    .getRoot()
                    .find(".modal-dialog")
                    .addClass(modalDialogClass)
                    .css({
                        width: `${width}px`,
                        "max-width": `${width}px`,
                    });
                modal
                    .getRoot()
                    .find(".modal-content")
                    .addClass(modalContentClass)
                    .css({
                        height: `${height}px`,
                        "max-height": `${height}px`,
                    });
                modal
                    .getRoot()
                    .find(".modal-body")
                    .addClass(modalBodyClass);
                modal.show();

                document.body.panoptoWindow = modal;
                document.body.addEventListener(
                    "sessionSelected",
                    close_popup_callback.bind(this),
                    false
                );
            }).catch(notification.exception);
    };

    var close_popup_callback = (closeEvent) =>
    {
        $("input[id=submit_video]").removeAttr("disabled");

        let iFrameNode = $("iframe[id=contentframe]"),
            contentWrapperNode = $("div[id=contentcontainer]"),
            thumbnailNode = $("img[id=panoptothumbnail]"),
            thumbnailLinkNode = $("a[id=panoptothumbnaillink]"),
            titleNode = $("a[id=panoptosessiontitle]"),
            newSubmissionSource = new URL(closeEvent.detail.ltiViewerUrl),
            searchParams = newSubmissionSource.searchParams;

        searchParams.set("course", courseId);
        searchParams.set("custom", decodeURI(closeEvent.detail.customData));
        searchParams.set("contentUrl", closeEvent.detail.contentUrl);

        newSubmissionSource.search = searchParams.toString();

        $("input[id=sessiontitle]").attr("value", closeEvent.detail.title);
        $("input[id=source]").attr("value", closeEvent.detail.contentUrl);
        $("input[id=customdata]").attr(
            "value",
            closeEvent.detail.customData
        );
        $("input[id=width]").attr("value", closeEvent.detail.width);
        $("input[id=height]").attr("value", closeEvent.detail.height);
        $("input[id=thumbnailsource]").attr(
            "value",
            closeEvent.detail.thumbnailUrl
        );
        $("input[id=thumbnailwidth]").attr(
            "value",
            closeEvent.detail.thumbnailWidth
        );
        $("input[id=thumbnailheight]").attr(
            "value",
            closeEvent.detail.thumbnailHeight
        );

        titleNode.text(closeEvent.detail.title);
        titleNode.attr("href", newSubmissionSource.toString());

        thumbnailLinkNode.attr("href", newSubmissionSource.toString());
        thumbnailNode.attr("src", closeEvent.detail.thumbnailUrl);
        thumbnailNode.attr("width", closeEvent.detail.thumbnailWidth);
        thumbnailNode.attr("height", closeEvent.detail.thumbnailHeight);

        iFrameNode.attr("width", closeEvent.detail.width);
        iFrameNode.attr("height", closeEvent.detail.height);

        if (!iFrameNode.hasClass("session-hidden"))
        {
            iFrameNode.attr("src", newSubmissionSource.toString());
        }

        contentWrapperNode.removeClass("no-session");

        str.get_string("replacevideo", "panoptosubmission")
            .done((replaceText) =>
            {
                $("#id_add_video").val(replaceText);
            })
            .fail(notification.exception);

        $("#id_add_video").addClass("btn-secondary");
        $("#submit_video").addClass("btn-primary");
        $("#id_add_video").removeClass("btn-primary");
        $("#submit_video").removeClass("btn-secondary");
        document.body.panoptoWindow.destroy();
    };

    return {
        initsubmissionpanel: init,
    };
});
