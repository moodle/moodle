<?PHP  // $Id$
       // No authentication at all.  This method approves everything!

function auth_user_login ($username, $password) {
// Returns true if the username doesn't exist yet
// Returns true if the username and password work

    if (! $user = get_user_info_from_db("username", $username)) {
        return true;
    }

    return ($user->password == md5($password));
}



?>
