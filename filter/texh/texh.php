<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// TeXH - convert TeX code in <tex> tags into images, using HeVeA        //
//                                                                       //
// Copyright (C) 2001-2003  Bruno Vernier  bruno@vsbeducation.ca         //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

//-------------------------------------------------------------------------
//
// README PREREQUISITES:
//
// 1: install HeVeA on your server http://pauillac.inria.fr/~maranget/hevea/
//     (debian and rpm available) (it is a TeX to HTML/MathML filter)
//
// 2: must include this file "texh.php" in the filters in config.php
//
// 3: must include TEX in the legal tags list in lib/weblib.php
//    CAVEAT: if above not yet done, <TEX> gets converted to &lt;TEX&gt;
//    and can only be fixed by editing in HTML conversion mode
//
// 4: if you are using Windows then the hevea.bat file that comes with the
//    windows distribution needs to have the line
//        if "%1"=="" goto syn
//    removed.
//
//-------------------------------------------------------------------------

/// These lines are important - the variable must match the name 
/// of the actual function below

    $textfilter_function='texh_filter';

    if (function_exists($textfilter_function)) {
        return;
    }


function texh_pipe_cmd($cmd, $text) {
    // general function for external system calls to $cmd,  piping in $text, retrieving output
    // taken mostly from a php tutorial on php.net website (modified slightly)    
    $spec = array(   
            0 => array("pipe", "r"),  // stdin 
            1 => array("pipe", "w"),  // stdout 
            );
    $process = proc_open($cmd , $spec, $pipes);
    $output="";
    if (is_resource($process)) {
        fwrite($pipes[0], $text);
        fclose($pipes[0]);
        while (!feof($pipes[1])) { 
            $buffer = fgets($pipes[1], 1024);
            $output .= $buffer;
        }
        fclose($pipes[1]);
        proc_close($process);
    }
    return $output;     
}

function texh_filter ($courseid, $text) {  
    // TeX conversion with $$ tex code $$ 
    // written by Bruno Vernier (c) 2004 GPL 

    /// Do a quick check using stripos to avoid unnecessary work
    if (!(stripos($text, '<tex') or stripos($text, '$$'))) {
        return $text;
    }

    if (isadmin()) { error_reporting (E_ALL); }; //for debugging

    // <TEX> some general TeX expression </TEX> 
    preg_match_all('/<tex>(.+?)<\/tex>/is', $text, $matches);  
    for ($i=0; $i<count($matches[0]); $i++) { 
        $pipe = texh_pipe_cmd("hevea",$matches[1][$i]);  // take the content only (non-math TeX expected)
        $text = str_replace( $matches[0][$i], $pipe, $text);
    }

    // $$ some MATHEMATICAL TeX expression $$
    preg_match_all('/\$\$(.+?)\$\$/', $text, $matches);  
    for ($i=0; $i<count($matches[0]); $i++) { 
        $pipe = texh_pipe_cmd("hevea",$matches[0][$i]);  // take everything since the $$ is part of the TeX syntax for math
        $text = str_replace( $matches[0][$i], $pipe, $text);
    }

    return $text; 

}



//  Various Notes and Possibilities for Developers only:    
    
//   Note to self:  the following did not work consistently:    
//   $text = preg_replace('/\$\$(.*?)\$\$/', texh_pipe_cmd('hevea',"\\$0"), $text);
//   whereas this seems to work consistently:
//   $text = str_replace( $matches[0][$i], texh_pipe_cmd("hevea",$matches[0][$i]), $text);


// TODO:
//
// - user-friendly guide to inputting TeX (at least enough for up to high school level math)
// - caveat regarding square-roots and roots being converted to fractional exponents 
// - integrate memaid http://memaid.sourceforge.net
// - compile a list of useful shortcuts and shorthands worth doing here


// future possibilities
// $text = str_replace("my_marks", "my marks are ...%", $text);                   // insert SQL/php_function results here
// $text = str_replace("my_uptime", "uptime: ". shell_exec("/usr/bin/uptime") , $text);  // a shortcut to a safe system call
// $text = preg_replace("/vernier/", "Mr. Bruno Vernier" , $text);                // a shortcut expanded

// Version 0.1  Jan 24,2004

?>

