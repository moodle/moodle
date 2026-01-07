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

class RolloutUpdateEvent extends \Google\Model
{
  /**
   * Rollout update type unspecified.
   */
  public const ROLLOUT_UPDATE_TYPE_ROLLOUT_UPDATE_TYPE_UNSPECIFIED = 'ROLLOUT_UPDATE_TYPE_UNSPECIFIED';
  /**
   * Rollout state updated to pending (release has succeeded, waiting on the
   * rollout to start).
   */
  public const ROLLOUT_UPDATE_TYPE_PENDING = 'PENDING';
  /**
   * Rollout state updated to pending release.
   */
  public const ROLLOUT_UPDATE_TYPE_PENDING_RELEASE = 'PENDING_RELEASE';
  /**
   * Rollout state updated to in progress.
   */
  public const ROLLOUT_UPDATE_TYPE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Rollout state updated to cancelling.
   */
  public const ROLLOUT_UPDATE_TYPE_CANCELLING = 'CANCELLING';
  /**
   * Rollout state updated to cancelled.
   */
  public const ROLLOUT_UPDATE_TYPE_CANCELLED = 'CANCELLED';
  /**
   * Rollout state updated to halted.
   */
  public const ROLLOUT_UPDATE_TYPE_HALTED = 'HALTED';
  /**
   * Rollout state updated to succeeded.
   */
  public const ROLLOUT_UPDATE_TYPE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Rollout state updated to failed.
   */
  public const ROLLOUT_UPDATE_TYPE_FAILED = 'FAILED';
  /**
   * Rollout requires approval.
   */
  public const ROLLOUT_UPDATE_TYPE_APPROVAL_REQUIRED = 'APPROVAL_REQUIRED';
  /**
   * Rollout has been approved.
   */
  public const ROLLOUT_UPDATE_TYPE_APPROVED = 'APPROVED';
  /**
   * Rollout has been rejected.
   */
  public const ROLLOUT_UPDATE_TYPE_REJECTED = 'REJECTED';
  /**
   * Rollout requires advance to the next phase.
   */
  public const ROLLOUT_UPDATE_TYPE_ADVANCE_REQUIRED = 'ADVANCE_REQUIRED';
  /**
   * Rollout has been advanced.
   */
  public const ROLLOUT_UPDATE_TYPE_ADVANCED = 'ADVANCED';
  /**
   * Type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A Pub/Sub notification failed to be sent.
   */
  public const TYPE_TYPE_PUBSUB_NOTIFICATION_FAILURE = 'TYPE_PUBSUB_NOTIFICATION_FAILURE';
  /**
   * Resource state changed.
   */
  public const TYPE_TYPE_RESOURCE_STATE_CHANGE = 'TYPE_RESOURCE_STATE_CHANGE';
  /**
   * A process aborted.
   */
  public const TYPE_TYPE_PROCESS_ABORTED = 'TYPE_PROCESS_ABORTED';
  /**
   * Restriction check failed.
   */
  public const TYPE_TYPE_RESTRICTION_VIOLATED = 'TYPE_RESTRICTION_VIOLATED';
  /**
   * Resource deleted.
   */
  public const TYPE_TYPE_RESOURCE_DELETED = 'TYPE_RESOURCE_DELETED';
  /**
   * Rollout updated.
   */
  public const TYPE_TYPE_ROLLOUT_UPDATE = 'TYPE_ROLLOUT_UPDATE';
  /**
   * Deploy Policy evaluation.
   */
  public const TYPE_TYPE_DEPLOY_POLICY_EVALUATION = 'TYPE_DEPLOY_POLICY_EVALUATION';
  /**
   * Deprecated: This field is never used. Use release_render log type instead.
   *
   * @deprecated
   */
  public const TYPE_TYPE_RENDER_STATUES_CHANGE = 'TYPE_RENDER_STATUES_CHANGE';
  /**
   * Debug message for when a rollout update event occurs.
   *
   * @var string
   */
  public $message;
  /**
   * Unique identifier of the pipeline.
   *
   * @var string
   */
  public $pipelineUid;
  /**
   * The name of the `Release`.
   *
   * @var string
   */
  public $release;
  /**
   * Unique identifier of the release.
   *
   * @var string
   */
  public $releaseUid;
  /**
   * The name of the rollout. rollout_uid is not in this log message because we
   * write some of these log messages at rollout creation time, before we've
   * generated the uid.
   *
   * @var string
   */
  public $rollout;
  /**
   * The type of the rollout update.
   *
   * @var string
   */
  public $rolloutUpdateType;
  /**
   * ID of the target.
   *
   * @var string
   */
  public $targetId;
  /**
   * Type of this notification, e.g. for a rollout update event.
   *
   * @var string
   */
  public $type;

  /**
   * Debug message for when a rollout update event occurs.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Unique identifier of the pipeline.
   *
   * @param string $pipelineUid
   */
  public function setPipelineUid($pipelineUid)
  {
    $this->pipelineUid = $pipelineUid;
  }
  /**
   * @return string
   */
  public function getPipelineUid()
  {
    return $this->pipelineUid;
  }
  /**
   * The name of the `Release`.
   *
   * @param string $release
   */
  public function setRelease($release)
  {
    $this->release = $release;
  }
  /**
   * @return string
   */
  public function getRelease()
  {
    return $this->release;
  }
  /**
   * Unique identifier of the release.
   *
   * @param string $releaseUid
   */
  public function setReleaseUid($releaseUid)
  {
    $this->releaseUid = $releaseUid;
  }
  /**
   * @return string
   */
  public function getReleaseUid()
  {
    return $this->releaseUid;
  }
  /**
   * The name of the rollout. rollout_uid is not in this log message because we
   * write some of these log messages at rollout creation time, before we've
   * generated the uid.
   *
   * @param string $rollout
   */
  public function setRollout($rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return string
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * The type of the rollout update.
   *
   * Accepted values: ROLLOUT_UPDATE_TYPE_UNSPECIFIED, PENDING, PENDING_RELEASE,
   * IN_PROGRESS, CANCELLING, CANCELLED, HALTED, SUCCEEDED, FAILED,
   * APPROVAL_REQUIRED, APPROVED, REJECTED, ADVANCE_REQUIRED, ADVANCED
   *
   * @param self::ROLLOUT_UPDATE_TYPE_* $rolloutUpdateType
   */
  public function setRolloutUpdateType($rolloutUpdateType)
  {
    $this->rolloutUpdateType = $rolloutUpdateType;
  }
  /**
   * @return self::ROLLOUT_UPDATE_TYPE_*
   */
  public function getRolloutUpdateType()
  {
    return $this->rolloutUpdateType;
  }
  /**
   * ID of the target.
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
   * Type of this notification, e.g. for a rollout update event.
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_PUBSUB_NOTIFICATION_FAILURE,
   * TYPE_RESOURCE_STATE_CHANGE, TYPE_PROCESS_ABORTED,
   * TYPE_RESTRICTION_VIOLATED, TYPE_RESOURCE_DELETED, TYPE_ROLLOUT_UPDATE,
   * TYPE_DEPLOY_POLICY_EVALUATION, TYPE_RENDER_STATUES_CHANGE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RolloutUpdateEvent::class, 'Google_Service_CloudDeploy_RolloutUpdateEvent');
