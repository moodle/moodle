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

namespace Google\Service\FirebaseAppHosting;

class Rollout extends \Google\Model
{
  /**
   * The rollout is in an unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The rollout is waiting for actuation to begin. This may be because it is
   * waiting on another rollout to complete.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * The rollout is waiting for the build process to complete, which builds the
   * code and sets up the underlying infrastructure.
   */
  public const STATE_PENDING_BUILD = 'PENDING_BUILD';
  /**
   * The rollout has started and is actively modifying traffic.
   */
  public const STATE_PROGRESSING = 'PROGRESSING';
  /**
   * The rollout has been paused due to either being manually paused or a PAUSED
   * stage. This should be set while `paused = true`.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The rollout has completed.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The rollout has failed. See error for more information.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The rollout has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Required. Immutable. The name of a build that already exists. It doesn't
   * have to be built; a rollout will wait for a build to be ready before
   * updating traffic.
   *
   * @var string
   */
  public $build;
  /**
   * Output only. Time at which the rollout was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time at which the rollout was deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Human-readable name. 63 character limit.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the rollout. Format: `projects/{project}/l
   * ocations/{locationId}/backends/{backendId}/rollouts/{rolloutId}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that, if true, indicates that the Rollout currently
   * has an LRO.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The state of the rollout.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the rollout was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
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
   * Required. Immutable. The name of a build that already exists. It doesn't
   * have to be built; a rollout will wait for a build to be ready before
   * updating traffic.
   *
   * @param string $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return string
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Output only. Time at which the rollout was created.
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
   * Output only. Time at which the rollout was deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. Human-readable name. 63 character limit.
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
   * Output only. A status and (human readable) error message for the rollout,
   * if in a `FAILED` state.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
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
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
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
   * Identifier. The resource name of the rollout. Format: `projects/{project}/l
   * ocations/{locationId}/backends/{backendId}/rollouts/{rolloutId}`.
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
   * Output only. A field that, if true, indicates that the Rollout currently
   * has an LRO.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The state of the rollout.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, PENDING_BUILD, PROGRESSING,
   * PAUSED, SUCCEEDED, FAILED, CANCELLED
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
   * Output only. System-assigned, unique identifier.
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
  /**
   * Output only. Time at which the rollout was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Rollout::class, 'Google_Service_FirebaseAppHosting_Rollout');
