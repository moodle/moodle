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

namespace Google\Service\Storagetransfer;

class AgentPool extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * This is an initialization state. During this stage, resources are allocated
   * for the AgentPool.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Determines that the AgentPool is created for use. At this state, Agents can
   * join the AgentPool and participate in the transfer jobs in that pool.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * Determines that the AgentPool deletion has been initiated, and all the
   * resources are scheduled to be cleaned up and freed.
   */
  public const STATE_DELETING = 'DELETING';
  protected $bandwidthLimitType = BandwidthLimit::class;
  protected $bandwidthLimitDataType = '';
  /**
   * Specifies the client-specified AgentPool description.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Specifies a unique string that identifies the agent pool. Format:
   * `projects/{project_id}/agentPools/{agent_pool_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Specifies the state of the AgentPool.
   *
   * @var string
   */
  public $state;

  /**
   * Specifies the bandwidth limit details. If this field is unspecified, the
   * default value is set as 'No Limit'.
   *
   * @param BandwidthLimit $bandwidthLimit
   */
  public function setBandwidthLimit(BandwidthLimit $bandwidthLimit)
  {
    $this->bandwidthLimit = $bandwidthLimit;
  }
  /**
   * @return BandwidthLimit
   */
  public function getBandwidthLimit()
  {
    return $this->bandwidthLimit;
  }
  /**
   * Specifies the client-specified AgentPool description.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. Specifies a unique string that identifies the agent pool. Format:
   * `projects/{project_id}/agentPools/{agent_pool_id}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Specifies the state of the AgentPool.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, CREATED, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentPool::class, 'Google_Service_Storagetransfer_AgentPool');
