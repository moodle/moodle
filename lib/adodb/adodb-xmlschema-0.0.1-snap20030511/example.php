<?PHP
require( "path_to_adodb/adodb-xmlschema.inc.php" );

// To build the schema, start by creating a normal ADOdb connection:
$db = ADONewConnection( 'mysql' );
$db->Connect( 'localhost', 'someuser', '', 'schematest' );

// Create the schema object and build the query array.
$schema = new adoSchema( $db );

// Build the SQL array
$sql = $schema->ParseSchema( "example.xml" );

print "Here's the SQL to do the build:\n";
print_r( $sql );
print "\n";

// Execute the SQL on the database
$result = $schema->ExecuteSchema( $sql );

// Finally, clean up after the XML parser
// (PHP won't do this for you!)
$schema->Destroy();
?>