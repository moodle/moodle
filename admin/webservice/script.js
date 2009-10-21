
/* This function disable the valid until field of a user into service_users.php*/
function external_disablevaliduntil(event, userid) {
   var disabled;
   if (document.getElementById('enablevaliduntil'+userid).checked) {
       disabled = false;
   } else {
       disabled = true;
   }
   document.getElementById('menufromday'+userid).disabled = disabled;
   document.getElementById('menufromyear'+userid).disabled = disabled;
   document.getElementById('menufrommonth'+userid).disabled = disabled;
}