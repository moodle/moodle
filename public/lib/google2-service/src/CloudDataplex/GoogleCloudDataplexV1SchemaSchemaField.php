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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1SchemaSchemaField extends \Google\Collection
{
  /**
   * Mode unspecified.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * The field has required semantics.
   */
  public const MODE_REQUIRED = 'REQUIRED';
  /**
   * The field has optional semantics, and may be null.
   */
  public const MODE_NULLABLE = 'NULLABLE';
  /**
   * The field has repeated (0 or more) semantics, and is a list of values.
   */
  public const MODE_REPEATED = 'REPEATED';
  /**
   * SchemaType unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Boolean field.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Single byte numeric field.
   */
  public const TYPE_BYTE = 'BYTE';
  /**
   * 16-bit numeric field.
   */
  public const TYPE_INT16 = 'INT16';
  /**
   * 32-bit numeric field.
   */
  public const TYPE_INT32 = 'INT32';
  /**
   * 64-bit numeric field.
   */
  public const TYPE_INT64 = 'INT64';
  /**
   * Floating point numeric field.
   */
  public const TYPE_FLOAT = 'FLOAT';
  /**
   * Double precision numeric field.
   */
  public const TYPE_DOUBLE = 'DOUBLE';
  /**
   * Real value numeric field.
   */
  public const TYPE_DECIMAL = 'DECIMAL';
  /**
   * Sequence of characters field.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Sequence of bytes field.
   */
  public const TYPE_BINARY = 'BINARY';
  /**
   * Date and time field.
   */
  public const TYPE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Date field.
   */
  public const TYPE_DATE = 'DATE';
  /**
   * Time field.
   */
  public const TYPE_TIME = 'TIME';
  /**
   * Structured field. Nested fields that define the structure of the map. If
   * all nested fields are nullable, this field represents a union.
   */
  public const TYPE_RECORD = 'RECORD';
  /**
   * Null field that does not have values.
   */
  public const TYPE_NULL = 'NULL';
  protected $collection_key = 'fields';
  /**
   * Optional. User friendly field description. Must be less than or equal to
   * 1024 characters.
   *
   * @var string
   */
  public $description;
  protected $fieldsType = GoogleCloudDataplexV1SchemaSchemaField::class;
  protected $fieldsDataType = 'array';
  /**
   * Required. Additional field semantics.
   *
   * @var string
   */
  public $mode;
  /**
   * Required. The name of the field. Must contain only letters, numbers and
   * underscores, with a maximum length of 767 characters, and must begin with a
   * letter or underscore.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The type of field.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. User friendly field description. Must be less than or equal to
   * 1024 characters.
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
   * Optional. Any nested field for complex types.
   *
   * @param GoogleCloudDataplexV1SchemaSchemaField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDataplexV1SchemaSchemaField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Required. Additional field semantics.
   *
   * Accepted values: MODE_UNSPECIFIED, REQUIRED, NULLABLE, REPEATED
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Required. The name of the field. Must contain only letters, numbers and
   * underscores, with a maximum length of 767 characters, and must begin with a
   * letter or underscore.
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
   * Required. The type of field.
   *
   * Accepted values: TYPE_UNSPECIFIED, BOOLEAN, BYTE, INT16, INT32, INT64,
   * FLOAT, DOUBLE, DECIMAL, STRING, BINARY, TIMESTAMP, DATE, TIME, RECORD, NULL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1SchemaSchemaField::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1SchemaSchemaField');
