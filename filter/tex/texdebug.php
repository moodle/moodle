<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This function fetches math. images from the data directory
 * If not, it obtains the corresponding TeX expression from the cache_tex db table
 * and uses mimeTeX to create the image file
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once("../../config.php");

    if (!filter_is_enabled('tex')) {
        print_error('filternotenabled');
    }

    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/filter/tex/lib.php');
    require_once($CFG->dirroot.'/filter/tex/latex.php');

    $action = optional_param('action', '', PARAM_ALPHA);
    $texexp = optional_param('tex', '', PARAM_RAW);

    require_login();
    require_capability('moodle/site:config', context_system::instance(), $USER->id); /// Required cap to run this. MDL-18552

    $output = '';

    // look up in cache if required
    if ($action=='ShowDB' or $action=='DeleteDB') {
        $md5 = md5($texexp);
        $texcache = $DB->get_record("cache_filters", array("filter"=>"tex", "md5key"=>$md5));
    }

    // Action: Show DB Entry
    if ($action=='ShowDB') {
        if ($texcache) {
            $output = "DB cache_filters entry for $texexp\n";
            $output .= "id = $texcache->id\n";
            $output .= "filter = $texcache->filter\n";
            $output .= "version = $texcache->version\n";
            $output .= "md5key = $texcache->md5key\n";
            $output .= "rawtext = $texcache->rawtext\n";
            $output .= "timemodified = $texcache->timemodified\n";
        } else {
            $output = "DB cache_filters entry for $texexp not found\n";
        }
    }

    // Action: Delete DB Entry
    if ($action=='DeleteDB') {
        if ($texcache) {
            $output = "Deleting DB cache_filters entry for $texexp\n";
            $result =  $DB->delete_records("cache_filters", array("id"=>$texcache->id));
            if ($result) {
                $result = 1;
            } else {
                $result = 0;
            }
            $output .= "Number of records deleted = $result\n";
        } else {
            $output = "Could not delete DB cache_filters entry for $texexp\nbecause it could not be found.\n";
        }
    }

    // Action: Show Image
    if ($action=='ShowImageMimetex') {
        tex2image($texexp);
    }

    // Action: Check Slasharguments
    if ($action=='SlashArguments') {
        slasharguments($texexp);
    }

    // Action: Show Tex command line output
    if ($action=='ShowImageTex') {
        TexOutput($texexp, true);
        exit;
    }

    // Action: Show Tex command line output
    if ($action=='ShowOutputTex') {
        if (debugging()) {
            TexOutput($texexp);
        } else {
            echo "Can not output detailed information due to security concerns, please turn on debug mode first.";
        }
        exit;
    }

    if (!empty($action)) {
        outputText($output);
    }

    // nothing more to do if there was any action
    if (!empty($action)) {
        exit;
    }


    function outputText($texexp) {
        header("Content-type: text/html; charset=utf-8");
        echo "<html><body><pre>\n";
        if ($texexp) {
            echo s($texexp)."\n\n";
        } else {
            echo "No text output available\n\n";
        }
        echo "</pre></body></html>\n";
    }

    function tex2image($texexp, $return=false) {
        global $CFG;

        if (!$texexp) {
            echo 'No tex expresion specified';
            return;
        }

        $image  = md5($texexp) . ".gif";
        $filetype = 'image/gif';
        if (!file_exists("$CFG->dataroot/filter/tex")) {
            make_upload_directory("filter/tex");
        }
        $pathname = "$CFG->dataroot/filter/tex/$image";
        if (file_exists($pathname)) {
            unlink($pathname);
        }

        $texexp = '\Large '.$texexp;
        $commandpath = filter_tex_get_executable(true);
        $cmd = filter_tex_get_cmd($pathname, $texexp);
        system($cmd, $status);

        if ($return) {
          return $image;
        }

        if (file_exists($pathname)) {
            send_file($pathname, $image);

        } else if (debugging()) {
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
        } else {
            echo "Can not output detailed information due to security concerns, please turn on debug mode first.";
        }
    }


    // test Tex/Ghostscript output - command execution only
    function TexOutput($expression, $graphic=false) {
        global $CFG;
        $output = '';

        $latex = new latex();

        // first check if it is likely to work at all
        $output .= "<h3>Checking executables</h3>\n";
        $executables_exist = true;
        $pathlatex = get_config('filter_tex', 'pathlatex');
        if (is_file($pathlatex)) {
            $output .= "latex executable ($pathlatex) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> latex executable ($pathlatex) is not readable<br />\n";
        }
        $pathdvips = get_config('filter_tex', 'pathdvips');
        if (is_file($pathdvips)) {
            $output .= "dvips executable ($pathdvips) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> dvips executable ($pathdvips) is not readable<br />\n";
        }
        $pathconvert = get_config('filter_tex', 'pathconvert');
        if (is_file($pathconvert)) {
            $output .= "convert executable ($pathconvert) is readable<br />\n";
        }
        else {
            $executables_exist = false;
            $output .= "<b>Error:</b> convert executable ($pathconvert) is not readable<br />\n";
        }

        // knowing that it might work..
        $md5 = md5($expression);
        $output .= "<p>base filename for expression is '$md5'</p>\n";

        // temporary paths
        $tex = "$latex->temp_dir/$md5.tex";
        $dvi = "$latex->temp_dir/$md5.dvi";
        $ps = "$latex->temp_dir/$md5.ps";
        $convertformat = get_config('filter_tex', 'convertformat');
        $img = "$latex->temp_dir/$md5.{$convertformat}";

        // put the expression as a file into the temp area
        $expression = html_entity_decode($expression);
        $output .= "<p>Processing TeX expression:</p><pre>$expression</pre>\n";
        $doc = $latex->construct_latex_document($expression);
        $fh = fopen($tex, 'w');
        fputs($fh, $doc);
        fclose($fh);

        // cd to temp dir
        chdir($latex->temp_dir);

        // step 1: latex command
        $cmd = "$pathlatex --interaction=nonstopmode --halt-on-error $tex";
        $output .= execute($cmd);

        // step 2: dvips command
        $cmd = "$pathdvips -E $dvi -o $ps";
        $output .= execute($cmd);

        // step 3: convert command
        $cmd = "$pathconvert -density 240 -trim $ps $img ";
        $output .= execute($cmd);

        if (!$graphic) {
            echo $output;
        } else if (file_exists($img)){
            send_file($img, "$md5.{$convertformat}");
        } else {
            echo "Error creating image, see command execution output for more details.";
        }
    }

    function execute($cmd) {
        exec($cmd, $result, $code);
        $output = "<pre>$ $cmd\n";
        $lines = implode("\n", $result);
        $output .= "OUTPUT: $lines\n";
        $output .= "RETURN CODE: $code\n</pre>\n";
        return $output;
    }

    function slasharguments($texexp) {
        global $CFG;
        $admin = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=http';
        $image = tex2image($texexp,true);
        echo "<p>If the following image displays correctly, set your ";
        echo "<a href=\"$admin\" target=\"_blank\">Administration->Server->HTTP</a> ";
        echo "setting for slasharguments to file.php/1/pic.jpg: ";
        echo "<img src=\"$CFG->wwwroot/filter/tex/pix.php/$image\" align=\"absmiddle\"></p>\n";
        echo "<p>Otherwise set it to file.php?file=/1/pic.jpg ";
        echo "It should display correctly as ";
        echo "<img src=\"$CFG->wwwroot/filter/tex/pix.php?file=$image\" align=\"absmiddle\"></p>\n";
        echo "<p>If neither equation image displays correctly, please seek ";
        echo "further help at moodle.org at the ";
        echo "<a href=\"http://moodle.org/mod/forum/view.php?id=752&loginguest=true\" target=\"_blank\">";
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
                    value="f(x)=\int_{-\infty}^x~e^{-t^2}dt" />
            </center>
           <p>The following tests are available:</p>
           <ol>
           <li><input type="radio" name="action" value="ShowDB" id="ShowDB" />
               <label for="ShowDB">See the cache_filters database entry for this expression (if any).</label></li>
           <li><input type="radio" name="action" value="DeleteDB" id="DeleteDB" />
               <label for="DeleteDB">Delete the cache_filters database entry for this expression (if any).</label></li>
           <li><input type="radio" name="action" value="ShowImageMimetex" id="ShowImageMimetex"  checked="checked" />
               <label for="ShowImageMimetex">Show a graphic image of the algebraic expression rendered with mimetex.</label></li>
           <li><input type="radio" name="action" value="ShowImageTex" id="ShowImageTex" />
               <label for="ShowImageTex">Show a graphic image of the algebraic expression rendered with Tex/Ghostscript.</label></li>
           <li><input type="radio" name="action" value="ShowOutputTex" id="ShowOutputTex" />
               <label for="ShowOutputTex">Show command execution output from the algebraic expression rendered with Tex/Ghostscript.</label></li>
           <li><input type="radio" name="action" value="SlashArguments" id="SlashArguments" />
               <label for="SlashArguments">Check slasharguments setting.</label></li>
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
appropriate gif/png image file for that expression and to create one if it doesn't exist.
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
<li>The TeX to gif/png image conversion process does not work.
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
