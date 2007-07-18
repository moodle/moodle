<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_import_form extends moodleform {
    function definition (){
        $mform =& $this->_form;

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'id', optional_param('id'));
        $mform->setType('id', PARAM_INT);
        $mform->addElement('header', 'general', get_string('importfile', 'grades'));
        // file upload
        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->setType('userfile', PARAM_FILE);
        $mform->addRule('userfile', null, 'required');
        $textlib = new textlib();
        $encodings = $textlib->get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $encodings);

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', 'Preview rows', $options); // TODO: localize
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
    }

    function get_userfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['userfile']['tmp_name'];
        } else{
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

        $mform->addElement('header', 'general', get_string('identifier', 'grades'));
        $mapfromoptions = array();

        if ($header) {
            foreach ($header as $i=>$h) {
                $mapfromoptions[$i] = s($h);
            }
        }
        $mform->addElement('select', 'mapfrom', get_string('mapfrom', 'grades'), $mapfromoptions);
        //choose_from_menu($mapfromoptions, 'mapfrom');

        $maptooptions = array('userid'=>'userid', 'username'=>'username', 'useridnumber'=>'useridnumber', 'useremail'=>'useremail', '0'=>'ignore');
        //choose_from_menu($maptooptions, 'mapto');
        $mform->addElement('select', 'mapto', get_string('mapto', 'grades'), $maptooptions);

        $mform->addElement('header', 'general', get_string('mappings', 'grades'));

        // add a comment option

        if ($gradeitems = $this->_customdata['gradeitems']) {
            $comments = array();
            foreach ($gradeitems as $itemid => $itemname) {
                $comments['feedback_'.$itemid] = 'comments for '.$itemname;
            }
        }

        include_once($CFG->libdir.'/gradelib.php');

        if ($header) {
            $i = 0; // index
            foreach ($header as $h) {

                $h = trim($h);
                // this is what each header maps to
                $mform->addElement('selectgroups',
                                   'mapping_'.$i, s($h),
                                   array('others'=>array('0'=>'ignore', 'new'=>'new gradeitem'),
                                         'gradeitems'=>$gradeitems,
                                         'comments'=>$comments));
                $i++;
            }
        }

        // find a non-conflicting file name based on time stamp
        $newfilename = 'cvstemp_'.time();
        while (file_exists($CFG->dataroot.'/temp/'.$newfilename)) {
            $newfilename = 'cvstemp_'.time();
        }

        // move the uploaded file
        move_uploaded_file($filename, $CFG->dataroot.'/temp/'.$newfilename);

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'map', 1);
        $mform->setType('map', PARAM_INT);
        $mform->addElement('hidden', 'id', optional_param('id'));
        $mform->setType('id', PARAM_INT);
        //echo '<input name="filename" value='.$newfilename.' type="hidden" />';
        $mform->addElement('hidden', 'filename', $newfilename);
        $mform->setType('filename', PARAM_FILE);
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));

    }
}
?>
