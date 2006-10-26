<?php //$Id$

// TODO - THE HELP BUTTON FOR FORMATTING TO BE Included and formatting drop down box.
include_once $CFG->libdir.'/formslib.php';
class glossary_comment_form extends moodleform {
    function definition() {
        $mform    =& $this->_form;
        if(isset($this->_customdata['comment'])) {
            $comment   = $this->_customdata['comment'];
            $commentid=$comment->id;
            $commenttext = $comment->entrycomment;
            $defaultformat=$comment->format;

        }else{
            $commentid=0;
            $commenttext ='';
            $defaultformat=null;
        }
        $entry   = $this->_customdata['entry'];
        $cm   = $this->_customdata['cm'];
        $action = $this->_customdata['action'];

        $mform    =& $this->_form;

        //two pronged attack for trusttext
        //submitted value
        if (!empty($_POST)){
            trusttext_prepare_edit($_POST['comment'], $_POST['format'], can_use_html_editor(),
                                  $this->_customdata['context']);
        }

        $mform->addElement('htmleditor','comment',get_string("comment", "glossary"));
        $mform->setType('comment', PARAM_RAW);

        $mform->addElement('format', 'format', get_string("format"));
        $mform->setDefault('format',$defaultformat);

        //second prong : defaults
        // format element works it's default out for itself
        $format=$mform->exportValue('format');
        trusttext_prepare_edit($commenttext, $format, can_use_html_editor(),
                                     $this->_customdata['context']);
        $mform->setDefault('format',$format);
        $mform->setDefault('comment',$commenttext);


        //hidden elements, in this case setType may not be needed as these
        //are all processed by optional_param in comment.php but just in case
        //someone later gets data from form->data_submitted() we'll add them.
        $mform->addElement('hidden','cid',$comment->id);
        $mform->setType('cid', PARAM_INT);

        $mform->addElement('hidden','id',$cm->id);
        $mform->setType('cid', PARAM_INT);

        $mform->addElement('hidden','eid',$entry->id);
        $mform->setType('eid', PARAM_INT);

        $mform->addElement('hidden','action',$action);
        $mform->setType('action', PARAM_ACTION);

        $buttonarray[] = &MoodleQuickForm::createElement('submit','submit',get_string("savechanges"));
        $buttonarray[] = &MoodleQuickForm::createElement('reset','reset',get_string("revert"));
        $mform->addGroup($buttonarray,'buttonar','', array(" "), false);


    }

}
?>