<CENTER>
<table cellpadding=20> <tr> <td bgcolor="<?=$THEME->cellheading ?>">

<form name="form" method="post" action="signup.php">
<table>
<tr valign=top>
	<td colspan=2><P><B>Create a new username and password to log in with:</td>
</tr>
<tr valign=top>
	<td><P>New username:</td>
	<td><input type="text" name="username" size=12 value="<? pv($user->username) ?>">
	<? formerr($err->username) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>New password:</td>
	<td><input type="text" name="password" size=12 value="<? pv($user->password) ?>">
	<? formerr($err->password) ?>
	</td>
</tr>
<tr valign=top>
	<td colspan=2><BR><P><B>Please supply some information about yourself:</B><BR>(Note: your email address must be a real one)</P>
</tr>
<tr valign=top>
	<td><P>Email address:</td>
	<td><input type="text" name="email" size=25 value="<? pv($user->email) ?>">
	<? formerr($err->email) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>First name:</td>
	<td><input type="text" name="firstname" size=25 value="<? pv($user->firstname) ?>">
	<? formerr($err->firstname) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>Last name:</td>
	<td><input type="text" name="lastname" size=25 value="<? pv($user->lastname) ?>">
	<? formerr($err->lastname) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>ID Number:</td>
	<td><input type="text" name="idnumber" size=25 value="<? pv($user->idnumber) ?>"> (optional)
	<? formerr($err->idnumber) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>Phone number:</td>
	<td><input type="text" name="phone" size=25 value="<? pv($user->phone) ?>"> (optional)
	<? formerr($err->phone) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>City or town:</td>
	<td><input type="text" name="city" size=25 value="<? pv($user->city) ?>">
	<? formerr($err->city) ?>
	</td>
</tr>
<tr valign=top>
	<td><P>Country:</td>
	<td><? choose_from_menu ($COUNTRIES, "country", $user->country, "Select a country...", "", "") ?>
	<? formerr($err->country) ?>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Create my new account"></td>
</table>
</form>

</td></tr></table>

<HR>
<CENTER>
<P><A HREF="<?=$CFG->wwwroot ?>">Home</A></P>

