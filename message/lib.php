<?php
/// library functions for messaging

/// todo: add some functions


function message_print_contacts() {
    global $USER;
    $contacts = get_records('message_contacts', 'userid', $USER->id, '', 'id, contactid');
    echo 'contacts';
}

function message_print_search() {
    global $USER;
    
    if ($frm = data_submitted()) {
    
        message_print_search_results($frm);
        
    } else {
    /// todo make the following queries more efficient
        if ($teachers = get_records('user_teachers', 'userid', $USER->id, '', 'id, course')) {
        
            $courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.shortname');
            $cs = '<select name="courseselect">';
            foreach ($teachers as $tcourse) {
                $cs .= "<option value=\"$tcourse->course\">".$courses[$tcourse->course]->shortname."</option>\n";
            }
            $cs .= '</select>';
        }
        
        include('search.html');
    }
}

function message_print_settings() {
    if ($frm = data_submitted()) {
        echo 'settings submitted - quick do something';
    } else {
        echo 'settings form';
    }
}

function message_print_search_results($frm) {
    if (!empty($frm->personsubmit)) {
        echo 'searched for person';
    } else if (!empty($frm->keywordssubmit)) {
        echo 'searched for keywords';
    }

    print_single_button($ME, array( 'tab' => 'search'), get_string('newsearch') );
}

?>
