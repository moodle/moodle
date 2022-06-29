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
 * Behat data generator for mod_glossary.
 *
 * @package   mod_glossary
 * @category  test
 * @copyright 2021 Noel De Martin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_glossary_generator extends behat_generator_base {

    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'categories' => [
                'singular' => 'category',
                'datagenerator' => 'category',
                'required' => ['glossary', 'name'],
                'switchids' => ['glossary' => 'glossaryid'],
            ],
            'entries' => [
                'singular' => 'entry',
                'datagenerator' => 'entry',
                'required' => ['glossary', 'concept', 'definition'],
                'switchids' => ['glossary' => 'glossaryid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Get the glossary id using an activity idnumber.
     *
     * @param string $idnumber
     * @return int The glossary id
     */
    protected function get_glossary_id(string $idnumber): int {
        $cm = $this->get_cm_by_activity_name('glossary', $idnumber);

        return $cm->instance;
    }

    /**
     * Add a category.
     *
     * @param array $data Category data.
     */
    public function process_category(array $data) {
        global $DB;

        $glossary = $DB->get_record('glossary', ['id' => $data['glossaryid']], '*', MUST_EXIST);

        unset($data['glossaryid']);

        $this->get_data_generator()->create_category($glossary, $data);
    }

    /**
     * Preprocess entry data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_entry(array $data): array {
        if (isset($data['categories'])) {
            $categorynames = array_map('trim', explode(',', $data['categories']));
            $categoryids = array_map(function ($categoryname) {
                global $DB;

                if (!$id = $DB->get_field('glossary_categories', 'id', ['name' => $categoryname])) {
                    throw new Exception('The specified category with name "' . $categoryname . '" could not be found.');
                }

                return $id;
            }, $categorynames);

            $data['categoryids'] = $categoryids;
            unset($data['categories']);
        }

        return $data;
    }

    /**
     * Get the module data generator.
     *
     * @return mod_glossary_generator Glossary data generator.
     */
    protected function get_data_generator() {
        return $this->componentdatagenerator;
    }
}
