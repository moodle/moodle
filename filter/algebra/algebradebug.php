<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");


    $CFG->texfilterdir = "filter/tex";
    $CFG->algebrafilterdir = "filter/algebra";
    $CFG->algebraimagedir = "filter/algebra";

    $query = urldecode($_SERVER['QUERY_STRING']);
    error_reporting(E_ALL);

    if ($query) {
      $output = $query;
      $splitpos = strpos($query,'&')-8;
      $algebra = substr($query,8,$splitpos);
      $md5 = md5($algebra);
      if (strpos($query,'ShowDB') || strpos($query,'DeleteDB')) {
	$texcache = get_record("cache_filters","filter","algebra", "md5key", $md5);
      }
      if (strpos($query,'ShowDB')) {
        if ($texcache) {
	  $output = "DB cache_filters entry for $algebra\n";
          $output .= "id = $texcache->id\n";
          $output .= "filter = $texcache->filter\n";
          $output .= "version = $texcache->version\n";
          $output .= "md5key = $texcache->md5key\n";
          $output .= "rawtext = $texcache->rawtext\n";
          $output .= "timemodified = $texcache->timemodified\n";
        } else {
          $output = "DB cache_filters entry for $algebra not found\n";
        }
      }
      if (strpos($query,'DeleteDB')) {
        if ($texcache) {
          $output = "Deleting DB cache_filters entry for $algebra\n";
          $result =  delete_records("cache_filters","id",$texcache->id);
          if ($result) {
	    $result = 1;
          } else {
            $result = 0;
          }
          $output .= "Number of records deleted = $result\n";
        } else {
          $output = "Could not delete DB cache_filters entry for $algebra\nbecause it could not be found.\n";
        }
      }
      if (strpos($query,'TeXStage1')) {
	$output = algebra2tex($algebra);
      }
      if (strpos($query,'TeXStage2')) {
	$output = algebra2tex($algebra);
        $output = refineTeX($output);
      }
      if (strpos($query,'ShowImage')) {
	$output = algebra2tex($algebra);
        $output = refineTeX($output);
        tex2image($output, $md5);
      } else {   
        outputText($output);
      }
      exit;
    }

function algebra2tex($algebra) {
  Global $CFG;
  $algebra = str_replace('&lt;','<',$algebra);
  $algebra = str_replace('&gt;','>',$algebra);
  $algebra = str_replace('<>','#',$algebra);
  $algebra = str_replace('<=','%',$algebra);
  $algebra = str_replace('>=','!',$algebra);
  $algebra = preg_replace('/([=><%!#] *)-/',"\$1 zeroplace -",$algebra);
  $algebra = str_replace('delta','zdelta',$algebra);
  $algebra = str_replace('beta','bita',$algebra);
  $algebra = str_replace('theta','thita',$algebra);
  $algebra = str_replace('zeta','zita',$algebra);
  $algebra = str_replace('eta','xeta',$algebra);
  $algebra = str_replace('epsilon','zepslon',$algebra);
  $algebra = str_replace('upsilon','zupslon',$algebra);
  $algebra = preg_replace('!\r\n?!',' ',$algebra);

  if ( (PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows") ) {
    $algebra = "\"". str_replace('"','\"',$algebra) . "\"";
    $cmd  = "cd $CFG->dirroot/$CFG->algebrafilterdir & algebra2tex.pl $algebra";
  } else {      
    $algebra = escapeshellarg($algebra);
    $cmd  = "cd $CFG->dirroot/$CFG->algebrafilterdir; ./algebra2tex.pl $algebra";
  }
  $texexp = `$cmd`;
  return $texexp;
}

function refineTeX($texexp) {
  $texexp = str_replace('zeroplace','',$texexp);
  $texexp = str_replace('#','\not= ',$texexp);
  $texexp = str_replace('%','\leq ',$texexp);
  $texexp = str_replace('!','\geq ',$texexp);
  $texexp = str_replace('\left{','{',$texexp);
  $texexp = str_replace('\right}','}',$texexp);
  $texexp = str_replace('\fun',' ',$texexp);
  $texexp = str_replace('infty','\infty',$texexp);
  $texexp = str_replace('alpha','\alpha',$texexp);
  $texexp = str_replace('gamma','\gamma',$texexp);
  $texexp = str_replace('iota','\iota',$texexp);
  $texexp = str_replace('kappa','\kappa',$texexp);
  $texexp = str_replace('lambda','\lambda',$texexp);
  $texexp = str_replace('mu','\mu',$texexp);
  $texexp = str_replace('nu','\nu',$texexp);
  $texexp = str_replace('xi','\xi',$texexp);
  $texexp = str_replace('rho','\rho',$texexp);
  $texexp = str_replace('sigma','\sigma',$texexp);
  $texexp = str_replace('tau','\tau',$texexp);
  $texexp = str_replace('phi','\phi',$texexp);
  $texexp = str_replace('chi','\chi',$texexp);
  $texexp = str_replace('psi','\psi',$texexp);
  $texexp = str_replace('omega','\omega',$texexp);
  $texexp = str_replace('zdelta','\delta',$texexp);
  $texexp = str_replace('bita','\beta',$texexp);
  $texexp = str_replace('thita','\theta',$texexp);
  $texexp = str_replace('zita','\zeta',$texexp);
  $texexp = str_replace('xeta','\eta',$texexp);
  $texexp = str_replace('zepslon','\epsilon',$texexp);
  $texexp = str_replace('zupslon','\upsilon',$texexp);
  $texexp = str_replace('\mbox{logten}','\mbox{log}_{10}',$texexp);
  $texexp = str_replace('\mbox{acos}','\mbox{cos}^{-1}',$texexp);
  $texexp = str_replace('\mbox{asin}','\mbox{sin}^{-1}',$texexp);
  $texexp = str_replace('\mbox{atan}','\mbox{tan}^{-1}',$texexp);
  $texexp = str_replace('\mbox{asec}','\mbox{sec}^{-1}',$texexp);
  $texexp = str_replace('\mbox{acsc}','\mbox{csc}^{-1}',$texexp);
  $texexp = str_replace('\mbox{acot}','\mbox{cot}^{-1}',$texexp);
  $texexp = str_replace('\mbox{acosh}','\mbox{cosh}^{-1}',$texexp);
  $texexp = str_replace('\mbox{asinh}','\mbox{sinh}^{-1}',$texexp);
  $texexp = str_replace('\mbox{atanh}','\mbox{tanh}^{-1}',$texexp);
  $texexp = str_replace('\mbox{asech}','\mbox{sech}^{-1}',$texexp);
  $texexp = str_replace('\mbox{acsch}','\mbox{csch}^{-1}',$texexp);
  $texexp = str_replace('\mbox{acoth}','\mbox{coth}^{-1}',$texexp);
  $texexp = preg_replace('/\\\sqrt{(.+?),(.+?)}/s','\sqrt['. "\$2]{\$1}",$texexp);
  $texexp = preg_replace('/\\\mbox{abs}\\\left\((.+?)\\\right\)/s',"|\$1|",$texexp);
  $texexp = preg_replace('/\\\log\\\left\((.+?),(.+?)\\\right\)/s','\log_{'. "\$2}\\left(\$1\\right)",$texexp);
  $texexp = preg_replace('/(\\\cos|\\\sin|\\\tan|\\\sec|\\\csc|\\\cot)([h]*)\\\left\((.+?),(.+?)\\\right\)/s',"\$1\$2^{". "\$4}\\left(\$3\\right)",$texexp);
  $texexp = preg_replace('/\\\int\\\left\((.+?),(.+?),(.+?)\\\right\)/s','\int_'. "{\$2}^{\$3}\$1 ",$texexp);
  $texexp = preg_replace('/\\\int\\\left\((.+?d[a-z])\\\right\)/s','\int '. "\$1 ",$texexp);
  $texexp = preg_replace('/\\\lim\\\left\((.+?),(.+?),(.+?)\\\right\)/s','\lim_'. "{\$2\\to \$3}\$1 ",$texexp);
  return $texexp;
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

function tex2image($texexp, $md5) {
  global $CFG;
  $error_message1 = "Your system is not configured to run mimeTeX. ";
  $error_message1 .= "You need to download the appropriate<br> executable ";
  $error_message1 .= "from <a href=\"http://moodle.org/download/mimetex/\">";
  $error_message1 .= "http://moodle.org/download/mimetex/</a>, or obtain the ";
  $error_message1 .= "C source<br> from <a href=\"http://www.forkosh.com/mimetex.zip\">";
  $error_message1 .= "http://www.forkosh.com/mimetex.zip</a>, compile it and ";
  $error_message1 .= "put the executable into your<br> moodle/filter/tex/ directory. ";
  $error_message1 .= "You also need to edit your moodle/filter/algebra/pix.php file<br>";
  $error_message1 .= ' by adding the line<br><pre>       case "' . PHP_OS . "\":\n";
  $error_message1 .= "           \$cmd = \"\\\\\"\$CFG->dirroot/\$CFG->texfilterdir/";
  $error_message1 .= 'mimetex.' . strtolower(PHP_OS) . "\\\\\" -e \\\\\"\$pathname\\\\\" \". escapeshellarg(\$texexp);";
  $error_message1 .= "</pre>You also need to add this to your algebradebug.php file.";

  if ($texexp) {
       $texexp = '\Large ' . $texexp;
       $lifetime = 86400;
       $image  = $md5 . ".gif";
       $filetype = 'image/gif';
       if (!file_exists("$CFG->dataroot/$CFG->algebraimagedir")) {
          make_upload_directory($CFG->algebraimagedir);
       }
       $pathname = "$CFG->dataroot/$CFG->algebraimagedir/$image";
       if (file_exists($pathname)) {
	 unlink($pathname);
       } 
       $commandpath = "";
       $cmd = "";
         switch (PHP_OS) {
       case "Linux":
         $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.linux";
         $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.linux\" -e \"$pathname\" ". escapeshellarg($texexp);
       break;
       case "WINNT":
       case "WIN32":
       case "Windows":
	 $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.exe";
	 $texexp = str_replace('"','\"',$texexp);
	 $cmd = str_replace(' ','^ ',$commandpath);
	 $cmd .= " ++ -e  \"$pathname\" \"$texexp\"";
       break;
       case "Darwin":
	 $commandpath="$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin";
         $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin\" -e \"$pathname\" ". escapeshellarg($texexp);
       break;
       }
       if (!$cmd) {
	   error($error_message1);
       }
       system($cmd, $status);
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
           echo `$ecmd` . "<br>\n";
           echo "The shell command<br>$cmd<br>returned status = $status<br>\n";
           if ($status == 4) {
             echo "Status corresponds to illegal instruction<br>\n";
           } else if ($status == 11) {
             echo "Status corresponds to bus error<br>\n";
           } else if ($status == 22) {
             echo "Status corresponds to abnormal termination<br>\n";
           }
           if (file_exists($commandpath)) {
              echo "File size of mimetex executable  $commandpath is " . filesize($commandpath) . "<br>";
              echo "The file permissions are: " . decoct(fileperms($commandpath)) . "<br>";
              if (function_exists("md5_file")) {
                echo "The md5 checksum of the file is " . md5_file($commandpath) . "<br>";
              } else {
                $handle = fopen($commandpath,"rb");
                $contents = fread($handle,16384);
                fclose($handle);
                echo "The md5 checksum of the first 16384 bytes is " . md5($contents) . "<br>";
              }
           } else {
              echo "mimetex executable $commandpath not found!<br>";
           }
           echo "Image not found!";
   }
}
?>

<html>
<head><title>Algebra Filter Debugger</title></head>
<body>
    <p>Please enter an algebraic expression <b>without</b> any surrounding @@ into
       the text box below. (Click <a href="#help">here for help.</a>)
          <form action="algebradebug.php" method="get"
           target="inlineframe">
            <center>
             <input type="text" name="algebra" size=50
                    value="sin(z)/(x^2+y^2)">
            </center>
           <ol>
           <li>First click on this button <input type="submit" name="ShowDB" value="Show DB Entry">
               to see the cache_filters database entry for this expression.</li>
	   <li>If the database entry looks corrupt, click on this button to delete it:
               <input type="submit" name="DeleteDB" value="Delete DB Entry"></li>
           <li>Now click on this button <input type="submit" name="TeXStage1" value="First Stage Tex Translation">.
               A preliminary translation into TeX will appear in the box below.</li>
           <li>Next click on this button <input type="submit" name="TeXStage2" value="Second Stage Tex Translation">.
               A more refined translation into TeX will appear in the box below.</li>
           <li>Finally click on this button <input type="submit" name="ShowImage" value="Show Image">
               to show a graphic image of the algebraic expression.</li>
           </ol>
          </form> <br> <br>
       <center>
          <iframe name="inlineframe" align="middle" width="80%" height="200">
          &lt;p&gt;Something is wrong...&lt;/p&gt; 
          </iframe>
       </center> <br>
<hr>
<a name="help">
<h2>Debugging Help</h2>
</a>
<p>First here is a brief overview on how the algebra filter works. It
takes an algebra expression and first translates it into TeX.  It first
looks for the TeX translation in the Moodle database in the table cache_filters
in the field rawtext. If not found, it passes the algebraic expression to the
Perl script algebra2tex.pl, which also uses the Perl library AlgParser.pm.
It then saves the TeX translation in the database for subsequent uses and
passes the TeX to the mimetex executable to be converted to a gif image.
Here are a few common things that can go wrong and some suggestions on how
you might try to fix them.</p>
<ol>
<li>Something had gone wrong on a previous occasion when the filter tried to
translate this expression. Then the database entry for that expression contains
a bad TeX translation in the rawtext field (usually blank). You can fix this
by clicking on &quot;Delete DB Entry&quot;</li>
<li>The First Stage TeX Translation gives a &quot;No text output available&quot;
message. If your server is running Windows, this may be due to the fact that
you haven't installed Perl or didn't install it correctly. If your server is
running some version of Unix (e.g. Linux), then this may be due to your Perl
binary being installed in a nonstandard location. To fix this edit the first
line of the algebra2tex.pl script. Another possible problem which may affect
both Unix and Windows servers is that the web server doesn't have execute permission
on the algebra2tex.pl script. In that case change permissions accordingly</li>
<li>The Second Stage TeX Translation produces malformed TeX. This indicates
a bug in the algebra filter. Post the original algebraic expression and the
bad TeX translation in the <a href="http://moodle.org/mod/forum/view.php?id=752">
Mathematics Tools</a> forum in the Using Moodle course on moodle.org.</li>
<li>The TeX to gif image conversion process does not work. If your server is
running Unix, a likely cause is that the mimetex binary you are using is
incompatible with your operating system. You can try compiling it from the
C sources downloaded from <a href="http://www.forkosh.com/mimetex.zip">
http://www.forkosh.com/mimetex.zip</a>, or looking for an appropriate
binary at <a href="http://moodle.org/download/mimetex/">
http://moodle.org/download/mimetex/</a>. You may then also need to
edit your moodle/filter/algebra/pix.php file to add 
<br /><?PHP echo "case &quot;" . PHP_OS . "&quot;:" ;?><br ?> to the list of operating systems
in the switch (PHP_OS) statement. Windows users may have a problem properly
unzipping mimetex.exe. Make sure that mimetex.exe is is <b>PRECISELY</b>
329728 bytes in size. If not, download fresh copy from
<a href="http://moodle.org/download/mimetex/windows/mimetex.exe">
http://moodle.org/download/mimetex/windows/mimetex.exe</a>. Lastly check
the execute permissions on your mimetex binary, as outlined in item 2 above.</li>
</ol>
</body>
</html>
