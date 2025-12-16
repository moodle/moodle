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

class ReportAgentStateRequest extends \Google\Model
{
  protected $agentInfoType = AgentInfo::class;
  protected $agentInfoDataType = '';
  protected $agentTimingInfoType = AgentTimingInfo::class;
  protected $agentTimingInfoDataType = '';
  protected $metadataType = AgentMetadata::class;
  protected $metadataDataType = '';

  /**
   * Agent info.
   *
   * @param AgentInfo $agentInfo
   */
  public function setAgentInfo(AgentInfo $agentInfo)
  {
    $this->agentInfo = $agentInfo;
  }
  /**
   * @return AgentInfo
   */
  public function getAgentInfo()
  {
    return $this->agentInfo;
  }
  /**
   * Agent timing info.
   *
   * @param AgentTimingInfo $agentTimingInfo
   */
  public function setAgentTimingInfo(AgentTimingInfo $agentTimingInfo)
  {
    $this->agentTimingInfo = $agentTimingInfo;
  }
  /**
   * @return AgentTimingInfo
   */
  public function getAgentTimingInfo()
  {
    return $this->agentTimingInfo;
  }
  /**
   * Agent metadata.
   *
   * @param AgentMetadata $metadata
   */
  public function setMetadata(AgentMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return AgentMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportAgentStateRequest::class, 'Google_Service_Batch_ReportAgentStateRequest');
