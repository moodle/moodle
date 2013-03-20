<h1>Example version.php file</h1>

<p>version.php is required for all plugins but themes.</p>

<h2>Example of values</h2>

<pre>
    $plugin->version = 2011051000;
    $plugin->requires = 2010112400;
    $plugin->cron = 0;
    $plugin->component = 'plugintype_pluginname';
    $plugin->maturity = MATURITY_STABLE;
    $plugin->release = '2.x (Build: 2011051000)';
    $plugin->dependencies = array('mod_forum' => ANY_VERSION, 'mod_data' => 2010020300);
</pre>

Replace $plugin with $module for activity modules, as in

<pre>
    $module->version = 2012122400;
</pre><?php // $Id$ $module->version = 1;

    $plugin->component
        = 'old_foobar';//$plugin->component='commented';

    $plugin->component      =   
        'block_foobar';
    
$plugin->version = 2013010100;
 ////////$plugin->version = 0;
    /* for activity
       modules use:
    $module->version = 2014131300;

    ***/
$plugin->version = "2010091855";        // Do not use quotes here.
$plugin->version = '2010091856.9'; // Do not use quotes here.


$plugin->requires = /* 2012010100  */ 2012122401  ;

$module->maturity = MATURITY_STABLE;
$module->maturity = 50; // If both present, the constant wins (on contrary to what PHP would do)
$module->maturity = 'MATURITY_BETA'; // Do not use quotes here.

$plugin->maturity = 10;
$plugin->maturity = MATURITY_ALPHA;



$module->release = 2.3;         $plugin->release  = 'v2.4';
$module->release = "v2.3";      $plugin->release    = 2.4;
