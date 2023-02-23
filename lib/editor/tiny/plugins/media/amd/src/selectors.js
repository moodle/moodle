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
        },
        elements: {
            form: 'form.tiny_image_form',
            alignment: '.tiny_image_alignment',
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
        },
        styles: {
            responsive: 'img-fluid',
        },
        alignments: [
            // Vertical alignment.
            {
                name: 'verticalAlign',
                value: 'align-top',
                margin: '0 0.5em',
                legacyValues: [
                    'atto_image_button_text-top',
                ],
            },
            {
                name: 'verticalAlign',
                value: 'align-middle',
                margin: '0 0.5em',
                legacyValues: [
                    'atto_image_button_middle',
                ],
            },
            {
                name: 'verticalAlign',
                value: 'align-bottom',
                margin: '0 0.5em',
                isDefault: true,
                legacyValues: [
                    'atto_image_button_text-bottom',
                ],
            },
        ],
    },
    EMBED: {
        actions: {
            submit: '.tiny_media_submit',
            mediaBrowser: '.openmediabrowser',
        },
        elements: {
            form: 'form.tiny_media_form',
            source: '.tiny_media_source',
            track: '.tiny_media_track',
            mediaSource: '.tiny_media_media_source',
            linkSource: '.tiny_media_link_source',
            linkSize: '.tiny_media_link_size',
            posterSource: '.tiny_media_poster_source',
            posterSize: '.tiny_media_poster_size',
            displayOptions: '.tiny_media_display_options',
            name: '.tiny_media_name_entry',
            title: '.tiny_media_title_entry',
            url: '.tiny_media_url_entry',
            width: '.tiny_media_width_entry',
            height: '.tiny_media_height_entry',
            trackSource: '.tiny_media_track_source',
            trackKind: '.tiny_media_track_kind_entry',
            trackLabel: '.tiny_media_track_label_entry',
            trackLang: '.tiny_media_track_lang_entry',
            trackDefault: '.tiny_media_track_default',
            mediaControl: '.tiny_media_controls',
            mediaAutoplay: '.tiny_media_autoplay',
            mediaMute: '.tiny_media_mute',
            mediaLoop: '.tiny_media_loop',
            advancedSettings: '.tiny_media_advancedsettings',
            linkTab: 'li[data-medium-type="link"]',
            videoTab: 'li[data-medium-type="video"]',
            audioTab: 'li[data-medium-type="audio"]',
            linkPane: '.tab-pane[data-medium-type="link"]',
            videoPane: '.tab-pane[data-medium-type="video"]',
            audioPane: '.tab-pane[data-medium-type="audio"]',
            trackSubtitlesTab: 'li[data-track-kind="subtitles"]',
            trackCaptionsTab: 'li[data-track-kind="captions"]',
            trackDescriptionsTab: 'li[data-track-kind="descriptions"]',
            trackChaptersTab: 'li[data-track-kind="chapters"]',
            trackMetadataTab: 'li[data-track-kind="metadata"]',
            trackSubtitlesPane: '.tab-pane[data-track-kind="subtitles"]',
            trackCaptionsPane: '.tab-pane[data-track-kind="captions"]',
            trackDescriptionsPane: '.tab-pane[data-track-kind="descriptions"]',
            trackChaptersPane: '.tab-pane[data-track-kind="chapters"]',
            trackMetadataPane: '.tab-pane[data-track-kind="metadata"]',
        },
        mediaTypes: {
            link: 'LINK',
            video: 'VIDEO',
            audio: 'AUDIO',
        },
        trackKinds: {
            subtitles: 'SUBTITLES',
            captions: 'CAPTIONS',
            descriptions: 'DESCRIPTIONS',
            chapters: 'CHAPTERS',
            metadata: 'METADATA',
        },
    },
};
