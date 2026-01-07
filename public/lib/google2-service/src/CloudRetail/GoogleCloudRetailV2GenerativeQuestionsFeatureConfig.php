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

class GoogleCloudRetailV2GenerativeQuestionsFeatureConfig extends \Google\Model
{
  /**
   * Required. Resource name of the affected catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   *
   * @var string
   */
  public $catalog;
  /**
   * Optional. Determines whether questions will be used at serving time. Note:
   * This feature cannot be enabled until initial data requirements are
   * satisfied.
   *
   * @var bool
   */
  public $featureEnabled;
  /**
   * Optional. Minimum number of products in the response to trigger follow-up
   * questions. Value must be 0 or positive.
   *
   * @var int
   */
  public $minimumProducts;

  /**
   * Required. Resource name of the affected catalog. Format:
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
   * Optional. Determines whether questions will be used at serving time. Note:
   * This feature cannot be enabled until initial data requirements are
   * satisfied.
   *
   * @param bool $featureEnabled
   */
  public function setFeatureEnabled($featureEnabled)
  {
    $this->featureEnabled = $featureEnabled;
  }
  /**
   * @return bool
   */
  public function getFeatureEnabled()
  {
    return $this->featureEnabled;
  }
  /**
   * Optional. Minimum number of products in the response to trigger follow-up
   * questions. Value must be 0 or positive.
   *
   * @param int $minimumProducts
   */
  public function setMinimumProducts($minimumProducts)
  {
    $this->minimumProducts = $minimumProducts;
  }
  /**
   * @return int
   */
  public function getMinimumProducts()
  {
    return $this->minimumProducts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2GenerativeQuestionsFeatureConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2GenerativeQuestionsFeatureConfig');
