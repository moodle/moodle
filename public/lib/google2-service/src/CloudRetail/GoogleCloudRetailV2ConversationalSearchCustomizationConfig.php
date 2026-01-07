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

class GoogleCloudRetailV2ConversationalSearchCustomizationConfig extends \Google\Model
{
  /**
   * Required. Resource name of the catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   *
   * @var string
   */
  public $catalog;
  protected $intentClassificationConfigType = GoogleCloudRetailV2IntentClassificationConfig::class;
  protected $intentClassificationConfigDataType = '';
  /**
   * Optional. The retailer's display name that could be used in our LLM
   * answers. Example - "Google"
   *
   * @var string
   */
  public $retailerDisplayName;

  /**
   * Required. Resource name of the catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   *
   * @param string $catalog
   */
  public function setCatalog($catalog)
  {
    $this->catalog = $catalog;
  }
  /**
   * @return string
   */
  public function getCatalog()
  {
    return $this->catalog;
  }
  /**
   * Optional. The configs for intent classification.
   *
   * @param GoogleCloudRetailV2IntentClassificationConfig $intentClassificationConfig
   */
  public function setIntentClassificationConfig(GoogleCloudRetailV2IntentClassificationConfig $intentClassificationConfig)
  {
    $this->intentClassificationConfig = $intentClassificationConfig;
  }
  /**
   * @return GoogleCloudRetailV2IntentClassificationConfig
   */
  public function getIntentClassificationConfig()
  {
    return $this->intentClassificationConfig;
  }
  /**
   * Optional. The retailer's display name that could be used in our LLM
   * answers. Example - "Google"
   *
   * @param string $retailerDisplayName
   */
  public function setRetailerDisplayName($retailerDisplayName)
  {
    $this->retailerDisplayName = $retailerDisplayName;
  }
  /**
   * @return string
   */
  public function getRetailerDisplayName()
  {
    return $this->retailerDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchCustomizationConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchCustomizationConfig');
