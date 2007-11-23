<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class question_context_move_question_form extends moodleform {

    function definition() {
        global $CFG;
        $mform    =& $this->_form;

//--------------------------------------------------------------------------------
        $urls   = $this->_customdata['urls'];
        $fromareaname   = $this->_customdata['fromareaname'];
        $toareaname   = $this->_customdata['toareaname'];
        $fileoptions = array(QUESTION_FILEDONOTHING=>get_string('donothing', 'question'),
                  QUESTION_FILECOPY=>get_string('copy', 'question', $fromareaname),
                  QUESTION_FILEMOVE=>get_string('move', 'question', $fromareaname),
                  QUESTION_FILEMOVELINKSONLY=>get_string('movelinksonly', 'question', $fromareaname));
        $brokenfileoptions = array(QUESTION_FILEDONOTHING=>get_string('donothing', 'question'),
                  QUESTION_FILEMOVELINKSONLY=>get_string('movelinksonly', 'question', $fromareaname));

        $brokenurls   = $this->_customdata['brokenurls'];
        if (count($urls)){

            $mform->addElement('header','general', get_string('filestomove', 'question', $toareaname));

            $i = 0;
            foreach (array_keys($urls) as $url){
                $iconname = mimeinfo('icon', $url);
                $icontype = mimeinfo('type', $url);
                $img = "<img src=\"$CFG->pixpath/f/$iconname\"  class=\"icon\" alt=\"$icontype\" />";
                if (in_array($url, $brokenurls)){
                    $mform->addElement('select', "urls[$i]", $img.$url, $brokenfileoptions);
                } else {
                    $mform->addElement('select', "urls[$i]", $img.$url, $fileoptions);
                }
                $i++;
            }

        }
        if (count($brokenurls)){
            $mform->addElement('advcheckbox','ignorebroken', get_string('ignorebroken', 'question'));
        }
//--------------------------------------------------------------------------------
        $this->add_action_buttons(true, get_string('moveq', 'question'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $tocoursefilesid = $this->_customdata['tocoursefilesid'];
        $fromcoursefilesid = $this->_customdata['fromcoursefilesid'];
        if (isset($data['urls'])  && (count($data['urls']))){
            foreach ($data['urls'] as $key => $urlaction){
                switch ($urlaction){
                    case QUESTION_FILEMOVE :
                        if (!has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $fromcoursefilesid))){
                            $errors["urls[$key]"] = get_string('filecantmovefrom', 'question');
                        }
                    case QUESTION_FILECOPY :
                        if (!has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $tocoursefilesid))){
                            $errors["urls[$key]"] = get_string('filecantmoveto', 'question');
                        }
                        break;
                    case QUESTION_FILEMOVELINKSONLY :
                    case  QUESTION_FILEDONOTHING :
                        break;
                }
            }
        }
        //check that there hasn't been any changes in files between time form was displayed
        //and now when it has been submitted.
        if (isset($data['urls'])  &&
            (count($data['urls'])
                != count($this->_customdata['urls']))){
            $errors['urls[0]'] = get_string('errorfileschanged', 'question');

        }
        return $errors;
    }
    /*
     * We want these errors to show up on first loading the form which is not the default for
     * validation method which is not run until submission.
     */
    function definition_after_data(){
        static $done = false;
        if (!$done){
            $mform = $this->_form;
            $brokenurls   = $this->_customdata['brokenurls'];
            if (count($brokenurls)){
                $ignoreval = $mform->getElementValue('ignorebroken');
                if (!$ignoreval){
                    $urls   = $this->_customdata['urls'];
                    $i = 0;
                    foreach (array_keys($urls) as $url){
                        if (in_array($url, $brokenurls)){
                            $mform->setElementError("urls[$i]", get_string('broken', 'question'));
                        } else {
                            $mform->setElementError("urls[$i]", '');
                        }
                        $i++;
                    }
                }
            }
            $done = true;
        }
    }
}
?>
