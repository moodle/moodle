<?php //$Id$

require('../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$adminroot = admin_get_root();
admin_externalpage_setup('profilefields', $adminroot);

require_login();

require_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID));


$id       = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);
$dir      = optional_param('dir', '', PARAM_ALPHA);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$type     = optional_param('type', '', PARAM_ALPHANUM);

$datatypes       = profile_list_datatypes();
$redirect        = $CFG->wwwroot.'/user/profile/index.php';

$strchangessaved    = get_string('changessaved');
$strcancelled       = get_string('cancelled');
$strdefaultcategory = get_string('profiledefaultcategory', 'admin');
$strnofields        = get_string('profilenofieldsdefined', 'admin');
$strcreatefield     = get_string('profilecreatefield', 'admin');


/// Do we have any actions to perform before printing the header

switch ($action) {
    case 'movecategory':
        if (confirm_sesskey()) {
            profile_move_category($id, $dir);
        }
        redirect($redirect);
        exit;
        break;        
    case 'movefield':
        if (confirm_sesskey()) {
            profile_move_field($id, $dir);
        }
        redirect($redirect);
        exit;
        break;
    case 'deletecategory':
        if ($confirm and confirm_sesskey()) {
            $categorycount = count_records('user_info_category');
            $fieldcount    = count_records('user_info_field', 'categoryid', $id);

            /// Can only delete the last category if there are no fields in it
            if ( ($categorycount > 1) or ($fieldcount == 0) ) {
                profile_delete_category($id);
            }
            redirect($redirect);
            exit;
        }
        break;        
    case 'deletefield':
    
        if ($confirm and confirm_sesskey()) {
            if ($field = get_record('user_info_field', 'id', $id)) {
                require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                $newfield = 'profile_field_'.$field->datatype;
                $formfield = new $newfield($field->id);
                $formfield->edit_remove_field();
            }
            redirect($redirect);
            exit;
        }
        
        break;
    case 'editfield':
        unset($field);
        if ($id == 0) {
            if (!isset($datatypes[$type])) {
                redirect($redirect);
                exit;
            }
            $field->id = 0;
            $field->datatype = $type;
            $field->categoryid = 0;
        } elseif (!($field = get_record('user_info_field', 'id', $id))) {
            redirect($redirect);
            exit;
        }
        break;
    case 'editcategory':
        unset($category);
        if ($id == 0) {
            $category->id = 0;
            $category->name = '';
        } elseif (!($category = get_record('user_info_category', 'id', $id))) {
            redirect($redirect);
            exit;
        }
        break;
    default:
}






/// Are we adding or editing a cateogory?
if ( ($action == 'editcategory' )) {

   
    require_once('index_category_form.php');
    $categoryform = new category_form(null);
    $categoryform->set_data($category);
    if ($categoryform->is_cancelled()) {
        redirect($redirect);
        exit;
    } else {
        if ($data = $categoryform->get_data()) {
            if ($data->id == 0) {
                unset($data->id);
                $data->sortorder = count_records('user_info_category') + 1;
                if (!insert_record('user_info_category', $data, false)) {
                    error('There was a problem adding the record to the database');
                    exit;
                }
            } else {
                if (!update_record('user_info_category', $data)) {
                    error('There was a problem updating the record in the database');
                    exit;
                }
            }
            redirect($redirect);
            exit;
            
        } else {
        
            if ($id == 0) {
                $strheading = get_string('profilecreatenewcategory', 'admin');
            } else {
                $strheading = get_string('profileeditcategory', 'admin', $category->name);
            }

            /// Print the header
            admin_externalpage_print_header($adminroot);
            
            print_heading($strheading);
            
            $categoryform->display();
        }
    }

/// Are we adding or editing a field?
} elseif ( $action == 'editfield' ) {
   
    require_once('index_field_form.php');
    $fieldform = new field_form(null, $field->datatype);
    $fieldform->set_data($field);
    if ($fieldform->is_cancelled()) {
        redirect($redirect);
        exit;
    } else {
        if ($data = $fieldform->get_data()) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            if (!$formfield->edit_save($data)) {
                error('There was an error updating the database');
            } else {
                redirect($redirect);
                exit;
            }

        } else {

            if ($id == 0) {
                $strheading = get_string('profilecreatenewfield', 'admin', $datatypes[$type]);
            } else {
                $strheading = get_string('profileeditfield', 'admin', $field->name);
            }

            /// Print the header
            admin_externalpage_print_header($adminroot);

            print_heading($strheading);

            $fieldform->display();
        }
    }

/// Deleting a category that has fields in it, print confirm screen?
} elseif ( ($action == 'deletecategory') and !$confirm ) {

    /// Print the header
    admin_externalpage_print_header($adminroot);

    print_heading('profiledeletecategory', 'admin');

    $fieldcount = count_records('user_info_field', 'categoryid', $id);
    echo '<center>'.get_string('profileconfirmcategorydeletion', 'admin', $fieldcount).'<br /><a href="index.php?id='.$id.'&amp;action=deletecategory&amp;sesskey='.$USER->sesskey.'&amp;confirm=1">'.get_string('yes').' <a href="index.php">'.get_string('no').'</center>';


/// Deleting a field that has user data, print confirm screen
} elseif ( ($action == 'deletefield') and !$confirm ) {

    /// Print the header
    admin_externalpage_print_header($adminroot);

    print_heading('profiledeletefield', 'admin');

    $datacount = count_records('user_info_data', 'fieldid', $id);
    echo '<center>'.get_string('profileconfirmfielddeletion', 'admin', $datacount).'<br /><a href="index.php?id='.$id.'&amp;action=deletefield&amp;sesskey='.$USER->sesskey.'&amp;confirm=1">'.get_string('yes').' <a href="index.php">'.get_string('no').'</center>';
    


/// Print the table of categories and fields
} else {

    /// Print the header
    admin_externalpage_print_header($adminroot);

    print_heading(get_string('profilefields', 'admin'));

    /// Check that we have at least one category defined
    if (count_records('user_info_category') == 0) {
        unset($defaultcategory);
        $defaultcategory->name = $strdefaultcategory;
        $defaultcategory->sortorder = 1;
        insert_record('user_info_category', $defaultcategory);
    }

    /// We only displaying if there are fields defined or there is a category with a name different to the default name
    if ( ( (count_records_select('user_info_category', "name<>'$strdefaultcategory'") > 0) or
           (count_records('user_info_field') > 0) ) and
         ( $categories = get_records_select('user_info_category', '', 'sortorder ASC')) ) {

        
        foreach ($categories as $category) {

        unset ($table);
        $table->head  = array(get_string('profilefield','admin'),get_string('edit'));
        $table->align = array('left','right');
        $table->width = '95%';
        $table->class = 'generaltable profilefield';
        $table->data = array();
            
            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                foreach ($fields as $field) {

                    $table->data[] = array($field->name, profile_field_icons($field));

                } /// End of $fields foreach
            } /// End of $fields if
        
        print_heading($category->name.' '.profile_category_icons($category));
        print_table($table);
        
        } /// End of $categories foreach

    } else {

        notify($strnofields);
        
    } /// End of $categories if


    echo '<hr />';
    echo '<div class="profileeditor">';

    /// Create a new field link
    $options = profile_list_datatypes();
    popup_form($CFG->wwwroot.'/user/profile/index.php?id=0&amp;action=editfield&amp;type=', $options, 'newfieldform','','choose','','',false,'self',$strcreatefield);

/// Create a new category link
    $options = array('action'=>'editcategory', 'id'=>'0');
    print_single_button('index.php',$options,get_string('profilecreatecategory', 'admin'));

    echo '</div>';
}

admin_externalpage_print_footer($adminroot);



/***** Some functions relevant to this script *****/

/**
 * Create a string containing the editing icons for the user profile categories
 * @param   object   the category object
 * @return  string   the icon string
 */
function profile_category_icons ($category) {
    global $CFG, $USER;

    $str->delete   = get_string("delete");
    $str->moveup   = get_string("moveup");
    $str->movedown = get_string("movedown");
    $str->edit     = get_string("edit");

    $editstr = '';
    $categorycount = count_records('user_info_category');
    $fieldcount    = count_records('user_info_field', 'categoryid', $category->id);

    /// Edit
    $editstr .= '<a title="'.$str->edit.'" href="index.php?id='.$category->id.'&amp;action=editcategory&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.$str->edit.'" class="iconsmall" /></a> ';

    /// Delete
    /// Can only delete the last category if there are no fields in it
    if ( ($categorycount > 1) or ($fieldcount == 0) ) {
        $editstr .= '<a title="'.$str->delete.'" href="index.php?id='.$category->id.'&amp;action=deletecategory&amp;sesskey='.$USER->sesskey;
        if ($fieldcount == 0) $editstr .= '&amp;confirm=1';
        $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.$str->delete.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move up
    if ($category->sortorder > 1) {
        $editstr .= '<a title="'.$str->moveup.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=up&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$str->moveup.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move down
    if ($category->sortorder < $categorycount) {
        $editstr .= '<a title="'.$str->movedown.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=down&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$str->movedown.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }
        

    return $editstr;
}

/**
 * Create a string containing the editing icons for the user profile fields
 * @param   object   the field object
 * @return  string   the icon string
 */
function profile_field_icons ($field) {
    global $CFG, $USER;

    if (empty($str)) {
        $str->delete   = get_string("delete");
        $str->moveup   = get_string("moveup");
        $str->movedown = get_string("movedown");
        $str->edit     = get_string("edit");
    }

    $editstr = '';
    $fieldcount = count_records('user_info_field', 'categoryid',$field->categoryid);
    $datacount  = count_records('user_info_data', 'fieldid', $field->id);

    /// Edit
    $editstr .= '<a title="'.$str->edit.'" href="index.php?id='.$field->id.'&amp;action=editfield&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.$str->edit.'" class="iconsmall" /></a> ';

    /// Delete
    $editstr .= '<a title="'.$str->delete.'" href="index.php?id='.$field->id.'&amp;action=deletefield&amp;sesskey='.$USER->sesskey;
    if ($datacount == 0) $editstr .= '&amp;confirm=1'; /// Immediate delete if there is no user data
    $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.$str->delete.'" class="iconsmall" /></a> ';

    /// Move up
    if ($field->sortorder > 1) {
        $editstr .= '<a title="'.$str->moveup.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=up&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$str->moveup.'" class="iconsmall" /></a> ';
     } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move down
    if ($field->sortorder < $fieldcount) {
        $editstr .= '<a title="'.$str->movedown.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=down&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$str->movedown.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    return $editstr;
}


?>
