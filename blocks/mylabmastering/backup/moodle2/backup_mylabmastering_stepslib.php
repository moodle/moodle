<?php

/**
 * @package 
 * @subpackage 
 * @copyright 
 * @license   
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that wll be used by the backup_mylabmastering_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_mylabmastering_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;

        // Get the block
        $block = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));
        // Extract configdata
        $config = unserialize(base64_decode($block->configdata));

        // Define each element separated

        $mylabmastering = new backup_nested_element('mylabmastering', array('id'), null);

        // Define sources

        $mylabmastering->set_source_array(array((object)array('id' => $this->task->get_blockid())));

        // Annotations (none)

        // Return the root element (mylabmastering), wrapped into standard block structure
        return $this->prepare_block_structure($mylabmastering);
    }
}
