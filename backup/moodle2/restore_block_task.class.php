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
 * Defines restore_block_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * abstract block task that provides all the properties and common steps to be performed
 * when one block is being restored
 *
 * TODO: Finish phpdocs
 */
abstract class restore_block_task extends restore_task {

    protected $taskbasepath; // To store the basepath of this block
    protected $blockname;    // Name of the block
    protected $contextid;   // new (target) context of the block
    protected $oldcontextid;// old (original) context of the block
    protected $blockid;     // new (target) id of the block
    protected $oldblockid;  // old (original) id of the block

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $taskbasepath, $plan = null) {
        $this->taskbasepath = $taskbasepath;
        $this->blockname = '';
        $this->contextid = 0;
        $this->oldcontextid = 0;
        $this->blockid = 0;
        $this->oldblockid = 0;
        parent::__construct($name, $plan);
    }

    /**
     * Block tasks have their own directory to write files
     */
    public function get_taskbasepath() {
        return $this->taskbasepath;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // If we have decided not to backup blocks, prevent anything to be built
        if (!$this->get_setting_value('blocks')) {
            $this->built = true;
            return;
        }

        // If "child" of activity task and it has been excluded, nothing to do
        $parent = basename(dirname(dirname($this->taskbasepath)));
        if ($parent != 'course') {
            $includedsetting = $parent . '_included';
            if (!$this->get_setting_value($includedsetting)) {
                $this->built = true;
                return;
            }
        }

        // Process the block.xml common file (instance + positions)
        $this->add_step(new restore_block_instance_structure_step('block_commons', 'block.xml'));

        // Here we add all the common steps for any block and, in the point of interest
        // we call to define_my_steps() in order to get the particular ones inserted in place.
        $this->define_my_steps();

        // Restore block role assignments and overrides (internally will observe the role_assignments setting)
        $this->add_step(new restore_ras_and_caps_structure_step('block_ras_and_caps', 'roles.xml'));

        // Restore block comments (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new restore_comments_structure_step('block_comments', 'comments.xml'));
        }

        // Search reindexing (if enabled).
        if (\core_search\manager::is_indexing_enabled()) {
            $wholecourse = $this->get_target() == backup::TARGET_NEW_COURSE;
            $wholecourse = $wholecourse || $this->setting_exists('overwrite_conf') && $this->get_setting_value('overwrite_conf');
            if (!$wholecourse) {
                $this->add_step(new restore_block_search_index('block_search_index'));
            }
        }

        // At the end, mark it as built
        $this->built = true;
    }

    public function set_blockname($blockname) {
        $this->blockname = $blockname;
    }

    public function get_blockname() {
        return $this->blockname;
    }

    public function set_blockid($blockid) {
        $this->blockid = $blockid;
    }

    public function get_blockid() {
        return $this->blockid;
    }

    public function set_old_blockid($blockid) {
        $this->oldblockid = $blockid;
    }

    public function get_old_blockid() {
        return $this->oldblockid;
    }

    public function set_contextid($contextid) {
        $this->contextid = $contextid;
    }

    public function get_contextid() {
        return $this->contextid;
    }

    public function set_old_contextid($contextid) {
        $this->oldcontextid = $contextid;
    }

    public function get_old_contextid() {
        return $this->oldcontextid;
    }

    /**
     * Define one array() of fileareas that each block controls
     */
    abstract public function get_fileareas();

    /**
     * Define one array() of configdata attributes
     * that need to be decoded
     */
    abstract public function get_configdata_encoded_attributes();

    /**
     * Helper method to safely unserialize block configuration during restore
     *
     * @param string $configdata The original base64 encoded block config, as retrieved from the block_instances table
     * @return stdClass
     */
    protected function decode_configdata(string $configdata): stdClass {
        return unserialize_object(base64_decode($configdata));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        throw new coding_exception('define_decode_contents() method needs to be overridden in each subclass of restore_block_task');
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        throw new coding_exception('define_decode_rules() method needs to be overridden in each subclass of restore_block_task');
    }

// Protected API starts here

    /**
     * Define the common setting that any backup block will have
     */
    protected function define_settings() {

        // Nothing to add, blocks doesn't have common settings (for now)

        // End of common activity settings, let's add the particular ones
        $this->define_my_settings();
    }

    /**
     * Define (add) particular settings that each block can have
     */
    abstract protected function define_my_settings();

    /**
     * Define (add) particular steps that each block can have
     */
    abstract protected function define_my_steps();
}
