<?PHP // $Id$
      // This function fetches user pictures from the data directory
      // Syntax:   pix.php/userid/f1.jpg or pix.php/userid/f2.jpg
      //     OR:   ?file=userid/f1.jpg or ?file=userid/f2.jpg

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once('../config.php');
    require_once('../files/mimetypes.php');

    $relativepath = get_file_argument('pix.php');

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 2) {
        $userid   = (integer)$args[0];
        $image    = $args[1];
        $pathname = $CFG->dataroot.'/users/'.$userid.'/'.$image;
    } else {
        $image    = 'f1.png';
        $pathname = $CFG->dirroot.'/pix/u/f1.png';
    }

    if (file_exists($pathname) and !is_dir($pathname)) {
        send_file($pathname, $image);
    } else {
        header('HTTP/1.0 404 not found');
        error(get_string('filenotfound', 'error')); //this is not displayed on IIS??
    }
?>
