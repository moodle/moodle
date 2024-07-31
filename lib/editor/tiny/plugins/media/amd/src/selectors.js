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
            sizeOriginal: '.tiny_image_sizeoriginal',
            sizeCustom: '.tiny_image_sizecustom',
            properties: '.tiny_image_properties',
        },
        styles: {
            responsive: 'img-fluid',
        },
    },
    EMBED: {
        actions: {
            mediaBrowser: '.openmediabrowser',
        },
        elements: {
            source: '.tiny_media_source',
            track: '.tiny_media_track',
            posterSource: '.tiny_media_poster_source',
            title: '.tiny_media_title_entry',
            url: '.tiny_media_url_entry',
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
        },
    },
};
