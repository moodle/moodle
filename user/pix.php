<?PHP // $Id$
      // This function fetches user pictures from the data directory
      // Syntax:   pix.php/userid/f1.jpg or pix.php/userid/f2.jpg
      //     OR:   ?file=userid/f1.jpg or ?file=userid/f2.jpg

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('pix.php');

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 2) {
        $userid   = (integer)$args[0];
        $image    = $args[1];
        $pathname = make_user_directory($userid, true) . "/$image";
        if (file_exists($pathname) and !is_dir($pathname)) {
            send_file($pathname, $image);
        }
    }

    // picture was deleted - use default instead
    redirect($CFG->pixpath.'/u/f1.png');
?>
