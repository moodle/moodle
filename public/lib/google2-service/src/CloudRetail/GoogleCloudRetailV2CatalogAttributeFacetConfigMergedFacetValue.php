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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * All the previous values are replaced by this merged facet value. This
   * merged_value must be non-empty and can have up to 128 characters.
   *
   * @var string
   */
  public $mergedValue;
  /**
   * All the facet values that are replaces by the same merged_value that
   * follows. The maximum number of values per MergedFacetValue is 25. Each
   * value can have up to 128 characters.
   *
   * @var string[]
   */
  public $values;

  /**
   * All the previous values are replaced by this merged facet value. This
   * merged_value must be non-empty and can have up to 128 characters.
   *
   * @param string $mergedValue
   */
  public function setMergedValue($mergedValue)
  {
    $this->mergedValue = $mergedValue;
  }
  /**
   * @return string
   */
  public function getMergedValue()
  {
    return $this->mergedValue;
  }
  /**
   * All the facet values that are replaces by the same merged_value that
   * follows. The maximum number of values per MergedFacetValue is 25. Each
   * value can have up to 128 characters.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue');
