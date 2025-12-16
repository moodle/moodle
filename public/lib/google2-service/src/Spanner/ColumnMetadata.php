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

namespace Google\Service\Spanner;

class ColumnMetadata extends \Google\Model
{
  /**
   * Indicates whether the column is a primary key column.
   *
   * @var bool
   */
  public $isPrimaryKey;
  /**
   * Name of the column.
   *
   * @var string
   */
  public $name;
  /**
   * Ordinal position of the column based on the original table definition in
   * the schema starting with a value of 1.
   *
   * @var string
   */
  public $ordinalPosition;
  protected $typeType = Type::class;
  protected $typeDataType = '';

  /**
   * Indicates whether the column is a primary key column.
   *
   * @param bool $isPrimaryKey
   */
  public function setIsPrimaryKey($isPrimaryKey)
  {
    $this->isPrimaryKey = $isPrimaryKey;
  }
  /**
   * @return bool
   */
  public function getIsPrimaryKey()
  {
    return $this->isPrimaryKey;
  }
  /**
   * Name of the column.
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
   * Ordinal position of the column based on the original table definition in
   * the schema starting with a value of 1.
   *
   * @param string $ordinalPosition
   */
  public function setOrdinalPosition($ordinalPosition)
  {
    $this->ordinalPosition = $ordinalPosition;
  }
  /**
   * @return string
   */
  public function getOrdinalPosition()
  {
    return $this->ordinalPosition;
  }
  /**
   * Type of the column.
   *
   * @param Type $type
   */
  public function setType(Type $type)
  {
    $this->type = $type;
  }
  /**
   * @return Type
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColumnMetadata::class, 'Google_Service_Spanner_ColumnMetadata');
