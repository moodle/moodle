$Id$

Starting with Moodle 1.4, the glossary module supports a plugin
architecture to create your own formats. This plugin system allows 
you to "invent" your own presentations for your glossary data. :-)

To facilitate the creation process a TEMPLATE format has been created. 
You should use it as the base for your own modifications. It includes
all the suitable data to be showed.

Please, follow this STEPS:

1.-Think about a English word (or short phrase) to define your format.
   For futher reference in this document, we call it "THENAME".
2.-Duplicate the TEMPLATE directory (under mod/glossary/formats/TEMPLATE).
3.-Rename it to THENAME.
4.-Go into the THENAME directory and rename the TEMPLATE_format.php file 
   to THENAME_format.php
5.-Edit the THENAME_format.php file. Change every ocurrence of TEMPLATE to
   THENAME.
6.-Login into Moodle. Go to any glossary in your site.
7.-Edit (configure) your glossary. In the Format popup you'll see a new
   entry. Is must be showed as "displayformatTHENAME". Select it and see
   your glossary.
8.-Edit the THENAME_format.php. Make your own modifications and see them
   in your glossary (you will need some PHP skills for this). This file has 
   been commented as possible to make things easier to you (anyway, every 
   idea to polish such comments will be welcome, of course!!).
9.-If you want to translate your THENAME format name to some nice name to
   be showed in the Format popup, simply, edit your lang/XX/glossary.php
   file (where XX is your languaje) and create a new displayformatTHENAME
   string.
10.-Enjoy!! (and don't forget to send your amazing glossary formats to
   Moodle's forum. They will be welcome!! ;-)

To talk about Glossay formats, go to:
    http://moodle.org/mod/forum/view.php?id=742

Eloy (stronk7)
08/02/2004 (MM/DD/YYYY) :-D
