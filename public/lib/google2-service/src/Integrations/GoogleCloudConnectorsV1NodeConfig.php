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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1NodeConfig extends \Google\Model
{
  /**
   * Optional. Maximum number of nodes in the runtime nodes.
   *
   * @var int
   */
  public $maxNodeCount;
  /**
   * Optional. Minimum number of nodes in the runtime nodes.
   *
   * @var int
   */
  public $minNodeCount;

  /**
   * Optional. Maximum number of nodes in the runtime nodes.
   *
   * @param int $maxNodeCount
   */
  public function setMaxNodeCount($maxNodeCount)
  {
    $this->maxNodeCount = $maxNodeCount;
  }
  /**
   * @return int
   */
  public function getMaxNodeCount()
  {
    return $this->maxNodeCount;
  }
  /**
   * Optional. Minimum number of nodes in the runtime nodes.
   *
   * @param int $minNodeCount
   */
  public function setMinNodeCount($minNodeCount)
  {
    $this->minNodeCount = $minNodeCount;
  }
  /**
   * @return int
   */
  public function getMinNodeCount()
  {
    return $this->minNodeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1NodeConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1NodeConfig');
