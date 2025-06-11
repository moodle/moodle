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

namespace core;

use pix_emoticon;
use stdClass;

/**
 * Provides core support for plugins that have to deal with emoticons (like HTML editor or emoticon filter).
 *
 * Whenever this manager mentiones 'emoticon object', the following data structure is expected:
 * stdClass with properties text, imagename, imagecomponent, altidentifier and altcomponent
 *
 * @see \admin_setting_emoticons
 *
 * @package     core
 * @copyright   2010 David Mudrak
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emoticon_manager {

    /**
     * Returns the currently enabled emoticons
     *
     * @param bool $selectable If true, only return emoticons that should be selectable from a list
     * @return stdClass[] array of emoticon objects
     */
    public function get_emoticons(
        bool $selectable = false,
    ): array {
        global $CFG;

        $notselectable = ['martin', 'egg'];

        if (empty($CFG->emoticons)) {
            return [];
        }

        $emoticons = $this->decode_stored_config($CFG->emoticons);

        if (!is_array($emoticons)) {
            // Something is wrong with the format of stored setting.
            debugging('Invalid format of emoticons setting, please resave the emoticons settings form', DEBUG_NORMAL);
            return [];
        }

        if ($selectable) {
            foreach ($emoticons as $index => $emote) {
                if (in_array($emote->altidentifier, $notselectable)) {
                    // Skip this one.
                    unset($emoticons[$index]);
                }
            }
        }

        return $emoticons;
    }

    /**
     * Converts emoticon object into renderable pix_emoticon object
     *
     * @param stdClass $emoticon emoticon object
     * @param array $attributes explicit HTML attributes to set
     * @return pix_emoticon
     */
    public function prepare_renderable_emoticon(
        stdClass $emoticon,
        array $attributes = [],
    ): pix_emoticon {
        $stringmanager = get_string_manager();
        if ($stringmanager->string_exists($emoticon->altidentifier, $emoticon->altcomponent)) {
            $alt = get_string($emoticon->altidentifier, $emoticon->altcomponent);
        } else {
            $alt = s($emoticon->text);
        }
        return new pix_emoticon($emoticon->imagename, $alt, $emoticon->imagecomponent, $attributes);
    }

    /**
     * Encodes the array of emoticon objects into a string storable in config table
     *
     * @param stdClass[] $emoticons array of emoticon objects
     * @return string
     */
    public function encode_stored_config(
        array $emoticons,
    ): string {
        return json_encode($emoticons);
    }

    /**
     * Decodes the string into an array of emoticon objects
     *
     * @param string $encoded
     * @return ?array
     */
    public function decode_stored_config(
        string $encoded,
    ): ?array {
        $decoded = json_decode($encoded);
        if (!is_array($decoded)) {
            return null;
        }
        return $decoded;
    }

    /**
     * Returns default set of emoticons supported by Moodle
     *
     * @return stdClass[] array of emoticon objects
     */
    public function default_emoticons(): array {
        return [
            $this->prepare_emoticon_object(":-)", 's/smiley', 'smiley'),
            $this->prepare_emoticon_object(":)", 's/smiley', 'smiley'),
            $this->prepare_emoticon_object(":-D", 's/biggrin', 'biggrin'),
            $this->prepare_emoticon_object(";-)", 's/wink', 'wink'),
            $this->prepare_emoticon_object(":-/", 's/mixed', 'mixed'),
            $this->prepare_emoticon_object("V-.", 's/thoughtful', 'thoughtful'),
            $this->prepare_emoticon_object(":-P", 's/tongueout', 'tongueout'),
            $this->prepare_emoticon_object(":-p", 's/tongueout', 'tongueout'),
            $this->prepare_emoticon_object("B-)", 's/cool', 'cool'),
            $this->prepare_emoticon_object("^-)", 's/approve', 'approve'),
            $this->prepare_emoticon_object("8-)", 's/wideeyes', 'wideeyes'),
            $this->prepare_emoticon_object(":o)", 's/clown', 'clown'),
            $this->prepare_emoticon_object(":-(", 's/sad', 'sad'),
            $this->prepare_emoticon_object(":(", 's/sad', 'sad'),
            $this->prepare_emoticon_object("8-.", 's/shy', 'shy'),
            $this->prepare_emoticon_object(":-I", 's/blush', 'blush'),
            $this->prepare_emoticon_object(":-X", 's/kiss', 'kiss'),
            $this->prepare_emoticon_object("8-o", 's/surprise', 'surprise'),
            $this->prepare_emoticon_object("P-|", 's/blackeye', 'blackeye'),
            $this->prepare_emoticon_object("8-[", 's/angry', 'angry'),
            $this->prepare_emoticon_object("(grr)", 's/angry', 'angry'),
            $this->prepare_emoticon_object("xx-P", 's/dead', 'dead'),
            $this->prepare_emoticon_object("|-.", 's/sleepy', 'sleepy'),
            $this->prepare_emoticon_object("}-]", 's/evil', 'evil'),
            $this->prepare_emoticon_object("(h)", 's/heart', 'heart'),
            $this->prepare_emoticon_object("(heart)", 's/heart', 'heart'),
            $this->prepare_emoticon_object("(y)", 's/yes', 'yes', 'core'),
            $this->prepare_emoticon_object("(n)", 's/no', 'no', 'core'),
            $this->prepare_emoticon_object("(martin)", 's/martin', 'martin'),
            $this->prepare_emoticon_object("( )", 's/egg', 'egg'),
        ];
    }

    /**
     * Helper method preparing an emoticon object
     *
     * @param string|string[] $text
     * @param string $imagename to be used by {@see pix_emoticon}
     * @param ?string $altidentifier alternative string identifier, null for no alt
     * @param string $altcomponent where the alternative string is defined
     * @param string $imagecomponent to be used by {@see pix_emoticon}
     * @return stdClass
     */
    protected function prepare_emoticon_object(
        string|array $text,
        string $imagename,
        ?string $altidentifier = null,
        string $altcomponent = 'core_pix',
        string $imagecomponent = 'core',
    ): stdClass {
        return (object) [
            'text' => $text,
            'imagename' => $imagename,
            'altidentifier' => $altidentifier,
            'altcomponent' => $altcomponent,
            'imagecomponent' => $imagecomponent,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(emoticon_manager::class, \emoticon_manager::class);
