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

namespace Google\Service\Batch;

class PlacementPolicy extends \Google\Model
{
  /**
   * UNSPECIFIED vs. COLLOCATED (default UNSPECIFIED). Use COLLOCATED when you
   * want VMs to be located close to each other for low network latency between
   * the VMs. No placement policy will be generated when collocation is
   * UNSPECIFIED.
   *
   * @var string
   */
  public $collocation;
  /**
   * When specified, causes the job to fail if more than max_distance logical
   * switches are required between VMs. Batch uses the most compact possible
   * placement of VMs even when max_distance is not specified. An explicit
   * max_distance makes that level of compactness a strict requirement. Not yet
   * implemented
   *
   * @var string
   */
  public $maxDistance;

  /**
   * UNSPECIFIED vs. COLLOCATED (default UNSPECIFIED). Use COLLOCATED when you
   * want VMs to be located close to each other for low network latency between
   * the VMs. No placement policy will be generated when collocation is
   * UNSPECIFIED.
   *
   * @param string $collocation
   */
  public function setCollocation($collocation)
  {
    $this->collocation = $collocation;
  }
  /**
   * @return string
   */
  public function getCollocation()
  {
    return $this->collocation;
  }
  /**
   * When specified, causes the job to fail if more than max_distance logical
   * switches are required between VMs. Batch uses the most compact possible
   * placement of VMs even when max_distance is not specified. An explicit
   * max_distance makes that level of compactness a strict requirement. Not yet
   * implemented
   *
   * @param string $maxDistance
   */
  public function setMaxDistance($maxDistance)
  {
    $this->maxDistance = $maxDistance;
  }
  /**
   * @return string
   */
  public function getMaxDistance()
  {
    return $this->maxDistance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacementPolicy::class, 'Google_Service_Batch_PlacementPolicy');
