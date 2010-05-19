<?php 

if (phpversion() < 5) {
    exit('Minify requires PHP5 or greater.');
}

// check for auto-encoding
$encodeOutput = (function_exists('gzdeflate')
                 && !ini_get('zlib.output_compression'));

require dirname(__FILE__) . '/../config.php';

if (! $min_enableBuilder) {
    header('Location: /');
    exit();
}

ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <title>Minify URI Builder</title>
    <style type="text/css">
body {margin:1em 60px;}
h1, h2, h3 {margin-left:-25px; position:relative;}
h1 {margin-top:0;}
#sources {margin:0; padding:0;}
#sources li {margin:0 0 0 40px}
#sources li input {margin-left:2px}
#add {margin:5px 0 1em 40px}
.hide {display:none}
#uriTable {border-collapse:collapse;}
#uriTable td, #uriTable th {padding-top:10px;}
#uriTable th {padding-right:10px;}
#groupConfig {font-family:monospace;}
b {color:#c00}
.topNote {background: #ff9; display:inline-block; padding:.5em .6em; margin:0 0 1em;}
.topWarning {background:#c00; color:#fff; padding:.5em .6em; margin:0 0 1em;}
    </style>
</head>

<?php if (! isset($min_cachePath)): ?>
<p class=topNote><strong>Note:</strong> Please set <code>$min_cachePath</code> 
in /min/config.php to improve performance.</p>
<?php endIf; ?>

<p id=minRewriteFailed class="hide"><strong>Note:</strong> Your webserver does not seem to
 support mod_rewrite (used in /min/.htaccess). Your Minify URIs will contain "?", which 
<a href="http://www.stevesouders.com/blog/2008/08/23/revving-filenames-dont-use-querystring/"
>may reduce the benefit of proxy cache servers</a>.</p>

<h1>Minify URI Builder</h1>

<noscript><p class="topNote">Javascript and a browser supported by jQuery 1.2.6 is required
for this application.</p></noscript>

<div id=app class=hide>

<p>Create a list of Javascript or CSS files (or 1 is fine) you'd like to combine
and click [Update].</p>

<ol id=sources><li></li></ol>
<div id=add><button>Add file +</button></div>

<div id=bmUris></div>

<p><button id=update class=hide>Update</button></p>

<div id=results class=hide>

<h2>Minify URI</h2>
<p>Place this URI in your HTML to serve the files above combined, minified, compressed and
with cache headers.</p>
<table id=uriTable>
    <tr><th>URI</th><td><a id=uriA class=ext>/min</a> <small>(opens in new window)</small></td></tr>
    <tr><th>HTML</th><td><input id=uriHtml type=text size=100 readonly></td></tr>
</table>

<h2>How to serve these files as a group</h2>
<p>For the best performance you can serve these files as a pre-defined group with a URI
like: <code><span class=minRoot>/min/?</span>g=keyName</code></p>
<p>To do this, add a line like this to /min/groupsConfig.php:</p>

<pre><code>return array(
    <span style="color:#666">... your existing groups here ...</span>
<input id=groupConfig size=100 type=text readonly>
);</code></pre>

<p><em>Make sure to replace <code>keyName</code> with a unique key for this group.</em></p>
</div>

<div id=getBm>
<h3>Find URIs on a Page</h3>
<p>You can use the bookmarklet below to fetch all CSS &amp; Javascript URIs from a page
on your site. When you active it, this page will open in a new window with a list of
available URIs to add.</p>

<p><a id=bm>Create Minify URIs</a> <small>(right-click, add to bookmarks)</small></p>
</div>

<h3>Combining CSS files that contain <code>@import</code></h3>
<p>If your CSS files contain <code>@import</code> declarations, Minify will not 
remove them. Therefore, you will want to remove those that point to files already
in your list, and move any others to the top of the first file in your list 
(imports below any styles will be ignored by browsers as invalid).</p>
<p>If you desire, you can use Minify URIs in imports and they will not be touched
by Minify. E.g. <code>@import "<span class=minRoot>/min/?</span>g=css2";</code></p>

</div><!-- #app -->

<hr>
<p>Need help? Search or post to the <a class=ext 
href="http://groups.google.com/group/minify">Minify discussion list</a>.</p>
<p><small>This app is minified :) <a class=ext 
href="http://code.google.com/p/minify/source/browse/trunk/min/builder/index.php">view 
source</a></small></p>

<script type="text/javascript" 
src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>

<script type="text/javascript">
$(function () {
    // detection of double output encoding
    var msg = '<\p class=topWarning><\strong>Warning:<\/strong> ';
    var url = 'ocCheck.php?' + (new Date()).getTime();
    $.get(url, function (ocStatus) {
        $.get(url + '&hello=1', function (ocHello) {
            if (ocHello != 'World!') {
                msg += 'It appears output is being automatically compressed, interfering ' 
                     + ' with Minify\'s own compression. ';
                if (ocStatus == '1')
                    msg += 'The option "zlib.output_compression" is enabled in your PHP configuration. '
                         + 'Minify set this to "0", but it had no effect. This option must be disabled ' 
                         + 'in php.ini or .htaccess.';
                else
                    msg += 'The option "zlib.output_compression" is disabled in your PHP configuration '
                         + 'so this behavior is likely due to a server option.';
                $(document.body).prepend(msg + '<\/p>');
            } else
                if (ocStatus == '1')
                    $(document.body).prepend('<\p class=topNote><\strong>Note:</\strong> The option '
                        + '"zlib.output_compression" is enabled in your PHP configuration, but has been '
                        + 'successfully disabled via ini_set(). If you experience mangled output you '
                        + 'may want to consider disabling this option in your PHP configuration.<\/p>'
                    );
        });
    });
});
</script>
<script type="text/javascript">
    // workaround required to test when /min isn't child of web root
    var src = location.pathname.replace(/\/[^\/]*$/, '/_index.js').substr(1);
    document.write('<\script type="text/javascript" src="../?f=' + src + '"><\/script>');
</script>

<?php

$serveOpts = array(
    'content' => ob_get_contents()
    ,'id' => __FILE__
    ,'lastModifiedTime' => max(
        // regenerate cache if either of these change
        filemtime(__FILE__)
        ,filemtime(dirname(__FILE__) . '/../config.php')
    )
    ,'minifyAll' => true
    ,'encodeOutput' => $encodeOutput
);
ob_end_clean();

set_include_path(dirname(__FILE__) . '/../lib' . PATH_SEPARATOR . get_include_path());

require 'Minify.php';

if (0 === stripos(PHP_OS, 'win')) {
    Minify::setDocRoot(); // we may be on IIS
}
Minify::setCache(isset($min_cachePath) ? $min_cachePath : null);
Minify::$uploaderHoursBehind = $min_uploaderHoursBehind;

Minify::serve('Page', $serveOpts);
