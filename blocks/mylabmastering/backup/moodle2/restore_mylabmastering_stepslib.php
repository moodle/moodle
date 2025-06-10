<?php

/**
 * @package 
 * @subpackage 
 * @copyright 
 * @license   
 */
defined('MOODLE_INTERNAL') || die;
class restore_mylabmastering_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('mylabmastering', '/block/mylabmastering');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

        $data = (object)$data;
 
        // For any reason (non multiple, dupe detected...) block not restored, return
        if (!$this->task->get_blockid()) {
            return;
        }

         // Get the configdata
        $configdata = $DB->get_field('block_instances', 'configdata', array('id' => $this->task->get_blockid()));
        // Extract configdata
        $config = unserialize(base64_decode($configdata));
        // Serialize back the configdata
        $configdata = base64_encode(serialize($config));
        // Set the configdata back
        $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $this->task->get_blockid()));
    }
}
