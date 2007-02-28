<?php /// $Id$
       /// Main interface window for messaging

    require('../config.php');
    require('lib.php');

    require_login(0, false);

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

/// Optional variables that may be passed in
    $tab            = optional_param('tab', 'contacts'); // current tab - default to contacts
    $addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
    $removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
    $blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
    $unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact
    $popup          = optional_param('popup', false, PARAM_ALPHA);    // If set then starts a new popup window

/// Popup a window if required and quit (usually from external links).
    if ($popup) {
        print_header();
        echo '<script type="text/javascript">'."\n//<![CDATA[\n openpopup('/message/index.php', 'message', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\n//]]>\n</script>";
        redirect("$CFG->wwwroot/");
        exit;
    }

/// Process any contact maintenance requests there may be
    if ($addcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'add contact', 'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
        message_add_contact($addcontact);
    }
    if ($removecontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'remove contact', 'history.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
        message_remove_contact($removecontact);
    }
    if ($blockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'block contact', 'history.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
        message_block_contact($blockcontact);
    }
    if ($unblockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'unblock contact', 'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
        message_unblock_contact($unblockcontact);
    }


/// Header on this page
    if ($tab == 'contacts') {
        print_header(get_string('messages', 'message').' - '.format_string($SITE->fullname), '', '', '', 
                '<meta http-equiv="refresh" content="'. $CFG->message_contacts_refresh .'; url=index.php" />');
    } else {
        print_header(get_string('messages', 'message').' - '.format_string($SITE->fullname));
    }

    echo '<table cellspacing="2" cellpadding="2" border="0" align="center" width="95%">';
    echo '<tr>';

/// Print out the tabs
    echo '<td>';
    $tabrow = array();
    $tabrow[] = new tabobject('contacts', $CFG->wwwroot.'/message/index.php?tab=contacts', 
                               get_string('contacts', 'message'));
    $tabrow[] = new tabobject('search', $CFG->wwwroot.'/message/index.php?tab=search', 
                               get_string('search', 'message'));
    $tabrow[] = new tabobject('settings', $CFG->wwwroot.'/message/index.php?tab=settings', 
                               get_string('settings', 'message'));
    $tabrows = array($tabrow);

    print_tabs($tabrows, $tab);
    
    echo '</td>';


    echo '</tr><tr>';

/// Print out contents of the tab
    echo '<td>';

/// a print function is associated with each tab
    $tabprintfunction = 'message_print_'.$tab;
    if (function_exists($tabprintfunction)) {
        $tabprintfunction();
    }

    echo '</td> </tr> </table>';
    print_footer('none');

?>
