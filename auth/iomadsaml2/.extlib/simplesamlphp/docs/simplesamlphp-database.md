SimpleSAML\Database
=============================

<!-- 
	This file is written in Markdown syntax. 
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

Purpose
-------
This document covers the SimpleSAML\Database class and is only relevant to anyone writing code for SimpleSAMLphp, including modules, that require a database connection.

The Database class provides a single class that can be used to connect to a database which can be shared by anything within SimpleSAMLphp.

Getting Started
---------------
If you are just using the already configured database, which would normally be the case, all you need to do is get the global instance of the Database class.

	$db = \SimpleSAML\Database::getInstance();

If there is a requirement to connect to an alternate database server (ex. authenticating users that exist on a different SQL server or database) you can specify an alternate configuration.

	$config = new \SimpleSAML\Configuration($myconfigarray, "mymodule/lib/Auth/Source/myauth.php");
	$db = \SimpleSAML\Database::getInstance($config);

That will create a new instance of the database, separate from the global instance, specific to the configuration defined in $myconfigarray. If you are going to specify an alternate config, your configuration array must contain the same keys that exist in the master config (database.dsn, database.username, database.password, database.prefix, etc).

Database Prefix
---------------
Administrators can add a prefix to all the table names that this database classes accesses and you should take that in account when querying. Assuming that a prefix has been configured as "sp_":

	$table = $db->applyPrefix("saml20_idp_hosted");

$table would be set to "sp_saml20_idp_hosted"

Querying The Database
---------------------
You can query the database through two public functions read() and write() which are fairly self-explanitory when it comes to determining which one to use when querying.

### Writing to The Database
Since the database class allows administrators to configure master and slave database servers, the write function will always use the master database connection.

The write function takes 2 parameters: SQL, params.

	$table = $db->applyPrefix("test");
	$values = [
		'id' => 20,
		'data' => 'Some data',
	];
	
	$query = $db->write("INSERT INTO $table (id, data) VALUES (:id, :data)", $values);

The values specified in the $values array will be bound to the placeholders and will be executed on the master. By default, values are binded as PDO::PARAM_STR. If you need to override this, you can specify it in the values array.

	$table = $db->applyPrefix("test");
	$values = [
		'id' => [20, PDO::PARAM_INT],
		'data' => 'Some data',
	];
	
	$query = $db->write("INSERT INTO $table (id, data) VALUES (:id, :data)", $values);

You can also skip usage of prepared statements. You should **only** use this if you have a statement that has no user input (ex. CREATE TABLE). If the params variable is explicity set to false, it will skip usage of prepared statements. This is only available when writing to the database.

	$table = $db->applyPrefix("test");
	$query = $db->write("CREATE TABLE IF NOT EXISTS $table (id INT(16) NOT NULL, data TEXT NOT NULL)", false);

### Reading The Database
Since the database class allows administrators to configure master and slave database servers, the read function will randomly select a slave server to query. If no slaves are configured, it will read from the master.

The read function takes 2 parameters: SQL, params.

	$table = $db->applyPrefix("test");
	$values = [
		'id' => 20,
	];
	
	$query = $db->read("SELECT * FROM $table WHERE id = :id", $values);

The values specified in the $values array will be bound to the placeholders and will be executed on the selected slave. By default, values are binded as PDO::PARAM_STR. If you need to override this, you can specify it in the values array.

	$table = $db->applyPrefix("test");
	$values = [
		'id' => [20, PDO::PARAM_INT],
	];
	
	$query = $db->read("SELECT * FROM $table WHERE id = :id", $values);
