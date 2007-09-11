<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");
    require( 'latex.php' );

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
 
    $param = null;
    $param->action = optional_param( 'action','',PARAM_ALPHA );
    $param->tex = optional_param( 'tex','' );

    $query = urldecode($_SERVER['QUERY_STRING']);
    error_reporting(E_ALL);
    $output = '';

    // look up in cache if required
    if ($param->action=='ShowDB' or  $param->action=='DeleteDB') {
        $md5 = md5($param->tex);
        $texcache = get_record("cache_filters","filter","tex", "md5key", $md5);
    }

    // Action: Show DB Entry
    if ($param->action=='ShowDB') {
        if ($texcache) {
            $output = "DB cache_filters entry for $param->tex\n";
            $output .= "id = $texcache->id\n";
            $output .= "filter = $texcache->filter\n";
            $output .= "version = $texcache->version\n";
            $output .= "md5key = $texcache->md5key\n";
            $output .= "rawtext = $texcache->rawtext\n";
            $output .= "timemodified = $texcache->timemodified\n";
        } else {
            $output = "DB cache_filters entry for $param->tex not found\n";
        }
    }

    // Action: Delete DB Entry
    if ($param->action=='DeleteDB') {
        if ($texcache) {
            $output = "Deleting DB cache_filters entry for $param->tex\n";
            $result =  delete_records("cache_filters","id",$texcache->id);
            if ($result) {
                $result = 1;
            } else {
                $result = 0;
            }
            $output .= "Number of records deleted = $result\n";
        } else {
            $output = "Could not delete DB cache_filters entry for $param->tex\nbecause it could not be found.\n";
        }
    }

    // Action: Show Image
    if ($param->action=='ShowImageMimetex') {
        tex2image($param->tex);
    }

    // Action: Check Slasharguments
    if ($param->action=='SlashArguments') {
        slasharguments($param->tex);
    }

    // Action: Show Tex command line output
    if ($param->action=='ShowImageTex') {
        TexOutput($param->tex, true);
        exit;
    }

    // Action: Show Tex command line output
    if ($param->action=='ShowOutputTex') {
        TexOutput($param->tex);
        exit;
    }

    if (!empty($param->action)) {   
        outputText($output);
    }

    // nothing more to do if there was any action
    if (!empty($param->action)) {
        exit;
    }


    function outputText($texexp) {
        header("Content-type: text/html");
        echo "<html><body><pre>\n";
        if ($texexp) {
            $texexp = str_replace('<','&lt;',$texexp);
            $texexp = str_replace('>','&gt;',$texexp);
            $texexp = str_replace('"','&quot;',$texexp);
            echo "$texexp\n\n";
        } else {
            echo "No text output available\n\n";
        }
        echo "</pre></body></html>\n";
    }

    function tex2image($texexp, $return=false) {
        global $CFG;
        $error_message1 = "Your system is not configured to run mimeTeX. ";
        $error_message1 .= "You need to download the appropriate<br /> executable ";
        $error_message1 .= "from <a href=\"http://moodle.org/download/mimetex/\">";
        $error_message1 .= "http://moodle.org/download/mimetex/</a>, or obtain the ";
        $error_message1 .= "C source<br /> from <a href=\"http://www.forkosh.com/mimetex.zip\">";
        $error_message1 .= "http://www.forkosh.com/mimetex.zip</a>, compile it and ";
        $error_message1 .= "put the executable into your<br /> moodle/filter/tex/ directory. ";
        $error_message1 .= "You also need to edit your moodle/filter/tex/pix.php file<br />";
        $error_message1 .= ' by adding the line<br /><pre>       case "' . PHP_OS . "\":\n";
        $error_message1 .= "           \$cmd = \"\\\\\"\$CFG->dirroot/\$CFG->texfilterdir/";
        $error_message1 .= 'mimetex.' . strtolower(PHP_OS) . "\\\\\" -e \\\\\"\$pathname\\\\\" \". escapeshellarg(\$texexp);";
        $error_message1 .= "</pre>You also need to add this to your texdebug.php file.";

        if ($texexp) {
            $texexp = '\Large ' . $texexp;
            $lifetime = 86400;
            $image  = md5($texexp) . ".gif";
            $filetype = 'image/gif';
            if (!file_exists("$CFG->dataroot/$CFG->teximagedir")) {
                make_upload_directory($CFG->teximagedir);
            }
            $pathname = "$CFG->dataroot/$CFG->teximagedir/$image";
            if (file_exists($pathname)) {
                unlink($pathname);
            }
            $commandpath = "";
            $cmd = "";
            $texexp = escapeshellarg($texexp);
            switch (PHP_OS) {
                case "Linux":
                    $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.linux";           
                    $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.linux\" -e \"$pathname\" $texexp";
                break;
                case "WINNT":
                case "WIN32":
                case "Windows":
                    $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.exe";
                    $cmd = str_replace(' ','^ ',$commandpath);
                    $cmd .= " ++ -e  \"$pathname\" $texexp";
                break;
                case "Darwin":
                    $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin";
                    $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin\" -e \"$pathname\" $texexp";
                break;
            }
            if (!$cmd) {
                if (is_executable("$CFG->dirroot/$CFG->texfilterdir/mimetex")) {   /// Use the custom binary
                    $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex";
                    $cmd = "$CFG->dirroot/$CFG->texfilterdir/mimetex -e $pathname $texexp";
                } else {
                    error($error_message1);
                }
            }
            system($cmd, $status);
        }
        if ($return) {
          return $image;
        }
        if ($texexp && file_exists($pathname)) {
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
            $ecmd = "$cmd 2>&1";
            echo `$ecmd` . "<br />\n";
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            if ($status == 4) {
                echo "Status corresponds to illegal instruction<br />\n";
            } else if ($status == 11) {
                echo "Status corresponds to bus error<br />\n";
            } else if ($status == 22) {
                echo "Status corresponds to abnormal termination<br />\n";
            }
            if (file_exists($commandpath)) {
                echo "File size of mimetex executable  $commandpath is " . filesize($commandpath) . "<br />";
                echo "The file permissions are: " . decoct(fileperms($commandpath)) . "<br />";
                if (function_exists("md5_file")) {
                    echo "The md5 checksum of the file is " . md5_file($commandpath) . "<br />";
                } else {
                    $handle = fopen($commandpath,"rb");
                    $contents = fread($handle,16384);
                    fclose($handle);
                    echo "The md5 checksum of the first 16384 bytes is " . md5($contents) . "<br />";
                }
            } else {
                echo "mimetex executable $commandpath not found!<br />";
            }
            echo "Image not found!";
        }
    }


    // test Tex/Ghostscript output - command execution only
    function TexOutput( $expression, $graphic=false ) {
        global $CFG;
        $output = '';

        $latex = new latex();

        // first check if it is likely to work at all
        $output .= "<h3>Checking executables</h3>\n";
        $executables_exist = true;
        if (is_file($CFG->filter_tex_pathlatex)) {
            $output .= "latex executable ($CFG->filter_tex_pathlatex) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> latex executable ($CFG->filter_tex_pathlatex) is not readable<br />\n";
        }
        if (is_file($CFG->filter_tex_pathdvips)) {
            $output .= "dvips executable ($CFG->filter_tex_pathdvips) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> dvips executable ($CFG->filter_tex_pathdvips) is not readable<br />\n";
        }
        if (is_file($CFG->filter_tex_pathconvert)) {
            $output .= "convert executable ($CFG->filter_tex_pathconvert) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> convert executable ($CFG->filter_tex_pathconvert) is not readable<br />\n";
        }

        // knowing that it might work.. 
        $md5 = md5( $expression );
        $output .= "<p>base filename for expression is '$md5'</p>\n";
  
        // temporary paths
        $tex = "$latex->temp_dir/$md5.tex";
        $dvi = "$latex->temp_dir/$md5.dvi";
        $ps = "$latex->temp_dir/$md5.ps";
        $gif = "$latex->temp_dir/$md5.gif";

        // put the expression as a file into the temp area
        $expression = stripslashes( $expression );
        $expression = html_entity_decode( $expression );
        $output .= "<p>Processing TeX expression:</p><pre>$expression</pre>\n";
        $doc = $latex->construct_latex_document( $expression );
        $fh = fopen( $tex, 'w' );
        fputs( $fh, $doc );
        fclose( $fh );

        // cd to temp dir
        chdir( $latex->temp_dir );

        // step 1: latex command
        $cmd = "$CFG->filter_tex_pathlatex --interaction=nonstopmode $tex";
        $output .= execute( $cmd );

        // step 2: dvips command
        $cmd = "$CFG->filter_tex_pathdvips -E $dvi -o $ps";
        $output .= execute( $cmd );

        // step 3: convert command
        $cmd = "$CFG->filter_tex_pathconvert -density 240 -trim $ps $gif ";
        $output .= execute( $cmd );

        if (!$graphic) {
            echo( $output );
        } else {
            $lastmodified = filemtime($gif);
            $lifetime = 86400;
            $filetype = 'image/gif';
            $image = "$md5.gif";
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
            header("Cache-control: max_age = $lifetime"); // a day
            header("Pragma: ");
            header("Content-disposition: inline; filename=$image");
            header("Content-length: ".filesize($gif));
            header("Content-type: $filetype");
            readfile("$gif");
         }
    }

    function execute( $cmd ) {
        exec( $cmd, $result, $code );
        $output = "<pre>$ $cmd\n";
        $lines = implode( "\n", $result );
        $output .= "OUTPUT: $lines\n";
        $output .= "RETURN CODE: $code\n</pre>\n";
        return $output;
    }

    function slasharguments($texexp) {
        global $CFG;
        $admin = $CFG->wwwroot . '/' . $CFG->admin . '/config.php';
        $image = tex2image($texexp,true);
        echo "<p>If the following image displays correctly, set your ";
        echo "<a href=\"$admin\" target=\"_blank\">Administration->Configuration->Variables</a> ";
        echo "setting for slasharguments to file.php/1/pic.jpg: ";
        echo "<img src=\"pix.php/$image\" align=\"absmiddle\"></p>\n";
        echo "<p>Otherwise set it to file.php?file=/1/pic.jpg ";
        echo "It should display correctly as ";
        echo "<img src=\"pix.php?file=$image\" align=\"absmiddle\"></p>\n";
        echo "<p>If neither equation image displays correctly, please seek ";
        echo "further help at moodle.org at the ";
        echo "<a href=\"http://moodle.org/mod/forum/view.php?id=752&username=guest\" target=\"_blank\">";
        echo "Mathematics Tools Forum</a></p>";
    }

?>

<html>
<head><title>TeX Filter Debugger</title></head>
<body>
  <p>Please enter an algebraic expression <b>without</b> any surrounding $$ into
       the text box below. (Click <a href="#help">here for help.</a>)
          <form action="texdebug.php" method="get"
           target="inlineframe">
            <center>
             <input type="text" name="tex" size="50"
                    value="f(x)=\Bigint_{-\infty}^x~e^{-t^2}dt" />
            </center>
           <p>The following tests are available:</p>
           <ol>
           <li><input type="radio" name="action" value="ShowDB" />
               See the cache_filters database entry for this expression (if any).</li>
           <li><input type="radio" name="DeleteDB" value="DeleteDB" />
               Delete the cache_filters database entry for this expression (if any).</li>
           <li><input type="radio" name="action" value="ShowImageMimetex" />
               Show a graphic image of the algebraic expression rendered with mimetex.</li>
           <li><input type="radio" name="action" value="ShowImageTex" />
               Show a graphic image of the algebraic expression rendered with Tex/Ghostscript.</li>
           <li><input type="radio" name="action" value="ShowOutputTex" />
               Show command execution output from the algebraic expression rendered with Tex/Ghostscript.</li>
           <li><input type="radio" name="action" value="SlashArguments" />
               Check slasharguments setting.</li>
           </ol>
           <input type="submit" value="Do it!" />
          </form> <br /> <br />
       <center>
          <iframe name="inlineframe" align="middle" width="80%" height="200">
          &lt;p&gt;Something is wrong...&lt;/p&gt; 
          </iframe>
       </center> <br />
<hr />
<a name="help">
<h2>Debugging Help</h2>
</a>
<p>First a brief overview of how the TeX filter works. The TeX filter first
searches the database cache_filters table to see if this TeX expression had been
processed before. If not, it adds a DB entry for that expression.  It then
replaces the TeX expression by an &lt;img src=&quot;.../filter/tex/pix.php...&quot;&gt;
tag.  The filter/tex/pix.php script then searches the database to find an
appropriate gif image file for that expression and to create one if it doesn't exist.
It will then use either the LaTex/Ghostscript renderer (using external executables
on your system) or the bundled Mimetex executable. The full Latex/Ghostscript
renderer produces better results and is tried first. 
Here are a few common things that can go wrong and some suggestions on how
you might try to fix them.</p>
<ol>
<li>Something had gone wrong on a previous occasion when the filter tried to
process this expression. Then the database entry for that expression contains
a bad TeX expression in the rawtext field (usually blank). You can fix this
by clicking on &quot;Delete DB Entry&quot;</li>
<li>The TeX to gif image conversion process does not work. 
If paths are specified in the filter configuation screen for the three
executables these will be tried first. Note that they still must be correctly
installed and have the correct permissions. In particular make sure that you 
have all the packages installed (e.g., on Debian/Ubuntu you need to install
the 'tetex-extra' package). Running the 'show command execution' test should
give a big clue.
If this fails or is not available, the Mimetex executable is tried. If this 
fails a likely cause is that the mimetex binary you are using is
incompatible with your operating system. You can try compiling it from the
C sources downloaded from <a href="http://www.forkosh.com/mimetex.zip">
http://www.forkosh.com/mimetex.zip</a>, or looking for an appropriate
binary at <a href="http://moodle.org/download/mimetex/">
http://moodle.org/download/mimetex/</a>. You may then also need to
edit your moodle/filter/tex/pix.php file to add 
<br /><?PHP echo "case &quot;" . PHP_OS . "&quot;:" ;?><br ?> to the list of operating systems
in the switch (PHP_OS) statement. Windows users may have a problem properly
unzipping mimetex.exe. Make sure that mimetex.exe is is <b>PRECISELY</b>
433152 bytes in size. If not, download a fresh copy from
<a href="http://moodle.org/download/mimetex/windows/mimetex.exe">
http://moodle.org/download/mimetex/windows/mimetex.exe</a>. 
Another possible problem which may affect
both Unix and Windows servers is that the web server doesn't have execute permission
on the mimetex binary. In that case change permissions accordingly</li>
</ol>
</body>
</html>
