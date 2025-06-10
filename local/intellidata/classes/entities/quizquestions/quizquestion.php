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
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizquestions;

use local_intellidata\helpers\ParamsHelper;

/**
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizquestion extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'quizquestions';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Question ID.',
                'default' => 0,
            ],
            'name' => [
                'type' => PARAM_RAW,
                'description' => 'Question name.',
                'default' => '',
            ],
            'questiontext' => [
                'type' => PARAM_RAW,
                'description' => 'Question text.',
                'default' => '',
            ],
            'qtype' => [
                'type' => PARAM_TEXT,
                'description' => 'Question type.',
                'default' => '',
            ],
            'defaultmark' => [
                'type' => PARAM_FLOAT,
                'description' => 'Possible score.',
                'default' => 1,
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        $object->questiontext = substr($object->questiontext, 0, 5000);
        return $object;
    }
}
