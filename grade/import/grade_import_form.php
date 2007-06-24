<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_import_form extends moodleform {
    function definition (){
        $mform =& $this->_form;

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'id', optional_param('id'));
        $mform->addElement('header', 'general', get_string('importfile', 'grades'));
        // file upload
        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');
        $textlib = new textlib();
        $encodings = $textlib->get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $encodings);

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000); 
        $mform->addElement('select', 'previewrows', 'Preview rows', $options); // TODO: localize
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
    }

    function get_userfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['userfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }
}

class grade_import_mapping_form extends moodleform {
    
    function definition () {
        global $CFG;
        
        $mform =& $this->_form;

        // this is an array of headers
        $header = $this->_customdata['header'];
        // temporary filename
        $filename = $this->_customdata['filename'];
        // course id
        $id = $this->_customdata['id'];

        $mform->addElement('header', 'general', get_string('identifier', 'grades'));
        $mapfromoptions = array();
        
        if ($header) {
            foreach ($header as $h) {
                $mapfromoptions[$h] = $h;
            }
        }
        $mform->addElement('select', 'mapfrom', get_string('mapfrom', 'grades'), $mapfromoptions);
        //choose_from_menu($mapfromoptions, 'mapfrom');    
        
        $maptooptions = array('userid'=>'userid', 'username'=>'username', 'useridnumber'=>'useridnumber', 'useremail'=>'useremail', '0'=>'ignore');
        //choose_from_menu($maptooptions, 'mapto');
        $mform->addElement('select', 'mapto', get_string('mapto', 'grades'), $maptooptions);
        
        $mform->addElement('header', 'general', get_string('mappings', 'grades'));
        
        $gradeitems = array();
    
        include_once($CFG->libdir.'/gradelib.php');
        
        if ($id) {
            if ($grade_items = grade_grades::fetch_all(array('courseid'=>$id))) {
                foreach ($grade_items as $grade_item) {
                    $gradeitems[$grade_item->idnumber] = $grade_item->itemname;      
                }
            }
        }    

        if ($header) {
            foreach ($header as $h) {
            
                $h = trim($h);
                // this is the order of the headers
                $mform->addElement('hidden', 'maps[]', $h);
                //echo '<input type="hidden" name="maps[]" value="'.$h.'"/>';
                // this is what they map to
        
                $mapfromoptions = array_merge(array('0'=>'ignore', 'new'=>'new gradeitem'), $gradeitems);
                $mform->addElement('select', 'mapping[]', $h, $mapfromoptions);
                //choose_from_menu($mapfromoptions, 'mapping[]', $h);

            }
        }
        $newfilename = 'cvstemp_'.time();
        move_uploaded_file($filename, $CFG->dataroot.'/temp/'.$newfilename);
        
        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'map', 1);
        $mform->addElement('hidden', 'id', optional_param('id'));
        //echo '<input name="filename" value='.$newfilename.' type="hidden" />';
        $mform->addElement('hidden', 'filename', $newfilename);
        
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));        
        
    }
}
?>
