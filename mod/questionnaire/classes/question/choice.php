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

namespace mod_questionnaire\question;

/**
 * This defines a structured class to hold question choices.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 * @copyright 2019, onwards Poet
 */
class choice {

    // Class properties.

    /** The table name. */
    const TABLE = 'questionnaire_quest_choice';

    /** @var int $id The id of the question choice this applies to. */
    public $id;

    /** @var int $questionid The id of the question this choice applies to. */
    public $questionid;

    /** @var string $content The display content for this choice. */
    public $content;

    /** @var string $value Optional value assigned to this choice. */
    public $value;

    /**
     * Choice constructor.
     * @param int $id
     * @param int $questionid
     * @param string $content
     * @param mixed $value
     */
    public function __construct($id = null, $questionid = null, $content = null, $value = null) {
        $this->id = $id;
        $this->questionid = $questionid;
        $this->content = $content;
        $this->value = $value;
    }

    /**
     * Create and return a choice object from a data id. If not found, an empty object is returned.
     *
     * @param int $id The data id to load.
     * @return choice
     */
    public static function create_from_id($id) {
        global $DB;

        // Rename the data field question_id to questionid to conform with code conventions. Eventually, data table should be
        // changed.
        if ($record = $DB->get_record(self::tablename(), ['id' => $id], 'id,question_id as questionid,content,value')) {
            return new choice($id, $record->questionid, $record->content, $record->value);
        } else {
            return new choice();
        }
    }

    /**
     * Create and return a choice object from data.
     *
     * @param \stdclass|array $choicedata The data to load.
     * @return choice
     */
    public static function create_from_data($choicedata) {
        if (!is_array($choicedata)) {
            $choicedata = (array)$choicedata;
        }

        $properties = array_keys(get_class_vars(__CLASS__));
        foreach ($properties as $property) {
            if (!isset($choicedata[$property])) {
                $choicedata[$property] = null;
            }
        }
        // Since the data table uses 'question_id' instead of 'questionid', look for that field as well. Hack that should be fixed
        // by renaming the data table column.
        if (!empty($choicedata['question_id'])) {
            $choicedata['questionid'] = $choicedata['question_id'];
        }

        return new choice($choicedata['id'], $choicedata['questionid'], $choicedata['content'], $choicedata['value']);
    }

    /**
     * Return the table name for choice.
     */
    public static function tablename() {
        return self::TABLE;
    }

    /**
     * Delete the choice record.
     * @param int $id
     * @return bool
     */
    public static function delete_from_db_by_id($id) {
        global $DB;
        return $DB->delete_records(self::tablename(), ['id' => $id]);
    }

    /**
     * Delete this record from the DB.
     * @return bool
     */
    public function delete_from_db() {
        return self::delete_from_db_by_id($this->id);
    }

    /**
     * Return true if the content string is an "other" choice.
     *
     * @param string $content
     * @return bool
     */
    public static function content_is_other_choice($content) {
        return (strpos($content, '!other') === 0);
    }

    /**
     * Return true if the choice object is an "other" choice.
     *
     * @return bool
     */
    public function is_other_choice() {
        return (self::content_is_other_choice($this->content));
    }

    /**
     * Return the string to display for an "other" option content string. If the option is not an "other", return false.
     *
     * @param string $content
     * @return string|bool
     */
    public static function content_other_choice_display($content) {
        if (!self::content_is_other_choice($content)) {
            return false;
        }

        // If there is a defined string display after the "=", return it. Otherwise the "other" language string.
        return preg_replace(["/^!other=/", "/^!other/"], ['', get_string('other', 'questionnaire')], $content);
    }

    /**
     * Return the string to display for an "other" option for this object. If the option is not an "other", return false.
     *
     * @return string|bool
     */
    public function other_choice_display() {
        return self::content_other_choice_display($this->content);
    }

    /**
     * Is the content a named degree rate choice.
     * @param string $content
     * @return array|bool
     */
    public static function content_is_named_degree_choice($content) {
        if (preg_match("/^([0-9]{1,3})=(.*)$/", $content, $ndegrees)) {
            return [$ndegrees[1] => $ndegrees[2]];
        } else {
            return false;
        }
    }

    /**
     * Is the choice object a named degree rate choice.
     * @return array|bool
     */
    public function is_named_degree_choice() {
        return self::content_is_named_degree_choice($this->content);
    }

    /**
     * Return the string to use as an input name for an other choice.
     *
     * @param int $choiceid
     * @return string
     */
    public static function id_other_choice_name($choiceid) {
        return 'o' . $choiceid;
    }

    /**
     * Return the string to use as an input name for an other choice.
     * @return string
     */
    public function other_choice_name() {
        return self::id_other_choice_name($this->id);
    }
}
