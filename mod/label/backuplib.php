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
 * Backup supportfor label module
 *
 * @package    mod
 * @subpackage label
 * @copyright  2003 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

    //This php script contains all the stuff to backup/restore
    //label mods

    //This is the "graphical" structure of the label mod:
    //
    //                       label
    //                     (CL,pk->id)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
    function label_backup_mods($bf,$preferences) {
        global $CFG, $DB;

        $status = true;

        ////Iterate over label table
        if ($labels = $DB->get_records ("label","course", array($preferences->backup_course=>"id"))) {
            foreach ($labels as $label) {
                if (backup_mod_selected($preferences,'label',$label->id)) {
                    $status = label_backup_one_mod($bf,$preferences,$label);
                }
            }
        }
        return $status;
    }

    function label_backup_one_mod($bf,$preferences,$label) {
        global $CFG, $DB;

        if (is_numeric($label)) {
            $label = $DB->get_record('label', array('id'=>$label));
        }

        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$label->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"label"));
        fwrite ($bf,full_tag("NAME",4,false,$label->name));
        fwrite ($bf,full_tag("CONTENT",4,false,$label->intro));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$label->timemodified));
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    ////Return an array of info (name,value)
    function label_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        global $DB;

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += label_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }

         //First the course data
         $info[0][0] = get_string("modulenameplural","label");
         $info[0][1] = $DB->count_records("label", array("course"=>$course));
         return $info;
    }

    ////Return an array of info (name,value)
    function label_check_backup_mods_instances($instance,$backup_unique_code) {
         //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        return $info;
    }


