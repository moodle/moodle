<CENTER>
<table cellpadding=20> <tr> <td bgcolor="<?=$THEME->cellcontent2 ?>" class="generalbox">

<form name="form" method="post" action="signup.php">
<table>
<tr valign=top>
	<td colspan=2><P><B><? print_string("createuserandpass") ?>:</td>
</tr>
<tr valign=top>
	<td><P><? print_string("username") ?>:</td>
	<td><input type="text" name="username" size=12 value="<? p($user->username) ?>">
	<? if (!empty($err->username)) { formerr($err->username); } ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("password") ?>:</td>
	<td><input type="password" name="password" size=12 value="<? p($user->password) ?>">
	<? if (!empty($err->password)) { formerr($err->password); } ?>
	</td>
</tr>
<tr valign=top>
	<td colspan=2><P><BR><B><? print_string("supplyinfo") ?>:</B><BR>(<? print_string("emailmustbereal") ?>)</P>
</tr>
<tr valign=top>
	<td><P><? print_string("email") ?>:</td>
	<td><input type="text" name="email" size=25 value="<? p($user->email) ?>">
	<? if (!empty($err->email)) { formerr($err->email); } ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("firstname") ?>:</td>
	<td><input type="text" name="firstname" size=25 value="<? p($user->firstname) ?>">
	<? if (!empty($err->firstname)) { formerr($err->firstname);} ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("lastname") ?>:</td>
	<td><input type="text" name="lastname" size=25 value="<? p($user->lastname) ?>">
	<? if (!empty($err->lastname)) { formerr($err->lastname);} ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("idnumber") ?>:</td>
	<td><input type="text" name="idnumber" size=25 value="<? p($user->idnumber) ?>"> (<? print_string("optional") ?>)
	<? if (!empty($err->idnumber)) { formerr($err->idnumber);} ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("phone") ?>:</td>
	<td><input type="text" name="phone" size=25 value="<? p($user->phone) ?>"> (<? print_string("optional") ?>)
	<? if (!empty($err->phone)) { formerr($err->phone);} ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("city") ?>:</td>
	<td><input type="text" name="city" size=25 value="<? p($user->city) ?>">
	<? if (!empty($err->city)) { formerr($err->city);} ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("country") ?>:</td>
	<td><? choose_from_menu ($COUNTRIES, "country", $user->country, get_string("selectacountry"), "", "") ?>
	<? if (!empty($err->country)) { formerr($err->country);} ?>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="<? print_string("createaccount") ?>"></td>
</table>
</form>

</td></tr></table>

