Book module for Moodle (http://moodle.org/) - Copyright (C) 2004-2011  Petr Skoda (http://skodak.org/)

The Book module makes it easy to create multi-page resources with a book-like format. This module can be used to build complete book-like websites inside of your Moodle course.
This module was developed for Technical University of Liberec (Czech Republic). Many ideas and code were taken from other Moodle modules and Moodle itself

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details: http://www.gnu.org/copyleft/gpl.html


Created by:

* Petr Skoda (skodak) - most of the coding & design
* Mojmir Volf, Eloy Lafuente, Antonio Vicent and others

Intentionally omitted features:

* more chapter levels - it would encourage teachers to write too much complex and long books, better use standard standalone HTML editor and import it as Resource. DocBook format is another suitable solution.
* TOC hiding in normal view - instead use printer friendly view
* PDF export - there is no elegant way AFAIK to convert HTML to PDF, use virtual PDF printer or better use DocBook format for authoring
* detailed student tracking (postponed till officially supported)
* export as zipped set of HTML pages - instead use browser command Save page as... in print view
