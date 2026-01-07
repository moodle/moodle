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

class GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_LOOKER_COLUMN_TYPE_UNSPECIFIED = 'LOOKER_COLUMN_TYPE_UNSPECIFIED';
  /**
   * Dimension.
   */
  public const TYPE_DIMENSION = 'DIMENSION';
  /**
   * Dimension group - parent for Dimension.
   */
  public const TYPE_DIMENSION_GROUP = 'DIMENSION_GROUP';
  /**
   * Filter.
   */
  public const TYPE_FILTER = 'FILTER';
  /**
   * Measure.
   */
  public const TYPE_MEASURE = 'MEASURE';
  /**
   * Parameter.
   */
  public const TYPE_PARAMETER = 'PARAMETER';
  /**
   * Looker specific column type of this column.
   *
   * @var string
   */
  public $type;

  /**
   * Looker specific column type of this column.
   *
   * Accepted values: LOOKER_COLUMN_TYPE_UNSPECIFIED, DIMENSION,
   * DIMENSION_GROUP, FILTER, MEASURE, PARAMETER
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
class_alias(GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ColumnSchemaLookerColumnSpec');
