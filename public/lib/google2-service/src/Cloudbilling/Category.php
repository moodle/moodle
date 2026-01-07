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

namespace Google\Service\Cloudbilling;

class Category extends \Google\Model
{
  /**
   * The type of product the SKU refers to. Example: "Compute", "Storage",
   * "Network", "ApplicationServices" etc.
   *
   * @var string
   */
  public $resourceFamily;
  /**
   * A group classification for related SKUs. Example: "RAM", "GPU",
   * "Prediction", "Ops", "GoogleEgress" etc.
   *
   * @var string
   */
  public $resourceGroup;
  /**
   * The display name of the service this SKU belongs to.
   *
   * @var string
   */
  public $serviceDisplayName;
  /**
   * Represents how the SKU is consumed. Example: "OnDemand", "Preemptible",
   * "Commit1Mo", "Commit1Yr" etc.
   *
   * @var string
   */
  public $usageType;

  /**
   * The type of product the SKU refers to. Example: "Compute", "Storage",
   * "Network", "ApplicationServices" etc.
   *
   * @param string $resourceFamily
   */
  public function setResourceFamily($resourceFamily)
  {
    $this->resourceFamily = $resourceFamily;
  }
  /**
   * @return string
   */
  public function getResourceFamily()
  {
    return $this->resourceFamily;
  }
  /**
   * A group classification for related SKUs. Example: "RAM", "GPU",
   * "Prediction", "Ops", "GoogleEgress" etc.
   *
   * @param string $resourceGroup
   */
  public function setResourceGroup($resourceGroup)
  {
    $this->resourceGroup = $resourceGroup;
  }
  /**
   * @return string
   */
  public function getResourceGroup()
  {
    return $this->resourceGroup;
  }
  /**
   * The display name of the service this SKU belongs to.
   *
   * @param string $serviceDisplayName
   */
  public function setServiceDisplayName($serviceDisplayName)
  {
    $this->serviceDisplayName = $serviceDisplayName;
  }
  /**
   * @return string
   */
  public function getServiceDisplayName()
  {
    return $this->serviceDisplayName;
  }
  /**
   * Represents how the SKU is consumed. Example: "OnDemand", "Preemptible",
   * "Commit1Mo", "Commit1Yr" etc.
   *
   * @param string $usageType
   */
  public function setUsageType($usageType)
  {
    $this->usageType = $usageType;
  }
  /**
   * @return string
   */
  public function getUsageType()
  {
    return $this->usageType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Category::class, 'Google_Service_Cloudbilling_Category');
