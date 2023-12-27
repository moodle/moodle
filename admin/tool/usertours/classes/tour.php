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
 * Tour class.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

use tool_usertours\local\clientside_filter\clientside_filter;

defined('MOODLE_INTERNAL') || die();

/**
 * Tour class.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tour {

    /**
     * The tour is currently disabled
     *
     * @var DISABLED
     */
    const DISABLED = 0;

    /**
     * The tour is currently disabled
     *
     * @var DISABLED
     */
    const ENABLED = 1;

    /**
     * The user preference value to indicate the time of completion of the tour for a user.
     *
     * @var TOUR_LAST_COMPLETED_BY_USER
     */
    const TOUR_LAST_COMPLETED_BY_USER   = 'tool_usertours_tour_completion_time_';

    /**
     * The user preference value to indicate the time that a user last requested to see the tour.
     *
     * @var TOUR_REQUESTED_BY_USER
     */
    const TOUR_REQUESTED_BY_USER        = 'tool_usertours_tour_reset_time_';

    /**
     * @var $id The tour ID.
     */
    protected $id;

    /**
     * @var $name The tour name.
     */
    protected $name;

    /**
     * @var $description The tour description.
     */
    protected $description;

    /**
     * @var $pathmatch The tour pathmatch.
     */
    protected $pathmatch;

    /**
     * @var $enabled The tour enabled state.
     */
    protected $enabled;

    /**
     * @var $endtourlabel The end tour label.
     */
    protected $endtourlabel;

    /**
     * @var $sortorder The sort order.
     */
    protected $sortorder;

    /**
     * @var $dirty Whether the current view of the tour has been modified.
     */
    protected $dirty = false;

    /**
     * @var $config The configuration object for the tour.
     */
    protected $config;

    /**
     * @var $filtervalues The filter configuration object for the tour.
     */
    protected $filtervalues;

    /**
     * @var $steps  The steps in this tour.
     */
    protected $steps = [];

    /**
     * @var bool $displaystepnumbers Display the step numbers in this tour.
     */
    protected $displaystepnumbers = true;

    /**
     * Create an instance of the specified tour.
     *
     * @param   int         $id         The ID of the tour to load.
     * @return  tour
     */
    public static function instance($id) {
        $tour = new self();
        return $tour->fetch($id);
    }

    /**
     * Create an instance of tour from its provided DB record.
     *
     * @param   stdClass    $record     The record of the tour to load.
     * @param   boolean     $clean      Clean the values.
     * @return  tour
     */
    public static function load_from_record($record, $clean = false) {
        $tour = new self();
        return $tour->reload_from_record($record, $clean);
    }

    /**
     * Fetch the specified tour into the current object.
     *
     * @param   int         $id         The ID of the tour to fetch.
     * @return  tour
     */
    protected function fetch($id) {
        global $DB;

        return $this->reload_from_record(
            $DB->get_record('tool_usertours_tours', array('id' => $id), '*', MUST_EXIST)
        );
    }

    /**
     * Reload the current tour from database.
     *
     * @return  tour
     */
    protected function reload() {
        return $this->fetch($this->id);
    }

    /**
     * Reload the tour into the current object.
     *
     * @param   stdClass    $record     The record to reload.
     * @param   boolean     $clean      Clean the values.
     * @return  tour
     */
    protected function reload_from_record($record, $clean = false) {
        $this->id           = $record->id;
        if (!property_exists($record, 'description')) {
            if (property_exists($record, 'comment')) {
                $record->description = $record->comment;
                unset($record->comment);
            }
        }
        if ($clean) {
            $this->name         = clean_param($record->name, PARAM_TEXT);
            $this->description  = clean_text($record->description);
        } else {
            $this->name         = $record->name;
            $this->description  = $record->description;
        }
        $this->pathmatch    = $record->pathmatch;
        $this->enabled      = $record->enabled;
        if (isset($record->sortorder)) {
            $this->sortorder = $record->sortorder;
        }
        $this->endtourlabel = $record->endtourlabel ?? null;
        $this->config       = json_decode($record->configdata);
        $this->dirty        = false;
        $this->steps        = [];
        $this->displaystepnumbers = !empty($record->displaystepnumbers);

        return $this;
    }

    /**
     * Fetch all steps in the tour.
     *
     * @return  step[]
     */
    public function get_steps() {
        if (empty($this->steps)) {
            $this->steps = helper::get_steps($this->id);
        }

        return $this->steps;
    }

    /**
     * Count the number of steps in the tour.
     *
     * @return  int
     */
    public function count_steps() {
        return count($this->get_steps());
    }

    /**
     * The ID of the tour.
     *
     * @return  int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * The name of the tour.
     *
     * @return  string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the tour to the specified value.
     *
     * @param   string      $value      The new name.
     * @return  $this
     */
    public function set_name($value) {
        $this->name = clean_param($value, PARAM_TEXT);
        $this->dirty = true;

        return $this;
    }

    /**
     * The description associated with the tour.
     *
     * @return  string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description of the tour to the specified value.
     *
     * @param   string      $value      The new description.
     * @return  $this
     */
    public function set_description($value) {
        $this->description = clean_text($value);
        $this->dirty = true;

        return $this;
    }

    /**
     * The path match for the tour.
     *
     * @return  string
     */
    public function get_pathmatch() {
        return $this->pathmatch;
    }

    /**
     * Set the patchmatch of the tour to the specified value.
     *
     * @param   string      $value      The new patchmatch.
     * @return  $this
     */
    public function set_pathmatch($value) {
        $this->pathmatch = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * The enabled state of the tour.
     *
     * @return  int
     */
    public function get_enabled() {
        return $this->enabled;
    }

    /**
     * Whether the tour is currently enabled.
     *
     * @return  boolean
     */
    public function is_enabled() {
        return ($this->enabled == self::ENABLED);
    }

    /**
     * Set the enabled state of the tour to the specified value.
     *
     * @param   boolean     $value      The new state.
     * @return  $this
     */
    public function set_enabled($value) {
        $this->enabled = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * The end tour label for the tour.
     *
     * @return string
     */
    public function get_endtourlabel(): string {
        if ($this->endtourlabel) {
            $label = helper::get_string_from_input($this->endtourlabel);
        } else if ($this->count_steps() == 1) {
            $label = get_string('endonesteptour', 'tool_usertours');
        } else {
            $label = get_string('endtour', 'tool_usertours');
        }

        return $label;
    }

    /**
     * Set the endtourlabel of the tour to the specified value.
     *
     * @param string $value
     * @return $this
     */
    public function set_endtourlabel(string $value): tour {
        $this->endtourlabel = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * The link to view this tour.
     *
     * @return  \moodle_url
     */
    public function get_view_link() {
        return helper::get_view_tour_link($this->id);
    }

    /**
     * The link to edit this tour.
     *
     * @return  \moodle_url
     */
    public function get_edit_link() {
        return helper::get_edit_tour_link($this->id);
    }

    /**
     * The link to reset the state of this tour for all users.
     *
     * @return  moodle_url
     */
    public function get_reset_link() {
        return helper::get_reset_tour_for_all_link($this->id);
    }

    /**
     * The link to export this tour.
     *
     * @return  moodle_url
     */
    public function get_export_link() {
        return helper::get_export_tour_link($this->id);
    }

    /**
     * The link to duplicate this tour.
     *
     * @return  moodle_url
     */
    public function get_duplicate_link() {
        return helper::get_duplicate_tour_link($this->id);
    }

    /**
     * The link to remove this tour.
     *
     * @return  moodle_url
     */
    public function get_delete_link() {
        return helper::get_delete_tour_link($this->id);
    }

    /**
     * Prepare this tour for saving to the database.
     *
     * @return  object
     */
    public function to_record() {
        return (object) array(
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'pathmatch'     => $this->pathmatch,
            'enabled'       => $this->enabled,
            'sortorder'     => $this->sortorder,
            'endtourlabel'  => $this->endtourlabel,
            'configdata'    => json_encode($this->config),
            'displaystepnumbers' => $this->displaystepnumbers,
        );
    }

    /**
     * Get the current sortorder for this tour.
     *
     * @return  int
     */
    public function get_sortorder() {
        return (int) $this->sortorder;
    }

    /**
     * Whether this tour is the first tour.
     *
     * @return  boolean
     */
    public function is_first_tour() {
        return ($this->get_sortorder() === 0);
    }

    /**
     * Whether this tour is the last tour.
     *
     * @param   int         $tourcount  The pre-fetched count of tours
     * @return  boolean
     */
    public function is_last_tour($tourcount = null) {
        if ($tourcount === null) {
            $tourcount = helper::count_tours();
        }
        return ($this->get_sortorder() === ($tourcount - 1));
    }

    /**
     * Set the sortorder for this tour.
     *
     * @param   int         $value      The new sortorder to use.
     * @return  $this
     */
    public function set_sortorder($value) {
        $this->sortorder = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * Calculate the next sort-order value.
     *
     * @return  int
     */
    protected function calculate_sortorder() {
        $this->sortorder = helper::count_tours();

        return $this;
    }

    /**
     * Get the link to move this tour up in the sortorder.
     *
     * @return  moodle_url
     */
    public function get_moveup_link() {
        return helper::get_move_tour_link($this->get_id(), helper::MOVE_UP);
    }

    /**
     * Get the link to move this tour down in the sortorder.
     *
     * @return  moodle_url
     */
    public function get_movedown_link() {
        return helper::get_move_tour_link($this->get_id(), helper::MOVE_DOWN);
    }

    /**
     * Get the value of the specified configuration item.
     *
     * @param   string      $key        The configuration key to set.
     * @param   mixed       $default    The default value to use if a value was not found.
     * @return  mixed
     */
    public function get_config($key = null, $default = null) {
        if ($this->config === null) {
            $this->config = (object) array();
        }
        if ($key === null) {
            return $this->config;
        }

        if (property_exists($this->config, $key)) {
            return $this->config->$key;
        }

        if ($default !== null) {
            return $default;
        }

        return configuration::get_default_value($key);
    }

    /**
     * Set the configuration item as specified.
     *
     * @param   string      $key        The configuration key to set.
     * @param   mixed       $value      The new value for the configuration item.
     * @return  $this
     */
    public function set_config($key, $value) {
        if ($this->config === null) {
            $this->config = (object) array();
        }
        $this->config->$key = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * Save the tour and it's configuration to the database.
     *
     * @param   boolean     $force      Whether to force writing to the database.
     * @return  $this
     */
    public function persist($force = false) {
        global $DB;

        if (!$this->dirty && !$force) {
            return $this;
        }

        if ($this->id) {
            $record = $this->to_record();
            $DB->update_record('tool_usertours_tours', $record);
        } else {
            $this->calculate_sortorder();
            $record = $this->to_record();
            unset($record->id);
            $this->id = $DB->insert_record('tool_usertours_tours', $record);
        }

        $this->reload();

        // Notify the cache that a tour has changed.
        cache::notify_tour_change();

        return $this;
    }

    /**
     * Remove this step.
     */
    public function remove() {
        global $DB;

        if ($this->id === null) {
            // Nothing to delete - this tour has not been persisted.
            return null;
        }

        // Delete all steps associated with this tour.
        // Note, although they are currently just DB records, there may be other components in the future.
        foreach ($this->get_steps() as $step) {
            $step->remove();
        }

        // Remove the configuration for the tour.
        $DB->delete_records('tool_usertours_tours', array('id' => $this->id));
        helper::reset_tour_sortorder();

        $this->remove_user_preferences();

        return null;
    }

    /**
     * Reset the sortorder for all steps in the tour.
     *
     * @return  $this
     */
    public function reset_step_sortorder() {
        global $DB;
        $steps = $DB->get_records('tool_usertours_steps', array('tourid' => $this->id), 'sortorder ASC', 'id');

        $index = 0;
        foreach ($steps as $step) {
            $DB->set_field('tool_usertours_steps', 'sortorder', $index, array('id' => $step->id));
            $index++;
        }

        // Notify of a change to the step configuration.
        // Note: Do not notify of a tour change here. This is only a step change for a tour.
        cache::notify_step_change($this->get_id());

        return $this;
    }

    /**
     * Remove stored user preferences for the tour
     */
    protected function remove_user_preferences(): void {
        global $DB;

        $DB->delete_records('user_preferences', ['name' => self::TOUR_LAST_COMPLETED_BY_USER . $this->get_id()]);
        $DB->delete_records('user_preferences', ['name' => self::TOUR_REQUESTED_BY_USER . $this->get_id()]);
    }

    /**
     * Whether this tour should be displayed to the user.
     *
     * @return  boolean
     */
    public function should_show_for_user() {
        if (!$this->is_enabled()) {
            // The tour is disabled - it should not be shown.
            return false;
        }

        if ($tourcompletiondate = get_user_preferences(self::TOUR_LAST_COMPLETED_BY_USER . $this->get_id(), null)) {
            if ($tourresetdate = get_user_preferences(self::TOUR_REQUESTED_BY_USER . $this->get_id(), null)) {
                if ($tourresetdate >= $tourcompletiondate) {
                    return true;
                }
            }
            $lastmajorupdate = $this->get_config('majorupdatetime', time());
            if ($tourcompletiondate > $lastmajorupdate) {
                // The user has completed the tour since the last major update.
                return false;
            }
        }

        return true;
    }

    /**
     * Get the key for this tour.
     * This is used in the session cookie to determine whether the user has seen this tour before.
     */
    public function get_tour_key() {
        global $USER;

        $tourtime = $this->get_config('majorupdatetime', null);

        if ($tourtime === null) {
            // This tour has no majorupdate time.
            // Set one now to prevent repeated displays to the user.
            $this->set_config('majorupdatetime', time());
            $this->persist();
            $tourtime = $this->get_config('majorupdatetime', null);
        }

        if ($userresetdate = get_user_preferences(self::TOUR_REQUESTED_BY_USER . $this->get_id(), null)) {
            $tourtime = max($tourtime, $userresetdate);
        }

        return sprintf('tool_usertours_%d_%d_%s', $USER->id, $this->get_id(), $tourtime);
    }

    /**
     * Reset the requested by user date.
     *
     * @return  $this
     */
    public function request_user_reset() {
        set_user_preference(self::TOUR_REQUESTED_BY_USER . $this->get_id(), time());

        return $this;
    }

    /**
     * Mark this tour as completed for this user.
     *
     * @return  $this
     */
    public function mark_user_completed() {
        set_user_preference(self::TOUR_LAST_COMPLETED_BY_USER . $this->get_id(), time());

        return $this;
    }

    /**
     * Update a tour giving it a new major update time.
     * This will ensure that it is displayed to all users, even those who have already seen it.
     *
     * @return  $this
     */
    public function mark_major_change() {
        // Clear old reset and completion notes.
        $this->remove_user_preferences();

        $this->set_config('majorupdatetime', time());
        $this->persist();

        return $this;
    }

    /**
     * Add the step configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     * @return  $this
     */
    public function add_config_to_form(\MoodleQuickForm &$mform) {
        $options = configuration::get_placement_options();
        $mform->addElement('select', 'placement', get_string('placement', 'tool_usertours'), $options);
        $mform->addHelpButton('placement', 'placement', 'tool_usertours');

        $this->add_config_field_to_form($mform, 'orphan');
        $this->add_config_field_to_form($mform, 'backdrop');
        $this->add_config_field_to_form($mform, 'reflex');

        return $this;
    }

    /**
     * Add the specified step field configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     * @param   string          $key        The key to add.
     * @return  $this
     */
    protected function add_config_field_to_form(\MoodleQuickForm &$mform, $key) {
        $options = [
            true    => get_string('yes'),
            false   => get_string('no'),
        ];
        $mform->addElement('select', $key, get_string($key, 'tool_usertours'), $options);
        $mform->setDefault($key, configuration::get_default_value($key));
        $mform->addHelpButton($key, $key, 'tool_usertours');

        return $this;
    }

    /**
     * Prepare the configuration data for the moodle form.
     *
     * @return  object
     */
    public function prepare_data_for_form() {
        $data = $this->to_record();
        foreach (configuration::get_defaultable_keys() as $key) {
            $data->$key = $this->get_config($key, configuration::get_default_value($key));
        }

        return $data;
    }

    /**
     * Get the configured filter values.
     *
     * @param   string      $filter     The filter to retrieve values for.
     * @return  array
     */
    public function get_filter_values($filter) {
        if ($allvalues = (array) $this->get_config('filtervalues')) {
            if (isset($allvalues[$filter])) {
                return $allvalues[$filter];
            }
        }

        return [];
    }

    /**
     * Set the values for the specified filter.
     *
     * @param   string      $filter     The filter to set.
     * @param   array       $values     The values to set.
     * @return  $this
     */
    public function set_filter_values($filter, array $values = []) {
        $allvalues = (array) $this->get_config('filtervalues', []);
        $allvalues[$filter] = $values;

        return $this->set_config('filtervalues', $allvalues);
    }

    /**
     * Check whether this tour matches all filters.
     *
     * @param   \context     $context    The context to check.
     * @param   array|null   $filters    Optional array of filters.
     * @return  bool
     */
    public function matches_all_filters(\context $context, array $filters = null): bool {
        if (!$filters) {
            $filters = helper::get_all_filters();
        }

        // All filters must match.
        // If any one filter fails to match, we return false.
        foreach ($filters as $filterclass) {
            if (!$filterclass::filter_matches($this, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets all filter values for use in client side filters.
     *
     * @param   array     $filters    Array of clientside filters.
     * @return  array
     */
    public function get_client_filter_values(array $filters): array {
        $results = [];

        foreach ($filters as $filter) {
            $results[$filter::get_filter_name()] = $filter::get_client_side_values($this);
        }

        return $results;
    }

    /**
     * Set the value for the display step numbers setting.
     *
     * @param bool $value True for enable.
     * @return $this
     */
    public function set_display_step_numbers(bool $value): tour {
        $this->displaystepnumbers = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the value of the display step numbers setting.
     *
     * @return bool
     */
    public function get_display_step_numbers(): bool {
        return $this->displaystepnumbers;
    }
}
