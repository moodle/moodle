<?php //$Id$

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/profile/definelib.php');

admin_externalpage_setup('profilefields');

$action   = optional_param('action', '', PARAM_ALPHA);

$redirect = $CFG->wwwroot.'/user/profile/index.php';

$strchangessaved    = get_string('changessaved');
$strcancelled       = get_string('cancelled');
$strdefaultcategory = get_string('profiledefaultcategory', 'admin');
$strnofields        = get_string('profilenofieldsdefined', 'admin');
$strcreatefield     = get_string('profilecreatefield', 'admin');


/// Do we have any actions to perform before printing the header

switch ($action) {
    case 'movecategory':
        $id  = required_param('id', PARAM_INT);
        $dir = required_param('dir', PARAM_ALPHA);

        if (confirm_sesskey()) {
            profile_move_category($id, $dir);
        }
        redirect($redirect);
        break;
    case 'movefield':
        $id  = required_param('id', PARAM_INT);
        $dir = required_param('dir', PARAM_ALPHA);

        if (confirm_sesskey()) {
            profile_move_field($id, $dir);
        }
        redirect($redirect);
        break;
    case 'deletecategory':
        $id      = required_param('id', PARAM_INT);
        $confirm = optional_param('confirm', 0, PARAM_BOOL);

        if (data_submitted() and $confirm and confirm_sesskey()) {
            profile_delete_category($id);
            redirect($redirect);
        }

        //ask for confirmation
        $fieldcount = count_records('user_info_field', 'categoryid', $id);
        $optionsyes = array ('id'=>$id, 'confirm'=>1, 'action'=>'deletecategory', 'sesskey'=>sesskey());
        admin_externalpage_print_header();
        print_heading('profiledeletecategory', 'admin');
        notice_yesno(get_string('profileconfirmcategorydeletion', 'admin', $fieldcount), $redirect, $redirect, $optionsyes, null, 'post', 'get');
        admin_externalpage_print_footer();
        die;
        break;
    case 'deletefield':
        $id      = required_param('id', PARAM_INT);
        $confirm = optional_param('confirm', 0, PARAM_BOOL);

        if (data_submitted() and $confirm and confirm_sesskey()) {
            profile_delete_field($id);
            redirect($redirect);
        }

        //ask for confirmation
        $datacount = count_records('user_info_data', 'fieldid', $id);
        $optionsyes = array ('id'=>$id, 'confirm'=>1, 'action'=>'deletefield', 'sesskey'=>sesskey());
        admin_externalpage_print_header();
        print_heading('profiledeletefield', 'admin');
        notice_yesno(get_string('profileconfirmfielddeletion', 'admin', $datacount), $redirect, $redirect, $optionsyes, null, 'post', 'get');
        admin_externalpage_print_footer();
        die;
        break;
    case 'editfield':
        $id       = optional_param('id', 0, PARAM_INT);
        $datatype = optional_param('datatype', '', PARAM_ALPHA);

        profile_edit_field($id, $datatype, $redirect);
        die;
        break;
    case 'editcategory':
        $id = optional_param('id', 0, PARAM_INT);

        profile_edit_category($id, $redirect);
        die;
        break;
    default:
        //normal form
}

/// Print the header
admin_externalpage_print_header();
print_heading(get_string('profilefields', 'admin'));

/// Check that we have at least one category defined
if (count_records('user_info_category') == 0) {
    $defaultcategory = new object();
    $defaultcategory->name = $strdefaultcategory;
    $defaultcategory->sortorder = 1;
    insert_record('user_info_category', $defaultcategory);
    redirect($redirect);
}

/// Show all categories
$categories = get_records_select('user_info_category', '', 'sortorder ASC');

foreach ($categories as $category) {
    $table = new object();
    $table->head  = array(get_string('profilefield', 'admin'), get_string('edit'));
    $table->align = array('left', 'right');
    $table->width = '95%';
    $table->class = 'generaltable profilefield';
    $table->data = array();

    if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
        foreach ($fields as $field) {
            $table->data[] = array(format_string($field->name), profile_field_icons($field));
        }
    }

    print_heading(format_string($category->name) .' '.profile_category_icons($category));
    if (count($table->data)) {
        print_table($table);
    } else {
        notify($strnofields);
    }

} /// End of $categories foreach




echo '<hr />';
echo '<div class="profileeditor">';

/// Create a new field link
$options = profile_list_datatypes();
popup_form($CFG->wwwroot.'/user/profile/index.php?id=0&amp;action=editfield&amp;datatype=', $options, 'newfieldform','','choose','','',false,'self',$strcreatefield);

/// Create a new category link
$options = array('action'=>'editcategory');
print_single_button('index.php', $options, get_string('profilecreatecategory', 'admin'));

echo '</div>';

admin_externalpage_print_footer();
die;


/***** Some functions relevant to this script *****/

/**
 * Create a string containing the editing icons for the user profile categories
 * @param   object   the category object
 * @return  string   the icon string
 */
function profile_category_icons ($category) {
    global $CFG, $USER;

    $strdelete   = get_string('delete');
    $strmoveup   = get_string('moveup');
    $strmovedown = get_string('movedown');
    $stredit     = get_string('edit');

    $categorycount = count_records('user_info_category');
    $fieldcount    = count_records('user_info_field', 'categoryid', $category->id);

    /// Edit
    $editstr = '<a title="'.$stredit.'" href="index.php?id='.$category->id.'&amp;action=editcategory"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.$stredit.'" class="iconsmall" /></a> ';

    /// Delete
    /// Can only delete the last category if there are no fields in it
    if ( ($categorycount > 1) or ($fieldcount == 0) ) {
        $editstr .= '<a title="'.$strdelete.'" href="index.php?id='.$category->id.'&amp;action=deletecategory';
        $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.$strdelete.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move up
    if ($category->sortorder > 1) {
        $editstr .= '<a title="'.$strmoveup.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=up&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$strmoveup.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move down
    if ($category->sortorder < $categorycount) {
        $editstr .= '<a title="'.$strmovedown.'" href="index.php?id='.$category->id.'&amp;action=movecategory&amp;dir=down&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$strmovedown.'" class="iconsmall" /></a> ';
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
        $strdelete   = get_string('delete');
        $strmoveup   = get_string('moveup');
        $strmovedown = get_string('movedown');
        $stredit     = get_string('edit');
    }

    $fieldcount = count_records('user_info_field', 'categoryid',$field->categoryid);
    $datacount  = count_records('user_info_data', 'fieldid', $field->id);

    /// Edit
    $editstr = '<a title="'.$stredit.'" href="index.php?id='.$field->id.'&amp;action=editfield"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.$stredit.'" class="iconsmall" /></a> ';

    /// Delete
    $editstr .= '<a title="'.$strdelete.'" href="index.php?id='.$field->id.'&amp;action=deletefield';
    $editstr .= '"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.$strdelete.'" class="iconsmall" /></a> ';

    /// Move up
    if ($field->sortorder > 1) {
        $editstr .= '<a title="'.$strmoveup.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=up&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/up.gif" alt="'.$strmoveup.'" class="iconsmall" /></a> ';
     } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    /// Move down
    if ($field->sortorder < $fieldcount) {
        $editstr .= '<a title="'.$strmovedown.'" href="index.php?id='.$field->id.'&amp;action=movefield&amp;dir=down&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/down.gif" alt="'.$strmovedown.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$CFG->pixpath.'/spacer.gif" alt="" class="iconsmall" /> ';
    }

    return $editstr;
}


?>
