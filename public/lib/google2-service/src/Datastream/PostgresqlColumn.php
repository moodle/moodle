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

namespace Google\Service\Datastream;

class PostgresqlColumn extends \Google\Model
{
  /**
   * Column name.
   *
   * @var string
   */
  public $column;
  /**
   * The PostgreSQL data type.
   *
   * @var string
   */
  public $dataType;
  /**
   * Column length.
   *
   * @var int
   */
  public $length;
  /**
   * Whether or not the column can accept a null value.
   *
   * @var bool
   */
  public $nullable;
  /**
   * The ordinal position of the column in the table.
   *
   * @var int
   */
  public $ordinalPosition;
  /**
   * Column precision.
   *
   * @var int
   */
  public $precision;
  /**
   * Whether or not the column represents a primary key.
   *
   * @var bool
   */
  public $primaryKey;
  /**
   * Column scale.
   *
   * @var int
   */
  public $scale;

  /**
   * Column name.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * The PostgreSQL data type.
   *
   * @param string $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return string
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Column length.
   *
   * @param int $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return int
   */
  public function getLength()
  {
    return $this->length;
  }
  /**
   * Whether or not the column can accept a null value.
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
  /**
   * The ordinal position of the column in the table.
   *
   * @param int $ordinalPosition
   */
  public function setOrdinalPosition($ordinalPosition)
  {
    $this->ordinalPosition = $ordinalPosition;
  }
  /**
   * @return int
   */
  public function getOrdinalPosition()
  {
    return $this->ordinalPosition;
  }
  /**
   * Column precision.
   *
   * @param int $precision
   */
  public function setPrecision($precision)
  {
    $this->precision = $precision;
  }
  /**
   * @return int
   */
  public function getPrecision()
  {
    return $this->precision;
  }
  /**
   * Whether or not the column represents a primary key.
   *
   * @param bool $primaryKey
   */
  public function setPrimaryKey($primaryKey)
  {
    $this->primaryKey = $primaryKey;
  }
  /**
   * @return bool
   */
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }
  /**
   * Column scale.
   *
   * @param int $scale
   */
  public function setScale($scale)
  {
    $this->scale = $scale;
  }
  /**
   * @return int
   */
  public function getScale()
  {
    return $this->scale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgresqlColumn::class, 'Google_Service_Datastream_PostgresqlColumn');
