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

class ResultMetadata extends \Google\Model
{
  /**
   * Datatype unspecified.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Deprecated Int type, use INTEGER type instead.
   *
   * @deprecated
   */
  public const DATA_TYPE_INT = 'INT';
  /**
   * Small int type.
   */
  public const DATA_TYPE_SMALLINT = 'SMALLINT';
  /**
   * Double type.
   */
  public const DATA_TYPE_DOUBLE = 'DOUBLE';
  /**
   * Date type.
   */
  public const DATA_TYPE_DATE = 'DATE';
  /**
   * Deprecated Datetime type.
   *
   * @deprecated
   */
  public const DATA_TYPE_DATETIME = 'DATETIME';
  /**
   * Time type.
   */
  public const DATA_TYPE_TIME = 'TIME';
  /**
   * Deprecated string type, use VARCHAR type instead.
   *
   * @deprecated
   */
  public const DATA_TYPE_STRING = 'STRING';
  /**
   * Deprecated Long type, use BIGINT type instead.
   *
   * @deprecated
   */
  public const DATA_TYPE_LONG = 'LONG';
  /**
   * Boolean type.
   */
  public const DATA_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Decimal type.
   */
  public const DATA_TYPE_DECIMAL = 'DECIMAL';
  /**
   * Deprecated UUID type, use VARCHAR instead.
   *
   * @deprecated
   */
  public const DATA_TYPE_UUID = 'UUID';
  /**
   * Blob type.
   */
  public const DATA_TYPE_BLOB = 'BLOB';
  /**
   * Bit type.
   */
  public const DATA_TYPE_BIT = 'BIT';
  /**
   * Tiny int type.
   */
  public const DATA_TYPE_TINYINT = 'TINYINT';
  /**
   * Integer type.
   */
  public const DATA_TYPE_INTEGER = 'INTEGER';
  /**
   * Big int type.
   */
  public const DATA_TYPE_BIGINT = 'BIGINT';
  /**
   * Float type.
   */
  public const DATA_TYPE_FLOAT = 'FLOAT';
  /**
   * Real type.
   */
  public const DATA_TYPE_REAL = 'REAL';
  /**
   * Numeric type.
   */
  public const DATA_TYPE_NUMERIC = 'NUMERIC';
  /**
   * Char type.
   */
  public const DATA_TYPE_CHAR = 'CHAR';
  /**
   * Varchar type.
   */
  public const DATA_TYPE_VARCHAR = 'VARCHAR';
  /**
   * Long varchar type.
   */
  public const DATA_TYPE_LONGVARCHAR = 'LONGVARCHAR';
  /**
   * Timestamp type.
   */
  public const DATA_TYPE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Nchar type.
   */
  public const DATA_TYPE_NCHAR = 'NCHAR';
  /**
   * Nvarchar type.
   */
  public const DATA_TYPE_NVARCHAR = 'NVARCHAR';
  /**
   * Long Nvarchar type.
   */
  public const DATA_TYPE_LONGNVARCHAR = 'LONGNVARCHAR';
  /**
   * Null type.
   */
  public const DATA_TYPE_NULL = 'NULL';
  /**
   * Other type.
   */
  public const DATA_TYPE_OTHER = 'OTHER';
  /**
   * Java object type.
   */
  public const DATA_TYPE_JAVA_OBJECT = 'JAVA_OBJECT';
  /**
   * Distinct type keyword.
   */
  public const DATA_TYPE_DISTINCT = 'DISTINCT';
  /**
   * Struct type.
   */
  public const DATA_TYPE_STRUCT = 'STRUCT';
  /**
   * Array type.
   */
  public const DATA_TYPE_ARRAY = 'ARRAY';
  /**
   * Clob type.
   */
  public const DATA_TYPE_CLOB = 'CLOB';
  /**
   * Ref type.
   */
  public const DATA_TYPE_REF = 'REF';
  /**
   * Datalink type.
   */
  public const DATA_TYPE_DATALINK = 'DATALINK';
  /**
   * Row ID type.
   */
  public const DATA_TYPE_ROWID = 'ROWID';
  /**
   * Binary type.
   */
  public const DATA_TYPE_BINARY = 'BINARY';
  /**
   * Varbinary type.
   */
  public const DATA_TYPE_VARBINARY = 'VARBINARY';
  /**
   * Long Varbinary type.
   */
  public const DATA_TYPE_LONGVARBINARY = 'LONGVARBINARY';
  /**
   * Nclob type.
   */
  public const DATA_TYPE_NCLOB = 'NCLOB';
  /**
   * SQLXML type.
   */
  public const DATA_TYPE_SQLXML = 'SQLXML';
  /**
   * Ref_cursor type.
   */
  public const DATA_TYPE_REF_CURSOR = 'REF_CURSOR';
  /**
   * Time with timezone type.
   */
  public const DATA_TYPE_TIME_WITH_TIMEZONE = 'TIME_WITH_TIMEZONE';
  /**
   * Timestamp with timezone type.
   */
  public const DATA_TYPE_TIMESTAMP_WITH_TIMEZONE = 'TIMESTAMP_WITH_TIMEZONE';
  /**
   * The data type of the metadata field
   *
   * @var string
   */
  public $dataType;
  /**
   * The following field specifies the default value of the Parameter provided
   * by the external system if a value is not provided.
   *
   * @var array
   */
  public $defaultValue;
  /**
   * A brief description of the metadata field.
   *
   * @var string
   */
  public $description;
  protected $jsonSchemaType = JsonSchema::class;
  protected $jsonSchemaDataType = '';
  /**
   * Name of the metadata field.
   *
   * @var string
   */
  public $name;
  /**
   * Specifies whether a null value is allowed.
   *
   * @var bool
   */
  public $nullable;

  /**
   * The data type of the metadata field
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, INT, SMALLINT, DOUBLE, DATE,
   * DATETIME, TIME, STRING, LONG, BOOLEAN, DECIMAL, UUID, BLOB, BIT, TINYINT,
   * INTEGER, BIGINT, FLOAT, REAL, NUMERIC, CHAR, VARCHAR, LONGVARCHAR,
   * TIMESTAMP, NCHAR, NVARCHAR, LONGNVARCHAR, NULL, OTHER, JAVA_OBJECT,
   * DISTINCT, STRUCT, ARRAY, CLOB, REF, DATALINK, ROWID, BINARY, VARBINARY,
   * LONGVARBINARY, NCLOB, SQLXML, REF_CURSOR, TIME_WITH_TIMEZONE,
   * TIMESTAMP_WITH_TIMEZONE
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * The following field specifies the default value of the Parameter provided
   * by the external system if a value is not provided.
   *
   * @param array $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return array
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * A brief description of the metadata field.
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
   * JsonSchema of the result, applicable only if parameter is of type `STRUCT`
   *
   * @param JsonSchema $jsonSchema
   */
  public function setJsonSchema(JsonSchema $jsonSchema)
  {
    $this->jsonSchema = $jsonSchema;
  }
  /**
   * @return JsonSchema
   */
  public function getJsonSchema()
  {
    return $this->jsonSchema;
  }
  /**
   * Name of the metadata field.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Specifies whether a null value is allowed.
   *
   * @param bool $nullable
   */
  public function setNullable($nullable)
  {
    $this->nullable = $nullable;
  }
  /**
   * @return bool
   */
  public function getNullable()
  {
    return $this->nullable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultMetadata::class, 'Google_Service_Connectors_ResultMetadata');
