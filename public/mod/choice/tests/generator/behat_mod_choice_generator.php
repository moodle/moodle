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
 * Behat generator for choice.
 *
 * @package    mod_choice
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_choice_generator extends behat_generator_base {
    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'responses' => [
                'singular' => 'response',
                'datagenerator' => 'response',
                'required' => ['choice', 'user'],
                'switchids' => ['choice' => 'choiceid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Get the choice id using an activity idnumber or name.
     *
     * @param string $idnumberorname The choice activity idnumber or name.
     * @return int The choice id
     */
    protected function get_choice_id(string $idnumberorname): int {
        return $this->get_cm_by_activity_name('choice', $idnumberorname)->instance;
    }

    /**
     * Preprocess answer data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_response(array $data) {
        global $DB;
        $choices = $DB->get_records_menu('choice_options', ['choiceid' => $data['choiceid']], '', 'id,text');

        $optionstoid = array_map(
            function($choice) {
                return strtolower(trim($choice));
            },
            $choices
        );
        $optionstoid = array_flip($optionstoid);

        $responsesfield = $data['responses'] ?? ($data['response'] ?? '');
        $responsesitems = explode(',', strtolower($responsesfield));
        $responseitems = array_filter(array_map('trim', $responsesitems));

        $userresponses = [];
        foreach ($responseitems as $response) {
            if (isset($optionstoid[$response])) {
                $userresponses[] = $optionstoid[$response];
            }
        }

        // If only one valid response, we just return a scalar not an array.
        $data['responses'] = count($userresponses) === 1 ? $userresponses[0] : $userresponses;

        return $data;
    }
}
