<?php
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
 * Atto text editor integration version file.
 *
 * @package    atto_media
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialise the js strings required for this plugin
 */
function atto_media_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('add',
                                          'addcaptionstrack',
                                          'addchapterstrack',
                                          'adddescriptionstrack',
                                          'addmetadatatrack',
                                          'addsource',
                                          'addsubtitlestrack',
                                          'addtrack',
                                          'advancedsettings',
                                          'audio',
                                          'audiosourcelabel',
                                          'autoplay',
                                          'browserepositories',
                                          'browserepositories',
                                          'captions',
                                          'captionssourcelabel',
                                          'chapters',
                                          'chapterssourcelabel',
                                          'controls',
                                          'createmedia',
                                          'default',
                                          'descriptions',
                                          'descriptionssourcelabel',
                                          'displayoptions',
                                          'entername',
                                          'entertitle',
                                          'entersource',
                                          'enterurl',
                                          'height',
                                          'kind',
                                          'label',
                                          'languagesavailable',
                                          'languagesinstalled',
                                          'link',
                                          'loop',
                                          'metadata',
                                          'metadatasourcelabel',
                                          'mute',
                                          'poster',
                                          'remove',
                                          'size',
                                          'srclang',
                                          'subtitles',
                                          'subtitlessourcelabel',
                                          'track',
                                          'tracks',
                                          'video',
                                          'videoheight',
                                          'videosourcelabel',
                                          'videowidth',
                                          'width'),
                                          'atto_media');
    $PAGE->requires->strings_for_js(array('expand', 'collapse'), 'moodle');
}

/**
 * Sends the parameters to the JS module.
 *
 * @return array
 */
function atto_media_params_for_js() {
    global $OUTPUT;
    global $PAGE;
    $currentlang = current_language();
    $langsinstalled = get_string_manager()->get_list_of_translations(true);
    $langsavailable = get_string_manager()->get_list_of_languages();
    $params = [
        'langs' => ['installed' => [], 'available' => []],
        'help' => []
    ];

    foreach ($langsinstalled as $code => $name) {
        $params['langs']['installed'][] = [
            'lang' => $name,
            'code' => $code,
            'default' => $currentlang == $code
        ];
    }

    foreach ($langsavailable as $code => $name) {
        // See MDL-50829 for an explanation of this lrm thing.
        $lrm = json_decode('"\u200E"');
        $params['langs']['available'][] = [
            'lang' => $name . ' ' . $lrm . '(' . $code . ')' . $lrm, 'code' => $code];
    }

    $params['help'] = [
        'addsource' => $OUTPUT->help_icon('addsource', 'atto_media'),
        'tracks' => $OUTPUT->help_icon('tracks', 'atto_media'),
        'subtitles' => $OUTPUT->help_icon('subtitles', 'atto_media'),
        'captions' => $OUTPUT->help_icon('captions', 'atto_media'),
        'descriptions' => $OUTPUT->help_icon('descriptions', 'atto_media'),
        'chapters' => $OUTPUT->help_icon('chapters', 'atto_media'),
        'metadata' => $OUTPUT->help_icon('metadata', 'atto_media')
    ];

    return $params;
}
