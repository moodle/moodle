Cron
=============================

<!-- 
	This file is written in Markdown syntax. 
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

Introduction
------------

The cron module allows you to do tasks regularly, by setting up a cron
job that calls a hook in SimpleSAMLphp.  This will invoke
`hooks/hook_cron.php` in any enabled modules


Preparations
------------

You need to enable the module and copy the `config-templates` files of the module into the global `config/` directory.

	[root@simplesamlphp] cd /var/simplesamlphp
	[root@simplesamlphp simplesamlphp] touch modules/cron/enable
	[root@simplesamlphp simplesamlphp] cp modules/cron/config-templates/*.php config/


Configuring the cron module
---------------------------

The configuration (`config/module_cron.php`) should look similar to this:

	$config = array (
	       'key' => 'RANDOM_KEY',
	       'allowed_tags' => array('daily', 'hourly', 'frequent'),
	       'debug_message' => TRUE,
	       'sendemail' => TRUE,
	
	);

Bear in mind that the key is used as a security feature, to restrict
access to your cron. Therefore, you need to make sure that the string
here is a random key available to no one but you. Additionally, make
sure that you include here the appropriate tags - for example any tags
that you previously told metarefresh to use in the `cron` directive.

Triggering Cron
---------------------------

You can trigger the cron hooks through HTTP or CLI.  The HTTP method
is the original technique, and it is recommended if you don't need to
trigger CPU or memory intensive cron hooks.  The CLI option is
recommended if you need more control over memory, CPU limits and
process priority.

### With HTTP

`cron` functionality can be invoked by making an HTTP request to the
cron module.  Use your web browser to go to
`https://YOUR_SERVER/simplesaml/module.php/cron/croninfo.php`. Make
sure to properly set your server's address, as well as use HTTP or
HTTPS accordingly, and also to specify the correct path to the root of
your SimpleSAMLphp installation.

Now, copy the cron configuration suggested on that page:

	# Run cron [daily]
	02 0 * * * curl --silent "https://YOUR_SERVER/simplesaml/module.php/cron/cron.php?key=RANDOM_KEY&tag=daily" > /dev/null 2>&1
	# Run cron [hourly]
	01 * * * * curl --silent "https://YOUR_SERVER/simplesaml/module.php/cron/cron.php?key=RANDOM_KEY&tag=hourly" > /dev/null 2>&1

Finally, add it to your crontab by going back to the terminal, and editing with:

	[root@simplesamlphp config]# crontab -e

This will open up your favourite editor. If an editor different than
the one you use normally appears, exit, and configure the `EDITOR`
variable to tell the command line which editor it should use:

	[root@simplesamlphp config]# export EDITOR=emacs

If you want to trigger a job manually, you can do
so by going back to the cron page in the web interface. Then, just
follow the appropriate links to execute the cron jobs you want. The
page will take a while loading, and eventually show a blank page.


### With CLI

You can invoke cron functionality by running
`/var/simplesamlphp/modules/cron/bin/cron.php` and providing a tag
with the `-t ` argument.

It is strongly recommended that you run the cron cli script as the
same user as the web server.  Several cron hooks created files and
those files may have the wrong permissions if you run the job as root.

**note:** Logging behavior in SSP when running from CLI varies by
version. The latest version logs to PHP's error log and ignores any
logging configuration from `config.php`

Below is an example of invoking the script. It will:

* Run a command as the `apache` user
   * `-s` specifies `apache` user's shell, since the default is non-interactive
* Override INI entries to increase memory and execution time.
    * This allows for processing large metadata files in metarefresh
* Run the `cron.php` script with the `hourly` tag
* Use `nice` to lower the priority below that of web server processes

```
su -s "/bin/sh" \
   -c "nice -n 10 \
       php -d max_execution_time=120 -d memory_limit=600M \
       /var/simplesamlphp/modules/cron/bin/cron.php -t hourly" \
    apache
    
```

