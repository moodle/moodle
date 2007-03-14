<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");

    if (empty($CFG->textfilters)) {
        error ('Filter not enabled!');
    } else {
        $filters = explode(',', $CFG->textfilters);
        if (array_search('filter/tex', $filters) === FALSE) {
            error ('Filter not enabled!');
        }
    }

    $CFG->texfilterdir = "filter/tex";
    $CFG->teximagedir = "filter/tex";

    error_reporting(E_ALL);
    $texexp = urldecode($_SERVER['QUERY_STRING']);
    $texexp = str_replace('formdata=','',$texexp);

    if ($texexp) {
        //$texexp = stripslashes($texexp);
        $lifetime = 86400;
        $image  = md5($texexp) . ".gif";
        $filetype = 'image/gif';
        if (!file_exists("$CFG->dataroot/$CFG->teximagedir")) {
            make_upload_directory($CFG->teximagedir);
        }
        $pathname = "$CFG->dataroot/$CFG->teximagedir/$image";
        $texexp = escapeshellarg($texexp);

        switch (PHP_OS) {
            case "Linux":
                system("$CFG->dirroot/$CFG->texfilterdir/mimetex.linux -e $pathname -- $texexp" );
            break;
            case "WINNT":
            case "WIN32":
            case "Windows":
                system("$CFG->dirroot/$CFG->texfilterdir/mimetex.exe -e  $pathname -- $texexp");
            break;
            case "Darwin":
                system("$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin -e $pathname -- $texexp" );
            break;
        }
        if (file_exists($pathname)) {
            $lastmodified = filemtime($pathname);
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
            header("Cache-control: max_age = $lifetime"); // a day
            header("Pragma: ");
            header("Content-disposition: inline; filename=$image");
            header("Content-length: ".filesize($pathname));
            header("Content-type: $filetype");
            readfile("$pathname");
        } else {
            echo "Image not found!";
        }
        exit;
    }

?>

<html>
<head><title>mimeTeX Previewer</title></head>
<body>
    <p>Now enter your own expression or use the sample provided,
     press the Submit button, and mimeTeX's rendering should be
     displayed in the little window immediately below it.
       <center>
          <form action="texed.php" method="get"
           target="inlineframe">
             <input type="text" name="formdata" size="50"
                    value="\Large f(x)=\Bigint_{-\infty}^x~e^{-t^2}dt">
             <input type="submit">
          </form> <br /> <br />
          <iframe name="inlineframe" align="middle" width="80%" height="100">
          &lt;p&gt;Something is wrong...&lt;/p&gt; 
          </iframe>
       </center> <br />
</body>
</html>
