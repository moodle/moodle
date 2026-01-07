<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Connectors;

class JsonSchema extends \Google\Collection
{
  /**
   * Datatype unspecified.
   */
  public const JDBC_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Deprecated Int type, use INTEGER type instead.
   *
   * @deprecated
   */
  public const JDBC_TYPE_INT = 'INT';
  /**
   * Small int type.
   */
  public const JDBC_TYPE_SMALLINT = 'SMALLINT';
  /**
   * Double type.
   */
  public const JDBC_TYPE_DOUBLE = 'DOUBLE';
  /**
   * Date type.
   */
  public const JDBC_TYPE_DATE = 'DATE';
  /**
   * Deprecated Datetime type.
   *
   * @deprecated
   */
  public const JDBC_TYPE_DATETIME = 'DATETIME';
  /**
   * Time type.
   */
  public const JDBC_TYPE_TIME = 'TIME';
  /**
   * Deprecated string type, use VARCHAR type instead.
   *
   * @deprecated
   */
  public const JDBC_TYPE_STRING = 'STRING';
  /**
   * Deprecated Long type, use BIGINT type instead.
   *
   * @deprecated
   */
  public const JDBC_TYPE_LONG = 'LONG';
  /**
   * Boolean type.
   */
  public const JDBC_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Decimal type.
   */
  public const JDBC_TYPE_DECIMAL = 'DECIMAL';
  /**
   * Deprecated UUID type, use VARCHAR instead.
   *
   * @deprecated
   */
  public const JDBC_TYPE_UUID = 'UUID';
  /**
   * Blob type.
   */
  public const JDBC_TYPE_BLOB = 'BLOB';
  /**
   * Bit type.
   */
  public const JDBC_TYPE_BIT = 'BIT';
  /**
   * Tiny int type.
   */
  public const JDBC_TYPE_TINYINT = 'TINYINT';
  /**
   * Integer type.
   */
  public const JDBC_TYPE_INTEGER = 'INTEGER';
  /**
   * Big int type.
   */
  public const JDBC_TYPE_BIGINT = 'BIGINT';
  /**
   * Float type.
   */
  public const JDBC_TYPE_FLOAT = 'FLOAT';
  /**
   * Real type.
   */
  public const JDBC_TYPE_REAL = 'REAL';
  /**
   * Numeric type.
   */
  public const JDBC_TYPE_NUMERIC = 'NUMERIC';
  /**
   * Char type.
   */
  public const JDBC_TYPE_CHAR = 'CHAR';
  /**
   * Varchar type.
   */
  public const JDBC_TYPE_VARCHAR = 'VARCHAR';
  /**
   * Long varchar type.
   */
  public const JDBC_TYPE_LONGVARCHAR = 'LONGVARCHAR';
  /**
   * Timestamp type.
   */
  public const JDBC_TYPE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Nchar type.
   */
  public const JDBC_TYPE_NCHAR = 'NCHAR';
  /**
   * Nvarchar type.
   */
  public const JDBC_TYPE_NVARCHAR = 'NVARCHAR';
  /**
   * Long Nvarchar type.
   */
  public const JDBC_TYPE_LONGNVARCHAR = 'LONGNVARCHAR';
  /**
   * Null type.
   */
  public const JDBC_TYPE_NULL = 'NULL';
  /**
   * Other type.
   */
  public const JDBC_TYPE_OTHER = 'OTHER';
  /**
   * Java object type.
   */
  public const JDBC_TYPE_JAVA_OBJECT = 'JAVA_OBJECT';
  /**
   * Distinct type keyword.
   */
  public const JDBC_TYPE_DISTINCT = 'DISTINCT';
  /**
   * Struct type.
   */
  public const JDBC_TYPE_STRUCT = 'STRUCT';
  /**
   * Array type.
   */
  public const JDBC_TYPE_ARRAY = 'ARRAY';
  /**
   * Clob type.
   */
  public const JDBC_TYPE_CLOB = 'CLOB';
  /**
   * Ref type.
   */
  public const JDBC_TYPE_REF = 'REF';
  /**
   * Datalink type.
   */
  public const JDBC_TYPE_DATALINK = 'DATALINK';
  /**
   * Row ID type.
   */
  public const JDBC_TYPE_ROWID = 'ROWID';
  /**
   * Binary type.
   */
  public const JDBC_TYPE_BINARY = 'BINARY';
  /**
   * Varbinary type.
   */
  public const JDBC_TYPE_VARBINARY = 'VARBINARY';
  /**
   * Long Varbinary type.
   */
  public const JDBC_TYPE_LONGVARBINARY = 'LONGVARBINARY';
  /**
   * Nclob type.
   */
  public const JDBC_TYPE_NCLOB = 'NCLOB';
  /**
   * SQLXML type.
   */
  public const JDBC_TYPE_SQLXML = 'SQLXML';
  /**
   * Ref_cursor type.
   */
  public const JDBC_TYPE_REF_CURSOR = 'REF_CURSOR';
  /**
   * Time with timezone type.
   */
  public const JDBC_TYPE_TIME_WITH_TIMEZONE = 'TIME_WITH_TIMEZONE';
  /**
   * Timestamp with timezone type.
   */
  public const JDBC_TYPE_TIMESTAMP_WITH_TIMEZONE = 'TIMESTAMP_WITH_TIMEZONE';
  protected $collection_key = 'type';
  /**
   * Additional details apart from standard json schema fields, this gives
   * flexibility to store metadata about the schema
   *
   * @var array[]
   */
  public $additionalDetails;
  /**
   * The default value of the field or object described by this schema.
   *
   * @var array
   */
  public $default;
  /**
   * A description of this schema.
   *
   * @var string
   */
  public $description;
  /**
   * Possible values for an enumeration. This works in conjunction with `type`
   * to represent types with a fixed set of legal values
   *
   * @var array[]
   */
  public $enum;
  /**
   * Format of the value as per https://json-schema.org/understanding-json-
   * schema/reference/string.html#format
   *
   * @var string
   */
  public $format;
  protected $itemsType = JsonSchema::class;
  protected $itemsDataType = '';
  /**
   * JDBC datatype of the field.
   *
   * @var string
   */
  public $jdbcType;
  protected $propertiesType = JsonSchema::class;
  protected $propertiesDataType = 'map';
  /**
   * Whether this property is required.
   *
   * @var string[]
   */
  public $required;
  /**
   * JSON Schema Validation: A Vocabulary for Structural Validation of JSON
   *
   * @var string[]
   */
  public $type;

  /**
   * Additional details apart from standard json schema fields, this gives
   * flexibility to store metadata about the schema
   *
   * @param array[] $additionalDetails
   */
  public function setAdditionalDetails($additionalDetails)
  {
    $this->additionalDetails = $additionalDetails;
  }
  /**
   * @return array[]
   */
  public function getAdditionalDetails()
  {
    return $this->additionalDetails;
  }
  /**
   * The default value of the field or object described by this schema.
   *
   * @param array $default
   */
  public function setDefault($default)
  {
    $this->default = $default;
  }
  /**
   * @return array
   */
  public function getDefault()
  {
    return $this->default;
  }
  /**
   * A description of this schema.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Possible values for an enumeration. This works in conjunction with `type`
   * to represent types with a fixed set of legal values
   *
   * @param array[] $enum
   */
  public function setEnum($enum)
  {
    $this->enum = $enum;
  }
  /**
   * @return array[]
   */
  public function getEnum()
  {
    return $this->enum;
  }
  /**
   * Format of the value as per https://json-schema.org/understanding-json-
   * schema/reference/string.html#format
   *
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Schema that applies to array values, applicable only if this is of type
   * `array`.
   *
   * @param JsonSchema $items
   */
  public function setItems(JsonSchema $items)
  {
    $this->items = $items;
  }
  /**
   * @return JsonSchema
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * JDBC datatype of the field.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, INT, SMALLINT, DOUBLE, DATE,
   * DATETIME, TIME, STRING, LONG, BOOLEAN, DECIMAL, UUID, BLOB, BIT, TINYINT,
   * INTEGER, BIGINT, FLOAT, REAL, NUMERIC, CHAR, VARCHAR, LONGVARCHAR,
   * TIMESTAMP, NCHAR, NVARCHAR, LONGNVARCHAR, NULL, OTHER, JAVA_OBJECT,
   * DISTINCT, STRUCT, ARRAY, CLOB, REF, DATALINK, ROWID, BINARY, VARBINARY,
   * LONGVARBINARY, NCLOB, SQLXML, REF_CURSOR, TIME_WITH_TIMEZONE,
   * TIMESTAMP_WITH_TIMEZONE
   *
   * @param self::JDBC_TYPE_* $jdbcType
   */
  public function setJdbcType($jdbcType)
  {
    $this->jdbcType = $jdbcType;
  }
  /**
   * @return self::JDBC_TYPE_*
   */
  public function getJdbcType()
  {
    return $this->jdbcType;
  }
  /**
   * The child schemas, applicable only if this is of type `object`. The key is
   * the name of the property and the value is the json schema that describes
   * that property
   *
   * @param JsonSchema[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return JsonSchema[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Whether this property is required.
   *
   * @param string[] $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return string[]
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * JSON Schema Validation: A Vocabulary for Structural Validation of JSON
   *
   * @param string[] $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string[]
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JsonSchema::class, 'Google_Service_Connectors_JsonSchema');
