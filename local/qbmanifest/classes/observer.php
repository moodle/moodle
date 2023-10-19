<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for local_qbmanifest.
 */
class local_qbmanifest_observer {
     /**
     * Observer for \core\event\course_module_created event.
     *
     * @param \core\event\course_module_created $event
     * @return void
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        global $CFG,$DB;
        
        $instanceid =  $event->other['instanceid'];
        if ($event->other['modulename'] === 'qbassign') {            
            $record = $DB->get_record('qbassign', array("id" => $instanceid));

            if($record){
                if($record->uid == ''){
                    $DB->set_field('qbassign', 'uid', 'custom_a_'.$instanceid, array('id' => $instanceid));
                }                
            }
        }elseif ($event->other['modulename'] === 'quiz') {            
            $record = $DB->get_record('quiz', array("id" => $instanceid));
            if($record){
                if($record->uid == ''){
                    $DB->set_field('quiz', 'uid', 'custom_q_'.$instanceid, array('id' => $instanceid));
                }                
            }
        }elseif ($event->other['modulename'] === 'qubitspage') {            
            $record = $DB->get_record('qubitspage', array("id" => $instanceid));
            if($record){
                if($record->uid == ''){
                    $DB->set_field('qubitspage', 'uid', 'custom_p_'.$instanceid, array('id' => $instanceid));
                }                
            }
        }
    }
}
