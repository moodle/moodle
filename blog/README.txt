=================================================
MOODLE BLOG
=================================================

This moodle module is based on the Simplog blogging system.
You can find more information on simplog at
http://www.simplog.net / http://www.simplog.org
Moodle integration work by Daryl Hawes.

=================================================
TESTERS WANTED
=================================================

This module is currently in the alpha testing phase.

I would appreciate feedback on any interface or functionality issues 
that you have. Please post your comments on moodle.org. Click "Free Support"
from the main page and then click "Blogs" to join the discussion.

Special thanks to these testers deserving of mention:
Tom Murdock
WP1
Dan Marsden

=================================================
TESTING
=================================================
There is online help available by adding the "Blog Menu" block and then clicking the Blog Help link. The online help is quickly getting out of date and could use some help - if you
make any modifications please send them my way for inclusion (or just commit them if you have cvs access.)

Once you have the files in place, either by copying the full easy distribution or by copying the files from cvs over an existing 
installation, you'll need to initialize moodle and complete the installation.

-- Admin your server --
Now that the moodle blog components are in place you'll want to update your database to support it. Visit yoursite.com/moodle/admin/ to 
run the database updates.

When the updates has been completed click the continue link to continue on to the variables setup page. 
When you're done configuring basic moodle settings then visit the admin area again and click on the 'Blog" link to admin site wide blog 
settings.

-- Access blogs --
To get started blogging you can stop by your blog index page at yoursite.com/moodle/blog/ or you can create a new course and use the 
Blog Menu block. Each user will need to click the "enable your blog" link to be able to add entries.
Note that when you add an entry the entry form recognizes whether you came from a course page. 
If you came from a course page the entry becomes associated with that course. 
If you did not get to the add and entry form from a course then your entry will be considered a general entry and not associated with 
any course.

Extra Credit:
Dan Marsden has some good tips on getting a high level of
error and warning output when testing development code. Perhaps you can
try various settings to give us extra feedback. Note that you would also want to
have the moodle debugging option turned on in the moodle admin configuration page.

"There are 3 things I have set differently (as far as I can remember!)
    in the Moodle install process I set the debuging to be "on"
    in php.ini file,�
        error_reporting = E_ALL
    and in my browser [Ed. I assume he is on a windows machine likely using some form of IE],
        internet options > advanced 
        under the browsing section
        display a notification about every script error selected
        disable Script debugging unselected."
             --Dan Marsden, 2004


=================================================
README CONTENTS ADAPTED FROM SIMPLOG
=================================================
 
Welcome to Moodle Blog (based on Simplog)!

Thanks for downloading this software while it in its testing phase.
 
Simplog is free software, released under the GPL:
http://www.gnu.org/copyleft/gpl.html GNU Public License

Simplog was created to provide people with an easy way to create and maintain their own personal or a community weblog.  With the increasing number of people creating on-line journals, there is a need for a tool that allows them a quick and easy way to share their 
lives with the rest of the world.

Moodle Blog is designed first and foremost with personal expression in mind. It is not
implemented as an assignment or course module within Moodle. Rather, it is incorporated
directly and is independent of any course. Each user has the ability to create one
personal blog where they can post their personal reflections. A 'course blog' is just
an aggregate of entries from individual user blogs that have been flagged as being
'associated' with that course.

Daryl Hawes



Relevant portion of Simplog.net notes (from CREDITS.txt file removed from distribution):

The Magpie RSS Parser and Atom Parser codebase courtesy of http://magpierss.sf.net

Trackback codebase contributed by Dougal Campbell - http://dougal.gunters.org
Pingback code based on pingback implementation for b2 - http://www.cafelog.com

Simplog development team:
f-bomb - http://www.webhack.com
jbuberel - http://www.buberel.org