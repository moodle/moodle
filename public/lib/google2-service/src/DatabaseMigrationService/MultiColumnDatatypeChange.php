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

namespace Google\Service\DatabaseMigrationService;

class MultiColumnDatatypeChange extends \Google\Model
{
  /**
   * Optional. Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * Required. New data type.
   *
   * @var string
   */
  public $newDataType;
  /**
   * Optional. Column fractional seconds precision - used only for timestamp
   * based datatypes - if not specified and relevant uses the source column
   * fractional seconds precision.
   *
   * @var int
   */
  public $overrideFractionalSecondsPrecision;
  /**
   * Optional. Column length - e.g. varchar (50) - if not specified and relevant
   * uses the source column length.
   *
   * @var string
   */
  public $overrideLength;
  /**
   * Optional. Column precision - when relevant - if not specified and relevant
   * uses the source column precision.
   *
   * @var int
   */
  public $overridePrecision;
  /**
   * Optional. Column scale - when relevant - if not specified and relevant uses
   * the source column scale.
   *
   * @var int
   */
  public $overrideScale;
  /**
   * Required. Filter on source data type.
   *
   * @var string
   */
  public $sourceDataTypeFilter;
  protected $sourceNumericFilterType = SourceNumericFilter::class;
  protected $sourceNumericFilterDataType = '';
  protected $sourceTextFilterType = SourceTextFilter::class;
  protected $sourceTextFilterDataType = '';

  /**
   * Optional. Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * Required. New data type.
   *
   * @param string $newDataType
   */
  public function setNewDataType($newDataType)
  {
    $this->newDataType = $newDataType;
  }
  /**
   * @return string
   */
  public function getNewDataType()
  {
    return $this->newDataType;
  }
  /**
   * Optional. Column fractional seconds precision - used only for timestamp
   * based datatypes - if not specified and relevant uses the source column
   * fractional seconds precision.
   *
   * @param int $overrideFractionalSecondsPrecision
   */
  public function setOverrideFractionalSecondsPrecision($overrideFractionalSecondsPrecision)
  {
    $this->overrideFractionalSecondsPrecision = $overrideFractionalSecondsPrecision;
  }
  /**
   * @return int
   */
  public function getOverrideFractionalSecondsPrecision()
  {
    return $this->overrideFractionalSecondsPrecision;
  }
  /**
   * Optional. Column length - e.g. varchar (50) - if not specified and relevant
   * uses the source column length.
   *
   * @param string $overrideLength
   */
  public function setOverrideLength($overrideLength)
  {
    $this->overrideLength = $overrideLength;
  }
  /**
   * @return string
   */
  public function getOverrideLength()
  {
    return $this->overrideLength;
  }
  /**
   * Optional. Column precision - when relevant - if not specified and relevant
   * uses the source column precision.
   *
   * @param int $overridePrecision
   */
  public function setOverridePrecision($overridePrecision)
  {
    $this->overridePrecision = $overridePrecision;
  }
  /**
   * @return int
   */
  public function getOverridePrecision()
  {
    return $this->overridePrecision;
  }
  /**
   * Optional. Column scale - when relevant - if not specified and relevant uses
   * the source column scale.
   *
   * @param int $overrideScale
   */
  public function setOverrideScale($overrideScale)
  {
    $this->overrideScale = $overrideScale;
  }
  /**
   * @return int
   */
  public function getOverrideScale()
  {
    return $this->overrideScale;
  }
  /**
   * Required. Filter on source data type.
   *
   * @param string $sourceDataTypeFilter
   */
  public function setSourceDataTypeFilter($sourceDataTypeFilter)
  {
    $this->sourceDataTypeFilter = $sourceDataTypeFilter;
  }
  /**
   * @return string
   */
  public function getSourceDataTypeFilter()
  {
    return $this->sourceDataTypeFilter;
  }
  /**
   * Optional. Filter for fixed point number data types such as NUMERIC/NUMBER.
   *
   * @param SourceNumericFilter $sourceNumericFilter
   */
  public function setSourceNumericFilter(SourceNumericFilter $sourceNumericFilter)
  {
    $this->sourceNumericFilter = $sourceNumericFilter;
  }
  /**
   * @return SourceNumericFilter
   */
  public function getSourceNumericFilter()
  {
    return $this->sourceNumericFilter;
  }
  /**
   * Optional. Filter for text-based data types like varchar.
   *
   * @param SourceTextFilter $sourceTextFilter
   */
  public function setSourceTextFilter(SourceTextFilter $sourceTextFilter)
  {
    $this->sourceTextFilter = $sourceTextFilter;
  }
  /**
   * @return SourceTextFilter
   */
  public function getSourceTextFilter()
  {
    return $this->sourceTextFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiColumnDatatypeChange::class, 'Google_Service_DatabaseMigrationService_MultiColumnDatatypeChange');
