///////////////////////////////////////////////////////////////////////////
// This program is part of Moodle - Modular Object-Oriented Dynamic      //
// Learning Environment - http://moodle.com                              //
//                                                                       //
//  Multilingual Filter                                                  //
//  $Id$ //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

This filter implements an XML syntax to provide
fully multilingual website.  No only the Moodle interface
can be translated but also the content!

To activate this filter, add a line like this to your
config.php:

   $CFG->textfilter1 = 'filter/multilang/multilang.php';

Syntax to display a multilingual content:

 <lang lang="en" format="auto">
 Introduction
 </lang>

 <lang lang="es" format="auto">
 Introducción
 </lang>

 <lang lang="de" format="auto">
 Einleitung
 </lang>


///////////////////////////////////////////////////////////////////////////
//                                                                       //
// Copyright (C) 2004  Gaëtan Frenoy <gaetan à frenoy.net>               //
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
