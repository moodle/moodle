<?PHP

// V4.50 6 July 2004

error_reporting(E_ALL);
include_once( "../adodb.inc.php" );
include_once( "../adodb-xmlschema.inc.php" );

// To build the schema, start by creating a normal ADOdb connection:
$db = ADONewConnection( 'mysql' );
$db->Connect( 'localhost', 'root', '', 'schematest' );

// To create a schema object and build the query array.
$schema = new adoSchema( $db );

// To upgrade an existing schema object, use the following 
// To upgrade an existing database to the provided schema,
// uncomment the following line:
#$schema->upgradeSchema();

print "<b>SQL to build xmlschema.xml</b>:\n<pre>";
// Build the SQL array
$sql = $schema->ParseSchema( "xmlschema.xml" );

print_r( $sql );
print "</pre>\n";

// Execute the SQL on the database
//$result = $schema->ExecuteSchema( $sql );

// Finally, clean up after the XML parser
// (PHP won't do this for you!)
//$schema->Destroy();



print "<b>SQL to build xmlschema-mssql.xml</b>:\n<pre>";

$db2 = ADONewConnection('mssql');
$db2->Connect('','adodb','natsoft','northwind') || die("Fail 2");

$db2->Execute("drop table simple_table");

$schema = new adoSchema( $db2 );
$sql = $schema->ParseSchema( "xmlschema-mssql.xml" );

print_r( $sql );
print "</pre>\n";

$db2->debug=1;

foreach ($sql as $s)
$db2->Execute($s);
?>