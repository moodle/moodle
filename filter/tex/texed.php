<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");
    require_once($CFG->dirroot.'/filter/tex/lib.php');

    if (empty($CFG->textfilters)) {
        error ('Filter not enabled!');
    } else {
        $filters = explode(',', $CFG->textfilters);
        if (array_search('filter/tex', $filters) === FALSE) {
            error ('Filter not enabled!');
        }
    }

    error_reporting(E_ALL);
    $texexp = urldecode($_SERVER['QUERY_STRING']);
    $texexp = str_replace('formdata=','',$texexp);

    if ($texexp) {
        $image  = md5($texexp) . ".gif";
        $filetype = 'image/gif';
        if (!file_exists("$CFG->dataroot/filter/tex")) {
            make_upload_directory("filter/tex");
        }

        $texexp = str_replace('&lt;','<',$texexp);
        $texexp = str_replace('&gt;','>',$texexp);
        $texexp = preg_replace('!\r\n?!',' ',$texexp);
        $pathname = "$CFG->dataroot/filter/tex/$image";
        $cmd = tex_filter_get_cmd($pathname, $texexp);
        system($cmd, $status);

        if (file_exists($pathname)) {
            require_once($CFG->libdir . '/filelib.php');
            send_file($pathname, $image);
        } else {
            echo "Image not found!";
        }
        exit;
    } else {
        echo "No tex expresion specified";
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
                    value="\Large f(x)=\Bigint_{-\infty}^x~e^{-t^2}dt" />
             <input type="submit" />
          </form> <br /> <br />
          <iframe name="inlineframe" align="middle" width="80%" height="100">
          &lt;p&gt;Something is wrong...&lt;/p&gt;
          </iframe>
       </center> <br />
</body>
</html>
