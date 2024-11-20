Riak Store module
=================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

Introduction
------------

The riak module implements a Store that can be used as a backend
for SimpleSAMLphp session data like the phpsession, sql, or memcache
backends.

Preparations
------------

The obvious first step for using Riak as a backend is to install
and configure a Riak cluster for SimpleSAMLphp to use. Please refer
to the Riak documentation for this.

This module requires the use of a Riak backend that supports secondary
indexes. Refer to the Riak documentation on how to enable an
appropriate backend for use by this module. Currently the only
storage backend that supports secondary indexes is leveldb.

Finally, you need to config SimpleSAMLphp to use the riak Store by
enabling the following modules:

 1. cron
 2. riak

The cron module allows you to do tasks regularly by setting up a
cronjob that calls hooks in SimpleSAMLphp. This is required by the
riak module to remove expired entries in the store.

Enabling the riak module allows it to be loaded and used as a storage
backend.

You also need to copy the `config-templates` files from the cron
module above into the global `config/` directory.

	$ cd /var/simplesamlphp
	$ touch modules/cron/enable
	$ cp modules/cron/config-templates/*.php config/
	$ touch modules/riak/enable
	$ cp modules/riak/config-templates/*.php config/


Configuring the cron module
---------------------------

At `/var/simplesamlphp/config`

	$ vi module_cron.php

edit:

	$config = array (
		'key' => 'secret',
		'allowed_tags' => array('daily', 'hourly', 'frequent'),
		'debug_message' => TRUE,
		'sendemail' => TRUE,
	);

Then: With your browser go to => https://simplesamlphp_machine/simplesaml/module.php/cron/croninfo.php

And copy the cron's sugestion:

	-------------------------------------------------------------------------------------------------------------------
	Cron is a way to run things regularly on unix systems.

	Here is a suggestion for a crontab file:

	# Run cron [daily]
	02 0 * * * curl --silent "https://simplesamlphp_machine/simplesaml/module.php/cron/cron.php?key=secret&tag=daily" > /dev/null 2>&1
	# Run cron [hourly]
	01 * * * * curl --silent "https://simplesamlphp_machine/simplesaml/module.php/cron/cron.php?key=secret&tag=hourly" > /dev/null 2>&1
	# Run cron [frequent]
	XXXXXXXXXX curl --silent "https://simplesamlphp_machine/simplesaml/module.php/cron/cron.php?key=secret&tag=frequent" > /dev/null 2>&1
		Click here to run the cron jobs:

	Run cron [daily]
	Run cron [hourly]
	Run cron [frequent]
	-------------------------------------------------------------------------------------------------------------------

Add to CRON with

	# crontab -e

Configuring the riak module
---------------------------

The riak module uses the following configuration options specified
in `config/module_riak.php`. The defaults are listed:

	$config = [
		'host' => 'localhost',
		'port' => 8098,
		'bucket' => 'SimpleSAMLphp',
	];

Finally, the module can be specified as the Store in `config/config.php`
with the following setting:

		'store.type' => 'riak:Riak',

