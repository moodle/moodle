<?php
/// main interface window for messaging

require('../config.php');
require('lib.php');

require_login(0, false);

/// optional variables that may be passed in
$tab            = optional_param('tab',       'contacts'); // current tab - default to contacts
$addcontact     = optional_param('addcontact',     false); // adding a contact
$removecontact  = optional_param('removecontact',  false); // removing a contact
$blockcontact   = optional_param('blockcontact',   false); // blocking a contact
$unblockcontact = optional_param('unblockcontact', false); // unblocking a contact

if (($addcontact     !== false) and confirm_sesskey()) message_add_contact($addcontact);
if (($removecontact  !== false) and confirm_sesskey()) message_remove_contact($removecontact);
if (($blockcontact   !== false) and confirm_sesskey()) message_block_contact($blockcontact);
if (($unblockcontact !== false) and confirm_sesskey()) message_unblock_contact($unblockcontact);


/// a print function is associated with each tab
$tabprintfunction = 'message_print_'.$tab;


print_header(get_string('messages', 'message').' - '.$SITE->fullname);

?>

<table cellspacing="2" cellpadding="2" border="0" align="center" width="95%">
<tr>
    <th class="<?php echo ($tab == 'contacts') ? 'generaltabselected' : 'generaltab' ?>">
        <a href="<?php echo $ME ?>?tab=contacts">contacts</a>
    </th>
    <th class="<?php echo ($tab == 'search') ? 'generaltabselected' : 'generaltab' ?>">
        <a href="<?php echo $ME ?>?tab=search">search</a>
    </th>
    <th class="<?php echo ($tab == 'settings') ? 'generaltabselected' : 'generaltab' ?>">
        <a href="<?php echo $ME ?>?tab=settings">settings</a>
    </th>
</tr>
<tr>
    <td colspan="3" bgcolor="#ffffff">
        <?php if (function_exists($tabprintfunction)) $tabprintfunction(); ?>
    </td>
</tr>
</table>


</body>
</html>
