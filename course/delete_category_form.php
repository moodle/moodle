<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

class delete_category_form extends moodleform {

    var $_category;

    function definition() {
        global $CFG;

        $mform    =& $this->_form;
        $category = $this->_customdata;
        ensure_context_subobj_present($category, CONTEXT_COURSECAT);
        $this->_category = $category;

        $mform->addElement('header','general', get_string('categorycurrentcontents', '', format_string($category->name)));

        $displaylist = array();
        $notused = array();
        make_categories_list($displaylist, $notused, 'moodle/course:create', $category->id);

        // Check permissions, to see if it OK to give the option to delete
        // the contents, rather than move elsewhere.
        $candeletecontent = true;
        $tocheck = array($category);
        while (!empty($tocheck)) {
            $checkcat = array_pop($tocheck);
            $tocheck = $tocheck + get_child_categories($checkcat->id);
            if (!has_capability('moodle/category:manage', $checkcat->context)) {
                $candeletecontent = false;
                break;
            }
        }

        // TODO check that the user is allowed to delete all the courses MDL-17502!

        $options = array();

        if ($displaylist) {
            $options[0] = get_string('move');
        }

        if ($candeletecontent) {
            $options[1] = get_string('delete');
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
