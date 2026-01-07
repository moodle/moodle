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

class GoogleCloudIntegrationsV1alphaSuspension extends \Google\Model
{
  /**
   * Unset state.
   */
  public const STATE_RESOLUTION_STATE_UNSPECIFIED = 'RESOLUTION_STATE_UNSPECIFIED';
  /**
   * The suspension has not yet been resolved.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The resolver has rejected the suspension.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * The resolver has lifted the suspension.
   */
  public const STATE_LIFTED = 'LIFTED';
  protected $approvalConfigType = GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig::class;
  protected $approvalConfigDataType = '';
  protected $auditType = GoogleCloudIntegrationsV1alphaSuspensionAudit::class;
  protected $auditDataType = '';
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. ID of the associated execution.
   *
   * @var string
   */
  public $eventExecutionInfoId;
  /**
   * Required. The name of the originating integration.
   *
   * @var string
   */
  public $integration;
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $lastModifyTime;
  /**
   * Resource name for suspensions suspension/{suspension_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. State of this suspension, indicating what action a resolver has
   * taken.
   *
   * @var string
   */
  public $state;
  protected $suspensionConfigType = EnterpriseCrmEventbusProtoSuspensionConfig::class;
  protected $suspensionConfigDataType = '';
  /**
   * Required. Task id of the associated SuspensionTask.
   *
   * @var string
   */
  public $taskId;

  /**
   * Controls the notifications and approval permissions for this suspension.
   *
   * @param GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig $approvalConfig
   */
  public function setApprovalConfig(GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig $approvalConfig)
  {
    $this->approvalConfig = $approvalConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig
   */
  public function getApprovalConfig()
  {
    return $this->approvalConfig;
  }
  /**
   * Metadata pertaining to the resolution of this suspension.
   *
   * @param GoogleCloudIntegrationsV1alphaSuspensionAudit $audit
   */
  public function setAudit(GoogleCloudIntegrationsV1alphaSuspensionAudit $audit)
  {
    $this->audit = $audit;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaSuspensionAudit
   */
  public function getAudit()
  {
    return $this->audit;
  }
  /**
   * Output only. Auto-generated.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. ID of the associated execution.
   *
   * @param string $eventExecutionInfoId
   */
  public function setEventExecutionInfoId($eventExecutionInfoId)
  {
    $this->eventExecutionInfoId = $eventExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getEventExecutionInfoId()
  {
    return $this->eventExecutionInfoId;
  }
  /**
   * Required. The name of the originating integration.
   *
   * @param string $integration
   */
  public function setIntegration($integration)
  {
    $this->integration = $integration;
  }
  /**
   * @return string
   */
  public function getIntegration()
  {
    return $this->integration;
  }
  /**
   * Output only. Auto-generated.
   *
   * @param string $lastModifyTime
   */
  public function setLastModifyTime($lastModifyTime)
  {
    $this->lastModifyTime = $lastModifyTime;
  }
  /**
   * @return string
   */
  public function getLastModifyTime()
  {
    return $this->lastModifyTime;
  }
  /**
   * Resource name for suspensions suspension/{suspension_id}
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
   * Required. State of this suspension, indicating what action a resolver has
   * taken.
   *
   * Accepted values: RESOLUTION_STATE_UNSPECIFIED, PENDING, REJECTED, LIFTED
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
  /**
   * Controls the notifications and resolver permissions for this suspension.
   *
   * @deprecated
   * @param EnterpriseCrmEventbusProtoSuspensionConfig $suspensionConfig
   */
  public function setSuspensionConfig(EnterpriseCrmEventbusProtoSuspensionConfig $suspensionConfig)
  {
    $this->suspensionConfig = $suspensionConfig;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmEventbusProtoSuspensionConfig
   */
  public function getSuspensionConfig()
  {
    return $this->suspensionConfig;
  }
  /**
   * Required. Task id of the associated SuspensionTask.
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaSuspension::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSuspension');
