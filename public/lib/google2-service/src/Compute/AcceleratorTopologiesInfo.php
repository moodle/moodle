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

class AcceleratorTopologiesInfo extends \Google\Collection
{
  protected $collection_key = 'acceleratorTopologyInfos';
  protected $acceleratorTopologyInfosType = AcceleratorTopologiesInfoAcceleratorTopologyInfo::class;
  protected $acceleratorTopologyInfosDataType = 'array';

  /**
   * Info for each accelerator topology.
   *
   * @param AcceleratorTopologiesInfoAcceleratorTopologyInfo[] $acceleratorTopologyInfos
   */
  public function setAcceleratorTopologyInfos($acceleratorTopologyInfos)
  {
    $this->acceleratorTopologyInfos = $acceleratorTopologyInfos;
  }
  /**
   * @return AcceleratorTopologiesInfoAcceleratorTopologyInfo[]
   */
  public function getAcceleratorTopologyInfos()
  {
    return $this->acceleratorTopologyInfos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcceleratorTopologiesInfo::class, 'Google_Service_Compute_AcceleratorTopologiesInfo');
