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

class ReleaseNotificationEvent extends \Google\Model
{
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
   * Debug message for when a notification fails to send.
   *
   * @var string
   */
  public $message;
  /**
   * Unique identifier of the `DeliveryPipeline`.
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
   * Unique identifier of the `Release`.
   *
   * @var string
   */
  public $releaseUid;
  /**
   * Type of this notification, e.g. for a Pub/Sub failure.
   *
   * @var string
   */
  public $type;

  /**
   * Debug message for when a notification fails to send.
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
   * Unique identifier of the `DeliveryPipeline`.
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
   * Unique identifier of the `Release`.
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
   * Type of this notification, e.g. for a Pub/Sub failure.
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
class_alias(ReleaseNotificationEvent::class, 'Google_Service_CloudDeploy_ReleaseNotificationEvent');
