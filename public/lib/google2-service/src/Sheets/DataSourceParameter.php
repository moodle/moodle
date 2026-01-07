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

namespace Google\Service\Sheets;

class DataSourceParameter extends \Google\Model
{
  /**
   * Named parameter. Must be a legitimate identifier for the DataSource that
   * supports it. For example, [BigQuery
   * identifier](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/lexical#identifiers).
   *
   * @var string
   */
  public $name;
  /**
   * ID of a NamedRange. Its size must be 1x1.
   *
   * @var string
   */
  public $namedRangeId;
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';

  /**
   * Named parameter. Must be a legitimate identifier for the DataSource that
   * supports it. For example, [BigQuery
   * identifier](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/lexical#identifiers).
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
   * ID of a NamedRange. Its size must be 1x1.
   *
   * @param string $namedRangeId
   */
  public function setNamedRangeId($namedRangeId)
  {
    $this->namedRangeId = $namedRangeId;
  }
  /**
   * @return string
   */
  public function getNamedRangeId()
  {
    return $this->namedRangeId;
  }
  /**
   * A range that contains the value of the parameter. Its size must be 1x1.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceParameter::class, 'Google_Service_Sheets_DataSourceParameter');
