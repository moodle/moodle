<?PHP

// V4.20 22 Feb 2004

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

// Build the SQL array
$sql = $schema->ParseSchema( "xmlschema.xml" );

print "Here's the SQL to do the build:\n<pre>";
print_r( $sql );
print "</pre>\n";

// Execute the SQL on the database
//$result = $schema->ExecuteSchema( $sql );

// Finally, clean up after the XML parser
// (PHP won't do this for you!)
//$schema->Destroy();
?>