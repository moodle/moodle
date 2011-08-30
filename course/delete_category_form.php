<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/questionlib.php');

class delete_category_form extends moodleform {

    var $_category;

    function definition() {
        global $CFG, $DB;

        $mform    =& $this->_form;
        $category = $this->_customdata;
        $categorycontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
        $this->_category = $category;

    /// Check permissions, to see if it OK to give the option to delete
    /// the contents, rather than move elsewhere.
    /// Are there any subcategories of this one, can they be deleted?
        $candeletecontent = true;
        $tocheck = get_child_categories($category->id);
        $containscategories = !empty($tocheck);
        $categoryids = array($category->id);
        while (!empty($tocheck)) {
            $checkcat = array_pop($tocheck);
            $childcategoryids[] = $checkcat->id;
            $tocheck = $tocheck + get_child_categories($checkcat->id);
            $chcontext = get_context_instance(CONTEXT_COURSECAT, $checkcat->id);
            if ($candeletecontent && !has_capability('moodle/category:manage', $chcontext)) {
                $candeletecontent = false;
            }
        }

    /// Are there any courses in here, can they be deleted?
        list($test, $params) = $DB->get_in_or_equal($categoryids);
        $containedcourses = $DB->get_records_sql(
                "SELECT id,1 FROM {course} c WHERE c.category $test", $params);
        $containscourses = false;
        if ($containedcourses) {
            $containscourses = true;
            foreach ($containedcourses as $courseid => $notused) {
                if ($candeletecontent && !can_delete_course($courseid)) {
                    $candeletecontent = false;
                    break;
                }
            }
        }

    /// Are there any questions in the question bank here?
        $containsquestions = question_context_has_any_questions($categorycontext);

    /// Get the list of categories we might be able to move to.
        $testcaps = array();
        if ($containscourses) {
            $testcaps[] = 'moodle/course:create';
        }
        if ($containscategories || $containsquestions) {
            $testcaps[] = 'moodle/category:manage';
        }
        $displaylist = array();
        $notused = array();
        if (!empty($testcaps)) {
            make_categories_list($displaylist, $notused, $testcaps, $category->id);
        }

    /// Now build the options.
        $options = array();
        if ($displaylist) {
            $options[0] = get_string('movecontentstoanothercategory');
        }
        if ($candeletecontent) {
            $options[1] = get_string('deleteallcannotundo');
        }

    /// Now build the form.
        $mform->addElement('header','general', get_string('categorycurrentcontents', '', format_string($category->name, true, array('context' => $categorycontext))));

        if ($containscourses || $containscategories || $containsquestions) {
            if (empty($options)) {
                print_error('youcannotdeletecategory', 'error', 'index.php', format_string($category->name, true, array('context' => $categorycontext)));
            }

        /// Describe the contents of this category.
            $contents = '<ul>';
            if ($containscategories) {
                $contents .= '<li>' . get_string('subcategories') . '</li>';
            }
            if ($containscourses) {
                $contents .= '<li>' . get_string('courses') . '</li>';
            }
            if ($containsquestions) {
                $contents .= '<li>' . get_string('questionsinthequestionbank') . '</li>';
            }
            $contents .= '</ul>';
            $mform->addElement('static', 'emptymessage', get_string('thiscategorycontains'), $contents);

        /// Give the options for what to do.
            $mform->addElement('select', 'fulldelete', get_string('whattodo'), $options);
            if (count($options) == 1) {
                $mform->hardFreeze('fulldelete');
                $mform->setConstant('fulldelete', reset(array_keys($options)));
            }

            if ($displaylist) {
                $mform->addElement('select', 'newparent', get_string('movecategorycontentto'), $displaylist);
                if (in_array($category->parent, $displaylist)) {
                    $mform->setDefault('newparent', $category->parent);
                }
                $mform->disabledIf('newparent', 'fulldelete', 'eq', '1');
            }
        } else {
            $mform->addElement('hidden', 'fulldelete', 1);
            $mform->setType('fulldelete', PARAM_INT);
            $mform->addElement('static', 'emptymessage', '', get_string('deletecategoryempty'));
        }

        $mform->addElement('hidden', 'delete');
        $mform->setType('delete', PARAM_ALPHANUM);
        $mform->addElement('hidden', 'sure');
        $mform->setType('sure', PARAM_ALPHANUM);
        $mform->setDefault('sure', md5(serialize($category)));

//--------------------------------------------------------------------------------
        $this->add_action_buttons(true, get_string('delete'));

    }

/// perform some extra moodle validation
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['fulldelete']) && empty($data['newparent'])) {
        /// When they have chosen the move option, they must specify a destination.
            $errors['newparent'] = get_string('required');
        }

        if ($data['sure'] != md5(serialize($this->_category))) {
            $errors['categorylabel'] = get_string('categorymodifiedcancel');
        }

        return $errors;
    }
}

