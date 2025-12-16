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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1ColumnSchema extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const HIGHEST_INDEXING_TYPE_INDEXING_TYPE_UNSPECIFIED = 'INDEXING_TYPE_UNSPECIFIED';
  /**
   * Column not a part of an index.
   */
  public const HIGHEST_INDEXING_TYPE_INDEXING_TYPE_NONE = 'INDEXING_TYPE_NONE';
  /**
   * Column Part of non unique index.
   */
  public const HIGHEST_INDEXING_TYPE_INDEXING_TYPE_NON_UNIQUE = 'INDEXING_TYPE_NON_UNIQUE';
  /**
   * Column part of unique index.
   */
  public const HIGHEST_INDEXING_TYPE_INDEXING_TYPE_UNIQUE = 'INDEXING_TYPE_UNIQUE';
  /**
   * Column part of the primary key.
   */
  public const HIGHEST_INDEXING_TYPE_INDEXING_TYPE_PRIMARY_KEY = 'INDEXING_TYPE_PRIMARY_KEY';
  protected $collection_key = 'subcolumns';
  /**
   * Required. Name of the column. Must be a UTF-8 string without dots (.). The
   * maximum size is 64 bytes.
   *
   * @var string
   */
  public $column;
  /**
   * Optional. Default value for the column.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * Optional. Description of the column. Default value is an empty string. The
   * description must be a UTF-8 string with the maximum size of 2000 bytes.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Garbage collection policy for the column or column family.
   * Applies to systems like Cloud Bigtable.
   *
   * @var string
   */
  public $gcRule;
  /**
   * Optional. Most important inclusion of this column.
   *
   * @var string
   */
  public $highestIndexingType;
  protected $lookerColumnSpecType = GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec::class;
  protected $lookerColumnSpecDataType = '';
  /**
   * Optional. A column's mode indicates whether values in this column are
   * required, nullable, or repeated. Only `NULLABLE`, `REQUIRED`, and
   * `REPEATED` values are supported. Default mode is `NULLABLE`.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. Ordinal position
   *
   * @var int
   */
  public $ordinalPosition;
  protected $rangeElementTypeType = GoogleCloudDatacatalogV1ColumnSchemaFieldElementType::class;
  protected $rangeElementTypeDataType = '';
  protected $subcolumnsType = GoogleCloudDatacatalogV1ColumnSchema::class;
  protected $subcolumnsDataType = 'array';
  /**
   * Required. Type of the column. Must be a UTF-8 string with the maximum size
   * of 128 bytes.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Name of the column. Must be a UTF-8 string without dots (.). The
   * maximum size is 64 bytes.
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
   * Optional. Default value for the column.
   *
   * @param string $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return string
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Optional. Description of the column. Default value is an empty string. The
   * description must be a UTF-8 string with the maximum size of 2000 bytes.
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
   * Optional. Garbage collection policy for the column or column family.
   * Applies to systems like Cloud Bigtable.
   *
   * @param string $gcRule
   */
  public function setGcRule($gcRule)
  {
    $this->gcRule = $gcRule;
  }
  /**
   * @return string
   */
  public function getGcRule()
  {
    return $this->gcRule;
  }
  /**
   * Optional. Most important inclusion of this column.
   *
   * Accepted values: INDEXING_TYPE_UNSPECIFIED, INDEXING_TYPE_NONE,
   * INDEXING_TYPE_NON_UNIQUE, INDEXING_TYPE_UNIQUE, INDEXING_TYPE_PRIMARY_KEY
   *
   * @param self::HIGHEST_INDEXING_TYPE_* $highestIndexingType
   */
  public function setHighestIndexingType($highestIndexingType)
  {
    $this->highestIndexingType = $highestIndexingType;
  }
  /**
   * @return self::HIGHEST_INDEXING_TYPE_*
   */
  public function getHighestIndexingType()
  {
    return $this->highestIndexingType;
  }
  /**
   * Looker specific column info of this column.
   *
   * @param GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec $lookerColumnSpec
   */
  public function setLookerColumnSpec(GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec $lookerColumnSpec)
  {
    $this->lookerColumnSpec = $lookerColumnSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec
   */
  public function getLookerColumnSpec()
  {
    return $this->lookerColumnSpec;
  }
  /**
   * Optional. A column's mode indicates whether values in this column are
   * required, nullable, or repeated. Only `NULLABLE`, `REQUIRED`, and
   * `REPEATED` values are supported. Default mode is `NULLABLE`.
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
   * Optional. Ordinal position
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
   * Optional. The subtype of the RANGE, if the type of this field is RANGE. If
   * the type is RANGE, this field is required. Possible values for the field
   * element type of a RANGE include: * DATE * DATETIME * TIMESTAMP
   *
   * @param GoogleCloudDatacatalogV1ColumnSchemaFieldElementType $rangeElementType
   */
  public function setRangeElementType(GoogleCloudDatacatalogV1ColumnSchemaFieldElementType $rangeElementType)
  {
    $this->rangeElementType = $rangeElementType;
  }
  /**
   * @return GoogleCloudDatacatalogV1ColumnSchemaFieldElementType
   */
  public function getRangeElementType()
  {
    return $this->rangeElementType;
  }
  /**
   * Optional. Schema of sub-columns. A column can have zero or more sub-
   * columns.
   *
   * @param GoogleCloudDatacatalogV1ColumnSchema[] $subcolumns
   */
  public function setSubcolumns($subcolumns)
  {
    $this->subcolumns = $subcolumns;
  }
  /**
   * @return GoogleCloudDatacatalogV1ColumnSchema[]
   */
  public function getSubcolumns()
  {
    return $this->subcolumns;
  }
  /**
   * Required. Type of the column. Must be a UTF-8 string with the maximum size
   * of 128 bytes.
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
class_alias(GoogleCloudDatacatalogV1ColumnSchema::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ColumnSchema');
