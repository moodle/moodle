<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

class delete_category_form extends moodleform {

    var $_category;

    function definition() {
        global $CFG;

        $mform    =& $this->_form;
        $category = $this->_customdata;
        $this->_category = $category;

        $mform->addElement('header','general', get_string('categorycurrentcontents', '', format_string($category->name)));

        $displaylist = array();
        $parentlist = array();
        $children = array();
        make_categories_list($displaylist, $parentlist);
        unset($displaylist[$category->id]);
        foreach ($displaylist as $catid=>$unused) {
            // remove all children of $category
            if (isset($parentlist[$catid]) and in_array($category->id, $parentlist[$catid])) {
                $children[] = $catid;
                unset($displaylist[$catid]);
                continue;
            }
            if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $catid))) {
                unset($displaylist[$catid]);
            }
        }

        $candeletecontent = true;
        foreach ($children as $catid) {
            $context = get_context_instance(CONTEXT_COURSECAT, $catid);
            if (!has_capability('moodle/category:delete', $context)) {
                $candeletecontent = false;
                break;
            }
        }

        $options = array();

        if ($displaylist) {
            $options[0] = get_string('move');
        }

        if ($candeletecontent) {
            $options[1] =get_string('delete');
        }

        if (empty($options)) {
            print_error('nocategorydelete', 'error', 'index.php', format_string($category->name));
        }

        $mform->addElement('select', 'fulldelete', get_string('categorycontents'), $options);
        $mform->disabledIf('newparent', 'fulldelete', 'eq', '1');
        $mform->setDefault('newparent', 0);

        if ($displaylist) {
            $mform->addElement('select', 'newparent', get_string('movecategorycontentto'), $displaylist);
            if (in_array($category->parent, $displaylist)) {
                $mform->setDefault('newparent', $category->parent);
            }
        }

        $mform->addElement('hidden', 'delete');
        $mform->addElement('hidden', 'sure');
        $mform->setDefault('sure', md5(serialize($category)));

//--------------------------------------------------------------------------------
        $this->add_action_buttons(true, get_string('delete'));

    }

/// perform some extra moodle validation
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['fulldelete'])) {
            // already verified
        } else {
            if (empty($data['newparent'])) {
                $errors['newparent'] = get_string('required');
            }
        }

        if ($data['sure'] != md5(serialize($this->_category))) {
            $errors['categorylabel'] = get_string('categorymodifiedcancel');
        }

        return $errors;
    }
}
?>
