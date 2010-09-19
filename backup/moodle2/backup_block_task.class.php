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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * abstract block task that provides all the properties and common steps to be performed
 * when one block is being backup
 *
 * TODO: Finish phpdocs
 */
abstract class backup_block_task extends backup_task {

    protected $blockid;
    protected $blockname;
    protected $contextid;
    protected $moduleid;
    protected $modulename;
    protected $parentcontextid;

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $blockid, $moduleid = null, $plan = null) {
        global $DB;

        // Check blockid exists
        if (!$block = $DB->get_record('block_instances', array('id' => $blockid))) {
            throw new backup_task_exception('block_task_block_instance_not_found', $blockid);
        }

        $this->blockid    = $blockid;
        $this->blockname  = $block->blockname;
        $this->contextid  = get_context_instance(CONTEXT_BLOCK, $this->blockid)->id;
        $this->moduleid   = $moduleid;
        $this->modulename = null;
        $this->parentcontextid = null;

        // If moduleid passed, check exists, supports moodle2 format and save info
        // Check moduleid exists
        if (!empty($moduleid)) {
            if (!$coursemodule = get_coursemodule_from_id(false, $moduleid)) {
                throw new backup_task_exception('block_task_coursemodule_not_found', $moduleid);
            }
            // Check activity supports this moodle2 backup format
            if (!plugin_supports('mod', $coursemodule->modname, FEATURE_BACKUP_MOODLE2)) {
                throw new backup_task_exception('block_task_activity_lacks_moodle2_backup_support', $coursemodule->modname);
            }

            $this->moduleid   = $moduleid;
            $this->modulename = $coursemodule->modname;
            $this->parentcontextid  = get_context_instance(CONTEXT_MODULE, $this->moduleid)->id;
        }

        parent::__construct($name, $plan);
    }

    public function get_blockid() {
        return $this->blockid;
    }

    public function get_blockname() {
        return $this->blockname;
    }

    public function get_moduleid() {
        return $this->moduleid;
    }

    public function get_modulename() {
        return $this->modulename;
    }

    public function get_contextid() {
        return $this->contextid;
    }

    public function get_parentcontextid() {
        return $this->parentcontextid;
    }

    /**
     * Block tasks have their own directory to write files
     */
    public function get_taskbasepath() {
        $basepath = $this->get_basepath();

        // Module blocks are under module dir
        if (!empty($this->moduleid)) {
            $basepath .= '/activities/' . $this->modulename . '_' . $this->moduleid .
                         '/blocks/' . $this->blockname . '_' . $this->blockid;

        // Course blocks are under course dir
        } else {
            $basepath .= '/course/blocks/' . $this->blockname . '_' . $this->blockid;
        }
        return $basepath;
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
        if (!empty($this->moduleid)) {
            $includedsetting = $this->modulename . '_' . $this->moduleid . '_included';
            if (!$this->get_setting_value($includedsetting)) {
                $this->built = true;
                return;
            }
        }

        // Add some extra settings that related processors are going to need
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_BLOCKID, base_setting::IS_INTEGER, $this->blockid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_BLOCKNAME, base_setting::IS_FILENAME, $this->blockname));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_MODID, base_setting::IS_INTEGER, $this->moduleid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_MODNAME, base_setting::IS_FILENAME, $this->modulename));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $this->get_courseid()));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $this->contextid));

        // Create the block directory
        $this->add_step(new create_taskbasepath_directory('create_block_directory'));

        // Create the block.xml common file (instance + positions)
        $this->add_step(new backup_block_instance_structure_step('block_commons', 'block.xml'));

        // Here we add all the common steps for any block and, in the point of interest
        // we call to define_my_steps() in order to get the particular ones inserted in place.
        $this->define_my_steps();

        // Generate the roles file (optionally role assignments and always role overrides)
        $this->add_step(new backup_roles_structure_step('block_roles', 'roles.xml'));

        // Generate the comments file (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new backup_comments_structure_step('block_comments', 'comments.xml'));
        }

        // Generate the inforef file (must be after ALL steps gathering annotations of ANY type)
        $this->add_step(new backup_inforef_structure_step('block_inforef', 'inforef.xml'));

        // Migrate the already exported inforef entries to final ones
        $this->add_step(new move_inforef_annotations_to_final('migrate_inforef'));

        // At the end, mark it as built
        $this->built = true;
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

    /**
     * Define one array() of fileareas that each block controls
     */
    abstract public function get_fileareas();

    /**
     * Define one array() of configdata attributes
     * that need to be processed by the contenttransformer
     */
    abstract public function get_configdata_encoded_attributes();

    /**
     * Code the transformations to perform in the block in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        throw new coding_exception('encode_content_links() method needs to be overridden in each subclass of backup_block_task');
    }
}
