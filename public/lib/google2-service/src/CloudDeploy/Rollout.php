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

class Rollout extends \Google\Collection
{
  /**
   * The `Rollout` has an unspecified approval state.
   */
  public const APPROVAL_STATE_APPROVAL_STATE_UNSPECIFIED = 'APPROVAL_STATE_UNSPECIFIED';
  /**
   * The `Rollout` requires approval.
   */
  public const APPROVAL_STATE_NEEDS_APPROVAL = 'NEEDS_APPROVAL';
  /**
   * The `Rollout` does not require approval.
   */
  public const APPROVAL_STATE_DOES_NOT_NEED_APPROVAL = 'DOES_NOT_NEED_APPROVAL';
  /**
   * The `Rollout` has been approved.
   */
  public const APPROVAL_STATE_APPROVED = 'APPROVED';
  /**
   * The `Rollout` has been rejected.
   */
  public const APPROVAL_STATE_REJECTED = 'REJECTED';
  /**
   * No reason for failure is specified.
   */
  public const DEPLOY_FAILURE_CAUSE_FAILURE_CAUSE_UNSPECIFIED = 'FAILURE_CAUSE_UNSPECIFIED';
  /**
   * Cloud Build is not available, either because it is not enabled or because
   * Cloud Deploy has insufficient permissions. See [required
   * permission](https://cloud.google.com/deploy/docs/cloud-deploy-service-
   * account#required_permissions).
   */
  public const DEPLOY_FAILURE_CAUSE_CLOUD_BUILD_UNAVAILABLE = 'CLOUD_BUILD_UNAVAILABLE';
  /**
   * The deploy operation did not complete successfully; check Cloud Build logs.
   */
  public const DEPLOY_FAILURE_CAUSE_EXECUTION_FAILED = 'EXECUTION_FAILED';
  /**
   * Deployment did not complete within the allotted time.
   */
  public const DEPLOY_FAILURE_CAUSE_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  /**
   * Release is in a failed state.
   */
  public const DEPLOY_FAILURE_CAUSE_RELEASE_FAILED = 'RELEASE_FAILED';
  /**
   * Release is abandoned.
   */
  public const DEPLOY_FAILURE_CAUSE_RELEASE_ABANDONED = 'RELEASE_ABANDONED';
  /**
   * No Skaffold verify configuration was found.
   */
  public const DEPLOY_FAILURE_CAUSE_VERIFICATION_CONFIG_NOT_FOUND = 'VERIFICATION_CONFIG_NOT_FOUND';
  /**
   * Cloud Build failed to fulfill Cloud Deploy's request. See failure_message
   * for additional details.
   */
  public const DEPLOY_FAILURE_CAUSE_CLOUD_BUILD_REQUEST_FAILED = 'CLOUD_BUILD_REQUEST_FAILED';
  /**
   * A Rollout operation had a feature configured that is not supported.
   */
  public const DEPLOY_FAILURE_CAUSE_OPERATION_FEATURE_NOT_SUPPORTED = 'OPERATION_FEATURE_NOT_SUPPORTED';
  /**
   * The `Rollout` has an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `Rollout` has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The `Rollout` has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The `Rollout` is being deployed.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The `Rollout` needs approval.
   */
  public const STATE_PENDING_APPROVAL = 'PENDING_APPROVAL';
  /**
   * An approver rejected the `Rollout`.
   */
  public const STATE_APPROVAL_REJECTED = 'APPROVAL_REJECTED';
  /**
   * The `Rollout` is waiting for an earlier Rollout(s) to complete on this
   * `Target`.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The `Rollout` is waiting for the `Release` to be fully rendered.
   */
  public const STATE_PENDING_RELEASE = 'PENDING_RELEASE';
  /**
   * The `Rollout` is in the process of being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The `Rollout` has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The `Rollout` is halted.
   */
  public const STATE_HALTED = 'HALTED';
  protected $collection_key = 'rolledBackByRollouts';
  /**
   * Output only. The AutomationRun actively repairing the rollout.
   *
   * @var string
   */
  public $activeRepairAutomationRun;
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Approval state of the `Rollout`.
   *
   * @var string
   */
  public $approvalState;
  /**
   * Output only. Time at which the `Rollout` was approved.
   *
   * @var string
   */
  public $approveTime;
  /**
   * Output only. Name of the `ControllerRollout`. Format is `projects/{project}
   * /locations/{location}/deliveryPipelines/{deliveryPipeline}/releases/{releas
   * e}/rollouts/{rollout}`.
   *
   * @var string
   */
  public $controllerRollout;
  /**
   * Output only. Time at which the `Rollout` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time at which the `Rollout` finished deploying.
   *
   * @var string
   */
  public $deployEndTime;
  /**
   * Output only. The reason this rollout failed. This will always be
   * unspecified while the rollout is in progress.
   *
   * @var string
   */
  public $deployFailureCause;
  /**
   * Output only. Time at which the `Rollout` started deploying.
   *
   * @var string
   */
  public $deployStartTime;
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to deploy the Rollout. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @var string
   */
  public $deployingBuild;
  /**
   * Optional. Description of the `Rollout` for user purposes. Max length is 255
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Time at which the `Rollout` was enqueued.
   *
   * @var string
   */
  public $enqueueTime;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Additional information about the rollout failure, if
   * available.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * Identifier. Name of the `Rollout`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{release}/rollouts
   * /{rollout}`. The `rollout` component must match
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  protected $phasesType = Phase::class;
  protected $phasesDataType = 'array';
  /**
   * Output only. Name of the `Rollout` that is rolled back by this `Rollout`.
   * Empty if this `Rollout` wasn't created as a rollback.
   *
   * @var string
   */
  public $rollbackOfRollout;
  /**
   * Output only. Names of `Rollouts` that rolled back this `Rollout`.
   *
   * @var string[]
   */
  public $rolledBackByRollouts;
  /**
   * Output only. Current state of the `Rollout`.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The ID of Target to which this `Rollout` is deploying.
   *
   * @var string
   */
  public $targetId;
  /**
   * Output only. Unique identifier of the `Rollout`.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. The AutomationRun actively repairing the rollout.
   *
   * @param string $activeRepairAutomationRun
   */
  public function setActiveRepairAutomationRun($activeRepairAutomationRun)
  {
    $this->activeRepairAutomationRun = $activeRepairAutomationRun;
  }
  /**
   * @return string
   */
  public function getActiveRepairAutomationRun()
  {
    return $this->activeRepairAutomationRun;
  }
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Approval state of the `Rollout`.
   *
   * Accepted values: APPROVAL_STATE_UNSPECIFIED, NEEDS_APPROVAL,
   * DOES_NOT_NEED_APPROVAL, APPROVED, REJECTED
   *
   * @param self::APPROVAL_STATE_* $approvalState
   */
  public function setApprovalState($approvalState)
  {
    $this->approvalState = $approvalState;
  }
  /**
   * @return self::APPROVAL_STATE_*
   */
  public function getApprovalState()
  {
    return $this->approvalState;
  }
  /**
   * Output only. Time at which the `Rollout` was approved.
   *
   * @param string $approveTime
   */
  public function setApproveTime($approveTime)
  {
    $this->approveTime = $approveTime;
  }
  /**
   * @return string
   */
  public function getApproveTime()
  {
    return $this->approveTime;
  }
  /**
   * Output only. Name of the `ControllerRollout`. Format is `projects/{project}
   * /locations/{location}/deliveryPipelines/{deliveryPipeline}/releases/{releas
   * e}/rollouts/{rollout}`.
   *
   * @param string $controllerRollout
   */
  public function setControllerRollout($controllerRollout)
  {
    $this->controllerRollout = $controllerRollout;
  }
  /**
   * @return string
   */
  public function getControllerRollout()
  {
    return $this->controllerRollout;
  }
  /**
   * Output only. Time at which the `Rollout` was created.
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
   * Output only. Time at which the `Rollout` finished deploying.
   *
   * @param string $deployEndTime
   */
  public function setDeployEndTime($deployEndTime)
  {
    $this->deployEndTime = $deployEndTime;
  }
  /**
   * @return string
   */
  public function getDeployEndTime()
  {
    return $this->deployEndTime;
  }
  /**
   * Output only. The reason this rollout failed. This will always be
   * unspecified while the rollout is in progress.
   *
   * Accepted values: FAILURE_CAUSE_UNSPECIFIED, CLOUD_BUILD_UNAVAILABLE,
   * EXECUTION_FAILED, DEADLINE_EXCEEDED, RELEASE_FAILED, RELEASE_ABANDONED,
   * VERIFICATION_CONFIG_NOT_FOUND, CLOUD_BUILD_REQUEST_FAILED,
   * OPERATION_FEATURE_NOT_SUPPORTED
   *
   * @param self::DEPLOY_FAILURE_CAUSE_* $deployFailureCause
   */
  public function setDeployFailureCause($deployFailureCause)
  {
    $this->deployFailureCause = $deployFailureCause;
  }
  /**
   * @return self::DEPLOY_FAILURE_CAUSE_*
   */
  public function getDeployFailureCause()
  {
    return $this->deployFailureCause;
  }
  /**
   * Output only. Time at which the `Rollout` started deploying.
   *
   * @param string $deployStartTime
   */
  public function setDeployStartTime($deployStartTime)
  {
    $this->deployStartTime = $deployStartTime;
  }
  /**
   * @return string
   */
  public function getDeployStartTime()
  {
    return $this->deployStartTime;
  }
  /**
   * Output only. The resource name of the Cloud Build `Build` object that is
   * used to deploy the Rollout. Format is
   * `projects/{project}/locations/{location}/builds/{build}`.
   *
   * @param string $deployingBuild
   */
  public function setDeployingBuild($deployingBuild)
  {
    $this->deployingBuild = $deployingBuild;
  }
  /**
   * @return string
   */
  public function getDeployingBuild()
  {
    return $this->deployingBuild;
  }
  /**
   * Optional. Description of the `Rollout` for user purposes. Max length is 255
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Time at which the `Rollout` was enqueued.
   *
   * @param string $enqueueTime
   */
  public function setEnqueueTime($enqueueTime)
  {
    $this->enqueueTime = $enqueueTime;
  }
  /**
   * @return string
   */
  public function getEnqueueTime()
  {
    return $this->enqueueTime;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
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
   * Output only. Additional information about the rollout failure, if
   * available.
   *
   * @param string $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return string
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Metadata contains information about the rollout.
   *
   * @param Metadata $metadata
   */
  public function setMetadata(Metadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Identifier. Name of the `Rollout`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{release}/rollouts
   * /{rollout}`. The `rollout` component must match
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Output only. The phases that represent the workflows of this `Rollout`.
   *
   * @param Phase[] $phases
   */
  public function setPhases($phases)
  {
    $this->phases = $phases;
  }
  /**
   * @return Phase[]
   */
  public function getPhases()
  {
    return $this->phases;
  }
  /**
   * Output only. Name of the `Rollout` that is rolled back by this `Rollout`.
   * Empty if this `Rollout` wasn't created as a rollback.
   *
   * @param string $rollbackOfRollout
   */
  public function setRollbackOfRollout($rollbackOfRollout)
  {
    $this->rollbackOfRollout = $rollbackOfRollout;
  }
  /**
   * @return string
   */
  public function getRollbackOfRollout()
  {
    return $this->rollbackOfRollout;
  }
  /**
   * Output only. Names of `Rollouts` that rolled back this `Rollout`.
   *
   * @param string[] $rolledBackByRollouts
   */
  public function setRolledBackByRollouts($rolledBackByRollouts)
  {
    $this->rolledBackByRollouts = $rolledBackByRollouts;
  }
  /**
   * @return string[]
   */
  public function getRolledBackByRollouts()
  {
    return $this->rolledBackByRollouts;
  }
  /**
   * Output only. Current state of the `Rollout`.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, FAILED, IN_PROGRESS,
   * PENDING_APPROVAL, APPROVAL_REJECTED, PENDING, PENDING_RELEASE, CANCELLING,
   * CANCELLED, HALTED
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
   * Required. The ID of Target to which this `Rollout` is deploying.
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
   * Output only. Unique identifier of the `Rollout`.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Rollout::class, 'Google_Service_CloudDeploy_Rollout');
