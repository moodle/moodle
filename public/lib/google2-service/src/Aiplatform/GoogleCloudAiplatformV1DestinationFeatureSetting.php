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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DestinationFeatureSetting extends \Google\Model
{
  /**
   * Specify the field name in the export destination. If not specified, Feature
   * ID is used.
   *
   * @var string
   */
  public $destinationField;
  /**
   * Required. The ID of the Feature to apply the setting to.
   *
   * @var string
   */
  public $featureId;

  /**
   * Specify the field name in the export destination. If not specified, Feature
   * ID is used.
   *
   * @param string $destinationField
   */
  public function setDestinationField($destinationField)
  {
    $this->destinationField = $destinationField;
  }
  /**
   * @return string
   */
  public function getDestinationField()
  {
    return $this->destinationField;
  }
  /**
   * Required. The ID of the Feature to apply the setting to.
   *
   * @param string $featureId
   */
  public function setFeatureId($featureId)
  {
    $this->featureId = $featureId;
  }
  /**
   * @return string
   */
  public function getFeatureId()
  {
    return $this->featureId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DestinationFeatureSetting::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DestinationFeatureSetting');
