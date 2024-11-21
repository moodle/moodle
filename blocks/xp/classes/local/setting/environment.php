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
 * Settings environment.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\setting;

use admin_settingpage;
use block_base;
use part_of_admin_tree;

/**
 * Settings environment.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class environment {

    /** @var part_of_admin_tree The root. */
    protected $adminroot;
    /** @var string The parent node name. */
    protected $parentnodename;
    /** @var bool Whether has site config. */
    protected $hassiteconfig;
    /** @var \core\plugininfo\block Plugin info. */
    protected $plugininfo;
    /** @var block_base The block instance. */
    protected $blockinstance;
    /** @var admin_settingpage The page to add settings to. */
    protected $settingspage;

    /**
     * Constructor.
     *
     * @param part_of_admin_tree $adminroot The root.
     * @param string $parentnodename The parent node name.
     * @param bool $hassiteconfig Whether has site config.
     * @param \core\plugininfo\block $plugininfo Plugin info.
     * @param block_base $blockinstance The block instance.
     * @param admin_settingpage $settingspage The page to add settings to.
     */
    public function __construct(
            part_of_admin_tree $adminroot,
            $parentnodename,
            $hassiteconfig,
            \core\plugininfo\block $plugininfo,
            block_base $blockinstance,
            admin_settingpage $settingspage) {

        $this->adminroot = $adminroot;
        $this->parentnodename = $parentnodename;
        $this->hassiteconfig = $hassiteconfig;
        $this->plugininfo = $plugininfo;
        $this->blockinstance = $blockinstance;
        $this->settingspage = $settingspage;
    }

    /**
     * Get admin root.
     *
     * @return part_of_admin_tree
     */
    public function get_admin() {
        return $this->adminroot;
    }

    /**
     * Get block instance.
     *
     * @return block_base
     */
    public function get_block_instance() {
        return $this->blockinstance;
    }

    /**
     * Plugin info.
     *
     * @return \core\plugininfo\block
     */
    public function get_plugininfo() {
        return $this->plugininfo;
    }

    /**
     * Parent node name.
     *
     * @return string
     */
    public function get_parent_node_name() {
        return $this->parentnodename;
    }

    /**
     * Get the settings page.
     *
     * @return admin_settingpage
     */
    public function get_settings_page() {
        return $this->settingspage;
    }

    /**
     * Has site config?
     *
     * @return bool
     */
    public function has_site_config() {
        return $this->hassiteconfig;
    }

    /**
     * Is full tree?
     *
     * @return bool
     */
    public function is_full_tree() {
        return $this->adminroot->fulltree;
    }

}
