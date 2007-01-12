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
            $categorycount = count_records_select('user_info_category', '1');
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
            $field->datatype = $datatypes[$type];
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



/// Print the header
admin_externalpage_print_header($adminroot);



/// Are we adding or editing a cateogory?
if ( ($action == 'editcategory' )) {

    if ($id == 0) {
        $strheading = get_string('profilecreatenewcategory', 'admin');
    } else {
        $strheading = get_string('profileeditcategory', 'admin', $category->name);
    }

    print_heading($strheading);
    
    require_once('index_category_form.php');
    $categoryform = new category_form(null, compact('category'));
    if ($categoryform->is_cancelled()) {
        redirect($redirect, $strcancelled);
    } else {
        if ($data = $categoryform->data_submitted()) {
            if ($data->id == 0) {
                unset($data->id);
                $data->sortorder = count_records_select('user_info_category', '1') + 1;
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
            redirect($redirect, $strchangessaved);
        } else {
            $categoryform->display();
        }
    }

/// Are we adding or editing a field?
} elseif ( $action == 'editfield' ) {

    if ($id == 0) {
        $strheading = get_string('profilecreatenewfield', 'admin');
    } else {
        $strheading = get_string('profileeditfield', 'admin', $field->name);
    }

    print_heading($strheading);
    
    require_once('index_field_form.php');
    $fieldform = new field_form(null, compact('field'));
    if ($fieldform->is_cancelled()) {
        redirect($redirect, $strcancelled);
    } else {
        if ($data = $fieldform->data_submitted()) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            if (!$formfield->edit_save($data)) {
                error('There was an error updating the database');
            } else {
                redirect($redirect, $strchangessaved);
            }

        } else {
            $fieldform->display();
        }
    }

/// Deleting a category that has fields in it, print confirm screen?
} elseif ( ($action == 'deletecategory') and !$confirm ) {

    print_heading('profiledeletecategory', 'admin');
    
    $fieldcount = count_records('user_info_field', 'categoryid', $id);
    echo '<center>'.get_string('profileconfirmcategorydeletion', 'admin', $fieldcount).'<br /><a href="index.php?id='.$id.'&amp;action=deletecategory&amp;sesskey='.$USER->sesskey.'&amp;confirm=1">'.get_string('yes').' <a href="index.php">'.get_string('no').'</center>';


/// Deleting a field that has user data, print confirm screen
} elseif ( ($action == 'deletefield') and !$confirm ) {

    print_heading('profiledeletefield', 'admin');

    $datacount = count_records('user_info_data', 'fieldid', $id);
    echo '<center>'.get_string('profileconfirmfielddeletion', 'admin', $datacount).'<br /><a href="index.php?id='.$id.'&amp;action=deletefield&amp;sesskey='.$USER->sesskey.'&amp;confirm=1">'.get_string('yes').' <a href="index.php">'.get_string('no').'</center>';
    


/// Print the table of categories and fields
} else {

    /// Check that we have at least one category defined
    if (count_records_select('user_info_category', '1') == 0) {
        unset($defaultcategory);
        $defaultcategory->name = $strdefaultcategory;
        $defaultcategory->sortorder = 1;
        insert_record('user_info_category', $defaultcategory);
    }

    print_heading(get_string('profilefields', 'admin'));

    /// We only displaying if there are fields defined or there is a category with a name different to the default name
    if ( ( (count_records_select('user_info_category', "name<>'$strdefaultcategory'") > 0) or
           (count_records_select('user_info_field', '1') > 0) ) and
         ( $categories = get_records_select('user_info_category', '1', 'sortorder ASC')) ) {
        unset ($table);
        $table->align = array('left', 'right');
        $table->width = '95%';
        $table->data = array();
        
        foreach ($categories as $category) {

            $table->data[] = array($category->name, profile_category_icons($category));

            unset($table2);
            $table2->align = array('left', 'right');
            $table2->width = '100%';
            $table2->data  = array();

            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                foreach ($fields as $field) {

                    $table2->data[] = array($field->shortname, profile_field_icons($field));

                } /// End of $fields foreach
            } /// End of $fields if

            if (!empty($table2->data)) {
                $table->data[] = array('',print_table($table2,true));
            }

        } /// End of $categories foreach
        
        print_table($table);

    } else {

        notify($strnofields);
        
    } /// End of $categories if


    echo '<hr />';
    echo '<center>';

    /// Create a new field link
    $options = profile_list_datatypes();
    popup_form($CFG->wwwroot.'/user/profile/index.php?id=0&amp;action=editfield&amp;type=', $options, 'newfieldform','','choose','','',false,'self',$strcreatefield);

/// Create a new category link
    $options = array('action'=>'editcategory', 'id'=>'0');
    print_single_button('index.php',$options,get_string('profilecreatecategory', 'admin'));

    echo '</center>';
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
    $categorycount = count_records_select('user_info_category', '1');
    $fieldcount    = count_records('user_info_field', 'categoryid', $category->id);

    /// Edit
    $editstr .= '<a title="'.$str->edit.'" href="index.php?id='.$category->id.'&amp;action=editcategory&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.$str->edit.'" /></a> ';

    /// Delete
    /// Can only delete the last category if there are no fields in it
    if ( ($categorycount > 1) or ($fieldcount == 0) ) {
        $editstr .= '<a title="'.$str->delete.'" href="index.php?id='.$category->id.'&amp;action=deletecategory&amp;sesskey='.$USER->sesskey;
        if ($fieldcount == 0) $editstr .= '&amp;confirm=1';
        $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.$str->delete.'" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" height="11" width="11" border="0" alt="" /> ';
    }

    /// Move up
    if ($category->sortorder > 1) {
        $editstr .= '<a title="'.$str->moveup.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=up&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/up.gif" height="11" width="11" border="0" alt="'.$str->moveup.'" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" height="11" width="11" border="0" alt="" /> ';
    }

    /// Move down
    if ($category->sortorder < $categorycount) {
        $editstr .= '<a title="'.$str->movedown.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=down&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/down.gif" height="11" width="11" border="0" alt="'.$str->movedown.'" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" height="11" width="11" border="0" alt="" /> ';
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
    $editstr .= '<a title="'.$str->edit.'" href="index.php?id='.$field->id.'&amp;action=editfield&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.$str->edit.'" /></a> ';

    /// Delete
    $editstr .= '<a title="'.$str->delete.'" href="index.php?id='.$field->id.'&amp;action=deletefield&amp;sesskey='.$USER->sesskey;
    if ($datacount == 0) $editstr .= '&amp;confirm=1'; /// Immediate delete if there is no user data
    $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.$str->delete.'" /></a> ';

    /// Move up
    if ($field->sortorder > 1) {
        $editstr .= '<a title="'.$str->moveup.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=up&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/up.gif" height="11" width="11" border="0" alt="'.$str->moveup.'" /></a> ';
     } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" height="11" width="11" border="0" alt="" /> ';
    }

    /// Move down
    if ($field->sortorder < $fieldcount) {
        $editstr .= '<a title="'.$str->movedown.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=down&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/down.gif" height="11" width="11" border="0" alt="'.$str->movedown.'" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" height="11" width="11" border="0" alt="" /> ';
    }

    return $editstr;
}


?>
