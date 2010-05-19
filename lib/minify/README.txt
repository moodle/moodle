The files in this directory represent the default Minify setup designed to ease
integration with your site. This app will combine and minify your Javascript or
CSS files and serve them with HTTP compression and cache headers.


RECOMMENDED

It's recommended to edit config.php to set $min_cachePath to a writeable
(by PHP) directory on your system. This will improve performance.


GETTING STARTED

The quickest way to get started is to use the Minify URI Builder application
on your website: http://example.com/min/builder/


MINIFYING A SINGLE FILE

Let's say you want to serve this file:
  http://example.com/wp-content/themes/default/default.css

Here's the "Minify URL" for this file:
  http://example.com/min/?f=wp-content/themes/default/default.css

In other words, the "f" argument is set to the file path from root without the 
initial "/". As CSS files may contain relative URIs, Minify will automatically
"fix" these by rewriting them as root relative.


COMBINING MULTIPLE FILES IN ONE DOWNLOAD

Separate the paths given to "f" with commas.

Let's say you have CSS files at these URLs:
  http://example.com/scripts/jquery-1.2.6.js
  http://example.com/scripts/site.js

You can combine these files through Minify by requesting this URL:
  http://example.com/min/?f=scripts/jquery-1.2.6.js,scripts/site.js


SIMPLIFYING URLS WITH A BASE PATH

If you're combining files that share the same ancestor directory, you can use
the "b" argument to set the base directory for the "f" argument. Do not include
the leading or trailing "/" characters.

E.g., the following URLs will serve the exact same content:
  http://example.com/min/?f=scripts/jquery-1.2.6.js,scripts/site.js,scripts/home.js
  http://example.com/min/?b=scripts&f=jquery-1.2.6.js,site.js,home.js


MINIFY URLS IN HTML

In (X)HTML files, don't forget to replace any "&" characters with "&amp;".


SPECIFYING ALLOWED DIRECTORIES

By default, Minify will serve any *.css/*.js files within the DOCUMENT_ROOT. If
you'd prefer to limit Minify's access to certain directories, set the 
$min_serveOptions['minApp']['allowDirs'] array in config.php. E.g. to limit 
to the /js and /themes/default directories, use:

$min_serveOptions['minApp']['allowDirs'] = array('//js', '//themes/default');


GROUPS: FASTER PERFORMANCE AND BETTER URLS

For the best performance, edit groupsConfig.php to pre-specify groups of files 
to be combined under preset keys. E.g., here's an example configuration in 
groupsConfig.php:

return array(
    'js' => array('//js/Class.js', '//js/email.js')
);

This pre-selects the following files to be combined under the key "js":
  http://example.com/js/Class.js
  http://example.com/js/email.js
  
You can now serve these files with this simple URL:
  http://example.com/min/?g=js
  

GROUPS: SPECIFYING FILES OUTSIDE THE DOC_ROOT

In the groupsConfig.php array, the "//" in the file paths is a shortcut for
the DOCUMENT_ROOT, but you can also specify paths from the root of the filesystem
or relative to the DOC_ROOT: 

return array(
    'js' => array(
        '//js/file.js'            // file within DOC_ROOT
        ,'//../file.js'           // file in parent directory of DOC_ROOT
        ,'C:/Users/Steve/file.js' // file anywhere on filesystem
    )
);


FAR-FUTURE EXPIRES HEADERS

Minify can send far-future (one year) Expires headers. To enable this you must
add a number to the querystring (e.g. /min/?g=js&1234 or /min/f=file.js&1234) 
and alter it whenever a source file is changed. If you have a build process you 
can use a build/source control revision number.

If you serve files as a group, you can use the utility function Minify_groupUri()
to get a "versioned" Minify URI for use in your HTML. E.g.:

<?php
// add /min/lib to your include_path first!
require $_SERVER['DOCUMENT_ROOT'] . '/min/utils.php';

$jsUri = Minify_groupUri('js'); 
echo "<script type='text/javascript' src='{$jsUri}'></script>";


DEBUG MODE

In debug mode, instead of compressing files, Minify sends combined files with
comments prepended to each line to show the line number in the original source
file. To enable this, set $min_allowDebugFlag to true in config.php and append
"&debug=1" to your URIs. E.g. /min/?f=script1.js,script2.js&debug=1

Known issue: files with comment-like strings/regexps can cause problems in this mode.


QUESTIONS?

http://groups.google.com/group/minify
