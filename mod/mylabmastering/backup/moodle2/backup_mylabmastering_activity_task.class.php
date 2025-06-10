<?php
/**
 * This file contains the mylabmastering module backup class
 *
 * @package    
 * @subpackage 
 * @copyright  
 * @author     
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/mylabmastering/backup/moodle2/backup_mylabmastering_stepslib.php');

/**
 * pearson backup task that provides all the settings and steps to perform one
 * complete backup of the module
 */
class backup_mylabmastering_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new backup_mylabmastering_activity_structure_step('mylabmastering_structure', 'mylabmastering.xml'));
    }

    static public function encode_content_links($content) {
        return $content;
    }
}
