<?PHP  // $Id$
       // manual method - Just does a simple check against the database

function auth_user_login ($username, $password) {
// Returns false if the username doesn't exist yet
// Returns true if the username and password work

    if ($user = get_user_info_from_db("username", $username)) {
        return ($user->password == md5($password));
    }

    return false;
}


?>
