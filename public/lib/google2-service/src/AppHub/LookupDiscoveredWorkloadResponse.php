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

namespace Google\Service\AppHub;

class LookupDiscoveredWorkloadResponse extends \Google\Model
{
  protected $discoveredWorkloadType = DiscoveredWorkload::class;
  protected $discoveredWorkloadDataType = '';

  /**
   * Discovered Workload if exists, empty otherwise.
   *
   * @param DiscoveredWorkload $discoveredWorkload
   */
  public function setDiscoveredWorkload(DiscoveredWorkload $discoveredWorkload)
  {
    $this->discoveredWorkload = $discoveredWorkload;
  }
  /**
   * @return DiscoveredWorkload
   */
  public function getDiscoveredWorkload()
  {
    return $this->discoveredWorkload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LookupDiscoveredWorkloadResponse::class, 'Google_Service_AppHub_LookupDiscoveredWorkloadResponse');
