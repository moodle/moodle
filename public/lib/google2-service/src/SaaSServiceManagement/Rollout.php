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

namespace Google\Service\SaaSServiceManagement;

class Rollout extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_ROLLOUT_STATE_UNSPECIFIED = 'ROLLOUT_STATE_UNSPECIFIED';
  /**
   * Rollout is in progress.
   */
  public const STATE_ROLLOUT_STATE_RUNNING = 'ROLLOUT_STATE_RUNNING';
  /**
   * Rollout has been paused.
   */
  public const STATE_ROLLOUT_STATE_PAUSED = 'ROLLOUT_STATE_PAUSED';
  /**
   * Rollout completed successfully.
   */
  public const STATE_ROLLOUT_STATE_SUCCEEDED = 'ROLLOUT_STATE_SUCCEEDED';
  /**
   * Rollout has failed.
   */
  public const STATE_ROLLOUT_STATE_FAILED = 'ROLLOUT_STATE_FAILED';
  /**
   * Rollout has been canceled.
   */
  public const STATE_ROLLOUT_STATE_CANCELLED = 'ROLLOUT_STATE_CANCELLED';
  /**
   * Rollout is waiting for some condition to be met before starting.
   */
  public const STATE_ROLLOUT_STATE_WAITING = 'ROLLOUT_STATE_WAITING';
  /**
   * Rollout is being canceled.
   */
  public const STATE_ROLLOUT_STATE_CANCELLING = 'ROLLOUT_STATE_CANCELLING';
  /**
   * Rollout is being resumed.
   */
  public const STATE_ROLLOUT_STATE_RESUMING = 'ROLLOUT_STATE_RESUMING';
  /**
   * Rollout is being paused.
   */
  public const STATE_ROLLOUT_STATE_PAUSING = 'ROLLOUT_STATE_PAUSING';
  /**
   * Optional. Annotations is an unstructured key-value map stored with a
   * resource that may be set by external tools to store and retrieve arbitrary
   * metadata. They are not queryable and should be preserved when modifying
   * objects. More info: https://kubernetes.io/docs/user-guide/annotations
   *
   * @var string[]
   */
  public $annotations;
  protected $controlType = RolloutControl::class;
  protected $controlDataType = '';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Output only. Output only snapshot of the effective unit filter at
   * Rollout start time. Contains a CEL(https://github.com/google/cel-spec)
   * expression consisting of a conjunction of Rollout.unit_filter and
   * RolloutKind.unit_filter. This field captures the filter applied by the
   * Rollout to determine the Unit population. If the associated RolloutKind's
   * unit_filter is modified after the rollout is started, it will not be
   * updated here.
   *
   * @var string
   */
  public $effectiveUnitFilter;
  /**
   * Optional. Output only. The time when the rollout finished execution
   * (regardless of success, failure, or cancellation). Will be empty if the
   * rollout hasn't finished yet. Once set, the rollout is in terminal state and
   * all the results are final.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. An opaque value that uniquely identifies a version or
   * generation of a resource. It can be used to confirm that the client and
   * server agree on the ordering of a resource being written.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels on the resource, which can be used for categorization.
   * similar to Kubernetes resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/rollout/{rollout_id}"
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Output only. The direct parent rollout that this rollout is
   * stemming from. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/rollouts/{rollout_id}"
   *
   * @var string
   */
  public $parentRollout;
  /**
   * Optional. Immutable. Name of the Release that gets rolled out to target
   * Units. Required if no other type of release is specified.
   *
   * @var string
   */
  public $release;
  /**
   * Optional. Immutable. Name of the RolloutKind this rollout is stemming from
   * and adhering to.
   *
   * @var string
   */
  public $rolloutKind;
  /**
   * Optional. The strategy used for executing this Rollout. This strategy will
   * override whatever strategy is specified in the RolloutKind. If not
   * specified on creation, the strategy from RolloutKind will be used. There
   * are two supported values strategies which are used to control -
   * "Google.Cloud.Simple.AllAtOnce" - "Google.Cloud.Simple.OneLocationAtATime"
   * A rollout with one of these simple strategies will rollout across all
   * locations defined in the targeted UnitKind's Saas Locations.
   *
   * @var string
   */
  public $rolloutOrchestrationStrategy;
  /**
   * Optional. Output only. The root rollout that this rollout is stemming from.
   * The resource name (full URI of the resource) following the standard naming
   * scheme: "projects/{project}/locations/{location}/rollouts/{rollout_id}"
   *
   * @var string
   */
  public $rootRollout;
  /**
   * Optional. Output only. The time when the rollout started executing. Will be
   * empty if the rollout hasn't started yet.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Current state of the rollout.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Human readable message indicating details about the last state
   * transition.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Optional. Output only. The time when the rollout transitioned into its
   * current state.
   *
   * @var string
   */
  public $stateTransitionTime;
  protected $statsType = RolloutStats::class;
  protected $statsDataType = '';
  /**
   * Output only. The unique identifier of the resource. UID is unique in the
   * time and space for this resource within the scope of the service. It is
   * typically generated by the server on successful creation of a resource and
   * must not be changed. UID is used to uniquely identify resources with
   * resource name reuses. This should be a UUID4.
   *
   * @var string
   */
  public $uid;
  /**
   * Optional. CEL(https://github.com/google/cel-spec) formatted filter string
   * against Unit. The filter will be applied to determine the eligible unit
   * population. This filter can only reduce, but not expand the scope of the
   * rollout. If not provided, the unit_filter from the RolloutKind will be
   * used.
   *
   * @var string
   */
  public $unitFilter;
  /**
   * Output only. The timestamp when the resource was last updated. Any change
   * to the resource made by users must refresh this value. Changes to a
   * resource made by the service should refresh this value.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Annotations is an unstructured key-value map stored with a
   * resource that may be set by external tools to store and retrieve arbitrary
   * metadata. They are not queryable and should be preserved when modifying
   * objects. More info: https://kubernetes.io/docs/user-guide/annotations
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
   * Optional. Requested change to the execution of this rollout. Default
   * RolloutControl.action is ROLLOUT_ACTION_RUN meaning the rollout will be
   * executed to completion while progressing through all natural Rollout States
   * (such as RUNNING -> SUCCEEDED or RUNNING -> FAILED). Requests can only be
   * made when the Rollout is in a non-terminal state.
   *
   * @param RolloutControl $control
   */
  public function setControl(RolloutControl $control)
  {
    $this->control = $control;
  }
  /**
   * @return RolloutControl
   */
  public function getControl()
  {
    return $this->control;
  }
  /**
   * Output only. The timestamp when the resource was created.
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
   * Optional. Output only. Output only snapshot of the effective unit filter at
   * Rollout start time. Contains a CEL(https://github.com/google/cel-spec)
   * expression consisting of a conjunction of Rollout.unit_filter and
   * RolloutKind.unit_filter. This field captures the filter applied by the
   * Rollout to determine the Unit population. If the associated RolloutKind's
   * unit_filter is modified after the rollout is started, it will not be
   * updated here.
   *
   * @param string $effectiveUnitFilter
   */
  public function setEffectiveUnitFilter($effectiveUnitFilter)
  {
    $this->effectiveUnitFilter = $effectiveUnitFilter;
  }
  /**
   * @return string
   */
  public function getEffectiveUnitFilter()
  {
    return $this->effectiveUnitFilter;
  }
  /**
   * Optional. Output only. The time when the rollout finished execution
   * (regardless of success, failure, or cancellation). Will be empty if the
   * rollout hasn't finished yet. Once set, the rollout is in terminal state and
   * all the results are final.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. An opaque value that uniquely identifies a version or
   * generation of a resource. It can be used to confirm that the client and
   * server agree on the ordering of a resource being written.
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
   * Optional. The labels on the resource, which can be used for categorization.
   * similar to Kubernetes resource labels.
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
   * Identifier. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/rollout/{rollout_id}"
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
   * Optional. Output only. The direct parent rollout that this rollout is
   * stemming from. The resource name (full URI of the resource) following the
   * standard naming scheme:
   * "projects/{project}/locations/{location}/rollouts/{rollout_id}"
   *
   * @param string $parentRollout
   */
  public function setParentRollout($parentRollout)
  {
    $this->parentRollout = $parentRollout;
  }
  /**
   * @return string
   */
  public function getParentRollout()
  {
    return $this->parentRollout;
  }
  /**
   * Optional. Immutable. Name of the Release that gets rolled out to target
   * Units. Required if no other type of release is specified.
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
   * Optional. Immutable. Name of the RolloutKind this rollout is stemming from
   * and adhering to.
   *
   * @param string $rolloutKind
   */
  public function setRolloutKind($rolloutKind)
  {
    $this->rolloutKind = $rolloutKind;
  }
  /**
   * @return string
   */
  public function getRolloutKind()
  {
    return $this->rolloutKind;
  }
  /**
   * Optional. The strategy used for executing this Rollout. This strategy will
   * override whatever strategy is specified in the RolloutKind. If not
   * specified on creation, the strategy from RolloutKind will be used. There
   * are two supported values strategies which are used to control -
   * "Google.Cloud.Simple.AllAtOnce" - "Google.Cloud.Simple.OneLocationAtATime"
   * A rollout with one of these simple strategies will rollout across all
   * locations defined in the targeted UnitKind's Saas Locations.
   *
   * @param string $rolloutOrchestrationStrategy
   */
  public function setRolloutOrchestrationStrategy($rolloutOrchestrationStrategy)
  {
    $this->rolloutOrchestrationStrategy = $rolloutOrchestrationStrategy;
  }
  /**
   * @return string
   */
  public function getRolloutOrchestrationStrategy()
  {
    return $this->rolloutOrchestrationStrategy;
  }
  /**
   * Optional. Output only. The root rollout that this rollout is stemming from.
   * The resource name (full URI of the resource) following the standard naming
   * scheme: "projects/{project}/locations/{location}/rollouts/{rollout_id}"
   *
   * @param string $rootRollout
   */
  public function setRootRollout($rootRollout)
  {
    $this->rootRollout = $rootRollout;
  }
  /**
   * @return string
   */
  public function getRootRollout()
  {
    return $this->rootRollout;
  }
  /**
   * Optional. Output only. The time when the rollout started executing. Will be
   * empty if the rollout hasn't started yet.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Current state of the rollout.
   *
   * Accepted values: ROLLOUT_STATE_UNSPECIFIED, ROLLOUT_STATE_RUNNING,
   * ROLLOUT_STATE_PAUSED, ROLLOUT_STATE_SUCCEEDED, ROLLOUT_STATE_FAILED,
   * ROLLOUT_STATE_CANCELLED, ROLLOUT_STATE_WAITING, ROLLOUT_STATE_CANCELLING,
   * ROLLOUT_STATE_RESUMING, ROLLOUT_STATE_PAUSING
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
   * Output only. Human readable message indicating details about the last state
   * transition.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * Optional. Output only. The time when the rollout transitioned into its
   * current state.
   *
   * @param string $stateTransitionTime
   */
  public function setStateTransitionTime($stateTransitionTime)
  {
    $this->stateTransitionTime = $stateTransitionTime;
  }
  /**
   * @return string
   */
  public function getStateTransitionTime()
  {
    return $this->stateTransitionTime;
  }
  /**
   * Optional. Output only. Details about the progress of the rollout.
   *
   * @param RolloutStats $stats
   */
  public function setStats(RolloutStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return RolloutStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * Output only. The unique identifier of the resource. UID is unique in the
   * time and space for this resource within the scope of the service. It is
   * typically generated by the server on successful creation of a resource and
   * must not be changed. UID is used to uniquely identify resources with
   * resource name reuses. This should be a UUID4.
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
   * Optional. CEL(https://github.com/google/cel-spec) formatted filter string
   * against Unit. The filter will be applied to determine the eligible unit
   * population. This filter can only reduce, but not expand the scope of the
   * rollout. If not provided, the unit_filter from the RolloutKind will be
   * used.
   *
   * @param string $unitFilter
   */
  public function setUnitFilter($unitFilter)
  {
    $this->unitFilter = $unitFilter;
  }
  /**
   * @return string
   */
  public function getUnitFilter()
  {
    return $this->unitFilter;
  }
  /**
   * Output only. The timestamp when the resource was last updated. Any change
   * to the resource made by users must refresh this value. Changes to a
   * resource made by the service should refresh this value.
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
class_alias(Rollout::class, 'Google_Service_SaaSServiceManagement_Rollout');
