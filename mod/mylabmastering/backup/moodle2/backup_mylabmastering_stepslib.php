<?php

/**
 * This file contains all the backup steps that will be used
 * by the backup_pearson_activity_task
 *
 * @package    
 * @subpackage 
 * @copyright  
 * @author     
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete assignment structure for backup, with file and id annotations
 */
class backup_mylabmastering_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $mylabmastering = new backup_nested_element('mylabmastering', array('id'), null);

        // Build the tree
        // (none)

        // Define sources
        $mylabmastering->set_source_table('mylabmastering', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        // (none)

        // Return the root element (mylabmastering), wrapped into standard activity structure
        return $this->prepare_activity_structure($mylabmastering);
    }
}
