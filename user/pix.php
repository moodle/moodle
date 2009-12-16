<?PHP
      // This function fetches user pictures from the data directory
      // Syntax:   pix.php/userid/f1.jpg or pix.php/userid/f2.jpg
      //     OR:   ?file=userid/f1.jpg or ?file=userid/f2.jpg

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');

    if (!empty($CFG->forcelogin) and !isloggedin()) {
        // protect images if login required and not logged in;
        // do not use require_login() because it is expensive and not suitable here anyway
        redirect($OUTPUT->pix_url('u/f1'));
    }

    $relativepath = get_file_argument();

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 2) {
        $userid   = (integer)$args[0];
        // do not serve images of deleted users
        if ($user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'picture'=>1))) {
            $image    = $args[1];
            $pathname = make_user_directory($userid, true) . "/$image";
            if (file_exists($pathname) and !is_dir($pathname)) {
                send_file($pathname, $image);
            }
        }
    }

    // picture was deleted - use default instead
    redirect($OUTPUT->pix_url('u/f1'));
