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

namespace mod_dataform\pluginbase;

require_once("$CFG->libdir/blocklib.php");

/**
 * Base class for Dataform Access Types
 */
class dataformrule {
    /* @var mix The context block. */
    protected $_block = null;
    protected $_dataformid;

    /**
     *
     */
    public function __construct($dataformid, $category, $block = null) {
        $this->_dataformid = $dataformid;
        $this->_category = $category;
        $this->_block = $block;
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
     *
     * @return object dataform
     */
    public function get_dataformid() {
        return $this->_dataformid;
    }

    /**
     *
     */
    public function get_data() {
        if (empty($this->_block)) {
            return null;
        }

        if (empty($this->_block->config)) {
            $this->_block->config = new \stdClass;
        }

        if (empty($this->_block->config->name)) {
            $this->_block->config->name = get_string('rulenew', 'dataform', $this->get_typename());;
        }
        if (empty($this->_block->config->description)) {
            $this->_block->config->description = null;
        }
        if (empty($this->_block->config->timefrom)) {
            $this->_block->config->timefrom = 0;
        }
        if (empty($this->_block->config->timeto)) {
            $this->_block->config->timeto = 0;
        }
        return $this->_block->config;
    }

    /**
     *
     * @return null|string
     */
    public function get_name() {
        if (!empty($this->_block->config->name)) {
            return $this->_block->config->name;
        }
        return null;
    }

    /**
     * Returns the rule category.
     * @return string
     */
    public function get_category() {
        return $this->_category;
    }

    /**
     * Returns the rule type from blockname - dataform - category.
     * @return string
     */
    public function get_type() {
        if ($blockname = $this->get_blockname()) {
            $component = 'dataform'. $this->_category;
            return str_replace($component, '', $blockname);
        }
        return null;
    }

    /**
     * Returns the type name of the rule
     */
    public function get_typename() {
        return get_string('typename', 'block_'. $this->get_blockname());
    }

    /**
     * Returns the block type
     */
    public function get_blockname() {
        if ($this->_block) {
            return $this->_block->instance->blockname;
        }
        return null;
    }

    /**
     * Returns the rule block
     */
    public function get_block() {
        return $this->_block;
    }

    /**
     * Returns the rule context as the rule's block context.
     *
     * @return context_block
     */
    public function get_context() {
        if (empty($this->_block)) {
            return null;
        }

        return $this->_block->context;
    }

    /**
     * Deletes the rule block.
     */
    public function delete() {
        blocks_delete_instance($this->_block->instance);
    }


    /**
     * Checks if the rule is enabled.
     *
     * @return bool
     */
    public function is_enabled() {
        return !empty($this->_block->config->enabled);
    }

    /**
     * Sets the rule's visibility (enabled/disabled).
     *
     * @param int 0 = disabled, 1 = enabled
     */
    public function set_visibility($visibility) {
        global $DB;

        $update = false;
        if ($config = $this->get_data()) {
            if (empty($config->enabled) and $visibility) {
                $config->enabled = $visibility;
                $update = true;
            } else if (!empty($config->enabled) and !$visibility) {
                unset($config->enabled);
                $update = true;
            }

            if ($update) {
                $configdata = base64_encode(serialize($config));
                $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $this->_block->instance->id));
                $this->_block->config->enabled = $visibility;
            }
        }
    }

    /**
     * Checks if the rule is applicable to the given data.
     *
     * @param array $data
     * @return bool
     */
    public function is_applicable(array $data) {
        $config = $this->_block->config;
        $now = time();

        // Timing.
        if (!empty($config->timefrom) and $now < $config->timefrom) {
            return false;
        }

        if (!empty($config->timeto) and $now > $config->timeto) {
            return false;
        }

        // Views.
        if (!empty($data['viewid']) and !empty($config->views)) {
            $vm = \mod_dataform_view_manager::instance($this->_dataformid);
            if (!$view = $vm->get_view_by_id($data['viewid'])) {
                return false;
            }

            if (!in_array($view->name, $config->views)) {
                return false;
            }
        }

        if (!$this->_block->is_applicable($data)) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function has_capability($capability) {
        return has_capability($capability, $this->_block->context);
    }

    /**
     *
     */
    public function require_capability($capability) {
        require_capability($capability, $this->_block->context);
    }

    /**
     * Returns the list of views the rule applies to.
     *
     * @return array|null
     */
    public function get_applicable_views() {
        if (!$this->is_enabled()) {
            return null;
        }

        if (!empty($this->_block->config->views)) {
            return $this->_block->config->views;
        }
        // Return all views.
        return \mod_dataform_view_manager::instance($this->_dataformid)->views_menu;
    }

    /**
     * Returns the list of fields the rule applies to.
     *
     * @return string
     */
    public function get_applicable_fields() {
        if (!$this->is_enabled()) {
            return null;
        }

        if (!empty($this->_block->config->fields)) {
            return $this->_block->config->fields;
        }
        return null;
    }

}
