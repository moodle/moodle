<?PHP // $Id$
/*
  Filename: config.php
  Purpose of file: Setting defines for the theme

  Author of file: Bjarne Varoystrand aka Black Skorpio
  E-mail: webmaster@postnuke-sweden.com
  Web: www.postnuke-sweden.com
  ICQ: 1194177

 ---------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

$THEME->themewidth    = "98%";     // The total width of the theme
$THEME->themeborders  = "#cccccc"; // Is used as border color for the theme
$THEME->topmargin     = "10";      // Used in the theme <body> tag
$THEME->leftmargin    = "0";       // Used in the theme <body> tag
$THEME->marginheight  = "10";      // Used in the theme <body> tag
$THEME->marginwidth   = "0";       // Used in the theme <body> tag

$THEME->body         = "#FFFFFF";  // Main page color
$THEME->cellheading  = "#E8E8E8";  // Standard headings of big tables
$THEME->cellheading2 = "#AAAAAA";  // Highlight headings of tables
$THEME->cellcontent  = "#FFFFFF";  // For areas with text
$THEME->cellcontent2 = "#EFEFEF";  // Alternate colour
$THEME->borders      = "#555555";  // Table borders

$THEME->frontlogo    = "images/frontlogo.jpg";       // Logo on front page
$THEME->smalllogo    = "images/smalllogo.jpg";  // Header logo on other pages

$THEME->highlight    = "#AAFFAA";  // Highlighted text (eg after a search)
$THEME->hidden       = "#AAAAAA";  // To color things that are hidden
$THEME->autolink     = "#DDDDDD";  // To color auto-generated links (eg glossary)

$THEME->custompix    = false;      // If true, then this theme must have a "pix" 
                                   // subdirectory that contains copies of all 
                                   // files from the moodle/pix directory
                                   // See "cordoroyblue" for an up-to-date example.
?>
