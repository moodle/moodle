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

class InterconnectGroupIntent extends \Google\Model
{
  public const TOPOLOGY_CAPABILITY_NO_SLA = 'NO_SLA';
  public const TOPOLOGY_CAPABILITY_PRODUCTION_CRITICAL = 'PRODUCTION_CRITICAL';
  public const TOPOLOGY_CAPABILITY_PRODUCTION_NON_CRITICAL = 'PRODUCTION_NON_CRITICAL';
  public const TOPOLOGY_CAPABILITY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * @var string
   */
  public $topologyCapability;

  /**
   * @param self::TOPOLOGY_CAPABILITY_* $topologyCapability
   */
  public function setTopologyCapability($topologyCapability)
  {
    $this->topologyCapability = $topologyCapability;
  }
  /**
   * @return self::TOPOLOGY_CAPABILITY_*
   */
  public function getTopologyCapability()
  {
    return $this->topologyCapability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupIntent::class, 'Google_Service_Compute_InterconnectGroupIntent');
