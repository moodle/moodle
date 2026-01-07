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

class ConditionalColumnSetValue extends \Google\Model
{
  /**
   * Optional. Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  protected $sourceNumericFilterType = SourceNumericFilter::class;
  protected $sourceNumericFilterDataType = '';
  protected $sourceTextFilterType = SourceTextFilter::class;
  protected $sourceTextFilterDataType = '';
  protected $valueTransformationType = ValueTransformation::class;
  protected $valueTransformationDataType = '';

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
   * Optional. Optional filter on source column precision and scale. Used for
   * fixed point numbers such as NUMERIC/NUMBER data types.
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
   * Optional. Optional filter on source column length. Used for text based data
   * types like varchar.
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
  /**
   * Required. Description of data transformation during migration.
   *
   * @param ValueTransformation $valueTransformation
   */
  public function setValueTransformation(ValueTransformation $valueTransformation)
  {
    $this->valueTransformation = $valueTransformation;
  }
  /**
   * @return ValueTransformation
   */
  public function getValueTransformation()
  {
    return $this->valueTransformation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConditionalColumnSetValue::class, 'Google_Service_DatabaseMigrationService_ConditionalColumnSetValue');
