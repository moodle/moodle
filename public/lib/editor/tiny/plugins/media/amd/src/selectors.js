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
 * Tiny Media plugin helper function to build queryable data selectors.
 *
 * @module      tiny_media/selectors
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    IMAGE: {
        actions: {
            submit: '.tiny_image_urlentrysubmit',
            imageBrowser: '.openimagebrowser',
            addUrl: '.tiny_image_addurl',
            deleteImage: '.tiny_image_deleteicon',
        },
        elements: {
            form: 'form.tiny_image_form',
            alignSettings: '.tiny_image_button',
            alt: '.tiny_image_altentry',
            altWarning: '.tiny_image_altwarning',
            height: '.tiny_image_heightentry',
            width: '.tiny_image_widthentry',
            url: '.tiny_image_urlentry',
            urlWarning: '.tiny_image_urlwarning',
            size: '.tiny_image_size',
            presentation: '.tiny_image_presentation',
            constrain: '.tiny_image_constrain',
            customStyle: '.tiny_image_customstyle',
            preview: '.tiny_image_preview',
            previewBox: '.tiny_image_preview_box',
            loaderIcon: '.tiny_image_loader',
            loaderIconContainer: '.tiny_image_loader_container',
            insertImage: '.tiny_image_insert_image',
            modalFooter: '.modal-footer',
            dropzoneContainer: '.tiny_image_dropzone_container',
            fileInput: '#tiny_image_fileinput',
            fileNameLabel: '.tiny_image_filename',
            originalSizeToggle: '.image-original-size-toggle',
            customSizeToggle: '.image-custom-size-toggle',
            properties: '.tiny_image_properties',
            bodyTemplate: '.tiny_image_body_template',
            footerTemplate: '.tiny_image_footer_template',
        },
        styles: {
            responsive: 'img-fluid',
        },
        template: {
            body: {
                insertImageBody: 'tiny_media/image/body/insert_image_modal_insert_body',
                insertImageDetailsBody: 'tiny_media/image/body/insert_image_modal_details_body',
            },
            footer: {
                insertImageFooter: 'tiny_media/image/footer/insert_image_modal_insert_footer',
                insertImageDetailsFooter: 'tiny_media/image/footer/insert_image_modal_details_footer',
            },
        },
        type: 'IMAGE',
    },
    EMBED: {
        actions: {
            mediaBrowser: '.openmediabrowser',
            addUrl: '.tiny_media_add_url',
            deleteMedia: '.tiny_media_delete_icon',
            showSubtitleCaption: '[data-action="show-subtitle-caption"]',
            backToMediaDetails: '[data-action="back-to-media-details"]',
            uploadCustomThumbnail: '.upload-custom-thumbnail',
            deleteCustomThumbnail: '.delete-custom-thumbnail',
            deleteThumbnail: '.delete_tiny_thumbnail',
            setPoster: '[data-action="set-poster"]',
            mediaLinkAsAudio: '.link_as_audio',
            mediaLinkAsVideo: '.link_as_video',
            cancelMediaDetails: '[data-action="cancel"]',
        },
        elements: {
            source: '.tiny_media_source',
            track: '.tiny_media_track',
            posterSource: '.tiny_media_poster_source',
            title: '.tiny_media_title_entry',
            url: '.tiny_media_source_url_entry',
            width: '.tiny_media_width_entry',
            height: '.tiny_media_height_entry',
            trackSource: '.tiny_media_track_source',
            trackLabel: '.tiny_media_track_label_entry',
            trackLang: '.tiny_media_track_lang_entry',
            trackDefault: '.tiny_media_track_default',
            mediaControl: '.tiny_media_controls',
            mediaAutoplay: '.tiny_media_autoplay',
            mediaMute: '.tiny_media_mute',
            mediaLoop: '.tiny_media_loop',
            bodyTemplate: '.tiny_media_body_template',
            footerTemplate: '.tiny_media_footer_template',
            dropzoneContainer: '.tiny_media_dropzone_container',
            fromUrl: '.tiny_media_from_url_entry',
            urlWarning: '.tiny_media_url_warning',
            loaderIcon: '.tiny_media_loader',
            loaderIconContainer: '.tiny_media_loader_container',
            insertMedia: '.tiny_media_insert_media',
            modalFooter: '.modal-footer',
            fileNameLabel: '.tiny_media_filename',
            previewBox: '.tiny_media_preview_box',
            originalSizeToggle: '.media-original-size-toggle',
            customSizeToggle: '.media-custom-size-toggle',
            properties: '.tiny_media_properties',
            mediaDetailsBody: '#tiny-media-details-body',
            mediaSubtitleCaptionBody: '#tiny-media-subtitle-caption-body',
            mediaMetadataTabPane: '.tab-pane-media-metadata',
            videoThumbnail: '.video-thumbnail',
            mediaSizeProperties: '.media-size-properties',
            thumbnailPreview: '.tiny_media_thumbnail_preview',
            mediaPreviewContainer: '#media-filter-preview-container',
        },
        template: {
            body: {
                insertMediaBody: 'tiny_media/embed/body/insert_media_body',
                mediaDetailsBody: 'tiny_media/embed/body/media_details_body',
                mediaThumbnailBody: 'tiny_media/embed/body/media_thumbnail_body',
            },
            footer: {
                insertMediaFooter: 'tiny_media/embed/footer/insert_media_footer',
                mediaDetailsFooter: 'tiny_media/embed/footer/media_details_footer',
                mediaThumbnailFooter: 'tiny_media/embed/footer/media_thumbnail_footer',
            },
        },
        type: 'EMBED',
        externalMediaProvider: 'external-media-provider',
    },
};
