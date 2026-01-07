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

class UpdateDimensionPropertiesRequest extends \Google\Model
{
  protected $dataSourceSheetRangeType = DataSourceSheetDimensionRange::class;
  protected $dataSourceSheetRangeDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `properties` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field.
   *
   * @var string
   */
  public $fields;
  protected $propertiesType = DimensionProperties::class;
  protected $propertiesDataType = '';
  protected $rangeType = DimensionRange::class;
  protected $rangeDataType = '';

  /**
   * The columns on a data source sheet to update.
   *
   * @param DataSourceSheetDimensionRange $dataSourceSheetRange
   */
  public function setDataSourceSheetRange(DataSourceSheetDimensionRange $dataSourceSheetRange)
  {
    $this->dataSourceSheetRange = $dataSourceSheetRange;
  }
  /**
   * @return DataSourceSheetDimensionRange
   */
  public function getDataSourceSheetRange()
  {
    return $this->dataSourceSheetRange;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `properties` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field.
   *
   * @param string $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Properties to update.
   *
   * @param DimensionProperties $properties
   */
  public function setProperties(DimensionProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DimensionProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The rows or columns to update.
   *
   * @param DimensionRange $range
   */
  public function setRange(DimensionRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return DimensionRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDimensionPropertiesRequest::class, 'Google_Service_Sheets_UpdateDimensionPropertiesRequest');
