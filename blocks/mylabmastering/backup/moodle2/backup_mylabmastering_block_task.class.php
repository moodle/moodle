<?php

/**
 * @package 
 * @subpackage 
 * @copyright 
 * @license   
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/mylabmastering/backup/moodle2/backup_mylabmastering_stepslib.php');

class backup_mylabmastering_block_task extends backup_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new backup_mylabmastering_block_structure_step('mylabmastering_structure', 'mylabmastering.xml'));
    }

    public function get_fileareas() {
        return array(); // No associated fileareas
    }

    public function get_configdata_encoded_attributes() {
        return array(); // No special handling of configdata
    }

    static public function encode_content_links($content) {
        return $content; // No special encoding of links
    }
}
