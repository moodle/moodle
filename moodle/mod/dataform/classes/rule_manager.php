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
 * Rule manager class
 */
abstract class mod_dataform_rule_manager {

    /** @var int Dataform id */
    protected $_dataformid;
    /** @var array List of available rule plugins */
    protected $_ruletypes = null;
    /** @var array List of rules as type => array(blockid => block) */
    protected $_rules = null;
    /** @var array List of rule (block) ids by type */
    protected $_typerules = null;
    /** @var array List of rule (block) ids by view name */
    protected $_viewrules = null;
    /** @var array List of rule (block) ids by field name */
    protected $_fieldrules = null;

    // Construct.
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
    }

    // RULE MANAGEMENT.

    /**
     *
     *
     */
    public function set_rule_visibility($biid, $newvisibility) {
        $this->get_rules();
        if (!empty($this->_rules[$biid])) {
            $this->_rules[$biid]->set_visibility($newvisibility);
        }
    }

    /**
     *
     * @return bool
     */
    public function delete_rule($biid) {
        $this->get_rules();
        if (!empty($this->_rules[$biid])) {
            $rule = $this->_rules[$biid];
            $rule->delete();
            unset($this->_rules[$biid]);
            $this->get_rules();
        }
    }

    /**
     *
     * @return bool
     */
    public function delete_rules($type = null) {
        $this->get_rules();
        if (!empty($this->_typerules)) {
            foreach ($this->_typerules as $ruletype => $rules) {
                if ($type and $type != $ruletype) {
                    continue;;
                }
                foreach ($rules as $key => $biid) {
                    if (!empty($this->_rules[$biid])) {
                        $rule = $this->_rules[$biid];
                        $rule->delete();

                        unset($this->_rules[$biid]);
                    }
                    unset($this->_typerules[$ruletype][$key]);
                }
            }
        }
    }

    /**
     *
     */
    public function get_types() {
        if ($this->_ruletypes === null) {
            $cat = $this->get_category();
            $component = "dataform$cat";
            $this->_ruletypes = array();

            if ($blockplugins = \core\plugininfo\block::get_enabled_plugins()) {
                ksort($blockplugins);
                foreach ($blockplugins as $name) {
                    if (strpos($name, $component) !== 0) {
                        if ($this->_ruletypes) {
                            // Found all candidates.
                            break;
                        } else {
                            // Keep looking.
                            continue;
                        }
                    }
                    $this->_ruletypes[$name] = get_string('typename', 'block_'. $name);
                }
            }
        }

        return $this->_ruletypes;
    }

    /**
     * Returns a list of rules for component and item.
     *
     * @param string $component Component name.
     * @param string $item      Component insance id.
     * @param string $ruletype  Rule type.
     * @return array
     */
    public function get_type_rules($type) {
        $this->get_rules();
        if (!empty($this->_typerules[$type])) {
            $rules = array_intersect_key($this->_rules, $this->_typerules[$type]);
            return $rules;
        }
        return array();
    }

    /**
     * Returns a list of enabled rules for for specified type .
     *
     * @param string $type  Rule type; deafult all rules.
     * @return array
     */
    public function get_type_rules_enabled($type = null) {
        if (!$rules = $this->get_rules()) {
            return array();
        }

        if ($type) {
            if (empty($this->_typerules[$type])) {
                return array();
            } else {
                $rules = array_intersect_key($this->_rules, $this->_typerules[$type]);
            }
        }

        foreach ($rules as $key => $rule) {
            if (!$rule->is_enabled()) {
                unset($rules[$key]);
            }

        }

        return $rules;
    }

    /**
     *
     * @return array Rule blocks
     */
    public function get_rules() {
        global $DB;

        if ($this->_rules !== null) {
            return $this->_rules;
        }

        // Get the list of rule subplugins.
        if (!$ruletypes = $this->get_types()) {
            return null;
        }

        $cat = $this->get_category();
        $component = "dataform$cat";

        // Get the block instances.
        $context = mod_dataform_dataform::instance($this->_dataformid)->context;

        $this->_rules = array();
        $this->_typerules = array();
        $this->_viewrules = array();
        $this->_fieldrules = array();

        foreach ($ruletypes as $blockname => $unused) {
            $typename = str_replace($component, '', $blockname);
            $this->_typerules[$typename] = array();

            $params = array(
                'blockname' => $blockname,
                'parentcontextid' => $context->id,
                'pagetypepattern' => "mod-dataform-$cat-index",
            );
            if (!$instances = $DB->get_records('block_instances', $params)) {
                continue;
            }

            foreach ($instances as $instanceid => $instance) {
                $block = block_instance($blockname, $instance);
                $rule = new \mod_dataform\pluginbase\dataformrule($this->_dataformid, $cat, $block);
                $this->_rules[$instanceid] = $rule;
                $this->_typerules[$typename][$instanceid] = $instanceid;

                // View rules.
                if ($views = $rule->get_applicable_views()) {
                    foreach ($views as $viewname) {
                        if (empty($this->_viewrules[$viewname])) {
                            $this->_viewrules[$viewname] = array();
                        }
                        $this->_viewrules[$viewname][$instanceid] = $instanceid;
                    }
                }

                // Field rules.
                if ($fields = $rule->get_applicable_fields()) {
                    foreach ($fields as $fieldname) {
                        if (empty($this->_fieldrules[$fieldname])) {
                            $this->_fieldrules[$fieldname] = array();
                        }
                        $this->_fieldrules[$fieldname][$instanceid] = $instanceid;
                    }
                }
            }
        }

        return $this->_rules;
    }

    /**
     *
     * @return array Rule blocks
     */
    public function get_view_rules($viewname) {
        $this->get_rules();

        if (!empty($this->_viewrules[$viewname])) {
            $rules = array_intersect_key($this->_rules, $this->_viewrules[$viewname]);
            return $rules;
        }
        return array();
    }

    /**
     *
     * @return array Rule blocks
     */
    public function get_field_rules($fieldname) {
        $this->get_rules();

        if (!empty($this->_fieldrules[$fieldname])) {
            $rules = array_intersect_key($this->_rules, $this->_fieldrules[$fieldname]);
            return $rules;
        }
        return array();
    }

    /**
     * Returns the category of rules the manager refer to.
     * @return string
     */
    protected abstract function get_category();

}
