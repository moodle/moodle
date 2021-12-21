<?php
// Copyright (c) 2004 ars Cognita Inc., all rights reserved
/* ******************************************************************************
    Released under both BSD license and Lesser GPL library license.
 	Whenever there is any discrepancy between the two licenses,
 	the BSD license will take precedence.
*******************************************************************************/
/**
 * xmlschema is a class that allows the user to quickly and easily
 * build a database on any ADOdb-supported platform using a simple
 * XML schema.
 *
 * Last Editor: $Author: jlim $
 * @author Richard Tango-Lowy & Dan Cech
 * @version $Revision: 1.12 $
 *
 * @package axmls
 * @tutorial getting_started.pkg
 */

function _file_get_contents($file)
{
 	if (function_exists('file_get_contents')) return file_get_contents($file);

	$f = fopen($file,'r');
	if (!$f) return '';
	$t = '';

	while ($s = fread($f,100000)) $t .= $s;
	fclose($f);
	return $t;
}


/**
* Debug on or off
*/
if( !defined( 'XMLS_DEBUG' ) ) {
	define( 'XMLS_DEBUG', FALSE );
}

/**
* Default prefix key
*/
if( !defined( 'XMLS_PREFIX' ) ) {
	define( 'XMLS_PREFIX', '%%P' );
}

/**
* Maximum length allowed for object prefix
*/
if( !defined( 'XMLS_PREFIX_MAXLEN' ) ) {
	define( 'XMLS_PREFIX_MAXLEN', 10 );
}

/**
* Execute SQL inline as it is generated
*/
if( !defined( 'XMLS_EXECUTE_INLINE' ) ) {
	define( 'XMLS_EXECUTE_INLINE', FALSE );
}

/**
* Continue SQL Execution if an error occurs?
*/
if( !defined( 'XMLS_CONTINUE_ON_ERROR' ) ) {
	define( 'XMLS_CONTINUE_ON_ERROR', FALSE );
}

/**
* Current Schema Version
*/
if( !defined( 'XMLS_SCHEMA_VERSION' ) ) {
	define( 'XMLS_SCHEMA_VERSION', '0.2' );
}

/**
* Default Schema Version.  Used for Schemas without an explicit version set.
*/
if( !defined( 'XMLS_DEFAULT_SCHEMA_VERSION' ) ) {
	define( 'XMLS_DEFAULT_SCHEMA_VERSION', '0.1' );
}

/**
* Default Schema Version.  Used for Schemas without an explicit version set.
*/
if( !defined( 'XMLS_DEFAULT_UPGRADE_METHOD' ) ) {
	define( 'XMLS_DEFAULT_UPGRADE_METHOD', 'ALTER' );
}

/**
* Include the main ADODB library
*/
if( !defined( '_ADODB_LAYER' ) ) {
	require( 'adodb.inc.php' );
	require( 'adodb-datadict.inc.php' );
}

/**
* Abstract DB Object. This class provides basic methods for database objects, such
* as tables and indexes.
*
* @package axmls
* @access private
*/
class dbObject {

	/**
	* var object Parent
	*/
	var $parent;

	/**
	* var string current element
	*/
	var $currentElement;

	/**
	* NOP
	*/
	function __construct( &$parent, $attributes = NULL ) {
		$this->parent = $parent;
	}

	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {

	}

	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {

	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {

	}

	function create(&$xmls) {
		return array();
	}

	/**
	* Destroys the object
	*/
	function destroy() {
	}

	/**
	* Checks whether the specified RDBMS is supported by the current
	* database object or its ranking ancestor.
	*
	* @param string $platform RDBMS platform name (from ADODB platform list).
	* @return boolean TRUE if RDBMS is supported; otherwise returns FALSE.
	*/
	function supportedPlatform( $platform = NULL ) {
		return is_object( $this->parent ) ? $this->parent->supportedPlatform( $platform ) : TRUE;
	}

	/**
	* Returns the prefix set by the ranking ancestor of the database object.
	*
	* @param string $name Prefix string.
	* @return string Prefix.
	*/
	function prefix( $name = '' ) {
		return is_object( $this->parent ) ? $this->parent->prefix( $name ) : $name;
	}

	/**
	* Extracts a field ID from the specified field.
	*
	* @param string $field Field.
	* @return string Field ID.
	*/
	function FieldID( $field ) {
		return strtoupper( preg_replace( '/^`(.+)`$/', '$1', $field ) );
	}
}

/**
* Creates a table object in ADOdb's datadict format
*
* This class stores information about a database table. As charactaristics
* of the table are loaded from the external source, methods and properties
* of this class are used to build up the table description in ADOdb's
* datadict format.
*
* @package axmls
* @access private
*/
class dbTable extends dbObject {

	/**
	* @var string Table name
	*/
	var $name;

	/**
	* @var array Field specifier: Meta-information about each field
	*/
	var $fields = array();

	/**
	* @var array List of table indexes.
	*/
	var $indexes = array();

	/**
	* @var array Table options: Table-level options
	*/
	var $opts = array();

	/**
	* @var string Field index: Keeps track of which field is currently being processed
	*/
	var $current_field;

	/**
	* @var boolean Mark table for destruction
	* @access private
	*/
	var $drop_table;

	/**
	* @var boolean Mark field for destruction (not yet implemented)
	* @access private
	*/
	var $drop_field = array();

	/**
	* Iniitializes a new table object.
	*
	* @param string $prefix DB Object prefix
	* @param array $attributes Array of table attributes.
	*/
	function __construct( &$parent, $attributes = NULL ) {
		$this->parent = $parent;
		$this->name = $this->prefix($attributes['NAME']);
	}

	/**
	* XML Callback to process start elements. Elements currently
	* processed are: INDEX, DROP, FIELD, KEY, NOTNULL, AUTOINCREMENT & DEFAULT.
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper( $tag );

		switch( $this->currentElement ) {
			case 'INDEX':
				if( !isset( $attributes['PLATFORM'] ) OR $this->supportedPlatform( $attributes['PLATFORM'] ) ) {
					$index = $this->addIndex( $attributes );
					xml_set_object( $parser,  $index );
				}
				break;
			case 'DATA':
				if( !isset( $attributes['PLATFORM'] ) OR $this->supportedPlatform( $attributes['PLATFORM'] ) ) {
					$data = $this->addData( $attributes );
					xml_set_object( $parser, $data );
				}
				break;
			case 'DROP':
				$this->drop();
				break;
			case 'FIELD':
				// Add a field
				$fieldName = $attributes['NAME'];
				$fieldType = $attributes['TYPE'];
				$fieldSize = isset( $attributes['SIZE'] ) ? $attributes['SIZE'] : NULL;
				$fieldOpts = isset( $attributes['OPTS'] ) ? $attributes['OPTS'] : NULL;

				$this->addField( $fieldName, $fieldType, $fieldSize, $fieldOpts );
				break;
			case 'KEY':
			case 'NOTNULL':
			case 'AUTOINCREMENT':
				// Add a field option
				$this->addFieldOpt( $this->current_field, $this->currentElement );
				break;
			case 'DEFAULT':
				// Add a field option to the table object

				// Work around ADOdb datadict issue that misinterprets empty strings.
				if( $attributes['VALUE'] == '' ) {
					$attributes['VALUE'] = " '' ";
				}

				$this->addFieldOpt( $this->current_field, $this->currentElement, $attributes['VALUE'] );
				break;
			case 'DEFDATE':
			case 'DEFTIMESTAMP':
				// Add a field option to the table object
				$this->addFieldOpt( $this->current_field, $this->currentElement );
				break;
			default:
				// print_r( array( $tag, $attributes ) );
		}
	}

	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
		switch( $this->currentElement ) {
			// Table constraint
			case 'CONSTRAINT':
				if( isset( $this->current_field ) ) {
					$this->addFieldOpt( $this->current_field, $this->currentElement, $cdata );
				} else {
					$this->addTableOpt( $cdata );
				}
				break;
			// Table option
			case 'OPT':
				$this->addTableOpt( $cdata );
				break;
			default:

		}
	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';

		switch( strtoupper( $tag ) ) {
			case 'TABLE':
				$this->parent->addSQL( $this->create( $this->parent ) );
				xml_set_object( $parser, $this->parent );
				$this->destroy();
				break;
			case 'FIELD':
				unset($this->current_field);
				break;

		}
	}

	/**
	* Adds an index to a table object
	*
	* @param array $attributes Index attributes
	* @return object dbIndex object
	*/
	function addIndex( $attributes ) {
		$name = strtoupper( $attributes['NAME'] );
		$this->indexes[$name] = new dbIndex( $this, $attributes );
		return $this->indexes[$name];
	}

	/**
	* Adds data to a table object
	*
	* @param array $attributes Data attributes
	* @return object dbData object
	*/
	function addData( $attributes ) {
		if( !isset( $this->data ) ) {
			$this->data = new dbData( $this, $attributes );
		}
		return $this->data;
	}

	/**
	* Adds a field to a table object
	*
	* $name is the name of the table to which the field should be added.
	* $type is an ADODB datadict field type. The following field types
	* are supported as of ADODB 3.40:
	* 	- C:  varchar
	*	- X:  CLOB (character large object) or largest varchar size
	*	   if CLOB is not supported
	*	- C2: Multibyte varchar
	*	- X2: Multibyte CLOB
	*	- B:  BLOB (binary large object)
	*	- D:  Date (some databases do not support this, and we return a datetime type)
	*	- T:  Datetime or Timestamp
	*	- L:  Integer field suitable for storing booleans (0 or 1)
	*	- I:  Integer (mapped to I4)
	*	- I1: 1-byte integer
	*	- I2: 2-byte integer
	*	- I4: 4-byte integer
	*	- I8: 8-byte integer
	*	- F:  Floating point number
	*	- N:  Numeric or decimal number
	*
	* @param string $name Name of the table to which the field will be added.
	* @param string $type	ADODB datadict field type.
	* @param string $size	Field size
	* @param array $opts	Field options array
	* @return array Field specifier array
	*/
	function addField( $name, $type, $size = NULL, $opts = NULL ) {
		$field_id = $this->FieldID( $name );

		// Set the field index so we know where we are
		$this->current_field = $field_id;

		// Set the field name (required)
		$this->fields[$field_id]['NAME'] = $name;

		// Set the field type (required)
		$this->fields[$field_id]['TYPE'] = $type;

		// Set the field size (optional)
		if( isset( $size ) ) {
			$this->fields[$field_id]['SIZE'] = $size;
		}

		// Set the field options
		if( isset( $opts ) ) {
			$this->fields[$field_id]['OPTS'][] = $opts;
		}
	}

	/**
	* Adds a field option to the current field specifier
	*
	* This method adds a field option allowed by the ADOdb datadict
	* and appends it to the given field.
	*
	* @param string $field	Field name
	* @param string $opt ADOdb field option
	* @param mixed $value Field option value
	* @return array Field specifier array
	*/
	function addFieldOpt( $field, $opt, $value = NULL ) {
		if( !isset( $value ) ) {
			$this->fields[$this->FieldID( $field )]['OPTS'][] = $opt;
		// Add the option and value
		} else {
			$this->fields[$this->FieldID( $field )]['OPTS'][] = array( $opt => $value );
		}
	}

	/**
	* Adds an option to the table
	*
	* This method takes a comma-separated list of table-level options
	* and appends them to the table object.
	*
	* @param string $opt Table option
	* @return array Options
	*/
	function addTableOpt( $opt ) {
		if(isset($this->currentPlatform)) {
			$this->opts[$this->parent->db->databaseType] = $opt;
		}
		return $this->opts;
	}


	/**
	* Generates the SQL that will create the table in the database
	*
	* @param object $xmls adoSchema object
	* @return array Array containing table creation SQL
	*/
	function create( &$xmls ) {
		$sql = array();

		// drop any existing indexes
		if( is_array( $legacy_indexes = $xmls->dict->MetaIndexes( $this->name ) ) ) {
			foreach( $legacy_indexes as $index => $index_details ) {
				$sql[] = $xmls->dict->DropIndexSQL( $index, $this->name );
			}
		}

		// remove fields to be dropped from table object
		foreach( $this->drop_field as $field ) {
			unset( $this->fields[$field] );
		}

		// if table exists
		if( is_array( $legacy_fields = $xmls->dict->MetaColumns( $this->name ) ) ) {
			// drop table
			if( $this->drop_table ) {
				$sql[] = $xmls->dict->DropTableSQL( $this->name );

				return $sql;
			}

			// drop any existing fields not in schema
			foreach( $legacy_fields as $field_id => $field ) {
				if( !isset( $this->fields[$field_id] ) ) {
					$sql[] = $xmls->dict->DropColumnSQL( $this->name, '`'.$field->name.'`' );
				}
			}
		// if table doesn't exist
		} else {
			if( $this->drop_table ) {
				return $sql;
			}

			$legacy_fields = array();
		}

		// Loop through the field specifier array, building the associative array for the field options
		$fldarray = array();

		foreach( $this->fields as $field_id => $finfo ) {
			// Set an empty size if it isn't supplied
			if( !isset( $finfo['SIZE'] ) ) {
				$finfo['SIZE'] = '';
			}

			// Initialize the field array with the type and size
			$fldarray[$field_id] = array(
				'NAME' => $finfo['NAME'],
				'TYPE' => $finfo['TYPE'],
				'SIZE' => $finfo['SIZE']
			);

			// Loop through the options array and add the field options.
			if( isset( $finfo['OPTS'] ) ) {
				foreach( $finfo['OPTS'] as $opt ) {
					// Option has an argument.
					if( is_array( $opt ) ) {
						$key = key( $opt );
						$value = $opt[key( $opt )];
						@$fldarray[$field_id][$key] .= $value;
					// Option doesn't have arguments
					} else {
						$fldarray[$field_id][$opt] = $opt;
					}
				}
			}
		}

		if( empty( $legacy_fields ) ) {
			// Create the new table
			$sql[] = $xmls->dict->CreateTableSQL( $this->name, $fldarray, $this->opts );
			logMsg( end( $sql ), 'Generated CreateTableSQL' );
		} else {
			// Upgrade an existing table
			logMsg( "Upgrading {$this->name} using '{$xmls->upgrade}'" );
			switch( $xmls->upgrade ) {
				// Use ChangeTableSQL
				case 'ALTER':
					logMsg( 'Generated ChangeTableSQL (ALTERing table)' );
					$sql[] = $xmls->dict->ChangeTableSQL( $this->name, $fldarray, $this->opts );
					break;
				case 'REPLACE':
					logMsg( 'Doing upgrade REPLACE (testing)' );
					$sql[] = $xmls->dict->DropTableSQL( $this->name );
					$sql[] = $xmls->dict->CreateTableSQL( $this->name, $fldarray, $this->opts );
					break;
				// ignore table
				default:
					return array();
			}
		}

		foreach( $this->indexes as $index ) {
			$sql[] = $index->create( $xmls );
		}

		if( isset( $this->data ) ) {
			$sql[] = $this->data->create( $xmls );
		}

		return $sql;
	}

	/**
	* Marks a field or table for destruction
	*/
	function drop() {
		if( isset( $this->current_field ) ) {
			// Drop the current field
			logMsg( "Dropping field '{$this->current_field}' from table '{$this->name}'" );
			// $this->drop_field[$this->current_field] = $xmls->dict->DropColumnSQL( $this->name, $this->current_field );
			$this->drop_field[$this->current_field] = $this->current_field;
		} else {
			// Drop the current table
			logMsg( "Dropping table '{$this->name}'" );
			// $this->drop_table = $xmls->dict->DropTableSQL( $this->name );
			$this->drop_table = TRUE;
		}
	}
}

/**
* Creates an index object in ADOdb's datadict format
*
* This class stores information about a database index. As charactaristics
* of the index are loaded from the external source, methods and properties
* of this class are used to build up the index description in ADOdb's
* datadict format.
*
* @package axmls
* @access private
*/
class dbIndex extends dbObject {

	/**
	* @var string	Index name
	*/
	var $name;

	/**
	* @var array	Index options: Index-level options
	*/
	var $opts = array();

	/**
	* @var array	Indexed fields: Table columns included in this index
	*/
	var $columns = array();

	/**
	* @var boolean Mark index for destruction
	* @access private
	*/
	var $drop = FALSE;

	/**
	* Initializes the new dbIndex object.
	*
	* @param object $parent Parent object
	* @param array $attributes Attributes
	*
	* @internal
	*/
	function __construct( &$parent, $attributes = NULL ) {
		$this->parent = $parent;

		$this->name = $this->prefix ($attributes['NAME']);
	}

	/**
	* XML Callback to process start elements
	*
	* Processes XML opening tags.
	* Elements currently processed are: DROP, CLUSTERED, BITMAP, UNIQUE, FULLTEXT & HASH.
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper( $tag );

		switch( $this->currentElement ) {
			case 'DROP':
				$this->drop();
				break;
			case 'CLUSTERED':
			case 'BITMAP':
			case 'UNIQUE':
			case 'FULLTEXT':
			case 'HASH':
				// Add index Option
				$this->addIndexOpt( $this->currentElement );
				break;
			default:
				// print_r( array( $tag, $attributes ) );
		}
	}

	/**
	* XML Callback to process CDATA elements
	*
	* Processes XML cdata.
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
		switch( $this->currentElement ) {
			// Index field name
			case 'COL':
				$this->addField( $cdata );
				break;
			default:

		}
	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';

		switch( strtoupper( $tag ) ) {
			case 'INDEX':
				xml_set_object( $parser, $this->parent );
				break;
		}
	}

	/**
	* Adds a field to the index
	*
	* @param string $name Field name
	* @return string Field list
	*/
	function addField( $name ) {
		$this->columns[$this->FieldID( $name )] = $name;

		// Return the field list
		return $this->columns;
	}

	/**
	* Adds options to the index
	*
	* @param string $opt Comma-separated list of index options.
	* @return string Option list
	*/
	function addIndexOpt( $opt ) {
		$this->opts[] = $opt;

		// Return the options list
		return $this->opts;
	}

	/**
	* Generates the SQL that will create the index in the database
	*
	* @param object $xmls adoSchema object
	* @return array Array containing index creation SQL
	*/
	function create( &$xmls ) {
		if( $this->drop ) {
			return NULL;
		}

		// eliminate any columns that aren't in the table
		foreach( $this->columns as $id => $col ) {
			if( !isset( $this->parent->fields[$id] ) ) {
				unset( $this->columns[$id] );
			}
		}

		return $xmls->dict->CreateIndexSQL( $this->name, $this->parent->name, $this->columns, $this->opts );
	}

	/**
	* Marks an index for destruction
	*/
	function drop() {
		$this->drop = TRUE;
	}
}

/**
* Creates a data object in ADOdb's datadict format
*
* This class stores information about table data.
*
* @package axmls
* @access private
*/
class dbData extends dbObject {

	var $data = array();

	var $row;

	/**
	* Initializes the new dbIndex object.
	*
	* @param object $parent Parent object
	* @param array $attributes Attributes
	*
	* @internal
	*/
	function __construct( &$parent, $attributes = NULL ) {
		$this->parent = $parent;
	}

	/**
	* XML Callback to process start elements
	*
	* Processes XML opening tags.
	* Elements currently processed are: DROP, CLUSTERED, BITMAP, UNIQUE, FULLTEXT & HASH.
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper( $tag );

		switch( $this->currentElement ) {
			case 'ROW':
				$this->row = count( $this->data );
				$this->data[$this->row] = array();
				break;
			case 'F':
				$this->addField($attributes);
			default:
				// print_r( array( $tag, $attributes ) );
		}
	}

	/**
	* XML Callback to process CDATA elements
	*
	* Processes XML cdata.
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
		switch( $this->currentElement ) {
			// Index field name
			case 'F':
				$this->addData( $cdata );
				break;
			default:

		}
	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';

		switch( strtoupper( $tag ) ) {
			case 'DATA':
				xml_set_object( $parser, $this->parent );
				break;
		}
	}

	/**
	* Adds a field to the index
	*
	* @param string $name Field name
	* @return string Field list
	*/
	function addField( $attributes ) {
		if( isset( $attributes['NAME'] ) ) {
			$name = $attributes['NAME'];
		} else {
			$name = count($this->data[$this->row]);
		}

		// Set the field index so we know where we are
		$this->current_field = $this->FieldID( $name );
	}

	/**
	* Adds options to the index
	*
	* @param string $opt Comma-separated list of index options.
	* @return string Option list
	*/
	function addData( $cdata ) {
		if( !isset( $this->data[$this->row] ) ) {
			$this->data[$this->row] = array();
		}

		if( !isset( $this->data[$this->row][$this->current_field] ) ) {
			$this->data[$this->row][$this->current_field] = '';
		}

		$this->data[$this->row][$this->current_field] .= $cdata;
	}

	/**
	* Generates the SQL that will create the index in the database
	*
	* @param object $xmls adoSchema object
	* @return array Array containing index creation SQL
	*/
	function create( &$xmls ) {
		$table = $xmls->dict->TableName($this->parent->name);
		$table_field_count = count($this->parent->fields);
		$sql = array();

		// eliminate any columns that aren't in the table
		foreach( $this->data as $row ) {
			$table_fields = $this->parent->fields;
			$fields = array();

			foreach( $row as $field_id => $field_data ) {
				if( !array_key_exists( $field_id, $table_fields ) ) {
					if( is_numeric( $field_id ) ) {
						$field_id = reset( array_keys( $table_fields ) );
					} else {
						continue;
					}
				}

				$name = $table_fields[$field_id]['NAME'];

				switch( $table_fields[$field_id]['TYPE'] ) {
					case 'C':
					case 'C2':
					case 'X':
					case 'X2':
						$fields[$name] = $xmls->db->qstr( $field_data );
						break;
					case 'I':
					case 'I1':
					case 'I2':
					case 'I4':
					case 'I8':
						$fields[$name] = intval($field_data);
						break;
					default:
						$fields[$name] = $field_data;
				}

				unset($table_fields[$field_id]);
			}

			// check that at least 1 column is specified
			if( empty( $fields ) ) {
				continue;
			}

			// check that no required columns are missing
			if( count( $fields ) < $table_field_count ) {
				foreach( $table_fields as $field ) {
					if (isset( $field['OPTS'] ))
						if( ( in_array( 'NOTNULL', $field['OPTS'] ) || in_array( 'KEY', $field['OPTS'] ) ) && !in_array( 'AUTOINCREMENT', $field['OPTS'] ) ) {
							continue(2);
						}
				}
			}

			$sql[] = 'INSERT INTO '. $table .' ('. implode( ',', array_keys( $fields ) ) .') VALUES ('. implode( ',', $fields ) .')';
		}

		return $sql;
	}
}

/**
* Creates the SQL to execute a list of provided SQL queries
*
* @package axmls
* @access private
*/
class dbQuerySet extends dbObject {

	/**
	* @var array	List of SQL queries
	*/
	var $queries = array();

	/**
	* @var string	String used to build of a query line by line
	*/
	var $query;

	/**
	* @var string	Query prefix key
	*/
	var $prefixKey = '';

	/**
	* @var boolean	Auto prefix enable (TRUE)
	*/
	var $prefixMethod = 'AUTO';

	/**
	* Initializes the query set.
	*
	* @param object $parent Parent object
	* @param array $attributes Attributes
	*/
	function __construct( &$parent, $attributes = NULL ) {
		$this->parent = $parent;

		// Overrides the manual prefix key
		if( isset( $attributes['KEY'] ) ) {
			$this->prefixKey = $attributes['KEY'];
		}

		$prefixMethod = isset( $attributes['PREFIXMETHOD'] ) ? strtoupper( trim( $attributes['PREFIXMETHOD'] ) ) : '';

		// Enables or disables automatic prefix prepending
		switch( $prefixMethod ) {
			case 'AUTO':
				$this->prefixMethod = 'AUTO';
				break;
			case 'MANUAL':
				$this->prefixMethod = 'MANUAL';
				break;
			case 'NONE':
				$this->prefixMethod = 'NONE';
				break;
		}
	}

	/**
	* XML Callback to process start elements. Elements currently
	* processed are: QUERY.
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		$this->currentElement = strtoupper( $tag );

		switch( $this->currentElement ) {
			case 'QUERY':
				// Create a new query in a SQL queryset.
				// Ignore this query set if a platform is specified and it's different than the
				// current connection platform.
				if( !isset( $attributes['PLATFORM'] ) OR $this->supportedPlatform( $attributes['PLATFORM'] ) ) {
					$this->newQuery();
				} else {
					$this->discardQuery();
				}
				break;
			default:
				// print_r( array( $tag, $attributes ) );
		}
	}

	/**
	* XML Callback to process CDATA elements
	*/
	function _tag_cdata( &$parser, $cdata ) {
		switch( $this->currentElement ) {
			// Line of queryset SQL data
			case 'QUERY':
				$this->buildQuery( $cdata );
				break;
			default:

		}
	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	*/
	function _tag_close( &$parser, $tag ) {
		$this->currentElement = '';

		switch( strtoupper( $tag ) ) {
			case 'QUERY':
				// Add the finished query to the open query set.
				$this->addQuery();
				break;
			case 'SQL':
				$this->parent->addSQL( $this->create( $this->parent ) );
				xml_set_object( $parser, $this->parent );
				$this->destroy();
				break;
			default:

		}
	}

	/**
	* Re-initializes the query.
	*
	* @return boolean TRUE
	*/
	function newQuery() {
		$this->query = '';

		return TRUE;
	}

	/**
	* Discards the existing query.
	*
	* @return boolean TRUE
	*/
	function discardQuery() {
		unset( $this->query );

		return TRUE;
	}

	/**
	* Appends a line to a query that is being built line by line
	*
	* @param string $data Line of SQL data or NULL to initialize a new query
	* @return string SQL query string.
	*/
	function buildQuery( $sql = NULL ) {
		if( !isset( $this->query ) OR empty( $sql ) ) {
			return FALSE;
		}

		$this->query .= $sql;

		return $this->query;
	}

	/**
	* Adds a completed query to the query list
	*
	* @return string	SQL of added query
	*/
	function addQuery() {
		if( !isset( $this->query ) ) {
			return FALSE;
		}

		$this->queries[] = $return = trim($this->query);

		unset( $this->query );

		return $return;
	}

	/**
	* Creates and returns the current query set
	*
	* @param object $xmls adoSchema object
	* @return array Query set
	*/
	function create( &$xmls ) {
		foreach( $this->queries as $id => $query ) {
			switch( $this->prefixMethod ) {
				case 'AUTO':
					// Enable auto prefix replacement

					// Process object prefix.
					// Evaluate SQL statements to prepend prefix to objects
					$query = $this->prefixQuery( '/^\s*((?is)INSERT\s+(INTO\s+)?)((\w+\s*,?\s*)+)(\s.*$)/', $query, $xmls->objectPrefix );
					$query = $this->prefixQuery( '/^\s*((?is)UPDATE\s+(FROM\s+)?)((\w+\s*,?\s*)+)(\s.*$)/', $query, $xmls->objectPrefix );
					$query = $this->prefixQuery( '/^\s*((?is)DELETE\s+(FROM\s+)?)((\w+\s*,?\s*)+)(\s.*$)/', $query, $xmls->objectPrefix );

					// SELECT statements aren't working yet
					#$data = preg_replace( '/(?ias)(^\s*SELECT\s+.*\s+FROM)\s+(\W\s*,?\s*)+((?i)\s+WHERE.*$)/', "\1 $prefix\2 \3", $data );

				case 'MANUAL':
					// If prefixKey is set and has a value then we use it to override the default constant XMLS_PREFIX.
					// If prefixKey is not set, we use the default constant XMLS_PREFIX
					if( isset( $this->prefixKey ) AND( $this->prefixKey !== '' ) ) {
						// Enable prefix override
						$query = str_replace( $this->prefixKey, $xmls->objectPrefix, $query );
					} else {
						// Use default replacement
						$query = str_replace( XMLS_PREFIX , $xmls->objectPrefix, $query );
					}
			}

			$this->queries[$id] = trim( $query );
		}

		// Return the query set array
		return $this->queries;
	}

	/**
	* Rebuilds the query with the prefix attached to any objects
	*
	* @param string $regex Regex used to add prefix
	* @param string $query SQL query string
	* @param string $prefix Prefix to be appended to tables, indices, etc.
	* @return string Prefixed SQL query string.
	*/
	function prefixQuery( $regex, $query, $prefix = NULL ) {
		if( !isset( $prefix ) ) {
			return $query;
		}

		if( preg_match( $regex, $query, $match ) ) {
			$preamble = $match[1];
			$postamble = $match[5];
			$objectList = explode( ',', $match[3] );
			// $prefix = $prefix . '_';

			$prefixedList = '';

			foreach( $objectList as $object ) {
				if( $prefixedList !== '' ) {
					$prefixedList .= ', ';
				}

				$prefixedList .= $prefix . trim( $object );
			}

			$query = $preamble . ' ' . $prefixedList . ' ' . $postamble;
		}

		return $query;
	}
}

/**
* Loads and parses an XML file, creating an array of "ready-to-run" SQL statements
*
* This class is used to load and parse the XML file, to create an array of SQL statements
* that can be used to build a database, and to build the database using the SQL array.
*
* @tutorial getting_started.pkg
*
* @author Richard Tango-Lowy & Dan Cech
* @version $Revision: 1.12 $
*
* @package axmls
*/
class adoSchema {

	/**
	* @var array	Array containing SQL queries to generate all objects
	* @access private
	*/
	var $sqlArray;

	/**
	* @var object	ADOdb connection object
	* @access private
	*/
	var $db;

	/**
	* @var object	ADOdb Data Dictionary
	* @access private
	*/
	var $dict;

	/**
	* @var string Current XML element
	* @access private
	*/
	var $currentElement = '';

	/**
	* @var string If set (to 'ALTER' or 'REPLACE'), upgrade an existing database
	* @access private
	*/
	var $upgrade = '';

	/**
	* @var string Optional object prefix
	* @access private
	*/
	var $objectPrefix = '';

	/**
	* @var long	System debug
	* @access private
	*/
	var $debug;

	/**
	* @var string Regular expression to find schema version
	* @access private
	*/
	var $versionRegex = '/<schema.*?( version="([^"]*)")?.*?>/';

	/**
	* @var string Current schema version
	* @access private
	*/
	var $schemaVersion;

	/**
	* @var int	Success of last Schema execution
	*/
	var $success;

	/**
	* @var bool	Execute SQL inline as it is generated
	*/
	var $executeInline;

	/**
	* @var bool	Continue SQL execution if errors occur
	*/
	var $continueOnError;

	/**
	* Creates an adoSchema object
	*
	* Creating an adoSchema object is the first step in processing an XML schema.
	* The only parameter is an ADOdb database connection object, which must already
	* have been created.
	*
	* @param object $db ADOdb database connection object.
	*/
	function __construct( $db ) {
		$this->db = $db;
		$this->debug = $this->db->debug;
		$this->dict = NewDataDictionary( $this->db );
		$this->sqlArray = array();
		$this->schemaVersion = XMLS_SCHEMA_VERSION;
		$this->executeInline( XMLS_EXECUTE_INLINE );
		$this->continueOnError( XMLS_CONTINUE_ON_ERROR );
		$this->setUpgradeMethod();
	}

	/**
	* Sets the method to be used for upgrading an existing database
	*
	* Use this method to specify how existing database objects should be upgraded.
	* The method option can be set to ALTER, REPLACE, BEST, or NONE. ALTER attempts to
	* alter each database object directly, REPLACE attempts to rebuild each object
	* from scratch, BEST attempts to determine the best upgrade method for each
	* object, and NONE disables upgrading.
	*
	* This method is not yet used by AXMLS, but exists for backward compatibility.
	* The ALTER method is automatically assumed when the adoSchema object is
	* instantiated; other upgrade methods are not currently supported.
	*
	* @param string $method Upgrade method (ALTER|REPLACE|BEST|NONE)
	* @returns string Upgrade method used
	*/
	function SetUpgradeMethod( $method = '' ) {
		if( !is_string( $method ) ) {
			return FALSE;
		}

		$method = strtoupper( $method );

		// Handle the upgrade methods
		switch( $method ) {
			case 'ALTER':
				$this->upgrade = $method;
				break;
			case 'REPLACE':
				$this->upgrade = $method;
				break;
			case 'BEST':
				$this->upgrade = 'ALTER';
				break;
			case 'NONE':
				$this->upgrade = 'NONE';
				break;
			default:
				// Use default if no legitimate method is passed.
				$this->upgrade = XMLS_DEFAULT_UPGRADE_METHOD;
		}

		return $this->upgrade;
	}

	/**
	* Enables/disables inline SQL execution.
	*
	* Call this method to enable or disable inline execution of the schema. If the mode is set to TRUE (inline execution),
	* AXMLS applies the SQL to the database immediately as each schema entity is parsed. If the mode
	* is set to FALSE (post execution), AXMLS parses the entire schema and you will need to call adoSchema::ExecuteSchema()
	* to apply the schema to the database.
	*
	* @param bool $mode execute
	* @return bool current execution mode
	*
	* @see ParseSchema(), ExecuteSchema()
	*/
	function ExecuteInline( $mode = NULL ) {
		if( is_bool( $mode ) ) {
			$this->executeInline = $mode;
		}

		return $this->executeInline;
	}

	/**
	* Enables/disables SQL continue on error.
	*
	* Call this method to enable or disable continuation of SQL execution if an error occurs.
	* If the mode is set to TRUE (continue), AXMLS will continue to apply SQL to the database, even if an error occurs.
	* If the mode is set to FALSE (halt), AXMLS will halt execution of generated sql if an error occurs, though parsing
	* of the schema will continue.
	*
	* @param bool $mode execute
	* @return bool current continueOnError mode
	*
	* @see addSQL(), ExecuteSchema()
	*/
	function ContinueOnError( $mode = NULL ) {
		if( is_bool( $mode ) ) {
			$this->continueOnError = $mode;
		}

		return $this->continueOnError;
	}

	/**
	* Loads an XML schema from a file and converts it to SQL.
	*
	* Call this method to load the specified schema (see the DTD for the proper format) from
	* the filesystem and generate the SQL necessary to create the database described.
	* @see ParseSchemaString()
	*
	* @param string $file Name of XML schema file.
	* @param bool $returnSchema Return schema rather than parsing.
	* @return array Array of SQL queries, ready to execute
	*/
	function ParseSchema( $filename, $returnSchema = FALSE ) {
		return $this->ParseSchemaString( $this->ConvertSchemaFile( $filename ), $returnSchema );
	}

	/**
	* Loads an XML schema from a file and converts it to SQL.
	*
	* Call this method to load the specified schema from a file (see the DTD for the proper format)
	* and generate the SQL necessary to create the database described by the schema.
	*
	* @param string $file Name of XML schema file.
	* @param bool $returnSchema Return schema rather than parsing.
	* @return array Array of SQL queries, ready to execute.
	*
	* @deprecated Replaced by adoSchema::ParseSchema() and adoSchema::ParseSchemaString()
	* @see ParseSchema(), ParseSchemaString()
	*/
	function ParseSchemaFile( $filename, $returnSchema = FALSE ) {
		// Open the file
		if( !($fp = fopen( $filename, 'r' )) ) {
			// die( 'Unable to open file' );
			return FALSE;
		}

		// do version detection here
		if( $this->SchemaFileVersion( $filename ) != $this->schemaVersion ) {
			return FALSE;
		}

		if ( $returnSchema )
		{
			$xmlstring = '';
			while( $data = fread( $fp, 100000 ) ) {
				$xmlstring .= $data;
			}
			return $xmlstring;
		}

		$this->success = 2;

		$xmlParser = $this->create_parser();

		// Process the file
		while( $data = fread( $fp, 4096 ) ) {
			if( !xml_parse( $xmlParser, $data, feof( $fp ) ) ) {
				die( sprintf(
					"XML error: %s at line %d",
					xml_error_string( xml_get_error_code( $xmlParser) ),
					xml_get_current_line_number( $xmlParser)
				) );
			}
		}

		xml_parser_free( $xmlParser );

		return $this->sqlArray;
	}

	/**
	* Converts an XML schema string to SQL.
	*
	* Call this method to parse a string containing an XML schema (see the DTD for the proper format)
	* and generate the SQL necessary to create the database described by the schema.
	* @see ParseSchema()
	*
	* @param string $xmlstring XML schema string.
	* @param bool $returnSchema Return schema rather than parsing.
	* @return array Array of SQL queries, ready to execute.
	*/
	function ParseSchemaString( $xmlstring, $returnSchema = FALSE ) {
		if( !is_string( $xmlstring ) OR empty( $xmlstring ) ) {
			return FALSE;
		}

		// do version detection here
		if( $this->SchemaStringVersion( $xmlstring ) != $this->schemaVersion ) {
			return FALSE;
		}

		if ( $returnSchema )
		{
			return $xmlstring;
		}

		$this->success = 2;

		$xmlParser = $this->create_parser();

		if( !xml_parse( $xmlParser, $xmlstring, TRUE ) ) {
			die( sprintf(
				"XML error: %s at line %d",
				xml_error_string( xml_get_error_code( $xmlParser) ),
				xml_get_current_line_number( $xmlParser)
			) );
		}

		xml_parser_free( $xmlParser );

		return $this->sqlArray;
	}

	/**
	* Loads an XML schema from a file and converts it to uninstallation SQL.
	*
	* Call this method to load the specified schema (see the DTD for the proper format) from
	* the filesystem and generate the SQL necessary to remove the database described.
	* @see RemoveSchemaString()
	*
	* @param string $file Name of XML schema file.
	* @param bool $returnSchema Return schema rather than parsing.
	* @return array Array of SQL queries, ready to execute
	*/
	function RemoveSchema( $filename, $returnSchema = FALSE ) {
		return $this->RemoveSchemaString( $this->ConvertSchemaFile( $filename ), $returnSchema );
	}

	/**
	* Converts an XML schema string to uninstallation SQL.
	*
	* Call this method to parse a string containing an XML schema (see the DTD for the proper format)
	* and generate the SQL necessary to uninstall the database described by the schema.
	* @see RemoveSchema()
	*
	* @param string $schema XML schema string.
	* @param bool $returnSchema Return schema rather than parsing.
	* @return array Array of SQL queries, ready to execute.
	*/
	function RemoveSchemaString( $schema, $returnSchema = FALSE ) {

		// grab current version
		if( !( $version = $this->SchemaStringVersion( $schema ) ) ) {
			return FALSE;
		}

		return $this->ParseSchemaString( $this->TransformSchema( $schema, 'remove-' . $version), $returnSchema );
	}

	/**
	* Applies the current XML schema to the database (post execution).
	*
	* Call this method to apply the current schema (generally created by calling
	* ParseSchema() or ParseSchemaString() ) to the database (creating the tables, indexes,
	* and executing other SQL specified in the schema) after parsing.
	* @see ParseSchema(), ParseSchemaString(), ExecuteInline()
	*
	* @param array $sqlArray Array of SQL statements that will be applied rather than
	*		the current schema.
	* @param boolean $continueOnErr Continue to apply the schema even if an error occurs.
	* @returns integer 0 if failure, 1 if errors, 2 if successful.
	*/
	function ExecuteSchema( $sqlArray = NULL, $continueOnErr =  NULL ) {
		if( !is_bool( $continueOnErr ) ) {
			$continueOnErr = $this->ContinueOnError();
		}

		if( !isset( $sqlArray ) ) {
			$sqlArray = $this->sqlArray;
		}

		if( !is_array( $sqlArray ) ) {
			$this->success = 0;
		} else {
			$this->success = $this->dict->ExecuteSQLArray( $sqlArray, $continueOnErr );
		}

		return $this->success;
	}

	/**
	* Returns the current SQL array.
	*
	* Call this method to fetch the array of SQL queries resulting from
	* ParseSchema() or ParseSchemaString().
	*
	* @param string $format Format: HTML, TEXT, or NONE (PHP array)
	* @return array Array of SQL statements or FALSE if an error occurs
	*/
	function PrintSQL( $format = 'NONE' ) {
		$sqlArray = null;
		return $this->getSQL( $format, $sqlArray );
	}

	/**
	* Saves the current SQL array to the local filesystem as a list of SQL queries.
	*
	* Call this method to save the array of SQL queries (generally resulting from a
	* parsed XML schema) to the filesystem.
	*
	* @param string $filename Path and name where the file should be saved.
	* @return boolean TRUE if save is successful, else FALSE.
	*/
	function SaveSQL( $filename = './schema.sql' ) {

		if( !isset( $sqlArray ) ) {
			$sqlArray = $this->sqlArray;
		}
		if( !isset( $sqlArray ) ) {
			return FALSE;
		}

		$fp = fopen( $filename, "w" );

		foreach( $sqlArray as $key => $query ) {
			fwrite( $fp, $query . ";\n" );
		}
		fclose( $fp );
	}

	/**
	* Create an xml parser
	*
	* @return object PHP XML parser object
	*
	* @access private
	*/
	function create_parser() {
		// Create the parser
		$xmlParser = xml_parser_create();
		xml_set_object( $xmlParser, $this );

		// Initialize the XML callback functions
		xml_set_element_handler( $xmlParser, '_tag_open', '_tag_close' );
		xml_set_character_data_handler( $xmlParser, '_tag_cdata' );

		return $xmlParser;
	}

	/**
	* XML Callback to process start elements
	*
	* @access private
	*/
	function _tag_open( &$parser, $tag, $attributes ) {
		switch( strtoupper( $tag ) ) {
			case 'TABLE':
				$this->obj = new dbTable( $this, $attributes );
				xml_set_object( $parser, $this->obj );
				break;
			case 'SQL':
				if( !isset( $attributes['PLATFORM'] ) OR $this->supportedPlatform( $attributes['PLATFORM'] ) ) {
					$this->obj = new dbQuerySet( $this, $attributes );
					xml_set_object( $parser, $this->obj );
				}
				break;
			default:
				// print_r( array( $tag, $attributes ) );
		}

	}

	/**
	* XML Callback to process CDATA elements
	*
	* @access private
	*/
	function _tag_cdata( &$parser, $cdata ) {
	}

	/**
	* XML Callback to process end elements
	*
	* @access private
	* @internal
	*/
	function _tag_close( &$parser, $tag ) {

	}

	/**
	* Converts an XML schema string to the specified DTD version.
	*
	* Call this method to convert a string containing an XML schema to a different AXMLS
	* DTD version. For instance, to convert a schema created for an pre-1.0 version for
	* AXMLS (DTD version 0.1) to a newer version of the DTD (e.g. 0.2). If no DTD version
	* parameter is specified, the schema will be converted to the current DTD version.
	* If the newFile parameter is provided, the converted schema will be written to the specified
	* file.
	* @see ConvertSchemaFile()
	*
	* @param string $schema String containing XML schema that will be converted.
	* @param string $newVersion DTD version to convert to.
	* @param string $newFile File name of (converted) output file.
	* @return string Converted XML schema or FALSE if an error occurs.
	*/
	function ConvertSchemaString( $schema, $newVersion = NULL, $newFile = NULL ) {

		// grab current version
		if( !( $version = $this->SchemaStringVersion( $schema ) ) ) {
			return FALSE;
		}

		if( !isset ($newVersion) ) {
			$newVersion = $this->schemaVersion;
		}

		if( $version == $newVersion ) {
			$result = $schema;
		} else {
			$result = $this->TransformSchema( $schema, 'convert-' . $version . '-' . $newVersion);
		}

		if( is_string( $result ) AND is_string( $newFile ) AND ( $fp = fopen( $newFile, 'w' ) ) ) {
			fwrite( $fp, $result );
			fclose( $fp );
		}

		return $result;
	}

	// compat for pre-4.3 - jlim
	function _file_get_contents($path)
	{
		if (function_exists('file_get_contents')) return file_get_contents($path);
		return join('',file($path));
	}

	/**
	* Converts an XML schema file to the specified DTD version.
	*
	* Call this method to convert the specified XML schema file to a different AXMLS
	* DTD version. For instance, to convert a schema created for an pre-1.0 version for
	* AXMLS (DTD version 0.1) to a newer version of the DTD (e.g. 0.2). If no DTD version
	* parameter is specified, the schema will be converted to the current DTD version.
	* If the newFile parameter is provided, the converted schema will be written to the specified
	* file.
	* @see ConvertSchemaString()
	*
	* @param string $filename Name of XML schema file that will be converted.
	* @param string $newVersion DTD version to convert to.
	* @param string $newFile File name of (converted) output file.
	* @return string Converted XML schema or FALSE if an error occurs.
	*/
	function ConvertSchemaFile( $filename, $newVersion = NULL, $newFile = NULL ) {

		// grab current version
		if( !( $version = $this->SchemaFileVersion( $filename ) ) ) {
			return FALSE;
		}

		if( !isset ($newVersion) ) {
			$newVersion = $this->schemaVersion;
		}

		if( $version == $newVersion ) {
			$result = _file_get_contents( $filename );

			// remove unicode BOM if present
			if( substr( $result, 0, 3 ) == sprintf( '%c%c%c', 239, 187, 191 ) ) {
				$result = substr( $result, 3 );
			}
		} else {
			$result = $this->TransformSchema( $filename, 'convert-' . $version . '-' . $newVersion, 'file' );
		}

		if( is_string( $result ) AND is_string( $newFile ) AND ( $fp = fopen( $newFile, 'w' ) ) ) {
			fwrite( $fp, $result );
			fclose( $fp );
		}

		return $result;
	}

	function TransformSchema( $schema, $xsl, $schematype='string' )
	{
		// Fail if XSLT extension is not available
		if( ! function_exists( 'xslt_create' ) ) {
			return FALSE;
		}

		$xsl_file = dirname( __FILE__ ) . '/xsl/' . $xsl . '.xsl';

		// look for xsl
		if( !is_readable( $xsl_file ) ) {
			return FALSE;
		}

		switch( $schematype )
		{
			case 'file':
				if( !is_readable( $schema ) ) {
					return FALSE;
				}

				$schema = _file_get_contents( $schema );
				break;
			case 'string':
			default:
				if( !is_string( $schema ) ) {
					return FALSE;
				}
		}

		$arguments = array (
			'/_xml' => $schema,
			'/_xsl' => _file_get_contents( $xsl_file )
		);

		// create an XSLT processor
		$xh = xslt_create ();

		// set error handler
		xslt_set_error_handler ($xh, array (&$this, 'xslt_error_handler'));

		// process the schema
		$result = xslt_process ($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);

		xslt_free ($xh);

		return $result;
	}

	/**
	* Processes XSLT transformation errors
	*
	* @param object $parser XML parser object
	* @param integer $errno Error number
	* @param integer $level Error level
	* @param array $fields Error information fields
	*
	* @access private
	*/
	function xslt_error_handler( $parser, $errno, $level, $fields ) {
		if( is_array( $fields ) ) {
			$msg = array(
				'Message Type' => ucfirst( $fields['msgtype'] ),
				'Message Code' => $fields['code'],
				'Message' => $fields['msg'],
				'Error Number' => $errno,
				'Level' => $level
			);

			switch( $fields['URI'] ) {
				case 'arg:/_xml':
					$msg['Input'] = 'XML';
					break;
				case 'arg:/_xsl':
					$msg['Input'] = 'XSL';
					break;
				default:
					$msg['Input'] = $fields['URI'];
			}

			$msg['Line'] = $fields['line'];
		} else {
			$msg = array(
				'Message Type' => 'Error',
				'Error Number' => $errno,
				'Level' => $level,
				'Fields' => var_export( $fields, TRUE )
			);
		}

		$error_details = $msg['Message Type'] . ' in XSLT Transformation' . "\n"
					   . '<table>' . "\n";

		foreach( $msg as $label => $details ) {
			$error_details .= '<tr><td><b>' . $label . ': </b></td><td>' . htmlentities( $details ) . '</td></tr>' . "\n";
		}

		$error_details .= '</table>';

		trigger_error( $error_details, E_USER_ERROR );
	}

	/**
	* Returns the AXMLS Schema Version of the requested XML schema file.
	*
	* Call this method to obtain the AXMLS DTD version of the requested XML schema file.
	* @see SchemaStringVersion()
	*
	* @param string $filename AXMLS schema file
	* @return string Schema version number or FALSE on error
	*/
	function SchemaFileVersion( $filename ) {
		// Open the file
		if( !($fp = fopen( $filename, 'r' )) ) {
			// die( 'Unable to open file' );
			return FALSE;
		}

		// Process the file
		while( $data = fread( $fp, 4096 ) ) {
			if( preg_match( $this->versionRegex, $data, $matches ) ) {
				return !empty( $matches[2] ) ? $matches[2] : XMLS_DEFAULT_SCHEMA_VERSION;
			}
		}

		return FALSE;
	}

	/**
	* Returns the AXMLS Schema Version of the provided XML schema string.
	*
	* Call this method to obtain the AXMLS DTD version of the provided XML schema string.
	* @see SchemaFileVersion()
	*
	* @param string $xmlstring XML schema string
	* @return string Schema version number or FALSE on error
	*/
	function SchemaStringVersion( $xmlstring ) {
		if( !is_string( $xmlstring ) OR empty( $xmlstring ) ) {
			return FALSE;
		}

		if( preg_match( $this->versionRegex, $xmlstring, $matches ) ) {
			return !empty( $matches[2] ) ? $matches[2] : XMLS_DEFAULT_SCHEMA_VERSION;
		}

		return FALSE;
	}

	/**
	* Extracts an XML schema from an existing database.
	*
	* Call this method to create an XML schema string from an existing database.
	* If the data parameter is set to TRUE, AXMLS will include the data from the database
	* in the schema.
	*
	* @param boolean $data Include data in schema dump
	* @return string Generated XML schema
	*/
	function ExtractSchema( $data = FALSE ) {
		$old_mode = $this->db->SetFetchMode( ADODB_FETCH_NUM );

		$schema = '<?xml version="1.0"?>' . "\n"
				. '<schema version="' . $this->schemaVersion . '">' . "\n";

		if( is_array( $tables = $this->db->MetaTables( 'TABLES' ) ) ) {
			foreach( $tables as $table ) {
				$schema .= '	<table name="' . $table . '">' . "\n";

				// grab details from database
				$rs = $this->db->Execute( 'SELECT * FROM ' . $table . ' WHERE 1=1' );
				$fields = $this->db->MetaColumns( $table );
				$indexes = $this->db->MetaIndexes( $table );

				if( is_array( $fields ) ) {
					foreach( $fields as $details ) {
						$extra = '';
						$content = array();

						if( $details->max_length > 0 ) {
							$extra .= ' size="' . $details->max_length . '"';
						}

						if( $details->primary_key ) {
							$content[] = '<KEY/>';
						} elseif( $details->not_null ) {
							$content[] = '<NOTNULL/>';
						}

						if( $details->has_default ) {
							$content[] = '<DEFAULT value="' . $details->default_value . '"/>';
						}

						if( $details->auto_increment ) {
							$content[] = '<AUTOINCREMENT/>';
						}

						// this stops the creation of 'R' columns,
						// AUTOINCREMENT is used to create auto columns
						$details->primary_key = 0;
						$type = $rs->MetaType( $details );

						$schema .= '		<field name="' . $details->name . '" type="' . $type . '"' . $extra . '>';

						if( !empty( $content ) ) {
							$schema .= "\n			" . implode( "\n			", $content ) . "\n		";
						}

						$schema .= '</field>' . "\n";
					}
				}

				if( is_array( $indexes ) ) {
					foreach( $indexes as $index => $details ) {
						$schema .= '		<index name="' . $index . '">' . "\n";

						if( $details['unique'] ) {
							$schema .= '			<UNIQUE/>' . "\n";
						}

						foreach( $details['columns'] as $column ) {
							$schema .= '			<col>' . $column . '</col>' . "\n";
						}

						$schema .= '		</index>' . "\n";
					}
				}

				if( $data ) {
					$rs = $this->db->Execute( 'SELECT * FROM ' . $table );

					if( is_object( $rs ) ) {
						$schema .= '		<data>' . "\n";

						while( $row = $rs->FetchRow() ) {
							foreach( $row as $key => $val ) {
								$row[$key] = htmlentities($val);
							}

							$schema .= '			<row><f>' . implode( '</f><f>', $row ) . '</f></row>' . "\n";
						}

						$schema .= '		</data>' . "\n";
					}
				}

				$schema .= '	</table>' . "\n";
			}
		}

		$this->db->SetFetchMode( $old_mode );

		$schema .= '</schema>';
		return $schema;
	}

	/**
	* Sets a prefix for database objects
	*
	* Call this method to set a standard prefix that will be prepended to all database tables
	* and indices when the schema is parsed. Calling setPrefix with no arguments clears the prefix.
	*
	* @param string $prefix Prefix that will be prepended.
	* @param boolean $underscore If TRUE, automatically append an underscore character to the prefix.
	* @return boolean TRUE if successful, else FALSE
	*/
	function SetPrefix( $prefix = '', $underscore = TRUE ) {
		switch( TRUE ) {
			// clear prefix
			case empty( $prefix ):
				logMsg( 'Cleared prefix' );
				$this->objectPrefix = '';
				return TRUE;
			// prefix too long
			case strlen( $prefix ) > XMLS_PREFIX_MAXLEN:
			// prefix contains invalid characters
			case !preg_match( '/^[a-z][a-z0-9_]+$/i', $prefix ):
				logMsg( 'Invalid prefix: ' . $prefix );
				return FALSE;
		}

		if( $underscore AND substr( $prefix, -1 ) != '_' ) {
			$prefix .= '_';
		}

		// prefix valid
		logMsg( 'Set prefix: ' . $prefix );
		$this->objectPrefix = $prefix;
		return TRUE;
	}

	/**
	* Returns an object name with the current prefix prepended.
	*
	* @param string	$name Name
	* @return string	Prefixed name
	*
	* @access private
	*/
	function prefix( $name = '' ) {
		// if prefix is set
		if( !empty( $this->objectPrefix ) ) {
			// Prepend the object prefix to the table name
			// prepend after quote if used
			return preg_replace( '/^(`?)(.+)$/', '$1' . $this->objectPrefix . '$2', $name );
		}

		// No prefix set. Use name provided.
		return $name;
	}

	/**
	* Checks if element references a specific platform
	*
	* @param string $platform Requested platform
	* @returns boolean TRUE if platform check succeeds
	*
	* @access private
	*/
	function supportedPlatform( $platform = NULL ) {
		$regex = '/^(\w*\|)*' . $this->db->databaseType . '(\|\w*)*$/';

		if( !isset( $platform ) OR preg_match( $regex, $platform ) ) {
			logMsg( "Platform $platform is supported" );
			return TRUE;
		} else {
			logMsg( "Platform $platform is NOT supported" );
			return FALSE;
		}
	}

	/**
	* Clears the array of generated SQL.
	*
	* @access private
	*/
	function clearSQL() {
		$this->sqlArray = array();
	}

	/**
	* Adds SQL into the SQL array.
	*
	* @param mixed $sql SQL to Add
	* @return boolean TRUE if successful, else FALSE.
	*
	* @access private
	*/
	function addSQL( $sql = NULL ) {
		if( is_array( $sql ) ) {
			foreach( $sql as $line ) {
				$this->addSQL( $line );
			}

			return TRUE;
		}

		if( is_string( $sql ) ) {
			$this->sqlArray[] = $sql;

			// if executeInline is enabled, and either no errors have occurred or continueOnError is enabled, execute SQL.
			if( $this->ExecuteInline() && ( $this->success == 2 || $this->ContinueOnError() ) ) {
				$saved = $this->db->debug;
				$this->db->debug = $this->debug;
				$ok = $this->db->Execute( $sql );
				$this->db->debug = $saved;

				if( !$ok ) {
					if( $this->debug ) {
						ADOConnection::outp( $this->db->ErrorMsg() );
					}

					$this->success = 1;
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	* Gets the SQL array in the specified format.
	*
	* @param string $format Format
	* @return mixed SQL
	*
	* @access private
	*/
	function getSQL( $format = NULL, $sqlArray = NULL ) {
		if( !is_array( $sqlArray ) ) {
			$sqlArray = $this->sqlArray;
		}

		if( !is_array( $sqlArray ) ) {
			return FALSE;
		}

		switch( strtolower( $format ) ) {
			case 'string':
			case 'text':
				return !empty( $sqlArray ) ? implode( ";\n\n", $sqlArray ) . ';' : '';
			case'html':
				return !empty( $sqlArray ) ? nl2br( htmlentities( implode( ";\n\n", $sqlArray ) . ';' ) ) : '';
		}

		return $this->sqlArray;
	}

	/**
	* Destroys an adoSchema object.
	*
	* Call this method to clean up after an adoSchema object that is no longer in use.
	* @deprecated adoSchema now cleans up automatically.
	*/
	function Destroy() {}
}

/**
* Message logging function
*
* @access private
*/
function logMsg( $msg, $title = NULL, $force = FALSE ) {
	if( XMLS_DEBUG or $force ) {
		echo '<pre>';

		if( isset( $title ) ) {
			echo '<h3>' . htmlentities( $title ) . '</h3>';
		}

		if( is_object( $this ) ) {
			echo '[' . get_class( $this ) . '] ';
		}

		print_r( $msg );

		echo '</pre>';
	}
}
