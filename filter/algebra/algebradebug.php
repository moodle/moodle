<?php
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    require_once("../../config.php");

    if (!filter_is_enabled('algebra')) {
        print_error('filternotenabled');
    }

    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/filter/tex/lib.php');

    $action = optional_param('action', '', PARAM_ALPHANUM);
    $algebra = optional_param('algebra', '', PARAM_RAW);

    require_login();
    require_capability('moodle/site:config', context_system::instance());
    if ($action || $algebra) {
        require_sesskey();
    }

    if ($algebra && $action) {
      $md5 = md5($algebra);
      if ($action == 'ShowDB' || $action == 'DeleteDB') {
        $texcache = $DB->get_record("cache_filters", array("filter"=>"algebra", "md5key"=>$md5));
      }
      if ($action == 'ShowDB') {
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
      if ($action == 'DeleteDB') {
        if ($texcache) {
          $output = "Deleting DB cache_filters entry for $algebra\n";
          $result =  $DB->delete_records("cache_filters", array("id"=>$texcache->id));
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
      if ($action == 'TeXStage1') {
        $output = algebra2tex($algebra);
      }
      if ($action == 'TexStage2') {
        $output = algebra2tex($algebra);
        $output = refineTeX($output);
      }
      if ($action == 'ShowImage'|| $action == 'SlashArguments') {
        $output = algebra2tex($algebra);
        $output = refineTeX($output);
        if ($action == 'ShowImage') {
          tex2image($output, $md5);
        } else {
          slasharguments($output, $md5);
        }
      } else {
        outputText($output);
      }
      exit;
    }

function algebra2tex($algebra) {
  global $CFG;
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
  $algebra = escapeshellarg($algebra);

  if ( (PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows") ) {
    $cmd  = "cd $CFG->dirroot\\filter\\algebra & algebra2tex.pl x/2";
    $test = `$cmd`;
    if ($test != '\frac{x}{2}') {
      echo "There is a problem with either Perl or the script algebra2tex.pl<br/>";
      $ecmd = $cmd . " 2>&1";
      echo `$ecmd` . "<br/>\n";
      echo "The shell command<br/>$cmd<br/>returned status = $status<br/>\n";
      $commandpath = "$CFG->dirroot\\filter\\algebra\\algebra2tex.pl";
      if (file_exists($commandpath)) {
        echo "The file permissions of algebra2tex.pl are: " . decoct(fileperms($commandpath)) . "<br/>";
      }
      die;
    }
    $cmd  = "cd $CFG->dirroot\\filter\\algebra & algebra2tex.pl $algebra";
  } else {
    $cmd  = "cd $CFG->dirroot/filter/algebra; ./algebra2tex.pl x/2";
    $test = `$cmd`;
    if ($test != '\frac{x}{2}') {
      echo "There is a problem with either Perl or the script algebra2tex.pl<br/>";
      $ecmd = $cmd . " 2>&1";
      echo `$ecmd` . "<br/>\n";
      echo "The shell command<br/>$cmd<br/>returned status = $status<br/>\n";
      $commandpath = "$CFG->dirroot/filter/algebra/algebra2tex.pl";
      if (file_exists($commandpath)) {
        echo "The file permissions of algebra2tex.pl are: " . decoct(fileperms($commandpath)) . "<br/>";
      }
      die;
    }
    $cmd  = "cd $CFG->dirroot/filter/algebra; ./algebra2tex.pl $algebra";
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
  header("Content-type: text/html; charset=utf-8");
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

function tex2image($texexp, $md5, $return=false) {
    global $CFG;

    if (!$texexp) {
        echo 'No tex expresion specified';
        return;
    }

    $texexp = '\Large ' . $texexp;
    $image  = $md5 . ".gif";
    $filetype = 'image/gif';
    if (!file_exists("$CFG->dataroot/filter/algebra")) {
        make_upload_directory("filter/algebra");
    }
    $pathname = "$CFG->dataroot/filter/algebra/$image";
    if (file_exists($pathname)) {
        unlink($pathname);
    }
    $commandpath = filter_tex_get_executable(true);
    $cmd = filter_tex_get_cmd($pathname, $texexp);
    system($cmd, $status);

    if ($return) {
        return $image;
    }
    if (file_exists($pathname)) {
        send_file($pathname, $image);

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

function slasharguments($texexp, $md5) {
  global $CFG;
  $admin = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=http';
  $image = tex2image($texexp,$md5,true);
  echo "<p>If the following image displays correctly, set your ";
  echo "<a href=\"$admin\" target=\"_blank\">Administration->Server->HTTP</a> ";
  echo "setting for slasharguments to file.php/1/pic.jpg: ";
  echo "<img src=\"pix.php/$image\" align=\"absmiddle\"></p>\n";
  echo "<p>Otherwise set it to file.php?file=/1/pic.jpg ";
  echo "It should display correctly as ";
  echo "<img src=\"pix.php?file=$image\" align=\"absmiddle\"></p>\n";
  echo "<p>If neither equation image displays correctly, please seek ";
  echo "further help at moodle.org at the ";
  echo "<a href=\"http://moodle.org/mod/forum/view.php?id=752&loginguest=true\" target=\"_blank\">";
  echo "Mathematics Tools Forum</a></p>";
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
             <label for="algebra" class="accesshide"><?php print_string('algebraicexpression', 'filter_algebra'); ?></label>
             <input type="text" id="algebra" name="algebra" size="50"
                    value="sin(z)/(x^2+y^2)" />
            </center>
           <ol>
           <li>First click on this button <button type="submit" name="action" value="ShowDB">Show DB Entry</button>
               to see the cache_filters database entry for this expression.</li>
           <li>If the database entry looks corrupt, click on this button to delete it:
               <button type="submit" name="action" value="DeleteDB">Delete DB Entry</button></li>
           <li>Now click on this button <button type="submit" name="action" value="TeXStage1">First Stage Tex Translation</button>.
               A preliminary translation into TeX will appear in the box below.</li>
           <li>Next click on this button <button type="submit" name="action" value="TexStage2">Second Stage Tex Translation</button>.
               A more refined translation into TeX will appear in the box below.</li>
           <li>Then click on this button <button type="submit" name="action" value="ShowImage">Show Image</button>
               to show a graphic image of the algebraic expression.</li>
           <li>Finally check your slash arguments setting
               <button type="submit" name="action" value="SlashArguments">Check Slash Arguments</button></li>
           </ol>
           <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
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
http://www.forkosh.com/mimetex.zip</a>. Lastly check the execute permissions
on your mimetex binary, as outlined in item 2 above.</li>
</ol>
</body>
</html>
