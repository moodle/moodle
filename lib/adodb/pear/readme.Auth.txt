From: Rich Tango-Lowy (richtl#arscognita.com)
Date: Sat, May 29, 2004 11:20 am

OK, I hacked out an ADOdb container for PEAR-Auth. The error handling's 
a bit of a mess, but all the methods work.

Copy ADOdb.php to your pear/Auth/Container/ directory.

Use the ADOdb container exactly as you would the DB
container, but specify 'ADOdb' instead of 'DB':

$dsn = "mysql://myuser:mypass@localhost/authdb";
$a = new Auth("ADOdb", $dsn, "loginFunction");


-------------------

John Lim adds:

See http://pear.php.net/manual/en/package.authentication.php
