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

namespace Google\Service\Compute;

class FutureResourcesSpecTargetResources extends \Google\Model
{
  protected $aggregateResourcesType = FutureResourcesSpecAggregateResources::class;
  protected $aggregateResourcesDataType = '';
  protected $specificSkuResourcesType = FutureResourcesSpecSpecificSKUResources::class;
  protected $specificSkuResourcesDataType = '';

  /**
   * @param FutureResourcesSpecAggregateResources $aggregateResources
   */
  public function setAggregateResources(FutureResourcesSpecAggregateResources $aggregateResources)
  {
    $this->aggregateResources = $aggregateResources;
  }
  /**
   * @return FutureResourcesSpecAggregateResources
   */
  public function getAggregateResources()
  {
    return $this->aggregateResources;
  }
  /**
   * @param FutureResourcesSpecSpecificSKUResources $specificSkuResources
   */
  public function setSpecificSkuResources(FutureResourcesSpecSpecificSKUResources $specificSkuResources)
  {
    $this->specificSkuResources = $specificSkuResources;
  }
  /**
   * @return FutureResourcesSpecSpecificSKUResources
   */
  public function getSpecificSkuResources()
  {
    return $this->specificSkuResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpecTargetResources::class, 'Google_Service_Compute_FutureResourcesSpecTargetResources');
