<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
* Instance add/edit form
*
* @package    mod_certificateuv
* @copyright  Mark Nelson <markn@moodle.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/certificateuv/locallib.php');


class mod_certificateuv_mod_form extends moodleform_mod {

    function definition() {
        global $CFG;
        global $DB;
        

        $mform =& $this->_form;

         $course = $this->get_course();
        //verificar que el curso tenga permisos para generar certificado
        if(certificateuv_course_permission($course->id)){ 
 
            $mform->addElement('header', 'general', get_string('general', 'form'));

            $mform->addElement('text', 'name', get_string('certificatename', 'certificateuv'), array('size'=>'64'));
            if (!empty($CFG->formatstringstriptags)) {
                $mform->setType('name', PARAM_TEXT);
            } else {
                $mform->setType('name', PARAM_CLEAN);
            }
            $mform->addRule('name', null, 'required', null, 'client');

            $this->standard_intro_elements(get_string('intro', 'certificateuv'));

            //Profesores del curso   
            $mform->addElement('select', 'idteacher', get_string('teachertosign', 'certificateuv'), certificateuv_get_teachers_course($course->id));
            $mform->setDefault('idteacher', '0');
            $mform->addHelpButton('idteacher', 'teachertosign', 'certificateuv');
            $mform->addRule('idteacher', null, 'required', null, 'client');


            $mform->addElement('hidden', 'orientation', get_string('orientation', 'certificateuv'));
            $mform->setType('orientation', PARAM_TEXT);
            $mform->setDefault('orientation', 'L');

            $mform->addElement('hidden', 'printwmark', get_string('printwmark', 'certificateuv'));
            $mform->setType('printwmark', PARAM_TEXT);
            $mform->setDefault('printwmark', 'logo_univalle.png');
            
            $mform->addElement('hidden', 'certificatetype', get_string('certificatetype', 'certificateuv'));
            $mform->setType('certificatetype', PARAM_TEXT);
            $mform->setDefault('certificatetype',  certificateuv_get_type_template($course->id));//funcion type certificate

            $mform->addElement('hidden', 'borderstyle', get_string('borderstyle', 'certificateuv'));
            $mform->setType('borderstyle', PARAM_TEXT);
            $mform->setDefault('borderstyle', 'dintev.jpg');

            $mform->addElement('hidden', 'printsignature', get_string('printsignature', 'certificateuv'));
            $mform->setType('printsignature', PARAM_TEXT);
            $mform->setDefault('printsignature', 'Line.png');
            

            $mform->addElement('text', 'printhours', get_string('printhours', 'certificateuv'), array('size'=>'5', 'maxlength' => '255'));
            $mform->setType('printhours', PARAM_TEXT);
            $mform->addHelpButton('printhours', 'printhours', 'certificateuv');
            $mform->addRule('printhours', null, 'required', null, 'client');

            $mform->addElement('date_selector', 'timestartcourse', get_string('timestartcourse', 'certificateuv'));
            $mform->addElement('date_selector', 'timefinalcourse', get_string('timefinalcourse', 'certificateuv'));
            
            $this->standard_coursemodule_elements();
            $this->add_action_buttons();
      
        }else{
            $mform->addElement('header', '', "<h1>Este curso no posee autorización por parte de la Dintev para exportar certificados</h1>");
            $this->standard_coursemodule_elements();
      

        }
    }

    /**
     * Some basic validation
     *
     * @param $data
     * @param $files
     * @return array
     */

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        // Check that the required time entered is valid
        if ((date($data['timestartcourse'])) > date($data['timefinalcourse'])){

            $errors['timestartcourse'] = "La fecha inicial es menor que la fecha final";
        }

        if(!certificadouv_check_signature_image($data['idteacher'])){
        	$errors['idteacher'] = "El docente seleccionado no ha cargado su firma digital";	
        }

        return $errors;
    }
}
