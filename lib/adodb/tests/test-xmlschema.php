<?PHP

// V4.50 6 July 2004

error_reporting(E_ALL);

require( "../adodb-xmlschema.inc.php" );

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


$db2 = ADONewConnection('mssql');
$db2->Connect('localhost','sa','natsoft','northwind') || die("Fail 2");

$db2->Execute("drop table simple_table");


print "<b>SQL to build xmlschema-mssql.xml</b>:\n<pre>";

$schema = new adoSchema( $db2 );
$sql = $schema->ParseSchema( "xmlschema-mssql.xml" );

print_r( $sql );
print "</pre>\n";

$db2->debug=1;

$db2->Execute($sql[0]);
?>