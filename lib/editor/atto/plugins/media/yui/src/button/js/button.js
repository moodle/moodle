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

/*
 * @package    atto_media
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_media-button
 */

/**
 * Atto media selection tool.
 *
 * @namespace M.atto_media
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_media',
    MEDIA_TYPES = {LINK: 'LINK', VIDEO: 'VIDEO', AUDIO: 'AUDIO'},
    TRACK_KINDS = {
        SUBTITLES: 'SUBTITLES',
        CAPTIONS: 'CAPTIONS',
        DESCRIPTIONS: 'DESCRIPTIONS',
        CHAPTERS: 'CHAPTERS',
        METADATA: 'METADATA'
    },
    CSS = {
        SOURCE: 'atto_media_source',
        TRACK: 'atto_media_track',
        MEDIA_SOURCE: 'atto_media_media_source',
        LINK_SOURCE: 'atto_media_link_source',
        POSTER_SOURCE: 'atto_media_poster_source',
        TRACK_SOURCE: 'atto_media_track_source',
        DISPLAY_OPTIONS: 'atto_media_display_options',
        NAME_INPUT: 'atto_media_name_entry',
        TITLE_INPUT: 'atto_media_title_entry',
        URL_INPUT: 'atto_media_url_entry',
        POSTER_SIZE: 'atto_media_poster_size',
        LINK_SIZE: 'atto_media_link_size',
        WIDTH_INPUT: 'atto_media_width_entry',
        HEIGHT_INPUT: 'atto_media_height_entry',
        TRACK_KIND_INPUT: 'atto_media_track_kind_entry',
        TRACK_LABEL_INPUT: 'atto_media_track_label_entry',
        TRACK_LANG_INPUT: 'atto_media_track_lang_entry',
        TRACK_DEFAULT_SELECT: 'atto_media_track_default',
        MEDIA_CONTROLS_TOGGLE: 'atto_media_controls',
        MEDIA_AUTOPLAY_TOGGLE: 'atto_media_autoplay',
        MEDIA_MUTE_TOGGLE: 'atto_media_mute',
        MEDIA_LOOP_TOGGLE: 'atto_media_loop',
        ADVANCED_SETTINGS: 'atto_media_advancedsettings',
        LINK: MEDIA_TYPES.LINK.toLowerCase(),
        VIDEO: MEDIA_TYPES.VIDEO.toLowerCase(),
        AUDIO: MEDIA_TYPES.AUDIO.toLowerCase(),
        TRACK_SUBTITLES: TRACK_KINDS.SUBTITLES.toLowerCase(),
        TRACK_CAPTIONS: TRACK_KINDS.CAPTIONS.toLowerCase(),
        TRACK_DESCRIPTIONS: TRACK_KINDS.DESCRIPTIONS.toLowerCase(),
        TRACK_CHAPTERS: TRACK_KINDS.CHAPTERS.toLowerCase(),
        TRACK_METADATA: TRACK_KINDS.METADATA.toLowerCase()
    },
    SELECTORS = {
        SOURCE: '.' + CSS.SOURCE,
        TRACK: '.' + CSS.TRACK,
        MEDIA_SOURCE: '.' + CSS.MEDIA_SOURCE,
        POSTER_SOURCE: '.' + CSS.POSTER_SOURCE,
        TRACK_SOURCE: '.' + CSS.TRACK_SOURCE,
        DISPLAY_OPTIONS: '.' + CSS.DISPLAY_OPTIONS,
        NAME_INPUT: '.' + CSS.NAME_INPUT,
        TITLE_INPUT: '.' + CSS.TITLE_INPUT,
        URL_INPUT: '.' + CSS.URL_INPUT,
        POSTER_SIZE: '.' + CSS.POSTER_SIZE,
        LINK_SIZE: '.' + CSS.LINK_SIZE,
        WIDTH_INPUT: '.' + CSS.WIDTH_INPUT,
        HEIGHT_INPUT: '.' + CSS.HEIGHT_INPUT,
        TRACK_KIND_INPUT: '.' + CSS.TRACK_KIND_INPUT,
        TRACK_LABEL_INPUT: '.' + CSS.TRACK_LABEL_INPUT,
        TRACK_LANG_INPUT: '.' + CSS.TRACK_LANG_INPUT,
        TRACK_DEFAULT_SELECT: '.' + CSS.TRACK_DEFAULT_SELECT,
        MEDIA_CONTROLS_TOGGLE: '.' + CSS.MEDIA_CONTROLS_TOGGLE,
        MEDIA_AUTOPLAY_TOGGLE: '.' + CSS.MEDIA_AUTOPLAY_TOGGLE,
        MEDIA_MUTE_TOGGLE: '.' + CSS.MEDIA_MUTE_TOGGLE,
        MEDIA_LOOP_TOGGLE: '.' + CSS.MEDIA_LOOP_TOGGLE,
        ADVANCED_SETTINGS: '.' + CSS.ADVANCED_SETTINGS,
        LINK_TAB: 'li[data-medium-type="' + CSS.LINK + '"]',
        LINK_PANE: '.tab-pane[data-medium-type="' + CSS.LINK + '"]',
        VIDEO_TAB: 'li[data-medium-type="' + CSS.VIDEO + '"]',
        VIDEO_PANE: '.tab-pane[data-medium-type="' + CSS.VIDEO + '"]',
        AUDIO_TAB: 'li[data-medium-type="' + CSS.AUDIO + '"]',
        AUDIO_PANE: '.tab-pane[data-medium-type="' + CSS.AUDIO + '"]',
        TRACK_SUBTITLES_TAB: 'li[data-track-kind="' + CSS.TRACK_SUBTITLES + '"]',
        TRACK_SUBTITLES_PANE: '.tab-pane[data-track-kind="' + CSS.TRACK_SUBTITLES + '"]',
        TRACK_CAPTIONS_TAB: 'li[data-track-kind="' + CSS.TRACK_CAPTIONS + '"]',
        TRACK_CAPTIONS_PANE: '.tab-pane[data-track-kind="' + CSS.TRACK_CAPTIONS + '"]',
        TRACK_DESCRIPTIONS_TAB: 'li[data-track-kind="' + CSS.TRACK_DESCRIPTIONS + '"]',
        TRACK_DESCRIPTIONS_PANE: '.tab-pane[data-track-kind="' + CSS.TRACK_DESCRIPTIONS + '"]',
        TRACK_CHAPTERS_TAB: 'li[data-track-kind="' + CSS.TRACK_CHAPTERS + '"]',
        TRACK_CHAPTERS_PANE: '.tab-pane[data-track-kind="' + CSS.TRACK_CHAPTERS + '"]',
        TRACK_METADATA_TAB: 'li[data-track-kind="' + CSS.TRACK_METADATA + '"]',
        TRACK_METADATA_PANE: '.tab-pane[data-track-kind="' + CSS.TRACK_METADATA + '"]'
    },
    TEMPLATES = {
        ROOT: '' +
            '<form class="mform atto_form atto_media" id="{{elementid}}_atto_media_form">' +
                '<ul class="root nav nav-tabs mb-1" role="tablist">' +
                    '<li data-medium-type="{{CSS.LINK}}" class="nav-item">' +
                        '<a class="nav-link active" href="#{{elementid}}_{{CSS.LINK}}" role="tab" data-toggle="tab">' +
                            '{{get_string "link" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-medium-type="{{CSS.VIDEO}}" class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{CSS.VIDEO}}" role="tab" data-toggle="tab">' +
                            '{{get_string "video" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-medium-type="{{CSS.AUDIO}}" class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{CSS.AUDIO}}" role="tab" data-toggle="tab">' +
                            '{{get_string "audio" component}}' +
                        '</a>' +
                    '</li>' +
                '</ul>' +
                '<div class="root tab-content">' +
                    '<div data-medium-type="{{CSS.LINK}}" class="tab-pane active" id="{{elementid}}_{{CSS.LINK}}">' +
                        '{{> tab_panes.link}}' +
                    '</div>' +
                    '<div data-medium-type="{{CSS.VIDEO}}" class="tab-pane" id="{{elementid}}_{{CSS.VIDEO}}">' +
                        '{{> tab_panes.video}}' +
                    '</div>' +
                    '<div data-medium-type="{{CSS.AUDIO}}" class="tab-pane" id="{{elementid}}_{{CSS.AUDIO}}">' +
                        '{{> tab_panes.audio}}' +
                    '</div>' +
                '</div>' +
                '<div class="mdl-align">' +
                    '<br/>' +
                    '<button class="btn btn-secondary submit" type="submit">{{get_string "createmedia" component}}</button>' +
                '</div>' +
            '</form>',
        TAB_PANES: {
            LINK: '' +
                '{{renderPartial "form_components.source" context=this id=CSS.LINK_SOURCE}}' +
                '<label for="{{elementid}}_link_nameentry">{{get_string "entername" component}}</label>' +
                '<input class="form-control fullwidth {{CSS.NAME_INPUT}}" type="text" id="{{elementid}}_link_nameentry"' +
                        'size="32" required="true"/>',
            VIDEO: '' +
                '{{renderPartial "form_components.source" context=this id=CSS.MEDIA_SOURCE entersourcelabel="videosourcelabel"' +
                    ' addcomponentlabel="addsource" multisource="true" addsourcehelp=helpStrings.addsource}}' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_video-display-options">' +
                    '<input name="mform_isexpanded_{{elementid}}_video-display-options" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "displayoptions" component}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.display_options" context=this id=CSS.VIDEO mediatype_video=true}}' +
                    '</div>' +
                '</fieldset>' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_video-advanced-settings">' +
                    '<input name="mform_isexpanded_{{elementid}}_video-advanced-settings" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "advancedsettings" component}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.advanced_settings" context=this id=CSS.VIDEO}}' +
                    '</div>' +
                '</fieldset>' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_video-tracks">' +
                    '<input name="mform_isexpanded_{{elementid}}_video-tracks" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "tracks" component}} {{{helpStrings.tracks}}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.track_tabs" context=this id=CSS.VIDEO}}' +
                    '</div>' +
                '</fieldset>',
            AUDIO: '' +
                '{{renderPartial "form_components.source" context=this id=CSS.MEDIA_SOURCE entersourcelabel="audiosourcelabel"' +
                    ' addcomponentlabel="addsource" multisource="true" addsourcehelp=helpStrings.addsource}}' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_audio-display-options">' +
                    '<input name="mform_isexpanded_{{elementid}}_audio-display-options" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "displayoptions" component}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.display_options" context=this id=CSS.AUDIO}}' +
                    '</div>' +
                '</fieldset>' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_audio-advanced-settings">' +
                    '<input name="mform_isexpanded_{{elementid}}_audio-advanced-settings" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "advancedsettings" component}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.advanced_settings" context=this id=CSS.AUDIO}}' +
                    '</div>' +
                '</fieldset>' +
                '<fieldset class="collapsible collapsed" id="{{elementid}}_audio-tracks">' +
                    '<input name="mform_isexpanded_{{elementid}}_audio-tracks" type="hidden">' +
                    '<legend class="ftoggler">{{get_string "tracks" component}} {{{helpStrings.tracks}}}</legend>' +
                    '<div class="fcontainer">' +
                        '{{renderPartial "form_components.track_tabs" context=this id=CSS.AUDIO}}' +
                    '</div>' +
                '</fieldset>'
        },
        FORM_COMPONENTS: {
            SOURCE: '' +
                '<div class="{{CSS.SOURCE}} {{id}}">' +
                    '<div class="mb-1">' +
                        '<label for="url-input">' +
                        '{{#entersourcelabel}}{{get_string ../entersourcelabel ../component}}{{/entersourcelabel}}' +
                        '{{^entersourcelabel}}{{get_string "entersource" ../component}}{{/entersourcelabel}}' +
                        '</label>' +
                        '<div class="input-group input-append w-100">' +
                            '<input id="url-input" class="form-control {{CSS.URL_INPUT}}" type="url" size="32"/>' +
                            '<span class="input-group-append">' +
                                '<button class="btn btn-secondary openmediabrowser" type="button">' +
                                '{{get_string "browserepositories" component}}</button>' +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                    '{{#multisource}}' +
                        '{{renderPartial "form_components.add_component" context=../this label=../addcomponentlabel ' +
                            ' help=../addsourcehelp}}' +
                    '{{/multisource}}' +
                '</div>',
            ADD_COMPONENT: '' +
                '<div>' +
                    '<a href="#" class="addcomponent">' +
                        '{{#label}}{{get_string ../label ../component}}{{/label}}' +
                        '{{^label}}{{get_string "add" ../component}}{{/label}}' +
                    '</a>' +
                    '{{#help}}{{{../help}}}{{/help}}' +
                '</div>',
            REMOVE_COMPONENT: '' +
                '<div>' +
                    '<a href="#" class="removecomponent">' +
                        '{{#label}}{{get_string ../label ../component}}{{/label}}' +
                        '{{^label}}{{get_string "remove" ../component}}{{/label}}' +
                    '</a>' +
                '</div>',
            DISPLAY_OPTIONS: '' +
                '<div class="{{CSS.DISPLAY_OPTIONS}}">' +
                    '<div class="mb-1">' +
                        '<label for="{{id}}_media-title-entry">{{get_string "entertitle" component}}</label>' +
                        '<input class="form-control fullwidth {{CSS.TITLE_INPUT}}" type="text" id="{{id}}_media-title-entry"' +
                            'size="32"/>' +
                    '</div>' +
                    '<div class="clearfix"></div>' +
                    '{{#mediatype_video}}' +
                    '<div class="mb-1">' +
                        '<label>{{get_string "size" component}}</label>' +
                        '<div class="form-inline {{CSS.POSTER_SIZE}}">' +
                            '<label class="accesshide">{{get_string "videowidth" component}}</label>' +
                            '<input type="text" class="form-control mr-1 {{CSS.WIDTH_INPUT}} input-mini" size="4"/>' +
                            ' x ' +
                            '<label class="accesshide">{{get_string "videoheight" component}}</label>' +
                            '<input type="text" class="form-control ml-1 {{CSS.HEIGHT_INPUT}} input-mini" size="4"/>' +
                        '</div>' +
                    '</div>' +
                    '<div class="clearfix"></div>' +
                    '{{renderPartial "form_components.source" context=this id=CSS.POSTER_SOURCE entersourcelabel="poster"}}' +
                    '{{/mediatype_video}}' +
                '<div>',
            ADVANCED_SETTINGS: '' +
                '<div class="{{CSS.ADVANCED_SETTINGS}}">' +
                    '<div class="form-check">' +
                        '<input type="checkbox" checked="true" class="form-check-input {{CSS.MEDIA_CONTROLS_TOGGLE}}"' +
                        'id="{{id}}_media-controls-toggle"/>' +
                        '<label class="form-check-label" for="{{id}}_media-controls-toggle">' +
                        '{{get_string "controls" component}}' +
                        '</label>' +
                    '</div>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input {{CSS.MEDIA_AUTOPLAY_TOGGLE}}"' +
                        'id="{{id}}_media-autoplay-toggle"/>' +
                        '<label class="form-check-label" for="{{id}}_media-autoplay-toggle">' +
                        '{{get_string "autoplay" component}}' +
                        '</label>' +
                    '</div>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input {{CSS.MEDIA_MUTE_TOGGLE}}" ' +
                            'id="{{id}}_media-mute-toggle"/>' +
                        '<label class="form-check-label" for="{{id}}_media-mute-toggle">' +
                        '{{get_string "mute" component}}' +
                        '</label>' +
                    '</div>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input {{CSS.MEDIA_LOOP_TOGGLE}}" ' +
                            'id="{{id}}_media-loop-toggle"/>' +
                        '<label class="form-check-label" for="{{id}}_media-loop-toggle">' +
                        '{{get_string "loop" component}}' +
                        '</label>' +
                    '</div>' +
                '</div>',
            TRACK_TABS: '' +
                '<ul class="nav nav-tabs mb-3">' +
                    '<li data-track-kind="{{CSS.TRACK_SUBTITLES}}" class="nav-item">' +
                        '<a class="nav-link active" href="#{{elementid}}_{{id}}_{{CSS.TRACK_SUBTITLES}}"' +
                            ' role="tab" data-toggle="tab">' +
                            '{{get_string "subtitles" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-track-kind="{{CSS.TRACK_CAPTIONS}}" class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{id}}_{{CSS.TRACK_CAPTIONS}}" role="tab" data-toggle="tab">' +
                            '{{get_string "captions" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-track-kind="{{CSS.TRACK_DESCRIPTIONS}}"  class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{id}}_{{CSS.TRACK_DESCRIPTIONS}}"' +
                            ' role="tab" data-toggle="tab">' +
                            '{{get_string "descriptions" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-track-kind="{{CSS.TRACK_CHAPTERS}}" class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{id}}_{{CSS.TRACK_CHAPTERS}}" role="tab" data-toggle="tab">' +
                            '{{get_string "chapters" component}}' +
                        '</a>' +
                    '</li>' +
                    '<li data-track-kind="{{CSS.TRACK_METADATA}}" class="nav-item">' +
                        '<a class="nav-link" href="#{{elementid}}_{{id}}_{{CSS.TRACK_METADATA}}" role="tab" data-toggle="tab">' +
                            '{{get_string "metadata" component}}' +
                        '</a>' +
                    '</li>' +
                '</ul>' +
                '<div class="tab-content">' +
                    '<div data-track-kind="{{CSS.TRACK_SUBTITLES}}" class="tab-pane active"' +
                        ' id="{{elementid}}_{{id}}_{{CSS.TRACK_SUBTITLES}}">' +
                        '<div class="trackhelp">{{{helpStrings.subtitles}}}</div>' +
                        '{{renderPartial "form_components.track" context=this sourcelabel="subtitlessourcelabel"' +
                            ' addcomponentlabel="addsubtitlestrack"}}' +
                    '</div>' +
                    '<div data-track-kind="{{CSS.TRACK_CAPTIONS}}" class="tab-pane"' +
                        ' id="{{elementid}}_{{id}}_{{CSS.TRACK_CAPTIONS}}">' +
                        '<div class="trackhelp">{{{helpStrings.captions}}}</div>' +
                        '{{renderPartial "form_components.track" context=this sourcelabel="captionssourcelabel"' +
                            ' addcomponentlabel="addcaptionstrack"}}' +
                    '</div>' +
                    '<div data-track-kind="{{CSS.TRACK_DESCRIPTIONS}}" class="tab-pane"' +
                        ' id="{{elementid}}_{{id}}_{{CSS.TRACK_DESCRIPTIONS}}">' +
                        '<div class="trackhelp">{{{helpStrings.descriptions}}}</div>' +
                        '{{renderPartial "form_components.track" context=this sourcelabel="descriptionssourcelabel"' +
                            ' addcomponentlabel="adddescriptionstrack"}}' +
                    '</div>' +
                    '<div data-track-kind="{{CSS.TRACK_CHAPTERS}}" class="tab-pane"' +
                        ' id="{{elementid}}_{{id}}_{{CSS.TRACK_CHAPTERS}}">' +
                        '<div class="trackhelp">{{{helpStrings.chapters}}}</div>' +
                        '{{renderPartial "form_components.track" context=this sourcelabel="chapterssourcelabel"' +
                            ' addcomponentlabel="addchapterstrack"}}' +
                    '</div>' +
                    '<div data-track-kind="{{CSS.TRACK_METADATA}}" class="tab-pane"' +
                        ' id="{{elementid}}_{{id}}_{{CSS.TRACK_METADATA}}">' +
                        '<div class="trackhelp">{{{helpStrings.metadata}}}</div>' +
                        '{{renderPartial "form_components.track" context=this sourcelabel="metadatasourcelabel"' +
                            ' addcomponentlabel="addmetadatatrack"}}' +
                    '</div>' +
                '</div>',
            TRACK: '' +
                '<div class="mb-1 {{CSS.TRACK}}">' +
                    '{{renderPartial "form_components.source" context=this id=CSS.TRACK_SOURCE entersourcelabel=sourcelabel}}' +
                    '<div class="form-group">' +
                        '<label class="w-100" for="lang-input">{{get_string "srclang" component}}</label>' +
                        '<select id="lang-input" class="custom-select {{CSS.TRACK_LANG_INPUT}}">' +
                            '<optgroup label="{{get_string "languagesinstalled" component}}">' +
                                '{{#langsinstalled}}' +
                                    '<option value="{{code}}" {{#default}}selected="selected"{{/default}}>{{lang}}</option>' +
                                '{{/langsinstalled}}' +
                            '</optgroup>' +
                            '<optgroup label="{{get_string "languagesavailable" component}} ">' +
                                '{{#langsavailable}}<option value="{{code}}">{{lang}}</option>{{/langsavailable}}' +
                            '</optgroup>' +
                        '</select>' +
                    '</div>' +
                    '<div class="form-group">' +
                        '<label class="w-100" for="track-input">{{get_string "label" component}}</label>' +
                        '<input id="track-input" class="form-control {{CSS.TRACK_LABEL_INPUT}}" type="text"/>' +
                    '</div>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input {{CSS.TRACK_DEFAULT_SELECT}}"/>' +
                        '<label class="form-check-label">{{get_string "default" component}}</label>' +
                    '</div>' +
                    '{{renderPartial "form_components.add_component" context=this label=addcomponentlabel}}' +
                '</div>'
        },
        HTML_MEDIA: {
            VIDEO: '' +
                '&nbsp;<video ' +
                    '{{#width}}width="{{../width}}" {{/width}}' +
                    '{{#height}}height="{{../height}}" {{/height}}' +
                    '{{#poster}}poster="{{../poster}}" {{/poster}}' +
                    '{{#showControls}}controls="true" {{/showControls}}' +
                    '{{#loop}}loop="true" {{/loop}}' +
                    '{{#muted}}muted="true" {{/muted}}' +
                    '{{#autoplay}}autoplay="true" {{/autoplay}}' +
                    '{{#title}}title="{{../title}}" {{/title}}' +
                '>' +
                    '{{#sources}}<source src="{{source}}">{{/sources}}' +
                    '{{#tracks}}' +
                        '<track src="{{track}}" kind="{{kind}}" srclang="{{srclang}}" label="{{label}}"' +
                            ' {{#defaultTrack}}default="true"{{/defaultTrack}}>' +
                    '{{/tracks}}' +
                    '{{#description}}{{../description}}{{/description}}' +
                '</video>&nbsp',
            AUDIO: '' +
                '&nbsp;<audio ' +
                    '{{#showControls}}controls="true" {{/showControls}}' +
                    '{{#loop}}loop="true" {{/loop}}' +
                    '{{#muted}}muted="true" {{/muted}}' +
                    '{{#autoplay}}autoplay="true" {{/autoplay}}' +
                    '{{#title}}title="{{../title}}" {{/title}}' +
                '>' +
                    '{{#sources}}<source src="{{source}}">{{/sources}}' +
                    '{{#tracks}}' +
                        '<track src="{{track}}" kind="{{kind}}" srclang="{{srclang}}" label="{{label}}"' +
                            ' {{#defaultTrack}}default="true"{{/defaultTrack}}>' +
                    '{{/tracks}}' +
                    '{{#description}}{{../description}}{{/description}}' +
                '</audio>&nbsp',
            LINK: '' +
                '<a href="{{url}}" ' +
                    '{{#width}}data-width="{{../width}}" {{/width}}' +
                    '{{#height}}data-height="{{../height}}"{{/height}}' +
                '>{{#name}}{{../name}}{{/name}}{{^name}}{{../url}}{{/name}}</a>'
         }
    };

Y.namespace('M.atto_media').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    initializer: function() {
        if (this.get('host').canShowFilepicker('media')) {
            this.editor.delegate('dblclick', this._displayDialogue, 'video', this);
            this.editor.delegate('click', this._handleClick, 'video', this);

            this.addButton({
                icon: 'e/insert_edit_video',
                callback: this._displayDialogue,
                tags: 'video, audio',
                tagMatchRequiresAll: false
            });
        }
    },

    /**
     * Gets the root context for all templates, with extra supplied context.
     *
     * @method _getContext
     * @param  {Object} extra The extra context to add
     * @return {Object}
     * @private
     */
    _getContext: function(extra) {
        return Y.merge({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            langsinstalled: this.get('langs').installed,
            langsavailable: this.get('langs').available,
            helpStrings: this.get('help'),
            CSS: CSS
        }, extra);
    },

    /**
     * Handles a click on a media element.
     *
     * @method _handleClick
     * @param  {EventFacade} e
     * @private
     */
    _handleClick: function(e) {
        var medium = e.target;

        var selection = this.get('host').getSelectionFromNode(medium);
        if (this.get('host').getSelection() !== selection) {
            this.get('host').setSelection(selection);
        }
    },

    /**
     * Display the media editing tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        if (this.get('host').getSelection() === false) {
            return;
        }

        if (!('renderPartial' in Y.Handlebars.helpers)) {
            (function smashPartials(chain, obj) {
                Y.each(obj, function(value, index) {
                    chain.push(index);
                    if (typeof value !== "object") {
                        Y.Handlebars.registerPartial(chain.join('.').toLowerCase(), value);
                    } else {
                        smashPartials(chain, value);
                    }
                    chain.pop();
                });
            })([], TEMPLATES);

            Y.Handlebars.registerHelper('renderPartial', function(partialName, options) {
                if (!partialName) {
                    return '';
                }

                var partial = Y.Handlebars.partials[partialName];
                var parentContext = options.hash.context ? Y.clone(options.hash.context) : {};
                var context = Y.merge(parentContext, options.hash);
                delete context.context;

                if (!partial) {
                    return '';
                }
                return new Y.Handlebars.SafeString(Y.Handlebars.compile(partial)(context));
            });
        }

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('createmedia', COMPONENTNAME),
            focusAfterHide: true,
            width: 660,
            focusOnShowSelector: SELECTORS.URL_INPUT
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent(this.get('host').getSelection())).show();
        M.form.shortforms({formid: this.get('host').get('elementid') + '_atto_media_form'});
    },

    /**
     * Returns the dialogue content for the tool.
     *
     * @method _getDialogueContent
     * @param  {WrappedRange[]} selection Current editor selection
     * @return {Y.Node}
     * @private
     */
    _getDialogueContent: function(selection) {
        var content = Y.Node.create(
            Y.Handlebars.compile(TEMPLATES.ROOT)(this._getContext())
        );

        var medium = this.get('host').getSelectedNodes().filter('video,audio').shift();
        var mediumProperties = medium ? this._getMediumProperties(medium) : false;
        return this._attachEvents(this._applyMediumProperties(content, mediumProperties), selection);
    },

    /**
     * Attaches required events to the content node.
     *
     * @method _attachEvents
     * @param  {Y.Node}         content The content to which events will be attached
     * @param  {WrappedRange[]} selection Current editor selection
     * @return {Y.Node}
     * @private
     */
    _attachEvents: function(content, selection) {
        // Delegate add component link for media source fields.
        content.delegate('click', function(e) {
            e.preventDefault();
            this._addMediaSourceComponent(e.currentTarget);
        }, SELECTORS.MEDIA_SOURCE + ' .addcomponent', this);

        // Delegate add component link for track fields.
        content.delegate('click', function(e) {
            e.preventDefault();
            this._addTrackComponent(e.currentTarget);
        }, SELECTORS.TRACK + ' .addcomponent', this);

        // Only allow one track per tab to be selected as "default".
        content.delegate('click', function(e) {
            var element = e.currentTarget;
            if (element.get('checked')) {
                var getKind = function(el) {
                    return this._getTrackTypeFromTabPane(el.ancestor('.tab-pane'));
                }.bind(this);

                element.ancestor('.root.tab-content').all(SELECTORS.TRACK_DEFAULT_SELECT).each(function(select) {
                    if (select !== element && getKind(element) === getKind(select)) {
                        select.set('checked', false);
                    }
                });
            }
        }, SELECTORS.TRACK_DEFAULT_SELECT, this);

        // Set up filepicker click event.
        content.delegate('click', function(e) {
            var element = e.currentTarget;
            var fptype = (element.ancestor(SELECTORS.POSTER_SOURCE) && 'image') ||
                    (element.ancestor(SELECTORS.TRACK_SOURCE) && 'subtitle') ||
                    'media';
            e.preventDefault();
            this.get('host').showFilepicker(fptype, this._getFilepickerCallback(element, fptype), this);
        }, '.openmediabrowser', this);

        // This is a nasty hack. Basically we are using BS4 markup for the tabs
        // but it isn't completely backwards compatible with BS2. The main problem is
        // that the "active" class goes on a different node. So the idea is to put it
        // the node for BS4, and then use CSS to make it look right in BS2. However,
        // once another tab is clicked, everything sorts itself out, more or less. Except
        // that the original "active" tab hasn't had the BS4 "active" class removed
        // (so the styles will still apply to it). So we need to remove the "active"
        // class on the BS4 node so that BS2 is happy.
        //
        // This doesn't upset BS4 since it removes this class anyway when clicking on
        // another tab.
        content.all('.nav-item').on('click', function(elem) {
            elem.currentTarget.get('parentNode').all('.active').removeClass('active');
        });

        content.one('.submit').on('click', function(e) {
            e.preventDefault();
            var mediaHTML = this._getMediaHTML(e.currentTarget.ancestor('.atto_form')),
                host = this.get('host');
            this.getDialogue({
                focusAfterHide: null
            }).hide();
            if (mediaHTML) {
                host.setSelection(selection);
                host.insertContentAtFocusPoint(mediaHTML);
                this.markUpdated();
            }
        }, this);

        return content;
    },

    /**
     * Applies medium properties to the content node.
     *
     * @method _applyMediumProperties
     * @param  {Y.Node} content The content to apply the properties to
     * @param  {object} properties The medium properties to apply
     * @return {Y.Node}
     * @private
     */
    _applyMediumProperties: function(content, properties) {
        if (!properties) {
            return content;
        }

        var applyTrackProperties = function(track, properties) {
            track.one(SELECTORS.TRACK_SOURCE + ' ' + SELECTORS.URL_INPUT).set('value', properties.src);
            track.one(SELECTORS.TRACK_LANG_INPUT).set('value', properties.srclang);
            track.one(SELECTORS.TRACK_LABEL_INPUT).set('value', properties.label);
            track.one(SELECTORS.TRACK_DEFAULT_SELECT).set('checked', properties.defaultTrack);
        };

        var tabPane = content.one('.root.tab-content > .tab-pane#' + this.get('host').get('elementid') +
                              '_' + properties.type.toLowerCase());

        // Populate sources.
        tabPane.one(SELECTORS.MEDIA_SOURCE + ' ' + SELECTORS.URL_INPUT).set('value', properties.sources[0]);
        Y.Array.each(properties.sources.slice(1), function(source) {
            this._addMediaSourceComponent(tabPane.one(SELECTORS.MEDIA_SOURCE + ' .addcomponent'), function(newComponent) {
                newComponent.one(SELECTORS.URL_INPUT).set('value', source);
            });
        }, this);

        // Populate tracks.
        Y.Object.each(properties.tracks, function(value, key) {
            var trackData = value.length ? value : [{src: '', srclang: '', label: '', defaultTrack: false}];
            var paneSelector = SELECTORS['TRACK_' + key.toUpperCase() + '_PANE'];

            applyTrackProperties(tabPane.one(paneSelector + ' ' + SELECTORS.TRACK), trackData[0]);
            Y.Array.each(trackData.slice(1), function(track) {
                this._addTrackComponent(
                    tabPane.one(paneSelector + ' ' + SELECTORS.TRACK + ' .addcomponent'), function(newComponent) {
                    applyTrackProperties(newComponent, track);
                });
            }, this);
        }, this);

        // Populate values.
        tabPane.one(SELECTORS.TITLE_INPUT).set('value', properties.title);
        tabPane.one(SELECTORS.MEDIA_CONTROLS_TOGGLE).set('checked', properties.controls);
        tabPane.one(SELECTORS.MEDIA_AUTOPLAY_TOGGLE).set('checked', properties.autoplay);
        tabPane.one(SELECTORS.MEDIA_MUTE_TOGGLE).set('checked', properties.muted);
        tabPane.one(SELECTORS.MEDIA_LOOP_TOGGLE).set('checked', properties.loop);

        // Determine medium type.
        var mediumType = this._getMediumTypeFromTabPane(tabPane);

        if (mediumType === 'video') {
            // Populate values unique for video.
            tabPane.one(SELECTORS.POSTER_SOURCE + ' ' + SELECTORS.URL_INPUT).setAttribute('value', properties.poster);
            tabPane.one(SELECTORS.WIDTH_INPUT).set('value', properties.width);
            tabPane.one(SELECTORS.HEIGHT_INPUT).set('value', properties.height);
        }

        // Switch to the correct tab.
        // Remove active class from all tabs + tab panes.
        tabPane.siblings('.active').removeClass('active');
        content.all('.root.nav-tabs .nav-item a').removeClass('active');

        // Add active class to the desired tab and tab pane.
        tabPane.addClass('active');
        content.one(SELECTORS[mediumType.toUpperCase() + '_TAB'] + ' a').addClass('active');

        return content;
    },

    /**
     * Extracts medium properties.
     *
     * @method _getMediumProperties
     * @param  {Y.Node} medium The medium node from which to extract
     * @return {Object}
     * @private
     */
    _getMediumProperties: function(medium) {
        var boolAttr = function(elem, attr) {
            return elem.getAttribute(attr) ? true : false;
        };

        var tracks = {
            subtitles: [],
            captions: [],
            descriptions: [],
            chapters: [],
            metadata: []
        };

        medium.all('track').each(function(track) {
            tracks[track.getAttribute('kind')].push({
                src: track.getAttribute('src'),
                srclang: track.getAttribute('srclang'),
                label: track.getAttribute('label'),
                defaultTrack: boolAttr(track, 'default')
            });
        });

        return {
            type: medium.test('video') ? MEDIA_TYPES.VIDEO : MEDIA_TYPES.AUDIO,
            sources: medium.all('source').get('src'),
            poster: medium.getAttribute('poster'),
            title: medium.getAttribute('title'),
            width: medium.getAttribute('width'),
            height: medium.getAttribute('height'),
            autoplay: boolAttr(medium, 'autoplay'),
            loop: boolAttr(medium, 'loop'),
            muted: boolAttr(medium, 'muted'),
            controls: boolAttr(medium, 'controls'),
            tracks: tracks
        };
    },

    /**
     * Adds a track form component.
     *
     * @method _addTrackComponent
     * @param  {Y.Node}   element    The element which was used to trigger this function
     * @param  {Function} [callback] Function to be called when the new component is added
     *     @param {Y.Node}    callback.newComponent The compiled component
     * @private
     */
    _addTrackComponent: function(element, callback) {
        var trackType = this._getTrackTypeFromTabPane(element.ancestor('.tab-pane'));
        var context = this._getContext({
            sourcelabel: trackType + 'sourcelabel',
            addcomponentlabel: 'add' + trackType + 'track'
        });

        this._addComponent(element, TEMPLATES.FORM_COMPONENTS.TRACK, SELECTORS.TRACK, context, callback);
    },

    /**
     * Adds a media source form component.
     *
     * @method _addMediaSourceComponent
     * @param  {Y.Node}   element    The element which was used to trigger this function
     * @param  {Function} [callback] Function to be called when the new component is added
     *     @param {Y.Node}    callback.newComponent The compiled component
     * @private
     */
    _addMediaSourceComponent: function(element, callback) {
        var mediumType = this._getMediumTypeFromTabPane(element.ancestor('.tab-pane'));
        var context = this._getContext({
            multisource: true,
            id: CSS.MEDIA_SOURCE,
            entersourcelabel: mediumType + 'sourcelabel',
            addcomponentlabel: 'addsource',
            addsourcehelp: this.get('help').addsource
        });
        this._addComponent(element, TEMPLATES.FORM_COMPONENTS.SOURCE, SELECTORS.MEDIA_SOURCE, context, callback);
    },

    /**
     * Adds an arbitrary form component.
     *
     * This function Compiles and adds the provided component in the supplied 'ancestor' container.
     * It will also add links to add/remove the relevant components, attaching the
     * necessary events.
     *
     * @method _addComponent
     * @param  {Y.Node}   element    The element which was used to trigger this function
     * @param  {String}   component  The component to compile and add
     * @param  {String}   ancestor   A selector used to find an ancestor of 'component', to which
     *                               the compiled component will be appended
     * @param  {Object}   context    The context with which to render the component
     * @param  {Function} [callback] Function to be called when the new component is added
     *     @param {Y.Node}    callback.newComponent The compiled component
     * @private
     */
    _addComponent: function(element, component, ancestor, context, callback) {
        var currentComponent = element.ancestor(ancestor),
            newComponent = Y.Node.create(Y.Handlebars.compile(component)(context)),
            removeNodeContext = this._getContext(context);

        removeNodeContext.label = "remove";
        var removeNode = Y.Node.create(Y.Handlebars.compile(TEMPLATES.FORM_COMPONENTS.REMOVE_COMPONENT)(removeNodeContext));

        removeNode.one('.removecomponent').on('click', function(e) {
            e.preventDefault();
            currentComponent.remove(true);
        });

        currentComponent.insert(newComponent, 'after');
        element.ancestor().insert(removeNode, 'after');
        element.ancestor().remove(true);

        if (callback) {
            callback.call(this, newComponent);
        }
    },

    /**
     * Returns the callback for the file picker to call after a file has been selected.
     *
     * @method _getFilepickerCallback
     * @param  {Y.Node} element The element which triggered the callback
     * @param  {String} fptype  The file pickertype (as would be passed to `showFilePicker`)
     * @return {Function} The function to be used as a callback when the file picker returns the file
     * @private
     */
    _getFilepickerCallback: function(element, fptype) {
        return function(params) {
            if (params.url !== '') {
                var tabPane = element.ancestor('.tab-pane');
                element.ancestor(SELECTORS.SOURCE).one(SELECTORS.URL_INPUT).set('value', params.url);

                // Links (and only links) have a name field.
                if (tabPane.get('id') === this.get('host').get('elementid') + '_' + CSS.LINK) {
                    tabPane.one(SELECTORS.NAME_INPUT).set('value', params.file);
                }

                if (fptype === 'subtitle') {
                    var subtitleLang = params.file.split('.vtt')[0].split('-').slice(-1)[0];
                    var langObj = this.get('langs').available.reduce(function(carry, lang) {
                        return lang.code === subtitleLang ? lang : carry;
                    }, false);
                    if (langObj) {
                        element.ancestor(SELECTORS.TRACK).one(SELECTORS.TRACK_LABEL_INPUT).set('value',
                                langObj.lang.substr(0, langObj.lang.lastIndexOf(' ')));
                        element.ancestor(SELECTORS.TRACK).one(SELECTORS.TRACK_LANG_INPUT).set('value', langObj.code);
                    }
                }
            }
        };
    },

    /**
     * Given a "medium" tab pane, returns what kind of medium it contains.
     *
     * @method _getMediumTypeFromTabPane
     * @param  {Y.Node} tabPane The tab pane
     * @return {String} The type of medium in the pane
     */
    _getMediumTypeFromTabPane: function(tabPane) {
        return tabPane.getAttribute('data-medium-type');
    },

    /**
     * Given a "track" tab pane, returns what kind of track it contains.
     *
     * @method _getTrackTypeFromTabPane
     * @param  {Y.Node} tabPane The tab pane
     * @return {String} The type of track in the pane
     */
    _getTrackTypeFromTabPane: function(tabPane) {
        return tabPane.getAttribute('data-track-kind');
    },

    /**
     * Returns the HTML to be inserted to the text area.
     *
     * @method _getMediaHTML
     * @param  {Y.Node} form The form from which to extract data
     * @return {String} The compiled markup
     * @private
     */
    _getMediaHTML: function(form) {
        var mediumType = this._getMediumTypeFromTabPane(form.one('.root.tab-content > .tab-pane.active'));
        var tabContent = form.one(SELECTORS[mediumType.toUpperCase() + '_PANE']);

        return this['_getMediaHTML' + mediumType[0].toUpperCase() + mediumType.substr(1)](tabContent);
    },

    /**
     * Returns the HTML to be inserted to the text area for the link tab.
     *
     * @method _getMediaHTMLLink
     * @param  {Y.Node} tab The tab from which to extract data
     * @return {String} The compiled markup
     * @private
     */
    _getMediaHTMLLink: function(tab) {
        var context = {
            url: tab.one(SELECTORS.URL_INPUT).get('value'),
            name: tab.one(SELECTORS.NAME_INPUT).get('value') || false
        };

        return context.url ? Y.Handlebars.compile(TEMPLATES.HTML_MEDIA.LINK)(context) : '';
    },

    /**
     * Returns the HTML to be inserted to the text area for the video tab.
     *
     * @method _getMediaHTMLVideo
     * @param  {Y.Node} tab The tab from which to extract data
     * @return {String} The compiled markup
     * @private
     */
    _getMediaHTMLVideo: function(tab) {
        var context = this._getContextForMediaHTML(tab);
        context.width = tab.one(SELECTORS.WIDTH_INPUT).get('value') || false;
        context.height = tab.one(SELECTORS.HEIGHT_INPUT).get('value') || false;
        context.poster = tab.one(SELECTORS.POSTER_SOURCE + ' ' + SELECTORS.URL_INPUT).get('value') || false;

        return context.sources.length ? Y.Handlebars.compile(TEMPLATES.HTML_MEDIA.VIDEO)(context) : '';
    },

    /**
     * Returns the HTML to be inserted to the text area for the audio tab.
     *
     * @method _getMediaHTMLAudio
     * @param  {Y.Node} tab The tab from which to extract data
     * @return {String} The compiled markup
     * @private
     */
    _getMediaHTMLAudio: function(tab) {
        var context = this._getContextForMediaHTML(tab);

        return context.sources.length ? Y.Handlebars.compile(TEMPLATES.HTML_MEDIA.AUDIO)(context) : '';
    },

    /**
     * Returns the context with which to render a media template.
     *
     * @method _getContextForMediaHTML
     * @param  {Y.Node} tab The tab from which to extract data
     * @return {Object}
     * @private
     */
    _getContextForMediaHTML: function(tab) {
        var tracks = [];

        tab.all(SELECTORS.TRACK).each(function(track) {
            tracks.push({
                track: track.one(SELECTORS.TRACK_SOURCE + ' ' + SELECTORS.URL_INPUT).get('value'),
                kind: this._getTrackTypeFromTabPane(track.ancestor('.tab-pane')),
                label: track.one(SELECTORS.TRACK_LABEL_INPUT).get('value') ||
                    track.one(SELECTORS.TRACK_LANG_INPUT).get('value'),
                srclang: track.one(SELECTORS.TRACK_LANG_INPUT).get('value'),
                defaultTrack: track.one(SELECTORS.TRACK_DEFAULT_SELECT).get('checked') ? "true" : null
            });
        }, this);

        return {
            sources: tab.all(SELECTORS.MEDIA_SOURCE + ' ' + SELECTORS.URL_INPUT).get('value').filter(function(source) {
                return !!source;
            }).map(function(source) {
                return {source: source};
            }),
            description: tab.one(SELECTORS.MEDIA_SOURCE + ' ' + SELECTORS.URL_INPUT).get('value') || false,
            tracks: tracks.filter(function(track) {
                return !!track.track;
            }),
            showControls: tab.one(SELECTORS.MEDIA_CONTROLS_TOGGLE).get('checked'),
            autoplay: tab.one(SELECTORS.MEDIA_AUTOPLAY_TOGGLE).get('checked'),
            muted: tab.one(SELECTORS.MEDIA_MUTE_TOGGLE).get('checked'),
            loop: tab.one(SELECTORS.MEDIA_LOOP_TOGGLE).get('checked'),
            title: tab.one(SELECTORS.TITLE_INPUT).get('value') || false
        };
    }
}, {
    ATTRS: {
        langs: {},
        help: {}
    }
});
