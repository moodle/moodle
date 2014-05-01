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
 * This script prints basic CSS for the installer
 *
 * @package    core
 * @subpackage install
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(dirname(dirname(__FILE__)).'/config.php')) {
    // already installed
    die;
}

// include only the necessary stuff from themes, keep this small otherwise IE will complain...

// MDL-43839 IE9 cannot handle all of our css.
// Once IE9 is no longer supported we can include 'bootstrapbase/style/moodle.css'
// and remove some of the CSS in $content.
$files = array('');

$content = '';

foreach($files as $file) {
    $content .= file_get_contents(dirname(dirname(__FILE__)).'/theme/'.$file) . "\n";
}

$content .= <<<EOF

body {
    padding: 4px;
}

.headermain {
    margin: 15px;
}

h2 {
  text-align:center;
}

textarea, .uneditable-input {
    width: 50%;
}

#installdiv {
    width: 800px;
    margin-left:auto;
    margin-right:auto;
    padding: 5px;
    margin-bottom: 15px;
}

#installdiv dt {
    font-weight: bold;
}

#installdiv dd {
    padding-bottom: 0.5em;
}

.stage {
    margin-top: 2em;
    margin-bottom: 2em;
    padding: 25px;
}

#installform {
    width: 100%;
}

#envresult {
    text-align:left;
    width: auto;
    margin-left:10em;
}

#envresult dd {
    color: red;
}

fieldset {
    text-align:center;
    border:none;
}

fieldset .configphp,
fieldset .alert {
    text-align: left;
}

.sitelink {
    text-align: center;
}

/*
MDL-43839 IE9 cannot handle all of our CSS.
Once IE9 is no longer supported we can include 'bootstrapbase/style/moodle.css' above
and remove the following.
*/

#page-footer {
    padding: 1em 0;
    margin-top: 1em;
    border-top: 2px solid #ddd;
}

.fitem {
    clear:both;
    text-align:left;
    padding: 8px;
}

.fitemtitle {
    float: left;
    width: 245px;
    text-align: right;
}

label {
    font-weight: bold;
    display: inline-block;
    margin: 5px;
}

.fitemelement {
    margin-left: 265px;
}

.alert, .alert h4 {
    color: #c09853;
}
.alert {
    padding: 8px 35px 8px 14px;
    margin-bottom: 20px;
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    background-color: #fcf8e3;
    border: 1px solid #fbeed5;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

.alert-info {
    color: #3a87ad;
    background-color: #d9edf7;
    border-color: #bce8f1;
}

.alert-success {
    color: #468847;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

.alert-error {
    color: #b94a48;
    background-color: #f2dede;
    border-color: #eed3d7;
}

pre {
    display: block;
    padding: 9.5px;
    margin: 0 0 10px;
    font-size: 13px;
    line-height: 20px;
    word-break: break-all;
    word-wrap: break-word;
    white-space: pre;
    white-space: pre-wrap;
    background-color: #f5f5f5;
    border: 1px solid #ccc;
    border: 1px solid rgba(0,0,0,0.15);
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

.notifytiny {
    font-size: 10.5px;
}

input[type="button"], input[type="submit"] {
    margin: 0 0 10px 5px;
    display: inline-block;
    padding: 4px 12px;
    font-size: 14px;
    line-height: 20px;
    color: #333;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255,255,255,0.75);
    vertical-align: middle;
    cursor: pointer;
    background-color: #f5f5f5;
    background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
    background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
    background-image: -o-linear-gradient(top,#fff,#e6e6e6);
    background-image: linear-gradient(to bottom,#fff,#e6e6e6);
    background-repeat: repeat-x;
    border: 1px solid #ccc;
    border-color: #e6e6e6 #e6e6e6 #bfbfbf;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    border-bottom-color: #b3b3b3;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
    -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
    -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
}

input[type="button"]:hover, input[type="submit"]:hover, input[type="button"]:focus, input[type="submit"]:focus {
    color: #333;
    text-decoration: none;
    background-position: 0 -15px;
    -webkit-transition: background-position .1s linear;
    -moz-transition: background-position .1s linear;
    -o-transition: background-position .1s linear;
    transition: background-position .1s linear;
}

input[type="button"]:hover, input[type="submit"]:hover, input[type="button"]:focus, input[type="submit"]:focus, input[type="button"]:active, input[type="submit"]:active, input[type="button"].active, input[type="submit"].active, input[type="button"].disabled, input[type="submit"].disabled, input[type="reset"].disabled, input[type="submit"][disabled], input[type="reset"][disabled] {
    color: #333;
    background-color: #e6e6e6;
}

button, input, select, textarea {
    margin: 0;
}

select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"] {
    display: inline-block;
    height: 20px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555;
    vertical-align: middle;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

select {
    background-color: #fff;
    border: 1px solid #ccc;
}

label, select, button, input[type="button"], input[type="submit"], input[type="radio"], input[type="checkbox"] {
    cursor: pointer;
}


select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"] {
    display: inline-block;
    height: 20px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555;
    vertical-align: middle;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

select, input[type="file"] {
    height: 30px;
    line-height: 30px;
}

textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"] {
    background-color: #fff;
    border: 1px solid #ccc;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    -webkit-transition: border linear .2s,box-shadow linear .2s;
    -moz-transition: border linear .2s,box-shadow linear .2s;
    -o-transition: border linear .2s,box-shadow linear .2s;
    transition: border linear .2s,box-shadow linear .2s;
}

input[disabled], select[disabled], textarea[disabled], input[readonly], select[readonly], textarea[readonly] {
    cursor: not-allowed;
    background-color: #eee;
}

input.btn-primary {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
    background-color: #005aa8;
    background-image: -moz-linear-gradient(top,#0070a8,#0038a8);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#0070a8),to(#0038a8));
    background-image: -webkit-linear-gradient(top,#0070a8,#0038a8);
    background-image: -o-linear-gradient(top,#0070a8,#0038a8);
    background-image: linear-gradient(to bottom,#0070a8,#0038a8);
    background-repeat: repeat-x;
    border-color: #0038a8 #0038a8 #001e5c;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0070a8',endColorstr='#ff0038a8',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}

input.btn-primary:hover,
input.btn-primary:active,
input.btn-primary:focus {
    color: #fff;
    background-color: #0038a8;
}


.breadcrumb {
    background-color: #f5f5f5;
}
.breadcrumb {
    padding: 8px 15px;
    margin: 0 0 20px;
    list-style: none;
    background-color: #f5f5f5;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

.breadcrumb > li {
    display: inline-block;
    text-shadow: 0 1px 0 #fff;
    line-height: 20px;
}

body {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 14px;
    line-height: 20px;
    color: #333;
}
.breadcrumb {
    background-color: rgb(245, 245, 245);
    padding: 8px 15px;
}
/*
End of MDL-43839 IE9 specific CSS.
*/

EOF;

// fix used urls
$content = str_replace('[[pix:theme|hgradient]]', '../theme/standard/pix/hgradient.jpg', $content);
$content = str_replace('[[pix:theme|vgradient]]', '../theme/standard/pix/vgradient.jpg', $content);

@header('Content-Disposition: inline; filename="css.php"');
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Cache-Control: post-check=0, pre-check=0', false);
@header('Pragma: no-cache');
@header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
@header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
@header('Accept-Ranges: none');
@header('Content-Type: text/css; charset=utf-8');

echo $content;
