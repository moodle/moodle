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

namespace Google\Service\OSConfig;

class GoogleCloudOsconfigV2OrchestrationScopeSelector extends \Google\Model
{
  protected $locationSelectorType = GoogleCloudOsconfigV2OrchestrationScopeLocationSelector::class;
  protected $locationSelectorDataType = '';
  protected $resourceHierarchySelectorType = GoogleCloudOsconfigV2OrchestrationScopeResourceHierarchySelector::class;
  protected $resourceHierarchySelectorDataType = '';

  /**
   * Selector for selecting locations.
   *
   * @param GoogleCloudOsconfigV2OrchestrationScopeLocationSelector $locationSelector
   */
  public function setLocationSelector(GoogleCloudOsconfigV2OrchestrationScopeLocationSelector $locationSelector)
  {
    $this->locationSelector = $locationSelector;
  }
  /**
   * @return GoogleCloudOsconfigV2OrchestrationScopeLocationSelector
   */
  public function getLocationSelector()
  {
    return $this->locationSelector;
  }
  /**
   * Selector for selecting resource hierarchy.
   *
   * @param GoogleCloudOsconfigV2OrchestrationScopeResourceHierarchySelector $resourceHierarchySelector
   */
  public function setResourceHierarchySelector(GoogleCloudOsconfigV2OrchestrationScopeResourceHierarchySelector $resourceHierarchySelector)
  {
    $this->resourceHierarchySelector = $resourceHierarchySelector;
  }
  /**
   * @return GoogleCloudOsconfigV2OrchestrationScopeResourceHierarchySelector
   */
  public function getResourceHierarchySelector()
  {
    return $this->resourceHierarchySelector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOsconfigV2OrchestrationScopeSelector::class, 'Google_Service_OSConfig_GoogleCloudOsconfigV2OrchestrationScopeSelector');
