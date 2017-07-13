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
 * Step class.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

/**
 * Step class.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class step {

    /**
     * @var     int     $id         The id of the step.
     */
    protected $id;

    /**
     * @var     int     $tourid     The id of the tour that this step belongs to.
     */
    protected $tourid;

    /**
     * @var     tour    $tour       The tour class that this step belongs to.
     */
    protected $tour;

    /**
     * @var     string  $title      The title of the step.
     */
    protected $title;

    /**
     * @var     string  $content    The content of this step.
     */
    protected $content;

    /**
     * @var     int     $targettype The type of target.
     */
    protected $targettype;

    /**
     * @var     string  $targetvalue    The value for this type of target.
     */
    protected $targetvalue;

    /**
     * @var     int     $sortorder  The sort order.
     */
    protected $sortorder;

    /**
     * @var     object  $config     The configuration as an object.
     */
    protected $config;

    /**
     * @var     bool    $dirty      Whether the step has been changed since it was loaded
     */
    protected $dirty = false;

    /**
     * Fetch the step instance.
     *
     * @param   int             $id         The id of the step to be retrieved.
     * @return  step
     */
    public static function instance($id) {
        $step = new step();
        return $step->fetch($id);
    }

    /**
     * Load the step instance.
     *
     * @param   stdClass        $record     The step record to be loaded.
     * @param   boolean         $clean      Clean the values.
     * @return  step
     */
    public static function load_from_record($record, $clean = false) {
        $step = new self();
        return $step->reload_from_record($record, $clean);
    }

    /**
     * Fetch the step instance.
     *
     * @param   int             $id         The id of the step to be retrieved.
     * @return  step
     */
    protected function fetch($id) {
        global $DB;

        return $this->reload_from_record(
            $DB->get_record('tool_usertours_steps', array('id' => $id))
        );
    }

    /**
     * Refresh the current step from the datbase.
     *
     * @return  step
     */
    protected function reload() {
        return $this->fetch($this->id);
    }

    /**
     * Reload the current step from the supplied record.
     *
     * @param   stdClass        $record     The step record to be loaded.
     * @param   boolean         $clean      Clean the values.
     * @return  step
     */
    protected function reload_from_record($record, $clean = false) {
        $this->id           = $record->id;
        $this->tourid       = $record->tourid;
        if ($clean) {
            $this->title    = clean_param($record->title, PARAM_TEXT);
            $this->content  = clean_text($record->content);
        } else {
            $this->title    = $record->title;
            $this->content  = $record->content;
        }
        $this->targettype   = $record->targettype;
        $this->targetvalue  = $record->targetvalue;
        $this->sortorder    = $record->sortorder;
        $this->config       = json_decode($record->configdata);
        $this->dirty        = false;

        return $this;
    }

    /**
     * Get the ID of the step.
     *
     * @return  int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the Tour ID of the step.
     *
     * @return  int
     */
    public function get_tourid() {
        return $this->tourid;
    }

    /**
     * Get the Tour instance that this step belongs to.
     *
     * @return  tour
     */
    public function get_tour() {
        if ($this->tour === null) {
            $this->tour = tour::instance($this->tourid);
        }
        return $this->tour;
    }

    /**
     * Set the id of the tour.
     *
     * @param   int             $value      The id of the tour.
     * @return  self
     */
    public function set_tourid($value) {
        $this->tourid = $value;
        $this->tour = null;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the Title of the step.
     *
     * @return  string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Set the title for this step.
     *
     * @param   string      $value      The new title to use.
     * @return  $this
     */
    public function set_title($value) {
        $this->title = clean_text($value);
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the body content of the step.
     *
     * @return  string
     */
    public function get_content() {
        return $this->content;
    }

    /**
     * Set the content value for this step.
     *
     * @param   string      $value      The new content to use.
     * @return  $this
     */
    public function set_content($value) {
        $this->content = clean_text($value);
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the content value for this step.
     *
     * @return  string
     */
    public function get_targettype() {
        return $this->targettype;
    }

    /**
     * Set the type of target for this step.
     *
     * @param   string      $value      The new target to use.
     * @return  $this
     */
    public function set_targettype($value) {
        $this->targettype = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the target value for this step.
     *
     * @return  string
     */
    public function get_targetvalue() {
        return $this->targetvalue;
    }

    /**
     * Set the target value for this step.
     *
     * @param   string      $value      The new target value to use.
     * @return  $this
     */
    public function set_targetvalue($value) {
        $this->targetvalue = $value;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the target instance for this step.
     *
     * @return  target
     */
    public function get_target() {
        return target::get_target_instance($this);
    }

    /**
     * Get the current sortorder for this step.
     *
     * @return  int
     */
    public function get_sortorder() {
        return (int) $this->sortorder;
    }

    /**
     * Whether this step is the first step in the tour.
     *
     * @return  boolean
     */
    public function is_first_step() {
        return ($this->get_sortorder() === 0);
    }

    /**
     * Whether this step is the last step in the tour.
     *
     * @return  boolean
     */
    public function is_last_step() {
        $stepcount = $this->get_tour()->count_steps();
        return ($this->get_sortorder() === $stepcount - 1);
    }

    /**
     * Set the sortorder for this step.
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
     * Get the link to move this step up in the sortorder.
     *
     * @return  moodle_url
     */
    public function get_moveup_link() {
        return helper::get_move_step_link($this->get_id(), helper::MOVE_UP);
    }

    /**
     * Get the link to move this step down in the sortorder.
     *
     * @return  moodle_url
     */
    public function get_movedown_link() {
        return helper::get_move_step_link($this->get_id(), helper::MOVE_DOWN);
    }

    /**
     * Get the value of the specified configuration item.
     *
     * If notvalue was found, and no default was specified, the default for the tour will be used.
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

        if ($this->get_targettype() !== null) {
            $target = $this->get_target();
            if ($target->is_setting_forced($key)) {
                return $target->get_forced_setting_value($key);
            }
        }

        if (property_exists($this->config, $key)) {
            return $this->config->$key;
        }

        if ($default !== null) {
            return $default;
        }

        return $this->get_tour()->get_config($key);
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

        if ($value === null) {
            unset($this->config->$key);
        } else {
            $this->config->$key = $value;
        }
        $this->dirty = true;

        return $this;
    }

    /**
     * Get the edit link for this step.
     *
     * @return  moodle_url
     */
    public function get_edit_link() {
        return helper::get_edit_step_link($this->tourid, $this->id);
    }

    /**
     * Get the delete link for this step.
     *
     * @return  moodle_url
     */
    public function get_delete_link() {
        return helper::get_delete_step_link($this->id);
    }

    /**
     * Prepare this step for saving to the database.
     *
     * @return  object
     */
    public function to_record() {
        return (object) array(
            'id'            => $this->id,
            'tourid'        => $this->tourid,
            'title'         => $this->title,
            'content'       => $this->content,
            'targettype'    => $this->targettype,
            'targetvalue'   => $this->targetvalue,
            'sortorder'     => $this->sortorder,
            'configdata'    => json_encode($this->config),
        );
    }

    /**
     * Calculate the next sort-order value.
     *
     * @return  int
     */
    protected function calculate_sortorder() {
        $count = $this->get_tour()->count_steps();
        $this->sortorder = $count;

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
            $DB->update_record('tool_usertours_steps', $record);
        } else {
            $this->calculate_sortorder();
            $record = $this->to_record();
            unset($record->id);
            $this->id = $DB->insert_record('tool_usertours_steps', $record);
        }

        $this->get_tour()->reset_step_sortorder();

        $this->reload();

        // Notify of a change to the step configuration.
        // This must be done separately to tour change notifications.
        cache::notify_step_change($this->get_tourid());

        // Notify the cache that a tour has changed.
        // Tours are only stored in the cache if there are steps.
        // If there step count has changed for some reason, this will change the potential cache results.
        cache::notify_tour_change();

        return $this;
    }

    /**
     * Remove this step.
     */
    public function remove() {
        global $DB;

        if ($this->id === null) {
            return;
        }

        $DB->delete_records('tool_usertours_steps', array('id' => $this->id));
        $this->get_tour()->reset_step_sortorder();

        // Notify of a change to the step configuration.
        // This must be done separately to tour change notifications.
        cache::notify_step_change($this->get_id());

        // Notify the cache that a tour has changed.
        // Tours are only stored in the cache if there are steps.
        // If there step count has changed for some reason, this will change the potential cache results.
        cache::notify_tour_change();
    }

    /**
     * Get the list of possible placement options.
     *
     * @return  array
     */
    public function get_placement_options() {
        return configuration::get_placement_options(true);
    }

    /**
     * The list of possible configuration keys.
     *
     * @return  array
     */
    public static function get_config_keys() {
        return [
            'placement',
            'orphan',
            'backdrop',
            'reflex',
        ];
    }

    /**
     * Add the step configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     * @return  $this
     */
    public function add_config_to_form(\MoodleQuickForm $mform) {
        $tour = $this->get_tour();

        $options = configuration::get_placement_options($tour->get_config('placement'));
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
    public function add_config_field_to_form(\MoodleQuickForm $mform, $key) {
        $tour = $this->get_tour();

        $default = (bool) $tour->get_config($key);

        $options = [
            true    => get_string('yes'),
            false   => get_string('no'),
        ];

        if (!isset($options[$default])) {
            $default = configuration::get_default_value($key);
        }

        $options = array_reverse($options, true);
        $options[configuration::TOURDEFAULT] = get_string('defaultvalue', 'tool_usertours', $options[$default]);
        $options = array_reverse($options, true);

        $mform->addElement('select', $key, get_string($key, 'tool_usertours'), $options);
        $mform->setDefault($key, configuration::TOURDEFAULT);
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
        foreach (self::get_config_keys() as $key) {
            $data->$key = $this->get_config($key, configuration::get_step_default_value($key));
        }

        if ($this->get_targettype() !== null) {
            $this->get_target()->prepare_data_for_form($data);
        }

        return $data;
    }

    /**
     * Handle submission of the step editing form.
     *
     * @param   local\forms\editstep  $mform      The sumitted form.
     * @param   stdClass        $data       The submitted data.
     * @return  $this
     */
    public function handle_form_submission(local\forms\editstep &$mform, \stdClass $data) {
        $this->set_title($data->title);
        $this->set_content($data->content);
        $this->set_targettype($data->targettype);

        $this->set_targetvalue($this->get_target()->get_value_from_form($data));

        foreach (self::get_config_keys() as $key) {
            if (!$this->get_target()->is_setting_forced($key)) {
                if (isset($data->$key)) {
                    $value = $data->$key;
                } else {
                    $value = configuration::TOURDEFAULT;
                }
                if ($value === configuration::TOURDEFAULT) {
                    $this->set_config($key, null);
                } else {
                    $this->set_config($key, $value);
                }
            }
        }

        $this->persist();

        return $this;
    }

    /**
     * Attempt to fetch any matching langstring if the string is in the
     * format identifier,component.
     *
     * @param   string  $string
     * @return  string
     */
    public static function get_string_from_input($string) {
        $string = trim($string);

        if (preg_match('|^([a-zA-Z][a-zA-Z0-9\.:/_-]*),([a-zA-Z][a-zA-Z0-9\.:/_-]*)$|', $string, $matches)) {
            if ($matches[2] === 'moodle') {
                $matches[2] = 'core';
            }

            if (get_string_manager()->string_exists($matches[1], $matches[2])) {
                $string = get_string($matches[1], $matches[2]);
            }
        }

        return $string;
    }
}
