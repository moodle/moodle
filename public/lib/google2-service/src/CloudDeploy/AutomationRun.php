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

namespace Google\Service\CloudDeploy;

class AutomationRun extends \Google\Model
{
  /**
   * The `AutomationRun` has an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `AutomationRun` has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The `AutomationRun` was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The `AutomationRun` has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The `AutomationRun` is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The `AutomationRun` is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The `AutomationRun` was aborted.
   */
  public const STATE_ABORTED = 'ABORTED';
  protected $advanceRolloutOperationType = AdvanceRolloutOperation::class;
  protected $advanceRolloutOperationDataType = '';
  /**
   * Output only. The ID of the automation that initiated the operation.
   *
   * @var string
   */
  public $automationId;
  protected $automationSnapshotType = Automation::class;
  protected $automationSnapshotDataType = '';
  /**
   * Output only. Time at which the `AutomationRun` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The weak etag of the `AutomationRun` resource. This checksum
   * is computed by the server based on the value of other fields, and may be
   * sent on update and delete requests to ensure the client has an up-to-date
   * value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Time the `AutomationRun` expires. An `AutomationRun` expires
   * after 14 days from its creation date.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. Name of the `AutomationRun`. Format is `projects/{project}/loc
   * ations/{location}/deliveryPipelines/{delivery_pipeline}/automationRuns/{aut
   * omation_run}`.
   *
   * @var string
   */
  public $name;
  protected $policyViolationType = PolicyViolation::class;
  protected $policyViolationDataType = '';
  protected $promoteReleaseOperationType = PromoteReleaseOperation::class;
  protected $promoteReleaseOperationDataType = '';
  protected $repairRolloutOperationType = RepairRolloutOperation::class;
  protected $repairRolloutOperationDataType = '';
  /**
   * Output only. The ID of the automation rule that initiated the operation.
   *
   * @var string
   */
  public $ruleId;
  /**
   * Output only. Email address of the user-managed IAM service account that
   * performs the operations against Cloud Deploy resources.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Current state of the `AutomationRun`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Explains the current state of the `AutomationRun`. Present
   * only when an explanation is needed.
   *
   * @var string
   */
  public $stateDescription;
  /**
   * Output only. The ID of the source target that initiates the
   * `AutomationRun`. The value of this field is the last segment of a target
   * name.
   *
   * @var string
   */
  public $targetId;
  protected $timedPromoteReleaseOperationType = TimedPromoteReleaseOperation::class;
  protected $timedPromoteReleaseOperationDataType = '';
  /**
   * Output only. Time at which the automationRun was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. Earliest time the `AutomationRun` will attempt to resume.
   * Wait-time is configured by `wait` in automation rule.
   *
   * @var string
   */
  public $waitUntilTime;

  /**
   * Output only. Advances a rollout to the next phase.
   *
   * @param AdvanceRolloutOperation $advanceRolloutOperation
   */
  public function setAdvanceRolloutOperation(AdvanceRolloutOperation $advanceRolloutOperation)
  {
    $this->advanceRolloutOperation = $advanceRolloutOperation;
  }
  /**
   * @return AdvanceRolloutOperation
   */
  public function getAdvanceRolloutOperation()
  {
    return $this->advanceRolloutOperation;
  }
  /**
   * Output only. The ID of the automation that initiated the operation.
   *
   * @param string $automationId
   */
  public function setAutomationId($automationId)
  {
    $this->automationId = $automationId;
  }
  /**
   * @return string
   */
  public function getAutomationId()
  {
    return $this->automationId;
  }
  /**
   * Output only. Snapshot of the Automation taken at AutomationRun creation
   * time.
   *
   * @param Automation $automationSnapshot
   */
  public function setAutomationSnapshot(Automation $automationSnapshot)
  {
    $this->automationSnapshot = $automationSnapshot;
  }
  /**
   * @return Automation
   */
  public function getAutomationSnapshot()
  {
    return $this->automationSnapshot;
  }
  /**
   * Output only. Time at which the `AutomationRun` was created.
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
   * Output only. The weak etag of the `AutomationRun` resource. This checksum
   * is computed by the server based on the value of other fields, and may be
   * sent on update and delete requests to ensure the client has an up-to-date
   * value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Time the `AutomationRun` expires. An `AutomationRun` expires
   * after 14 days from its creation date.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. Name of the `AutomationRun`. Format is `projects/{project}/loc
   * ations/{location}/deliveryPipelines/{delivery_pipeline}/automationRuns/{aut
   * omation_run}`.
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
   * Output only. Contains information about what policies prevented the
   * `AutomationRun` from proceeding.
   *
   * @param PolicyViolation $policyViolation
   */
  public function setPolicyViolation(PolicyViolation $policyViolation)
  {
    $this->policyViolation = $policyViolation;
  }
  /**
   * @return PolicyViolation
   */
  public function getPolicyViolation()
  {
    return $this->policyViolation;
  }
  /**
   * Output only. Promotes a release to a specified 'Target'.
   *
   * @param PromoteReleaseOperation $promoteReleaseOperation
   */
  public function setPromoteReleaseOperation(PromoteReleaseOperation $promoteReleaseOperation)
  {
    $this->promoteReleaseOperation = $promoteReleaseOperation;
  }
  /**
   * @return PromoteReleaseOperation
   */
  public function getPromoteReleaseOperation()
  {
    return $this->promoteReleaseOperation;
  }
  /**
   * Output only. Repairs a failed 'Rollout'.
   *
   * @param RepairRolloutOperation $repairRolloutOperation
   */
  public function setRepairRolloutOperation(RepairRolloutOperation $repairRolloutOperation)
  {
    $this->repairRolloutOperation = $repairRolloutOperation;
  }
  /**
   * @return RepairRolloutOperation
   */
  public function getRepairRolloutOperation()
  {
    return $this->repairRolloutOperation;
  }
  /**
   * Output only. The ID of the automation rule that initiated the operation.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
  /**
   * Output only. Email address of the user-managed IAM service account that
   * performs the operations against Cloud Deploy resources.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. Current state of the `AutomationRun`.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, CANCELLED, FAILED,
   * IN_PROGRESS, PENDING, ABORTED
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
   * Output only. Explains the current state of the `AutomationRun`. Present
   * only when an explanation is needed.
   *
   * @param string $stateDescription
   */
  public function setStateDescription($stateDescription)
  {
    $this->stateDescription = $stateDescription;
  }
  /**
   * @return string
   */
  public function getStateDescription()
  {
    return $this->stateDescription;
  }
  /**
   * Output only. The ID of the source target that initiates the
   * `AutomationRun`. The value of this field is the last segment of a target
   * name.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * Output only. Promotes a release to a specified 'Target' as defined in a
   * Timed Promote Release rule.
   *
   * @param TimedPromoteReleaseOperation $timedPromoteReleaseOperation
   */
  public function setTimedPromoteReleaseOperation(TimedPromoteReleaseOperation $timedPromoteReleaseOperation)
  {
    $this->timedPromoteReleaseOperation = $timedPromoteReleaseOperation;
  }
  /**
   * @return TimedPromoteReleaseOperation
   */
  public function getTimedPromoteReleaseOperation()
  {
    return $this->timedPromoteReleaseOperation;
  }
  /**
   * Output only. Time at which the automationRun was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. Earliest time the `AutomationRun` will attempt to resume.
   * Wait-time is configured by `wait` in automation rule.
   *
   * @param string $waitUntilTime
   */
  public function setWaitUntilTime($waitUntilTime)
  {
    $this->waitUntilTime = $waitUntilTime;
  }
  /**
   * @return string
   */
  public function getWaitUntilTime()
  {
    return $this->waitUntilTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomationRun::class, 'Google_Service_CloudDeploy_AutomationRun');
