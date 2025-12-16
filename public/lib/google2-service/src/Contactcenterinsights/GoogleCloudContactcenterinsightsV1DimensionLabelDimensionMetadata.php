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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1DimensionLabelDimensionMetadata extends \Google\Model
{
  /**
   * Optional. The label key.
   *
   * @var string
   */
  public $labelKey;
  /**
   * Optional. The label value.
   *
   * @var string
   */
  public $labelValue;

  /**
   * Optional. The label key.
   *
   * @param string $labelKey
   */
  public function setLabelKey($labelKey)
  {
    $this->labelKey = $labelKey;
  }
  /**
   * @return string
   */
  public function getLabelKey()
  {
    return $this->labelKey;
  }
  /**
   * Optional. The label value.
   *
   * @param string $labelValue
   */
  public function setLabelValue($labelValue)
  {
    $this->labelValue = $labelValue;
  }
  /**
   * @return string
   */
  public function getLabelValue()
  {
    return $this->labelValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1DimensionLabelDimensionMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1DimensionLabelDimensionMetadata');
