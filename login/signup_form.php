<CENTER>
<table cellpadding=20> <tr> <td bgcolor="<?=$THEME->cellheading ?>">

<form name="form" method="post" action="signup.php">
<table>
<tr valign=top>
	<td colspan=2><P><B>Create a new username and password to log in with:</td>
</tr>
<tr valign=top>
	<td><P><? print_string("username") ?>:</td>
	<td><input type="text" name="username" size=12 value="<? p($user->username) ?>">
	<? formerr($err->username) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("password") ?>:</td>
	<td><input type="text" name="password" size=12 value="<? p($user->password) ?>">
	<? formerr($err->password) ?>
	</td>
</tr>
<tr valign=top>
	<td colspan=2><BR><P><B>Please supply some information about yourself:</B><BR>(Note: your email address must be a real one)</P>
</tr>
<tr valign=top>
	<td><P><? print_string("email") ?>:</td>
	<td><input type="text" name="email" size=25 value="<? p($user->email) ?>">
	<? formerr($err->email) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("firstname") ?>:</td>
	<td><input type="text" name="firstname" size=25 value="<? p($user->firstname) ?>">
	<? formerr($err->firstname) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("lastname") ?>:</td>
	<td><input type="text" name="lastname" size=25 value="<? p($user->lastname) ?>">
	<? formerr($err->lastname) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("idnumber") ?>:</td>
	<td><input type="text" name="idnumber" size=25 value="<? p($user->idnumber) ?>"> (optional)
	<? formerr($err->idnumber) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("phone") ?>:</td>
	<td><input type="text" name="phone" size=25 value="<? p($user->phone) ?>"> (optional)
	<? formerr($err->phone) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("city") ?>:</td>
	<td><input type="text" name="city" size=25 value="<? p($user->city) ?>">
	<? formerr($err->city) ?>
	</td>
</tr>
<tr valign=top>
	<td><P><? print_string("country") ?>:</td>
	<td><? choose_from_menu ($COUNTRIES, "country", $user->country, get_string("selectacountry"), "", "") ?>
	<? formerr($err->country) ?>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="<? print_string("createaccount") ?>"></td>
</table>
</form>

</td></tr></table>

