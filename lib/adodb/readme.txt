>> ADODB Library for PHP4

(c) 2000-2002 John Lim (jlim@natsoft.com.my)

Released under both BSD and GNU Lesser GPL library license. 
This means you can use it in proprietary products.
 
 
>> Introduction

PHP's database access functions are not standardised. This creates a 
need for a database class library to hide the differences between the 
different databases (encapsulate the differences) so we can easily 
switch databases.

We currently support MySQL, Interbase, Sybase, PostgreSQL, Oracle, 
Microsoft SQL server,  Foxpro ODBC, Access ODBC, Informix, DB2 ODBC,
Sybase SQL Anywhere, generic ODBC and Microsoft's ADO. 

We hope more people will contribute drivers to support other databases.


>> Documentation and Examples

Refer to readme.htm for full documentation and examples. There is also a 
tutorial tute.htm that contrasts ADODB code with mysql code.


>>> Files
Adodb.inc.php is the main file. You need to include only this file.

Adodb-*.inc.php are the database specific driver code.

Test.php contains a list of test commands to exercise the class library.

Adodb-session.php is the PHP4 session handling code.

Testdatabases.inc.php contains the list of databases to apply the tests on.

Benchmark.php is a simple benchmark to test the throughput of a simple SELECT 
statement for databases described in testdatabases.inc.php. The benchmark
tables are created in test.php.

readme.htm is the main documentation.

tute.htm is the tutorial.


>> More Info

For more information, including installation see readme.htm


>> Feature Requests and Bug Reports

Email to jlim@natsoft.com.my 


 