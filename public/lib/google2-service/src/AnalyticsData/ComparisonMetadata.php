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

namespace Google\Service\AnalyticsData;

class ComparisonMetadata extends \Google\Model
{
  /**
   * This comparison's resource name. Useable in [Comparison](#Comparison)'s
   * `comparison` field. For example, 'comparisons/1234'.
   *
   * @var string
   */
  public $apiName;
  /**
   * This comparison's description.
   *
   * @var string
   */
  public $description;
  /**
   * This comparison's name within the Google Analytics user interface.
   *
   * @var string
   */
  public $uiName;

  /**
   * This comparison's resource name. Useable in [Comparison](#Comparison)'s
   * `comparison` field. For example, 'comparisons/1234'.
   *
   * @param string $apiName
   */
  public function setApiName($apiName)
  {
    $this->apiName = $apiName;
  }
  /**
   * @return string
   */
  public function getApiName()
  {
    return $this->apiName;
  }
  /**
   * This comparison's description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * This comparison's name within the Google Analytics user interface.
   *
   * @param string $uiName
   */
  public function setUiName($uiName)
  {
    $this->uiName = $uiName;
  }
  /**
   * @return string
   */
  public function getUiName()
  {
    return $this->uiName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComparisonMetadata::class, 'Google_Service_AnalyticsData_ComparisonMetadata');
