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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Pattern manager class
 */
class mod_dataform_pattern_manager {

    /** @var int The id of the Dataform this manager works for */
    protected $_dataformid;

    /**
     * Returns and caches (for the current script) if not already, a patterns manager for the specified Dataform.
     *
     * @param int Dataform id
     * @return mod_dataform_pattern_manager
     */
    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'pattern_manager')) {
            $instance = new mod_dataform_pattern_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'pattern_manager', $instance);
        }

        return $instance;
    }

    /**
     * Constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return null;
    }


    /**
     * Returns a list view patterns for the specified view (by id) or all views.
     *
     * @param int View id
     * @return array
     */
    public function get_view_patterns($viewid = 0) {
        $patterns = array();
        $views = array();

        if ($viewid) {
            if ($view = $this->view_manager->get_view_by_id($viewid)) {
                $views = array($view);
            }
        } else {
            $views = $this->view_manager->views;
        }

        if ($views) {
            foreach ($views as $view) {
                if ($viewpatterns = $view->renderer->get_list(true)) {
                    $patterns = array_merge($patterns, $viewpatterns);
                }
            }
        }
        return $patterns;
    }

    /**
     * Returns a list field patterns for the specified field (by id) or all fields.
     *
     * @param int Field id
     * @return array
     */
    public function get_field_patterns($fieldid = 0) {
        $patterns = array();
        $fields = array();

        if ($fieldid) {
            if ($field = $this->field_manager->get_field_by_id($fieldid)) {
                $fields = array($field);
            }
        } else {
            $fields = $this->field_manager->fields;
        }

        if ($fields) {
            foreach ($fields as $field) {
                if ($fieldpatterns = $field->renderer->get_list(true)) {
                    $patterns = array_merge($patterns, $fieldpatterns);
                }
            }
        }
        return $patterns;
    }

    /**
     * Generates field patterns menu for all or a specific field (by id).
     * Used in view template forms for adding field patterns to template.
     *
     * @param int Field id
     * @return array Associative array of associative arrays
     */
    public function get_field_patterns_menu($fieldid = 0) {
        $patterns = array();
        $fields = array();

        if ($fieldid) {
            if ($field = $this->field_manager->get_field_by_id($fieldid)) {
                $fields = array($field);
            }
        } else {
            $fields = $this->field_manager->fields;
        }

        if ($fields) {
            foreach ($fields as $field) {
                if ($fieldpatterns = $field->renderer->get_menu()) {
                    $patterns = array_merge_recursive($patterns, $fieldpatterns);
                }
            }
        }
        return $patterns;
    }


    /**
     * Returns the view manager of the Dataform this mannager works for.
     *
     * @return mod_dataform_view_manager
     */
    public function get_view_manager() {
        return mod_dataform_view_manager::instance($this->_dataformid);
    }

    /**
     * Returns the field manager of the Dataform this mannager works for.
     *
     * @return mod_dataform_field_manager
     */
    public function get_field_manager() {
        return mod_dataform_field_manager::instance($this->_dataformid);
    }
}
