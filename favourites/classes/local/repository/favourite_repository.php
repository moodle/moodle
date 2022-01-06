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
 * Contains the favourite_repository class, responsible for CRUD operations for favourites.
 *
 * @package   core_favourites
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local\repository;
use \core_favourites\local\entity\favourite;

defined('MOODLE_INTERNAL') || die();

/**
 * Class favourite_repository.
 *
 * This class handles persistence of favourites. Favourites from all areas are supported by this repository.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class favourite_repository implements favourite_repository_interface {

    /**
     * @var string the name of the table which favourites are stored in.
     */
    protected $favouritetable = 'favourite';

    /**
     * Get a favourite object, based on a full record.
     * @param \stdClass $record the record we wish to hydrate.
     * @return favourite the favourite record.
     */
    protected function get_favourite_from_record(\stdClass $record) : favourite {
        $favourite = new favourite(
            $record->component,
            $record->itemtype,
            $record->itemid,
            $record->contextid,
            $record->userid
        );
        $favourite->id = $record->id;
        $favourite->ordering = $record->ordering ?? null;
        $favourite->timecreated = $record->timecreated ?? null;
        $favourite->timemodified = $record->timemodified ?? null;

        return $favourite;
    }

    /**
     * Get a list of favourite objects, based on a list of records.
     * @param array $records the record we wish to hydrate.
     * @return array the list of favourites.
     */
    protected function get_list_of_favourites_from_records(array $records) {
        $list = [];
        foreach ($records as $index => $record) {
            $list[$index] = $this->get_favourite_from_record($record);
        }
        return $list;
    }

    /**
     * Basic validation, confirming we have the minimum field set needed to save a record to the store.
     *
     * @param favourite $favourite the favourite record to validate.
     * @throws \moodle_exception if the supplied favourite has missing or unsupported fields.
     */
    protected function validate(favourite $favourite) {

        $favourite = (array)$favourite;

        // The allowed fields, and whether or not each is required to create a record.
        // The timecreated, timemodified and id fields are generated during create/update.
        $allowedfields = [
            'userid' => true,
            'component' => true,
            'itemtype' => true,
            'itemid' => true,
            'contextid' => true,
            'ordering' => false,
            'timecreated' => false,
            'timemodified' => false,
            'id' => false
        ];

        $requiredfields = array_filter($allowedfields, function($field) {
            return $field;
        });

        if ($missingfields = array_keys(array_diff_key($requiredfields, $favourite))) {
            throw new \moodle_exception("Missing object property(s) '" . join(', ', $missingfields) . "'.");
        }

        // If the record contains fields we don't allow, throw an exception.
        if ($unsupportedfields = array_keys(array_diff_key($favourite, $allowedfields))) {
            throw new \moodle_exception("Unexpected object property(s) '" . join(', ', $unsupportedfields) . "'.");
        }
    }

    /**
     * Add a favourite to the repository.
     *
     * @param favourite $favourite the favourite to add.
     * @return favourite the favourite which has been stored.
     * @throws \dml_exception if any database errors are encountered.
     * @throws \moodle_exception if the favourite has missing or invalid properties.
     */
    public function add(favourite $favourite) : favourite {
        global $DB;
        $this->validate($favourite);
        $favourite = (array)$favourite;
        $time = time();
        $favourite['timecreated'] = $time;
        $favourite['timemodified'] = $time;
        $id = $DB->insert_record($this->favouritetable, $favourite);
        return $this->find($id);
    }

    /**
     * Add a collection of favourites to the repository.
     *
     * @param array $items the list of favourites to add.
     * @return array the list of favourites which have been stored.
     * @throws \dml_exception if any database errors are encountered.
     * @throws \moodle_exception if any of the favourites have missing or invalid properties.
     */
    public function add_all(array $items) : array {
        global $DB;
        $time = time();
        foreach ($items as $item) {
            $this->validate($item);
            $favourite = (array)$item;
            $favourite['timecreated'] = $time;
            $favourite['timemodified'] = $time;
            $ids[] = $DB->insert_record($this->favouritetable, $favourite);
        }
        list($insql, $params) = $DB->get_in_or_equal($ids);
        $records = $DB->get_records_select($this->favouritetable, "id $insql", $params);
        return $this->get_list_of_favourites_from_records($records);
    }

    /**
     * Find a favourite by id.
     *
     * @param int $id the id of the favourite.
     * @return favourite the favourite.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function find(int $id) : favourite {
        global $DB;
        $record = $DB->get_record($this->favouritetable, ['id' => $id], '*', MUST_EXIST);
        return $this->get_favourite_from_record($record);
    }

    /**
     * Return all items in this repository, as an array, indexed by id.
     *
     * @param int $limitfrom optional pagination control for returning a subset of records, starting at this point.
     * @param int $limitnum optional pagination control for returning a subset comprising this many records.
     * @return array the list of all favourites stored within this repository.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function find_all(int $limitfrom = 0, int $limitnum = 0) : array {
        global $DB;
        $records = $DB->get_records($this->favouritetable, null, '', '*', $limitfrom, $limitnum);
        return $this->get_list_of_favourites_from_records($records);
    }

    /**
     * Return all items matching the supplied criteria (a [key => value,..] list).
     *
     * @param array $criteria the list of key/value(s) criteria pairs.
     * @param int $limitfrom optional pagination control for returning a subset of records, starting at this point.
     * @param int $limitnum optional pagination control for returning a subset comprising this many records.
     * @return array the list of favourites matching the criteria.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function find_by(array $criteria, int $limitfrom = 0, int $limitnum = 0) : array {
        global $DB;
        $conditions = [];
        $params = [];
        foreach ($criteria as $field => $value) {
            if (is_array($value) && count($value)) {
                list($insql, $inparams) = $DB->get_in_or_equal($value, SQL_PARAMS_NAMED);
                $conditions[] = "$field $insql";
                $params = array_merge($params, $inparams);
            } else {
                $conditions[] = "$field = :$field";
                $params = array_merge($params, [$field => $value]);
            }
        }

        $records = $DB->get_records_select($this->favouritetable, implode(' AND ', $conditions), $params,
            '', '*', $limitfrom, $limitnum);

        return $this->get_list_of_favourites_from_records($records);
    }

    /**
     * Find a specific favourite, based on the properties known to identify it.
     *
     * Used if we don't know its id.
     *
     * @param int $userid the id of the user to which the favourite belongs.
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $itemid the id of the item which was favourited (not the favourite's id).
     * @param int $contextid the contextid of the item which was favourited.
     * @return favourite the favourite.
     * @throws \dml_exception if any database errors are encountered or if the record could not be found.
     */
    public function find_favourite(int $userid, string $component, string $itemtype, int $itemid, int $contextid) : favourite {
        global $DB;
        // Favourites model: We know that only one favourite can exist based on these properties.
        $record = $DB->get_record($this->favouritetable, [
            'userid' => $userid,
            'component' => $component,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'contextid' => $contextid
        ], '*', MUST_EXIST);
        return $this->get_favourite_from_record($record);
    }

    /**
     * Check whether a favourite exists in this repository, based on its id.
     *
     * @param int $id the id to search for.
     * @return bool true if the favourite exists, false otherwise.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function exists(int $id) : bool {
        global $DB;
        return $DB->record_exists($this->favouritetable, ['id' => $id]);
    }

    /**
     * Check whether an item exists in this repository, based on the specified criteria.
     *
     * @param array $criteria the list of key/value criteria pairs.
     * @return bool true if the favourite exists, false otherwise.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function exists_by(array $criteria) : bool {
        global $DB;
        return $DB->record_exists($this->favouritetable, $criteria);
    }

    /**
     * Update a favourite.
     *
     * @param favourite $favourite the favourite to update.
     * @return favourite the updated favourite.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function update(favourite $favourite) : favourite {
        global $DB;
        $time = time();
        $favourite->timemodified = $time;
        $DB->update_record($this->favouritetable, $favourite);
        return $this->find($favourite->id);
    }

    /**
     * Delete a favourite, by id.
     *
     * @param int $id the id of the favourite to delete.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function delete(int $id) {
        global $DB;
        $DB->delete_records($this->favouritetable, ['id' => $id]);
    }

    /**
     * Delete all favourites matching the specified criteria.
     *
     * @param array $criteria the list of key/value criteria pairs.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function delete_by(array $criteria) {
        global $DB;
        $DB->delete_records($this->favouritetable, $criteria);
    }

    /**
     * Return the total number of favourites in this repository.
     *
     * @return int the total number of items.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function count() : int {
        global $DB;
        return $DB->count_records($this->favouritetable);
    }

    /**
     * Return the number of user favourites matching the specified criteria.
     *
     * @param array $criteria the list of key/value criteria pairs.
     * @return int the number of favourites matching the criteria.
     * @throws \dml_exception if any database errors are encountered.
     */
    public function count_by(array $criteria) : int {
        global $DB;
        return $DB->count_records($this->favouritetable, $criteria);
    }
}
