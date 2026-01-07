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

namespace Google\Service\CloudAsset;

class TableFieldSchema extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * The field name. The name must contain only letters (a-z, A-Z), numbers
   * (0-9), or underscores (_), and must start with a letter or underscore. The
   * maximum length is 128 characters.
   *
   * @var string
   */
  public $field;
  protected $fieldsType = TableFieldSchema::class;
  protected $fieldsDataType = 'array';
  /**
   * The field mode. Possible values include NULLABLE, REQUIRED and REPEATED.
   * The default value is NULLABLE.
   *
   * @var string
   */
  public $mode;
  /**
   * The field data type. Possible values include * STRING * BYTES * INTEGER *
   * FLOAT * BOOLEAN * TIMESTAMP * DATE * TIME * DATETIME * GEOGRAPHY, *
   * NUMERIC, * BIGNUMERIC, * RECORD (where RECORD indicates that the field
   * contains a nested schema).
   *
   * @var string
   */
  public $type;

  /**
   * The field name. The name must contain only letters (a-z, A-Z), numbers
   * (0-9), or underscores (_), and must start with a letter or underscore. The
   * maximum length is 128 characters.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * Describes the nested schema fields if the type property is set to RECORD.
   *
   * @param TableFieldSchema[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return TableFieldSchema[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The field mode. Possible values include NULLABLE, REQUIRED and REPEATED.
   * The default value is NULLABLE.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The field data type. Possible values include * STRING * BYTES * INTEGER *
   * FLOAT * BOOLEAN * TIMESTAMP * DATE * TIME * DATETIME * GEOGRAPHY, *
   * NUMERIC, * BIGNUMERIC, * RECORD (where RECORD indicates that the field
   * contains a nested schema).
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableFieldSchema::class, 'Google_Service_CloudAsset_TableFieldSchema');
