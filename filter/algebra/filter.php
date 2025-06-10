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
 * Moodle - Filter for converting simple calculator-type algebraic
 * expressions to cached gif images
 *
 * @package    filter
 * @subpackage algebra
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//-------------------------------------------------------------------------
// NOTE: This Moodle text filter converts algebraic expressions delimited
// by either @@...@@ or by <algebra...>...</algebra> tags
// first converts it to TeX using WeBWorK algebra parser Perl library
// AlgParser.pm, part of the WeBWorK distribution obtained from
// http://webhost.math.rochester.edu/downloadwebwork/
// then converts the TeX to gif images using
// mimetex.cgi obtained from http://www.forkosh.com/mimetex.html authored by
// John Forkosh john@forkosh.com. The mimetex.cgi ELF binary compiled for Linux i386
// as well as AlgParser.pm are included with this distribution.
// Note that there may be patent restrictions on the production of gif images
// in Canada and some parts of Western Europe and Japan until July 2004.
//-------------------------------------------------------------------------
// You will then need to edit your moodle/config.php to invoke mathml_filter.php
//-------------------------------------------------------------------------

function filter_algebra_image($imagefile, $tex= "", $height="", $width="", $align="middle") {
  // Given the path to a picture file in a course, or a URL,
  // this function includes the picture in the page.
  global $CFG, $OUTPUT;

  $output = "";
  $style = 'style="border:0px; vertical-align:'.$align.';';
  $title = '';
  if ($tex) {
    $tex = html_entity_decode($tex, ENT_QUOTES, 'UTF-8');
    $title = 'title="'.s($tex).'"';
  }
  if ($height) {
    $style .= " height:{$height}px;";
  }
  if ($width) {
    $style .= " width:{$width}px;";
  }
  $style .= '"';
  $anchorcontents = '';
  if ($imagefile) {
    $anchorcontents .= "<img $title alt=\"".s($tex)."\" src=\"";
    if ($CFG->slasharguments) {        // Use this method if possible for better caching
      $anchorcontents .= "$CFG->wwwroot/filter/algebra/pix.php/$imagefile";
    } else {
      $anchorcontents .= "$CFG->wwwroot/filter/algebra/pix.php?file=$imagefile";
    }
    $anchorcontents .= "\" $style />";

    if (!file_exists("$CFG->dataroot/filter/algebra/$imagefile") && has_capability('moodle/site:config', context_system::instance())) {
        $link = '/filter/algebra/algebradebug.php';
        $action = null;
    } else {
        $link = new moodle_url('/filter/tex/displaytex.php', array('texexp'=>$tex));
        $action = new popup_action('click', $link, 'popup', array('width'=>320,'height'=>240)); //TODO: the popups do not work when text caching is enabled!!
    }
    $output .= $OUTPUT->action_link($link, $anchorcontents, $action, array('title'=>'TeX'));

  } else {
    $output .= "Error: must pass URL or course";
  }
  return $output;
}

class filter_algebra extends moodle_text_filter {
    public function filter($text, array $options = array()){
        global $CFG, $DB;

        /// Do a quick check using stripos to avoid unnecessary wor
        if (!preg_match('/<algebra/i',$text) && !strstr($text,'@@')) {
            return $text;
        }

//restrict filtering to forum 130 (Maths Tools on moodle.org)
#    $scriptname = $_SERVER['SCRIPT_NAME'];
#    if (!strstr($scriptname,'/forum/')) {
#        return $text;
#    }
#    if (strstr($scriptname,'post.php')) {
#        $parent = forum_get_post_full($_GET['reply']);
#        $discussion = $DB->get_record("forum_discussions",array("id"=>$parent->discussion));
#    } else if (strstr($scriptname,'discuss.php')) {
#        $discussion = $DB->get_record("forum_discussions",array("id"=>$_GET['d']));
#    } else {
#        return $text;
#    }
#    if ($discussion->forum != 130) {
#        return $text;
#    }

        preg_match_all('/@(@@+)([^@])/',$text,$matches);
        for ($i=0;$i<count($matches[0]);$i++) {
            $replacement = str_replace('@','&#x00040;',$matches[1][$i]).$matches[2][$i];
            $text = str_replace($matches[0][$i],$replacement,$text);
        }

        // <algebra> some algebraic input expression </algebra>
        // or @@ some algebraic input expression @@

        preg_match_all('/<algebra>(.+?)<\/algebra>|@@(.+?)@@/is', $text, $matches);
        for ($i=0; $i<count($matches[0]); $i++) {
            $algebra = $matches[1][$i] . $matches[2][$i];

            // Look for some common false positives, and skip processing them.
            if ($algebra == 'PLUGINFILE' || $algebra == 'DRAFTFILE') {
                // Raw pluginfile URL.
                continue;
            }
            if (preg_match('/^ -\d+(,\d+)? \+\d+(,\d+)? $/', $algebra)) {
                // Part of a unified diff.
                continue;
            }

            $algebra = str_replace('<nolink>','',$algebra);
            $algebra = str_replace('</nolink>','',$algebra);
            $algebra = str_replace('<span class="nolink">','',$algebra);
            $algebra = str_replace('</span>','',$algebra);
            $align = "middle";
            if (preg_match('/^align=bottom /',$algebra)) {
              $align = "text-bottom";
              $algebra = preg_replace('/^align=bottom /','',$algebra);
            } else if (preg_match('/^align=top /',$algebra)) {
              $align = "text-top";
              $algebra = preg_replace('/^align=top /','',$algebra);
            }
            $md5 =  md5($algebra);
            $filename =  $md5  . ".gif";
            if (! $texcache = $DB->get_record("cache_filters",array("filter"=>"algebra", "md5key"=>$md5))) {
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
               if ( (PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
                  $cmd  = "cd $CFG->dirroot\\filter\\algebra & algebra2tex.pl $algebra";
               } else {
                  $cmd  = "cd $CFG->dirroot/filter/algebra; ./algebra2tex.pl $algebra";
               }
               $texexp = `$cmd`;
               if (preg_match('/parsehilight/',$texexp)) {
                 $text = str_replace( $matches[0][$i],"<b>Syntax error:</b> " . $texexp,$text);
               } else if ($texexp) {
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
                  //$texexp = preg_replace('/\\\frac{(.+?)}{\\\left\((.+?)\\\right\)}/s','\frac{'."\$1}{\$2}",$texexp);
                  $texexp = preg_replace('/\\\sqrt{(.+?),(.+?)}/s','\sqrt['. "\$2]{\$1}",$texexp);
                  $texexp = preg_replace('/\\\mbox{abs}\\\left\((.+?)\\\right\)/s',"|\$1|",$texexp);
                  $texexp = preg_replace('/\\\log\\\left\((.+?),(.+?)\\\right\)/s','\log_{'. "\$2}\\left(\$1\\right)",$texexp);
                  $texexp = preg_replace('/(\\\cos|\\\sin|\\\tan|\\\sec|\\\csc|\\\cot)([h]*)\\\left\((.+?),(.+?)\\\right\)/s',"\$1\$2^{". "\$4}\\left(\$3\\right)",$texexp);
                  $texexp = preg_replace('/\\\int\\\left\((.+?),(.+?),(.+?)\\\right\)/s','\int_'. "{\$2}^{\$3}\$1 ",$texexp);
                  $texexp = preg_replace('/\\\int\\\left\((.+?d[a-z])\\\right\)/s','\int '. "\$1 ",$texexp);
                  $texexp = preg_replace('/\\\lim\\\left\((.+?),(.+?),(.+?)\\\right\)/s','\lim_'. "{\$2\\to \$3}\$1 ",$texexp);
                  // Remove a forbidden keyword.
                  $texexp = str_replace('\mbox', '', $texexp);
                  $texcache = new stdClass();
                  $texcache->filter = 'algebra';
                  $texcache->version = 1;
                  $texcache->md5key = $md5;
                  $texcache->rawtext = $texexp;
                  $texcache->timemodified = time();
                  $DB->insert_record("cache_filters", $texcache, false);
                  $text = str_replace( $matches[0][$i], filter_algebra_image($filename, $texexp, '', '', $align), $text);
               } else {
                  $text = str_replace( $matches[0][$i],"<b>Undetermined error:</b> " . $matches[0][$i], $text);
               }
            } else {
               $text = str_replace( $matches[0][$i], filter_algebra_image($filename, $texcache->rawtext), $text);
            }
        }
        return $text;
    }
}


