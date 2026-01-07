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

namespace Google\Service\WorkloadManager;

class Insight extends \Google\Model
{
  protected $agentStatusType = AgentStatus::class;
  protected $agentStatusDataType = '';
  /**
   * Optional. The instance id where the insight is generated from
   *
   * @var string
   */
  public $instanceId;
  protected $openShiftValidationType = OpenShiftValidation::class;
  protected $openShiftValidationDataType = '';
  protected $sapDiscoveryType = SapDiscovery::class;
  protected $sapDiscoveryDataType = '';
  protected $sapValidationType = SapValidation::class;
  protected $sapValidationDataType = '';
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $sentTime;
  protected $sqlserverValidationType = SqlserverValidation::class;
  protected $sqlserverValidationDataType = '';
  protected $torsoValidationType = TorsoValidation::class;
  protected $torsoValidationDataType = '';

  /**
   * The insights data for the agent status.
   *
   * @param AgentStatus $agentStatus
   */
  public function setAgentStatus(AgentStatus $agentStatus)
  {
    $this->agentStatus = $agentStatus;
  }
  /**
   * @return AgentStatus
   */
  public function getAgentStatus()
  {
    return $this->agentStatus;
  }
  /**
   * Optional. The instance id where the insight is generated from
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * The insights data for the OpenShift workload validation.
   *
   * @param OpenShiftValidation $openShiftValidation
   */
  public function setOpenShiftValidation(OpenShiftValidation $openShiftValidation)
  {
    $this->openShiftValidation = $openShiftValidation;
  }
  /**
   * @return OpenShiftValidation
   */
  public function getOpenShiftValidation()
  {
    return $this->openShiftValidation;
  }
  /**
   * The insights data for SAP system discovery. This is a copy of SAP System
   * proto and should get updated whenever that one changes.
   *
   * @param SapDiscovery $sapDiscovery
   */
  public function setSapDiscovery(SapDiscovery $sapDiscovery)
  {
    $this->sapDiscovery = $sapDiscovery;
  }
  /**
   * @return SapDiscovery
   */
  public function getSapDiscovery()
  {
    return $this->sapDiscovery;
  }
  /**
   * The insights data for the SAP workload validation.
   *
   * @param SapValidation $sapValidation
   */
  public function setSapValidation(SapValidation $sapValidation)
  {
    $this->sapValidation = $sapValidation;
  }
  /**
   * @return SapValidation
   */
  public function getSapValidation()
  {
    return $this->sapValidation;
  }
  /**
   * Output only. [Output only] Create time stamp
   *
   * @param string $sentTime
   */
  public function setSentTime($sentTime)
  {
    $this->sentTime = $sentTime;
  }
  /**
   * @return string
   */
  public function getSentTime()
  {
    return $this->sentTime;
  }
  /**
   * The insights data for the sqlserver workload validation.
   *
   * @param SqlserverValidation $sqlserverValidation
   */
  public function setSqlserverValidation(SqlserverValidation $sqlserverValidation)
  {
    $this->sqlserverValidation = $sqlserverValidation;
  }
  /**
   * @return SqlserverValidation
   */
  public function getSqlserverValidation()
  {
    return $this->sqlserverValidation;
  }
  /**
   * The insights data for workload validation of torso workloads.
   *
   * @param TorsoValidation $torsoValidation
   */
  public function setTorsoValidation(TorsoValidation $torsoValidation)
  {
    $this->torsoValidation = $torsoValidation;
  }
  /**
   * @return TorsoValidation
   */
  public function getTorsoValidation()
  {
    return $this->torsoValidation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Insight::class, 'Google_Service_WorkloadManager_Insight');
