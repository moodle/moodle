<?php
/// main interface window for messaging

require('../config.php');
require('lib.php');

$tab = optional_param('tab', 'contacts');
$ftab = 'message_print_'.$tab;





print_header(get_string('messaging').' - '.$SITE->fullname);

?>

<table cellspacing="2" cellpadding="2" border="0" bgcolor="#ffffff" align="center" width="80%">
<tr>
    <th class="message_tab_selected"><a href="<?php echo $ME ?>?tab=contacts">contacts</a></th>
    <th class="message_tab"><a href="<?php echo $ME ?>?tab=search">search</a></th>
    <th class="message_tab"><a href="<?php echo $ME ?>?tab=settings">settings</a></th>
</tr>
<tr>
    <td colspan="3">
        <?php if (function_exists($ftab)) $ftab(); ?>
    </td>
</tr>
</table>


</body>
</html>
